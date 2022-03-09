let axios = require('axios')

import { xml2json } from '@assets/utils/xml2json'

function getCurrencyValue(s) {
  if (typeof(s) === 'number') {
    return s;
  } else if (typeof(s) === 'string') {
    return parseFloat(s.replace(',', '.'))
  } else {
    return 0;
  }
}

class Dropin {

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

  async fetchLeads(filters = {}) {
    let objects = []

    let products = await this.fetchProducts()
    try {
      let headers = this.getHeaders()
      let params = {}

      if (filters.hasOwnProperty('date_from') && filters.date_from != '') {
        params.filter_date_from = filters.date_from
      }
      if (filters.hasOwnProperty('date_to') && filters.date_to != '') {
        params.filter_date_to = filters.date_to
      }
      if (filters.hasOwnProperty('status') && filters.status != '') {
        params.filter_status = filters.status
      }
      if (filters.hasOwnProperty('product') && filters.product != '') {
        params.filter_product = filters.product
      }
      const response = await this.$http.get('/api/3rd/dropin/leads', { headers, params })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    for (let i = 0; i < objects.length; i++) {
      let obj = objects[i]

      let first_name = obj.first_name
      let last_name = obj.last_name

      // When we have the full name in the first_name field we need to split
      // into first name and last name
      if (last_name === '.' || last_name.trim() === '') {
        let tmp = first_name.split(' ')
        if (tmp.length > 0) {
          first_name = tmp[0]
          last_name = ''
          for (let j = 1; j < tmp.length; j++) {
            last_name += ' ' + tmp[j]
          }
          last_name = last_name.trim()
        }
      }

      obj.first_name = first_name
      obj.last_name = last_name

      obj.full_name = obj.first_name
      if (obj.last_name !== '.') {
        obj.full_name += ' ' + obj.last_name
      }
      if (obj.hasOwnProperty('phone') && obj.phone !== null && obj.phone !== '') {
        obj.phone = this.adjustPhoneNumber(obj.phone)
      }

      obj.products = []
      obj.product_name = ''
      obj.sku = ''
      obj.price = 0.0
      obj.quantity = 0
      obj.total = 0.0
      obj.contact = false
      obj.call_count = 0
      obj.paid = false
      obj.order_number = ''
      obj.carrier = ''
      obj.order_status = ''
      obj.gateway = ''
      obj.delivery_status = 'not sent'
      if (obj.hasOwnProperty('info') && obj.info !== null) {
        if (obj.info.hasOwnProperty('product_name')) {
          obj.product_name = obj.info.product_name
        }
        if (obj.info.hasOwnProperty('sku')) {
          obj.sku = obj.info.sku
        }
        if (obj.info.hasOwnProperty('price')) {
          obj.price = getCurrencyValue(obj.info.price)
        }
        if (obj.info.hasOwnProperty('quantity')) {
          obj.quantity = parseInt(obj.info.quantity)
        }
        if (obj.info.hasOwnProperty('total')) {
          obj.total = getCurrencyValue(obj.info.total)
        }
        if (obj.info.hasOwnProperty('contact')) {
          obj.contact = obj.info.contact
        }
        if (obj.info.hasOwnProperty('call_count')) {
          obj.call_count = obj.info.call_count
        }
        if (obj.info.hasOwnProperty('paid')) {
          obj.paid = obj.info.paid
        }

        if (obj.info.hasOwnProperty('gateway')) {
          obj.gateway = obj.info.gateway
        }


        if (!obj.info.hasOwnProperty('products') || obj.info.products === null || obj.info.products.length === 0) {
          obj.info.products = []
          let p = await this.getProductBySku(obj.sku, products)
          if (p !== null) {
            let quantity = obj.quantity

            let price = 0.0
            if (obj.info.hasOwnProperty('price')) {
              price = getCurrencyValue(obj.info.price)
            } else {
              price = getCurrencyValue(p.sale_price)
            }

            let total = 0.0
            if (obj.info.hasOwnProperty('total')) {
              total = getCurrencyValue(obj.info.total)
            } else {
              total = price*quantity
            }

            obj.info.products.push({
              id: p.id,
              name: p.name,
              sku: p.sku,
              price: price,
              quantity: quantity,
              total: total,
              upsell: false,
            })
          }
        }
        obj.products = obj.info.products

        obj.completed_at = ''
        if (obj.info.hasOwnProperty('completed_at') && obj.info.completed_at !== null) {
          obj.completed_at = obj.info.completed_at
        }

        obj.recall_at = ''
        if (obj.info.hasOwnProperty('recall_at') && obj.info.recall_at !== null) {
          obj.recall_at = obj.info.recall_at
        }
      }

      objects[i] = obj
    }

    if (filters.hasOwnProperty('statuses') && filters.statuses.length > 0) {
      objects = objects.filter((obj) => {
        return (obj.hasOwnProperty('status') && filters.statuses.includes(obj.status))
      })
    }

    if (filters.hasOwnProperty('order_status') && filters.order_status != '') {
      objects = objects.filter((obj) => {
        return (obj.hasOwnProperty('order_status') && obj.order_status === filters.order_status)
      })
    }

    return objects
  }

  async createLead(params) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.post('/api/3rd/dropin/leads', params, { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        object = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return object
  }

  async updateLead(id, params) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.put('/api/3rd/dropin/leads/' + id, params, { headers })
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
      const response = await this.$http.delete('/api/3rd/dropin/leads/' + id, { headers })
      return true
    } catch (error) {
      return false
    }
    return false
  }

