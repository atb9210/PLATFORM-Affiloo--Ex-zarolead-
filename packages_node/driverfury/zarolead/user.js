let axios = require('axios')

class User {
  constructor(apiKey) {
    this.apiKey = apiKey
    this.$http = axios
  }

  getHeaders() {
    return {
      Authorization: 'Bearer ' + this.apiKey,
    }
  }

  async me() {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/v1/users', params, { headers: headers })
      object = response.data.data
    } catch (error) {
      // Nothing
    }

    return object
  }

  async create(params) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.post('/api/v1/users', params, { headers: headers })
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
      const response = await this.$http.put('/api/v1/users/' + id, params, { headers: headers })
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
      const response = await this.$http.delete('/api/v1/users/' + id, { headers: headers })
      result = true
    } catch (error) {
      // Nothing
    }

    return result
  }
}

export default User
