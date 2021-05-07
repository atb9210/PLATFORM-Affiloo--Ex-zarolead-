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
              <vs-select v-model="product" class="w-full select-large" label="Product" v-validate="'required'" @change="changeUpSellOptions">
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
        <tab-content title="Up sell & Cross sell" class="mb-5" icon="feather icon-dollar-sign" :before-change="validateStep2">
          <form data-vv-scope="step-2">
          <div class="vx-row">
            <!--
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
            -->
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Up sell</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="upSell"></vs-checkbox>
                  </div>
                </template>
                <vs-select v-model="upSellBestSeller" class="w-full">
                  <vs-select-item :key="index" :value="item" :text="item" v-for="(item,index) in upSellOptions" class="w-full" />
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
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Delivery</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="delivery.enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-input @click="showDeliveryForm" value="Click here to update" class="w-full" icon-pack="feather" icon="icon-globe" icon-no-border />
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

          <!-- Thank you redirect plugin -->
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

            <!-- Facebook pixel plugin -->
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

            <!-- Zeus track plugin -->
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

            <!-- Double recovery plugin -->
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

            <!-- CSS style template plugin -->
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
                    <div class="append-text bg-primary cursor-pointer" @click="showForm('create_forms_style')">
                        <span><vs-icon icon-pack="feather" icon="icon-plus"></vs-icon></span>
                    </div>
                </template>
              </vx-input-group>
            </div>

            <!-- Custom JS plugin -->
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

    <!-- Create forms style popup -->
    <vs-popup title="Create forms style" :active.sync="showPopup['create_forms_style']">

        <div class="vx-row mb-6">
            <div class="vx-col w-full">
                <center><h1>Current styles</h1></center>
            </div>
        </div>

        <div class="vx-row mb-6" :key="index" v-for="(item, index) in formsStyles">
            <div class="vx-col sm:w-1/3 w-full">
                <span>{{ item.name }}</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <p class="flex items-center mb-4">
                    <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-airplay" @click="loadFormsStyle(index)">Load</vs-button>
                    <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="deleteFormsStyle(index)">Delete</vs-button>
                </p>
            </div>
        </div>

        <div class="vx-row mb-6">
            <div class="vx-col w-full">
                <center><h1>Fields</h1></center>
            </div>
        </div>

        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Name</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create_forms_style'].name" />
            </div>
        </div>

        <!--
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>CSS Code</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-textarea v-model="newObject['create_forms_style'].code"/>
            </div>
        </div>
        -->

        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Container:</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full flex items-center">
                <input type="color" class="w-1/2" v-model="newObject['create_forms_style'].background_color" />
                <input type="color" class="w-1/2" v-model="newObject['create_forms_style'].text_color" />
            </div>
        </div>

        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Input:</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full flex items-center">
                <input type="color" class="w-1/3" v-model="newObject['create_forms_style'].input_background_color" />
                <input type="color" class="w-1/3" v-model="newObject['create_forms_style'].input_text_color" />
                <input type="color" class="w-1/3" v-model="newObject['create_forms_style'].input_border_color" />
            </div>
        </div>

        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Button:</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full flex items-center">
                <input type="color" class="w-full" v-model="newObject['create_forms_style'].button_background_color" />
                <input type="color" class="w-1/2" v-model="newObject['create_forms_style'].button_text_color" />
            </div>
        </div>

        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>Button text:</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full flex items-center">
                <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create_forms_style'].button_text" />
            </div>
        </div>

        <div class="vx-row mb-6">
            <vs-button
                v-if="currentStyleExists()"
                class="mt-12 mr-auto ml-auto"
                @click="updateFormsStyle"
                color="primary" type="filled"
                :disabled="!isValidForm('create_forms_style')">
                Update
            </vs-button>
            <vs-button
                v-else
                class="mt-12 mr-auto ml-auto"
                @click="createFormsStyle"
                color="primary" type="filled"
                :disabled="!isValidForm('create_forms_style')">
                Create
            </vs-button>
        </div>

        <!-- CSS Template preview -->
        <div class="vx-row mb-6">
            <div class="vx-col w-full">
                <center><h1>Preview</h1></center>
            </div>
            <div class="vx-col w-full">
                <div id="css-plugin-preview-form-container" :style="cssPluginContainerStyle">
                    <form id="css-plugin-preview-main-form" method="post" action="" accept-charset="utf-8" role="form" style="display: block;">
                        <div>
                            <div style="margin-botton: 1rem; display: block;">
                                <label style="display: inline-block;margin-bottom: .5rem;">Nome e cognome</label>
                                <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                            </div>
                            <div style="margin-botton: 1rem; display: block;">
                                <label style="display: inline-block;margin-bottom: .5rem;">Telefono (meglio cellulare)</label>
                                <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                            </div>
                            <div style="margin-botton: 1rem; display: block;">
                                <label style="display: inline-block;margin-bottom: .5rem;">Città e indirizzo di consegna</label>
                                <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                            </div>
                        </div>
                        <button class="css-plugin-preview-form-button" :style="cssPluginButtonStyle">{{ newObject['create_forms_style'].button_text }}</button>
                    </form>

                </div>
            </div>
        </div>

    </vs-popup>

    <!-- Cross sell popup -->
    <vs-popup title="cross sell" :active.sync="showPopup['cross_sell']">
        <div class="vx-row mb-6">
            <div class="vx-col sm:w-1/3 w-full">
                <span>SKU</span>
            </div>
            <div class="vx-col sm:w-2/3 w-full">
                <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['cross_sell'].sku" />
            </div>
        </div>
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

    <!-- Delivery popup -->
    <vs-popup title="Delivery Options" :active.sync="showPopup['delivery']">
        <div class="vx-row">
            <div class="vx-col w-full flex items-center">
                <div class="w-1/3">Name</div>
                <div class="w-1/3">Days</div>
                <div class="w-1/3">Price</div>
            </div>
        </div>
        <div class="vx-row mb-6" v-for="(option, index) in newObject['delivery'].options">
            <div class="vx-col w-full flex items-center">
                <vs-input class="w-1/3" icon-pack="feather" icon="icon-tag" placeholder="Standard delivery" icon-no-border v-model="newObject['delivery'].options[index].name" />
                <vs-input class="w-1/3" icon-pack="feather" icon="icon-globe" placeholder="3" icon-no-border type="number" step="1" min="1" v-model="newObject['delivery'].options[index].days" />
                <vs-input class="w-1/3" icon-pack="feather" icon="icon-dollar-sign" icon-no-border type="number" step="0.01" min="0" v-model="newObject['delivery'].options[index].price" />
            </div>
        </div>

        <div class="vx-row mb-6">
            <div class="vx-col md:w-1/3 w-full">
            </div>
            <div class="vx-col md:w-2/3 w-full flex items-center">
                <div class="md:w-1/3"></div>
                <div class="md:w-1/3"></div>
                <vs-button class="md:w-1/3" icon-pack="feather" icon="icon-plus" @click="addNewDeliveryOption">Add</vs-button>
            </div>
        </div>

        <div class="vx-row mb-6">
            <vs-button class="mt-12 mr-auto ml-auto" @click="setDelivery" color="primary" type="filled" :disabled="!isValidForm('delivery')">Update</vs-button>
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
  data () {
    return {
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

      upSell: false,
      upSellBestSeller: '1',
      upSellOptions: [],
      crossSell: {
        enabled: false,
        sku: '',
        title: '',
        description: '',
        img: '',
        old_price: '',
        price: '',
        commission: '',
        sub_commission: '',
      },
      delivery: {
        enabled: false,
        options: [],
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
        'cross_sell': false,
        'delivery': false,
      },
      newObject: {
        'create': {
          price: '',
          commission: '',
          sub_commission: '',
          old_price: '',
          discount: '',
        },
        'edit': {
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
          background_color: '#ffffff',
          text_color: '#000000',
          input_background_color: '#ffffff',
          input_text_color: '#000000',
          input_border_color: '#f2e832',
          button_background_color: '#eaac00',
          button_text_color: '#ffffff',
          button_text: 'Conferma l\'ordine',
        },
        'cross_sell': {
          sku: '',
          title: '',
          description: '',
          img: '',
          old_price: '',
          price: '',
          commission: '',
          sub_commission: '',
        },
        'delivery': {
          options: [{
            name: '',
            days: 1,
            price: 0,
          }],
        },
      },
      editingQuantityOptionIndex: -1,
    }
  },
  computed: {
    canChangeUser() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin'))
    },
    cssPluginContainerStyle() {
      return {
        backgroundColor: this.newObject['create_forms_style'].background_color,
        color: this.newObject['create_forms_style'].text_color,
      }
    },
    cssPluginInputStyle() {
      return {
        backgroundColor: this.newObject['create_forms_style'].input_background_color,
        color: this.newObject['create_forms_style'].input_text_color,
        borderColor: this.newObject['create_forms_style'].input_border_color,
      }
    },
    cssPluginButtonStyle() {
      return {
        backgroundColor: this.newObject['create_forms_style'].button_background_color,
        color: this.newObject['create_forms_style'].button_text_color,
      }
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
    currentStyleExists() {
      let name = this.newObject['create_forms_style'].name

      for (let i = 0; i < this.formsStyles.length; i++) {
        if (name == this.formsStyles[i].name) {
          return true
        }
      }

      return false
    },
    async getFormsStyles() {
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$vs.loading()
      const response = await this.$http.get('/api/v1/forms-styles', { headers })
      this.formsStyles = response.data.data
      this.formsStylesOptions = []
      for (let i = 0; i < this.formsStyles.length; i++) {
        let formsStyle = this.formsStyles[i]
        this.formsStylesOptions.push({
          value: formsStyle.url,
          label: formsStyle.name,
        })
      }
      this.$vs.loading.close()
    },
    async loadFormsStyle(index) {
      this.newObject['create_forms_style'] = this.formsStyles[index].settings
    },
    async createFormsStyle() {
      this.$vs.loading()
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Create CSS code
      this.newObject['create_forms_style'].code = ''

      this.newObject['create_forms_style'].code += '#formContainer {'
      this.newObject['create_forms_style'].code += 'background-color: ' + this.newObject['create_forms_style'].background_color + ' !important;'
      this.newObject['create_forms_style'].code += 'color: ' + this.newObject['create_forms_style'].text_color + ' !important;'
      this.newObject['create_forms_style'].code += '}'

      this.newObject['create_forms_style'].code += '.form-input {'
      this.newObject['create_forms_style'].code += 'background-color: ' + this.newObject['create_forms_style'].input_background_color + ' !important;'
      this.newObject['create_forms_style'].code += 'color: ' + this.newObject['create_forms_style'].input_text_color + ' !important;'
      this.newObject['create_forms_style'].code += 'border-color: ' + this.newObject['create_forms_style'].input_border_color + ' !important;'
      this.newObject['create_forms_style'].code += '}'


      this.newObject['create_forms_style'].code += '.form-button {'
      this.newObject['create_forms_style'].code += 'background-color: ' + this.newObject['create_forms_style'].button_background_color + ' !important;'
      this.newObject['create_forms_style'].code += 'color: ' + this.newObject['create_forms_style'].button_text_color + ' !important;'
      this.newObject['create_forms_style'].code += '}'

      let settings = this.newObject['create_forms_style']

      let data = {
        name: this.newObject['create_forms_style'].name,
        type: this.newObject['create_forms_style'].type,
        code: this.newObject['create_forms_style'].code,
        settings: JSON.stringify(settings),
      }

      // Create forms style
      const response = await this.$http.post('/api/v1/forms-styles', data, { headers: headers })
      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Style created',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.showPopup['create_forms_style'] = false
      this.clearForm('create_forms_style')

      this.getFormsStyles()
    },
    async updateFormsStyle() {
      this.$vs.loading()
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

     
      let formsStyle = null

      let name = this.newObject['create_forms_style'].name
      for (let i = 0; i < this.formsStyles.length; i++) {
        if (name == this.formsStyles[i].name) {
          formsStyle = this.formsStyles[i]
        }
      }

      if (formsStyle == null) {
        this.$vs.loading.close()
        return
      }

      // Create CSS code
      this.newObject['create_forms_style'].code = ''

      this.newObject['create_forms_style'].code += '#formContainer {'
      this.newObject['create_forms_style'].code += 'background-color: ' + this.newObject['create_forms_style'].background_color + ' !important;'
      this.newObject['create_forms_style'].code += 'color: ' + this.newObject['create_forms_style'].text_color + ' !important;'
      this.newObject['create_forms_style'].code += '}'

      this.newObject['create_forms_style'].code += '.form-input {'
      this.newObject['create_forms_style'].code += 'background-color: ' + this.newObject['create_forms_style'].input_background_color + ' !important;'
      this.newObject['create_forms_style'].code += 'color: ' + this.newObject['create_forms_style'].input_text_color + ' !important;'
      this.newObject['create_forms_style'].code += 'border-color: ' + this.newObject['create_forms_style'].input_border_color + ' !important;'
      this.newObject['create_forms_style'].code += '}'


      this.newObject['create_forms_style'].code += '.form-button {'
      this.newObject['create_forms_style'].code += 'background-color: ' + this.newObject['create_forms_style'].button_background_color + ' !important;'
      this.newObject['create_forms_style'].code += 'color: ' + this.newObject['create_forms_style'].button_text_color + ' !important;'
      this.newObject['create_forms_style'].code += '}'

      let settings = this.newObject['create_forms_style']

      let data = {
        name: this.newObject['create_forms_style'].name,
        type: this.newObject['create_forms_style'].type,
        code: this.newObject['create_forms_style'].code,
        settings: JSON.stringify(settings),
      }

      // Update forms style
      const response = await this.$http.put('/api/v1/forms-styles/' + formsStyle.id, data, { headers: headers })
      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Style updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.showPopup['create_forms_style'] = false
      this.clearForm('create_forms_style')

      this.getFormsStyles()
    },
    async deleteFormsStyle(index) {
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let formsStyle = this.formsStyles[index]

      this.$vs.loading()
      await this.$http.delete('/api/v1/forms-styles/' + formsStyle.id, { headers: headers })
      this.$vs.loading.close()

      this.$vs.notify({
        title: 'Success',
        text: 'Style deleted',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.getFormsStyles()
    },
    async showForm(type) {
      if (type == 'create_forms_style') {
        this.getFormsStyles()
      }
      this.showPopup[type] = true
    },
    clearForm(type) {
      if (type == 'create_forms_style') {
        this.newObject[type] = {
          name: '',
          type: 'css',
          code: '',
          background_color: '#ffffff',
          text_color: '#000000',
          input_background_color: '#ffffff',
          input_text_color: '#000000',
          input_border_color: '#f2e832',
          button_background_color: '#eaac00',
          button_text_color: '#ffffff',
          button_text: 'Conferma l\'ordine',
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
      } else if (type == 'delivery') {
        this.newObject[type] = {
          options: [{
            name: '',
            days: 1,
            price: 0,
          }],
        }
      } else {
        this.newObject[type] = {
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
        return (obj.name != '')
      } else if (type == 'cross_sell') {
        return (obj.sku != '' && obj.title != '' && obj.description != '' &&
          obj.img != '' &&
          obj.price != '' && obj.old_price &&
          obj.commission != '' && obj.sub_commission)
      } else if (type == 'delivery') {
        return true
      } else {
        return false
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
          up_sell: {
            enabled: this.upSell,
            best_seller: this.upSellBestSeller,
          },
          cross_sell: this.crossSell,
          delivery: this.delivery,
          network_params: {},
          plugins: {},
        }
        if (this.canChangeUser) {
          if (!(this.user === '' || this.user <= 0)) {
            data.user_id = this.user
          }
        }
        for (let i = 0; i < this.network_params.length; i++) {
          data.network_params[this.network_params[i].name] = this.network_params_values[i]
        }
        data.plugins = this.plugins

        data.up_sell = JSON.stringify(data.up_sell)
        data.cross_sell = JSON.stringify(data.cross_sell)
        data.delivery = JSON.stringify(data.delivery)
        data.network_params = JSON.stringify(data.network_params)
        data.plugins = JSON.stringify(data.plugins)

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
    showCrossSellForm() {
      this.newObject['cross_sell'].sku = this.crossSell.sku
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
      this.crossSell.sku = this.newObject['cross_sell'].sku
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
    showDeliveryForm() {
      this.newObject['delivery'].options = this.delivery.options
      this.showForm('delivery')
    },
    setDelivery() {
      this.delivery.options = []
      this.newObject['delivery'].options.forEach((opt) => {
        if (opt.name == '' || opt.days == '' || opt.price == '') {
          return
        }
        this.delivery.options.push(opt)
      })
      this.showPopup['delivery'] = false
      this.clearForm('delivery')
    },
    addNewDeliveryOption() {
      this.newObject['delivery'].options.push({
        name: '',
        days: 1,
        price: 0,
      })
    },
    changeUpSellOptions() {
      let index = -1

      this.upSellOptions = []

      for (let i = 0; i < this.products.length; i++) {
        if (this.products[i].id == this.product) {
          index = i
          break
        }
      }
      if(index < 0) return

      let p = this.products[index]

      for (const [key, value] of Object.entries(p.quantity.options)) {
        this.upSellOptions.push(key)
        this.upSellBestSeller = key
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

    // Retrieve forms styles
    this.getFormsStyles()

    // Retrieve users
    this.$vs.loading()
    this.$http.get('/api/v1/users', { headers })
      .then((response) => {
        this.traffic_sources = []
        response.data.data.forEach((user) => {
          this.users.push({
            id: user.id,
            label: user.email,
          })
          this.user = user.id;
        })

        // Retrieve traffic sources
        this.$http.get('/api/v1/traffic-sources', { headers })
          .then((response) => {
            this.traffic_sources = []
            response.data.data.forEach((traffic_source) => {
              this.traffic_sources.push({
                id: traffic_source.id,
                label: traffic_source.name,
              })
              this.traffic_source = traffic_source.id;
            })

            // Retrieve products
            this.$http.get('/api/v1/products', { headers })
              .then((response) => {
                this.products = []
                response.data.data.forEach((product) => {
                  this.products.push({
                    id: product.id,
                    label: product.name,
                    quantity: product.quantity,
                  })
                  this.product = product.id;
                })

                // Retrieve networks
                this.$http.get('/api/v1/networks', { headers })
                  .then((response) => {
                    this.networks = response.data.data
                    this.networks.forEach((network) => {
                      this.networksOptions.push({
                        id: network.id,
                        label: network.name,
                      })
                      this.network = network.id;
                    })

                    // Close loading here, we don't need forms in the first step
                    this.$vs.loading.close()

                    // Retrieve forms
                    this.$http.get('/api/v1/forms', { headers })
                      .then((response) => {
                        this.forms = response.data.data
                        for (let i = 0; i < this.forms.length; i++) {
                          let form = this.forms[i]
                          this.formsOptions.push({
                            id: form.id,
                            label: form.name + " - ID: " + form.id,
                          })
                          this.plugins['double_recovery'].form_id = form.id;
                        }
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

<style>
#css-plugin-preview-form-container {
    width: 100%;
    /*color: black;*/
    /*background-color: white;*/
}

#css-plugin-preview-main-form {
   padding: 15px;
   height: 100%;
}

.css-plugin-preview-form-input {
	margin-bottom: 0.5em;
	font-weight: 400;
	width: 100%;
	border-radius: 3px;
	padding: 15px;
	font-size: 22px;
	border-width: 1px;
	border-style: double;
	/*border-color: #f2e832;*/
	box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5);
	font-family: "Open Sans",Sans-serif;
	font-weight: 400;
}

.css-plugin-preview-form-button {
    margin-bottom: 3em;
    border: 1px solid #e0e0e0;
    font-size: 25px;
    padding: 10px;
    width: 90%;
    margin-left: auto !important;
    margin-right: auto !important;
    display: block;
    float: none;
    font-family: "Arial",Sans-serif;
    font-weight: 600;
    text-transform: none;
    /*color: #fff;*/
    /*background-color: #eaac00;*/
    border-radius: 8px;
    box-shadow: 0px 0px 10px 0px rgba(0,0,0,.5);
}
</style>
