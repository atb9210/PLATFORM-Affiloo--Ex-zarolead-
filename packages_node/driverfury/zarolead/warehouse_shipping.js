import ZaroLeadObject from './object'

class WarehouseShipping extends ZaroLeadObject {

  constructor(zarolead) {
    super(zarolead, '/api/v1/warehouse/shippings')
  }

}

export default WarehouseShipping
