let axios = require('axios')

class ZaroLead {

  constructor(apiKey) {
    this.apiKey = apiKey
    this.$http = axios
  }

  getHeaders() {
    return {
      Authorization: 'Bearer ' + this.apiKey,
    }
  }

  async get(url, params = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get(url, { headers, params })
      objects = response.data.data
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async post(url, params = {}) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.post(url, params, { headers })
      object = response.data.data
    } catch (error) {
      // Nothing
    }

    return object
  }

  async put(url, params = {}) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.put(url, params, { headers })
      object = response.data.data
    } catch (error) {
      // Nothing
    }

    return object
  }

  async delete(url, params = {}) {
    let result = false

    try {
      let headers = this.getHeaders()
      const response = await this.$http.delete(url, { headers, params })
      result = true
    } catch (error) {
      // Nothing
    }

    return result
  }

}

import User from './user'
import Customer from './customer'
import WarehouseOrder from './warehouse_order'

export {
  ZaroLead,
  User,
  Customer,
  WarehouseOrder,
}
