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

        <div class="vx-row mb-2">
            <div class="vx-col w-1/3">
            </div>
            <div class="vx-col w-1/3 text-center">
                <vs-select
                    autocomplete
                    @input="onFormSwitch"
                    label="Switch Form"
                    v-model="formSwitch"
                    >
                    <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in formSwitchOptions" />
                </vs-select>
            </div>
            <div class="vx-col w-1/3">
            </div>
        </div>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-pie-chart" @click="showChart = !showChart"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-message-square" @click="showNotes"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="refreshFormStats"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-globe" @click="showLandingsStats(form_id)"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="(view == 'normal') ? 'icon-facebook' : 'icon-activity'" @click="view = (view == 'normal') ? 'traffic_source' : 'normal'"></vs-button>
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

        <div v-if="view == 'normal'">
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
                    <vs-th sort-key="tq">TQ</vs-th>
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
                            <vs-input v-if="isEditingCost && editingObject == data[indextr].id" :ref="'editCostInput' + data[indextr].id" type="number" step="0.01" min="0" v-model="editingCostValue" @keyup.enter="editCost" @keyup.escape="dontEditCost" icon-pack="feather" icon="icon-dollar-sign" placeholder="Amount" class="is-label-placeholder"></vs-input>
                            <span v-else>€ {{data[indextr].total_cost.toFixed(2)}} <span class="underline" @click="showEditCostInput(data[indextr].id)">(edit)</span></span>
                        </vs-td>

                        <vs-td :data="data[indextr].total_commission" v-if="canViewStats">
                            € {{data[indextr].total_commission.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].profit" v-if="canViewStats">
                            <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">€ {{data[indextr].profit.toFixed(2)}}</vs-chip>
                        </vs-td>

                        <vs-td :data="data[indextr].roi" v-if="canViewStats">
                            <vs-chip transparent :color="(data[indextr].roi > 0) ? 'success' : ((data[indextr].roi < 0) ? 'danger' : '')">{{data[indextr].roi.toFixed(2)}} %</vs-chip>
                        </vs-td>

                        <vs-td :data="data[indextr].cr">
                            {{data[indextr].cr.toFixed(2)}} %
                        </vs-td>

                        <vs-td :data="data[indextr].tq">
                            {{data[indextr].tq}}
                        </vs-td>

                        <vs-td :data="data[indextr].cpv" v-if="canViewStats">
                            € {{data[indextr].cpv.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].epv" v-if="canViewStats">
                            <vs-chip transparent :color="(data[indextr].epv > 0) ? 'success' : ((data[indextr].epv < 0) ? 'danger' : '')">€ {{data[indextr].epv.toFixed(2)}}</vs-chip>
                        </vs-td>

                        <vs-td :data="data[indextr].cpl">
                            € {{data[indextr].cpl.toFixed(2)}}
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
                        € {{total_stats.total_cost.toFixed(2)}}
                    </vs-td>
                    <vs-td v-if="canViewStats">
                        € {{total_stats.total_commission.toFixed(2)}}
                    </vs-td>
                    <vs-td v-if="canViewStats">
                        <vs-chip transparent :color="(total_stats.profit > 0) ? 'success' : 'danger'">€ {{total_stats.profit.toFixed(2)}}</vs-chip>
                    </vs-td>
                    <vs-td v-if="canViewStats">
                        <vs-chip transparent :color="(total_stats.roi > 0) ? 'success' : 'danger'">{{total_stats.roi.toFixed(2)}} %</vs-chip>
                    </vs-td>
                    <vs-td>
                        {{total_stats.cr.toFixed(2)}} %
                    </vs-td>
                    <vs-td>
                        {{total_stats.tq}}
                    </vs-td>
                    <vs-td v-if="canViewStats">
                        € {{total_stats.cpv.toFixed(2)}}
                    </vs-td>
                    <vs-td v-if="canViewStats">
                        <vs-chip transparent :color="(total_stats.epv > 0) ? 'success' : 'danger'">€ {{total_stats.epv.toFixed(2)}}</vs-chip>
                    </vs-td>
                    <vs-td>
                        € {{total_stats.cpl}}
                    </vs-td>
                    <!-- END TOTAL STATS-->
                </template>
            </vs-table>
        </div>

        <div v-if="view == 'traffic_source'">
            <vs-select
                autocomplete
                label="Query Param"
                v-model="currentParam"
                >
                <vs-select-item :key="index" :value="item.query_param" :text="item.name" v-for="(item, index) in paramOptions" />
            </vs-select>

            <vs-table stripe multiple v-model="selectedParamValues" search :data="(customParamsStats.hasOwnProperty(currentParam)) ? customParamsStats[currentParam] : []">

                <template slot="thead">
                    <vs-th sort-key="value">Value</vs-th>
                    <vs-th sort-key="visits">Visits</vs-th>
                    <vs-th sort-key="leads">Leads</vs-th>
                    <vs-th sort-key="total_commission" v-if="canViewStats">Commission</vs-th>
                    <vs-th sort-key="cr">CR</vs-th>
                    <vs-th sort-key="epv" v-if="canViewStats">EPV</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td :data="data[indextr].value">
                            {{data[indextr].value}}
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
                            <vs-chip transparent :color="(data[indextr].epv > 0) ? 'success' : ((data[indextr].epv < 0) ? 'danger' : '')">€ {{data[indextr].epv}}</vs-chip>
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>
                        {{customParamsStatsTotal[currentParam].visits}}
                    </vs-td>
                    <vs-td>
                        {{customParamsStatsTotal[currentParam].leads}}
                    </vs-td>
                    <vs-td v-if="canViewStats">
                        € {{customParamsStatsTotal[currentParam].total_commission}}
                    </vs-td>
                    <vs-td>
                        {{customParamsStatsTotal[currentParam].cr}} %
                    </vs-td>
                    <vs-td v-if="canViewStats">
                        <vs-chip transparent :color="(customParamsStatsTotal[currentParam].epv > 0) ? 'success' : 'danger'">€ {{customParamsStatsTotal[currentParam].epv}}</vs-chip>
                    </vs-td>
                    <!-- END TOTAL STATS-->
                </template>
            </vs-table>
        </div>

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
      view: 'normal',

      form: {},
      objects: [],
      selected: [],
      deleted: [],

      customParamsStats: {},
      currentParam: '',
      selectedParamValues: [],
      paramOptions: [],

      maxItems: 20,

      isEditingCost: false,
      editingCostValue: 0,

      editingObject: -1,

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
      themeColors: {
          admin: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#1E1E1E'],
          mediaBuyer: ['#7367F0', '#28C76F', '#9C1E10', '#E36222', '#1E1E1E'],
      },
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
          colors: [],
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

      formSwitchOptions: [],
      formSwitch: 1,
    }
  },
  watch: {
    form_id: function(newVal, oldVal) {
      this.retrieveObjects()
      this.applyFilters()
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
          this.paramOptions = this.form.traffic_source.custom_params
          if (this.paramOptions.length > 0) {
            this.currentParam = this.paramOptions[0].query_param
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

      // Retrieve forms
      this.$http.get('/api/v1/forms/', { headers })
        .then((response) => {
          let forms = response.data.data
          this.formSwitchOptions = []
          for (let i = 0; i < forms.length; i++) {
            this.formSwitchOptions.push({
              label: forms[i].name,
              value: forms[i].id,
            });
            this.formSwitch = this.form_id
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
    calculateStats() {
      this.total_stats = {
        visits: 0,
        leads: 0,
        total_cost: 0,
        total_commission: 0,
        profit: 0,
        roi: 0,
        cr: 0,
        tq: 0,
        cpv: 0,
        epv: 0,
        cpl: 0,
      }

      this.customParamsStats = {}
      this.customParamsStatsTotal = {}

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
        this.total_stats.total_cost += parseFloat(obj.total_cost)
        this.total_stats.total_commission += parseFloat(obj.total_commission)
        this.total_stats.tq += parseInt(obj.tq)

        this.objects[i].visits = parseInt(obj.visits)
        this.objects[i].leads = parseInt(obj.leads)
        this.objects[i].total_commission = parseFloat(obj.total_commission)
        this.objects[i].total_cost = parseFloat(obj.total_cost)
        this.objects[i].profit = profit
        this.objects[i].roi = roi
        this.objects[i].cr = cr
        this.objects[i].cpv = cpv
        this.objects[i].epv = epv
        this.objects[i].cpl = cpl

        if (obj.custom_params_stats !== undefined && obj.custom_params_stats !== null) {
          for (const [param, valueStats] of Object.entries(obj.custom_params_stats)) {
            if (!this.customParamsStats.hasOwnProperty(param)) {
              this.customParamsStats[param] = []
            }
            for (let [value, stats] of Object.entries(valueStats)) {
              let visits = parseInt(stats.visits)
              let leads = parseInt(stats.leads)
              let total_commission = parseFloat(stats.total_commission)

              let index = -1
              for (let j = 0; j < this.customParamsStats[param].length; j++) {
                if (this.customParamsStats[param][j].value == value) {
                  index = j
                  break
                }
              }
              if (index == -1) {
                this.customParamsStats[param].push({
                  value: value,
                  visits: 0,
                  leads: 0,
                  total_commission: 0.0,
                  cr: 0.0,
                  epv: 0.0,
                })
                index = this.customParamsStats[param].length - 1
              }
              this.customParamsStats[param][index].visits += visits
              this.customParamsStats[param][index].leads += leads
              this.customParamsStats[param][index].total_commission += total_commission

              let cr = 0.0
              if (this.customParamsStats[param][index].leads == 0) cr = 0.0
              else if (this.customParamsStats[param][index].visits == 0) cr = 100.0
              else cr = parseFloat((this.customParamsStats[param][index].leads/this.customParamsStats[param][index].visits)*100.0)
              let epv = parseFloat((this.customParamsStats[param][index].visits == 0) ?
                this.customParamsStats[param][index].total_commission :
                this.customParamsStats[param][index].total_commission / this.customParamsStats[param][index].visits)

              this.customParamsStats[param][index].cr = cr
              this.customParamsStats[param][index].epv = epv
            }
          }
        }
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

      this.total_stats.profit = profit
      this.total_stats.roi = roi
      this.total_stats.cr = cr
      this.total_stats.tq = parseInt(this.total_stats.tq / this.objects.length)
      this.total_stats.cpv = cpv
      this.total_stats.epv = epv
      this.total_stats.cpl = cpl

      // Calculate custom params total stats and format custom params stats
      for (const [param, stats] of Object.entries(this.customParamsStats)) {
        if (!this.customParamsStatsTotal.hasOwnProperty(param)) {
          this.customParamsStatsTotal[param] = {
            visits: 0,
            leads: 0,
            total_commission: 0.0,
            cr: 0.0,
            epv: 0.0,
          }
        }
        for (let j = 0; j < stats.length; j++) {
          this.customParamsStatsTotal[param].visits += parseInt(stats[j].visits)
          this.customParamsStatsTotal[param].leads += parseInt(stats[j].leads)
          this.customParamsStatsTotal[param].total_commission += parseFloat(stats[j].total_commission)

          // Format total_commission, cr and epv
          this.customParamsStats[param][j].total_commission = this.customParamsStats[param][j].total_commission.toFixed(2)
          this.customParamsStats[param][j].cr = this.customParamsStats[param][j].cr.toFixed(2)
          this.customParamsStats[param][j].epv = this.customParamsStats[param][j].epv.toFixed(2)
        }

        let cr = 0.0
        if (this.customParamsStatsTotal[param].leads == 0) cr = 0.0
        else if (this.customParamsStatsTotal[param].visits == 0) cr = 100.0
        else cr = parseFloat((this.customParamsStatsTotal[param].leads/this.customParamsStatsTotal[param].visits)*100.0)
        let epv = parseFloat((this.customParamsStatsTotal[param].visits == 0) ?
          this.customParamsStatsTotal[param].total_commission :
          this.customParamsStatsTotal[param].total_commission/this.customParamsStatsTotal[param].visits)
        this.customParamsStatsTotal[param].cr = cr
        this.customParamsStatsTotal[param].epv = epv
      }
      for (const [param, stats] of Object.entries(this.customParamsStatsTotal)) {
        // Format total_commission, cr and epv
        this.customParamsStatsTotal[param].total_commission = this.customParamsStatsTotal[param].total_commission.toFixed(2)
        this.customParamsStatsTotal[param].cr = this.customParamsStatsTotal[param].cr.toFixed(2)
        this.customParamsStatsTotal[param].epv = this.customParamsStatsTotal[param].epv.toFixed(2)
      }
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    showEditCostInput(id) {
      let index = this.getObjectIndexById(id)

      this.isEditingCost = true
      this.editingCostValue = this.objects[index].total_cost
      this.editingObject = id

      let refValue = 'editCostInput' + id
      this.$nextTick(() => {
        let inputEl = this.$refs[refValue][0].$el.querySelector('input')
        inputEl.focus()
      });
    },
    dontEditCost() {
      this.isEditingCost = false
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

      // Retrieve forms' stats and set chart Y axis
      this.lineAreaChartSpline.series = []
      let series = []

      let stats = this.objects

      // Calculate CPV and EPV
      let cpv_values = []
      let epv_values = []
      let cost_values = []
      let tq_values = []
      let cpa_values = []
      for(let j = 0; j < numDots; j++) {
        cpv_values.push(0)
        epv_values.push(0)
        cost_values.push(0)
        tq_values.push(0)
        cpa_values.push(0)
      }
      for(let j = 0; j < stats.length; j++) {
        let stat = stats[j]

        let cpv = 0
        let epv = 0
        let cost = 0
        let tq = 0
        let cpa = 0

        cpv = parseFloat((stat.visits == 0) ? stat.total_cost : stat.total_cost/stat.visits)
        epv = parseFloat((stat.visits == 0) ? stat.total_commission : stat.total_commission/stat.visits)
        cost = parseFloat(stat.total_cost)
        tq = parseInt(stat.tq)
        cpa = parseFloat((stat.leads == 0) ? stat.total_cost : stat.total_cost/stat.leads)

        let index = categories.indexOf(stat.day)

        if (index >= 0) {
          cpv_values[index] = cpv.toFixed(2)
          epv_values[index] = epv.toFixed(2)
          cost_values[index] = cost.toFixed(2)
          tq_values[index] = tq
          cpa_values[index] = cpa.toFixed(2)
        }
      }

      let themeColors = []
      if (this.canViewStats) {
        themeColors = this.themeColors.admin
        series.push({
          name: "cpv",
          data: cpv_values,
        })
        series.push({
          name: "epv",
          data: epv_values,
        })
      } else {
        themeColors = this.themeColors.mediaBuyer
        series.push({
          name: "CPV",
          data: cpv_values,
        })
        series.push({
          name: "TQ",
          data: tq_values,
        })
        series.push({
          name: "Cost",
          data: cost_values,
        })
        series.push({
          name: "CPA",
          data: cpa_values,
        })
      }
      this.lineAreaChartSpline.series = series

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
        colors: themeColors,
        xaxis: {
          categories: categories,
        },
        tooltip: {
          x: {
            format: 'yyyy-MM-dd'
          }
        },
      }
    },
    showLandingsStats(id) {
      this.$router.push({name: 'landings-stats', params: {form_id: id}})
    },
    onFormSwitch(value) {
      this.$parent.switchForm(value)
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
