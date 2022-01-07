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
      <form-wizard color="rgba(var(--vs-primary), 1)" errorColor="rgba(var(--vs-danger), 1)" :title="null" :subtitle="null" finishButtonText="Finish">
        <!-- tab 1 content -->
        <tab-content title="Form Informations" class="mb-5" icon="feather icon-info" :before-change="validateStep1">
          <form data-vv-scope="step-1">
          <div class="vx-row">
            <div class="vx-col md:w-full w-full mt-5">
              <vs-input icon-pack="feather" icon="icon-tag" icon-no-border label="Name" v-model="name" class="w-full" name="name" v-validate="'required'" />
              <span class="text-danger">{{ errors.first('step-1.name') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select autocomplete v-model="product" class="w-full select-large" label="Product" v-validate="'required'" @change="changeProduct">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in products" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.product') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select autocomplete v-model="network" class="w-full select-large" label="Network" v-validate="'required'" @change="changeNetwork">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in networksOptions" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.network') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select autocomplete v-model="traffic_source" class="w-full select-large" label="Traffic Source" v-validate="'required'" @change="changeTrafficSource">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in traffic_sources" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-1.traffic_source') }}</span>
            </div>
            <div class="vx-col md:w-1/2 w-full mt-5" v-if="canChangeUser">
              <vs-select autocomplete v-model="user" class="w-full select-large" label="Mediabuyer" v-validate="'required'">
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

          <div class="vx-row">

            <!-- Thank you redirect plugin -->
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
                  placeholder="Facebook pixel"
                />
                <vs-select autocomplete v-model="plugins['facebook'].eventName" class="w-full select-large">
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
                <vs-select autocomplete v-model="plugins['facebook'].eventCurrency" class="w-full select-large">
                  <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in facebookEventCurrencyOptions" class="w-full" />
                </vs-select>
              </vx-input-group>
            </div>

            <!-- Facebook conversion API plugin -->
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Facebook Conversions API</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="plugins['facebook_conversions_api'].enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-input
                  class="w-full"
                  icon-pack="feather" icon="icon-facebook" icon-no-border
                  v-model="plugins['facebook_conversions_api'].pixel"
                  placeholder="Facebook pixel"
                />
                <vs-input
                  class="w-full"
                  icon-pack="feather" icon="icon-facebook" icon-no-border
                  v-model="plugins['facebook_conversions_api'].access_token"
                  placeholder="Access token"
                />
                <!-- TODO: This is only for testing -->
                <vs-input
                  class="w-full"
                  icon-pack="feather" icon="icon-facebook" icon-no-border
                  v-model="plugins['facebook_conversions_api'].test_code"
                  placeholder="Test code (e.g. TEST2331)"
                />
                <vs-input
                  type="number"
                  step="0.01"
                  min="0"
                  class="w-full"
                  icon-pack="feather" icon="icon-dollar-sign" icon-no-border
                  v-model="plugins['facebook_conversions_api'].eventValue"
                  name="fb_event_value" placeholder="Conversion event value"
                />
                <vs-select autocomplete v-model="plugins['facebook_conversions_api'].eventCurrency" class="w-full select-large">
                  <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in facebookEventCurrencyOptions" class="w-full" />
                </vs-select>
              </vx-input-group>
            </div>

            <!-- TikTok pixel plugin -->
            <div class="vx-col w-full mt-5">
              <div class="mb-2">TikTok Pixel</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="plugins['tiktok'].enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-input
                  class="w-full"
                  icon-pack="feather" icon="icon-music" icon-no-border
                  v-model="plugins['tiktok'].pixel"
                  placeholder="TikTok pixel"
                />
                <vs-input
                  class="w-full"
                  icon-pack="feather" icon="icon-music" icon-no-border
                  v-model="plugins['tiktok'].token"
                  placeholder="TikTok pixel token API"
                />
                <vs-select autocomplete v-model="plugins['tiktok'].eventName" class="w-full select-large">
                  <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in tiktokEventNameOptions" class="w-full" />
                </vs-select>
              </vx-input-group>
            </div> 

            <!-- Google pixel plugin -->
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Google Pixel</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="plugins['google'].enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-input
                  class="w-full"
                  icon-pack="feather" icon="icon-goole" icon-no-border
                  v-model="plugins['google'].pixel"
                  placeholder="Google pixel"
                />
                <vs-select autocomplete v-model="plugins['google'].eventName" class="w-full select-large">
                  <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in googleEventNameOptions" class="w-full" />
                </vs-select>
              </vx-input-group>
            </div>

            <!-- Outbrain pixel plugin -->
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Outbrain Pixel</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="plugins['outbrain'].enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-input
                  class="w-full"
                  icon-pack="feather" icon="icon-brain" icon-no-border
                  v-model="plugins['outbrain'].pixel"
                  placeholder="Outbrain pixel"
                />
                <vs-select autocomplete v-model="plugins['outbrain'].eventName" class="w-full select-large">
                  <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in outbrainEventNameOptions" class="w-full" />
                </vs-select>
              </vx-input-group>
            </div>

            <!-- Zeus track plugin -->
            <!-- TODO: At the moment we don't use it -->
            <!--
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
            -->

            <!-- Double recovery plugin -->
            <div class="vx-col w-full mt-5">
              <div class="mb-2">Double Recovery</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="plugins['double_recovery'].enabled"></vs-checkbox>
                  </div>
                </template>
                <vs-select autocomplete v-model="plugins['double_recovery'].form_id" class="w-full select-large" v-validate="'required'">
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
                <vs-select autocomplete v-model="selectedFormsStyle" class="w-full select-large" v-validate="'required'">
                  <vs-select-item :key="index" :value="item" :text="item.name" v-for="(item, index) in formsStyles" class="w-full" />
                </vs-select>
                <template slot="append">
                    <div class="append-text cursor-pointer" :class="(selectedFormsStyle != null) ? 'bg-primary' : 'bg-dark'" @click="previewFormsStyle()">
                        <span><vs-icon icon-pack="feather" icon="icon-eye"></vs-icon></span>
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

        <!-- tab 5 content -->
        <!-- DEBUG TODO: Old step 5 -->
        <!--
        <tab-content title="Landing" class="mb-5" icon="feather icon-layout" :before-change="validateStep5">
          <form data-vv-scope="step-5">

          <div class="vx-row">
            <div class="vx-col w-full mt-5">
              <vs-checkbox v-model="landing.useExistingOne">Use an already existing landing</vs-checkbox>
            </div>
          </div>

          <div class="vx-row" v-if="!landing.useExistingOne">

            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-input icon-pack="feather" icon="icon-layout" icon-no-border label="Landing Name" v-model="landing.name" class="w-full" name="landing_name" v-validate="'required'" />
              <span class="text-danger">{{ errors.first('step-5.landing_name') }}</span>
            </div>

            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select autocomplete v-model="landing.product" class="w-full select-large" label="Product" v-validate="'required'" disabled="disabled">
                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in products" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-5.landing_product') }}</span>
            </div>

            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-input icon-pack="feather" icon="icon-globe" icon-no-border label="Landing URL" v-model="landing.url" class="w-full" name="landing_url" v-validate="'required'" />
              <span class="text-danger">{{ errors.first('step-5.landing_url') }}</span>
            </div>

            <div class="vx-col md:w-1/2 w-full mt-5">
              <vs-select autocomplete v-model="landing.country" class="w-full select-large" label="Country" v-validate="'required'">
                <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in countryOptions" class="w-full" />
              </vs-select>
              <span class="text-danger">{{ errors.first('step-5.landing_country') }}</span>
            </div>

          </div>

          <div class="vx-row" v-else>

            <div class="vx-col w-full mt-5">
              <vs-select autocomplete v-model="landing.landing" class="w-full select-large" label="Choose the existing landing to use" v-validate="'required'">
                <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in landings" class="w-full" />
              </vs-select>
            </div>

          </div>

          </form>
        </tab-content>
        -->
        <tab-content title="Flow" class="mb-5" icon="feather icon-git-branch" :before-change="validateStep5">
          <form data-vv-scope="step-5">

          <div class="vx-row">
            <div class="vx-col w-full mt-5">
              <vs-checkbox v-model="flow.useExistingOne">Use an already existing flow</vs-checkbox>
            </div>
          </div>

          <div v-if="!flow.useExistingOne" class="w-full">

            <div class="vx-row mb-10">
                <div class="vx-col md:w-1/2 w-full mt-5">
                  <vs-input icon-pack="feather" icon="icon-tag" icon-no-border label="Flow Name" v-model="flow.name" class="w-full" name="flow_name" v-validate="'required'" />
                  <span class="text-danger">{{ errors.first('step-5.flow_name') }}</span>
                </div>

                <div class="vx-col md:w-1/2 w-full mt-5">
                  <vs-select autocomplete v-model="flow.domain_id" class="w-full select-large" label="Domain" v-validate="'required'">
                    <vs-select-item :key="index" :value="item.id" :text="item.domain" v-for="(item, index) in domains" class="w-full" />
                  </vs-select>
                  <span class="text-danger">{{ errors.first('step-5.flow_domain_id') }}</span>
                </div>

                <div class="vx-col md:w-1/2 w-full mt-5">
                  <vs-select autocomplete v-model="flow.type" class="w-full select-large" label="Type" v-validate="'required'">
                    <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in flowTypes" class="w-full" />
                  </vs-select>
                  <span class="text-danger">{{ errors.first('step-5.flow_type') }}</span>
                </div>

                <div class="vx-col md:w-1/2 w-full mt-5">
                  <vs-input icon-pack="feather" icon="icon-hash" icon-no-border label="Path name" v-model="flow.path_name" class="w-full" name="flow_path_name" v-validate="'required'" />
                  <span>{{flowPathname}}</span>
                  <span class="text-danger">{{ errors.first('step-5.flow_path_name') }}</span>
                </div>

                <div class="vx-col w-full mt-5">
                  <vs-select autocomplete v-model="flow.traffic_source_id" class="w-full select-large" label="Traffic source" v-validate="'required'">
                    <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in traffic_sources" class="w-full" />
                  </vs-select>
                  <span class="text-danger">{{ errors.first('step-5.flow_traffic_source_id') }}</span>
                </div>
            </div>

            <div class="vx-row mb-2" v-for="(lan, lanIndex) in flow.landings">
                <div class="vx-col w-1/2 mb-2 text-center" v-if="lanIndex == 0">
                    <span>Landings</span>
                </div>
                <div class="vx-col w-1/6 mb-2 text-center" v-if="lanIndex == 0">
                    <span>Weight</span>
                </div>
                <div class="vx-col w-1/6 mb-2 text-center" v-if="lanIndex == 0">
                    <span>%</span>
                </div>
                <div class="vx-col w-1/6 mb-2 text-center" v-if="lanIndex == 0">
                </div>
                <div class="vx-col w-full flex items-center">
                    <vs-select autocomplete class="w-1/2 select-large" v-model="flow.landings[lanIndex]" v-validate="'required'">
                        <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in landings" class="w-full" />
                    </vs-select>
                    <vs-input class="w-1/6" type="number" min="0" icon-pack="feather" icon="icon-percent" placeholder="Weight" icon-no-border v-model="flow.weights[lanIndex]" />
                    <div class="w-1/6 text-center">{{ weightPercentage(lanIndex) }} %</div>
                    <div class="w-1/6 text-right">
                        <vs-button color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="removeLanding('create', lanIndex)"></vs-button>
                    </div>
                </div>
            </div>
            <div class="w-full text-danger">{{ errors.first('step-5.flow_rules') }}</div>
            <div class="w-full text-danger">{{ errors.first('step-5.flow_landings') }}</div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <vs-button class="w-1/3" icon-pack="feather" icon="icon-plus" @click="addNewLanding()">Add</vs-button>
                    <div class="w-1/3"></div>
                    <div class="w-1/3"></div>
                </div>
            </div>

            <h2>Protection</h2>

            <div class="vx-col w-full mt-5">
              <div class="mb-2">Protection (Fraud Filter)</div>
              <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                <template slot="prepend">
                  <div class="prepend-text">
                    <vs-checkbox v-model="flow.fraud_filter_protection"></vs-checkbox>
                  </div>
                </template>
                <vs-input
                  class="w-full"
                  icon-pack="feather" icon="icon-lock" icon-no-border
                  v-model="flow.safe_page_url"
                  placeholder="Safe page URL (https://...)"
                />
                <vs-input
                  class="w-full"
                  icon-pack="feather" icon="icon-tag" icon-no-border
                  v-model="flow.fraud_filter_campaign_id"
                  placeholder="Campaign ID"
                />
                <vs-input
                  class="w-full"
                  icon-pack="feather" icon="icon-hash" icon-no-border
                  v-model="flow.fraud_filter_campaign_key"
                  placeholder="Campaign Key (1e6b4d4d-cfa8-...)"
                />
              </vx-input-group>
            </div>

          </div>

          <div class="vx-row" v-else>

            <div class="vx-col w-full mt-5">
              <vs-select autocomplete v-model="flow.flow" class="w-full select-large" label="Choose the existing flow to use" v-validate="'required'">
                <vs-select-item :key="index" :value="item" :text="item.name" v-for="(item, index) in flows" class="w-full" />
              </vs-select>
            </div>

          </div>

          </form>
        </tab-content>

        <!-- tab 6 content -->
        <tab-content title="Links" class="mb-5" icon="feather icon-link" :before-change="validateStep6">
          <form data-vv-scope="step-6">

          <div class="vx-row">

            <div class="vx-col w-full mt-5">
              <vs-input icon-pack="feather" icon="icon-layout" icon-no-border label="Promotional URL" v-model="linkBuilder.promotional" class="w-full" disabled="disabled" />
            </div>

            <div class="vx-col w-full mt-5">
              <vs-input icon-pack="feather" icon="icon-hash" icon-no-border label="Postback URL" v-model="linkBuilder.postback" class="w-full" disabled="disabled" />
            </div>

            <div class="vx-col w-full mt-5">
              <vs-textarea v-model="linkBuilder.formCode" disabled height="10rem" />
            </div>

          </div>
          </form>
        </tab-content>
      </form-wizard>
    </div>

    <!-- Forms style preview popup -->
    <vs-popup title="Style preview" :active.sync="showPopup['forms_style_preview']">

        <div class="vx-row mb-6">
            <div class="vx-col w-full">
                <center><h1>Style name: {{ formsStylePreviewObject.name }}</h1></center>
            </div>
            <style-preview class="vx-col w-full" :styleObject="formsStylePreviewObject" />
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
import StylePreview from '../components/StylePreview.vue'

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
  components: {
    FormWizard,
    TabContent,
    StylePreview,
  },
  data () {
    return {
      settings: {},

      users: [],
      networks: [],
      forms: [],
      products: [],
      traffic_sources: [],
      formsStyles: [],

      networksOptions: [],
      formsOptions: [],
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
        'facebook_conversions_api': {
          enabled: false,
          pixel: '',
          access_token: '',
          test_code: '',
          eventValue: '',
          eventCurrency: 'EUR',
        },
        'tiktok': {
          enabled: false,
          pixel: '',
          token: '',
          eventName: 'SubmitForm',
        },
        'google': {
          enabled: false,
          pixel: '',
          eventName: 'Purchase',
        },
        'outbrain': {
          enabled: false,
          pixel: '',
          eventName: 'Purchase',
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
          enabled: true, // Enabled by default
          stylesheet_url: '',
          style_id: '',
          button_text: '',
        },
        'custom': {
          enabled: false,
          afterSubmit: '',
        },
      },
      facebookEventNameOptions: ['Purchase', 'Lead', 'Complete registration'],
      facebookEventCurrencyOptions: ['EUR', 'USD'],
      tiktokEventNameOptions: ['SubmitForm', 'ClickButton'],
      googleEventNameOptions: ['Purchase', 'Lead', 'Complete registration'],
      outbrainEventNameOptions: ['Purchase', 'Lead', 'Complete registration'],

      showPopup: {
        'create': false,
        'edit': false,
        'forms_style_preview': false,
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

      formsStylePreviewObject: { name: '' },
      selectedFormsStyle: null,

      form: null,

      landings: [],
      landing: {
        useExistingOne: false,
        landing: '',
        name: '',
        product: '',
        url: '',
        country: '',
      },
      countryOptions: [ 'IT', 'FR', 'UK', 'US' ],

      domains: [],

      flows: [],
      flow: {
        flow: '',
        useExistingOne: false,
        name: '',
        domain_id: '',
        type: 'hotload',
        path_name: '',
        traffic_source_id: '',
        landings: [''],
        weights: [1],
        safe_page_url: '',
        fraud_filter_campaign_id: '',
        fraud_filter_protection: '',
        fraud_filter_campaign_key: '',
      },

      flowTypes: [
        'hotload',
        'redirect',
      ],

      linkBuilder: {
        promotional: '',
        postback: '',
        formCode: '',
      },
    }
  },
  watch: {
    selectedFormsStyle: function (val) {
      this.plugins['css_style'].stylesheet_url = val.url
      this.plugins['css_style'].style_id = val.id
      this.plugins['css_style'].button_text = val.settings.button_text
      this.plugins['css_style'].countdown_time = val.settings.countdown_time
    },
  },
  computed: {
    canChangeUser() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin'))
    },
    flowPathname() {
      let obj = this.flow
      let flowType = obj.type
      if (flowType == 'redirect') {
        return 'https://zarolead.com/flo/' + obj.path_name
      } else if (flowType == 'hotload') {
        let domain = null
        for (let i = 0; i < this.domains.length; i++) {
          let d = this.domains[i]
          if (d.id == obj.domain_id) {
            domain = d
            break
          }
        }
        if (domain !== null) {
          return 'http://' + domain.domain + '/' + obj.path_name
        }
      }
      return ''
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
      let prod = null
      let prodIndex = this.getObjectIndexById(this.product, this.products)
      if (prodIndex >= 0) {
        prod = this.products[prodIndex]
      }

      this.network_params = []
      this.network_params_values = []
      if (this.network != '' && this.network > 0) {
        let index = this.getObjectIndexById(this.network, this.networks)
        if (index != -1 && this.networks[index].api.hasOwnProperty('form')
            && this.networks[index].api.form.hasOwnProperty('params')) {
          let networkSlug = this.networks[index].slug
          for (const [key, value] of Object.entries(this.networks[index].api.form.params)) {
            this.network_params.push(value)
            this.network_params[this.network_params.length - 1].name = key
            this.network_params[this.network_params.length - 1].validation = this.networks[index].api.form.validation.params[key];

            let val = ''
            if (this.settings !== null && this.settings.hasOwnProperty(networkSlug) && this.settings[networkSlug] !== null) {
              if (this.settings[networkSlug].hasOwnProperty(key) && this.settings[networkSlug][key] !== null) {
                val = this.settings[networkSlug][key]
              }
            }
            if (prod !== null && prod.hasOwnProperty('info') && prod.info !== null) {
              if (prod.info.hasOwnProperty(networkSlug) && prod.info[networkSlug] !== null &&
                  prod.info[networkSlug].hasOwnProperty(key) && prod.info[networkSlug][key] !== null) {
                val = prod.info[networkSlug][key]
              }
            }

            this.network_params_values.push(val)
            this.network_params[this.network_params.length - 1].value = val
          }
        }
      }
    },
    changeTrafficSource() {
      this.flow.traffic_source_id = this.traffic_source
    },
    async getFormsStyles() {
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      const response = await this.$http.get('/api/v1/forms-styles', { headers })
      this.formsStyles = response.data.data
    },
    previewFormsStyle() {
      if (this.selectedFormsStyle == null) return
      this.formsStylePreviewObject = this.selectedFormsStyle.settings
      this.showPopup['forms_style_preview'] = true
    },
    showForm(type) {
      this.showPopup[type] = true
    },
    clearForm(type) {
      if (type == 'cross_sell') {
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
      if (type == 'cross_sell') {
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
    validateStep1() {
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
    validateStep2() {
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
    validateStep3() {
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
    validateStep4() {
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
            this.form = response.data.data
            this.$vs.loading.close()
            this.$vs.notify({
              title: 'Success',
              text: 'Form created',
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'success'
            })
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
    // DEBUG TODO: Old step 5
    /*
    validateStep5() {
      return new Promise((resolve, reject) => {
        this.$vs.loading()
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }

        if (this.landing.useExistingOne) {
          let landing = null
          let landingIndex = this.getObjectIndexById(this.landing.landing, this.landings)
          if (landingIndex >= 0) {
            landing = this.landings[landingIndex]
          }

          if (landing === null) {
            this.$vs.loading.close()
            reject('correct all values')
          }

          let params = {
            default_form: this.form.id,
          }

          this.$http.get('/api/v1/landings/' + landing.id + '/code', { params: params, headers: headers })
            .then((formCodeResponse) => {
              let promotionalUrl = ''
              let formCode = ''

              formCode = formCodeResponse.data.data

              let origin = ''
              let u = new URL(window.location.href)
              origin = u.origin

              u = landing.url
              let separator = '?'
              if (u.includes('?')) {
                separator = '&'
              }
              promotionalUrl = u + separator + 'id=' + this.form.id

              this.linkBuilder.promotional = promotionalUrl
              this.linkBuilder.postback = origin + '/api/v1/postback'
              this.linkBuilder.formCode = formCode

              this.$vs.loading.close()
              resolve(true)
            })
            .catch((error) => {
              this.$vs.loading.close()
              reject('correct all values')
            })
        } else {
          // Create landing
          let data = {
            name: this.landing.name,
            product_id: this.landing.product,
            url: this.landing.url,
            country: this.landing.country,
          }
          this.$http.post('/api/v1/landings', data, { headers: headers })
            .then((response) => {
              this.$vs.notify({
                title: 'Success',
                text: 'Landing created',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })

              let landing = response.data.data

              let params = {
                default_form: this.form.id,
              }
              this.$http.get('/api/v1/landings/' + landing.id + '/code', { params: params, headers: headers })
                .then((formCodeResponse) => {
                  let promotionalUrl = ''
                  let formCode = ''

                  formCode = formCodeResponse.data.data

                  let origin = ''
                  let u = new URL(window.location.href)
                  origin = u.origin

                  u = landing.url
                  let separator = '?'
                  if (u.includes('?')) {
                    separator = '&'
                  }
                  promotionalUrl = u + separator + 'id=' + this.form.id

                  this.linkBuilder.promotional = promotionalUrl
                  this.linkBuilder.postback = origin + '/api/v1/postback'
                  this.linkBuilder.formCode = formCode

                  this.$vs.loading.close()
                  resolve(true)
                })
                .catch((error) => {
                  this.$vs.loading.close()
                  reject('correct all values')
                })
            })
            .catch((error) => {
              let e = []
              if (error !== null && error.hasOwnProperty('response') && error.response !== null &&
                  error.response.hasOwnProperty('data') && error.response.data !== null &&
                  error.response.data.hasOwnProperty('errors') && error.response.data.errors !== null) {
                e = error.response.data.errors
              }
              for (const [key, value] of Object.entries(e)) {
                for (let eIndex = 0; eIndex < value.length; eIndex++) {
                  this.errors.add({
                    scope: 'step-5',
                    field: 'landing_' . key,
                    msg: value[eIndex],
                  })
                }
              }

              this.$vs.notify({
                title: 'Error',
                text: error.message,
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'danger'
              })

              this.$vs.loading.close()
              reject('correct all values')
            })
        }
      })
    },
    */
    validateStep5() {
      return new Promise((resolve, reject) => {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }

        if (this.flow.useExistingOne) {
          let promotionalUrl = ''

          let origin = ''
          let u = new URL(window.location.href)
          origin = u.origin

          promotionalUrl = this.flow.flow.url_domain
          promotionalUrl = promotionalUrl.replace('index.html', '')
          promotionalUrl = promotionalUrl.replace('index.php', '')

          this.linkBuilder.promotional = promotionalUrl
          this.linkBuilder.postback = origin + '/api/v1/postback'
          this.linkBuilder.formCode = ''

          resolve(true)
        } else {
          this.$vs.loading()
          let pathName = this.flow.path_name
          if (pathName.length > 0) {
            if (!pathName.includes(".")) {
              if (this.flow.path_name.slice(-1) != "/") {
                pathName += "/"
              }
              pathName += 'index.php'
            }
          }
          this.flow.path_name = pathName

          let landings = []
          for (let i = 0; i < this.flow.landings.length; i++) {
            let landing = this.flow.landings[i]
            let weight = this.flow.weights[i]
            if (landing == '') {
              continue
            }
            landings.push({
              id: landing,
              weight: parseInt(weight)
            })
          }
          let rules = {
            landings: landings,
            safe_page_url: this.flow.safe_page_url,
            fraud_filter_protection: this.flow.fraud_filter_protection,
            fraud_filter_campaign_id: this.flow.fraud_filter_campaign_id,
            fraud_filter_campaign_key: this.flow.fraud_filter_campaign_key,
          }

          let data = {
            name: this.flow.name,
            type: this.flow.type,
            path_name: this.flow.path_name,
            rules: JSON.stringify(rules),
            traffic_source_id: this.flow.traffic_source_id,
            domain_id: this.flow.domain_id,
          }

          // TODO: to delete
          console.log('rules', rules)
          console.log('data', data)

          // Create flow
          this.$http.post('/api/v1/flows', data, { headers: headers })
            .then((response) => {
              if (response.data.hasOwnProperty('jobs')) {
                for (let i = 0; i < response.data.jobs.length; i++) {
                  let job = response.data.jobs[i]
                  this.$store.commit('addJob', job)
                }
              }

              this.$vs.notify({
                title: 'Success',
                text: 'Flow created (in process)',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })

              let promotionalUrl = ''

              let origin = ''
              let u = new URL(window.location.href)
              origin = u.origin

              promotionalUrl = response.data.data.url_domain
              promotionalUrl = promotionalUrl.replace('index.html', '')
              promotionalUrl = promotionalUrl.replace('index.php', '')

              this.linkBuilder.promotional = promotionalUrl
              this.linkBuilder.postback = origin + '/api/v1/postback'
              this.linkBuilder.formCode = ''

              this.$vs.loading.close()
              resolve(true)
            })
            .catch((error) => {
              let e = []
              if (error !== null && error.hasOwnProperty('response') && error.response !== null &&
                  error.response.hasOwnProperty('data') && error.response.data !== null &&
                  error.response.data.hasOwnProperty('errors') && error.response.data.errors !== null) {
                e = error.response.data.errors
              }
              for (const [key, value] of Object.entries(e)) {
                for (let eIndex = 0; eIndex < value.length; eIndex++) {
                  this.errors.add({
                    scope: 'step-5',
                    field: 'flow_' . key,
                    msg: value[eIndex],
                  })
                }
              }

              this.$vs.notify({
                title: 'Error',
                text: error.message,
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'danger'
              })

              this.$vs.loading.close()
              reject('correct all value')
            })
        }
      })
    },
    validateStep6() {
      return new Promise((resolve, reject) => {
        this.$router.push('/dashboard/forms')
        resolve(true)
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
    changeProduct() {
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

      let eventValue = 0.0
      if (p.hasOwnProperty('quantity') && p.quantity !== null && p.quantity.hasOwnProperty('options') && p.quantity.options !== null) {
        let o = null
        if (p.quantity.options.hasOwnProperty('1')) {
          o = p.quantity.options['1']
        } else {
          for (const [key, value] of Object.entries(p.quantity.options)) {
            o = value
            break
          }
        }
        if (o !== null && o.hasOwnProperty('commission') && o.commission !== null) {
          eventValue = parseFloat(o.commission)
        }
      }
      this.plugins['facebook'].eventValue = eventValue
      this.plugins['facebook_conversions_api'].eventValue = eventValue

      this.landing.product = this.product
    },
    async fetchTrafficSourcesDefaultInfo() {
      let plugins = {
        'Facebook': 'facebook',
        'TikTok': 'tiktok',
        'Google ads': 'google',
        'Outbrain': 'outbrain',
      }

      for (let i = 0; i < this.traffic_sources.length; i++) {
        let ts = this.traffic_sources[i]
        if (plugins.hasOwnProperty(ts.name)) {
          let p = plugins[ts.name]
          if (this.plugins.hasOwnProperty(p)) {
            if (ts.script != null && ts.script.hasOwnProperty('default')) {
              for (const [key, value] of Object.entries(ts.script.default)) {
                if (this.plugins[p].hasOwnProperty(key)) {
                  this.plugins[p][key] = value
                }
              }
            }
          }
        }
      }
    },
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
    addNewLanding() {
      this.flow.landings.push('')
      this.flow.weights.push(1)
    },
    removeLanding(type, index) {
      this.flow.landings.splice(index, 1)
      this.flow.weights.splice(index, 1)
    },
    weightPercentage(index) {
      let totalWeight = 0
      let percentage = 0

      let weights = this.flow.weights
      for (let i = 0; i < weights.length; i++) {
        totalWeight += parseInt(weights[i])
      }

      if (totalWeight > 0 && index >= 0 && index < weights.length) {
        percentage = parseInt(weights[index]) / totalWeight
      }

      percentage *= 100
      return percentage.toFixed(2)
    },
  },
  async created() {
    this.$vs.loading()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve settings
    this.settings = await this.fetchSettings()

    // Retrieve forms styles
    await this.getFormsStyles()
    for (let i = 0; i < this.formsStyles.length; i++) {
      let s = this.formsStyles[i]
      if (s.default_style) {
        this.selectedFormsStyle = s
        break
      }
    }

    // Retrieve users
    try {
      this.users = []
      let params = {
        filter_roles: [3]
      }
      const userResponse = await this.$http.get('/api/v1/users', { headers, params })
      for (let i = 0; i < userResponse.data.data.length; i++) {
        let user = userResponse.data.data[i]
        this.users.push({
          id: user.id,
          label: user.email,
        })
        this.user = user.id;
      }
    } catch (error) {
    }

    // Retrieve traffic sources
    this.traffic_sources = []
    const trafficSourceResponse = await this.$http.get('/api/v1/traffic-sources', { headers })
    for (let i = 0; i < trafficSourceResponse.data.data.length; i++) {
      let ts = trafficSourceResponse.data.data[i]
      this.traffic_sources.push({
        id: ts.id,
        label: ts.name,
        name: ts.name,
        script: ts.script,
      })
      this.traffic_source = ts.id;
    }
    await this.fetchTrafficSourcesDefaultInfo()

    // Retrieve products
    this.products = []
    const productResponse = await this.$http.get('/api/v1/products', { headers })
    for (let i = 0; i < productResponse.data.data.length; i++) {
      let p = productResponse.data.data[i]
      p.label = p.name
      this.products.push(p)
      this.product = p.id;
    }

    // Retrieve networks
    this.networks = []
    const networkResponse = await this.$http.get('/api/v1/networks', { headers })
    this.networks = networkResponse.data.data
    this.networksOptions = []
    for (let i = 0; i < this.networks.length; i++) {
      let network = this.networks[i]
      if (network.enabled) {
        this.networksOptions.push({
          id: network.id,
          label: network.name,
        })
      }
      this.network = network.id;
    }

    // Close loading here, we don't need forms in the first step
    this.$vs.loading.close()

    // Retrieve forms
    const formResponse = await this.$http.get('/api/v1/forms', { headers })
    this.forms = formResponse.data.data
    this.formsOptions = []
    for (let i = 0; i < this.forms.length; i++) {
      let form = this.forms[i]
      this.formsOptions.push({
        id: form.id,
        label: form.name + " - ID: " + form.id,
      })
      this.plugins['double_recovery'].form_id = form.id;
    }

    // Adjust plugins default values
    if (this.settings !== null) {
      if (this.settings.hasOwnProperty('facebook') && this.settings.facebook !== null) {
        for (const [key, value] of Object.entries(this.settings.facebook)) {
          this.plugins['facebook'][key] = value
          this.plugins['facebook_conversions_api'][key] = value
        }
      }
      if (this.settings.hasOwnProperty('tiktok') && this.settings.tiktok !== null) {
        for (const [key, value] of Object.entries(this.settings.tiktok)) {
          this.plugins['tiktok'][key] = value
        }
      }
    }

    // Retrieve landings
    try {
      this.landings = []
      const response = await this.$http.get('/api/v1/landings', { headers })
      this.landings = response.data.data
    } catch (error) {
      // Nothing
    }

    // Retrieve domains
    try {
      this.domains = []
      const response = await this.$http.get('/api/v1/domains', { headers })
      this.domains = response.data.data

      if (this.domains.length > 0) {
        this.flow.domain_id = this.domains[0].id
      }
    } catch (error) {
      // Nothing
    }

    // Retrieve flows
    try {
      this.flows = []
      const response = await this.$http.get('/api/v1/flows', { headers })
      this.flows = response.data.data
    } catch (error) {
      // Nothing
    }
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
