let axios = require('axios')

function cloneObject(obj) {
  if (obj == null || "object" != typeof(obj)) {
    return obj
  }

  let copy = obj.constructor()
  for (let attr in obj) {
    if (obj.hasOwnProperty(attr)) {
      copy[attr] = obj[attr]
    }
  }
  return copy
}

function copyObject(sourceObj, destObj) {
  for (let attr in sourceObj) {
    destObj[attr] = sourceObj[attr]
  }
}

function mergeObject(sourceObj, destObj) {
  for (let attr in sourceObj) {
    if (!destObj.hasOwnProperty(attr)) {
      destObj[attr] = sourceObj[attr]
    }
  }
}

class Facebook {
  monitorDefaultStrategy = {
    'bid.stop': {
      enabled: false,
      spent: 0.0,
      lifetime_spent: 0.0,
      conversions: 0,
      times_per_day: 1,
      time_check: 'every_thirty_minutes',
    },
    'bid.down': {
      enabled: false,
      variation: 0.0,
      min_bid: 0.0,
      spent: 0.0,
      lifetime_spent: 0.0,
      conversions: 0,
      times_per_day: 1,
      time_check: 'every_thirty_minutes',
    },
  }

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

  async isAccessTokenValid() {
    try {
      let params = {
        access_token: this.accessToken,
      }
      const response = await this.$http.get(this.apiUrl + '/me', { params })
      if (response !== null && response.data !== null) {
        return true
      }
    } catch (error) {
      return false
    }
    return false
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

  async fetchAdvertiser(id) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/facebook/advertisers/' + id, { headers })
      if (response !== null && response.hasOwnProperty('data') &&
          response.data !== null && response.data.hasOwnProperty('data') && response.data.data !== null) {
        object = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return object
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

  async getRequestAllPages(path, params = {}) {
    let objects = []

    params.access_token = this.accessToken

    let apiCallUrl = this.apiUrl + path
    let endLoop = false
    do {
      try {
        const response = await this.$http.get(apiCallUrl, { params })
        if (response !== null && response.hasOwnProperty('data') && response.data !== null) {
          if (response.data.hasOwnProperty('data') && response.data.data !== null) {
            let tmpObjects = response.data.data
            for (let i = 0; i < tmpObjects.length; i++) {
              objects.push(tmpObjects[i])
            }
          }

          if (response.data.hasOwnProperty('paging') && response.data.paging !== null &&
              response.data.paging.hasOwnProperty('next') && response.data.paging.next !== null && response.data.paging.next !== '') {
            apiCallUrl = response.data.paging.next
            params = {}
          } else {
            endLoop = true
          }
        }
      } catch (error) {
        endLoop = true
      }
    } while (!endLoop)

    return objects
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

      if (!filters.hasOwnProperty('date_from') && !filters.hasOwnProperty('date_to')) {
        insightsParam = ''
      }

      let fields = 'id,name,account_status,adspixels{id,name}'
      if (insightsParam != '') {
        fields += ',' + insightsParam
      }

      let params = {
        access_token: this.accessToken,
        fields: fields,
      }
      
      objects = await this.getRequestAllPages('/me/adaccounts', params)
      for (let i = 0; i < objects.length; i++) {
        let obj = objects[i]

        obj.status = 2
        if (obj.hasOwnProperty('account_status') && obj.account_status !== null) {
          obj.status = obj.account_status
        }

        if (obj.hasOwnProperty('adspixels') && obj.adspixels !== null &&
            obj.adspixels.hasOwnProperty('data') && obj.adspixels.data !== null) {
          obj.adspixels = obj.adspixels.data
        } else {
          obj.adspixels = []
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
        obj.roi = (obj.spend > 0) ? (((obj.profit/obj.spend))*100) : 0

        objects[i] = obj
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async fetchObjects(type, parentId = null, filters = {}) {
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

      if (!filters.hasOwnProperty('date_from') && !filters.hasOwnProperty('date_to')) {
        insightsParam = ''
      }

      let fields = 'id,name,effective_status,daily_budget,bid_amount,bid_strategy'
      if (insightsParam != '') {
        fields += ',' + insightsParam
      }

      let params = {
        access_token: this.accessToken,
        limit: 2000,
        fields: fields,
      }

      let apiCallUrl = '/'
      if (parentId !== null && parentId != '') {
        apiCallUrl += parentId + '/'
      }
      apiCallUrl += type + 's'

      let tmpObjects = await this.getRequestAllPages(apiCallUrl, params)
      for (let i = 0; i < tmpObjects.length; i++) {
        let obj = tmpObjects[i]

        obj.type = type

        obj.status = 'ARCHIVED'
        if (obj.hasOwnProperty('effective_status') && obj.effective_status !== null) {
          obj.status = obj.effective_status
        }

        // Filter by status
        if (filters.hasOwnProperty('status') && filters.status !== null && filters.status !== '') {
          if (obj.status != filters.status) {
            // TODO: to delete
            console.log(obj.status, '!==', filters.status)

            continue
          }
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
        obj.roi = (obj.spend > 0) ? (((obj.profit/obj.spend))*100) : 0

        objects.push(obj)
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

  async createObject(type, parentId, params) {
    let object = null

    try {
      params.access_token = this.accessToken

      let apiCallUrl = this.apiUrl + '/'
      if (parentId != '') {
        apiCallUrl += parentId + '/'
      }
      apiCallUrl += type + 's'
      const response = await this.$http.post(apiCallUrl, params)
      if (response.hasOwnProperty('data') && response.data !== null &&
          response.data.hasOwnProperty('id') && response.data.id !== null) {
        object = {
          id: response.data.id,
        }
      }
    } catch (error) {
      // Nothing
    }

    return object
  }

  async createCampaign(adAccountId, params) {
    const result = await this.createObject('campaign', adAccountId, params)
    return result
  }

  async createAdset(adAccountId, params) {
    const result = await this.createObject('adset', adAccountId, params)
    return result
  }

  async createAd(adAccountId, params) {
    const result = await this.createObject('ad', adAccountId, params)
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

  async fetchPages() {
    let objects = await this.getRequestAllPages('/me/accounts')
    return objects
  }

  async fetchPageBackedInstagramAccounts(pageId) {
    let objects = await this.getRequestAllPages('/' + pageId + '/page_backed_instagram_accounts', { fields: 'id,username'})
    return objects
  }

  async uploadAdImage(adAccountId, params) {
    let object = null

    try {
      const response = await this.$http.post(this.apiUrl + '/' + adAccountId + '/adimages', params)
      if (response.hasOwnProperty('data') && response.data !== null &&
          response.data.hasOwnProperty('images') && response.data.images !== null) {
        for (const [key, value] of Object.entries(response.data.images)) {
          object = value
        }
      }
    } catch (error) {
      // Nothing
    }

    return object
  }

  async uploadAdVideo(adAccountId, params) {
    let object = null

    try {
      const response = await this.$http.post(this.apiUrl + '/' + adAccountId + '/advideos', params)
      if (response.hasOwnProperty('data') && response.data !== null &&
          response.data.hasOwnProperty('id') && response.data.id !== null) {
        object = response.data
      }
    } catch (error) {
      // Nothing
    }

    return object
  }

  getMonitorDefaultStrategy() {
    return cloneObject(this.monitorDefaultStrategy)
  }

  async fetchMonitors(filters = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      let params = filters
      const response = await this.$http.get('/api/3rd/facebook/monitors', { headers, params })
      objects = response.data.data

      for (let i = 0; i < objects.length; i++) {
        let obj = objects[i]

        let campaigns = []
        if (obj.hasOwnProperty('campaigns') && obj.campaigns !== null) {
          for (const [key, value] of Object.entries(obj.campaigns)) {
            campaigns = value
          }
        }
        if (campaigns === null) {
          campaigns = []
        }
        obj.numOfCampaigns = campaigns.length

        obj.views = 0
        obj.spent = 0.0
        obj.conversions = 0
        obj.conversions_value = 0.0
        if (obj.hasOwnProperty('reports') && obj.reports !== null) {
          if (obj.reports.hasOwnProperty('views') && obj.reports.views !== null) {
            obj.views = parseInt(obj.reports.views)
          }
          if (obj.reports.hasOwnProperty('spent') && obj.reports.spent !== null) {
            obj.spent = parseFloat(obj.reports.spent)
          }
          if (obj.reports.hasOwnProperty('conversions') && obj.reports.conversions !== null) {
            obj.conversions = parseInt(obj.reports.conversions)
          }
          if (obj.reports.hasOwnProperty('conversions_value') && obj.reports.conversions_value !== null) {
            obj.conversions_value = parseFloat(obj.reports.conversions_value)
          }
        }
        obj.profit = obj.conversions_value - obj.spent
        obj.roi = (obj.spent > 0) ? (((obj.profit/obj.spent))*100) : 0

        obj.averageSpent = ((obj.numOfCampaigns > 0) ? (obj.spent/obj.numOfCampaigns) : 0.0)
        obj.cpc = ((obj.views > 0) ? (obj.spent/obj.views) : 0.0)
        obj.epc = ((obj.views > 0) ? (obj.conversions_value/obj.views) : 0.0)

        if (!obj.hasOwnProperty('strategy') || obj.strategy === null) {
          obj.strategy = cloneObject(this.monitorDefaultStrategy)
        } else {
          mergeObject(this.monitorDefaultStrategy, obj.strategy)
        }

        objects[i] = obj
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

  async fetchMonitor(id) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.get('/api/3rd/facebook/monitors/' + id, { headers })
      if (response !== null && response.hasOwnProperty('data') &&
          response.data !== null && response.data.hasOwnProperty('data') && response.data.data !== null) {
        object = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return object
  }

  async createMonitor(params) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.post('/api/3rd/facebook/monitors', params, { headers })
      object = response.data.data
    } catch (error) {
      // Nothing
    }

    return object
  }

  async updateMonitor(id, params) {
    let object = null

    try {
      let headers = this.getHeaders()
      const response = await this.$http.put('/api/3rd/facebook/monitors/' + id, params, { headers })
      if (response !== null && response.hasOwnProperty('data') &&
          response.data !== null && response.data.hasOwnProperty('data') && response.data.data !== null) {
        object = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    return object
  }

  async deleteMonitor(id) {
    let result = false

    try {
      let headers = this.getHeaders()
      const response = await this.$http.delete('/api/3rd/facebook/monitors/' + id, { headers })
      result = true
    } catch (error) {
      // Nothing
    }

    return result
  }

  async addCampaignsToMonitor(id, campaigns = []) {
    let result = false

    try {
      let monitor = await this.fetchMonitor(id)

      let adAccountId = null
      for (const [key, value] of Object.entries(monitor.campaigns)) {
        adAccountId = key
      }
      if (adAccountId === null) {
        return false
      }
      adAccountId = adAccountId.toString()


      let oldCampaigns = monitor.campaigns[adAccountId]
      let newCampaigns = oldCampaigns.concat(campaigns)
      newCampaigns = new Set(newCampaigns)
      newCampaigns = [...newCampaigns]

      let paramCampaigns = {}
      paramCampaigns[adAccountId] = newCampaigns
      result = await this.updateMonitor(monitor.id, {
        campaigns: JSON.stringify(paramCampaigns)
      })
    } catch (error) {
      // Nothing
    }

    return result
  }

  async fetchMonitorActions(monitorId, filters = {}) {
    let objects = []

    try {
      let headers = this.getHeaders()
      let params = filters
      const response = await this.$http.get('/api/3rd/facebook/monitors/' + monitorId + '/actions', { headers, params })
      objects = response.data.data

      for (let i = 0; i < objects.length; i++) {
        let obj = objects[i]

        obj.spent = 0.0
        obj.conversions = 0
        obj.conversions_value = 0.0
        if (obj.hasOwnProperty('spent') && obj.spent !== null) {
          obj.spent = parseFloat(obj.spent)
        } else {
          obj.spent = 0.0
        }
        if (obj.hasOwnProperty('conversions') && obj.conversions !== null) {
          obj.conversions = parseInt(obj.conversions)
        } else {
          obj.conversions = 0.0
        }
        if (obj.hasOwnProperty('conversions_value') && obj.conversions_value !== null) {
          obj.conversions_value = parseFloat(obj.conversions_value)
        } else {
          obj.conversions_value = 0.0
        }
        obj.profit = obj.conversions_value - obj.spent

        if (obj.hasOwnProperty('end_roi') && obj.end_roi !== null) {
          obj.end_roi = parseFloat(obj.end_roi)
        } else {
          obj.end_roi = 0
        }
        if (obj.hasOwnProperty('end_roi_campaign') && obj.end_roi_campaign !== null) {
          obj.end_roi_campaign = parseFloat(obj.end_roi_campaign)
        } else {
          obj.end_roi_campaign = 0.0
        }

        objects[i] = obj
      }
    } catch (error) {
      // Nothing
    }

    return objects
  }

}

export default Facebook
