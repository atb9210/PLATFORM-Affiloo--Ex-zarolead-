import ZaroLeadObject from './object'

class WarehouseShipping extends ZaroLeadObject {

  constructor(zarolead) {
    super(zarolead, '/api/v1/warehouse/shippings')
  }

  async fetchCarriers() {
    let objects = await this.$zarolead.get('/api/v1/warehouse/shipping-carriers')
    return objects
  }

}

export default WarehouseShipping
