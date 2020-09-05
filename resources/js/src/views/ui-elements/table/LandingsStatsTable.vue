<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Landing Stats" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button> 
        </p>

        <div v-if="showFilters" class="mb-12">
            <div class="vx-row mb-2">
                <div class="vx-col w-3-12">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('today')" icon-pack="feather" icon="icon-calendar">Today</vs-button>
                </div>
                <div class="vx-col w-3-12">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('yesterday')" icon-pack="feather" icon="icon-calendar">Yesterday</vs-button>
                </div>
                <div class="vx-col w-3-12">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('this_month')" icon-pack="feather" icon="icon-calendar">This Month</vs-button>
                </div>
                <div class="vx-col w-3-12">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('last_month')" icon-pack="feather" icon="icon-calendar">Last Month</vs-button>
                </div>
            </div>

            <div class="vx-row mb-2">
                <div class="vx-col w-full">
                    <label>From</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_from" />
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col w-full">
                    <label>To</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_to" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                </div>
            </div>
            <div class="vx-row">
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

        <vs-table stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">ID</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="visits">Visits</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="total_commission" v-if="canViewStats">Commission</vs-th>
                <vs-th sort-key="cr">CR</vs-th>
                <vs-th sort-key="epv" v-if="canViewStats">EPV</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td :data="data[indextr].day">
                        {{data[indextr].name}}
                    </vs-td>

                    <vs-td :data="data[indextr].visits">
                        {{data[indextr].visits}}
                    </vs-td>

                    <vs-td :data="data[indextr].leads">
                        {{data[indextr].leads}}
                    </vs-td>

                    <vs-td :data="data[indextr].total_commission" v-if="canViewStats">
                        € {{data[indextr].total_commission}}
                    </vs-td>

                    <vs-td :data="data[indextr].cr">
                        {{data[indextr].cr}} %
                    </vs-td>

                    <vs-td :data="data[indextr].epv" v-if="canViewStats">
                        <vs-chip transparent :color="(data[indextr].epv > 0) ? 'success' : 'danger'">€ {{data[indextr].epv}}</vs-chip>
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td>
                    {{total_stats.visits}}
                </vs-td>
                <vs-td>
                    {{total_stats.leads}}
                </vs-td>
                <vs-td v-if="canViewStats">
                    € {{total_stats.total_commission}}
                </vs-td>
                <vs-td>
                    {{total_stats.cr}} %
                </vs-td>
                <vs-td v-if="canViewStats">
                    <vs-chip transparent :color="(total_stats.epv > 0) ? 'success' : 'danger'">€ {{total_stats.epv}}</vs-chip>
                </vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>
    </vx-card>
</template>

<script>
import vSelect from 'vue-select'