  async fetchProducts(filters = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/dropin/products', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data
      }

      for (let i = 0; i < objects.length; i++) {
        let obj = objects[i]

        // DEBUG NOTE TODO OPTIMIZE
        // TODO: Index as id => Not good
        // TODO: You must change this!!!!!!
        obj.id = i

        if (obj.hasOwnProperty('dropshipping_price')) {
          obj.dropshipping_price = parseFloat(obj.dropshipping_price)
        } else {
          obj.dropshipping_price = 0.0
        }
        if (obj.hasOwnProperty('sale_price')) {
          obj.sale_price = parseFloat(obj.sale_price)
        } else {
          obj.sale_price = 0.0
        }
        if (obj.hasOwnProperty('price')) {
          obj.price = parseFloat(obj.price)
        } else {
          obj.price = obj.sale_price
        }
        if (obj.hasOwnProperty('quantity')) {
          obj.quantity = parseInt(obj.quantity)
        } else {
          obj.quantity = 0
        }

        objects[i] = obj
      }

      if (filters.hasOwnProperty('categories') && filters.categories.length > 0) {
        objects = objects.filter((obj) => filters.categories.includes(obj.category))
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async getProductBySku(sku, products = null) {
    let object = null

    let objects = []
    if (products === null) {
      objects = await this.fetchProducts()
    } else {
      objects = products
    }

    for (let i = 0; i < objects.length; i++) {
      if (sku == objects[i].sku) {
        object = objects[i]
        break
      }
    }

    return object
  }

  async createProduct(params) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.post('/api/3rd/dropin/products', params, { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        object = response.data.data
      }
    } catch (error) {
      // Nothing

      // DEBUG TODO: to delete
      console.log(error)
    }

    return object
  }

  async deleteProduct(sku) {
    let result = false

    try {
      let headers = this.getHeaders()
      const response = await this.$http.delete('/api/3rd/dropin/products/' + sku, { headers })
      result = true
    } catch (error) {
      // Nothing

      // DEBUG TODO: to delete
      console.log(error)
    }

    return result
  }

