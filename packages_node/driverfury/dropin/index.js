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

function formatDateTime(dateObj) {
  let dd = String(dateObj.getDate()).padStart(2, '0')
  let mm = String(dateObj.getMonth() + 1).padStart(2, '0')
  let yyyy = dateObj.getFullYear()
  let hh = String(dateObj.getHours()).padStart(2, '0')
  let ii = String(dateObj.getMinutes()).padStart(2, '0')
  
  let formattedDate = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + ii

  return formattedDate
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

  async mapLeadObject(obj, products) {
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

    obj.product_id = ''
    if (obj.hasOwnProperty('info') && obj.info !== null &&
        obj.info.hasOwnProperty('product_id') && obj.info.product_id !== null) {
      obj.product_id = parseInt(obj.info.product_id)
    }

    obj.num_items = 0
    obj.products = []
    obj.product_name = ''
    obj.order_id = ''
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
    obj.last_message = null
    obj.last_message_read = null
    obj.has_new_message = false
    if (obj.hasOwnProperty('info') && obj.info !== null) {
      if (obj.info.hasOwnProperty('product_name')) {
        obj.product_name = obj.info.product_name
      }
      if (obj.info.hasOwnProperty('order_id')) {
        obj.order_id = obj.info.order_id
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
      for (let i = 0; i < obj.products.length; i++) {
        let p = obj.products[i]
        obj.num_items += parseInt(p.quantity)
      }

      obj.completed_at = ''
      if (obj.info.hasOwnProperty('completed_at') && obj.info.completed_at !== null) {
        obj.completed_at = obj.info.completed_at
      }

      obj.recall_at = ''
      if (obj.info.hasOwnProperty('recall_at') && obj.info.recall_at !== null) {
        obj.recall_at = obj.info.recall_at
      }

      obj.last_message = null
      obj.last_message_read = null
      if (obj.info.hasOwnProperty('whatsapp') && obj.info.whatsapp !== null) {
        if (obj.info.whatsapp.hasOwnProperty('last_message') && obj.info.whatsapp.last_message !== null) {
          obj.last_message = obj.info.whatsapp.last_message
        }
        if (obj.info.whatsapp.hasOwnProperty('last_message_read') && obj.info.whatsapp.last_message_read !== null) {
          obj.last_message_read = obj.info.whatsapp.last_message_read
        }
      }
      obj.has_new_message = (obj.last_message_read !== obj.last_message)
    }

    return obj
  }

  async fetchLeads(filters = {}, products = null) {
    let objects = []

    if (products === null) {
      products = await this.fetchProducts()
    }
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
      obj = await this.mapLeadObject(obj, products)
      objects[i] = obj
    }

    if (filters.hasOwnProperty('product_id') && filters.product_id != '') {
      objects = objects.filter((obj) => {
        return (obj.product_id === filters.product_id)
      })
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

  async fetchLead(id, products = null) {
    let object = null

    try {
      if (products === null) {
        products = await this.fetchProducts()
      }
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/dropin/leads/' + id, { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
        object = response.data.data
        object = await this.mapLeadObject(object, products)

        // DEBUG TODO: to delete
        console.log(object)
      }
    } catch (error) {
      // Nothing
    }

    return object
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

  async fetchLeadMessages(leadId) {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/dropin/leads/' + leadId + '/messages', { headers })
      objects = response.data.data

      for (let i = 0; i < objects.length; i++) {
        let obj = objects[i]

        let d = new Date(obj.time*1000)
        obj.datetime = formatDateTime(d)

        objects[i] = obj
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async sendLeadMessage(leadId, message) {
    let object = null

    try {
      let headers = this.getHeaders()
      let params = {
        message: message,
      }
      const response = await this.$http.post('/api/3rd/dropin/leads/' + leadId + '/messages', params, { headers })
      object = response.data.data
    } catch (error) {
      // Nothing
    }

    return object
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
    let result = false

    try {
      let headers = this.getHeaders()
      let params = {
        phone: phone,
        message: message,
      }
      const response = await this.$http.post('/api/3rd/dropin/send-message', params, { headers })
      if (response.hasOwnProperty('data') && response.data.hasOwnProperty('status') && response.data.status === 'success') {
        result = true
      }
    } catch (error) {
      // Nothing
    }

    return result
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

    obj.marked = false
    if (info.hasOwnProperty('marked') && info.marked) {
      obj.marked = true
    }

    obj.order_id = ''
    if (info.hasOwnProperty('order_id') && info.order_id !== null) {
      obj.order_id = info.order_id
    }
    obj.cod_amount = 0.0
    if (info.hasOwnProperty('cod_amount') && info.cod_amount !== null) {
      obj.cod_amount = info.cod_amount
    }
    obj.vat = obj.cod_amount - ((obj.cod_amount / 122) * 100)

    obj.status_check_date = ''
    if (info.hasOwnProperty('status_check_date') && info.status_check_date !== null) {
      obj.status_check_date = info.status_check_date
    }

    obj.packeta_status = ''
    obj.carrier_status_changes = ''
    obj.carrier_status = {
      date: '',
      status: '',
    }
    obj.delivered_at = ''
    obj.started_at = ''
    if (info.hasOwnProperty('packeta') && info.packeta !== null) {
      if (info.packeta.hasOwnProperty('status')) {
        obj.packeta_status = info.packeta.status
      }
    }
    obj.brt_tracking_number = ''
    obj.brt_tracking_url = ''
    if (info.hasOwnProperty('bartolini') && info.bartolini !== null) {
      if (info.bartolini.hasOwnProperty('tracking_number')) {
        obj.brt_tracking_number = info.bartolini.tracking_number
      }
      if (info.bartolini.hasOwnProperty('tracking_url')) {
        obj.brt_tracking_url = info.bartolini.tracking_url
      }
      if (info.bartolini.hasOwnProperty('changes')) {
        obj.carrier_status_changes = info.bartolini.changes
        if (obj.carrier_status_changes.length > 0) {
          obj.carrier_status = obj.carrier_status_changes[0]
        }
        for (let j = 0; j < obj.carrier_status_changes.length; j++) {
          let statusChange = obj.carrier_status_changes[j]
          if (statusChange.status == 'CONSEGNATA') {
            obj.delivered_at = statusChange.date
          } else if (statusChange.status == 'RITIRATA') {
            obj.started_at = statusChange.date
          }
        }
      }
    }
    obj.carrier_status_name = obj.carrier_status.status

    obj.processing_time = ''
    if (obj.created_at != '' && obj.started_at != '') {
      let d1 = new Date(obj.created_at)
      let d2 = new Date(obj.started_at)
      obj.processing_time = Math.round((d2-d1)/(1000*60*60*24)) -1
    }

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
          return (obj.hasOwnProperty('carrier_status_name') && filters.carrier_statuses.includes(obj.carrier_status_name))
        })
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async updateShipping(id, params) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.put('/api/3rd/dropin/shippings/' + id, params, { headers })
      object = response.data.data
    } catch (error) {
      // Nothing
    }

    return object
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

  async toggleMarkShipping(shipping) {
    let object = null

    try {
      let info = shipping.info
      if (info === null || Array.isArray(info)) {
        info = {}
      }
      if (info.hasOwnProperty('marked')) {
        info.marked = !info.marked
      } else {
        info.marked = true
      }
      let params = {
        info: JSON.stringify(info),
      }

      object = await this.updateShipping(shipping.id, params)
    } catch (error) {
      // Nothing
    }

    return object
  }

}

export default Dropin
