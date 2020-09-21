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
      <form-wizard color="rgba(var(--vs-primary), 1)" errorColor="rgba(var(--vs-danger), 1)" :title="null" :subtitle="null" finishButtonText="Create Form">
        <!-- tab 1 content -->
        <tab-content title="Form Informations" class="mb-5" icon="feather icon-info" :before-change="validateStep1">
          <form data-vv-scope="step-1">
          <div class="vx-row">
            <div class="vx-col md:w-full w-full mt-5">
              <vs-input icon-pack="feather" icon="icon-tag" icon-no-border label="Name" v-model="name" class="w-full" name="name" v-validate="'required'" />
              <span class="text-danger">{{ errors.first('step-1.name') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select v-model="campaign" class="w-full select-large" label="Campaign" v-validate="'required'">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in campaigns" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.campaign') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select v-model="product" class="w-full select-large" label="Product" v-validate="'required'">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in products" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.product') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select v-model="network" class="w-full select-large" label="Network" v-validate="'required'" @change="changeNetwork">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in networksOptions" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.network') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select v-model="traffic_source" class="w-full select-large" label="Traffic Source" v-validate="'required'">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in traffic_sources" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.traffic_source') }}</span>
            </div>
            <div class="vx-col w-full mt-5" v-if="canChangeUser">
              <vs-select v-model="user" class="w-full select-large" label="User" v-validate="'required'">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in users" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.user') }}</span>
            </div>
          </div>
          </form>
        </tab-content>

        <!-- tab 2 content -->
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

        <!-- tab 3 content -->
        <tab-content title="Network API" class="mb-5" icon="feather icon-settings" :before-change="validateStep3">
          <form data-vv-scope="step-3">
          <div class="vx-row">
            <div class="vx-col w-full" v-for="(param, index) in network_params" :key="index">
              <vs-input
                class="w-full"
                icon-pack="feather" icon="icon-tag" icon-no-border
                :type="param.type" :label="param.name + (param.hasOwnProperty('required') ? '*' : '')"
                :name="param.name"
                v-model="network_params_values[index]"
              />
              <span class="text-danger">{{ errors.first('step-3.' + param.name) }}</span>
            </div>
            <div class="vx-col w-full mt-4">
              <span>* Mandatory fields</span>
            </div>
          </div>
          </form>
        </tab-content>

        <!-- tab 4 content -->
        <tab-content title="Plugins" class="mb-5" icon="feather icon-plus-square" :before-change="validateStep4">
          <form data-vv-scope="step-4">
          <div class="vx-row">
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Thank You Page URL Redirect</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="plugins['thankyou_redirect'].enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-input
                  type="url"
                  class="w-full"
                  icon-pack="feather" icon="icon-facebook" icon-no-border
                  v-model="plugins['thankyou_redirect'].url"
                  placeholder="http://..."
                />
              </vx-input-group>
            </div>
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Facebook Pixel</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="plugins['facebook'].enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-input
                  class="w-full"
                  icon-pack="feather" icon="icon-facebook" icon-no-border
                  v-model="plugins['facebook'].pixel"
                  name="fb_pixel" placeholder="Facebook pixel"
                />
              </vx-input-group>
            </div>
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Zeus Track</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="plugins['zeustrack'].enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-input
                  class="w-full"
                  icon-pack="feather" icon="icon-at-sign" icon-no-border
                  v-model="plugins['zeustrack'].sub_id"
                  placeholder="Sub ID"
                />
              </vx-input-group>
            </div>
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Double Recovery</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="plugins['double_recovery'].enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-select v-model="plugins['double_recovery'].form_id" class="w-full select-large" v-validate="'required'">
                  <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in formsOptions" class="w-full" />
                </vs-select>
              </vx-input-group>
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
    user: {
      required: 'User is required',
    },
    name: {
      required: 'Name is required',
    },
    campaign: {
      required: 'Campaign is required',
    },
    network: {
      required: 'Network is required',
    },
    product: {
      required: 'Product is required',
    },
    traffic_source: {
      required: 'Traffic Source is required',
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
      users: [],
      campaigns: [],
      networks: [],
      forms: [],
      products: [],
      traffic_sources: [],

      networksOptions: [],
      formsOptions: [],
      network_params: [],
      network_params_values: [],

      user: '',
      name: '',
      campaign: '',
      network: '',
      product: '',
      traffic_source: '',
      quantityOptions: [
          {
            'quantity': '1',
            'price': 99.99,
            'commission': 99.99,
            'sub_commission': 99.99,
            'default': true,
          },
      ],
      quantityDefaultIndex: 0,

      upsell: false,
      upsellQuantity: '1',

      plugins: {
        'thankyou_redirect': {
          enabled: false,
          url: '',
        },
        'facebook': {
          enabled: false,
          pixel: '',
        },
        'zeustrack': {
          enabled: false,
          sub_id: '',
        },
        'double_recovery': {
          enabled: false,
          form_id: 0,
        },
      },

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
    changeNetwork() {
      this.network_params = []
      this.network_params_values = []
      if (this.network != '' && this.network > 0) {
        let index = this.getObjectIndexById(this.network, this.networks)
        if (index != -1 && this.networks[index].api.hasOwnProperty('form')
            && this.networks[index].api.form.hasOwnProperty('params')) {
          for (const [key, value] of Object.entries(this.networks[index].api.form.params)) {
            this.network_params.push(value)
            this.network_params[this.network_params.length - 1].name = key
            this.network_params[this.network_params.length - 1].validation = this.networks[index].api.form.validation.params[key];
            this.network_params_values.push('')
            this.network_params[this.network_params.length - 1].value = ''
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
    changeQuantityDefault() {
        console.log(this.quantityDefaultIndex)
      for (let i = 0; i < this.quantityOptions.length; i++) {
        if (i == this.quantityDefaultIndex) {
          this.quantityOptions[i].default = true
        } else {
          this.quantityOptions[i].default = false
        }
      }
    },
    validateStep1 () {
      return new Promise((resolve, reject) => {
        this.$validator.validateAll('step-1').then(result => {
          let selectionResult = true
          if (this.canChangeUser) {
            if (this.user === '' || this.user <= 0) {
              selectionResult = false
              this.errors.add({
                scope: 'step-1',
                field: 'user',
                msg: 'User is required (if it doesn\'t exist just create one)'
              })
            }
          }
          if (this.campaign === '' || this.campaign <= 0) {
            selectionResult = false
            this.errors.add({
              scope: 'step-1',
              field: 'campaign',
              msg: 'Campaign is required (if it doesn\'t exist just create one)'
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
          if (this.network === '' || this.network <= 0) {
            selectionResult = false
            this.errors.add({
              scope: 'step-1',
              field: 'network',
              msg: 'Network is required (if it doesn\'t exist just create one)'
            })
          }
          if (this.traffic_source === '' || this.traffic_source <= 0) {
            selectionResult = false
            this.errors.add({
              scope: 'step-1',
              field: 'traffic_source',
              msg: 'Traffic Source is required (if it doesn\'t exist just create one)'
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
      return new Promise((resolve, reject) => {
        this.$validator.validateAll('step-2').then(result => {
          if (result) {
            resolve(true)
          } else {
            reject('correct all values')
          }
        })
      })
    },
    validateStep3 () {
      return new Promise((resolve, reject) => {
        this.$vs.loading()

        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }

        let data = {
          network_params: {
          }
        }
        for (let i = 0; i < this.network_params.length; i++) {
          data.network_params[this.network_params[i].name] = this.network_params_values[i]
        }
        data.network_params = JSON.stringify(data.network_params)

        // Validate network params
        this.$http.post('/api/v1/networks/' + this.network + '/params/validate', data, { headers: headers })
          .then((response) => {
            this.$vs.loading.close()
            resolve(true)
          })
          .catch((error) => {
            this.$vs.loading.close()
            this.errors.clear()
            for (const [key, value] of Object.entries(error.response.data.errors)) {
              for (let i = 0; i < value.length; i++) {
                this.errors.add({
                  scope: 'step-3',
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
    validateStep4 () {
      return new Promise((resolve, reject) => {
        this.$vs.loading()
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }

        let data = {
          name: this.name,
          campaign_id: this.campaign,
          network_id: this.network,
          product_id: this.product,
          traffic_source_id: this.traffic_source,
          quantity: {
            options: {},
            default: '',
          },
          upsell: {
            enabled: this.upsell,
            quantity: this.upsellQuantity,
          },
          network_params: {},
          plugins: {},
        }
        if (this.canChangeUser) {
          if (!(this.user === '' || this.user <= 0)) {
            data.user_id = this.user
          }
        }
        this.quantityOptions.forEach((opt) => {
          data.quantity['options'][opt.quantity] = {
            price: opt.price,
            commission: opt.commission,
            sub_commission: opt.sub_commission,
          }
        })
        data.quantity['default'] = this.quantityOptions[this.quantityDefaultIndex].quantity
        for (let i = 0; i < this.network_params.length; i++) {
          data.network_params[this.network_params[i].name] = this.network_params_values[i]
        }
        data.plugins = this.plugins

        data.quantity = JSON.stringify(data.quantity)
        data.upsell = JSON.stringify(data.upsell)
        data.network_params = JSON.stringify(data.network_params)
        data.plugins = JSON.stringify(data.plugins)

        console.log(data)

        // Create form
        this.$http.post('/api/v1/forms', data, { headers: headers })
          .then((response) => {
            this.$vs.loading.close()
            this.$vs.notify({
              title: 'Success',
              text: 'Form created',
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'success'
            })
            this.$router.push('/dashboard/forms')
            resolve(true)
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
            reject('correct all values')
          })
      })
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

    // Retrieve users
    this.$vs.loading()
    this.$http.get('/api/v1/users/', { headers })
      .then((response) => {
        this.traffic_sources = []
        response.data.data.forEach((user) => {
          this.users.push({
            id: user.id,
            label: user.email,
          })
          this.user = user.id;
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

    // Retrieve traffic sources
    this.$vs.loading()
    this.$http.get('/api/v1/traffic-sources/', { headers })
      .then((response) => {
        this.traffic_sources = []
        response.data.data.forEach((traffic_source) => {
          this.traffic_sources.push({
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

    // Retrieve products
    this.$vs.loading()
    this.$http.get('/api/v1/products/', { headers })
      .then((response) => {
        this.products = []
        response.data.data.forEach((product) => {
          this.products.push({
            id: product.id,
            label: product.name,
          })
          this.product = product.id;
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

    // Retrieve networks
    this.$vs.loading()
    this.$http.get('/api/v1/networks/', { headers })
      .then((response) => {
        this.networks = response.data.data
        this.networks.forEach((network) => {
          this.networksOptions.push({
            id: network.id,
            label: network.name,
          })
          this.network = network.id;
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

    // Retrieve campaigns
    this.$vs.loading()
    this.$http.get('/api/v1/campaigns/', { headers })
      .then((response) => {
        this.campaigns = []
        response.data.data.forEach((campaign) => {
          this.campaigns.push({
            id: campaign.id,
            label: campaign.name,
          })
          this.campaign = campaign.id;
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
          this.plugins['double_recovery'].form_id = form.id;
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

  },
}
</script>
