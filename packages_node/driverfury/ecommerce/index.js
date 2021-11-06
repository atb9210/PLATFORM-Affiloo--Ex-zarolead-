let axios = require('axios')

class Ecommerce {
  constructor(apiKey) {
    this.apiKey = apiKey
    this.$http = axios
  }

  adjustPhoneNumber(phone) {
    phone = phone.replace(' ', '')
    if (phone[0] != '+' && phone.length <= 10) {
      phone = '+39' + phone
    }
    return phone
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
        } else {
          obj.total = 0.0
        }

        obj.customer_name = ''
        if (obj.hasOwnProperty('billing')) {
          if (obj.billing.hasOwnProperty('first_name')) {
            obj.customer_name += obj.billing.first_name
          }
          if (obj.billing.hasOwnProperty('last_name')) {
            obj.customer_name += ' ' + obj.billing.last_name
          }
        }

        obj.notes = ''
        if (obj.hasOwnProperty('meta_data')) {
          for (let mIndex = 0; mIndex < obj.meta_data.length; mIndex++) {
            let md = obj.meta_data[mIndex]
            if (md.key === '_zl_notes') {
              obj.notes = md.value
              break
            }
          }
        }

        obj.num_items = 0
        if (obj.hasOwnProperty('line_items')) {
          for (let itemIndex = 0; itemIndex < obj.line_items.length; itemIndex++) {
            if (obj.line_items[itemIndex].hasOwnProperty('quantity')) {
              obj.num_items += parseInt(obj.line_items[itemIndex].quantity)
            }
          }
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
              break
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
              break
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

  async parseCsv(csvUrl, filters = {}) {
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

  async getAdvertisingCsv() {
    let csv = ''

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/ecommerce/advertising/csv', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        csv = response.data.data
      }
    } catch (error) {
      // Nothing
    }
    return csv
  }

  async updateAdvertisingCsv(csvUrl) {
    let params = {
      csv: csvUrl,
    }
    try {
      let headers = this.getHeaders()
      const response = await this.$http.put('/api/3rd/ecommerce/advertising/csv', params, { headers })
      return true
    } catch (error) {
      // Nothing
    }
    return false
  }

