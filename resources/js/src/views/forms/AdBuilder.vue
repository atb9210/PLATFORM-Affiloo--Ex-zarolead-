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
      <form-wizard color="rgba(var(--vs-primary), 1)" errorColor="rgba(var(--vs-danger), 1)" @on-change="beforeStep2" :title="null" :subtitle="null" finishButtonText="Create Ad">
        <!-- tab 1 content -->
        <tab-content title="Ad Informations" class="mb-5" icon="feather icon-info" :before-change="validateStep1">
          <form data-vv-scope="step-1">
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select v-model="product" class="w-full select-large" label="Product" v-validate="'required'" autocomplete>
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in productsOptions" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.product_id') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select v-model="traffic_source" class="w-full select-large" label="Traffic Source" v-validate="'required'" @change="changeTrafficSource">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in trafficSourcesOptions" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.traffic_source') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <vs-input class="w-full" icon-pack="feather" icon="icon-percent" icon-no-border type="number" step="0.01" label="CTR" v-model="ctr"/>
              <span class="text-danger">{{ errors.first('step-1.ctr') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <vs-input class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border type="number" step="0.01" label="CPC" v-model="cpc"/>
              <span class="text-danger">{{ errors.first('step-1.cpc') }}</span>
            </div>
            <div class="vx-col md:w-1/3 w-full mt-5">
              <vs-input class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border type="number" step="0.01" label="CPA" v-model="cpa"/>
              <span class="text-danger">{{ errors.first('step-1.cpa') }}</span>
            </div>
          </div>
          </form>
        </tab-content>

        <!-- tab 2 content -->
        <tab-content title="Ad Model" class="mb-5" icon="feather icon-image" :before-change="validateStep2">
          <form data-vv-scope="step-2">
          <div class="vx-row">
            <div class="vx-col w-full mt-4 mb-4">
              <span>(* Mandatory fields)</span>
            </div>
            <div class="vx-col w-full" v-for="(param, index) in traffic_source_params" :key="index">
              <vs-input
                class="w-full"
                icon-pack="feather" icon="icon-tag" icon-no-border
                :type="param.type" :label="param.name + (param.hasOwnProperty('required') ? '*' : '')"
                :name="param.name"
                v-model="traffic_source_params_values[index]"
              />
              <span class="text-danger">{{ errors.first('step-2.' + param.name) }}</span>
            </div>

            <div class="vx-col w-full mb-2" vs-justify="center" vs-align="center">
              <h3 class="mt-5 mb-4" vs-justify="center">Product Media</h3>
            </div>

            <div class="vx-col md:w-1/5 w-full mb-2" vs-justify="center" vs-align="center">
              <vs-select v-model="type" class="w-full select-large" label="Type" v-validate="'required'" @change="changeType">
                <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in typesOptions" class="w-full" />
              </vs-select>
            </div>

            <div class="w-full flex">
              <span class="text-danger">{{ errors.first('step-2.product_media_id') }}</span>
            </div>
            <div class="w-full flex vx-row">
              <div class="vx-col md:w-1/6 w-full pb-2 pr-2" v-for="(object, index) in productMediasOptions.filter(filterByType)" :key="index">
                <vs-card class="cardx" :class="(object.id == productMedia) ? 'selected-product-media' : ''" @click.native="selectProductMedia(object.id)">
                  <div slot="media">
                    <img :src="(object.type == 'image') ? object.url_preview : videoThumbnail">
                  </div>
                  <div>
                    <span>{{object.pathname}}</span>
                  </div>
                </vs-card>
              </div>
            </div>

            <div class="vx-col w-full mb-2" vs-justify="center" vs-align="center" v-if="type == 'video'">
              <h3 class="mt-5 mb-4" vs-justify="center">Preview</h3>
            </div>
            <div class="w-full flex vx-row" v-if="type == 'video'">
              <div class="vx-col md:w-1/6 w-full pb-2 pr-2" v-for="(object, index) in productMediasOptions.filter(filterOnlyImages)" :key="index">
                <vs-card class="cardx" :class="(object.id == productMediaPreview) ? 'selected-product-media' : ''" @click.native="selectProductMediaPreview(object.id)">
                  <div slot="media">
                    <img :src="object.url_preview">
                  </div>
                  <div>
                    <span>{{object.pathname}}</span>
                  </div>
                </vs-card>
              </div>
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
      traffic_sources: [],
      products: [],

      trafficSourcesOptions: [],
      productsOptions: [],
      productMediasOptions: [],
      typesOptions: ['image', 'video'],
      type: 'image',

      traffic_source: '',
      product: '',
      ctr: 0.0,
      cpc: 0.0,
      cpa: 0.0,
      productMedia: '',
      productMediaPreview: '',
      params: {},

      traffic_source_params: [],
      traffic_source_params_values: [],

      videoThumbnail: 'data:image/png;base64,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',
    }
  },
  computed: {
    canChangeUser() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin'))
    },
    uploadCardImage() {
      return this.images[this.type]
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
    changeTrafficSource() {
      this.traffic_source_params = []
      this.traffic_source_params_values = []
      if (this.traffic_source != '' && this.traffic_source > 0) {
        let index = this.getObjectIndexById(this.traffic_source, this.traffic_sources)
        if (index != -1 && this.traffic_sources[index].ad_model.hasOwnProperty('params')) {
          for (const [key, value] of Object.entries(this.traffic_sources[index].ad_model.params)) {
            this.traffic_source_params.push(value)
            this.traffic_source_params[this.traffic_source_params.length - 1].name = key
            this.traffic_source_params[this.traffic_source_params.length - 1].validation = this.traffic_sources[index].ad_model.validation.params[key];
            this.traffic_source_params_values.push('')
            this.traffic_source_params[this.traffic_source_params.length - 1].value = ''
          }
        }
      }
    },
    validateStep1 () {
      return new Promise((resolve, reject) => {
        this.$validator.validateAll('step-1').then(result => {
          let selectionResult = true
          if (this.traffic_source === '' || this.traffic_source <= 0) {
            selectionResult = false
            this.errors.add({
              scope: 'step-1',
              field: 'traffic_source',
              msg: 'Traffic Source is required (if it doesn\'t exist just create one)'
            })
          }
          if (this.product === '' || this.product <= 0) {
            selectionResult = false
            this.errors.add({
              scope: 'step-1',
              field: 'product',
              msg: 'Product is required (if it doesn\'t exist just create one)'
            })
          }
          if (result && selectionResult) {
            resolve(true)
          } else {
            reject('correct all values')
          }
        })
      })
    },
    beforeStep2(prevIndex, nextIndex) {
      if (nextIndex != 1) return

      // Retrieve product medias
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        filter_product: this.product,
      }
      this.$vs.loading()
      this.$http.get('/api/v1/product-medias/', { headers, params: data })
        .then((response) => {
          this.productMediasOptions = []
          response.data.data.forEach((product_media) => {
            this.productMediasOptions.push({
              id: product_media.id,
              type: product_media.type,
              pathname: product_media.pathname,
              url: product_media.url,
              url_preview: product_media.url_preview,
            })
            this.productMedia = product_media.id;
          })
          this.$vs.loading.close()
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    },
    validateStep2 () {
      let isValid = true
      if (this.productMedia <= 0 || this.productMedia == '') {
        isValid = false
        this.errors.add({
          scope: 'step-2',
          field: 'product_media_id',
          msg: 'You must chose a product media'
        })
      }

      if(!isValid) {
        return new Promise((resolve, reject) => {
          reject('correct all values')
        })
      }

      return new Promise((resolve, reject) => {
        this.$vs.loading()

        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken'),
        }

        let data = {
          params: {
          }
        }
        for (let i = 0; i < this.traffic_source_params.length; i++) {
          data.params[this.traffic_source_params[i].name] = this.traffic_source_params_values[i]
        }
        this.params = data.params
        data.params = JSON.stringify(data.params)

        // Validate network params
        this.$http.post('/api/v1/traffic-sources/' + this.traffic_source + '/ad-model/validate', data, { headers: headers })
          .then((response) => {
            this.$vs.loading.close()

            // Create Ad
            this.$vs.loading()
            let headers = {
              'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
            }
            let data = {}
            data['traffic_source_id'] = this.traffic_source
            data['product_id'] = this.product
            data['ctr'] = this.ctr
            data['cpc'] = this.cpc
            data['cpa'] = this.cpa
            data['info'] = JSON.stringify(this.params)
            data['product_media_id'] = this.productMedia
            if (this.productMediaPreview != '') {
                data['product_media_preview_id'] = this.productMediaPreview
            }
            this.$http.post('/api/v1/ads', data, { headers: headers })
              .then((response) => {
                this.$vs.loading.close()
                this.$vs.notify({
                  title: 'Success',
                  text: 'Ad created',
                  iconPack: 'feather',
                  icon: 'icon-alert-circle',
                  color: 'success'
                })
                this.$router.push('/dashboard/ads')
                resolve(true)
              })
              .catch((error) => {
                this.$vs.loading.close()
                this.errors.clear()
                for (const [key, value] of Object.entries(error.response.data.errors)) {
                  for (let i = 0; i < value.length; i++) {
                    this.errors.add({
                      scope: 'step-2',
                      field: key,
                      msg: value[i]
                    })
                  }
                }
                reject('correct all values')
              })
          })
          .catch((error) => {
            this.$vs.loading.close()
            this.errors.clear()
            for (const [key, value] of Object.entries(error.response.data.errors)) {
              for (let i = 0; i < value.length; i++) {
                this.errors.add({
                  scope: 'step-2',
                  field: key,
                  msg: value[i]
                })
              }
            }
            reject('correct all values')
          })
        /*
        this.$validator.validateAll('step-3').then(result => {
          if (result) {
            this.createForm()
            resolve(true)
          } else {
            reject('correct all values')
          }
        })
        */
      })
    },
    changeType() {
      this.productMedia = ''
    },
    showFilePicker(what = 'main') {
      if (what == 'preview') {
        this.$refs.filePreviewUpload.click()
      } else {
        this.$refs.fileUpload.click()
      }
    },
    onFileUploadChange(event) {
      this.fileToUpload = event.target.files[0]
      if (this.type == 'image') {
        this.imagePreviewUrl = URL.createObjectURL(this.fileToUpload)
      }
    },
    onFilePreviewUploadChange(event) {
      this.filePreviewToUpload = event.target.files[0]
      this.imagePreviewUrl = URL.createObjectURL(this.filePreviewToUpload)
    },
    selectProductMedia(id) {
      this.productMedia = id
    },
    selectProductMediaPreview(id) {
      this.productMediaPreview = id
    },
    filterByType(value) {
      return (value.type == this.type)
    },
    filterOnlyImages(value) {
      return (value.type == 'image')
    },
  },
  components: {
    FormWizard,
    TabContent,
  },
  created() {
    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve traffic sources
    this.$vs.loading()
    this.$http.get('/api/v1/traffic-sources/', { headers })
      .then((response) => {
        this.traffic_sources = response.data.data
        this.traffic_sources.forEach((traffic_source) => {
          this.trafficSourcesOptions.push({
            id: traffic_source.id,
            label: traffic_source.name,
          })
          this.traffic_source = traffic_source.id;
        })

        // Retrieve products
        this.$vs.loading()
        this.$http.get('/api/v1/products/', { headers })
          .then((response) => {
            this.products = response.data.data
            this.products.forEach((product) => {
              this.productsOptions.push({
                id: product.id,
                label: product.name + " - ID: " + product.id,
              })
              this.product = product.id;
            })
            this.$vs.loading.close()
          })
          .catch((error) => {
            this.$vs.loading.close()
          })
      })
      .catch((error) => {
        this.$vs.loading.close()
      })
  },
}
</script>

<style>
.selected-product-media {
    border: 3px solid red;
}
</style>
