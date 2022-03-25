import ZaroLeadObject from './object'

class WarehouseProduct extends ZaroLeadObject {

  constructor(zarolead) {
    super(zarolead, '/api/v1/warehouse/products')
  }

  async getVariations(productId) {
    let objects = []

    try {
      objects = await this.$zarolead.get('/api/v1/warehouse/products/' + productId + '/variations')
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async createVariation(productId, params) {
    let object = null

    try {
      object = await this.$zarolead.post('/api/v1/warehouse/products/' + productId + '/variations', params)
    } catch (error) {
      // Nothing
    }

    return object
  }

  async updateVariation(variationId, params) {
    let object = null

    try {
      object = await this.$zarolead.put('/api/v1/warehouse/products/variations/' + variationId, params)
    } catch (error) {
      // Nothing
    }

    return object
  }

  async deleteVariation(variationId) {
    let result = false

    try {
      result = await this.$zarolead.delete('/api/v1/warehouse/products/variations/' + variationId)
    } catch (error) {
      // Nothing
    }

    return result
  }

}

export default WarehouseProduct
