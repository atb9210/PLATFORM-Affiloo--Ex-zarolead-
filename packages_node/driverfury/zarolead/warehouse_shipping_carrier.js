import ZaroLeadObject from './object'

class WarehouseShippingCarrier extends ZaroLeadObject {

  constructor(zarolead) {
    super(zarolead, '/api/v1/warehouse/shipping-carriers')
  }

}

export default WarehouseShippingCarrier
