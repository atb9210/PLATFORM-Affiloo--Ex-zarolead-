<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Flows" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create')"></vs-button>
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>
        </p>

        <vs-dropdown vs-trigger-click class="cursor-pointer">
            <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
                <span class="mr-2">{{ currentPage * maxItems - (maxItems - 1) }} - {{ objects.length - currentPage * maxItems > 0 ? currentPage * maxItems : objects.length }} of {{ objects.length }}</span>
                <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
            </div>
            <vs-dropdown-menu>
                <vs-dropdown-item @click="maxItems = 20">
                    <span>20</span>
                </vs-dropdown-item>
                <vs-dropdown-item @click="maxItems = 50">
                    <span>50</span>
                </vs-dropdown-item>
                <vs-dropdown-item @click="maxItems = 100">
                    <span>100</span>
                </vs-dropdown-item>
                <vs-dropdown-item @click="maxItems = 150">
                    <span>150</span>
                </vs-dropdown-item>
            </vs-dropdown-menu>
        </vs-dropdown>

        <vs-table stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Id</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="url_domain">Link</vs-th>
                <vs-th>Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td :data="data[indextr].name">
                        {{data[indextr].name}}
                    </vs-td>

                    <vs-td :data="data[indextr].url_domain">
                        <a :href="data[indextr].url_domain" target="_blank">{{data[indextr].url_domain}}</a>
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center">
                            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-link" v-clipboard:copy="data[indextr].url_domain" v-clipboard:success="onUrlCopied"></vs-button>
                            <vs-button @click="showDuplicatePopup(indextr)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-copy"></vs-button>
                            <vs-button @click="editForm(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-edit"></vs-button>
                            <vs-button @click="deleteObject(data[indextr].id)" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash"></vs-button>
                        </div>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Create popup -->
        <vs-popup title="Create Flow" :active.sync="showPopup['create']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" color="primary">{{newObject['create'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].name" />
                    <span class="w-full text-danger">{{ errors.first('name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Domain</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['create'].domain" class="w-full select-large" v-validate="'required'">
                        <vs-select-item :key="index" :value="item" :text="item.domain" v-for="(item, index) in domains" class="w-full" />
                    </vs-select>
                    <span class="w-full text-danger">{{ errors.first('domain_id') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Type</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select v-model="newObject['create'].type" class="w-full select-large" v-validate="'required'">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in typeOptions" class="w-full" />
                    </vs-select>
                    <span class="w-full text-danger">{{ errors.first('type') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Path name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-folder" icon-no-border v-model="newObject['create'].path_name" />
                    <span>{{flowPathname}}</span>
                    <span class="w-full text-danger">{{ errors.first('path_name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Traffic Source</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['create'].traffic_source" class="w-full select-large" v-validate="'required'">
                        <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in trafficSources" class="w-full" />
                    </vs-select>
                    <span class="w-full text-danger">{{ errors.first('traffic_source_id') }}</span>
                </div>
            </div>

            <div class="vx-row mb-2" v-for="(lan, indexparam) in newObject['create'].landings">
                <div class="vx-col w-1/2 mb-2 text-center" v-if="indexparam == 0">
                    <span>Landings</span>
                </div>
                <div class="vx-col w-1/6 mb-2 text-center" v-if="indexparam == 0">
                    <span>Weight</span>
                </div>
                <div class="vx-col w-1/6 mb-2 text-center" v-if="indexparam == 0">
                    <span>%</span>
                </div>
                <div class="vx-col w-1/6 mb-2 text-center" v-if="indexparam == 0">
                </div>
                <div class="vx-col w-full flex items-center">
                    <vs-select autocomplete class="w-1/2 select-large" v-model="newObject['create'].landings[indexparam]" v-validate="'required'">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in landingOptions" class="w-full" />
                    </vs-select>
                    <vs-input class="w-1/6" type="number" min="0" icon-pack="feather" icon="icon-percent" placeholder="Weight" icon-no-border v-model="newObject['create'].weights[indexparam]" />
                    <div class="w-1/6 text-center">{{ weightPercentage('create', indexparam) }} %</div>
                    <div class="w-1/6 text-right">
                        <vs-button color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="removeLanding('create', indexparam)"></vs-button>
                    </div>
                </div>
            </div>
            <div class="w-full text-danger">{{ errors.first('rules') }}</div>
            <div class="w-full text-danger">{{ errors.first('landings') }}</div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <vs-button class="w-1/3" icon-pack="feather" icon="icon-plus" @click="addNewLanding('create')">Add</vs-button>
                    <div class="w-1/3"></div>
                    <div class="w-1/3"></div>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Safe Page URL</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="url" placeholder="https://..." class="w-full" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['create'].safe_page_url" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>FraudFilter</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <vs-input placeholder="Campaign ID" class="w-1/2" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create'].fraud_filter_campaign_id" />
                    <vs-checkbox class="w-1/2" v-model="newObject['create'].fraud_filter_protection">Enabled</vs-checkbox>
                </div>
                <div class="vx-col sm:w-1/3 w-full">
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <vs-input placeholder="Campaign Key (1e6b4d4d-cfa8-...)" class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create'].fraud_filter_campaign_key" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Edit Flow" :active.sync="showPopup['edit']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" color="primary">{{newObject['edit'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].name" />
                    <span class="w-full text-danger">{{ errors.first('name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Domain</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['edit'].domain" class="w-full select-large" v-validate="'required'" disabled="true">
                        <vs-select-item :key="index" :value="item" :text="item.domain" v-for="(item, index) in domains" class="w-full" />
                    </vs-select>
                    <span class="w-full text-danger">{{ errors.first('domain_id') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Type</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select v-model="newObject['edit'].type" class="w-full select-large" v-validate="'required'" disabled="true">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in typeOptions" class="w-full" />
                    </vs-select>
                    <span class="w-full text-danger">{{ errors.first('type') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Path name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-folder" icon-no-border v-model="newObject['edit'].path_name" disabled="true" />
                    <span>{{flowPathname}}</span>
                    <span class="w-full text-danger">{{ errors.first('path_name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Traffic Source</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['edit'].traffic_source" class="w-full select-large" v-validate="'required'">
                        <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in trafficSources" class="w-full" />
                    </vs-select>
                    <span class="w-full text-danger">{{ errors.first('traffic_source_id') }}</span>
                </div>
            </div>

            <div class="vx-row mb-2" v-for="(lan, indexparam) in newObject['edit'].landings">
                <div class="vx-col w-1/2 mb-2 text-center" v-if="indexparam == 0">
                    <span>Landings</span>
                </div>
                <div class="vx-col w-1/6 mb-2 text-center" v-if="indexparam == 0">
                    <span>Weight</span>
                </div>
                <div class="vx-col w-1/6 mb-2 text-center" v-if="indexparam == 0">
                    <span>%</span>
                </div>
                <div class="vx-col w-1/6 mb-2 text-center" v-if="indexparam == 0">
                </div>
                <div class="vx-col w-full flex items-center">
                    <vs-select autocomplete class="w-1/2 select-large" v-model="newObject['edit'].landings[indexparam]" v-validate="'required'">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in landingOptions" class="w-full" />
                    </vs-select>
                    <vs-input class="w-1/6" type="number" min="0" icon-pack="feather" icon="icon-percent" placeholder="Weight" icon-no-border v-model="newObject['edit'].weights[indexparam]" />
                    <div class="w-1/6 text-center">{{ weightPercentage('edit', indexparam) }} %</div>
                    <div class="w-1/6 text-right">
                        <vs-button color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="removeLanding('edit', indexparam)"></vs-button>
                    </div>
                </div>
            </div>
            <div class="w-full text-danger">{{ errors.first('rules') }}</div>
            <div class="w-full text-danger">{{ errors.first('landings') }}</div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <vs-button class="w-1/3" icon-pack="feather" icon="icon-plus" @click="addNewLanding('edit')">Add</vs-button>
                    <div class="w-1/3"></div>
                    <div class="w-1/3"></div>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Safe Page URL</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="url" placeholder="https://..." class="w-full" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['edit'].safe_page_url" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>FraudFilter</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <vs-input placeholder="Campaign ID" class="w-1/2" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['edit'].fraud_filter_campaign_id" />
                    <vs-checkbox class="w-1/2" v-model="newObject['edit'].fraud_filter_protection">Enabled</vs-checkbox>
                </div>
                <div class="vx-col sm:w-1/3 w-full">
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <vs-input placeholder="Campaign Key (1e6b4d4d-cfa8-...)" class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['edit'].fraud_filter_campaign_key" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateObject" color="primary" type="filled" :disabled="!isValidForm('edit')">Update</vs-button>
            </div>
        </vs-popup>

        <!-- Duplicate popup -->
        <vs-popup title="Duplicate Flow" :active.sync="showPopup['duplicate']">
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Path name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-folder" icon-no-border v-model="duplicatePathName" />
                    <span>{{flowPathname}}</span>
                    <span class="w-full text-danger">{{ errors.first('path_name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="duplicateObject" color="primary" type="filled" :disabled="!isValidDuplicatePopup()">Duplicate</vs-button>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import vSelect from 'vue-select'

export default {
  components: {
    'v-select': vSelect,
  },
  data () {
    return {
      objects: [],
      selected: [],
      deleted: [],

      maxItems: 20,

      trafficSources: [],
      firstTrafficSourceId: 0,
      domains: [],

      editingObject: -1,

      showPopup: {
        'create': false,
        'edit': false,
        'duplicate': false,
      },
      newObject: {
        'create': {
          name: '',
          type: 'redirect',
          path_name: '',
          traffic_source: 0,
          landings: [
            {
              id: '',
            },
          ],
          weights: [
            1,
          ],
          domain: {},
          safe_page_url: '',
          fraud_filter_protection: false,
          fraud_filter_campaign_id: '',
          fraud_filter_campaign_key: '',
        },
        'edit': {
          name: '',
          type: 'redirect',
          path_name: '',
          traffic_source: 0,
          landings: [
            {
              id: '',
            },
          ],
          weights: [
            1,
          ],
          domain: {},
          safe_page_url: '',
          fraud_filter_protection: false,
          fraud_filter_campaign_id: '',
          fraud_filter_campaign_key: '',
        },
      },

      landings: [],
      landingOptions: [],

      typeOptions: [
        'redirect', 'hotload',
      ],

      duplicateObjectIndex: 0,
      duplicatePathName: '',
    }
  },
  computed: {
    paginationPageSize () {
      if (this.gridApi) return this.gridApi.paginationGetPageSize()
      else return 50
    },
    totalPages () {
      if (this.gridApi) return this.gridApi.paginationGetTotalPages()
      else return 0
    },
    currentPage: {
      get () {
        if (this.gridApi) return this.gridApi.paginationGetCurrentPage() + 1
        else return 1
      },
      set (val) {
        this.gridApi.paginationGoToPage(val - 1)
      }
    },
    formType() {
      if (this.showPopup['create']) {
        return 'create'
      } else if (this.showPopup['edit']) {
        return 'edit'
      } else if (this.showPopup['duplicate']) {
        return 'duplicate'
      }
      return 'create'
    },
    flowPathname() {
      let obj = this.newObject[this.formType]
      let flowType = obj.type
      if (flowType == 'redirect') {
        return 'https://zarolead.com/flo/' + obj.path_name
      } else if (flowType == 'hotload') {
        return 'http://' + obj.domain.domain + '/' + obj.path_name
      }
      return ''
    },
  },
  methods: {
    getObjectIndexById(id) {
      var index = -1;
      this.objects.forEach((obj, i) => {
        if (obj.id == id) {
          index = i
          return
        }
      })
      return index
    },
    retrieveObjects() {
      this.selected = []
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Retrieve flows
      this.$http.get('/api/v1/flows', { headers })
        .then((response) => {
          this.objects = response.data.data
          for (let i = 0; i < this.objects.length; i++) {
            if (this.objects[i].rules == null) {
              this.objects[i].rules = {
                landings: [],
              }
            }
            if (this.objects[i].domains == null) {
              this.objects[i].domains = {
                list: [],
                selected: 0,
              }
            }
            this.objects[i].url_domain = this.objects[i].url_domain.replace('index.html', '')
            this.objects[i].url_domain = this.objects[i].url_domain.replace('index.php', '')
          }
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
    onPageChanged(val) {
      this.currentPage = val
    },
    confirmDeletePopup() {
      if (this.selected.length == 0) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Delete confirmation',
        text: 'Are you sure you want to delete ' + this.selected.length + ' object(s)?',
        accept: this.deleteSelected
      })
    },
    deleteSelected() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      this.deleted = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.selected.forEach((flow) => {
        this.$http.delete('/api/v1/flows/' + flow.id, { headers })
          .then((response) => {
            this.deleted.push(flow.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Flow(s) deleted',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.selected = []
              this.deleted = []
              this.retrieveObjects()
            }
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
      })
    },
    showForm(type) {
      this.showPopup[type] = true
    },
    clearForm(type) {
      let defaultLanding = 0
      if (this.landingOptions.length > 0) {
        defaultLanding = this.landingOptions[0].id
      }
      this.newObject[type] = {
        name: '',
        type: 'redirect',
        path_name: '',
        traffic_source: this.firstTrafficSourceId,
        landings: [
          defaultLanding,
        ],
        weights: [
          1,
        ],
        domain: {},
        safe_page_url: '',
        fraud_filter_protection: false,
        fraud_filter_campaign_id: '',
        fraud_filter_campaign_key: '',
      }
    },
    isValidForm(type) {
      let obj = this.newObject[type]

      let landings = obj.landings
      let correctRules = (landings.length > 0)

      let weights = obj.weights
      for (let i = 0; i < landings.length; i++) {
        let landing = landings[i]
        let weight = weights[i]
        correctRules = (correctRules && (parseInt(weight) >= 0))
      }

      if (obj.fraud_filter_protection) {
        correctRules = (correctRules && obj.safe_page_url != '' &&
          obj.fraud_filter_campaign_id != '' && obj.fraud_filter_campaign_key != '')
      }

      return (obj.name.length > 0 && this.typeOptions.includes(obj.type) && correctRules)
    },
    async createObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let pathName = this.newObject['create'].path_name
      if (pathName.length > 0) {
        if (!pathName.includes(".")) {
          if (this.newObject['create'].path_name.slice(-1) != "/") {
            pathName += "/"
          }
          pathName += 'index.php'
        }
      }
      this.newObject['create'].path_name = pathName

      let landings = []
      for (let i = 0; i < this.newObject['create'].landings.length; i++) {
        let landing = this.newObject['create'].landings[i]
        let weight = this.newObject['create'].weights[i]
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
        safe_page_url: this.newObject['create'].safe_page_url,
        fraud_filter_protection: this.newObject['create'].fraud_filter_protection,
        fraud_filter_campaign_id: this.newObject['create'].fraud_filter_campaign_id,
        fraud_filter_campaign_key: this.newObject['create'].fraud_filter_campaign_key,
      }

      let data = {
        name: this.newObject['create'].name,
        type: this.newObject['create'].type,
        path_name: pathName,
        rules: JSON.stringify(rules),
        traffic_source_id: this.newObject['create'].traffic_source,
        domain_id: this.newObject['create'].domain.id,
      }

      const response = await this.$http.post('/api/v1/flows', data, { headers: headers })

      if (response.data.hasOwnProperty('jobs')) {
        for (let i = 0; i < response.data.jobs.length; i++) {
          let job = response.data.jobs[i]
          await this.$store.commit('addJob', job)
        }
      }

      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Flow created',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.errors.clear()
      this.clearForm('create')
      this.showPopup['create'] = false
      this.retrieveObjects()

      // TODO: Show errors
      //.catch((error) => {
      //  this.$vs.loading.close()
      //  this.errors.clear()
      //  if (error.response.hasOwnProperty('data') && error.response.data.hasOwnProperty('errors')) {
      //    for (const [key, value] of Object.entries(error.response.data.errors)) {
      //      for (let i = 0; i < value.length; i++) {
      //        this.errors.add({
      //          field: key,
      //          msg: value[i]
      //        })
      //      }
      //    }
      //  }
      //})
    },
    isValidDuplicatePopup() {
      return this.duplicatePathName != ''
    },
    duplicateObject(index) {
      if (this.duplicateObjectIndex < 0 || this.duplicateObjectIndex >= this.objects.length) return

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let obj = this.objects[this.duplicateObjectIndex]

      let pathName = this.duplicatePathName
      if (pathName.length > 0) {
        if (!pathName.includes(".")) {
          if (this.duplicatePathName.slice(-1) != "/") {
            pathName += "/"
          }
          pathName += 'index.php'
        }
      }

      let data = {
        name: obj.name,
        type: obj.type,
        rules: JSON.stringify(obj.rules),
        domain_id: obj.domain.id,
        path_name: pathName,
        traffic_source_id: obj.traffic_source.id,
      }

      this.$http.post('/api/v1/flows', data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Flow duplicated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.errors.clear()
          this.duplicatePathName = ''
          this.showPopup['duplicate'] = false
          this.retrieveObjects()
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.errors.clear()
          if (error.response.hasOwnProperty('data') && error.response.data.hasOwnProperty('errors')) {
            for (const [key, value] of Object.entries(error.response.data.errors)) {
              for (let i = 0; i < value.length; i++) {
                this.errors.add({
                  field: key,
                  msg: value[i]
                })
              }
            }
          }
        })
    },
    editForm(id) {
      let obj = this.objects[this.getObjectIndexById(id)]

      let landings = []
      let weights = []
      if (obj.rules.hasOwnProperty('landings') && obj.rules.landings.length > 0) {
        for (let i = 0; i < obj.rules.landings.length; i++) {
          landings.push(obj.rules.landings[i].id)
          weights.push(obj.rules.landings[i].weight)
        }
      }

      if (landings.length == 0) {
        landings.push('')
        weights.push(1)
      }

      let domain = {}
      for (let i = 0; i < this.domains.length; i++) {
        if (obj.domain.id == this.domains[i].id) {
          domain = this.domains[i]
          break
        }
      }

      let safePageUrl = ''
      if (obj.rules.hasOwnProperty('safe_page_url')) {
        safePageUrl = obj.rules.safe_page_url
      }

      let fraudFilterProtection = false
      if (obj.rules.hasOwnProperty('fraud_filter_protection')) {
        fraudFilterProtection = obj.rules.fraud_filter_protection
      }

      let fraudFilterCampaignId = ''
      if (obj.rules.hasOwnProperty('fraud_filter_campaign_id')) {
        fraudFilterCampaignId = obj.rules.fraud_filter_campaign_id
      }

      let fraudFilterCampaignKey = ''
      if (obj.rules.hasOwnProperty('fraud_filter_campaign_key')) {
        fraudFilterCampaignId = obj.rules.fraud_filter_campaign_key
      }

      this.newObject['edit'] = {
        name: obj.name,
        type: obj.type,
        path_name: obj.path_name,
        landings: landings,
        weights: weights,
        traffic_source: obj.traffic_source.id,
        domain: domain,
        safe_page_url: safePageUrl,
        fraud_filter_protection: fraudFilterProtection,
        fraud_filter_campaign_id: fraudFilterCampaignId,
        fraud_filter_campaign_key: fraudFilterCampaignKey,
      }
      this.editingObject = obj.id
      this.errors.clear()
      this.showForm('edit')
    },
    async updateObject() {
      if (this.editingObject <= 0) return

      let id = this.editingObject

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let pathName = this.newObject['edit'].path_name
      if (pathName.length > 0) {
        if (!pathName.includes(".")) {
          if (this.newObject['edit'].path_name.slice(-1) != "/") {
            pathName += "/"
          }
          pathName += 'index.php'
        }
      }
      this.newObject['edit'].path_name = pathName

      let landings = []
      for (let i = 0; i < this.newObject['edit'].landings.length; i++) {
        let landing = this.newObject['edit'].landings[i]
        let weight = this.newObject['edit'].weights[i]
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
        safe_page_url: this.newObject['edit'].safe_page_url,
        fraud_filter_protection: this.newObject['edit'].fraud_filter_protection,
        fraud_filter_campaign_id: this.newObject['edit'].fraud_filter_campaign_id,
        fraud_filter_campaign_key: this.newObject['edit'].fraud_filter_campaign_key,
      }

      let data = {
        name: this.newObject['edit'].name,
        traffic_source_id: this.newObject['edit'].traffic_source,
        rules: JSON.stringify(rules),
      }

      const response = await this.$http.put('/api/v1/flows/' + id, data, { headers: headers })
      if (response.data.hasOwnProperty('jobs')) {
        for (let i = 0; i < response.data.jobs.length; i++) {
          let job = response.data.jobs[i]
          await this.$store.commit('addJob', job)
        }
      }

      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Flow updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.errors.clear()
      this.clearForm('edit')
      this.showPopup['edit'] = false
      this.editingObject = -1
      this.retrieveObjects()

      // TODO: Show errors
      //.catch((error) => {
      //  this.$vs.loading.close()
      //  if (error.response.hasOwnProperty('data') && error.response.data.hasOwnProperty('errors')) {
      //    for (const [key, value] of Object.entries(error.response.data.errors)) {
      //      for (let i = 0; i < value.length; i++) {
      //        this.errors.add({
      //          field: key,
      //          msg: value[i]
      //        })
      //      }
      //    }
      //  }
      //})
    },
    deleteObject(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$http.delete('/api/v1/flows/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Flow deleted',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.retrieveObjects()
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
    addNewLanding(type) {
      this.newObject[type].landings.push('')
      this.newObject[type].weights.push(1)
    },
    removeLanding(type, index) {
      this.newObject[type].landings.splice(index, 1)
      this.newObject[type].weights.splice(index, 1)
    },
    weightPercentage(type, index) {
      let totalWeight = 0
      let percentage = 0

      let weights = this.newObject[type].weights
      for (let i = 0; i < weights.length; i++) {
        totalWeight += parseInt(weights[i])
      }

      if (totalWeight > 0 && index >= 0 && index < weights.length) {
        percentage = parseInt(weights[index]) / totalWeight
      }

      percentage *= 100
      return percentage.toFixed(2)
    },
    onUrlCopied() {
      this.$vs.notify({
        title: 'Success',
        text: 'Link copied',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    showDuplicatePopup(index) {
      this.duplicateObjectIndex = index
      this.showPopup['duplicate'] = true
    },
  },
  created() {
    this.retrieveObjects()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve landings
    this.$vs.loading()
    this.$http.get('/api/v1/landings', { headers })
      .then((response) => {
        this.landings = response.data.data

        this.landingOptions = []
        this.landings.forEach((landing) => {
          this.landingOptions.push({
            id: landing.id,
            label: landing.name,
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

    // Retrieve traffic sources
    this.$vs.loading()
    this.$http.get('/api/v1/traffic-sources', { headers })
      .then((response) => {
        this.trafficSources = response.data.data
        if (this.trafficSources.length > 0) {
          this.firstTrafficSourceId = this.trafficSources[0].id
          this.newObject['create'].traffic_source = this.newObject['edit'].traffic_source = this.firstTrafficSourceId
        }
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

    // Retrieve domains
    this.$vs.loading()
    this.$http.get('/api/v1/domains', { headers })
      .then((response) => {
        this.domains = response.data.data
        if (this.domains.length > 0) {
          this.newObject['create'].domain = this.newObject['edit'].domain = this.domains[0]
        }
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
  mounted () {
  }
}

</script>
