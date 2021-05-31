<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Landings" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create')"></vs-button>
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>
        </p>

        <div v-if="showFilters" class="mb-12">
            <div class="vx-row mb-2">
                <div class="vx-col">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('today')" icon-pack="feather" icon="icon-calendar">Today</vs-button>
                </div>
                <div class="vx-col">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('yesterday')" icon-pack="feather" icon="icon-calendar">Yesterday</vs-button>
                </div>
                <div class="vx-col">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('this_month')" icon-pack="feather" icon="icon-calendar">This Month</vs-button>
                </div>
                <div class="vx-col">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('last_month')" icon-pack="feather" icon="icon-calendar">Last Month</vs-button>
                </div>
            </div>

            <div class="vx-row mb-2">
                <div class="vx-col md:w-1/2 w-full">
                    <label>From</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_from" />
                </div>
                <div class="vx-col md:w-1/2 w-full">
                    <label>To</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_to" />
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col md:w-1/3 w-full">
                    <label>Product</label>
                    <vs-select autocomplete v-model="filters.product">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in products" />
                    </vs-select>
                </div>
            </div>
            <div class="vx-row mt-6">
                <div class="vx-col w-full">
                    <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
                    <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
                </div>
            </div>
        </div>

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

        <vue-context ref="context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.context_menu.close();showLandingsStats(data.id);" class="flex items-center text-sm">
                <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show stats</span>
              </a>
            </li>
            <!--
            <li>
              <a href="#" v-clipboard:copy="data.url" v-clipboard:success="onUrlCopied" class="flex items-center text-sm">
                <feather-icon icon="CopyIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Copy URL</span>
              </a>
            </li>
            -->
            <li>
              <a href="#" @click="$refs.context_menu.close();editForm(data.id);" class="flex items-center text-sm">
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close();showFormCodePopup(data.id);" class="flex items-center text-sm">
                <feather-icon icon="CodeIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show code</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vs-table stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Id</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="product.name">Product</vs-th>
                <vs-th sort-key="visits">Visits</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="total_commission">Commission</vs-th>
                <vs-th sort-key="cr">CR</vs-th>
                <vs-th sort-key="epv">EPV</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td :data="data[indextr].name">
                        <div @contextmenu.prevent="$refs.context_menu.open($event, data[indextr])">
                            <vs-chip>
                                {{data[indextr].name}}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td :data="data[indextr].product.name">
                        {{data[indextr].product.name}}
                    </vs-td>

                    <vs-td :data="data[indextr].visits">
                        {{data[indextr].visits}}
                    </vs-td>

                    <vs-td :data="data[indextr].leads">
                        {{data[indextr].leads}}
                    </vs-td>

                    <vs-td :data="data[indextr].total_commission">
                        € {{data[indextr].total_commission.toFixed(2)}}
                    </vs-td>

                    <vs-td :data="data[indextr].cr">
                        {{data[indextr].cr.toFixed(2)}} %
                    </vs-td>

                    <vs-td :data="data[indextr].epv">
                        <vs-chip transparent :color="(data[indextr].epv > 0) ? 'success' : 'danger'">€ {{data[indextr].epv.toFixed(2)}}</vs-chip>
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td>
                    {{totalStats.visits}}
                </vs-td>
                <vs-td>
                    {{totalStats.leads}}
                </vs-td>
                <vs-td>
                    € {{totalStats.total_commission.toFixed(2)}}
                </vs-td>
                <vs-td>
                    {{totalStats.cr.toFixed(2)}} %
                </vs-td>
                <vs-td>
                    <vs-chip transparent :color="(totalStats.epv > 0) ? 'success' : 'danger'">€ {{totalStats.epv.toFixed(2)}}</vs-chip>
                </vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>

        <!-- Create popup -->
        <vs-popup title="Create Landing" :active.sync="showPopup['create']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" color="primary">{{newObject['create'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Product</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <v-select :options="products" v-model="newObject['create'].product" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>URL</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-link" icon-no-border v-model="newObject['create'].url" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Country</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['create'].country" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Edit Landing" :active.sync="showPopup['edit']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" color="primary">{{newObject['edit'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Products</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <v-select :options="products" v-model="newObject['edit'].product" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>URL</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-link" icon-no-border v-model="newObject['edit'].url" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Country</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['edit'].country" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateObject" color="primary" type="filled" :disabled="!isValidForm('edit')">Update</vs-button>
            </div>
        </vs-popup>

        <!-- Show code popup -->
        <vs-popup title="Form code snippet" :active.sync="showPopup['code']">
            <div class="vx-row mb-2">
                <div class="vx-col w-1/3">
                </div>
                <div class="vx-col w-1/3">
                    <vs-select autocomplete label="Default form" v-model="defaultFormId" @input="getFormCode">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in forms" />
                    </vs-select>
                </div>
                <div class="vx-col w-1/3">
                </div>
            </div>
            <vs-textarea v-model="formCode" disabled height="10rem" />
            <div class="vx-row mt-6">
                <vs-button class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-copy" v-clipboard:copy="formCode" v-clipboard:success="onCodeCopied">Copy to clipboard</vs-button>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import { VueContext } from 'vue-context'

export default {
  components: {
    'v-select': vSelect,
    VueContext,
  },
  data () {
    return {
      objects: [],
      selected: [],
      deleted: [],

      currentObject: null,

      maxItems: 20,

      editingObject: -1,

      showPopup: {
        'create': false,
        'edit': false,
        'code': false,
      },
      newObject: {
        'create': {
          name: '',
          product: '',
          url: '',
          country: '',
        },
        'edit': {
          name: '',
          product: '',
          url: '',
          country: '',
        },
      },
      dateFormat: 'yyyy-MM-dd',
      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        product: '',
      },
      totalStats: {
        visits: 0,
        leads: 0,
        total_commission: 0,
        cr: 0,
        epv: 0,
      },

      tags: [],
      campaigns: [],
      products: [],

      forms: [],
      formCodeId: -1,
      landingCodeId: -1,
      formCode: '',
      defaultFormId: -1,
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
  },
  methods: {
    getObjectIndexById(id) {
      let index = -1
      this.objects.forEach((obj, i) => {
        if (obj.id == id) {
          index = i
          return
        }
      })
      return index
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        product: '',
      }
      this.filterDatesShortcut('this_month')
    },
    async applyFilters() {
      this.selected = []
      this.objects = []
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {}
      if (this.filters.date_from != '') {
        data['filter_date_from'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        data['filter_date_to'] = this.filters.date_to
      }
      if (this.filters.product != '') {
        data['filter_product'] = this.filters.product
      }

      // Retrieve landings
      const response = await this.$http.get('/api/v1/landings', { headers, params: data })
      response.data.data.forEach((obj) => {
        let toPush = obj

        toPush.visits = 0
        toPush.leads = 0
        toPush.total_commission = 0.0
        toPush.cr = 0.0
        toPush.epv = 0.0

        obj.daily_stats.forEach((stats) => {
          toPush.visits += parseInt(stats.visits)
          toPush.leads += parseInt(stats.leads)
          toPush.total_commission += parseInt(stats.total_commission)
        })

        toPush.cr = (toPush.visits > 0) ? (toPush.leads / toPush.visits)*100 : ((toPush.leads > 0) ? 9999.99 : 0)
        toPush.epv = (toPush.visits > 0) ? (toPush.total_commission / toPush.visits) : (toPush.total_commission)
        this.objects.push(toPush)
      })

      this.calculateStats()
      this.$vs.loading.close()
    },
    filterDatesShortcut(period) {
      let invalidPeriod = false;

      switch (period) {
        case 'today': {
          let today = new Date();
          this.filters.date_from = this.formatDate(today);
          this.filters.date_to = this.formatDate(today);
        } break;
        case 'yesterday': {
          let dateObj = new Date();
          dateObj.setDate(dateObj.getDate() - 1);
          this.filters.date_from = this.formatDate(dateObj);
          this.filters.date_to = this.formatDate(dateObj);
        } break;
        case 'this_month': {
          let dateObj = new Date();
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1);
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0);
          this.filters.date_from = this.formatDate(fromObj);
          this.filters.date_to = this.formatDate(toObj);
        } break;
        case 'last_month': {
          let dateObj = new Date();
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth() - 1, 1);
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 0);
          this.filters.date_from = this.formatDate(fromObj);
          this.filters.date_to = this.formatDate(toObj);
        } break;
        default: {
          invalidPeriod = true;
        } break;
      }

      if(!invalidPeriod) {
        this.applyFilters();
      }
    },
    formatDate(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0');
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0'); //January is 0!
      let yyyy = dateObj.getFullYear();
      
      let formattedDate = yyyy + '-' + mm + '-' + dd;
      return formattedDate;
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

      this.selected.forEach((landing) => {
        this.$http.delete('/api/v1/landings/' + landing.id, { headers })
          .then((response) => {
            this.deleted.push(landing.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Landing(s) deleted',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.selected = []
              this.deleted = []
              this.applyFilters()
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
      this.newObject[type] = {
        name: '',
        product: '',
        url: '',
        country: '',
      }
    },
    isValidForm(type) {
      let obj = this.newObject[type]
      return (obj.name.length > 0
        && obj.product != '' && obj.product.id >= 1
          && obj.url.length > 0)
    },
    createObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.newObject['create'].name,
        product_id: this.newObject['create'].product.id,
        url: this.newObject['create'].url,
        country: this.newObject['create'].country,
      }

      // Retrieve landings
      this.$http.post('/api/v1/landings', data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Landing created',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.clearForm('create')
          this.showPopup['create'] = false
          this.applyFilters()
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
    editForm(id) {
      let obj = this.objects[this.getObjectIndexById(id)]
      this.newObject['edit'] = {
        name: obj.name,
        product: {
          id: obj.product.id,
          label: obj.product.name,
        },
        url: obj.url,
        country: obj.country,
      }
      this.editingObject = obj.id
      this.showForm('edit')
    },
    updateObject() {
      if (this.editingObject <= 0) return

      let id = this.editingObject

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        name: this.newObject['edit'].name,
        product_id: this.newObject['edit'].product.id,
        url: this.newObject['edit'].url,
        country: this.newObject['edit'].country,
      }

      this.$http.put('/api/v1/landings/' + id, data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Landing updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.applyFilters()
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
      this.showPopup['edit'] = false
      this.editingObject = -1
    },
    onUrlCopied() {
      this.$refs.context_menu.close()
      this.$vs.notify({
        title: 'Success',
        text: 'URL copied',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    getFormCode() {
      this.$vs.loading()
      let id = this.landingCodeId

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
          default_form: this.defaultFormId,
      }

      // Retrieve form code snippet
      this.$http.get('/api/v1/landings/' + id + '/code', { params: data, headers: headers })
        .then((response) => {
          this.formCode = response.data.data
          this.$vs.loading.close()
          this.showPopup['code'] = true
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
    showFormCodePopup(id) {
      this.landingCodeId = id
      if (this.formCodeId <= 0 && this.forms.length > 0) {
        this.formCodeId = this.forms[0].id
      }
      this.getFormCode()
    },
    onCodeCopied() {
      this.$vs.notify({
        title: 'Success',
        text: 'Form code copied',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    showLandingsStats(id) {
      this.$router.push({name: 'landings-stats', params: {landing_id: id}})
        .catch(error => {
          this.$router.push({name: 'landings-stats', params: {landing_id: id}})
        })
    },
    calculateStats() {
      this.totalStats = {
        visits: 0,
        leads: 0,
        total_commission: 0,
        cr: 0,
        epv: 0,
      }

      let visits = 0
      let leads = 0
      let total_commission = 0

      for (let i = 0; i < this.objects.length; i++) {
        visits += this.objects[i].visits
        leads += this.objects[i].leads
        total_commission += this.objects[i].total_commission
      }

      let cr = (visits > 0) ? (leads / visits)*100 : ((leads > 0) ? 9999.99 : 0)
      let epv = (visits > 0) ? (total_commission / visits) : (total_commission)

      this.totalStats.visits = visits
      this.totalStats.leads = leads
      this.totalStats.total_commission = total_commission
      this.totalStats.cr = cr
      this.totalStats.epv = epv
    },
  },
  async created() {
    this.resetFilters()

    this.$vs.loading()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve tags
    const responseTags = await this.$http.get('/api/v1/tags/', { headers })
    this.tags = []
    responseTags.data.data.forEach((tag) => {
      this.tags.push({
        id: tag.id,
        label: tag.name,
      })
    })

    // Retrieve products
    const responseProducts = await this.$http.get('/api/v1/products/', { headers })
    this.products = []
    responseProducts.data.data.forEach((product) => {
      this.products.push({
        id: product.id,
        label: product.name,
      })
    })

    // Retrieve forms
    let today = new Date();
    let d = this.formatDate(today)
    let data = {
      filter_date_from: d,
      filter_date_to: d,
    }
    const responseForms = await this.$http.get('/api/v1/forms/', { headers, params: data })
    this.forms = []
    responseForms.data.data.forEach((form) => {
      this.forms.push({
        id: form.id,
        label: form.name,
      })
      this.defaultFormId = form.id
    })

    this.$vs.loading.close()
  },
  mounted () {
  }
}

</script>
