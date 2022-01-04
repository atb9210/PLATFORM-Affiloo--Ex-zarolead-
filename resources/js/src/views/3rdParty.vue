<!-- =========================================================================================
    File Name: Table.vue
    Description: Table demo - Imports all table demos
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
  <div class="w-full">

    <vs-row vs-justify="center">

      <vs-col type="flex" class="mb-6" vs-justify="center" vs-align="center" vs-w="3" v-for="(item, index) in apps" :key="index">
        <vs-card class="cardx" fixedHeight>
          <div slot="header">
            <h3 class="text-center">
              {{item.name}}
            </h3>
          </div>
          <div slot="media">
            <img :src="item.image">
          </div>
          <div>
            <span>{{item.description}}</span>
          </div>
          <div slot="footer">
            <vs-row vs-justify="flex-end">
              <!--
              <vs-button type="gradient" color="danger" icon="favorite"></vs-button>
              <vs-button color="primary" icon="turned_in_not"></vs-button>
              -->
              <vs-button color="rgb(130,130,130)" color-text="rgb(50,50,50)" icon="settings" @click="callCallback(item.callback)"></vs-button>
            </vs-row>
          </div>
        </vs-card>
      </vs-col>

    </vs-row>

    <!-- ChatAPI popup -->
    <vs-popup title="Chat API" :active.sync="showPopup['chatapi']">
      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>API Instance</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-eye" icon-no-border v-model="chatapi.api_instance" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>API Token</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-lock" icon-no-border v-model="chatapi.api_token" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <vs-button class="mt-12 mr-auto ml-auto" @click="updateChatAPISettings" color="primary" type="filled">Update</vs-button>
      </div>
    </vs-popup>

    <!-- Stripe popup -->
    <vs-popup title="Stripe" :active.sync="showPopup['stripe']">
      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Test Mode</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-checkbox v-model="stripe.test_mode" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Test API Public Key</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-eye" icon-no-border v-model="stripe.test_api_public_key" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Test API Secret Key</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-lock" icon-no-border v-model="stripe.test_api_secret_key" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Live API Public Key</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-eye" icon-no-border v-model="stripe.live_api_public_key" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Live API Secret Key</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-lock" icon-no-border v-model="stripe.live_api_secret_key" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <vs-button class="mt-12 mr-auto ml-auto" @click="updateStripeSettings" color="primary" type="filled">Update</vs-button>
      </div>
    </vs-popup>

    <!-- Worldfilia popup -->
    <vs-popup title="Worldfilia" :active.sync="showPopup['worldfilia']">
      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>API Key</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-eye" icon-no-border v-model="worldfilia.api_key" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Source ID</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-lock" icon-no-border v-model="worldfilia.source_id" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <vs-button class="mt-12 mr-auto ml-auto" @click="updateWorldfiliaSettings" color="primary" type="filled">Update</vs-button>
      </div>
    </vs-popup>

    <!-- ProfitParadise popup -->
    <vs-popup title="ProfitParadise" :active.sync="showPopup['profit_paradise']">
      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>UID (User ID)</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-eye" icon-no-border v-model="profit_paradise.uid" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <vs-button class="mt-12 mr-auto ml-auto" @click="updateProfitParadiseSettings" color="primary" type="filled">Update</vs-button>
      </div>
    </vs-popup>

    <!-- Limitless popup -->
    <vs-popup title="Limitless" :active.sync="showPopup['limitless']">
      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>API Key</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-eye" icon-no-border v-model="limitless.apikey" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <vs-button class="mt-12 mr-auto ml-auto" @click="updateLimitlessSettings" color="primary" type="filled">Update</vs-button>
      </div>
    </vs-popup>

    <!-- Facebook popup -->
    <vs-popup title="Facebook" :active.sync="showPopup['facebook']">
      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Access token</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-eye" icon-no-border v-model="facebook.access_token" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Pixel</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-eye" icon-no-border v-model="facebook.pixel" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Event</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-select autocomplete v-model="facebook.eventName" class="w-full select-large">
            <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in ['Purchase', 'Lead', 'Complete registration']" class="w-full" />
          </vs-select>
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Event Currency</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-select autocomplete v-model="facebook.eventCurrency" class="w-full select-large">
            <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in ['EUR', 'USD']" class="w-full" />
          </vs-select>
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Test code</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-eye" icon-no-border v-model="facebook.test_code" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <vs-button class="mt-12 mr-auto ml-auto" @click="updateFacebookSettings" color="primary" type="filled">Update</vs-button>
      </div>
    </vs-popup>

    <!-- TikTok popup -->
    <vs-popup title="TikTok" :active.sync="showPopup['tiktok']">
      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Access token</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-eye" icon-no-border v-model="tiktok.token" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Pixel</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-eye" icon-no-border v-model="tiktok.pixel" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>Event</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-select autocomplete v-model="tiktok.eventName" class="w-full select-large">
            <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in ['SubmitForm', 'ClickButton']" class="w-full" />
          </vs-select>
        </div>
      </div>

      <div class="vx-row mb-6">
        <vs-button class="mt-12 mr-auto ml-auto" @click="updateTikTokSettings" color="primary" type="filled">Update</vs-button>
      </div>
    </vs-popup>

  </div>
