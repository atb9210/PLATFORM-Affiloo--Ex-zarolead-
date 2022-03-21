import Object from './object'

class WarehouseOrder extends Object {

  constructor(zarolead) {
    super(zarolead, '/api/v1/warehouse/orders')
  }

  async getLines(orderId) {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/v1/orders/' + orderId + '/lines', { headers: headers })
      objects = response.data.data
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async createLine(orderId, params) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.post('/api/v1/orders/' + orderId + '/lines', params, { headers: headers })
      object = response.data.data
    } catch (error) {
      // Nothing
    }

    return object
  }

  async updateLine(lineId, params) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.put('/api/v1/orders/lines/' + lineId, params, { headers: headers })
      object = response.data.data
    } catch (error) {
      // Nothing
    }

    return object
  }

  async deleteLine(lineId) {
    let result = false

    try {
      let headers = this.getHeaders()
      const response = await this.$http.delete('/api/v1/orders/lines/' + lineId, { headers: headers })
      result = true
    } catch (error) {
      // Nothing
    }

    return result
  }

}

export default Order