  async getExtraCostCsv() {
    let csv = ''

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/ecommerce/extra-cost/csv', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        csv = response.data.data
      }
    } catch (error) {
      // Nothing
    }
    return csv
  }

  async updateExtraCostCsv(csvUrl) {
    let params = {
      csv: csvUrl,
    }
    try {
      let headers = this.getHeaders()
      const response = await this.$http.put('/api/3rd/ecommerce/extra-cost/csv', params, { headers })
      return true
    } catch (error) {
      // Nothing
    }
    return false
  }

  async getShippingCsv() {
    let csv = ''

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/ecommerce/shipping/csv', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        csv = response.data.data
      }
    } catch (error) {
      // Nothing
    }
    return csv
  }

  async updateShippingCsv(csvUrl) {
    let params = {
      csv: csvUrl,
    }
    try {
      let headers = this.getHeaders()
      const response = await this.$http.put('/api/3rd/ecommerce/shipping/csv', params, { headers })
      return true
    } catch (error) {
      // Nothing
    }
    return false
  }

  async getAdSpent(filters = {}) {
    let csv = await this.getAdvertisingCsv()

    if (csv == '') {
      return {}
    }
    let res = await this.parseCsv(csv, filters)
    return res
  }

  async getShippingCost(filters = {}) {
    let csv = await this.getShippingCsv()

    if(csv == '') {
      return {}
    }
    let res = await this.parseCsv(csv, filters)
    return res
  }

  async getExtraCost(filters = {}) {
    let csv = await this.getExtraCostCsv()

    if(csv == '') {
      return {}
    }
    let res = await this.parseCsv(csv, filters)
    return res
  }

  async updateOrder(id, params = {}) {
    let order = null

    try {
      let headers = this.getHeaders()
      let paramsData = {
        params: JSON.stringify(params),
      }
      const response = await this.$http.put('/api/3rd/ecommerce/orders/' + id, paramsData, { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        order = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return order
  }

  async pushOrder(id) {
    try {
      let headers = this.getHeaders()
      await this.$http.post('/api/3rd/ecommerce/orders/' + id + '/push', {}, { headers: headers })
      return true
    } catch (error) {
      return false
    }
    return false
  }

  async fetchPaymentMethods(filters = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/ecommerce/payment-methods', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data

        for (let i = 0; i < objects.length; i++) {
          let obj = objects[i]

          if (obj.hasOwnProperty('fixed_fee')) {
            obj.fixed_fee = parseFloat(obj.fixed_fee)
          } else {
            obj.fixed_fee = 0.0
          }

          if (obj.hasOwnProperty('flat_fee')) {
            obj.flat_fee = parseFloat(obj.flat_fee)
          } else {
            obj.flat_fee = 0.0
          }
        }
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async updatePaymentMethodFees(id, fixed, flat) {
    let params = {
      fixed: fixed,
      flat: flat,
    }

    try {
      let headers = this.getHeaders()
      await this.$http.put('/api/3rd/ecommerce/payment-methods/' + id + '/fees', params, { headers })
    } catch (error) {
      return false
    }

    return true
  }

  async fetchCarts() {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/ecommerce/carts', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data

        for (let i = 0; i < objects.length; i++) {
          let obj = objects[i]

          obj.full_name = ''
          if (obj.hasOwnProperty('name') && obj.name !== null) {
            obj.full_name = obj.name
          }
          if (obj.hasOwnProperty('surname') && obj.surname !== null && obj.surname !== '') {
            obj.full_name += ' ' + obj.surname
          }

          if (obj.hasOwnProperty('phone') && obj.phone !== null && obj.phone !== '') {
            obj.phone = this.adjustPhoneNumber(obj.phone)
          }

          if (!obj.hasOwnProperty('location') || obj.location === null) {
            obj.location = ''
          } else {
            let location = ''
            if (obj.location.hasOwnProperty('country') && obj.location.country != '') {
              location += obj.location.country
            }
            if (obj.location.hasOwnProperty('city') && obj.location.city != '') {
              location += ', ' + obj.location.city
            }
            if (obj.location.hasOwnProperty('postcode') && obj.location.postcode != '') {
              location += ', ' + obj.location.postcode
            }
            obj.location = location
          }

          if (obj.hasOwnProperty('cart_total') && obj.cart_total !== null && obj.cart_total !== '') {
            obj.cart_total = parseFloat(obj.cart_total)
          } else {
            obj.cart_total = 0.0
          }

          obj.products = []
          if (obj.hasOwnProperty('cart_contents') && obj.cart_contents != null) {
            for (let j = 0; j < obj.cart_contents.length; j++) {
              obj.products.push(obj.cart_contents[j].product_title)
            }
          }

          objects[i] = obj
        }

        objects.sort((a, b) => {
          let da = new Date(a.time)
          let db = new Date(b.time)
          if (da < db) {
            return 1
          } else if (da > db) {
            return -1
          } else {
            return 0
          }
        })
      }
    } catch (error) {
      //
    }

    return objects
  }

  async fetchLeads(filters = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/ecommerce/leads', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    for (let i = 0; i < objects.length; i++) {
      let obj = objects[i]

      obj.full_name = obj.first_name + ' ' + obj.last_name
      if (obj.hasOwnProperty('phone') && obj.phone !== null && obj.phone !== '') {
        obj.phone = this.adjustPhoneNumber(obj.phone)
      }

      obj.sku = ''
      obj.price = 0.0
      obj.quantity = 0
      obj.total = 0.0
      obj.contact = false
      if (obj.hasOwnProperty('info') && obj.info !== null) {
        if (obj.info.hasOwnProperty('sku')) {
          obj.sku = obj.info.sku
        }
        if (obj.info.hasOwnProperty('price')) {
          obj.price = parseFloat(obj.info.price)
        }
        if (obj.info.hasOwnProperty('quantity')) {
          obj.quantity = parseInt(obj.info.quantity)
        }
        if (obj.info.hasOwnProperty('total')) {
          obj.total = parseFloat(obj.info.total)
        }
        if (obj.info.hasOwnProperty('contact')) {
          obj.contact = obj.info.contact
        }
      }

      objects[i] = obj
    }

    return objects
  }

  async updateLead(id, params) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.put('/api/3rd/ecommerce/leads/' + id, params, { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        object = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return object
  }

  async deleteLead(id) {
    try {
      let headers = this.getHeaders()
      const response = await this.$http.delete('/api/3rd/ecommerce/leads/' + id, { headers })
      return true
    } catch (error) {
      return false
    }
    return false
  }
}

export default Ecommerce
