import ZaroLeadObject from './object'

function formatDateTime(dateObj) {
  let dd = String(dateObj.getDate()).padStart(2, '0')
  let mm = String(dateObj.getMonth() + 1).padStart(2, '0')
  let yyyy = dateObj.getFullYear()
  let hh = String(dateObj.getHours()).padStart(2, '0')
  let ii = String(dateObj.getMinutes()).padStart(2, '0')
  
  let formattedDate = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + ii

  return formattedDate
}

class Customer extends ZaroLeadObject {

  constructor(zarolead) {
    super(zarolead, '/api/v1/customers')
  }

  async fetchMessages(id) {
    const res = await this.$zarolead.get('/api/v1/customers/' + id + '/messages')
    for (let i = 0; i < res.length; i++) {
      let obj = res[i]

      let d = new Date(obj.time*1000)
      obj.datetime = formatDateTime(d)
      obj.date_obj = d

      res[i] = obj
    }
    return res
  }

  async sendMessage(id, message) {
    const res = await this.$zarolead.post('/api/v1/customers/' + id + '/messages', { message })
    return res
  }

}

export default Customer
