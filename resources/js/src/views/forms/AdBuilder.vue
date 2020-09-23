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
        <tab-content title="Ad Informations" class="mb-5" icon="feather icon-info" :before-change="validateStep1">
          <form data-vv-scope="step-1">
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select v-model="form" class="w-full select-large" label="Form" v-validate="'required'">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in formsOptions" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.form') }}</span>
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

            <div class="vx-col md:w-1/5 w-full" vs-justify="center" vs-align="center">
              <h3 class="mt-5 mb-4" vs-justify="center">Media</h3>
              <div class="vx-col w-full mt-5">
                <vs-select v-model="type" class="w-full select-large" label="Type" v-validate="'required'" @change="changeType">
                  <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in typesOptions" class="w-full" />
                </vs-select>
                <span class="text-danger">{{ errors.first('step-2.type') }}</span>
              </div>
            </div>
            <div class="vx-col md:w-4/5 w-full" vs-justify="center" vs-align="center">
            </div>

            <div class="vx-col md:w-1/5 w-full" vs-justify="center" vs-align="center">
              <vs-card class="cardx">
                <div slot="header">
                  <h3 class="mr-auto ml-auto text-center">
                    <span class="mr-auto ml-auto">{{ (fileToUpload == '') ? 'No file selected' : 'File selected' }}</span>
                  </h3>
                </div>
                <div slot="media" class="mb-3">
                  <img :src="uploadCardImage">
                </div>
                <div slot="footer">
                  <vs-row vs-justify="center">
                    <vs-button class="mr-2" color="primary" icon-pack="feather" icon="icon-file-plus" :disabled="fileToUpload != ''" @click="showFilePicker"></vs-button>
                    <vs-button class="mr-2" color="danger" icon-pack="feather" icon="icon-trash" :disabled="fileToUpload == ''" @click="clearFileToUpload"></vs-button>
                  </vs-row>
                </div>
              </vs-card>
              <div class="vx-row mb-6 flex items-center">
                <div class="vx-col w-full flex items-center">
                  <span class="ml-auto mr-auto text-danger">{{ errors.first('step-2.file') }}</span>
                </div>
                <div class="vx-col w-full flex items-center">
                  <span class="ml-auto mr-auto">Upload: {{uploadProgress}}%</span>
                </div>
                <div class="vx-col w-full">
                  <vs-progress :height="12" :percent="uploadProgress" color="success"></vs-progress>
                </div>
              </div>
              <input type="file" ref="fileUpload" class="hidden" @change="onFileUploadChange" :accept="type + '/*'">
            </div>

            <div class="vx-col md:w-1/5 w-full" vs-justify="center" vs-align="center" v-if="type == 'video'">
              <vs-card class="cardx">
                <div slot="header">
                  <h3 class="mr-auto ml-auto text-center">
                    <span class="mr-auto ml-auto">{{ (filePreviewToUpload == '') ? 'No preview' : 'Preview selected' }}</span>
                  </h3>
                </div>
                <div slot="media" class="mb-3">
                  <img :src="images['image']">
                </div>
                <div slot="footer">
                  <vs-row vs-justify="center">
                    <vs-button class="mr-2" color="primary" icon-pack="feather" icon="icon-file-plus" :disabled="filePreviewToUpload != ''" @click="showFilePicker('preview')"></vs-button>
                    <vs-button class="mr-2" color="danger" icon-pack="feather" icon="icon-trash" :disabled="filePreviewToUpload == ''" @click="clearFileToUpload('preview')"></vs-button>
                  </vs-row>
                </div>
              </vs-card>
              <div class="vx-row mb-6 flex items-center">
                <div class="vx-col w-full flex items-center">
                    <span class="ml-auto mr-auto">Upload: {{uploadPreviewProgress}}%</span>
                </div>
                <div class="vx-col w-full">
                    <vs-progress :height="12" :percent="uploadPreviewProgress" color="success"></vs-progress>
                </div>
              </div>
              <input type="file" ref="filePreviewUpload" class="hidden" @change="onFilePreviewUploadChange" accept="image/*">
            </div>
            <div class="vx-col md:w-1/5 w-full" vs-justify="center" vs-align="center" v-if="imagePreviewUrl != ''">
              <div class="mt-5 text-center">
                <label>Preview</label>
                <img class="w-full" :src="imagePreviewUrl" style="border: 1px solid gray">
              </div>
            </div>

          </div>
          </form>
        </tab-content>
      </form-wizard>
    </div>

    <!-- Create quantity option popup -->
    <vs-popup title="Create Quantity Option" :active.sync="showPopup['create']">
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Quantity</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create'].quantity" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Price</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create'].price" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Commission</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create'].commission" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Sub Commission</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create'].sub_commission" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <vs-button class="mt-12 mr-auto ml-auto" @click="createQuantityOption" color="primary" type="filled" :disabled="!isValidForm('create')">Add</vs-button>
        </div>
    </vs-popup>

    <!-- Edit quantity option popup -->
    <vs-popup title="Edit Quantity Option" :active.sync="showPopup['edit']">
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Quantity</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['edit'].quantity" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Price</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit'].price" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Commission</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit'].commission" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Sub Commission</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit'].sub_commission" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <vs-button class="mt-12 mr-auto ml-auto" @click="editQuantityOption" color="primary" type="filled" :disabled="!isValidForm('edit')">Update</vs-button>
        </div>
    </vs-popup>

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
    form: {
      required: 'Form is required',
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
      forms: [],
      ctr: 0.0,
      cpc: 0.0,
      cpa: 0.0,

      trafficSourcesOptions: [],
      formsOptions: [],
      typesOptions: ['image', 'video'],

      traffic_source: '',
      form: '',
      type: 'image',

      traffic_source_params: [],
      traffic_source_params_values: [],

      fileToUpload: '',
      isUploading: false,
      uploadProgress: 0,

      filePreviewToUpload: '',
      isUploadingPreview: false,
      uploadPreviewProgress: 0,

      images: {
        'image': 'https://www.iconfinder.com/data/icons/set-app-incredibles/24/Image-01-512.png',
        'video': 'https://icon-library.com/images/media-icon-png/media-icon-png-0.jpg',
      },
      imagePreviewUrl: '',

      params: {},

      showPopup: {
        'create': false,
        'edit': false,
      },
      newObject: {
        'create': {
          quantity: '',
          price: '',
          commission: '',
          sub_commission: '',
        },
        'edit': {
          quantity: '',
          price: '',
          commission: '',
          sub_commission: '',
        },
      },
      editingQuantityOptionIndex: -1,
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
    showForm(type) {
      this.showPopup[type] = true
    },
    clearForm(type) {
      this.newObject[type] = {
        quantity: '',
        price: '',
        commission: '',
        sub_commission: '',
      }
    },
    isValidForm(type) {
      let obj = this.newObject[type]
      return (obj.quantity != ''
        && obj.price != '' && obj.price >= 0
        && obj.commission != '' && obj.commission >= 0
        && obj.sub_commission != '' && obj.sub_commission >= 0)
    },
    showEditQuantityOptionForm(index) {
      let obj = this.quantityOptions[index]
      this.newObject['edit'] = {
        quantity: obj.quantity,
        price: obj.price,
        commission: obj.commission,
        sub_commission: obj.sub_commission,
      }
      this.editingQuantityOptionIndex = index
      this.showForm('edit')
    },
    editQuantityOption() {
      this.quantityOptions[this.editingQuantityOptionIndex] = this.newObject['edit']
      this.clearForm('edit')
      this.editingQuantityOptionIndex = -1
      this.showPopup['edit'] = false
    },
    removeQuantityOption(index) {
      if (index >= this.quantityOptions.length) return

      let defaultRemoved = this.quantityOptions[index].default
      this.quantityOptions = this.quantityOptions.splice(index, 1)
      if (defaultRemoved && this.quantityOptions.length > 0) {
        this.quantityOptions[0].default = true
      }
    },
    createQuantityOption() {
      this.quantityOptions.push({
        quantity: this.newObject['create'].quantity,
        price: this.newObject['create'].price,
        commission: this.newObject['create'].commission,
        sub_commission: this.newObject['create'].sub_commission,
      })
      this.showPopup['create'] = false
      this.clearForm('create')
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
          if (this.form === '' || this.form <= 0) {
            selectionResult = false
            this.errors.add({
              scope: 'step-1',
              field: 'form',
              msg: 'Form is required (if it doesn\'t exist just create one)'
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
      if (this.fileToUpload == '') {
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
          //'Content-Type': 'multipart/form-data',
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
              'Content-Type': 'multipart/form-data',
            }
            let config = {
              headers: headers,
              onUploadProgress: e => {
                if (e.lengthComputable) {
                  this.uploadProgress = (e.loaded / e.total) * 100
                  this.uploadPreviewProgress = (e.loaded / e.total) * 100
                }
              }
            }
            let formData = new FormData()
            formData.append('traffic_source_id', this.traffic_source)
            formData.append('form_id', this.form)
            formData.append('ctr', this.ctr)
            formData.append('cpc', this.cpc)
            formData.append('cpa', this.cpa)
            formData.append('info', JSON.stringify(this.params))
            formData.append('type', this.type)
            formData.append('file', this.fileToUpload)
            if (this.filePreviewToUpload != '') {
              formData.append('file_preview', this.filePreviewToUpload)
            }
            this.$http.post('/api/v1/ads/', formData, config)
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
      this.clearFileToUpload()
      this.clearFileToUpload('preview')
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
      return (this.fileToUpload != '')
    },
    clearFileToUpload(what = 'main') {
      if (what == 'preview') {
        this.filePreviewToUpload = ''
        this.imagePreviewUrl = ''
      } else {
        this.fileToUpload = ''
        this.filePreviewToUpload = ''
        this.imagePreviewUrl = ''
      }
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
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

    // Retrieve forms
    this.$vs.loading()
    this.$http.get('/api/v1/forms/', { headers })
      .then((response) => {
        this.forms = response.data.data
        this.forms.forEach((form) => {
          this.formsOptions.push({
            id: form.id,
            label: form.name + " - ID: " + form.id,
          })
          this.form = form.id;
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

  },
}
</script>
