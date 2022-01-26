<!-- =========================================================================================
  File Name: FormWizardValidation.vue
  Description: Form wizard with validation
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
  <vx-card title="" code-toggler>
    <div class="mt-5">
      <form-wizard color="rgba(var(--vs-primary), 1)" errorColor="rgba(var(--vs-danger), 1)" :title="null" :subtitle="null" finishButtonText="Create Ad">
        <!-- tab 1 content -->
        <tab-content title="Campaign" class="mb-5" icon="feather icon-facebook" :before-change="validateStep1">
          <form data-vv-scope="step-1">
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mt-5">
              <my-select v-model="product" :options="products" class="w-full select-large" label="Product" :autocomplete="true" />
              <span class="text-danger">{{ errors.first('step-1.product') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <my-select v-model="page" :options="pages" class="w-full select-large" label="Facebook Page" :autocomplete="true" />
              <span class="text-danger">{{ errors.first('step-1.page') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <my-select v-model="adAccount" :options="adAccounts" class="w-full select-large" label="Ad Account" :autocomplete="true" @change="onChangeAdAccount" />
              <span class="text-danger">{{ errors.first('step-1.ad_account') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <my-select v-model="campaign.objective" :options="campaignObjectiveOptions" class="w-full select-large" label="Campaign objective" :autocomplete="true" />
              <span class="text-danger">{{ errors.first('step-1.campaign_objective') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <my-select v-model="campaign.conversionEvent" :options="campaignConversionEventOptions" class="w-full select-large" label="Conversion Event" :autocomplete="true" />
              <span class="text-danger">{{ errors.first('step-1.campaign_conversionEvent') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <my-select v-model="campaign.pixel" :options="pixelOptions" class="w-full select-large" label="Pixel" :autocomplete="true" />
              <span class="text-danger">{{ errors.first('step-1.campaign_pixel') }}</span>
            </div>
            <div class="vx-col md:w-full w-full mt-5">
              <vs-input v-model="campaign.name" class="w-full" label="Campaign Name" />
              <span class="text-danger">{{ errors.first('step-1.campaign_name') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <my-select v-model="campaign.cbo" :options="campaignBudgetOptimizationOptions" class="w-full select-large" label="Budget strategy" :autocomplete="true" />
              <span class="text-danger">{{ errors.first('step-1.campaign_cbo') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-input type="number" min="0.01" step="0.01" v-model="campaign.daily_budget" class="w-full" label="Daily budget" />
              <span class="text-danger">{{ errors.first('step-1.campaign_daily_budget') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <my-select v-model="campaign.bid_strategy" :options="bidStrategies" class="w-full select-large" label="Bid strategy" :autocomplete="true" />
              <span class="text-danger">{{ errors.first('step-1.campaign_bid_strategy') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-input v-show="campaign.bid_strategy !== 'LOWEST_COST_WITHOUT_CAP'" type="number" min="0.01" step="0.01" v-model="campaign.bid_amount" class="w-full" label="Bid Amount" />
              <span class="text-danger">{{ errors.first('step-1.campaign_bid_amount') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <vs-input type="number" min="1" step="1" v-model="campaign.count" class="w-full" label="How many campaigns?" />
              <span class="text-danger">{{ errors.first('step-1.campaign_count') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <vs-input type="number" min="1" step="1" v-model="adset.count" class="w-full" label="How many adsets per campaign?" />
              <span class="text-danger">{{ errors.first('step-1.adset_count') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <vs-input type="number" min="1" step="1" v-model="ad.count" class="w-full" label="How many ads per adset?" />
              <span class="text-danger">{{ errors.first('step-1.ad_count') }}</span>
            </div>
          </div>
          </form>
        </tab-content>

        <!-- tab 2 content -->
        <tab-content title="Target" class="mb-5" icon="feather icon-users" :before-change="validateStep2">
          <form data-vv-scope="step-2">
          <div class="vx-row">
            <div class="vx-col md:w-1/3 w-full mt-5">
              <my-select v-model="target.genders" :options="genderOptions" class="w-full select-large" label="Gender" :autocomplete="true" :multiple="true" />
              <span class="text-danger">{{ errors.first('step-2.target_genders') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <vs-input type="number" min="1" step="1" v-model="target.ageMin" class="w-full" label="Age minimum" />
              <span class="text-danger">{{ errors.first('step-2.target_ageMin') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <vs-input type="number" min="1" step="1" v-model="target.ageMax" class="w-full" label="Age maximum (empty = no age max)" />
              <span class="text-danger">{{ errors.first('step-2.target_ageMax') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <my-select v-model="target.countries" :options="countryOptions" class="w-full select-large" label="Country" :autocomplete="true" :multiple="true" />
              <span class="text-danger">{{ errors.first('step-2.target_countries') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <my-select v-model="target.devices" :options="deviceOptions" class="w-full select-large" label="Device" :autocomplete="true" :multiple="true" />
              <vs-checkbox v-model="target.wifi">Only when connected to WiFi</vs-checkbox>
              <span class="text-danger">{{ errors.first('step-2.target_devices') }}</span>
            </div>
            <div class="vx-col md:w-full w-full mt-5">
              <my-select v-model="target.platforms" :options="platformOptions" class="w-full select-large" label="Platforms" :autocomplete="true" :multiple="true" />
              <span class="text-danger">{{ errors.first('step-2.target_platforms') }}</span>
            </div>
          </div>
          </form>
        </tab-content>

        <!-- tab 3 content -->
        <tab-content title="Ad creativity" class="mb-5" icon="feather icon-image" :before-change="validateStep3">
          <form data-vv-scope="step-2">

          <div class="vx-row" v-if="view == 'normal'">
            <div class="vx-col md:w-full w-full mt-5">
              <vs-input type="text" v-model="ad.title" class="w-full" label="Title" />
              <span class="text-danger">{{ errors.first('step-3.ad_title') }}</span>
            </div>
            <div class="vx-col md:w-full w-full mt-5">
              <vs-textarea v-model="ad.text" class="w-full" label="Text"></vs-textarea>
              <span class="text-danger">{{ errors.first('step-3.ad_text') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-input type="url" v-model="ad.link" class="w-full" label="URL Link" />
              <span>Final URL: {{finalAdLink}}</span>
              <span class="text-danger">{{ errors.first('step-3.ad_link') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-input type="text" v-model="ad.conversionDomain" class="w-full" label="Conversion Domain" />
              <span class="text-danger">{{ errors.first('step-3.ad_conversionDomain') }}</span>
            </div>

            <div class="vx-col w-full mt-5">
              <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-image" class="w-full" @click="view = 'medias'">Choose medias</vs-button>
              <div class="text-center">
                <span class="ml-auto mr-auto">{{ (ad.medias.length == 0) ? 'No media selected' : ad.medias.length + ' media(s) selected' }}</span>
              </div>
              <span class="text-danger">{{ errors.first('step-3.ad_medias') }}</span>
            </div>

            <div class="vx-col w-full mt-5" v-if="hasVideo()">
              <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-image" class="w-full" @click="view = 'preview'">Choose preview for video(s)</vs-button>
              <div class="text-center">
                <span class="ml-auto mr-auto">{{ (ad.preview === null) ? 'No preview selected' : 'Preview selected' }}</span>
              </div>
              <span class="text-danger">{{ errors.first('step-3.ad_medias_preview') }}</span>
            </div>
          </div>

          <div class="vx-row" v-else-if="view == 'medias'">
            <div class="vx-col w-full flex items-center mb-8">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="(ad.medias.length > 0) ? 'icon-check' : 'icon-arrow-left'" @click="view = 'normal'"></vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="fetchProductMedias(product.id)"></vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="openProductMediaPage"></vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-search" @click="view = 'medias_search'"></vs-button>
            </div>
            <div class="vx-col sm:w-1/3 md:w-1/5 w-full" :class="mediaIsSelected(item) ? 'selected-media' : ''" v-for="(item, index) in mediasOptions" @click="selectMedia(item)" @dblclick="openProductMedia(item)">
              <img class="w-full" :src="(item.type == 'image') ? item.url : videoThumbnail">
              <center>{{item.type}} ({{item.pathname}})</center>
            </div>
          </div>

          <div class="vx-row" v-else-if="view == 'medias_search'">
            <div class="vx-col w-full flex items-center mb-8">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="(ad.medias.length > 0) ? 'icon-check' : 'icon-arrow-left'" @click="view = 'normal'"></vs-button>
              <vs-input type="text" v-model="mediaSearchQuery" @keydown.enter="searchImage(mediaSearchQuery)" />
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-search" @click="searchImage(mediaSearchQuery)"></vs-button>
            </div>
            <div class="vx-col sm:w-1/3 md:w-1/5 w-full" :class="mediaIsSelected(item) ? 'selected-media' : ''" v-for="(item, index) in mediasOptions" @click="selectMedia(item)" @dblclick="openNewPage(item.url)">
              <img class="w-full" :src="(item.type == 'image') ? item.url : videoThumbnail">
            </div>
          </div>

          <div class="vx-row" v-else-if="view == 'preview'">
            <div class="vx-col w-full flex items-center mb-8">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="(ad.preview !== null) ? 'icon-check' : 'icon-arrow-left'" @click="view = 'normal'"></vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="fetchProductMedias(product.id)"></vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="openProductMediaPage"></vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-search" @click="view = 'preview_search'"></vs-button>
            </div>
            <div v-if="item.type === 'image'" class="vx-col sm:w-1/3 md:w-1/5 w-full" :class="previewIsSelected(item) ? 'selected-media' : ''" v-for="(item, index) in mediasOptions" @click="selectPreview(item)" @dblclick="openProductMedia(item)">
              <img class="w-full" :src="(item.type == 'image') ? item.thumbnailUrl : videoThumbnail">
              <center>{{item.type}} ({{item.pathname}})</center>
            </div>
          </div>

          <div class="vx-row" v-else-if="view == 'preview_search'">
            <div class="vx-col w-full flex items-center mb-8">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="(ad.preview !== null) ? 'icon-check' : 'icon-arrow-left'" @click="view = 'normal'"></vs-button>
              <vs-input type="text" v-model="mediaSearchQuery" @keydown.enter="searchImage(mediaSearchQuery)" />
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-search" @click="searchImage(mediaSearchQuery)"></vs-button>
            </div>
            <div class="vx-col sm:w-1/3 md:w-1/5 w-full" :class="previewIsSelected(item) ? 'selected-media' : ''" v-for="(item, index) in mediasOptions" @click="selectPreview(item)" @dblclick="openNewPage(item.url)">
              <img class="w-full" :src="(item.type == 'image') ? item.url : videoThumbnail">
            </div>
          </div>

          </form>
        </tab-content>


      </form-wizard>
    </div>

  </vx-card>
</template>

<script>
import { FormWizard, TabContent } from 'vue-form-wizard'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'

import Facebook from 'facebook'

// TODO: Move this function into a utilities package?
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

function isValidHttpUrl(s) {
  let url = null
  try {
    url = new URL(s)
  } catch (_) {
    return false
  }
  return url.protocol === "http:" || url.protocol === "https:"
}

function createImageFromUrl(url) {
  return new Promise((resolve, reject) => {
    let img = new Image();
    img.crossOrigin = 'Anonymous';
    img.onload = () => resolve(img)
    img.onerror = reject
    img.src = url;
  })
}

// For custom error message
import { Validator } from 'vee-validate'
const dict = {
  custom: {
    traffic_source: {
      required: 'Traffic Source is required',
    },
    product: {
      required: 'Product is required',
    },
    type: {
      required: 'Type is required',
    },
    file: {
      required: 'File is required',
    },
  }
}

// register custom messages
Validator.localize('en', dict)

// custom rules
// ...

export default {
  data () {
    return {
      debug: false,

      facebook: null,
      advertiserId: '',
      advertiser: {},

      view: 'normal',

      products: [],
      pages: [],
      adAccounts: [],

      product: {},
      page: {},
      adAccount: {},

      campaign: {
        count: 1,
        name: '',
        objective: 'CONVERSIONS',
        conversionEvent: 'PURCHASE',
        cbo: true,
        daily_budget: 10,
        bid_strategy: 'LOWEST_COST_WITH_BID_CAP',
        bid_amount: 0.01,
        pixel: '',
      },
      adset: {
        count: 1,
      },
      ad: {
        count: 1,
        title: 'Title Example',
        text: 'Text Example',
        link: 'http://domain.com',
        conversionDomain: 'domain.com',
        medias: [],
        preview: null,
      },

      target: {
        genders: [1, 2],
        ageMin: 18,
        ageMax: 65,
        countries: ['IT'],
        devices: ['mobile','desktop'],
        wifi: true,
        platforms: ['facebook', 'instagram', 'audience_network', 'messenger'],
      },

      campaigns: [],
      adsets: [],
      ads: [],

      campaignObjectiveOptions: [
        'APP_INSTALLS',
        'BRAND_AWARENESS',
        'CONVERSIONS',
        'EVENT_RESPONSES',
        'LEAD_GENERATION',
        'LINK_CLICKS',
        'LOCAL_AWARENESS',
        'MESSAGES',
        'OFFER_CLAIMS',
        'PAGE_LIKES',
        'POST_ENGAGEMENT',
        'PRODUCT_CATALOG_SALES',
        'REACH',
        'STORE_VISITS',
        'VIDEO_VIEWS',
      ],
      campaignConversionEventOptions: [
        'SUBSCRIBE',
        'ADD_TO_CART',
        'PURCHASE',
        'LEAD',
        'COMPLETE_REGISTRATION',
      ],
      pixelOptions: [],
      campaignBudgetOptimizationOptions: [
        { value: true,  label: 'CBO' },
        { value: false, label: 'Adset' },
      ],
      bidStrategies: [
        'LOWEST_COST_WITHOUT_CAP',
        'LOWEST_COST_WITH_BID_CAP',
        'COST_CAP',
      ],
      genderOptions: [
        { value: 1, label: 'male' },
        { value: 2, label: 'female' },
      ],
      countryOptions: [
        'IT', 'US', 'FR', 'DE',
      ],
      deviceOptions: [
        'mobile',
        'desktop',
      ],
      platformOptions: [
        'facebook', 'instagram', 'audience_network', 'messenger',
      ],

      productMedias: [],
      videoThumbnail: 'data:image/png;base64,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',

      creatingCampaigns: false,
      creatingAdsets: false,
      creatingAds: false,

      trackingSpecs: null,

      mediaSearchQuery: '',
      searchImageResults: [],
    }
  },
  computed: {
    finalAdLink() {
      let finalLink = this.ad.link
      if (finalLink.includes('?')) {
        finalLink += '&'
      } else {
        finalLink += '?'
      }
      if (typeof(this.campaign.pixel) === 'object' && this.campaign.pixel.hasOwnProperty('id')) {
        finalLink += 'pi=' + this.campaign.pixel.id
      }
      return finalLink
    },
    mediasOptions() {
      if (this.view === 'medias_search') {
        return this.searchImageResults
      }
      return this.productMedias
    },
  },
  methods: {
    getObjectIndexById(id, arr) {
      let index = -1;
      arr.forEach((obj, i) => {
        if (obj.id == id) {
          index = i
          return
        }
      })
      return index
    },
    hasOption(val, arr) {
      for (let i = 0; i < arr.length; i++) {
        if (val === arr[i].value) {
          return true
        }
      }
      return false
    },
    validateStep1() {
      return new Promise((resolve, reject) => {
        this.$validator.validateAll('step-1').then(result => {
          let hasError = false

          if (!(this.product.hasOwnProperty('id') && this.product.hasOwnProperty('name')))  {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'product',
              msg: 'Product is required (if it doesn\'t exist just create one)'
            })
          }

          if (!(this.page.hasOwnProperty('id') && this.page.hasOwnProperty('name')))  {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'page',
              msg: 'Page is required (if it doesn\'t exist just create one)'
            })
          }

          if (!(this.adAccount.hasOwnProperty('id') && this.adAccount.hasOwnProperty('name')))  {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'ad_account',
              msg: 'Ad account is required (if it doesn\'t exist just create one)'
            })
          }

          if (this.campaign.objective == '') {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'campaign_objective',
              msg: 'Campaign objective is required'
            })
          }

          if (!this.campaignConversionEventOptions.includes(this.campaign.conversionEvent)) {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'campaign_conversionEvent',
              msg: 'Invalid conversion event'
            })
          }

          if (!this.pixelOptions.includes(this.campaign.pixel)) {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'campaign_pixel',
              msg: 'Invalid pixel'
            })
          }

          if (this.campaign.name.length == 0) {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'campaign_name',
              msg: 'Campaign name is required'
            })
          }

          if (typeof(this.campaign.cbo) !== 'boolean') {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'campaign_cbo',
              msg: 'Invalid campaign budget strategy'
            })
          }

          if (parseFloat(this.campaign.daily_budget) <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'campaign_daily_budget',
              msg: 'It must be a positive value'
            })
          }

          if (!this.bidStrategies.includes(this.campaign.bid_strategy)) {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'campaign_bid_strategy',
              msg: 'Invalid bid strategy'
            })
          }

          if (parseFloat(this.campaign.bid_amount) <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'campaign_bid_amount',
              msg: 'It must be a positive value'
            })
          }

          if (parseInt(this.campaign.count) <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'campaign_count',
              msg: 'It must be a positive number'
            })
          }

          if (parseInt(this.adset.count) <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'adset_count',
              msg: 'It must be a positive number'
            })
          }

          if (parseInt(this.ad.count) <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'ad_count',
              msg: 'It must be a positive number'
            })
          }

          if (hasError) {
            reject('correct all values')
          } else {
            this.fetchProductMedias(this.product.id)
            // TODO: to delete the if condition
            if (!this.debug) {
              this.createCampaigns()
            }
            resolve(true)
          }
        })
      })
    },
    validateStep2() {
      return new Promise((resolve, reject) => {
        this.$validator.validateAll('step-2').then(result => {
          let hasError = false

          if (this.target.genders.length <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-2',
              field: 'target_genders',
              msg: 'You must select at least one gender'
            })
          }
          for (let i = 0; i < this.target.genders.length; i++) {
            let gender = this.target.genders[i]
            if (!this.hasOption(gender, this.genderOptions)) {
              hasError = true
              this.errors.add({
                scope: 'step-2',
                field: 'target_genders',
                msg: 'Invalid gender'
              })
              break
            }
          }

          if (parseInt(this.target.ageMin) < 0) {
            hasError = true
            this.errors.add({
              scope: 'step-2',
              field: 'target_ageMin',
              msg: 'It must be greater or equal to zero'
            })
          }

          if (parseInt(this.target.ageMax) < 0) {
            hasError = true
            this.errors.add({
              scope: 'step-2',
              field: 'target_ageMax',
              msg: 'It must be greater or equal to zero'
            })
          }

          if (this.target.countries.length <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-2',
              field: 'target_countries',
              msg: 'You must select at least one country'
            })
          }
          for (let i = 0; i < this.target.countries.length; i++) {
            let country = this.target.countries[i]
            if (!this.countryOptions.includes(country)) {
              hasError = true
              this.errors.add({
                scope: 'step-2',
                field: 'target_countries',
                msg: 'Invalid country ' + country
              })
              break
            }
          }

          if (this.target.devices.length <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-2',
              field: 'target_devices',
              msg: 'You must select at least one device'
            })
          }
          for (let i = 0; i < this.target.devices.length; i++) {
            let device = this.target.devices[i]
            if (!this.deviceOptions.includes(device)) {
              hasError = true
              this.errors.add({
                scope: 'step-2',
                field: 'target_devices',
                msg: 'Invalid device ' + device
              })
              break
            }
          }

          if (this.target.platforms.length <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-2',
              field: 'target_platforms',
              msg: 'You must select at least one platform'
            })
          }
          for (let i = 0; i < this.target.platforms.length; i++) {
            let platform = this.target.platforms[i]
            if (!this.platformOptions.includes(platform)) {
              hasError = true
              this.errors.add({
                scope: 'step-2',
                field: 'target_platforms',
                msg: 'Invalid platform ' + platform
              })
              break
            }
          }

          if (hasError) {
            reject('correct all values')
          } else {
            // TODO: to delete the if condition
            if (!this.debug) {
              this.createAdsets()
            }
            resolve(true)
          }
        })
      })
    },
    validateStep3() {
      return new Promise((resolve, reject) => {
        this.$validator.validateAll('step-2').then(result => {
          let hasError = false

          if (this.view !== 'normal') {
            this.view = 'normal'
            this.errors.clear()
            reject('view must be normal')
          }

          this.view = 'normal'
          if (this.ad.title.length <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-3',
              field: 'ad_title',
              msg: 'Invalid title (cannot be empty)'
            })
          }

          if (this.ad.text.length <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-3',
              field: 'ad_text',
              msg: 'Invalid text (cannot be empty)'
            })
          }

          if (!isValidHttpUrl(this.ad.link)) {
            hasError = true
            this.errors.add({
              scope: 'step-3',
              field: 'ad_link',
              msg: 'Invalid URL'
            })
          }

          if (this.ad.conversionDomain.length <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-3',
              field: 'ad_conversionDomain',
              msg: 'Invalid conversionDomain (cannot be empty)'
            })
          }

          if (this.ad.medias.length <= 0) {
            hasError = true
            this.errors.add({
              scope: 'step-3',
              field: 'ad_medias',
              msg: 'You must choose at least one media'
            })
          }
          if (this.ad.medias.length != 1 && this.ad.medias.length != parseInt(this.ad.count)) {
            hasError = true
            this.errors.add({
              scope: 'step-3',
              field: 'ad_medias',
              msg: 'The number of medias selected must be either 1 or ' + this.ad.count + ' (number of ads per adset)'
            })
          }

          if (this.hasVideo() && this.ad.preview === null) {
            hasError = true
            this.errors.add({
              scope: 'step-3',
              field: 'ad_medias_preview',
              msg: 'Since you selected at least one video, you must also choose a preview'
            })
          }

          if (hasError) {
            reject('correct all values')
          } else {
            // TODO: to delete the if condition
            if (!this.debug) {
              this.createAds()
            }
            resolve(true)
          }
        })
      })
    },
    async fetchProductMedias(productId) {
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      const productMediasResponse = await this.$http.get('/api/v1/products/' + productId + '/medias', { headers })
      this.productMedias = productMediasResponse.data.data
      this.ad.medias = []
    },
    async createCampaigns() {
      this.creatingCampaigns = true

      this.campaigns = []
      let campaignCount = parseInt(this.campaign.count)
      let params = {
        special_ad_categories: 'NONE',
        objective: this.campaign.objective,
      }
      if (this.campaign.cbo) {
        params.daily_budget = parseInt(this.campaign.daily_budget*100)
        params.bid_strategy = this.campaign.bid_strategy
      }
      for (let i = 0; i < campaignCount; i++) {
        params.name = this.campaign.name + ' - ' + i
        const campaign = await this.facebook.createCampaign(this.adAccount.id, params)
        if (campaign !== null && campaign.hasOwnProperty('id') && campaign.id !== null) {
          this.campaigns.push(campaign.id)
        } else {
          // TODO: Log error
        }
      }

      this.creatingCampaigns = false
    },
    async createAdsets() {
      // Wait for campaigns to be created
      while (this.creatingCampaigns) {
        await sleep(1000)
      }

      this.creatingAdsets = true

      this.adsets = []
      let adsetCount = parseInt(this.adset.count)
      let params = {
        optimization_goal: this.campaign.objective,
        billing_event: 'IMPRESSIONS', // TODO: What about this?
        targeting: {
          device_platforms: this.target.devices,
          publisher_platforms: this.target.platforms,
          genders: this.target.genders,
          geo_locations: {
            countries: this.target.countries,
          },
        },
      }
      if (this.target.ageMin > 0) {
        params.targeting.age_min = parseInt(this.target.ageMin)
      }
      if (this.target.ageMax > 0) {
        params.targeting.age_max = parseInt(this.target.ageMax)
      }
      if (this.target.wifi) {
        params.targeting.wireless_carrier = ['Wifi']
      }

      // Budget & bid amount
      if (!this.campaign.cbo) {
        params.bid_strategy = this.campaign.bid_strategy
        params.daily_budget = parseInt(this.campaign.daily_budget*100)
      }
      if (this.campaign.bid_strategy != 'LOWEST_COST_WITHOUT_CAP') {
        params.bid_amount = parseInt(this.campaign.bid_amount*100)
      }

      // Tracking specs
      if (this.campaign.objective === 'CONVERSIONS') {
        params.promoted_object = {
          custom_event_type: this.campaign.conversionEvent,
          pixel_id: this.campaign.pixel.id,
        }
        params.optimization_goal = 'OFFSITE_CONVERSIONS'
        this.trackingSpecs = [{ 'action.type': 'offsite_conversion', 'fb_pixel': this.campaign.pixel.id }]
        params.tracking_specs = this.trackingSpecs
      }

      for (let cIndex = 0; cIndex < this.campaigns.length; cIndex++) {
        let campaignId = this.campaigns[cIndex]
        params.campaign_id = campaignId
        for (let aIndex = 0; aIndex < adsetCount; aIndex++) {
          params.name = 'Adset ' + aIndex // TODO:
          const adset = await this.facebook.createAdset(this.adAccount.id, params)
          if (adset !== null && adset.hasOwnProperty('id') && adset.id !== null) {
            this.adsets.push(adset.id)
          } else {
            // TODO: Log error
          }
        }
      }

      this.creatingAdsets = false
    },
    async createAds() {
      this.$vs.loading()

      // Wait for adsets to be created
      while (this.creatingAdsets) {
        await sleep(1000)
      }

      this.creatingAds = true

      // Get Page backed instagram account (PBIA)
      const pbiAccounts = await this.facebook.fetchPageBackedInstagramAccounts(this.page.id)
      if (typeof(pbiAccounts) !== 'object' || pbiAccounts.length <= 0) {
        // TODO: Log error
      }
      const pbiAccountId = pbiAccounts[0].id

      /*
      let preview = null
      let previewBase64 = ''
      if (this.ad.preview !== null) {
        previewBase64 = await this.convertImgToBase64(this.ad.preview.url)
        let params = {
          name: this.ad.preview.pathname.replace('/', '_'),
          bytes: previewBase64,
        }
        preview = await this.facebook.uploadAdImage(this.adAccount.id, params)
        if (preview === null) {
          // TODO: Log error
        }
      }
      */

      let medias = []
      for (let i = 0; i < this.ad.medias.length; i++) {
        let media = null

        let mediaType = this.ad.medias[i].type
        if (mediaType === 'video') {
          let params = {
            file_url: this.ad.medias[i].url,
          }
          media = await this.facebook.uploadAdVideo(this.adAccount.id, params)
          if (media !== null) { media.type = 'video' }
        } else if (mediaType === 'image') {
          // TODO: We don't need to upload the image anymore since we directly pass the url
          // to facebook
          /*
          let media64 = await this.convertImgToBase64(this.ad.medias[i].url)
          let params = {
            name: this.ad.medias[i].pathname.replace('/', '_'),
            bytes: media64,
          }
          media = await this.facebook.uploadAdImage(this.adAccount.id, params)
          if (media !== null) { media.type = 'image' }
          for (const [key, value] of Object.entries(media.images)) {
            media.hash = value.hash
          }
          */
        }

        if (media === null) {
          // TODO: Log error
        }
      }
      for (let i = 0; i < this.ad.medias.length; i++) {
        medias.push(this.ad.medias[i])
      }
      if (this.ad.count > medias.length) {
        let toRepeat = this.ad.count - medias.length
        for (let i = 0; i < toRepeat; i++) {
          medias.push(medias[0])
        }
      }

      for (let aIndex = 0; aIndex < this.adsets.length; aIndex++) {
        for (let i = 0; i < medias.length; i++) {
          let media = medias[i]
          let params = {
            adset_id: this.adsets[aIndex],
            name: 'Ad ' + i,
            conversion_domain: this.ad.conversionDomain,
          }

          // Creative
          if (media.type === 'image') {
            params.creative = {
              name: 'ad creative',
              object_story_spec: {
                link_data: {
                  call_to_action: { type: 'LEARN_MORE', value: { link: this.ad.link } },
                  link: this.ad.link,
                  name: this.ad.title,
                  message: this.ad.text,
                  picture: media.url,
                },
                page_id: this.page.id,
                instagram_actor_id: pbiAccountId,
              },
              instagram_actor_id: pbiAccountId,
            }
            if (this.trackingSpecs !== null) {
              params.tracking_specs = this.trackingSpecs
            }
          } else if (media.type === 'video') {
            params.creative = {
              name: 'ad creative',
              object_story_spec: {
                video_data: {
                  call_to_action: { type: 'LEARN_MORE', value: { link: this.ad.link } },
                  link: this.ad.link,
                  title: this.ad.title,
                  message: this.ad.text,
                  video_id: media.id,
                  image_url: this.ad.preview.url,
                },
                page_id: this.page.id,
                instagram_actor_id: pbiAccountId,
              },
              instagram_actor_id: pbiAccountId,
            }
          }

          const ad = await this.facebook.createAd(this.adAccount.id, params)
          if (ad === null) {
            // TODO: Log error
          }
          this.ads.push(ad.id)
        }
      }

      this.creatingAds = false

      this.$vs.loading.close()

      this.$vs.notify({
        title: 'Success',
        text: 'Ads successfully launched',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$router.push({ path:'/dashboard/facebook-ads' })
    },
    onChangeAdAccount() {
      this.campaign.pixel = ''
      this.pixelOptions = this.adAccount.adspixels
    },
    openNewPage(url) {
      window.open(url, '_blank')
    },
    selectMedia(pm) {
      if (this.ad.medias.includes(pm)) {
        let toRemove = this.ad.medias.indexOf(pm)
        this.ad.medias.splice(toRemove, 1);
      } else {
        this.ad.medias.push(pm)
      }
    },
    mediaIsSelected(pm) {
      return this.ad.medias.includes(pm)
    }, 
    openProductMedia(pm) {
      this.openNewPage(pm.url)
    },
    openProductMediaPage() {
      let routeData = this.$router.resolve({name: 'product-medias'});
      this.openNewPage(routeData.href)
    },
    selectPreview(pm) {
      if (this.ad.preview === null) {
        this.ad.preview = pm
      } else if (this.ad.preview === pm.id) {
        this.ad.preview = null
      } else {
        this.ad.preview = pm
      }
    },
    previewIsSelected(pm) {
      return (this.ad.preview !== null && this.ad.preview === pm)
    },
    hasVideo() {
      for (let i = 0; i < this.ad.medias.length; i++) {
        let pm = this.ad.medias[i]
        if (pm.type === 'video') {
          return true
        }
      }
      return false
    },
    async convertImgToBase64(url, outputFormat = null) {
      let result = ''

      let img = await createImageFromUrl(url)
      let canvas = document.createElement('CANVAS');
      let ctx = canvas.getContext('2d');
      canvas.height = img.height
      canvas.width = img.width
      ctx.drawImage(img,0,0)
      result = canvas.toDataURL(outputFormat || 'image/png')
      canvas = null // Cleanup

      let toSearch = 'base64,'
      result = result.substring(result.indexOf(toSearch) + toSearch.length, result.length)
      return result
    },
    async searchImage(query) {
      this.ad.medias = []
      let searchImageResults = []
      try {
        let apiCallUrl = 'https://api.bing.microsoft.com/v7.0/images/search'
        let params = {
          q: query,
          minWidth: 600,
          minHeight: 600,
          aspect: 'Square',
        }
        let headers = {
          'Ocp-Apim-Subscription-Key': 'e38ac9a09baa4264becfe21f7dc17211',
        }
        const response = await this.$http.get(apiCallUrl, { headers, params })
        for (let i = 0; i < response.data.value.length; i++) {
          let res = response.data.value[i]
          searchImageResults.push({
            type: 'image',
            thumbnailUrl: res.thumbnailUrl,
            url: res.contentUrl,
          })
        }
      } catch (error) {
        // Nothing

        // TODO: to delete
        console.log(error)
      }
      this.searchImageResults = searchImageResults
      return this.searchImageResults
    },
  },
  components: {
    FormWizard,
    TabContent,
  },
  async created() {
    this.$vs.loading()

    this.facebook = new Facebook(localStorage.getItem('accessToken'))

    // Fetch advertiser
    this.advertiserId = parseInt(this.$route.params.facebook_advertiser_id)
    let advertiser = await this.facebook.fetchAdvertiser(this.advertiserId)
    if (advertiser !== null) {
      this.advertiser = advertiser
      this.facebook.setAccessToken(this.advertiser.access_token)
    } else {
      this.$vs.notify({
        title: 'Error',
        text: 'Invalid advertiser',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'danger'
      })
      this.$router.push({path:'/'})
    }

    // Fetch ad accounts
    this.adAccounts = await this.facebook.fetchAdAccounts()

    // Fetch pages
    this.pages = await this.facebook.fetchPages()
    this.pages.filter(page => (page.hasOwnProperty('is_published') && page.is_published))

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Fetch products
    try {
      const productsResponse = await this.$http.get('/api/v1/products', { headers })
      if (productsResponse !== null && productsResponse.hasOwnProperty('data') && productsResponse.data !== null &&
          productsResponse.data.hasOwnProperty('data') && productsResponse.data.data !== null) {
        this.products = productsResponse.data.data
      }
    } catch (error) {
      // Nothing
    }

    this.$vs.loading.close()

    // TODO: to delete
    // DEBUG: This is only for debug
    if (this.debug) {
      this.product = this.products[3]
      this.page = this.pages[0]
      this.adAccount = this.adAccounts[0]
      this.onChangeAdAccount()
      this.campaign.name = 'test campaign'
      this.campaign.pixel = this.pixelOptions[0]
    }
  },
}
</script>

<style>
.selected-media {
    border: 3px solid red;
}
</style>
