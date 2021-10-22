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

      for (let i = 0; i < objects.length; i++) {
        let obj = objects[i]

        if (obj.hasOwnProperty('price')) {
          if (obj.price !== '') {
            obj.price = parseFloat(obj.price)
          } else {
            obj.price = 0.0
          }
        } else {
          obj.price = 0.0
        }

        if (obj.hasOwnProperty('sale_price')) {
          if (obj.sale_price !== '') {
            obj.sale_price = parseFloat(obj.sale_price)
          } else {
            obj.sale_price = 0.0
          }
        } else {
          obj.sale_price = 0.0
        }

        if (obj.hasOwnProperty('regular_price')) {
          if (obj.regular_price !== '') {
            obj.regular_price = parseFloat(obj.regular_price)
          } else {
            obj.regular_price = 0.0
          }
        } else {
          obj.regular_price = 0.0
        }

        obj.cog_cost = 0.0
        if (obj.hasOwnProperty('meta_data')) {
          for (let mIndex = 0; mIndex < obj.meta_data.length; mIndex++) {
            let md = obj.meta_data[mIndex]
            if (md.key === '_wc_cog_cost' && md.value !== '') {
              obj.cog_cost = parseFloat(md.value)
            }
          }
        }

        objects[i] = obj
      }
    } catch (error) {
      // DEBUG TODO: to delete
      console.error(error)

      // Nothing
    }

    return objects
  }

  async fetchProduct(id) {
    let object = {}

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/ecommerce/products/' + id, { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        object = response.data.data

        if (object.hasOwnProperty('price')) {
          if (object.price != '') {
            object.price = parseFloat(object.price)
          } else {
            object.price = 0.0
          }
        } else {
          object.price = 0.0
        }

        if (object.hasOwnProperty('sale_price')) {
          if (object.sale_price != '') {
            object.sale_price = parseFloat(object.sale_price)
          } else {
            object.sale_price = 0.0
          }
        } else {
          object.sale_price = 0.0
        }

        if (object.hasOwnProperty('regular_price')) {
          if (object.regular_price != '') {
            object.regular_price = parseFloat(object.regular_price)
          } else {
            object.regular_price = 0.0
          }
        } else {
          object.regular_price = 0.0
        }

      }
    } catch (error) {
      // Nothing
    }

    return object
  }

  async fetchProductVariations(id) {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/ecommerce/products/' + id + '/variations', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data
      }

      for (let i = 0; i < objects.length; i++) {
        let obj = objects[i]

        if (obj.hasOwnProperty('sale_price')) {
          if (obj.sale_price != '') {
            obj.sale_price = parseFloat(obj.sale_price)
          } else {
            obj.sale_price = 0.0
          }
        }
        if (obj.hasOwnProperty('regular_price')) {
          if (obj.regular_price != '') {
            obj.regular_price = parseFloat(obj.regular_price)
          } else {
            obj.regular_price = 0.0
          }
        }

        obj.cog_cost = 0.0
        if (obj.hasOwnProperty('meta_data')) {
          for (let mIndex = 0; mIndex < obj.meta_data.length; mIndex++) {
            let md = obj.meta_data[mIndex]
            if (md.key === '_wc_cog_cost' && md.value !== '') {
              obj.cog_cost = parseFloat(md.value)
            }
          }
        }

        objects[i] = obj
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async updateProduct(id, params = {}) {
    let product = null

    try {
      let headers = this.getHeaders()
      let paramsData = {
        params: JSON.stringify(params),
      }
      const response = await this.$http.put('/api/3rd/ecommerce/products/' + id, paramsData, { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        product = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return product
  }

  async updateProductVariation(pid, vid, params = {}) {
    let variation = null

    try {
      let headers = this.getHeaders()
      let paramsData = {
        params: JSON.stringify(params),
      }
      const response = await this.$http.put('/api/3rd/ecommerce/products/' + pid + '/variations/' + vid, paramsData, { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        variation = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return variation
  }

  async updateProductVariations(pid, params = {}) {
    let variations = []

    try {
      let tmpVariations = await this.fetchProductVariations(pid)
      for (let i = 0; i < tmpVariations.length; i++) {
        let tmpVar = tmpVariations[i]
        const variation = await this.updateProductVariation(pid, tmpVar.id, params)
        variations.push(variation)
      }
    } catch (error) {
      // Nothing
    }

    return variations
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

  async fetchCarts() {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/ecommerce/carts', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data
      }
    } catch (error) {
      //
    }

    return objects
  }
}

export default Ecommerce
