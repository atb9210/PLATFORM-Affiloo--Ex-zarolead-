import ZaroLeadObject from './object'

class Customer extends ZaroLeadObject {

  constructor(zarolead) {
    super(zarolead, '/api/v1/customers')
  }

  async getMessages(id) {
    const res = await this.$zarolead.get('/api/v1/customers/' + id + '/messages')
    return res
  }

  async sendMessage(id, message) {
    const res = await this.$zarolead.post('/api/v1/customers/' + id + '/messages', { message })
    return res
  }

}

export default Customer
