let axios = require('axios')

class Facebook {
  constructor(apiKey) {
    this.apiKey = apiKey
    this.$http = axios
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

  async fetchAdAccounts(advertiserId, filters = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      let params = {
        advertiser_id: advertiserId,
        reports: 1,
      }
      if (filters.hasOwnProperty('date_from') && filters.date_from != '') {
        params.filter_date_from = filters.date_from
      }
      if (filters.hasOwnProperty('date_to') && filters.date_to != '') {
        params.filter_date_to = filters.date_to
      }
      const response = await this.$http.get('/api/3rd/facebook/adaccounts', { headers, params: params })
      if (response !== null && response.hasOwnProperty('data') &&
          response.data !== null && response.data.hasOwnProperty('data') && response.data.data !== null) {
        objects = response.data.data

        for (let i = 0; i < objects.length; i++) {
          let obj = objects[i]

          obj.spend = 0.0
          obj.conversions = 0
          obj.cpa = 0.0
          obj.cr = 0.0
          obj.cpm = 0.0
          obj.profit = 0.0
          obj.roi = 0.0
          obj.budget = 0.0

          if (obj.hasOwnProperty('insights') && obj.insights !== null) {
            if (obj.insights.hasOwnProperty('spend')) {
              obj.spend = parseFloat(obj.insights.spend)
            }
            // DEBUG TEST TODO: Check if this works
            if (obj.insights.hasOwnProperty('conversions') && obj.insights.conversions !== null &&
                obj.insights.conversions.hasOwnProperty('data') && obj.insights.conversions.data !== null) {
              let conversions = obj.insights.conversions.data
              for (let i = 0; i < conversions.length; i++) {
                let c = conversions[i]
                if (c.hasOwnProperty('action_type') && typeof(c.action_type) === 'string') {
                  if (c.action_type.includes('lead') || c.action_type.includes('purchase')) {
                    obj.conversions++
                  }
                }
              }
              obj.cpm = parseFloat(obj.insights.cpm)
            }
            // TODO: CPA
            // TODO: CR
            if (obj.insights.hasOwnProperty('cpm')) {
              obj.cpm = parseFloat(obj.insights.cpm)
            }
          }

          // TODO: Calculate Profit
          // TODO: Calculate ROI

          objects[i] = obj
        }
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async fetchCampaigns(advertiserId, parentId = null, filters = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      let params = {
        advertiser_id: advertiserId,
        reports: 1,
      }
      if (parentId !== null) {
        params.parent_id = parentId
      }
      if (filters.hasOwnProperty('date_from') && filters.date_from != '') {
        params.filter_date_from = filters.date_from
      }
      if (filters.hasOwnProperty('date_to') && filters.date_to != '') {
        params.filter_date_to = filters.date_to
      }
      const response = await this.$http.get('/api/3rd/facebook/campaigns', { headers, params: params })
      if (response !== null && response.hasOwnProperty('data') &&
          response.data !== null && response.data.hasOwnProperty('data') && response.data.data !== null) {
        objects = response.data.data

        for (let i = 0; i < objects.length; i++) {
          let obj = objects[i]

          obj.spend = 0.0
          obj.conversions = 0
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
            // DEBUG TEST TODO: Check if this works
            if (insights.hasOwnProperty('actions') && insights.actions !== null) {
              let actions = insights.actions
              for (let i = 0; i < actions.length; i++) {
                let c = actions[i]
                if (c.hasOwnProperty('action_type') && typeof(c.action_type) === 'string') {
                  if (c.action_type === 'omni_purchase') {
                    if (c.hasOwnProperty('value') && c.value !== null) {
                      obj.conversions += parseInt(c.value)
                    }
                  }
                }
              }
              obj.cpm = parseFloat(insights.cpm)
            }
            // TODO: CPA
            // TODO: CR
            if (insights.hasOwnProperty('cpm')) {
              obj.cpm = parseFloat(insights.cpm)
            }
          }

          // TODO: Calculate Profit
          // TODO: Calculate ROI

          objects[i] = obj
        }
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async fetchAdsets(advertiserId, parentId = null, filters = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      let params = {
        advertiser_id: advertiserId,
        reports: 1,
      }
      if (parentId !== null) {
        params.parent_id = parentId
      }
      if (filters.hasOwnProperty('date_from') && filters.date_from != '') {
        params.filter_date_from = filters.date_from
      }
      if (filters.hasOwnProperty('date_to') && filters.date_to != '') {
        params.filter_date_to = filters.date_to
      }
      const response = await this.$http.get('/api/3rd/facebook/adsets', { headers, params: params })
      if (response !== null && response.hasOwnProperty('data') &&
          response.data !== null && response.data.hasOwnProperty('data') && response.data.data !== null) {
        objects = response.data.data

        for (let i = 0; i < objects.length; i++) {
          let obj = objects[i]

          obj.spend = 0.0
          obj.conversions = 0
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
            // DEBUG TEST TODO: Check if this works
            if (insights.hasOwnProperty('actions') && insights.actions !== null) {
              let actions = insights.actions
              for (let i = 0; i < actions.length; i++) {
                let c = actions[i]
                if (c.hasOwnProperty('action_type') && typeof(c.action_type) === 'string') {
                  if (c.action_type === 'omni_purchase') {
                    if (c.hasOwnProperty('value') && c.value !== null) {
                      obj.conversions += parseInt(c.value)
                    }
                  }
                }
              }
              obj.cpm = parseFloat(insights.cpm)
            }
            // TODO: CPA
            // TODO: CR
            if (insights.hasOwnProperty('cpm')) {
              obj.cpm = parseFloat(insights.cpm)
            }
          }

          // TODO: Calculate Profit
          // TODO: Calculate ROI

          objects[i] = obj
        }
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async fetchAds(advertiserId, parentId = null, filters = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      let params = {
        advertiser_id: advertiserId,
        reports: 1,
      }
      if (parentId !== null) {
        params.parent_id = parentId
      }
      if (filters.hasOwnProperty('date_from') && filters.date_from != '') {
        params.filter_date_from = filters.date_from
      }
      if (filters.hasOwnProperty('date_to') && filters.date_to != '') {
        params.filter_date_to = filters.date_to
      }
      const response = await this.$http.get('/api/3rd/facebook/ads', { headers, params: params })
      if (response !== null && response.hasOwnProperty('data') &&
          response.data !== null && response.data.hasOwnProperty('data') && response.data.data !== null) {
        objects = response.data.data

        for (let i = 0; i < objects.length; i++) {
          let obj = objects[i]

          obj.spend = 0.0
          obj.conversions = 0
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
            // DEBUG TEST TODO: Check if this works
            if (insights.hasOwnProperty('actions') && insights.actions !== null) {
              let actions = insights.actions
              for (let i = 0; i < actions.length; i++) {
                let c = actions[i]
                if (c.hasOwnProperty('action_type') && typeof(c.action_type) === 'string') {
                  if (c.action_type === 'omni_purchase') {
                    if (c.hasOwnProperty('value') && c.value !== null) {
                      obj.conversions += parseInt(c.value)
                    }
                  }
                }
              }
              obj.cpm = parseFloat(insights.cpm)
            }
            // TODO: CPA
            // TODO: CR
            if (insights.hasOwnProperty('cpm')) {
              obj.cpm = parseFloat(insights.cpm)
            }
          }

          // TODO: Calculate Profit
          // TODO: Calculate ROI

          objects[i] = obj
        }
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }
}

export default Facebook
