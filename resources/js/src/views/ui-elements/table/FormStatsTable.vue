<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Form Stats" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-pie-chart" @click="showChart = !showChart"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-message-square" @click="showNotes"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="refreshFormStats"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-globe" @click="showLandingsStats(form_id)"></vs-button>
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
                <div class="vx-col w-1/2">
                    <label>From</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_from" />
                </div>
                <div class="vx-col w-1/2">
                    <label>To</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_to" />
                </div>
            </div>
            <div class="vx-row mt-6">
                <div class="vx-col w-full">
                    <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
                    <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
                </div>
            </div>
        </div>

        <div v-if="showChart">
            <vue-apex-charts ref="chart" type="line" height="350" :options="lineAreaChartSpline.chartOptions" :series="lineAreaChartSpline.series"></vue-apex-charts> 
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
                <vs-th></vs-th>
                <vs-th sort-key="day">Day</vs-th>
                <vs-th sort-key="visits">Visits</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="total_cost">Cost</vs-th>
                <vs-th sort-key="total_commission" v-if="canViewStats">Commission</vs-th>
                <vs-th sort-key="profit" v-if="canViewStats">Profit</vs-th>
                <vs-th sort-key="roi" v-if="canViewStats">ROI</vs-th>
                <vs-th sort-key="cr">CR</vs-th>
                <vs-th sort-key="cpv" v-if="canViewStats">CPV</vs-th>
                <vs-th sort-key="epv" v-if="canViewStats">EPV</vs-th>
                <vs-th sort-key="cpl">CPL</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td>
                        <vs-button radius :color="(data[indextr].total_cost != 0) ? ((data[indextr].profit >= 0) ? 'success' : 'danger') : '#d9d9d9'" type="filled" icon-pack="feather" :icon="(data[indextr].total_cost != 0) ? ((data[indextr].profit >= 0) ? 'icon-plus' : 'icon-minus') : 'icon-refresh-cw'"></vs-button>
                    </vs-td>

                    <vs-td :data="data[indextr].day">
                        {{data[indextr].day}}
                    </vs-td>

                    <vs-td :data="data[indextr].visits">
                        {{data[indextr].visits}}
                    </vs-td>

                    <vs-td :data="data[indextr].leads">
                        {{data[indextr].leads}}
                    </vs-td>

                    <vs-td :data="data[indextr].total_cost">
                        <vs-input v-if="isEditingCost && editingObject == data[indextr].id" type="number" step="0.01" min="0" v-model="editingCostValue" @keyup.enter="editCost" icon-pack="feather" icon="icon-dollar-sign" placeholder="Amount" class="is-label-placeholder"></vs-input>
                        <span v-else>€ {{data[indextr].total_cost}} <span class="underline" @click="showEditCostInput(data[indextr].id)">(edit)</span></span>
                    </vs-td>

                    <vs-td :data="data[indextr].total_commission" v-if="canViewStats">
                        € {{data[indextr].total_commission}}
                    </vs-td>

                    <vs-td :data="data[indextr].profit" v-if="canViewStats">
                        <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">€ {{data[indextr].profit}}</vs-chip>
                    </vs-td>

                    <vs-td :data="data[indextr].roi" v-if="canViewStats">
                        <vs-chip transparent :color="(data[indextr].roi > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">{{data[indextr].roi}} %</vs-chip>
                    </vs-td>

                    <vs-td :data="data[indextr].cr">
                        {{data[indextr].cr}} %
                    </vs-td>

                    <vs-td :data="data[indextr].cpv" v-if="canViewStats">
                        € {{data[indextr].cpv}}
                    </vs-td>

                    <vs-td :data="data[indextr].epv" v-if="canViewStats">
                        <vs-chip transparent :color="(data[indextr].epv > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">€ {{data[indextr].epv}}</vs-chip>
                    </vs-td>

                    <vs-td :data="data[indextr].cpl">
                        € {{data[indextr].cpl}}
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
                <vs-td>
                    € {{total_stats.total_cost}}
                </vs-td>
                <vs-td v-if="canViewStats">
                    € {{total_stats.total_commission}}
                </vs-td>
                <vs-td v-if="canViewStats">
                    <vs-chip transparent :color="(total_stats.profit > 0) ? 'success' : 'danger'">€ {{total_stats.profit}}</vs-chip>
                </vs-td>
                <vs-td v-if="canViewStats">
                    <vs-chip transparent :color="(total_stats.roi > 0) ? 'success' : 'danger'">{{total_stats.roi}} %</vs-chip>
                </vs-td>
                <vs-td>
                    {{total_stats.cr}} %
                </vs-td>
                <vs-td v-if="canViewStats">
                    € {{total_stats.cpv}}
                </vs-td>
                <vs-td v-if="canViewStats">
                    <vs-chip transparent :color="(total_stats.epv > 0) ? 'success' : 'danger'">€ {{total_stats.epv}}</vs-chip>
                </vs-td>
                <vs-td>
                    € {{total_stats.cpl}}
                </vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>

        <!-- Notes popup -->
        <vs-popup title="Notes" :active.sync="showNotesPopup">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3 note" v-for="(note, index) in form.notes">
                    <div class="vx-col w-full">
                        <div style="text-align: right;">{{ formatDateTime(new Date(note.created_at)) }}</div>

                        <div v-if="isEditingNote && editingNoteId == note.id">
                            <vs-textarea v-model="editingNoteBody"></vs-textarea>
                            <div class="flex" style="float: right;">
                                <vs-button color="success" type="filled" icon-pack="feather" icon="icon-check" @click="editNote"></vs-button>
                                <vs-button color="dark" type="filled" icon-pack="feather" icon="icon-x" @click="editNoteCancel"></vs-button>
                            </div>
                        </div>
                        <div v-else>
                            <div style="white-space: pre;">{{ note.body }}</div>
                            <div class="flex" style="float: right;">
                                <vs-button color="dark" type="flat" icon-pack="feather" icon="icon-edit-2" @click="showEditNoteInput(index)"></vs-button>
                                <vs-button color="dark" type="flat" icon-pack="feather" icon="icon-trash-2" @click="deleteNote(note.id)"></vs-button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="vx-row mb-3">
                    <div class="vx-col w-full">
                        <h3>Create New Note</h3>
                        <vs-textarea v-model="creatingNoteBody"></vs-textarea>
                        <div class="flex" style="float: right;">
                            <vs-button color="success" type="filled" icon-pack="feather" icon="icon-check" @click="createNote"></vs-button>
                            <vs-button color="dark" type="filled" icon-pack="feather" icon="icon-x" @click="createNoteCancel"></vs-button>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>
    </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import VueApexCharts from 'vue-apexcharts'