</template>

<script>
export default {
  data () {
    return {
      settings: {},

      showPopup: {
        'chatapi': false,
        'stripe': false,
        'worldfilia': false,
        'profit_paradise': false,
        'limitless': false,
        'facebook': false,
        'tiktok': false,
      },

      apps: [
        {
          name: 'Chat API',
          image: '/images/3rd_party/chatapi.png',
          description: 'In-App Messaging',
          callback: this.showChatAPISettings,
        },
        {
          name: 'Stripe',
          image: '/images/3rd_party/stripe.png',
          description: 'Payment Gateway',
          callback: this.showStripeSettings,
        },
        {
          name: 'Worldfilia',
          image: '/images/3rd_party/worldfilia.png',
          description: 'Network',
          callback: this.showWorldfiliaSettings,
        },
        {
          name: 'AMSped',
          image: '/images/3rd_party/amsped.png',
          description: 'Network',
          callback: null,
        },
        {
          name: 'AffiliationPark',
          image: '/images/3rd_party/affiliation-park.png',
          description: 'Network',
          callback: null,
        },
        {
          name: 'ProfitParadise',
          image: '/images/3rd_party/profit-paradise.png',
          description: 'Network',
          callback: this.showProfitParadiseSettings,
        },
        {
          name: 'Limitless',
          image: '/images/3rd_party/limitless.png',
          description: 'Network',
          callback: this.showLimitlessSettings,
        },
        {
          name: 'Facebook',
          image: '/images/3rd_party/facebook.png',
          description: 'Advertising Platform',
          callback: this.showFacebookSettings,
        },
        {
          name: 'TikTok',
          image: '/images/3rd_party/tiktok.png',
          description: 'Advertising Platform',
          callback: this.showTikTokSettings,
        },
        {
          name: 'Google Ads',
          image: '/images/3rd_party/google-ads.png',
          description: 'Advertising Platform',
          callback: null,
        },
        {
          name: 'ServerPilot',
          image: '/images/3rd_party/serverpilot.png',
          description: 'Server Management',
          callback: null,
        },
        {
          name: 'Cloudflare',
          image: '/images/3rd_party/cloudflare.png',
          description: 'DNS & Security',
          callback: null,
        },
        {
          name: 'DigitalOcean',
          image: '/images/3rd_party/digitalocean.png',
          description: 'Cloud Service Provider',
          callback: null,
        },
        {
          name: 'Dropbox',
          image: '/images/3rd_party/dropbox.png',
          description: 'File Hosting Service',
          callback: null,
        },
        {
          name: 'Namecheap',
          image: '/images/3rd_party/namecheap.png',
          description: 'Web Hosting & Domain Registration Service',
          callback: null,
        },
        {
          name: 'ZaroCloner',
          image: '/images/3rd_party/zarocloner.png',
          description: 'Webpages Cloner',
          callback: null,
        },
      ],

      chatapi: {
        api_instance: '',
        api_token: '',
      },

      stripe: {
        test_mode: false,
        test_api_public_key: '',
        test_api_secret_key: '',
        live_api_public_key: '',
        live_api_secret_key: '',
      },

      /* Networks */
      worldfilia: {
        api_key: '',
        source_id: '',
      },
      profit_paradise: {
        uid: 'asd',
      },
      limitless: {
        apikey: '',
      },

      facebook: {
        access_token: '',
        pixel: '',
        eventName: '',
        eventCurrency: '',
        test_code: '',
      },
      tiktok: {
        token: '',
        pixel: '',
        eventName: '',
      },
    }
  },
  methods: {
    async fetchSettings() {
      let settings = []
      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        const response = await this.$http.get('/api/v1/settings', { headers })
        if (response !== null && response.hasOwnProperty('data') && response.data !== null &&
            response.data.hasOwnProperty('data') && response.data.data !== null) {
          settings = response.data.data
        }
      } catch (error) {
        // Nothing
      }
      return settings
    },
    async updateSettings() {
      try {
        let settings = Object.assign({}, this.settings)
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        let data = {
          settings: JSON.stringify(settings)
        }
        const response = await this.$http.put('/api/v1/settings', data, { headers })
        if (response !== null && response.hasOwnProperty('data') && response.data !== null &&
            response.data.hasOwnProperty('data') && response.data.data !== null) {
          this.settings = response.data.data
          return true
        }
      } catch (error) {
        this.settings = await this.fetchSettings()
        return false
      }
    },
    async updateSettingsWithLoading() {
      this.$vs.loading()

      let res = await this.updateSettings()
      if (res) {
        this.$vs.notify({
          title: 'Success',
          text: 'Settings updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update settings',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    callCallback(cb) {
      if (cb !== null) {
        cb()
      }
    },
    hasSetting(k) {
      if (this.settings !== null && this.settings.hasOwnProperty(k) && this.settings[k] !== null) {
        return true
      }
      return false
    },
    adjustSettings(obj, defaultSettings) {
      for (const [key, value] of Object.entries(defaultSettings)) {
        if (!obj.hasOwnProperty(key)) {
          obj[key] = value
        }
      }
      return obj
    },
    copySettings(src, dest) {
      for (const [key, value] of Object.entries(src)) {
        dest[key] = value
      }
    },

    /* ChatAPI */
    async showChatAPISettings() {
      let chatapiSettingsDefault = {
        api_instance: '',
        api_token: '',
      }
      if (!this.settings.hasOwnProperty('chatapi')) {
        this.settings['chatapi'] = chatapiSettingsDefault
      }
      this.adjustSettings(this.settings['chatapi'], chatapiSettingsDefault)
      this.copySettings(this.settings['chatapi'], this.chatapi)

      this.showPopup['chatapi'] = true
    },
    async updateChatAPISettings() {
      this.settings['chatapi'] = this.chatapi
      await this.updateSettingsWithLoading()
      this.showPopup['chatapi'] = false
    },

    /* Stripe */
    async showStripeSettings() {
      let stripeSettingsDefault = {
        test_mode: false,
        test_api_secret_key: '',
        test_api_public_key: '',
        live_api_secret_key: '',
        live_api_public_key: '',
        api_secret_key: '',
        api_public_key: '',
      }
      if (!this.settings.hasOwnProperty('stripe')) {
        this.settings['stripe'] = stripeSettingsDefault
      }
      this.adjustSettings(this.settings['stripe'], stripeSettingsDefault)
      this.copySettings(this.settings['stripe'], this.stripe)

      this.showPopup['stripe'] = true
    },
    async updateStripeSettings() {
      this.settings['stripe'] = this.stripe

      this.settings['stripe'].api_secret_key = this.settings['stripe'].test_api_secret_key
      this.settings['stripe'].api_public_key = this.settings['stripe'].test_api_public_key
      if (this.settings['stripe'].test_mode !== true) {
        this.settings['stripe'].api_secret_key = this.settings['stripe'].live_api_secret_key
        this.settings['stripe'].api_public_key = this.settings['stripe'].live_api_public_key
      }

      await this.updateSettingsWithLoading()
      this.showPopup['stripe'] = false
    },

    /* Worldfilia */
    async showWorldfiliaSettings() {
      let worldfiliaSettingsDefault = {
        api_key: '',
        source_id: '',
      }

      if (!this.settings.hasOwnProperty('worldfilia')) {
        this.settings['worldfilia'] = worldfiliaSettingsDefault
      }
      this.adjustSettings(this.settings['worldfilia'], worldfiliaSettingsDefault)
      this.copySettings(this.settings['worldfilia'], this.worldfilia)

      this.showPopup['worldfilia'] = true
    },
    async updateWorldfiliaSettings() {
      this.settings['worldfilia'] = this.worldfilia
      await this.updateSettingsWithLoading()
      this.showPopup['worldfilia'] = false
    },

    /* ProfitParadise */
    async showProfitParadiseSettings() {
      let profitParadiseSettingsDefault = {
        uid: '',
      }

      if (!this.settings.hasOwnProperty('profit_paradise')) {
        this.settings['profit_paradise'] = profitParadiseSettingsDefault
      }
      this.adjustSettings(this.settings['profit_paradise'], profitParadiseSettingsDefault)
      this.copySettings(this.settings['profit_paradise'], this.profit_paradise)

      this.showPopup['profit_paradise'] = true
    },
    async updateProfitParadiseSettings() {
      this.settings['profit_paradise'] = this.profit_paradise
      await this.updateSettingsWithLoading()
      this.showPopup['profit_paradise'] = false
    },

    /* Limitless */
    async showLimitlessSettings() {
      let limitlessSettingsDefault = {
        apikey: '',
      }

      if (!this.settings.hasOwnProperty('limitless')) {
        this.settings['limitless'] = limitlessSettingsDefault
      }
      this.adjustSettings(this.settings['limitless'], limitlessSettingsDefault)
      this.copySettings(this.settings['limitless'], this.limitless)

      this.showPopup['limitless'] = true
    },
    async updateLimitlessSettings() {
      this.settings['limitless'] = this.limitless
      await this.updateSettingsWithLoading()
      this.showPopup['limitless'] = false
    },

    /* Facebook */
    async showFacebookSettings() {
      let facebookSettingsDefault = {
        access_token: '',
        pixel: '',
        eventName: '',
        eventCurrency: '',
        test_code: '',
      }

      if (!this.settings.hasOwnProperty('facebook')) {
        this.settings['facebook'] = facebookSettingsDefault
      }
      this.adjustSettings(this.settings['facebook'], facebookSettingsDefault)
      this.copySettings(this.settings['facebook'], this.facebook)

      this.showPopup['facebook'] = true
    },
    async updateFacebookSettings() {
      this.settings['facebook'] = this.facebook
      await this.updateSettingsWithLoading()
      this.showPopup['facebook'] = false
    },

    /* TikTok */
    async showTikTokSettings() {
      let tiktokSettingsDefault = {
        access_token: '',
        pixel: '',
        eventName: '',
        eventCurrency: '',
        test_code: '',
      }

      if (!this.settings.hasOwnProperty('tiktok')) {
        this.settings['tiktok'] = tiktokSettingsDefault
      }
      this.adjustSettings(this.settings['tiktok'], tiktokSettingsDefault)
      this.copySettings(this.settings['tiktok'], this.tiktok)

      this.showPopup['tiktok'] = true
    },
    async updateTikTokSettings() {
      this.settings['tiktok'] = this.tiktok
      await this.updateSettingsWithLoading()
      this.showPopup['tiktok'] = false
    },

  },
  async created() {
    this.settings = await this.fetchSettings()
  }
}
</script>

<style>
.cardx {
    width: calc(100% - 10px);
    margin: 5px;
}
</style>
