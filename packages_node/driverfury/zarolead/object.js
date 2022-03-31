class ZaroLeadObject {

  constructor(zarolead, apiBasePath = '') {
    this.$zarolead = zarolead
    this.apiBasePath = apiBasePath
  }

  async fetch(filters = {}) {
    const res = await this.$zarolead.get(this.apiBasePath, filters)
    return res
  }

  async show(id, params = {}) {
    const res = await this.$zarolead.get(this.apiBasePath + '/' + id, params)
    return res
  }

  async create(params) {
    const res = await this.$zarolead.post(this.apiBasePath, params)
    return res
  }

  async update(id, params) {
    const res = await this.$zarolead.put(this.apiBasePath + '/' + id, params)
    return res
  }

  async delete(id) {
    const res = await this.$zarolead.delete(this.apiBasePath + '/' + id)
    return res
  }

}

export default ZaroLeadObject
