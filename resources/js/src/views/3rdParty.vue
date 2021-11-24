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

    <!-- Stripe popup -->
    <vs-popup title="Stripe" :active.sync="showPopup['stripe']">
      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>API Public Key</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-eye" icon-no-border v-model="settings.stripe.api_public_key" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <div class="vx-col sm:w-1/3 w-full">
          <span>API Secret Key</span>
        </div>
        <div class="vx-col sm:w-2/3 w-full">
          <vs-input class="w-full" icon-pack="feather" icon="icon-lock" icon-no-border v-model="settings.stripe.api_secret_key" />
        </div>
      </div>

      <div class="vx-row mb-6">
        <vs-button class="mt-12 mr-auto ml-auto" @click="updateStripeSettings" color="primary" type="filled">Update</vs-button>
      </div>
    </vs-popup>

  </div>
</template>

<script>
export default{
  data() {
    return {
      settings: {},

      showPopup: {
        'stripe': false,
      },

      apps: [
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
          callback: null,
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
          name: 'Facebook Business',
          image: '/images/3rd_party/facebook-business.png',
          description: 'Advertising Platform',
          callback: null,
        },
        {
          name: 'TikTok',
          image: '/images/3rd_party/tiktok.png',
          description: 'Advertising Platform',
          callback: null,
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

    /* Stripe */
    async showStripeSettings() {
      if (!this.settings.hasOwnProperty('stripe')) {
        this.settings['stripe'] = {
          api_secret_key: '',
          api_public_key: '',
        }
      }
      this.showPopup['stripe'] = true
    },
    async updateStripeSettings() {
      await this.updateSettingsWithLoading()
      this.showPopup['stripe'] = false
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
