let axios = require('axios')

class Facebook {
  constructor(apiKey) {
    this.apiKey = apiKey
    this.$http = axios

    this.accessToken = ''

    this.apiUrl = 'https://graph.facebook.com/v12.0'
  }

  setAccessToken(accessToken) {
    this.accessToken = accessToken
  }

  getHeaders() {
    return {
      Authorization: 'Bearer ' + this.apiKey,
    }
  }

  async fetchAdvertisers() {
    let objects = []

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/facebook/advertisers', { headers })
      if (response !== null && response.hasOwnProperty('data') &&
          response.data !== null && response.data.hasOwnProperty('data') && response.data.data !== null) {
        objects = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async createAdvertiser(accessToken, name = null) {
    let object = null

    try {
      let headers = this.getHeaders()
      let params = {
        access_token: accessToken
      }
      if (name !== null && name !== '') {
        params.name = name
      }
      const response = await this.$http.post('/api/3rd/facebook/advertisers', params, { headers })
      if (response !== null && response.hasOwnProperty('data') &&
          response.data !== null && response.data.hasOwnProperty('data') && response.data.data !== null) {
        object = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return object
  }

  async updateAdvertiser(id, accessToken, name = null) {
    let object = null

    try {
      let headers = this.getHeaders()
      let params = {
        access_token: accessToken
      }
      if (name !== null && name !== '') {
        params.name = name
      }
      const response = await this.$http.put('/api/3rd/facebook/advertisers/' + id, params, { headers })
      if (response !== null && response.hasOwnProperty('data') &&
          response.data !== null && response.data.hasOwnProperty('data') && response.data.data !== null) {
        object = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return object
  }

  async deleteAdvertiser(id) {
    let result = false

    try {
      let headers = this.getHeaders()
      const response = await this.$http.delete('/api/3rd/facebook/advertisers/' + id, { headers })
      result = true
    } catch (error) {
      // Nothing
    }

    return result
  }

  async fetchAdAccounts(filters = {}) {
    let objects = []

    try {
      let timeRange = {}
      if (filters.hasOwnProperty('date_from') && filters.date_from != '') {
        timeRange.since = filters.date_from
      }
      if (filters.hasOwnProperty('date_to') && filters.date_to != '') {
        timeRange.until = filters.date_to
      }
      let insightsParam = 'insights.time_range(' + JSON.stringify(timeRange) + ')'
      insightsParam += '{spend,cpm,actions,action_values}'

      let params = {
        access_token: this.accessToken,
        fields: 'id,name,account_status,' + insightsParam,
      }
      
      const response = await this.$http.get(this.apiUrl + '/me/adaccounts', { params })
      if (response !== null && response.hasOwnProperty('data') &&
          response.data !== null && response.data.hasOwnProperty('data') && response.data.data !== null) {
        objects = response.data.data

        for (let i = 0; i < objects.length; i++) {
          let obj = objects[i]

          obj.status = 2
          if (obj.hasOwnProperty('account_status') && obj.account_status !== null) {
            obj.status = obj.account_status
          }

          obj.spend = 0.0
          obj.link_clicks = 0.0
          obj.conversions = 0
          obj.conversions_value = 0.0
          obj.cpa = 0.0
          obj.cr = 0.0
          obj.cpm = 0.0
          obj.profit = 0.0
          obj.roi = 0.0
          obj.budget = 0.0

          if (obj.hasOwnProperty('insights') && obj.insights !== null &&
              obj.insights.hasOwnProperty('data') && obj.insights.data !== null &&
              obj.insights.data.length > 0) {
            let insights = obj.insights.data[0]

            if (insights.hasOwnProperty('spend')) {
              obj.spend = parseFloat(insights.spend)
            }
            if (insights.hasOwnProperty('actions') && insights.actions !== null) {
              for (let i = 0; i < insights.actions.length; i++) {
                let a = insights.actions[i]
                if (a.hasOwnProperty('action_type') && typeof(a.action_type) === 'string') {
                  if (a.action_type === 'omni_purchase') {
                    if (a.hasOwnProperty('value') && a.value !== null) {
                      obj.conversions += parseInt(a.value)
                    }
                  } else if (a.action_type === 'link_click') {
                    if (a.hasOwnProperty('value') && a.value !== null) {
                      obj.link_clicks += parseInt(a.value)
                    }
                  }
                }
              }
              obj.cpm = parseFloat(insights.cpm)
            }
            if (insights.hasOwnProperty('action_values') && insights.action_values !== null) {
              for (let i = 0; i < insights.action_values.length; i++) {
                let a = insights.action_values[i]
                if (a.hasOwnProperty('action_type') && typeof(a.action_type) === 'string') {
                  if (a.action_type === 'omni_purchase') {
                    if (a.hasOwnProperty('value') && a.value !== null) {
                      obj.conversions_value += parseFloat(a.value)
                    }
                  }
                }
              }
              obj.cpm = parseFloat(insights.cpm)
            }

            if (insights.hasOwnProperty('cpm')) {
              obj.cpm = parseFloat(insights.cpm)
            }

          }

          obj.cpa = (obj.conversions > 0) ? (obj.spend/obj.conversions) : obj.spend
          obj.cr = (obj.link_clicks > 0) ? (100*obj.conversions/obj.link_clicks) : 0
          obj.profit = obj.conversions_value - obj.spend
          obj.roi = (obj.spend > 0) ? (((obj.profit/obj.spend)-1)*100) : 0

          objects[i] = obj
        }
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async fetchObjects(type, parentId, filters = {}) {
    let objects = []

    try {
      let timeRange = {}
      if (filters.hasOwnProperty('date_from') && filters.date_from != '') {
        timeRange.since = filters.date_from
      }
      if (filters.hasOwnProperty('date_to') && filters.date_to != '') {
        timeRange.until = filters.date_to
      }
      let insightsParam = 'insights.time_range(' + JSON.stringify(timeRange) + ')'
      insightsParam += '{spend,cpm,actions,action_values}'

      let params = {
        access_token: this.accessToken,
        fields: 'id,name,effective_status,daily_budget,bid_amount,bid_strategy,' + insightsParam,
      }
      
      const response = await this.$http.get(this.apiUrl + '/' + parentId + '/' + type + 's', { params })
      if (response !== null && response.hasOwnProperty('data') &&
          response.data !== null && response.data.hasOwnProperty('data') && response.data.data !== null) {
        objects = response.data.data

        for (let i = 0; i < objects.length; i++) {
          let obj = objects[i]

          obj.type = type

          obj.status = 'ARCHIVED'
          if (obj.hasOwnProperty('effective_status') && obj.effective_status !== null) {
            obj.status = obj.effective_status
          }

          obj.cbo = (obj.hasOwnProperty('daily_budget') && obj.hasOwnProperty('bid_strategy'))

          obj.budget = 0.0
          if (obj.hasOwnProperty('daily_budget') && obj.daily_budget !== null) {
            obj.budget = parseInt(obj.daily_budget)/100
          } else {
            obj.daily_budget = 0
          }
          if (obj.hasOwnProperty('bid_amount') && obj.bid_amount !== null) {
            obj.bid_amount = parseInt(obj.bid_amount)/100
          } else {
            obj.bid_amount = 0
          }
          if (!obj.hasOwnProperty('bid_strategy')) {
            obj.bid_strategy = ''
          }

          obj.spend = 0.0
          obj.link_clicks = 0.0
          obj.conversions = 0
          obj.conversions_value = 0.0
          obj.cpa = 0.0
          obj.cr = 0.0
          obj.cpm = 0.0
          obj.profit = 0.0
          obj.roi = 0.0

          if (obj.hasOwnProperty('insights') && obj.insights !== null &&
              obj.insights.hasOwnProperty('data') && obj.insights.data !== null &&
              obj.insights.data.length > 0) {
            let insights = obj.insights.data[0]

            if (insights.hasOwnProperty('spend')) {
              obj.spend = parseFloat(insights.spend)
            }
            if (insights.hasOwnProperty('actions') && insights.actions !== null) {
              for (let i = 0; i < insights.actions.length; i++) {
                let a = insights.actions[i]
                if (a.hasOwnProperty('action_type') && typeof(a.action_type) === 'string') {
                  if (a.action_type === 'omni_purchase') {
                    if (a.hasOwnProperty('value') && a.value !== null) {
                      obj.conversions += parseInt(a.value)
                    }
                  } else if (a.action_type === 'link_click') {
                    if (a.hasOwnProperty('value') && a.value !== null) {
                      obj.link_clicks += parseInt(a.value)
                    }
                  }
                }
              }
              obj.cpm = parseFloat(insights.cpm)
            }
            if (insights.hasOwnProperty('action_values') && insights.action_values !== null) {
              for (let i = 0; i < insights.action_values.length; i++) {
                let a = insights.action_values[i]
                if (a.hasOwnProperty('action_type') && typeof(a.action_type) === 'string') {
                  if (a.action_type === 'omni_purchase') {
                    if (a.hasOwnProperty('value') && a.value !== null) {
                      obj.conversions_value += parseFloat(a.value)
                    }
                  }
                }
              }
              obj.cpm = parseFloat(insights.cpm)
            }

            if (insights.hasOwnProperty('cpm')) {
              obj.cpm = parseFloat(insights.cpm)
            }

          }

          obj.cpa = (obj.conversions > 0) ? (obj.spend/obj.conversions) : obj.spend
          obj.cr = (obj.link_clicks > 0) ? (100*obj.conversions/obj.link_clicks) : 0
          obj.profit = obj.conversions_value - obj.spend
          obj.roi = (obj.spend > 0) ? (((obj.profit/obj.spend)-1)*100) : 0

          objects[i] = obj
        }
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async fetchCampaigns(parentId, filters = {}) {
    const result = await this.fetchObjects('campaign', parentId, filters)
    return result
  }

  async fetchAdsets(parentId, filters = {}) {
    const result = await this.fetchObjects('adset', parentId, filters)
    return result
  }

  async fetchAds(parentId, filters = {}) {
    const result = await this.fetchObjects('ad', parentId, filters)
    return result
  }

  async updateObject(id, params) {
    let result = false

    try {
      params.access_token = this.accessToken
      const response = await this.$http.post(this.apiUrl + '/' + id, params)
      if (response.hasOwnProperty('data') && response.data !== null &&
          response.data.hasOwnProperty('success') && response.data.success == true) {
        result = true
      }
    } catch (error) {
      // Nothing
    }

    return result
  }

}

export default Facebook
