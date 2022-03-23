import ZaroLeadObject from './object'

class WarehouseProduct extends ZaroLeadObject {

  constructor(zarolead) {
    super(zarolead, '/api/v1/warehouse/products')
  }

}

export default WarehouseProduct
