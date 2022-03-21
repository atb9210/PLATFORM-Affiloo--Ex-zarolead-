let axios = require('axios')

class Customer {
  constructor(apiKey) {
    this.apiKey = apiKey
    this.$http = axios
  }

  getHeaders() {
    return {
      Authorization: 'Bearer ' + this.apiKey,
    }
  }

  async create(params) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.post('/api/v1/customers', params, { headers: headers })
      object = response.data.data
    } catch (error) {
      // Nothing
    }

    return object
  }

  async update(id, params) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.put('/api/v1/customers/' + id, params, { headers: headers })
      object = response.data.data
    } catch (error) {
      // Nothing
    }

    return object
  }

  async delete(id) {
    let result = false

    try {
      let headers = this.getHeaders()
      const response = await this.$http.delete('/api/v1/customers/' + id, { headers: headers })
      result = true
    } catch (error) {
      // Nothing
    }

    return result
  }

  async getChatMessages(id) {
    let messages = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/v1/customers/' + id + '/messages', { headers: headers })
      messages = response.data.data
    } catch (error) {
      // Nothing
    }

    return messages
  }

  async sendMessage(id, message) {
    let result = false

    try {
      let headers = this.getHeaders()
      const response = await this.$http.post('/api/v1/customers/' + id + '/messages', { message: message}, { headers: headers })
      result = true
    } catch (error) {
      // Nothing
    }

    return result
  }
}

export default Customer
