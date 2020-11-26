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
      <form-wizard color="rgba(var(--vs-primary), 1)" errorColor="rgba(var(--vs-danger), 1)" :title="null" :subtitle="null" finishButtonText="Apply Changes">
        <!-- tab 1 content -->
        <tab-content title="Form Informations" class="mb-5" icon="feather icon-info" :before-change="validateStep1">
          <form data-vv-scope="step-1">
          <div class="vx-row">
            <div class="vx-col md:w-full w-full mt-5">
              <vs-input icon-pack="feather" icon="icon-tag" icon-no-border label="Name" v-model="name" class="w-full" name="name" v-validate="'required'" />
              <span class="text-danger">{{ errors.first('step-1.name') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select v-model="product" class="w-full select-large" label="Product" v-validate="'required'">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in products" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.product') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select v-model="network" class="w-full select-large" label="Network" v-validate="'required'" @change="changeNetwork" disabled="true">
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
            <div class="vx-col md:w-1/2 w-full mt-5" v-if="canChangeUser">
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
                    <vs-checkbox v-model="upSell"></vs-checkbox>
                  </div>
                </template>
                <vs-select v-model="upSellBestSeller" class="w-full">
                  <vs-select-item :key="index" :value="item.quantity" :text="item.quantity" v-for="(item,index) in quantityOptions" class="w-full" />
                </vs-select>
              </vx-input-group>
            </div>
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Cross sell</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="crossSell.enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-input @click="showCrossSellForm" value="Click here to update" class="w-full" icon-pack="feather" icon="icon-globe" icon-no-border />
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
                  icon-pack="feather" icon="icon-globe" icon-no-border
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
                <vs-select v-model="plugins['facebook'].eventName" class="w-full select-large">
                  <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in facebookEventNameOptions" class="w-full" />
                </vs-select>
                <vs-input
                  type="number"
                  step="0.01"
                  min="0"
                  class="w-full"
                  icon-pack="feather" icon="icon-dollar-sign" icon-no-border
                  v-model="plugins['facebook'].eventValue"
                  name="fb_event_value" placeholder="Conversion event value (optional)"
                />
                <vs-select v-model="plugins['facebook'].eventCurrency" class="w-full select-large">
                  <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in facebookEventCurrencyOptions" class="w-full" />
                </vs-select>
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
                <vs-select v-model="plugins['double_recovery'].form_id" class="w-full select-large" v-validate="'required'" @change="changeNetwork">
                  <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in formsOptions" class="w-full" />
                </vs-select>
              </vx-input-group>
            </div>
            <div class="vx-col w-full mt-5">
              <div class="mb-2">CSS Style Template</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="plugins['css_style'].enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-select autocomplete v-model="plugins['css_style'].stylesheet_url" class="w-full select-large" v-validate="'required'">
                  <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in formsStylesOptions" class="w-full" />
                </vs-select>
                <template slot="append">
                    <div class="append-text bg-primary" @click="showForm('create_forms_style')">
                        <span><vs-icon icon-pack="feather" icon="icon-plus"></vs-icon></span>
                    </div>
                </template>
              </vx-input-group>
            </div>
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Custom (after submit, on success) wrapped into &lt;script&gt; tags</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="plugins['custom'].enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-textarea v-model="plugins['custom'].afterSubmit"/>
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
            <div class="vx-col sm:w-1/3 w-full">
                <span>Old price</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create'].old_price" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Discount %</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create'].discount" />
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
            <div class="vx-col sm:w-1/3 w-full">
                <span>Old price</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit'].old_price" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Discount %</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit'].discount" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <vs-button class="mt-12 mr-auto ml-auto" @click="editQuantityOption" color="primary" type="filled" :disabled="!isValidForm('edit')">Update</vs-button>
        </div>
    </vs-popup>

    <!-- Create forms style popup -->
    <vs-popup title="Create forms style" :active.sync="showPopup['create_forms_style']">
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Name</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create_forms_style'].name" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>CSS Code</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-textarea v-model="newObject['create_forms_style'].code"/>
            </div>
        </div>
        
        <div class="vx-row mb-6">
            <vs-button class="mt-12 mr-auto ml-auto" @click="createFormsStyle" color="primary" type="filled" :disabled="!isValidForm('create_forms_style')">Create</vs-button>
        </div>
    </vs-popup>

    <!-- Cross sell popup -->
    <vs-popup title="cross sell" :active.sync="showPopup['cross_sell']">
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Title</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['cross_sell'].title" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Description</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['cross_sell'].description" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Image URL</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="url" class="w-full" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['cross_sell'].img" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Old price</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['cross_sell'].old_price" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Price</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['cross_sell'].price" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Commission</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['cross_sell'].commission" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Sub commission</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['cross_sell'].sub_commission" />
            </div>
        </div>
        <div class="vx-row mb-6">
            <vs-button class="mt-12 mr-auto ml-auto" @click="setCrossSell" color="primary" type="filled" :disabled="!isValidForm('cross_sell')">Update</vs-button>
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
  props: {
    form_id: Number,
  },
  data () {
    return {
      form: [],

      users: [],
      networks: [],
      forms: [],
      products: [],
      traffic_sources: [],
      formsStyles: [],

      networksOptions: [],
      formsOptions: [],
      formsStylesOptions: [],
      network_params: [],
      network_params_values: [],

      user: '',
      name: '',
      network: '',
      product: '',
      traffic_source: '',
      quantityOptions: [
        /*
        {
          'quantity': '1',
          'price': 99.99,
          'commission': 99.99,
          'sub_commission': 99.99,
          'default': true,
        },
        */
      ],
      quantityDefaultIndex: 0,

      upSell: false,
      upSellBestSeller: '1',
      
      crossSell: {
        enabled: false,
        title: '',
        description: '',
        img: '',
        old_price: '',
        price: '',
        commission: '',
        sub_commission: '',
      },

      plugins: {
        'thankyou_redirect': {
          enabled: false,
          url: '',
        },
        'facebook': {
          enabled: false,
          pixel: '',
          eventName: 'Purchase',
          eventValue: '',
          eventCurrency: 'EUR',
        },
        'zeustrack': {
          enabled: false,
          sub_id: '',
        },
        'double_recovery': {
          enabled: false,
          form_id: 0,
        },
        'css_style': {
          enabled: false,
          stylesheet_url: '',
        },
        'custom': {
          enabled: false,
          afterSubmit: '',
        },
      },
      facebookEventNameOptions: ['Purchase', 'Lead', 'Complete registration'],
      facebookEventCurrencyOptions: ['EUR', 'USD'],

      showPopup: {
        'create': false,
        'edit': false,
        'create_forms_style': false,
      },
      newObject: {
        'create': {
          quantity: '',
          price: '',
          commission: '',
          sub_commission: '',
          old_price: '',
          discount: '',
        },
        'edit': {
          quantity: '',
          price: '',
          commission: '',
          sub_commission: '',
          old_price: '',
          discount: '',
        },
        'create_forms_style': {
          name: '',
          type: 'css',
          code: '',
        },
        'cross_sell': {
          title: '',
          description: '',
          img: '',
          old_price: '',
          price: '',
          commission: '',
          sub_commission: '',
        },
      },
      editingQuantityOptionIndex: -1,

      firstName: '',
      lastName: '',
      email: '',
      city: 'new-york',
      proposalTitle: '',
      jobTitle: '',
      textarea: '',
      eventName: '',
      eventLocation: 'san-francisco',
      status: 'plannning',
      cityOptions: [
        { text: 'New York', value: 'new-york' },
        { text: 'Chicago', value: 'chicago' },
        { text: 'San Francisco', value: 'san-francisco' },
        { text: 'Boston', value: 'boston' }
      ],
      statusOptions: [
        { text: 'Plannning', value: 'plannning' },
        { text: 'In Progress', value: 'in progress' },
        { text: 'Finished', value: 'finished' }
      ],
      LocationOptions: [
        { text: 'New York', value: 'new-york' },
        { text: 'Chicago', value: 'chicago' },
        { text: 'San Francisco', value: 'san-francisco' },
        { text: 'Boston', value: 'boston' }
      ]
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

            let v = ''
            if (this.form.network.id == this.network) {
              v = this.form.network_params[key]
            }
            this.network_params_values.push(v)
            this.network_params[this.network_params.length - 1].value = v
          }
        }
      }
    },
    changeNetworkParam(index, newVal) {
      this.network_params[index].value = newVal
    },
    showForm(type) {
      this.showPopup[type] = true
    },
    clearForm(type) {
      if (type == 'create_forms_style') {
        this.newObject[type] = {
          name: '',
          type: 'css',
          code: '',
        }
      } else if (type == 'cross_sell') {
        this.newObject[type] = {
          title: '',
          description: '',
          img: '',
          old_price: '',
          price: '',
          commission: '',
          sub_commission: '',
        }
      } else {
        this.newObject[type] = {
          quantity: '',
          price: '',
          commission: '',
          sub_commission: '',
          old_price: '',
          discount: '',
        }
      }
    },
    isValidForm(type) {
      let obj = this.newObject[type]
      if (type == 'create_forms_style') {
        return (obj.name != '' && obj.code != '')
      } else if (type == 'cross_sell') {
        return (obj.title != '' && obj.description != '' && obj.img != '' &&
          obj.price != '' && obj.old_price &&
          obj.commission != '' && obj.sub_commission)
      } else {
        return (obj.quantity != ''
          && obj.price != '' && obj.price >= 0
          && obj.commission != '' && obj.commission >= 0
          && obj.sub_commission != '' && obj.sub_commission >= 0)
      }
    },
    showEditQuantityOptionForm(index) {
      let obj = this.quantityOptions[index]
      this.newObject['edit'] = {
        quantity: obj.quantity,
        price: obj.price,
        commission: obj.commission,
        sub_commission: obj.sub_commission,
        old_price: obj.old_price,
        discount: obj.discount,
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
        old_price: this.newObject['create'].old_price,
        discount: this.newObject['create'].discount,
      })
      this.showPopup['create'] = false
      this.clearForm('create')
    },
    changeQuantityDefault() {
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
          network_id: this.network,
          product_id: this.product,
          traffic_source_id: this.traffic_source,
          quantity: {
            options: {},
            default: '',
          },
          up_sell: {
            enabled: this.upSell,
            best_seller: this.upSellBestSeller,
          },
          cross_sell: this.crossSell,
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
        data.up_sell = JSON.stringify(data.up_sell)
        data.cross_sell = JSON.stringify(data.cross_sell)
        data.network_params = JSON.stringify(data.network_params)
        data.plugins = JSON.stringify(data.plugins)

        // Edit form
        this.$http.put('/api/v1/forms/' + this.form_id, data, { headers: headers })
          .then((response) => {
            this.$vs.loading.close()
            this.$vs.notify({
              title: 'Success',
              text: 'Form modified',
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
    createFormsStyle() {
      this.$vs.loading()
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.newObject['create_forms_style'].name,
        type: this.newObject['create_forms_style'].type,
        code: this.newObject['create_forms_style'].code,
      }

      // Create forms style
      this.$http.post('/api/v1/forms-styles', data, { headers: headers })
        .then((response) => {
          this.formsStyles.push(response.data.data)
          this.formsStylesOptions.push({
            value: response.data.data.url,
            label: response.data.data.name,
          })
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Forms style created',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.showPopup['create_forms_style'] = false
          this.clearForm('create_forms_style')
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
          this.showPopup['create_forms_style'] = false
          this.clearForm('create_forms_style')
        })
    },
    showCrossSellForm() {
      this.newObject['cross_sell'].title = this.crossSell.title
      this.newObject['cross_sell'].description = this.crossSell.description
      this.newObject['cross_sell'].img = this.crossSell.img
      this.newObject['cross_sell'].old_price = this.crossSell.old_price
      this.newObject['cross_sell'].price = this.crossSell.price
      this.newObject['cross_sell'].commission = this.crossSell.commission
      this.newObject['cross_sell'].sub_commission = this.crossSell.sub_commission
      this.showForm('cross_sell')
    },
    setCrossSell() {
      this.crossSell.title = this.newObject['cross_sell'].title
      this.crossSell.description = this.newObject['cross_sell'].description
      this.crossSell.img = this.newObject['cross_sell'].img
      this.crossSell.old_price = this.newObject['cross_sell'].old_price
      this.crossSell.price = this.newObject['cross_sell'].price
      this.crossSell.commission = this.newObject['cross_sell'].commission
      this.crossSell.sub_commission = this.newObject['cross_sell'].sub_commission
      this.showPopup['cross_sell'] = false
      this.clearForm('cross_sell')
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

    // Retrieve form info
    this.$vs.loading()
    this.$http.get('/api/v1/forms/' + this.form_id, { headers })
      .then((response) => {
        this.form = response.data.data

        // Fetch general info
        this.name = this.form.name

        // Fetch quantity options
        this.quantityOptions = []
        let index = 0
        for (const [key, value] of Object.entries(this.form.quantity.options)) {
          let d = (this.form.quantity.default == key)
          this.quantityOptions.push({
            quantity: key,
            price: value.price,
            commission: value.commission,
            sub_commission: value.sub_commission,
            default: d,
          });
          if (d) {
            this.quantityDefaultIndex = index
          }
          index++
        }

        // Fetch up sell
        this.upSell = this.form.up_sell.enabled
        if (this.form.up_sell.hasOwnProperty('best_seller')) {
          this.upSellBestSeller = this.form.up_sell.best_seller
        }

        // Fetch cross sell
        if (this.form.cross_sell.hasOwnProperty('enabled')) {
          this.crossSell.enabled = (this.form.cross_sell.enabled == true)
        }
        if (this.form.cross_sell.hasOwnProperty('title')) {
          this.crossSell.title = this.form.cross_sell.title
        }
        if (this.form.cross_sell.hasOwnProperty('description')) {
          this.crossSell.description = this.form.cross_sell.description
        }
        if (this.form.cross_sell.hasOwnProperty('img')) {
          this.crossSell.img = this.form.cross_sell.img
        }
        if (this.form.cross_sell.hasOwnProperty('old_price')) {
          this.crossSell.old_price = this.form.cross_sell.old_price
        }
        if (this.form.cross_sell.hasOwnProperty('price')) {
          this.crossSell.price = this.form.cross_sell.price
        }
        if (this.form.cross_sell.hasOwnProperty('commission')) {
          this.crossSell.commission = this.form.cross_sell.commission
        }
        if (this.form.cross_sell.hasOwnProperty('sub_commission')) {
          this.crossSell.sub_commission = this.form.cross_sell.sub_commission
        }

        // Fetch plugins
        for (const [key, value] of Object.entries(this.form.plugins)) {
          if (!this.plugins.hasOwnProperty(key)) {
            this.plugins[key] = this.form.plugins[key]
          } else {
            for (const [key2, value2] of Object.entries(this.form.plugins[key])) {
                this.plugins[key][key2] = this.form.plugins[key][key2]
            }
          }
        }

        // Retrieve users
        this.$http.get('/api/v1/users/', { headers })
          .then((response) => {
            this.traffic_sources = []
            response.data.data.forEach((user) => {
              this.users.push({
                id: user.id,
                label: user.email,
              })
              if (user.id == this.form.user.id) {
                this.user = user.id;
              }
            })

            // Retrieve traffic sources
            this.$http.get('/api/v1/traffic-sources/', { headers })
              .then((response) => {
                this.traffic_sources = []
                response.data.data.forEach((traffic_source) => {
                  this.traffic_sources.push({
                    id: traffic_source.id,
                    label: traffic_source.name,
                  })
                  if (traffic_source.id == this.form.traffic_source.id) {
                    this.traffic_source = traffic_source.id;
                  }
                })

                // Retrieve products
                this.$http.get('/api/v1/products/', { headers })
                  .then((response) => {
                    this.products = []
                    response.data.data.forEach((product) => {
                      this.products.push({
                        id: product.id,
                        label: product.name,
                      })
                      if (product.id == this.form.product.id) {
                        this.product = product.id;
                      }
                    })

                    // Retrieve networks
                    this.$http.get('/api/v1/networks/', { headers })
                      .then((response) => {
                        this.networks = response.data.data
                        this.networks.forEach((network) => {
                          this.networksOptions.push({
                            id: network.id,
                            label: network.name,
                          })
                          if (network.id == this.form.network.id) {
                            this.network = network.id;
                          }
                        })

                        // Retrieve forms
                        this.$http.get('/api/v1/forms/', { headers })
                          .then((response) => {
                            this.forms = response.data.data
                            this.forms.forEach((form) => {
                              this.formsOptions.push({
                                id: form.id,
                                label: form.name + " - ID: " + form.id,
                              })
                              if (this.form.plugins.hasOwnProperty('double_recovery') && 
                                  form.id == this.form.plugins.double_recovery.form_id) {
                                this.plugins['double_recovery'].form_id = form.id;
                              }
                            })

                            // Retrieve forms styles
                            this.$http.get('/api/v1/forms-styles', { headers })
                              .then((response) => {
                                this.formsStyles = response.data.data
                                for (let i = 0; i < this.formsStyles.length; i++) {
                                  let formsStyle = this.formsStyles[i]
                                  this.formsStylesOptions.push({
                                    value: formsStyle.url,
                                    label: formsStyle.name,
                                  })
                                  if (this.form.plugins.hasOwnProperty('css_style') && 
                                      formsStyle.url == this.form.plugins.css_style.stylesheet_url) {
                                    this.plugins['css_style'].stylesheet_url = formsStyle.url
                                  }
                                }
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
      })
      .catch((error) => {
        this.$vs.loading.close()
      })
  },
}
</script>
