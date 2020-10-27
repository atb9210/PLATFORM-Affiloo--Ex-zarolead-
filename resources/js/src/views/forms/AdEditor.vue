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
      <form-wizard color="rgba(var(--vs-primary), 1)" errorColor="rgba(var(--vs-danger), 1)" @on-change="beforeStep2" :title="null" :subtitle="null" finishButtonText="Update Ad">
        <!-- tab 1 content -->
        <tab-content title="Ad Informations" class="mb-5" icon="feather icon-info" :before-change="validateStep1">
          <form data-vv-scope="step-1">
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select v-model="product" class="w-full select-large" label="Product" v-validate="'required'">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in productsOptions" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.product') }}</span>
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
        <!--
        <tab-content title="Quantity & Upsell" class="mb-5" icon="feather icon-dollar-sign" :before-change="validateStep2">
          <form data-vv-scope="step-2">
          <div class="vx-row">
            <vs-list class="vx-col w-full mt-5">
              <vs-list-header icon-pack="feather" icon="icon-hash" title="Quantity Options" ></vs-list-header>
              <vs-list-item
                icon-pack="feather" icon="icon-check"
                :title="'Quantity: ' + quantityOption.quantity"
                :subtitle="'Price: €' + quantityOption.price + ', Commission: €' + quantityOption.commission + ', Sub Commission: €' + quantityOption.sub_commission"
                v-for="(quantityOption, index) in quantityOptions" :key="index">
                <div class="flex items-center">
                  <vs-radio class="mr-4" v-model="quantityDefaultIndex" :vs-value="index" @change="changeQuantityDefault">Default</vs-radio>
                  <vs-button class="mr-2" icon-pack="feather" icon="icon-edit" type="filled" color="primary" @click="showEditQuantityOptionForm(index)"></vs-button>
                  <vs-button class="mr-2" icon-pack="feather" icon="icon-trash" type="filled" color="danger" @click="removeQuantityOption(index)"></vs-button>
                </div>
              </vs-list-item>
            </vs-list>
            <div class="vx-col w-full mt-2">
              <vs-button class="mt-3" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create')">Add option</vs-button> 
            </div>
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Upsell</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="upsell"></vs-checkbox>
                  </div>
                </template>
                <vs-select v-model="upsellQuantity" class="w-full">
                  <vs-select-item :key="index" :value="item.quantity" :text="item.quantity" v-for="(item,index) in quantityOptions" class="w-full" />
                </vs-select>
              </vx-input-group>
            </div>
          </div>
          </form>
        </tab-content>
        -->

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
              <vs-select v-model="type" class="w-full select-large" label="Type" v-validate="'required'">
                <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in typesOptions" class="w-full" />
              </vs-select>
            </div>

            <div class="images-grid w-full">
              <span class="text-danger">{{ errors.first('step-2.product_media_id') }}</span>
              <vs-images>
                <vs-image v-if="object.type == type" :src="object.file_url_preview" :key="index" v-for="(object, index) in productMediasOptions" @click.native="selectProductMedia(object.id)" :class="(object.id == productMedia) ? 'selected-product-media' : ''" />
              </vs-images>
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
  props: {
    ad_id: Number,
  },
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
      productMedia: 0,
      params: {},

      traffic_source_params: [],
      traffic_source_params_values: [],
    }
  },
  computed: {
    canChangeUser() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin'))
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
            let tmp = ''
            if (this.ad.info.hasOwnProperty(key)) {
              tmp = this.ad.info[key]
            }
            this.traffic_source_params_values.push(tmp)
            this.traffic_source_params[this.traffic_source_params.length - 1].value = tmp
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
              file_url_preview: product_media.file_url_preview,
            })
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
      if (!this.typesOptions.includes(this.type)) {
        isValid = false
        this.errors.add({
          scope: 'step-2',
          field: 'type',
          msg: 'Type is invalid'
        })
      }
      if (this.type != this.ad.type && this.fileToUpload == '') {
        isValid = false
        this.errors.add({
          scope: 'step-2',
          field: 'file',
          msg: 'File is required'
        })
      }

      isValid = isValid && this.isValidUpload()

      if(!isValid) {
        return new Promise((resolve, reject) => {
          reject('correct all values')
        })
      }

      return new Promise((resolve, reject) => {
        this.$vs.loading()

        let headers = {
          'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
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

            // Edit Ad
            this.$vs.loading()
            let headers = {
              Authorization: 'Bearer ' + localStorage.getItem('accessToken'),
            }

            data['traffic_source_id'] = this.traffic_source
            data['product_id'] = this.product
            data['ctr'] = this.ctr
            data['cpc'] = this.cpc
            data['cpa'] = this.cpa
            data['info'] = JSON.stringify(this.params)
            data['product_media_id'] = this.productMedia
            this.$http.put('/api/v1/ads/' + this.ad_id, data, { headers: headers })
              .then((response) => {
                this.$vs.loading.close()
                this.$vs.notify({
                  title: 'Success',
                  text: 'Ad updated',
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
      this.clearFileToUpload()
      this.clearFileToUpload('preview')
      this.errors.remove('type', 'step-2')
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
    isValidUpload() {
      return true
    },
    clearFileToUpload(what = 'main') {
      if (what == 'preview') {
        this.filePreviewToUpload = ''
        if (this.ad.type == this.type) {
          this.imagePreviewUrl = this.imagePreviewUrlOld
        } else {
          this.imagePreviewUrl = ''
        }
      } else {
        this.fileToUpload = ''
        this.filePreviewToUpload = ''
        if (this.ad.type == this.type) {
          this.imagePreviewUrl = this.imagePreviewUrlOld
        } else {
          this.imagePreviewUrl = ''
        }
      }
    },
    selectProductMedia(id) {
      this.productMedia = id
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

    // Retrieve ad
    this.$vs.loading()
    this.$http.get('/api/v1/ads/' + this.ad_id, { headers })
      .then((response) => {
        this.ad = response.data.data

        this.ctr = this.ad.ctr
        this.cpc = this.ad.cpc
        this.cpa = this.ad.cpa

        this.type = this.ad.product_media.type
        this.productMedia = this.ad.product_media.id

        // Retrieve traffic sources
        this.$http.get('/api/v1/traffic-sources/', { headers })
          .then((response) => {
            this.traffic_sources = response.data.data
            for (let i = 0; i < this.traffic_sources.length; i++) {
              let traffic_source = this.traffic_sources[i]
              this.trafficSourcesOptions.push({
                id: traffic_source.id,
                label: traffic_source.name,
              })
              if (traffic_source.id == this.ad.traffic_source.id) {
                this.traffic_source = traffic_source.id;
              }
            }
            this.changeTrafficSource()

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
                  if (product.id == this.ad.product.id) {
                    this.product = product.id;
                  }
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
