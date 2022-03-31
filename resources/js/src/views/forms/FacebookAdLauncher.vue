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
            <div class="vx-col w-full">
              <my-select v-model="adTemplate" :options="adTemplates" class="w-full select-large" label="Choose Ad Template" :autocomplete="true" />
              <p class="flex items-center w-full">
                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-download" @click="loadAdTemplate(adTemplate)"></vs-button>
                <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="deleteAdTemplate(adTemplate.id)"></vs-button>
              </p>
            </div>
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
              <my-select v-model="monitor" :options="monitorOptions" class="w-full select-large" label="Monitor" :autocomplete="true" />
              <span class="text-danger">{{ errors.first('step-1.campaign_monitor') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <my-select v-model="campaign.objective" :options="campaignObjectiveOptions" class="w-full select-large" label="Campaign objective" :autocomplete="true" />
              <span class="text-danger">{{ errors.first('step-1.campaign_objective') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <my-select v-model="campaign.conversionEvent" :options="campaignConversionEventOptions" class="w-full select-large" label="Conversion Event" :autocomplete="true" />
              <span class="text-danger">{{ errors.first('step-1.campaign_conversionEvent') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
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
            <div class="vx-col md:w-full w-full mt-5">
              <vs-checkbox v-model="campaign.accelerated">Accelerated</vs-checkbox>
            </div>
            <div class="vx-col md:w-full w-full mt-5">
              <my-select v-model="campaign.mode" :options="modeOptions" class="w-full select-large" label="Split mode" :autocomplete="true" @change="onChangeMode" />
              <span class="text-danger">{{ errors.first('step-1.campaign_mode') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <vs-input type="number" min="1" step="1" v-model="campaign.count" class="w-full" label="How many campaigns?" />
              <span class="text-danger">{{ errors.first('step-1.campaign_count') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <vs-input type="number" min="1" step="1" v-model="adset.count" class="w-full" label="How many adsets per campaign?" :disabled="campaign.mode === 'split adset' || campaign.mode === 'split creative'" />
              <span class="text-danger">{{ errors.first('step-1.adset_count') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <vs-input type="number" min="1" step="1" v-model="ad.count" class="w-full" :label="'How many ads per ' + ((campaign.mode === 'split adset') ? 'campaign' : 'adset') + '?'" />
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
              <vs-checkbox v-model="ad.useExistingCopy" @change="onChangeUseExistingCopy">Use an existing copy</vs-checkbox>
            </div>

            <div class="vx-col md:w-full w-full mt-5" v-if="ad.useExistingCopy">
              <my-select v-model="ad.copy" :options="copys" class="w-full select-large" label="Copys" :autocomplete="true" @change="onChangeCopy" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <vx-input-group prependClasses="border border-solid border-grey border-r-0" label="Copy">
                <template slot="prepend">
                  <div class="prepend-text bg-primary">
                    <span>Copy</span>
                  </div>
                </template>
                <vs-input type="number" min="1" step="1" v-model="ad.copyNumber" class="w-full" placeholder="Copy ID (Ex. 28)" />
                <vs-input type="text" v-model="ad.title" class="w-full" placeholder="Title" />
                <vs-textarea v-model="ad.text" class="w-full" placeholder="Title" ></vs-textarea>
              </vx-input-group>

              <span class="text-danger">{{ errors.first('step-3.ad_copy_number') }}</span>
              <span class="text-danger">{{ errors.first('step-3.ad_title') }}</span>
              <span class="text-danger">{{ errors.first('step-3.ad_text') }}</span>
            </div>
            <div class="vx-col md:w-full w-full flex items-center" v-if="ad.copy === null">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-save" @click="saveCopy(product.id, ad.title, ad.text)" :disabled="ad.copy !== null || (ad.title === '' || ad.text === '')"></vs-button>
            </div>
            <div class="vx-col md:w-full w-full flex items-center" v-else>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-save" @click="updateCopy(ad.copy.id, ad.title, ad.text)" :disabled="ad.copy === null || (ad.title == '' || ad.text == '')"></vs-button>
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
                <span class="ml-auto mr-auto">{{ (ad.medias.length == 0) ? 'No media selected' : ad.medias.length + ' media(s) selected / ' + ((campaign.mode === 'duplicate') ? '1 or ' : ' ') + getRequiredNumberOfMedias()+ ' required' }}</span>
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

            <div class="vx-col w-full">
              <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newAdTemplateName" label="New ad template name" />
              <vs-button class="mt-12 mr-auto ml-auto" @click="saveAdTemplate(newAdTemplateName)" color="primary" type="filled" :disabled="newAdTemplateName == ''">Save Ad Template</vs-button>
            </div>
          </div>

          <div class="vx-row" v-else-if="view == 'medias'">
            <div class="vx-col w-full flex items-center mb-8">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="(ad.medias.length > 0) ? 'icon-check' : 'icon-arrow-left'" @click="view = 'normal'"></vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="fetchProductMedias(product.id)"></vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="openProductMediaPage"></vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-search" @click="view = 'medias_search'"></vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-cloud" @click="chooseMediasFromDropbox()">From Dropbox</vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-hard-drive" @click="chooseMediasFromLocal()">From Your PC</vs-button>
              <input type="file" ref="mediaUploadFileInput" class="hidden" accept="image/png, image/jpeg" multiple @change="onLocalMediasChosen">
            </div>
            <div class="vx-col sm:w-1/3 md:w-1/5 w-full" :class="mediaIsSelected(item) ? 'selected-media' : ''" v-for="(item, index) in mediasOptions" @click="selectMedia(item)" @dblclick="openProductMedia(item)">
              <img class="w-full" :src="(item.type == 'image') ? item.url : videoThumbnail">
              <center class="text-xl font-bold">{{item.filename}} ({{item.type}})</center>
            </div>
          </div>

          <div class="vx-row" v-else-if="view == 'medias_search'">
            <div class="vx-col w-full flex items-center mb-8">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="(ad.medias.length > 0) ? 'icon-check' : 'icon-arrow-left'" @click="view = 'normal'"></vs-button>
              <vs-input type="text" v-model="mediaSearchQuery" @keydown.enter="searchImages(searchEngine, mediaSearchQuery)" />
              <vs-button v-for="(obj, se, index) in searchEngines" :key="'se_' + index" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-search" @click="searchImages(se, mediaSearchQuery)" :disabled="searchEngines[se].apiKey == ''">{{ se }}</vs-button>
            </div>
            <div class="vx-col sm:w-1/3 md:w-1/5 w-full" :class="mediaIsSelected(item) ? 'selected-media' : ''" v-for="(item, index) in mediasOptions" @click="selectMedia(item)" @dblclick="openNewPage(item.url)">
              <img class="w-full" :src="(item.type == 'image') ? item.url : videoThumbnail">
            </div>
            <div class="vx-col w-full flex items-center mt-4 mb-8" v-if="searchImageResults.length > 0">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="searchImages(searchEngine, mediaSearchQuery, searchEngines[searchEngine].prevOffset)" :disabled="searchEngines[searchEngine].currentOffset === 0"></vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-right" @click="searchImages(searchEngine, mediaSearchQuery, searchEngines[searchEngine].nextOffset)"></vs-button>
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
              <img class="w-full" :src="(item.type == 'image') ? item.url : videoThumbnail">
              <center class="text-xl font-bold">{{item.filename}} ({{item.type}})</center>
            </div>
          </div>

          <div class="vx-row" v-else-if="view == 'preview_search'">
            <div class="vx-col w-full flex items-center mb-8">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="(ad.preview !== null) ? 'icon-check' : 'icon-arrow-left'" @click="view = 'normal'"></vs-button>
              <vs-input type="text" v-model="mediaSearchQuery" @keydown.enter="searchImages(searchEngine, mediaSearchQuery)" />
              <vs-button v-for="(obj, se, index) in searchEngines" :key="'sep_' + index" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-search" @click="searchImages(se, mediaSearchQuery)" :disabled="searchEngines[se].apiKey == ''">{{ se }}</vs-button>
            </div>
            <div class="vx-col sm:w-1/3 md:w-1/5 w-full" :class="previewIsSelected(item) ? 'selected-media' : ''" v-for="(item, index) in mediasOptions" @click="selectPreview(item)" @dblclick="openNewPage(item.url)">
              <img class="w-full" :src="(item.type == 'image') ? item.url : videoThumbnail">
            </div>
            <div class="vx-col w-full flex items-center mt-4 mb-8" v-if="searchImageResults.length > 0">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="searchImages(searchEngine, mediaSearchQuery, searchEngines[searchEngine].prevOffset)" :disabled="searchEngines[searchEngine].currentOffset === 0"></vs-button>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-right" @click="searchImages(searchEngine, mediaSearchQuery, searchEngines[searchEngine].nextOffset)"></vs-button>
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

function getFilename(pathname) {
  return pathname.replace(/^.*[\\\/]/, '')
}

function getFilenameNoExtension(pathname) {
  return pathname.replace(/^.*[\\\/]/, '').replace(/\.[^/.]+$/, "")
}

function getFileExtension(pathname) {
  let ext = pathname.split('.').pop()
  if (ext === undefined) {
    ext = ''
  }
  return ext
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

function createImageFromFile(file) {
  return new Promise((resolve, reject) => {
    let img = new Image();
    img.crossOrigin = 'Anonymous';
    img.onload = () => resolve(img)
    img.onerror = reject
    img.src = URL.createObjectURL(file);
  })
}

function createFileFromBase64DataURL(base64DataUrl, fileName = null) {
  if (fileName === null) {
    let tmp = (base64DataUrl.split(';'))[0]
    tmp = (tmp.split('/'))[1]
    let extension = tmp
    if (extension == 'jpeg') {
      extension = 'jpg'
    }
    fileName = 'test.' + extension
  }

  let arr = base64DataUrl.split(',')
  let mime = arr[0].match(/:(.*?);/)[1]
  let bstr = atob(arr[1])
  let n = bstr.length
  let u8arr = new Uint8Array(n)
  while (n--) {
    u8arr[n] = bstr.charCodeAt(n)
  }
  return new File([u8arr], fileName, {type:mime})
}

function getDomainFromUrl(url) {
  let domain = ''

  try {
    url = url.trim()
    if (url.indexOf('http://') !== 0 && url.indexOf('https://') !== 0) {
      url = 'https://' + url
    }

    let u = new URL(url)
    domain = u.host

    let prefix = 'www.'
    if (domain.indexOf(prefix) === 0) {
      domain = domain.substr(prefix.length)
    }
  } catch (error) {
    // TODO: Log error?
  }

  return domain
}

// TODO: to delete??? We don't use this code
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
  components: {
    FormWizard,
    TabContent,
  },
  data () {
    return {
      debug: false,

      settings: {},

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
        accelerated: false,
        mode: 'duplicate',
      },
      adset: {
        count: 1,
      },
      ad: {
        count: 1,
        copyNumber: '',
        useExistingCopy: false,
        copy: null,
        title: 'Title Example',
        text: 'Text Example',
        link: 'http://domain.com',
        conversionDomain: 'domain.com',
        medias: [],
        preview: null,
      },

      modeOptions: [ 'duplicate', 'split adset', 'split creative' ],

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
      videoThumbnail: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAkFBMVEX///8EAgQAAAD29vby8vJubm7MzMzv7+/b29u7u7v7+/tTU1MzNTM3NzfOzs739/epqqmcnJzU1NTk5OR/f39lZmVISUiOjo6JiYkxMTEWFRZgYWDn5+coKSi4ubh3d3dBQUGfn59aWVpRUFEgHyAPDg/DxMOTlJOoqaiDg4MrKisjIyM9PD0LCwt5enlFRUUd5XBlAAAIIklEQVR4nO2da1savRaG42KAYdzAAAoKyEERtKL9//9uz1BAxAGyTsnwXnk+tB9au3KXkMM6xZigoKCgoKCgoKCgoKCgoKCgoKCgoNIo6sW1dHX3Va1Wn/v97Nfp3aDTiOOx74FJKF4ORrMFFOu1+TJNa5HvMZLVbqxmO5abIu3+cFFNe74Hi1bUepidYfsN+llN674Hba/6pDq0g/uJ2V/FvodupeU9lu4Astkp+ycZvxPxDj7JRuKb4qSSyS0Hb085HJRzG2nPH/l4W0aodn3j/FJ7cCPEt2V8KRdjpSMwPY8Zb0u0sqavwnxbyGpJDgLLpgbfBhEefMNlqt8r8W0Zl74B54p8/xifvU7VXl8ZcMP45g8w1efbID57umBFt04AN4wNH4C1T0d8G8R394DaS8wxY9/1pWPkFjD/GJ0e48b/c8y3QUzdAXYX7gFzxIErwKXrGbpHrLoBbHgCzBlvXQC++QPMEfU3f8e7xC/EWeW/DZghNnUdVRPfgPnerwnocZE5QBzpAbbKAJghqm0avXIAZohKzo3Iy0mmUEoHuNvSAGanm5YC4Ht5ADPEV/nLVFomwAzxURqwNKvMTuK3/mbJAG+kPal3ZQPMED8lv4q+boRnJXmVqgxLCCi6K1bxgG5cxVLztEYBdDGvpc7gySOBMH5wgCjlC+8QhgqR6T07iNp8SFyH65SBQu5rmEiHvgvsdAQICcvMltAkd9qIEotNjzRG2PqLutoBRoHbMO3OBHuPWKo7VQG4GRtEx8U3oYk0A/2ZpXsmIfHaC4dezdpaNZmB9yFSfU8/CE3S0UTkfRPviSODI8/0WG9zBODk+dEW0pvfhNn1RCVvamPri0H4JUdoEq1zHAD9YJOQx1RAaEz8osMIczLhG3lAhYRamyP8IRPSnTMnCE1lqoJYIwJ26aM5RZjtPwqbI3nDYPiATxNqhCDhhhYZbn/oEJq6+OYIExIhwXnxbfJ8LHoplfS+M0dzZ3B8pBcITXsligjQphC+KhJmxyXRzZHksOlyBnCZ0JjGUI6RdIdaaROaipyTA4BAyIrFWBFm8+SvFCMhZBqxbFsSbjZHEUaC023JMmxNaKKqCCK8oAkfHBEKbY6wQB9r+s4ITSKxOaLdNXVeQA1FaMyYn/aPrsqIeRaRhNnm+IfJCFOkxYljQtNmOjnQCX3M7Bk8YTZtZhxGwJocuSdk1jECrgIsYZYb0AhNxHByIA/fvBMNmdCYFnlzRHrc6szFm0yYh5yJwSBcSibvzMYipFalItNruHl6HMLs/5eyOcITygYlO0GO0CQDPCLyijjwS0iqv8URTn0TZl+UBZIRULcLatxQkNBE78gWN6g44qwEhMZ0UZsjzpHBraCUIdyUkVmPBBefKQuhqds7Oa6U0Jia7eZ4tYS559ZqNFdMaOInm+FcM+HAKoRyvYRdy/4310pov5jiCJ/LQohon4JzYzDdNFKEqPRU3KnN+91iI1z0DXe3YAUPhQgbOM8b8n44906ITvWHIerfZ7q8+YT4YA3MUAaYYQsuYe2D4MXABS68+UtzjUlBU1ihjFSYddscwjmtLAxbyfbiizCm9ijEdlliuqKohPTABeAcUWyHKZFwQs/Dgj/IXOiWB8KYE+tGZ0Uxq9MphEjn4bFFdK8MXnk6nnDJzHLDF6/zfMJYwjG3OAqQIWDDjT4hCfm5X7DGAmbnNid5bblaTwJdwAmFM6yCcwQhwud7ziAhhZZUHYsnTEW6GZCqZVkXKFvCHiuH5sBeEw9oKqz8HSvCCm8LPLRHqunm+NusCBvYGOgZe6RCUnpdlxXhWLDJK3xQAGmtBqwJB5JlbNTOpoxpeonQOmRmZ41a7cxYTc8T0twUZ6xRWylW6P2szxJiQtd21shd2+kX/TOEtqEkhDF6cwy6T/EkYaTQDIRTkb+mjuYUIToNyMoYo9N3Q5ZwrFLMTTqx7WUVQC+yWkDYJqTjWdlitVKinmsKCNn1BqdM8TooUn3fvwj1Hmvhdm0jhtmOCfVaSMMjsxlWQtv1fxK2tF4TuuF/hFSP1CFhXaWPws4QvWPEXqRv4gGhbqMoiR6fpLT9PSGv1OeyHXxhZYEol6gtYUW5tyC/09dGFM/pP8KGgCP0vJk7CUBSwWxOKFA2ecnKUCr1Cr/YQKTa32tnRewZKHwPEJgrndF+GHmWAqR0xHLSvVSwVTKlSau6ZJ8qYzRU0hKrTVuBuKVe4kJnJlwUM8FGWkJ7/aES6nVfRUDsDHVWvKCwsHReCyzBO0E7ab3dxU0cFhMsSI2vLERuhSkr0a3+SNzCSxEpLKPfSkrwmg6Q+0DayftbJeqvPFe49bNsQIWN8Kcir4gOAI3Xl610XrL6LW5RFAPQ1Yu56k8enAD8cPfMugMfTAHg2uWzzu6fRwL2EwFI9RxvjCDyGglKbd1HHY4BP3UPMsVy9zAwwF/9Z4CLRC7gQQM6n6F7uXnUaa14l7goevMxe0BnL6qf0Eo3AAozd7v8Kak+PzLUeTQWq6VOnDebHAM/S2iBUsHW3N98Vc4DMuKay0abMr6RzxW0SMmb3LKa8U3LxpcrafRF1tX8+1eq+XmoeApMyOzHmxPpqJKokrRPh8x+cPFQxul5pF6HBJn9yNPUxw2CpHF6DwjKzd99XLW0ohFKanX+Alzk/Pc3hl8Tlx4KQXUn0/4H7HXElet1PerUrpRup6jXTR+mo/UBaf6pPd1W7zrL+MrhDpW0o6hej+JWq579FlVKvSMEBQUFBQUFBQUFBQUFBQUFBQUF/db/Afm8kjbo5XSmAAAAAElFTkSuQmCC',
      videoExtensions: [
        '3gp','3gpp','3gpp2','3g2','asf','avi','dv','dvi','flv','m2t','mp4',
        'm4v','mkv','mov','mpeg','mpg','mts','ogv','ogx','rm','rmvb','ts',
        'vob','webm','wm',
      ],

      creatingCampaigns: false,
      creatingAdsets: false,
      creatingAds: false,

      trackingSpecs: null,

      mediaSearchQuery: '',
      searchImageResults: [],

      searchEngines: {
        'bing': {
          apiKey: '',
          count: 50,
          prevOffset: 0,
          currentOffset: 0,
          nextOffset: 0,
        },
        'google': {
          apiKey: '',
          prevOffset: 0,
          currentOffset: 0,
          nextOffset: 0,
        },
        'yandex': {
          apiKey: '',
          prevOffset: 0,
          currentOffset: 0,
          nextOffset: 0,
        },
      },
      searchEngine: 'bing',

      adTemplates: [],
      adTemplate: {},
      newAdTemplateName: '',

      copys: [],

      monitor: '',
      monitors: [],
      monitorOptions: [],
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

          // TODO: Not required for now
          /*
          if (this.campaign.name.length == 0) {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'campaign_name',
              msg: 'Campaign name is required'
            })
          }
          */

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

          if (!this.modeOptions.includes(this.campaign.mode)) {
            hasError = true
            this.errors.add({
              scope: 'step-1',
              field: 'campaign_mode',
              msg: 'Invalid mode'
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
            this.fetchCopys()
            this.fetchProductMedias(this.product.id)
            if (!this.debug) {
              this.createCampaigns()
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

          if (this.ad.copyNumber == '' || parseInt(this.ad.copyNumber) < 0) {
            hasError = true
            this.errors.add({
              scope: 'step-3',
              field: 'ad_copy_number',
              msg: 'Invalid copy id'
            })
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

          let requiredNumOfMedias = this.getRequiredNumberOfMedias()
          if (this.campaign.mode === 'duplicate') {
            if (this.ad.medias.length !== 1 && this.ad.medias.length !== requiredNumOfMedias) {
              hasError = true
              this.errors.add({
                scope: 'step-3',
                field: 'ad_medias',
                msg: 'In normal mode the number of medias selected must be either 1 or ' + requiredNumOfMedias + ' (number of ads per adset)'
              })
            }
          } else {
            if (this.ad.medias.length !== requiredNumOfMedias) {
              hasError = true
              this.errors.add({
                scope: 'step-3',
                field: 'ad_medias',
                msg: 'In split mode the number of medias selected must be ' + requiredNumOfMedias + ' (#ads per campaign x #campaigns)'
              })
            }
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
            if (!this.debug) {
              this.createAdsets()
            }

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
      for (let i = 0; i < this.productMedias.length; i++) {
        let pm = this.productMedias[i]
        pm.filename = getFilenameNoExtension(pm.pathname)
        this.productMedias[i] = pm
      }
      this.ad.medias = []
    },
    async fetchCopys() {
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let params = {
        filter_product: this.product.id,
      }
      const response = await this.$http.get('/api/3rd/facebook/copys', { headers, params })
      this.copys = response.data.data
      for (let i = 0; i < this.copys.length; i++) {
        let copy = this.copys[i]
        copy.name = 'C' + copy.number
        this.copys[i] = copy
      }
      return this.copys
    },
    async saveCopyNoLoading(productId, adTitle, adText) {
      if (adTitle == '' || adText == '') {
        return null
      }
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let params = {
        product_id: productId,
        ad_title: adTitle,
        ad_text: adText,
      }
      const response = await this.$http.post('/api/3rd/facebook/copys', params, { headers })
      let copy = response.data.data
      return copy
    },
    async saveCopy(productId, adTitle, adText) {
      this.$vs.loading()

      if (adTitle == '' || adText == '') {
        return
      }
      let copy = await this.saveCopyNoLoading(productId, adTitle, adText)
      this.ad.copyNumber = copy.number

      await this.fetchCopys()

      this.$vs.loading.close()

      this.$vs.notify({
        title: 'Success',
        text: 'Copy successfully created',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    async updateCopy(copyId, adTitle, adText) {
      this.$vs.loading()

      if (adTitle == '' || adText == '') {
        return
      }
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let params = {
        ad_title: adTitle,
        ad_text: adText,
      }
      const response = await this.$http.put('/api/3rd/facebook/copys/' + copyId, params, { headers })
      let copy = response.data.data

      await this.fetchCopys()

      for (let i = 0; i < this.copys.length; i++) {
        if (copy.id == this.copys[i].id) {
          this.ad.copy = this.copys[i]
          break
        }
      }
      this.onChangeCopy()

      this.$vs.loading.close()

      this.$vs.notify({
        title: 'Success',
        text: 'Copy successfully updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    onChangeUseExistingCopy() {
      if (!this.ad.useExistingCopy) {
        this.ad.copy = null
      }
    },
    onChangeCopy() {
      if (this.ad.copy !== null && this.ad.copy != '') {
        this.ad.copyNumber = this.ad.copy.number
        this.ad.title = this.ad.copy.ad_title
        this.ad.text  = this.ad.copy.ad_text
      }
    },
    getBidReference(bidStrategy, bidAmount) {
      let bidRef = ''
      if (bidStrategy === 'LOWEST_COST_WITHOUT_CAP') {
        bidRef = 'Auto'
      } else if (bidStrategy === 'LOWEST_COST_WITH_BID_CAP') {
        bidRef = 'BC ' + parseFloat(bidAmount).toFixed(2)
      } else if (bidStrategy === 'COST_CAP') {
        bidRef = 'CC ' + parseFloat(bidAmount).toFixed(2)
      }
      return bidRef
    },
    getTargetReference(ageMin, ageMax, genders, countries, devices, platforms) {
      let targetRef = ''

      targetRef += countries[0]
      for (let i = 1; i < countries.length; i++) {
        targetRef += ',' + countries[i]
      }

      targetRef += ' - '
      if (ageMin == '' || parseInt(ageMin) === 0) {
        if (ageMax == '' || parseInt(ageMax) === 0) {
          targetRef += '0+'
        } else {
          targetRef += parseInt(ageMax) + '-'
        }
      } else if (ageMax == '' || parseInt(ageMax) === 0) {
        if (ageMin == '' || parseInt(ageMin) === 0) {
          targetRef += '0+'
        } else {
          targetRef += parseInt(ageMin) + '+'
        }
      } else {
        targetRef += parseInt(ageMin) + '-' + parseInt(ageMax)
      }
      if (genders.length === 1) {
        if (genders[0] === 1) {
          targetRef += ' M'
        } else if (genders[0] === 2) {
          targetRef += ' F'
        }
      } else if (genders.length === 2) {
          targetRef += ' M/F'
      }
      

      // TODO: Just remove this for now
      /*
      let deviceRef = 'All'
      if (devices.length === 1) {
        if (devices[0] === 'mobile') {
          deviceRef = 'Mob'
        } else if (devices[0] === 'desktop') {
          deviceRef = 'Desk'
        }
      }
      targetRef += ' ' + deviceRef
      */

      targetRef += ' - '
      if (platforms.length === this.platformOptions.length) {
        targetRef += ' All'
      } else {
        let platformRef = {
          'facebook': 'FB',
          'instagram': 'IG',
          'audience_network': 'AN',
          'messenger': 'MG',
        }
        targetRef += ' ' + platformRef[platforms[0]]
        for (let i = 1; i < platforms.length; i++) {
          targetRef += ',' + platformRef[platforms[i]]
        }
      }

      return targetRef
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
        if (this.campaign.accelerated && this.campaign.bid_strategy === 'LOWEST_COST_WITH_BID_CAP') {
          params.pacing_type = ['no_pacing']
        }
      }

      let bidRef = this.getBidReference(this.campaign.bid_strategy, this.campaign.bid_amount)
      let targetRef = this.getTargetReference(this.target.ageMin, this.target.ageMax, this.target.genders, this.target.countries, this.target.devices, this.target.platforms)

      for (let i = 0; i < campaignCount; i++) {
        params.name = this.product.name + ' - ' + targetRef + ' - ' + bidRef + ' - '
        if (this.campaign.name.length > 0) {
          params.name += this.campaign.name + ' - '
        }
        params.name += '#' + i
        const campaign = await this.facebook.createCampaign(this.adAccount.id, params)
        if (campaign !== null && campaign.hasOwnProperty('id') && campaign.id !== null) {
          this.campaigns.push(campaign.id)
        } else {
          // TODO: Log error
        }
      }

      this.creatingCampaigns = false

      // Add campaigns to monitor
      if (this.monitor !== null && this.monitor != '') {
        let monitorResult = await this.facebook.addCampaignsToMonitor(this.monitor.id, this.campaigns)
        if (!monitorResult) {
          this.$vs.notify({
            title: 'Error',
            text: 'Cannot add the campaigns to the Facebook Monitor',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        }
      }
    },
    async createAdsets() {
      // Wait for campaigns to be created
      while (this.creatingCampaigns) {
        await sleep(1000)
      }

      this.creatingAdsets = true

      this.adsets = []
      let adsetCount = parseInt(this.adset.count)
      if (this.campaign.mode === 'split adset') {
        adsetCount = parseInt(this.ad.count)
      } else if (this.campaign.mode === 'split creative') {
        adsetCount = 1
      }
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
        if (this.campaign.accelerated && this.campaign.bid_strategy === 'LOWEST_COST_WITH_BID_CAP') {
          params.pacing_type = ['no_pacing']
        }
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

      let bidRef = this.getBidReference(this.campaign.bid_strategy, this.campaign.bid_amount)
      let targetRef = this.getTargetReference(this.target.ageMin, this.target.ageMax, this.target.genders, this.target.countries, this.target.devices, this.target.platforms)

      for (let cIndex = 0; cIndex < this.campaigns.length; cIndex++) {
        let campaignId = this.campaigns[cIndex]
        params.campaign_id = campaignId
        for (let aIndex = 0; aIndex < adsetCount; aIndex++) {
          params.name = this.product.name + ' - ' + targetRef + ' - ' + bidRef + ' - '
          if (this.campaign.name.length > 0) {
            params.name += this.campaign.name + ' - '
          }
          params.name += '#' + aIndex
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

      // We need to resize preview choosen from Google/Bing/etc...
      let preview = null
      // TODO: Old code
      /*
      if (this.ad.preview !== null && this.ad.preview.hasOwnProperty('to_resize') && this.ad.preview.to_resize) {
      */
      if (this.ad.preview !== null && (!this.ad.preview.hasOwnProperty('fb_uploaded') || !this.ad.preview.fb_uploaded)) {
        let previewBase64 = await this.resizeImageUrl(this.ad.preview.url)

        // TODO: Old code
        /*
        let imageFile = await createFileFromBase64DataURL(previewBase64)
        preview = await this.createTemporaryImage(imageFile)
        */

        let previewUrl = null
        let previewHash = null
        let uploadedOnFacebook = false
        const adImage = await this.facebook.uploadAdImage(this.adAccount.id, {
          bytes: this.base64RemovePrefix(previewBase64),
        })
        if (adImage !== null && adImage.hasOwnProperty('bytes') && adImage.bytes !== null) {
          if (adImage.bytes.hasOwnProperty('url') && adImage.bytes.url !== null) {
            previewUrl = adImage.bytes.url
          }
          if (adImage.bytes.hasOwnProperty('hash') && adImage.bytes.hash !== null) {
            previewHash = adImage.bytes.hash
          }
          uploadedOnFacebook = true
        }
        preview = {
          url: previewUrl,
          hash: previewHash,
          fb_uploaded: uploadedOnFacebook,
          type: 'image',
        }
      }

      if (preview !== null) {
        this.ad.preview = preview
      } else {
        // TODO: Log error
      }

      let medias = []
      for (let i = 0; i < this.ad.medias.length; i++) {
        let media = null

        let mediaType = this.ad.medias[i].type
        if (mediaType === 'video') {
          let params = {
            file_url: this.ad.medias[i].url,
          }
          media = await this.facebook.uploadAdVideo(this.adAccount.id, params)
          if (media !== null) {
            media.type = 'video'
            media.pathname = this.ad.medias[i].pathname
          }
        } else if (mediaType === 'image') {
          // We need to resize images choosen from Google/Bing/etc...
          // TODO: Old code
          /*
          if (this.ad.medias[i].hasOwnProperty('to_resize') && this.ad.medias[i].to_resize) {
          */
          if (!this.ad.medias[i].hasOwnProperty('fb_uploaded') || !this.ad.medias[i].fb_uploaded) {
            let imageBase64 = await this.resizeImageUrl(this.ad.medias[i].url)

            // TODO: Old code
            /*
            let imageFile = await createFileFromBase64DataURL(imageBase64)
            media = await this.createTemporaryImage(imageFile)
            */

            let mediaUrl = null
            let mediaHash = null
            let uploadedOnFacebook = false
            const adImage = await this.facebook.uploadAdImage(this.adAccount.id, {
              bytes: this.base64RemovePrefix(imageBase64),
            })
            if (adImage !== null && adImage.hasOwnProperty('bytes') && adImage.bytes !== null) {
              if (adImage.bytes.hasOwnProperty('url') && adImage.bytes.url !== null) {
                mediaUrl = adImage.bytes.url
              }
              if (adImage.bytes.hasOwnProperty('hash') && adImage.bytes.hash !== null) {
                mediaHash = adImage.bytes.hash
              }
              uploadedOnFacebook = true
            }
            media = {
              url: mediaUrl,
              hash: mediaHash,
              fb_uploaded: uploadedOnFacebook,
              type: mediaType,
            }
          }
        }

        if (media !== null) {
          this.ad.medias[i] = media
        } else {
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
          if (this.campaign.mode === 'split creative') {
            if (i > 0 && (i % parseInt(this.ad.count)) === 0) {
              aIndex++
            }
          }

          let media = medias[i]
          let adName = this.product.name + ' - ' + 'C' + parseInt(this.ad.copyNumber)
          if (media.hasOwnProperty('pathname')) {
            let fileNameNoExtension = getFilenameNoExtension(media.pathname)
            adName += ' - ' + fileNameNoExtension
          } else {
            // TODO: What if the media is taken from bing images?
          }
          if (this.campaign.name.length > 0) {
            adName += ' - ' + this.campaign.name
          }
          adName += ' - ' + '#' + i

          const ad = await this.createAd(
            this.adAccount.id, this.adsets[aIndex], adName,
            this.ad.link, this.ad.conversionDomain, this.ad.title, this.ad.text,
            media, this.page.id, pbiAccountId,
            this.ad.preview, this.trackingSpecs)
          if (ad === null) {
            // TODO: Log error
          }
          this.ads.push(ad.id)

          if (this.campaign.mode === 'split adset') {
            aIndex++
          }
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

      // TODO: uncomment this?
      //this.$router.push({ path:'/dashboard/facebook-ads' })
    },
    async createAd(
      adAccountId, adsetId, adName,
      adLink, conversionDomain, adTitle, adText,
      media, pageId, pbiAccountId,
      preview = null, trackingSpecs = null) {

      let params = {
        adset_id: adsetId,
        name: adName,
        conversion_domain: getDomainFromUrl(conversionDomain),
      }
      if (media.type === 'image') {
        params.creative = {
          name: 'ad creative',
          object_story_spec: {
            link_data: {
              call_to_action: { type: 'LEARN_MORE', value: { link: adLink } },
              link: adLink,
              name: adTitle,
              message: adText,
              //picture: media.url, // TODO: Does not work anymore with this
              image_hash: media.hash,
            },
            page_id: pageId,
            instagram_actor_id: pbiAccountId,
          },
          instagram_actor_id: pbiAccountId,
        }
        if (trackingSpecs !== null) {
          params.tracking_specs = trackingSpecs
        }
      } else if (media.type === 'video') {
        params.creative = {
          name: 'ad creative',
          object_story_spec: {
            video_data: {
              call_to_action: { type: 'LEARN_MORE', value: { link: adLink } },
              link: adLink,
              title: adTitle,
              message: adText,
              video_id: media.id,
              //image_url: preview.url, // TODO: Does not work anymore with this
              image_hash: preview.hash,
            },
            page_id: pageId,
            instagram_actor_id: pbiAccountId,
          },
          instagram_actor_id: pbiAccountId,
        }
      }

      const ad = await this.facebook.createAd(adAccountId, params)
      return ad
    },
    onChangeAdAccount() {
      this.campaign.pixel = ''
      this.pixelOptions = []
      if (this.adAccount !== null && this.adAccount != '' &&
          this.adAccount.hasOwnProperty('adspixels') && this.adAccount.adspixels !== null) {
        for (let i = 0; i < this.adAccount.adspixels.length; i++) {
          this.pixelOptions.push(this.adAccount.adspixels[i])
        }
      }

      this.monitor = ''
      this.monitorOptions = []
      for (let i = 0; i < this.monitors.length; i++) {
        let monitor = this.monitors[i]

        let adAccountId = null
        for (const [key, value] of Object.entries(monitor.campaigns)) {
          adAccountId = key
          break
        }
        if (adAccountId === null || adAccountId == '') {
          continue
        }
        if (adAccountId == this.adAccount.id) {
          this.monitorOptions.push(monitor)
        }
      }
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
    async convertImageToBase64(image, outputFormat = null) {
      let result = ''

      let canvas = document.createElement('CANVAS');
      let ctx = canvas.getContext('2d');
      canvas.height = image.height
      canvas.width = image.width
      ctx.drawImage(image,0,0)
      result = canvas.toDataURL(outputFormat || 'image/png')
      canvas = null // Cleanup

      return result
    },
    async searchImages(searchEngine, query, offset = 0) {
      this.$vs.loading()

      // TODO: We lose previous medias on a new search
      //this.ad.medias = []

      query = query.split(' ').join('+')

      let searchImageResults = []

      this.searchEngine = searchEngine
      let se = this.searchEngines[searchEngine]
      if (searchEngine === 'bing') {
        if (offset === 0) {
          se.prevOffset = 0
          se.currentOffset = 0
          se.nextOffset = 0
        }

        try {
          let apiCallUrl = 'https://api.bing.microsoft.com/v7.0/images/search'
          let params = {
            q: query,
            minWidth: 600,
            minHeight: 600,
            aspect: 'Square',
            offset: offset,
            count: se.count,
          }
          let headers = {
            'Ocp-Apim-Subscription-Key': se.apiKey,
          }
          const response = await this.$http.get(apiCallUrl, { headers, params })
          se.prevOffset = Math.max(0, se.currentOffset - se.count)
          se.currentOffset = response.data.currentOffset
          se.nextOffset = response.data.nextOffset
          for (let i = 0; i < response.data.value.length; i++) {
            let res = response.data.value[i]
            let width = parseInt(res.width)
            let height = parseInt(res.height)
            if (width < 700 || height < 700) {
              continue
            }
            searchImageResults.push({
              type: 'image',
              thumbnailUrl: res.thumbnailUrl,
              url: res.contentUrl,
              to_resize: true,
              fb_uploaded: false,
              width: width,
              height: height,
            })
          }
        } catch (error) {
          // Nothing
        }
      } else if (searchEngine === 'google') {
        try {
          let apiCallUrl = 'https://serpapi.com/search.json'
          let params = {
            api_key: se.apiKey,
            engine: 'google',
            q: query,
            google_domain: 'google.it', // TODO: .it or .com?
            tbm: 'isch',
            ijn: parseInt(offset),
            async: false,
          }
          const response = await this.$http.get(apiCallUrl, { params })
          for (let i = 0; i < response.data.images_results.length; i++) {
            let res = response.data.images_results[i]
            // TODO: Remove images too small
            // NOTE: SerpAPI does not give us the image size
            searchImageResults.push({
              type: 'image',
              thumbnailUrl: res.thumbnail,
              url: res.original,
              to_resize: true,
              fb_uploaded: false,
            })
          }
          se.currentOffset = offset
          se.prevOffset = Math.max(0, se.currentOffset - 1)
          se.nextOffset = se.currentOffset + 1
        } catch (error) {
          // Nothing
        }
      } else if (searchEngine === 'yandex') {
        try {
          let apiCallUrl = 'https://serpapi.com/search.json'
          let params = {
            api_key: se.apiKey,
            engine: 'yandex_images',
            text: query,
            yandex_domain: 'yandex.com',
            p: parseInt(offset),
            async: false,
          }
          const response = await this.$http.get(apiCallUrl, { params })
          for (let i = 0; i < response.data.images_results.length; i++) {
            let res = response.data.images_results[i]
            let width = parseInt(res.size.width)
            let height = parseInt(res.size.height)
            if (width < 700 || height < 700) {
              continue
            }
            searchImageResults.push({
              type: 'image',
              thumbnailUrl: res.thumbnail,
              url: res.original,
              to_resize: true,
              fb_uploaded: false,
            })
          }
          se.currentOffset = offset
          se.prevOffset = Math.max(0, se.currentOffset - 1)
          se.nextOffset = se.currentOffset + 1
        } catch (error) {
          // Nothing
        }
      }
      this.searchEngines[searchEngine] = se

      this.searchImageResults = searchImageResults

      this.$vs.loading.close()

      return this.searchImageResults
    },
    async searchImagesRelated(imageUrl) {
      this.$vs.loading()

      // TODO: We lose previous medias on a new search
      //this.ad.medias = []

      let searchImageResults = []

      let searchEngine = 'bing'
      this.searchEngine = searchEngine
      let se = this.searchEngines[searchEngine]
      if (searchEngine === 'bing') {
        try {
          let apiCallUrl = 'https://api.bing.microsoft.com/v7.0/images/visualsearch'
          let params = {
            imageInfo: {
              url : imageUrl,
            },
          }
          let headers = {
            'Ocp-Apim-Subscription-Key': se.apiKey,
          }
          const response = await this.$http.post(apiCallUrl, params, { headers })
          /*
          for (let i = 0; i < response.data.value.length; i++) {
            let res = response.data.value[i]
            searchImageResults.push({
              type: 'image',
              thumbnailUrl: res.thumbnailUrl,
              url: res.contentUrl,
            })
          }
          */
        } catch (error) {
          // Nothing
        }
      } else if (searchEngine === 'google') {
        try {
          let apiCallUrl = 'https://serpapi.com/search.json'
          let params = {
            api_key: se.apiKey,
            engine: 'google',
            q: query,
            google_domain: 'google.it', // TODO: .it or .com?
            tbm: 'isch',
            ijn: parseInt(offset),
            async: false,
          }
          const response = await this.$http.get(apiCallUrl, { params })
          for (let i = 0; i < response.data.images_results.length; i++) {
            let res = response.data.images_results[i]
            searchImageResults.push({
              type: 'image',
              thumbnailUrl: res.thumbnail,
              url: res.original,
            })
          }
          se.currentOffset = offset
          se.prevOffset = Math.max(0, se.currentOffset - 1)
          se.nextOffset = se.currentOffset + 1
        } catch (error) {
          // Nothing
        }
      }
      this.searchEngines[searchEngine] = se

      this.searchImageResults = searchImageResults

      this.$vs.loading.close()

      return this.searchImageResults
    },
    async fetchAdTemplates() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.adTemplates = []
      const response = await this.$http.get('/api/3rd/fb-ad-templates', { headers })
      this.adTemplates = response.data.data

      this.$vs.loading.close()

      return this.adTemplates
    },
    async saveAdTemplate(adTemplateName) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let value = this.getAdObject()

      let fullName = this.getTargetReference(this.target.ageMin, this.target.ageMax, this.target.genders, this.target.countries, this.target.devices, this.target.platforms)
      fullName += ' - ' + adTemplateName
      let data = {
        name: fullName,
        value: JSON.stringify(value),
      }

      let alreadyExists = false
      let adTemplateId = ''
      for (let i = 0; i < this.adTemplates.length; i++) {
        if (this.adTemplates[i].name == this.adTemplateName) {
          alreadyExists = true
          adTemplateId = this.adTemplates[i].id
        }
      }

      try {
        if (!alreadyExists) {
          const response = await this.$http.post('/api/3rd/fb-ad-templates', data, { headers })
        } else {
          const response = await this.$http.put('/api/3rd/fb-ad-templates/' + adTemplateId, data, { headers })
        }
        this.$vs.notify({
          title: 'Success',
          text: 'Ad template saved',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
        await this.fetchAdTemplates()
      } catch (error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Unknown error while saving ad template',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    async deleteAdTemplate(adTemplateId) {
      this.$vs.loading()

      let id = adTemplateId

      if (id <= 0) return

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      await this.$http.delete('/api/3rd/fb-ad-templates/' + id, { headers })

      this.$vs.loading.close()

      await this.fetchAdTemplates()
    },
    getAdObject() {
      return {
        product: this.product,
        campaign: this.campaign,
        adset: this.adset,
        ad: this.ad,
        target: this.target,
      }
    },
    loadAdTemplate(adTemplate) {
      for (let i = 0; i < this.products.length; i++) {
        if (adTemplate.value.product.id == this.products[i].id) {
          this.product = this.products[i]
        }
      }
      this.campaign = adTemplate.value.campaign
      this.onChangeMode()

      this.adset = adTemplate.value.adset
      this.ad = adTemplate.value.ad

      if (this.ad.hasOwnProperty('useExistingCopy') && this.ad.useExistingCopy) {
        for (let i = 0; i < this.copys.length; i++) {
          if (this.copys[i].number == this.ad.copyNumber) {
            this.ad.copy = this.copys[i]
            break
          }
        }
      }

      this.ad.medias = []
      for (let pmIndex = 0; pmIndex < adTemplate.value.medias; pmIndex++) {
        let media = adTemplate.value.medias[pmIndex]
        if (media !== null && media.hasOwnProperty('id')) {
          for (let i = 0; i < this.productMedias.length; i++) {
            if (this.productMedias[i].id == media.id) {
              this.ad.medias.push(this.productMedias[i])
            }
          }
        }
      }
      let preview = adTemplate.value.ad.preview
      if (preview !== null && preview.hasOwnProperty('id')) {
        for (let i = 0; i < this.productMedias.length; i++) {
          if (this.productMedias[i].id == preview.id) {
            this.ad.preview = this.productMedias[i]
            break
          }
        }
      }

      this.target = adTemplate.value.target
      this.newAdTemplateName = adTemplate.name
    },
    onChangeMode(value) {
      if (this.campaign.mode === 'split adset') {
        this.adset.count = this.ad.count
      } else if (this.campaign.mode === 'split creative' ) {
        this.adset.count = 1
      }
    },
    getRequiredNumberOfMedias() {
      if (this.campaign.mode === 'duplicate') {
        return parseInt(this.ad.count)
      } else if (this.campaign.mode === 'split adset') {
        return parseInt(this.ad.count)*parseInt(this.campaign.count)
      } else if (this.campaign.mode === 'split creative' ) {
        return parseInt(this.ad.count)*parseInt(this.campaign.count)
      }
      return 0
    },
    chooseMediasFromDropbox() {
      this.$vs.loading()

      let options = {
        success: async function(files) {
          for (let i = 0; i < files.length; i++) {
            let file = files[i]

            let ext = getFileExtension(file.name)
            let fileType = 'image'
            if (this.videoExtensions.includes(ext)) {
              fileType = 'video'
            }

            let mediaUrl = null
            let mediaHash = null
            let uploadedOnFacebook = false
            if (fileType == 'image') {
              // NOTE: Facebook does not accept images from DropBox (go wonder).
              // So we need to upload the dropbox image(s) temporarily on our server
              // and give our links to facebook.
              let imageBase64 = await this.resizeImageUrl(file.link)

              // TODO: Old code
              /*
              let imageFile = await createFileFromBase64DataURL(imageBase64)
              let uploadedImage = await this.createTemporaryImage(imageFile)
              mediaUrl = uploadedImage.url
              */

              const adImage = await this.facebook.uploadAdImage(this.adAccount.id, {
                bytes: this.base64RemovePrefix(imageBase64),
              })
              if (adImage !== null && adImage.hasOwnProperty('bytes') && adImage.bytes !== null) {
                if (adImage.bytes.hasOwnProperty('url') && adImage.bytes.url !== null) {
                  mediaUrl = adImage.bytes.url
                }
                if (adImage.bytes.hasOwnProperty('hash') && adImage.bytes.hash !== null) {
                  mediaHash = adImage.bytes.hash
                }
                uploadedOnFacebook = true
              }
            } else if (fileType == 'video') {
              mediaUrl = file.link
            }

            this.ad.medias.push({
              id: file.id,
              pathname: 'dropbox.com/' + file.name,
              url: mediaUrl,
              hash: mediaHash,
              fb_uploaded: uploadedOnFacebook,
              type: fileType,
            })
          }

          this.view = 'normal'
          this.$vs.loading.close()
        }.bind(this),
        cancel: function() {
          this.$vs.loading.close()
        }.bind(this),

        linkType: "direct", // or "direct"
        multiselect: true, // or true
        // NOTE: You may also specify file types, such as "video" or "images" in the list. For more information,
        // see File types below. By default, all extensions are allowed.
        extensions: ['images', 'video'],//['.png', '.jpg', '.jpeg'],
        folderselect: false,
      }
      this.$dropbox.choose(options)
    },
    chooseMediasFromLocal() {
      this.$refs.mediaUploadFileInput.click()
    },
    async onLocalMediasChosen(event) {
      this.$vs.loading()

      let files = event.target.files

      for (let i = 0; i < files.length; i++) {
        let imageUrl = null
        let imageHash = null
        let uploadedOnFacebook = false
        let fileType = 'image'

        let file = files[i]
        let fileName = file.name

        // TODO: Old code
        /*
        let uploadedImage = await this.createTemporaryImage(file)
        imageUrl = uploadedImage.url
        */

        let image = await createImageFromFile(file)
        let imageBase64 = await this.resizeImage(image)
        const adImage = await this.facebook.uploadAdImage(this.adAccount.id, {
          bytes: this.base64RemovePrefix(imageBase64),
        })
        if (adImage !== null && adImage.hasOwnProperty('bytes') && adImage.bytes !== null) {
          if (adImage.bytes.hasOwnProperty('url') && adImage.bytes.url !== null) {
            imageUrl = adImage.bytes.url
          }
          if (adImage.bytes.hasOwnProperty('hash') && adImage.bytes.hash !== null) {
            imageHash = adImage.bytes.hash
          }
          uploadedOnFacebook = true
        }

        this.ad.medias.push({
          id: 'local' + i,
          pathname: 'local/' + fileName,
          url: imageUrl,
          hash: imageHash,
          fb_uploaded: uploadedOnFacebook,
          type: fileType,
        })
      }

      this.$vs.loading.close()
    },
    async resizeImage(image) {
      let canvas = document.createElement('CANVAS');
      let newImageSize = parseInt(Math.min(image.width, image.height))
      canvas.width = newImageSize
      canvas.height = newImageSize
      if (canvas.width < 700) {
        canvas.width = 700
      }
      if (canvas.height < 700) {
        canvas.height = 700
      }

      let newImageSizeHalf = parseInt(newImageSize / 2)
      let halfWidth  = parseInt(image.width / 2)
      let halfHeight = parseInt(image.height / 2)

      // TODO: Also add option of stretching instead of only cropping?

      // NOTE: Point of interest should be the center because we can maximize
      // the image size (it must be square).
      let POI = {
        x: halfWidth,
        y: halfHeight,
      }

      let startX = POI.x - newImageSizeHalf
      let startY = POI.y - newImageSizeHalf

      const ctx = canvas.getContext('2d')
      ctx.drawImage(image, startX, startY, newImageSize, newImageSize, 0, 0, canvas.width, canvas.height)

      let resultBase64 = canvas.toDataURL()
      return resultBase64
    },
    async resizeImageUrl(url) {
      let image = await createImageFromUrl(url)
      let result = await this.resizeImage(image)
      return result
    },
    async createTemporaryImage(imageFile) {
      let temporaryImage = null

      try {
        let headers = {
          'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
          'Content-Type': 'multipart/form-data',
        }
        let formData = new FormData()
        formData.append('file', imageFile)
        const response = await this.$http.post('/api/v1/product-medias/temp', formData, { headers })
        temporaryImage = response.data.data
        temporaryImage.type = 'image'
      } catch (error) {
        // Nothing
      }

      return temporaryImage
    },
    base64RemovePrefix(base64Data) {
      let prefix = 'base64,'
      let prefixLength = prefix.length
      let prefixIndex = base64Data.indexOf(prefix)
      if (prefixIndex >= 0) {
        return base64Data.substr(prefixIndex + prefixLength)
      }
      return base64Data
    },
  },
  async created() {
    // TODO: What about visual search??
    //this.searchImagesRelated('https://cdn.webshopapp.com/shops/273775/files/310323894/image.jpg')

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

    let validAccessToken = await this.facebook.isAccessTokenValid()
    if (validAccessToken) {
      // Fetch ad accounts
      this.adAccounts = await this.facebook.fetchAdAccounts()

      // Fetch pages
      this.pages = await this.facebook.fetchPages()
      this.pages.filter(page => (page.hasOwnProperty('is_published') && page.is_published))
    } else {
      this.$vs.notify({
        title: 'Error',
        text: 'Invalid facebook access token',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'danger'
      })
    }

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

    await this.fetchAdTemplates()

    // TODO: to delete
    // DEBUG: This is only for debug
    if (this.debug) {
      this.product = this.products[3]
      this.page = this.pages[0]
      this.adAccount = this.adAccounts[2]
      this.onChangeAdAccount()
      this.campaign.name = 'test campaign'
      this.campaign.pixel = this.pixelOptions[0]
    }

    // Fetch settings
    try {
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      const response = await this.$http.get('/api/v1/settings', { headers })
      if (response !== null && response.hasOwnProperty('data') && response.data !== null &&
          response.data.hasOwnProperty('data') && response.data.data !== null) {
        this.settings = response.data.data
      }
    } catch (error) {
      // Nothing
    }
    if (this.settings.hasOwnProperty('bing') && this.settings.bing !== null &&
        this.settings.bing.hasOwnProperty('api_key') && this.settings.bing.api_key !== null) {
      this.searchEngines.bing.apiKey = this.settings.bing.api_key
    }
    if (this.settings.hasOwnProperty('serpapi') && this.settings.serpapi !== null &&
        this.settings.serpapi.hasOwnProperty('api_key') && this.settings.serpapi.api_key !== null) {
      this.searchEngines.google.apiKey = this.settings.serpapi.api_key
      this.searchEngines.yandex.apiKey = this.settings.serpapi.api_key
    }

    this.monitors = await this.facebook.fetchMonitors({ reports: 0 })
    this.monitorOptions = []
  },
}
</script>

<style>
.selected-media {
    border: 3px solid red;
}
</style>
