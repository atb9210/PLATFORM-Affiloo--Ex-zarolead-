let axios = require('axios')

function getCurrencyValue(s) {
  if (typeof(s) === 'number') {
    return s;
  } else if (typeof(s) === 'string') {
    return parseFloat(s.replace(',', '.'))
  } else {
    return 0;
  }
}

class Warehouse {
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
      const response = await this.$http.get('/api/3rd/warehouse/leads', { headers, params })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    for (let i = 0; i < objects.length; i++) {
      let obj = objects[i]

      obj.full_name = obj.first_name
      if (obj.last_name !== '.') {
        obj.full_name += ' ' + obj.last_name
      }
      if (obj.hasOwnProperty('phone') && obj.phone !== null && obj.phone !== '') {
        obj.phone = this.adjustPhoneNumber(obj.phone)
      }

      obj.product_name = ''
      obj.sku = ''
      obj.price = 0.0
      obj.quantity = 0
      obj.total = 0.0
      obj.contact = false
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

        if (obj.info.hasOwnProperty('delivery') && obj.info.delivery !== null) {
          if (obj.info.delivery.hasOwnProperty('order_number') && obj.info.delivery.order_number !== null) {
            obj.delivery_status = 'sent'
          } else {
            obj.delivery_status = 'error'
          }
        }

        obj.delivery = {
          order_number  : '',
          gateway       : '',
          products      : [],
          status        : 'lead',
          lead_status   : '',
          tracking_link : '',
          completed_at  : '',
        }
        if (obj.info.hasOwnProperty('delivery') && obj.info.delivery !== null) {
          if (obj.info.delivery.hasOwnProperty('order_number')) {
            obj.delivery.order_number = obj.info.delivery.order_number
          }
          if (obj.info.delivery.hasOwnProperty('gateway') && obj.info.delivery.gateway !== null) {
            obj.delivery.gateway = obj.info.delivery.gateway
          }
          if (obj.info.delivery.hasOwnProperty('products') && obj.info.delivery.products !== null) {
            obj.delivery.products = obj.info.delivery.products
          }
          if (obj.info.delivery.hasOwnProperty('status') && obj.info.delivery.status !== null) {
            obj.delivery.status = obj.info.delivery.status
          }
          if (obj.info.delivery.hasOwnProperty('lead_status') && obj.info.delivery.lead_status !== null) {
            obj.delivery.lead_status = obj.info.delivery.lead_status
          }
          if (obj.info.delivery.hasOwnProperty('tracking_link') && obj.info.delivery.tracking_link !== null) {
            obj.delivery.tracking_link = obj.info.delivery.tracking_link
          }
          if (obj.info.delivery.hasOwnProperty('completed_at') && obj.info.delivery.completed_at !== null) {
            obj.delivery.completed_at = obj.info.delivery.completed_at
          }
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
      const response = await this.$http.put('/api/3rd/warehouse/leads/' + id, params, { headers })
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
      const response = await this.$http.delete('/api/3rd/warehouse/leads/' + id, { headers })
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
      const response = await this.$http.get('/api/3rd/warehouse/products', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        objects = response.data.data
      }

      for (let i = 0; i < objects.length; i++) {
        let obj = objects[i]

        obj.min_price = getCurrencyValue(obj.min_price)
        obj.max_price = getCurrencyValue(obj.max_price)

        objects[i] = obj
      }
    } catch (error) {
      // DEBUG TODO: to delete
      console.error(error)

      // Nothing
    }

    return objects
  }

  async createDelivery(lead_id, params) {
    let delivery = null

    try {
      let headers = this.getHeaders()
      let paramsData = {
        lead_id: lead_id,
        params: JSON.stringify(params),
      }
      const response = await this.$http.post('/api/3rd/warehouse/delivery', paramsData, { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        delivery = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return delivery
  }

  async getDelivery(lead_id) {
    let delivery = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/warehouse/leads/' + lead_id + '/delivery', { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        delivery = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return delivery
  }
}

export default Warehouse
