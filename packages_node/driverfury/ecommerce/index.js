let axios = require('axios')

class Ecommerce {
  constructor(apiKey) {
    this.apiKey = apiKey
    this.$http = axios
  }

  getHeaders() {
    return {
      Authorization: 'Bearer ' + this.apiKey,
    }
  }

  async fetchOrders(filters = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      let params = {}

      if (filters.hasOwnProperty('date_from') && filters.date_from != '') {
        params.filter_date_from = filters.date_from
      }
      if (filters.hasOwnProperty('date_to') && filters.date_to != '') {
        params.filter_date_to = filters.date_to
      }

      const response = await this.$http.get('/api/3rd/ecommerce/orders', { headers, params })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data
      }

      for (let i = 0; i < objects.length; i++) {
        let obj = objects[i]

        obj.date = new Date(obj.date_created);

        obj.products = ''
        if (obj.hasOwnProperty('line_items')) {
          if (obj.line_items.length > 0) {
            obj.products += obj.line_items[0].name
          }
          for (let j = 1; j < obj.line_items.length; j++) {
            obj.products += ', ' + obj.line_items[j].name
          }
        }

        if (obj.hasOwnProperty('total')) {
          obj.total = parseFloat(obj.total)
        }
      }

    } catch (error) {
      // Nothing
    }

    return objects
  }

  async fetchCustomers(filters = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/ecommerce/customers', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async fetchProducts(filters = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/ecommerce/products', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  csvToArray(strData, strDelimiter) {
    strDelimiter = (strDelimiter || ",")
    
    let objPattern = new RegExp(
      (
        // Delimiters.
        "(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +
        
        // Quoted fields.
        "(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +
        
        // Standard fields.
        "([^\"\\" + strDelimiter + "\\r\\n]*))"
      ),
      "gi"
    )
    
    let arrData = [[]]
    let arrMatches = null
    
    while (arrMatches = objPattern.exec(strData)) {
      let strMatchedDelimiter = arrMatches[ 1 ]
      
      if (strMatchedDelimiter.length &&
          strMatchedDelimiter !== strDelimiter) {
        arrData.push([])
      }
      
      let strMatchedValue = ''
      if (arrMatches[2]) {
        strMatchedValue = arrMatches[2].replace(
          new RegExp("\"\"", "g"),
          "\""
        )
      } else {
        strMatchedValue = arrMatches[3]
      }

      strMatchedValue = strMatchedValue.trim()

      arrData[arrData.length - 1].push(strMatchedValue)
    }
    
    return arrData
  }

  async getAdSpentFromCsv(csvUrl, filters = {}) {
    let adSpent = {
      'total': 0.0,
    }

    let dateFrom = null
    let dateTo = null
    if (filters.hasOwnProperty('date_from') && filters.date_from != '') {
      let dateFrom = new Date(filters.date_from)
    }
    if (filters.hasOwnProperty('date_to') && filters.date_to != '') {
      let dateTo = new Date(filters.date_to)
    }

    let csvStrData = ''
    try {
      const response = await this.$http.get(csvUrl)
      if (response.hasOwnProperty('data')) {
        csvStrData = response.data
        let rows = this.csvToArray(csvStrData)

        if (rows.length == 0) {
          throw 'CSV has no rows'
        }

        let cols = []

        let header = rows[0]
        for (let i = 0; i < header.length; i++) {
          if (header[i] == 'date') continue
          cols.push({
            key: header[i],
            index: i,
          })
          adSpent[header[i]] = 0.0
        }

        let dateIndex = header.indexOf('date')
        if (dateIndex < 0) {
          throw 'CSV rows have no "date" column'
        }

        for (let i = 1; i < rows.length; i++) {
          let row = rows[i]

          // Filter by date
          if (row.length <= dateIndex) {
            throw 'CSV row #' + i + ' has no date'
          }
          let d = new Date(row[dateIndex])
          if (dateFrom != null && d < dateFrom) {
            continue
          }
          if (dateTo != null && d > dateTo) {
            continue
          }

          for (let j = 0; j < cols.length; j++) {
            if (row.length > cols[j].index) {
              if (row[cols[j].index] !== '') {
                let val = parseFloat(row[cols[j].index])
                adSpent[cols[j].key] += val
                adSpent['total'] += val
              }
            }
          }
        }
      }
    } catch (error) {
      // TODO: to delete
      console.error(error)

      // Nothing
    }

    return adSpent
  }
}

export default Ecommerce
