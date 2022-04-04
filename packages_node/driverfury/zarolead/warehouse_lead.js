import ZaroLeadObject from './object'

class WarehouseLead extends ZaroLeadObject {

  constructor(zarolead) {
    super(zarolead, '/api/v1/warehouse/leads')
  }

}

export default WarehouseLead
