import ZaroLeadObject from './object'

class WarehouseOrder extends ZaroLeadObject {

  constructor(zarolead) {
    super(zarolead, '/api/v1/warehouse/orders')
  }

  async getLines(orderId) {
    let objects = []

    try {
      objects = await this.$zarolead.get('/api/v1/warehouse/orders/' + orderId + '/lines')
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async createLine(orderId, params) {
    let object = null

    try {
      object = await this.$zarolead.post('/api/v1/warehouse/orders/' + orderId + '/lines', params)
    } catch (error) {
      // Nothing
    }

    return object
  }

  async updateLine(lineId, params) {
    let object = null

    try {
      object = await this.$zarolead.put('/api/v1/warehouse/orders/lines/' + lineId, params)
    } catch (error) {
      // Nothing
    }

    return object
  }

  async deleteLine(lineId) {
    let result = false

    try {
      result = await this.$zarolead.delete('/api/v1/warehouse/orders/lines/' + lineId)
    } catch (error) {
      // Nothing
    }

    return result
  }

}

export default WarehouseOrder