  async fetchDropinProducts() {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/dropin/products-dropin', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data

        for (let i = 0; i < objects.length; i++) {
          let obj = objects[i]

          if (obj.hasOwnProperty('dropshipping_price')) {
            obj.dropshipping_price = parseFloat(obj.dropshipping_price)
          } else {
            obj.dropshipping_price = 0.0
          }
          if (obj.hasOwnProperty('sale_price')) {
            obj.sale_price = parseFloat(obj.sale_price)
          } else {
            obj.sale_price = 0.0
          }
          if (obj.hasOwnProperty('cost')) {
            obj.cost = parseFloat(obj.cost)
          } else {
            obj.cost = 0.0
          }
          if (obj.hasOwnProperty('quantity')) {
            obj.quantity = parseInt(obj.quantity)
          } else {
            obj.quantity = 0.0
          }

          objects[i] = obj
        }
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async sendMessage(phone, message) {
    try {
      let headers = this.getHeaders()
      let params = {
        phone: phone,
        message: message,
      }
      const response = await this.$http.post('/api/3rd/dropin/send-message', params, { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('status') && response.data.status === 'success') {
        return true
      }
    } catch (error) {
      // Nothing
    }
    return false
  }

  mapShippingObject(obj) {
    if (obj.hasOwnProperty('amount')) {
      obj.amount = parseFloat(obj.amount)
    } else {
      obj.amount = 0.0
    }
    if (obj.hasOwnProperty('delivery_price')) {
      obj.delivery_price = parseFloat(obj.delivery_price)
    } else {
      obj.delivery_price = 0.0
    }
    if (obj.hasOwnProperty('tax')) {
      obj.tax = parseFloat(obj.tax)
    } else {
      obj.tax = 0.0
    }

    let info = obj.info
    if (info === null) {
      info = {}
    }

    obj.order_id = ''
    if (info.hasOwnProperty('order_id') && info.order_id !== null) {
      obj.order_id = info.order_id
    }
    obj.cod_amount = 0.0
    if (info.hasOwnProperty('cod_amount') && info.cod_amount !== null) {
      obj.cod_amount = info.cod_amount
    }

    obj.packeta_status = ''
    obj.carrier_status_changes = ''
    obj.carrier_status = {
      date: '',
      status: '',
    }
    obj.delivered_at = ''
    if (info.hasOwnProperty('packeta') && info.packeta !== null) {
      if (info.packeta.hasOwnProperty('status')) {
        obj.packeta_status = info.packeta.status
      }
      if (info.packeta.hasOwnProperty('changes')) {
        obj.carrier_status_changes = info.packeta.changes
        if (obj.carrier_status_changes.length > 0) {
          obj.carrier_status = obj.carrier_status_changes[obj.carrier_status_changes.length - 1]
        }
        for (let j = 0; j < obj.carrier_status_changes.length; j++) {
          let statusChange = obj.carrier_status_changes[j]
          if (statusChange.status == 'CONSEGNATA') {
            obj.delivered_at = statusChange.date
            break
          }
        }
      }
    }
    obj.carrier_status_name = obj.carrier_status.name

    return obj
  }

  async fetchShippings(filters = {}) {
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
      const response = await this.$http.get('/api/3rd/dropin/shippings', { headers, params })
      objects = response.data.data

      for (let i = 0; i < objects.length; i++) {
        this.mapShippingObject(objects[i])
      }

      if (filters.hasOwnProperty('dropin_statuses') && filters.dropin_statuses.length > 0) {
        objects = objects.filter((obj) => {
          return (obj.hasOwnProperty('status') && filters.dropin_statuses.includes(obj.status))
        })
      }
      if (filters.hasOwnProperty('packeta_statuses') && filters.packeta_statuses.length > 0) {
        objects = objects.filter((obj) => {
          return (obj.hasOwnProperty('packeta_status') && filters.packeta_statuses.includes(obj.packeta_status))
        })
      }
      if (filters.hasOwnProperty('carrier_statuses') && filters.carrier_statuses.length > 0) {
        objects = objects.filter((obj) => {
          return (obj.hasOwnProperty('carrier_status') && filters.carrier_statuses.includes(obj.carrier_status))
        })
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async refreshShipping(id) {
    let object = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/dropin/shippings/' + id + '/refresh', { headers })
      object = response.data.data
      this.mapShippingObject(object)
    } catch (error) {
      // Nothing
    }

    return object
  }

  async refreshShippingsTable() {
    let result = false

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/dropin/shippings/refresh', { headers })
      result = (response.data.success == true)
    } catch (error) {
      // Nothing
    }

    return result
  }

}

export default Dropin