export default {
  components: {
    'v-select': vSelect,
    VueApexCharts,
  },
  props: {
    form_id: Number,
  },
  data () {
    return {
      form: {},
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
      showNotesPopup: false,
      isEditingNote: false,
      editingNoteId: -1,
      editingNoteIndex: -1,
      editingNoteBody: '',
      creatingNoteBody: '',

      showChart: false,
      maxChartLines: 10,
      themeColors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#1E1E1E'],
      lineAreaChartSpline: {
        series: [],
        chartOptions: {
          chart: {
            type: 'line',
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            curve: 'smooth'
          },
          colors: this.themeColors,
          xaxis: {
            categories: [],
          },
          tooltip: {
            x: {
              format: 'yyyy-MM-dd'
            }
          },
        }
      },
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
      var index = -1
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

      // Retrieve form info
      this.$http.get('/api/v1/forms/' + this.form_id, { headers })
        .then((response) => {
          this.form = response.data.data
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
    calculateStats() {
      this.total_stats = {
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
      }

      for (var i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        let profit = obj.total_commission - obj.total_cost
        let roi = parseFloat((obj.total_commission > 0 && obj.total_cost == 0) ? 9999 : (profit/obj.total_cost)*100)
        let cr = 0.0
        if (obj.leads == 0) cr = 0.0
        else if (obj.visits == 0) cr = 100.0
        else cr = parseFloat((obj.leads/obj.visits)*100.0)
        let cpv = parseFloat((obj.visits == 0) ? obj.total_cost : obj.total_cost/obj.visits)
        let epv = parseFloat((obj.visits == 0) ? obj.total_commission : obj.total_commission/obj.visits)
        let cpl = parseFloat((obj.leads == 0) ? obj.total_cost : obj.total_cost/obj.leads)

        this.total_stats.visits += parseInt(obj.visits)
        this.total_stats.leads += parseInt(obj.leads)
        this.total_stats.total_cost += parseFloat(obj.total_cost),
        this.total_stats.total_commission += parseFloat(obj.total_commission),

        // Format calculated stats
        profit = profit.toFixed(2)
        roi = roi.toFixed(2)
        cr = cr.toFixed(2)
        cpv = cpv.toFixed(2)
        epv = epv.toFixed(2)
        cpl = cpl.toFixed(2)

        this.objects[i].profit = profit
        this.objects[i].roi = roi
        this.objects[i].cr = cr
        this.objects[i].cpv = cpv
        this.objects[i].epv = epv
        this.objects[i].cpl = cpl
      }

      // Total stats
      let obj = this.total_stats

      let profit = obj.total_commission - obj.total_cost
      let roi = parseFloat((obj.total_commission > 0 && obj.total_cost == 0) ? 9999 : (profit/obj.total_cost)*100)
      let cr = 0.0
      if (obj.leads == 0) cr = 0.0
      else if (obj.visits == 0) cr = 100.0
      else cr = parseFloat((obj.leads/obj.visits)*100.0)
      let cpv = parseFloat((obj.visits == 0) ? obj.total_cost : obj.total_cost/obj.visits)
      let epv = parseFloat((obj.visits == 0) ? obj.total_commission : obj.total_commission/obj.visits)
      let cpl = parseFloat((obj.leads == 0) ? obj.total_cost : obj.total_cost/obj.leads)

      // Format calculated stats
      profit = profit.toFixed(2)
      roi = roi.toFixed(2)
      cr = cr.toFixed(2)
      cpv = cpv.toFixed(2)
      epv = epv.toFixed(2)
      cpl = cpl.toFixed(2)

      this.total_stats.profit = profit
      this.total_stats.roi = roi
      this.total_stats.cr = cr
      this.total_stats.cpv = cpv
      this.total_stats.epv = epv
      this.total_stats.cpl = cpl

      this.total_stats.total_cost = this.total_stats.total_cost.toFixed(2)
      this.total_stats.total_commission = this.total_commission.total_cost.toFixed(2)
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
    editCost() {
      this.$vs.loading()

      let index = this.getObjectIndexById(this.editingObject)

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        form_stats_id: this.editingObject,
        total_cost: this.editingCostValue,
      }
      this.$http.put('/api/v1/forms/' + this.objects[index].form.id + '/stats', data, { headers })
        .then((response) => {
          this.objects[index].total_cost = this.editingCostValue
          this.isEditingCost = false
          this.calculateStats()
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Total cost updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
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
    deleteSelected() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      this.deleted = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.selected.forEach((form) => {
        this.$http.delete('/api/v1/forms/' + form.id, { headers })
          .then((response) => {
            this.deleted.push(form.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Form(s) deleted',
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

      // Retrieve forms
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

      this.$http.put('/api/v1/forms/' + id, data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Form updated',
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

      this.$http.delete('/api/v1/forms/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Form deleted',
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
      this.filterDatesShortcut('this_month')
    },
    applyFilters() {
      this.$vs.loading()

      this.selected = []

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

      // Retrieve form stats
      this.$http.get('/api/v1/forms/' + this.form_id + '/stats', { headers, params: data })
        .then((response) => {
          this.objects = response.data.data
          this.calculateStats()

          // TODO: to delete
          console.log("loading chart...")

          this.loadChart()
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
    filterDatesShortcut(period) {
      let invalidPeriod = false

      switch (period) {
        case 'today': {
          let today = new Date()
          this.filters.date_from = this.formatDate(today)
          this.filters.date_to = this.formatDate(today)
        } break
        case 'yesterday': {
          let dateObj = new Date()
          dateObj.setDate(dateObj.getDate() - 1)
          this.filters.date_from = this.formatDate(dateObj)
          this.filters.date_to = this.formatDate(dateObj)
        } break
        case 'this_month': {
          let dateObj = new Date()
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1)
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0)
          this.filters.date_from = this.formatDate(fromObj)
          this.filters.date_to = this.formatDate(toObj)
        } break
        case 'last_month': {
          let dateObj = new Date()
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth() - 1, 1)
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 0)
          this.filters.date_from = this.formatDate(fromObj)
          this.filters.date_to = this.formatDate(toObj)
        } break
        default: {
          invalidPeriod = true
        } break
      }

      if(!invalidPeriod) {
        this.applyFilters()
      }
    },
    formatDate(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0')
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0') //January is 0!
      let yyyy = dateObj.getFullYear()
      
      let formattedDate = yyyy + '-' + mm + '-' + dd
      return formattedDate
    },
    formatDateTime(dateObj) {
      let formattedDate = this.formatDate(dateObj)

      let hh = String(dateObj.getHours()).padStart(2, '0')
      let ii = String(dateObj.getMinutes()).padStart(2, '0')
      let ss = String(dateObj.getSeconds()).padStart(2, '0')

      let formattedTime = hh + ':' + ii + ':' + ss
      return formattedDate + ' ' + formattedTime
    },
    showNotes() {
      this.showNotesPopup = true
    },
    showEditNoteInput(index) {
      this.isEditingNote = true
      this.editingNoteBody = this.form.notes[index].body
      this.editingNoteId = this.form.notes[index].id
      this.editingNoteIndex = index
    },
    createNote() {
      if(this.creatingNoteBody == '') {
        return
      }

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        body: this.creatingNoteBody,
      }
      this.$http.post('/api/v1/forms/' + this.form.id + '/notes', data, { headers })
        .then((response) => {
          this.creatingNoteBody = ''
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Note created',
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
    createNoteCancel() {
      this.creatingNoteBody = ''
    },
    editNote() {
      this.$vs.loading()

      let index = this.editingNoteIndex

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        body: this.editingNoteBody,
      }
      this.$http.put('/api/v1/forms/notes/' + this.editingNoteId, data, { headers })
        .then((response) => {
          this.form.notes[index].body = this.editingNoteBody
          this.isEditingNote = false
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Note updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
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
    editNoteCancel() {
      this.isEditingNote = false
    },
    deleteNote(note_id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Retrieve form info
      this.$http.delete('/api/v1/forms/notes/' + note_id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Notes successfully deleted',
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
    refreshFormStats() {
      this.applyFilters()
    },
    loadChart() {
      // Chart X axis
      let dateObj = new Date()
      let startDay = new Date(this.filters.date_from)
      let endDay = new Date(this.filters.date_to)

      let diffTime = Math.abs(endDay - startDay);
      let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
      let numDots = diffDays + 1

      let currentDay = new Date(this.filters.date_from)
      let categories = []
      for(let i = 0; i < numDots; i++) {
        categories.push(this.formatDate(currentDay))
        currentDay.setDate(currentDay.getDate() + 1)
      }
      this.lineAreaChartSpline.chartOptions = {
        chart: {
          type: 'line',
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth'
        },
        colors: this.themeColors,
        xaxis: {
          categories: categories,
        },
        tooltip: {
          x: {
            format: 'yyyy-MM-dd'
          }
        },
      }

      // Retrieve forms' stats and set chart Y axis
      this.lineAreaChartSpline.series = []
      let series = []

      let stats = this.objects

      // Calculate CPV and EPV
      let cpv_values = []
      let epv_values = []
      for(let j = 0; j < numDots; j++) {
        cpv_values.push(0)
        epv_values.push(0)
      }
      for(let j = 0; j < stats.length; j++) {
        let stat = stats[j]

        let cpv = 0
        let epv = 0
        let ratio = 0

        cpv = parseFloat((stat.visits == 0) ? stat.total_cost : stat.total_cost/stat.visits)
        epv = parseFloat((stat.visits == 0) ? stat.total_commission : stat.total_commission/stat.visits)

        let index = categories.indexOf(stat.day)

        if (index >= 0) {
          cpv_values[index] = cpv.toFixed(2)
          epv_values[index] = epv.toFixed(2)
        }
      }

      series.push({
        name: "cpv",
        data: cpv_values,
      })
      series.push({
        name: "epv",
        data: epv_values,
      })
      this.lineAreaChartSpline.series = series
    },
    showLandingsStats(id) {
      this.$router.push({name: 'landings-stats', params: {form_id: id}})
    },
  },
  created() {
    this.retrieveObjects()
    this.resetFilters()
  },
  mounted () {
  }
}

</script>

<style>
.note {
    border: 1px solid gray;
    background-color: #eaeaea;
    border-radius: 0.5rem;
}
</style>