export default {
  components: {
    'v-select': vSelect,
  },
  props: {
    form_id: Number,
  },
  data () {
    return {
      objects: [],
      selected: [],
      deleted: [],

      maxItems: 20,

      isEditingCost: false,
      editingCostValue: 0,

      editingObject: -1,

      showPopup: {
        'create': false,
        'edit': false,
      },
      newObject: {
        'create': {
          name: '',
          campaign: '',
          network: '',
          product: '',
          quantity: '',
          price: 0,
          commission: 0,
          sub_commission: 0,
          traffic_source: '',

          fb_pixel: '',
          sub_id: '',
          api_call_url: '',

          // Worldfilia
          source_id: '',

          // Amsped
          product_reference: '',

          // Affiliation Park
          offer_id: '',
        },
        'edit': {
          name: '',
          campaign: '',
          network: '',
          product: '',
          quantity: '',
          price: 0,
          commission: 0,
          sub_commission: 0,
          traffic_source: '',

          fb_pixel: '',
          sub_id: '',
          api_call_url: '',

          // Worldfilia
          source_id: '',

          // Amsped
          product_reference: '',

          // Affiliation Park
          offer_id: '',
        },
      },

      campaigns: [],
      networks: [],
      products: [],
      traffic_sources: [],

      total_stats: {
        visits: 0,
        leads: 0,
        total_cost: 0,
        total_commission: 0,
        profit: 0,
        roi: 0,
        cr: 0,
        cpv: 0,
        epv: 0,
        cpl: 0,
      },
      dateFormat: 'yyyy-MM-dd',
      filters: {
        date_from: '',
        date_to: '',
      },
      showFilters: false,
    }
  },
  watch: {
    form_id: function(newVal, oldVal) {
      this.retrieveObjects()
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
    canViewStats() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin') ||
              this.$store.getters.userHasRole('mediabuyer'))
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

      // Retrieve landing stats
      this.$http.get('/api/v1/forms/' + this.form_id + '/landings/stats', { headers })
        .then((response) => {
          this.objects = response.data.data
          console.log(response.data.data)
          this.calculateStats()
          this.$vs.loading.close()
          console.log(this.objects)
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
    calculateStats() {
      this.total_stats = {
        visits: 0,
        leads: 0,
        total_commission: 0,
        cr: 0,
        epv: 0,
      }

      for (var i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        let cr = 0.0
        if (obj.leads == 0) cr = 0.0
        else if (obj.visits == 0) cr = 100.0
        else cr = parseFloat((obj.leads/obj.visits)*100.0)
        let epv = parseFloat((obj.visits == 0) ? obj.total_commission : obj.total_commission/obj.visits)

        this.total_stats.visits += parseInt(obj.visits)
        this.total_stats.leads += parseInt(obj.leads)
        this.total_stats.total_commission += parseFloat(obj.total_commission),

        // Format calculated stats
        cr = cr.toFixed(2)
        epv = epv.toFixed(2)

        this.objects[i].cr = cr
        this.objects[i].epv = epv
      }

      // Total stats
      let obj = this.total_stats

      let cr = 0.0
      if (obj.leads == 0) cr = 0.0
      else if (obj.visits == 0) cr = 100.0
      else cr = parseFloat((obj.leads/obj.visits)*100.0)
      let epv = parseFloat((obj.visits == 0) ? obj.total_commission : obj.total_commission/obj.visits)

      // Format calculated stats
      cr = cr.toFixed(2)
      epv = epv.toFixed(2)

      this.total_stats.cr = cr
      this.total_stats.epv = epv
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    showEditCostInput(id) {
      let index = this.getObjectIndexById(id)

      this.isEditingCost = true
      this.editingCostValue = this.objects[index].total_cost
      this.editingObject = id
    },
    showForm(type) {
      this.showPopup[type] = true
    },
    clearForm(type) {
      this.newObject[type] = {
        name: '',
        campaign: '',
        network: '',
        product: '',
        quantity: '',
        price: 0,
        commission: 0,
        sub_commission: 0,
        traffic_source: '',

        fb_pixel: '',
        sub_id: '',
        api_call_url: '',

        // Worldfilia
        source_id: '',

        // Amsped
        product_reference: '',

        // Affiliation Park
        offer_id: '',
      }
    },
    isValidForm(type) {
      let obj = this.newObject[type]
      return (obj.name.length > 0
        && obj.campaign != '' && obj.campaign.id >= 1
        && obj.network != '' && obj.network.id >= 1
        && obj.product != '' && obj.product.id >= 1
        && obj.quantity.length > 0
        && obj.traffic_source != '' && obj.traffic_source.id >= 1)
    },
    createObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.newObject['create'].name,
        campaign_id: this.newObject['create'].campaign.id,
        network_id: this.newObject['create'].network.id,
        product_id: this.newObject['create'].product.id,
        quantity: this.newObject['create'].quantity,
        price: this.newObject['create'].price,
        commission: this.newObject['create'].commission,
        sub_commission: this.newObject['create'].sub_commission,
        traffic_source_id: this.newObject['create'].traffic_source.id,
        
        network_params: {
        }
      }
      if (this.newObject['create'].network.label == 'Worldfilia') {
        data.network_params = {
          fb_pixel: this.newObject['create'].fb_pixel,
          api_call_url: this.newObject['create'].api_call_url,
          source_id: this.newObject['create'].source_id,
        }
      } else if (this.newObject['create'].network.label == 'Amsped') {
        data.network_params = {
          fb_pixel: this.newObject['create'].fb_pixel,
          api_call_url: this.newObject['create'].api_call_url,
          sub_id: this.newObject['create'].sub_id,
          product_reference: this.newObject['create'].product_reference,
        }
      } else if (this.newObject['create'].network.label == 'Affiliation Park') {
        data.network_params = {
          fb_pixel: this.newObject['create'].fb_pixel,
          api_call_url: this.newObject['create'].api_call_url,
          sub_id: this.newObject['create'].sub_id,
          offer_id: this.newObject['create'].offer_id,
        }
      }
      data.network_params = JSON.stringify(data.network_params)

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
    editForm(id) {
      let obj = this.objects[this.getObjectIndexById(id)]
      this.newObject['edit'] = {
        name: obj.name,
        campaign: {
          id: obj.campaign.id,
          label: obj.campaign.name,
        },
        network: {
          id: obj.network.id,
          label: obj.network.name,
        },
        product: {
          id: obj.product.id,
          label: obj.product.name,
        },
        quantity: obj.quantity,
        price: obj.price,
        commission: obj.commission,
        sub_commission: obj.sub_commission,
        traffic_source: {
          id: obj.traffic_source.id,
          label: obj.traffic_source.name,
        },

        fb_pixel: '',
        api_call_url: '',
        sub_id: '',

        // Worldfilia
        source_id: '',

        // Amsped
        product_reference: '',

        // Affiliation Park
        offer_id: '',
      }
      if (obj.hasOwnProperty('network_params')) {
        if (obj.network_params.hasOwnProperty('fb_pixel')) {
          this.newObject['edit'].fb_pixel = obj.network_params.fb_pixel
        }
        if (obj.network_params.hasOwnProperty('api_call_url')) {
          this.newObject['edit'].api_call_url = obj.network_params.api_call_url
        }
        if (obj.network.name == 'Worldfilia') {
          if (obj.network_params.hasOwnProperty('source_id')) {
            this.newObject['edit'].source_id = obj.network_params.source_id
          }
        } else if (obj.network.name == 'Amsped') {
          if (obj.network_params.hasOwnProperty('sub_id')) {
            this.newObject['edit'].sub_id = obj.network_params.sub_id
          }
          if (obj.network_params.hasOwnProperty('product_reference')) {
            this.newObject['edit'].product_reference = obj.network_params.product_reference
          }
        } else if (obj.network.name == 'Affiliation Park') {
          if (obj.network_params.hasOwnProperty('sub_id')) {
            this.newObject['edit'].sub_id = obj.network_params.sub_id
          }
          if (obj.network_params.hasOwnProperty('offer_id')) {
            this.newObject['edit'].offer_id = obj.network_params.offer_id
          }
        }
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
        campaign_id: this.newObject['edit'].campaign.id,
        network_id: this.newObject['edit'].network.id,
        product_id: this.newObject['edit'].product.id,
        quantity: this.newObject['edit'].quantity,
        price: this.newObject['edit'].price,
        commission: this.newObject['edit'].commission,
        sub_commission: this.newObject['edit'].sub_commission,
        traffic_source_id: this.newObject['edit'].traffic_source.id,
      }
      if (this.newObject['edit'].network.label == 'Worldfilia') {
        data.network_params = {
          fb_pixel: this.newObject['edit'].fb_pixel,
          api_call_url: this.newObject['edit'].api_call_url,
          source_id: this.newObject['edit'].source_id,
        }
      } else if (this.newObject['edit'].network.label == 'Amsped') {
        data.network_params = {
          fb_pixel: this.newObject['edit'].fb_pixel,
          api_call_url: this.newObject['edit'].api_call_url,
          sub_id: this.newObject['edit'].sub_id,
          product_reference: this.newObject['edit'].product_reference,
        }
      } else if (this.newObject['edit'].network.label == 'Affiliation Park') {
        data.network_params = {
          fb_pixel: this.newObject['edit'].fb_pixel,
          api_call_url: this.newObject['edit'].api_call_url,
          sub_id: this.newObject['edit'].sub_id,
          offer_id: this.newObject['edit'].offer_id,
        }
      }
      data.network_params = JSON.stringify(data.network_params)

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
      this.showPopup['edit'] = false
      this.editingObject = -1
    },
    deleteObject(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$http.delete('/api/v1/landings/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Landing deleted',
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
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
      }
      this.applyFilters()
    },
    applyFilters() {
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

      // Retrieve landing stats
      this.$http.get('/api/v1/forms/' + this.form_id + '/landings/stats', { headers, params: data })
        .then((response) => {
          this.objects = response.data.data
          this.calculateStats()
          this.$vs.loading.close()
          console.log(this.objects)
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
  },
  created() {
    this.retrieveObjects()
  },
  mounted () {
  }
}

</script>
