<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Users reports" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-pie-chart" @click="showChart = !showChart"></vs-button>
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
                <vs-th sort-key="day">Date</vs-th>
                <vs-th sort-key="visits">Visits</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="tq">TQ</vs-th>
                <vs-th sort-key="commission" v-if="isAdmin">Commission</vs-th>
                <vs-th sort-key="cost">Cost</vs-th>
                <vs-th sort-key="extra_cost" v-if="canViewStats">Extra Cost</vs-th>
                <vs-th sort-key="profit" v-if="isAdmin">Profit</vs-th>
                <vs-th></vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td>
                        <vs-button size="small" radius :color="(data[indextr].cost != 0) ? ((data[indextr].profit >= 0) ? 'success' : 'danger') : '#d9d9d9'" type="filled" icon-pack="feather" :icon="(data[indextr].cost != 0) ? ((data[indextr].profit >= 0) ? 'icon-plus' : 'icon-minus') : 'icon-refresh-cw'"></vs-button>
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

                    <vs-td :data="data[indextr].tq">
                        {{data[indextr].tq}}
                    </vs-td>

                    <vs-td :data="data[indextr].commission" v-if="isAdmin">
                        <vs-input v-if="isAdmin && isEditingCommission && editingObjectId == data[indextr].id" :ref="'editCommissionInput' + data[indextr].id" type="number" step="0.01" min="0" v-model="editingCommissionValue" @keyup.enter="editCommission" @keyup.escape="dontEditCommission" icon-pack="feather" icon="icon-dollar-sign" placeholder="Amount" class="is-label-placeholder"></vs-input>
                        <span v-else>€ {{data[indextr].commission}} <span v-if="isAdmin" class="underline" @click="showEditCommissionInput(data[indextr].id)">(edit)</span></span>
                    </vs-td>

                    <vs-td :data="data[indextr].cost">
                        <vs-input v-if="isAdmin && isEditingCost && editingObjectId == data[indextr].id" :ref="'editCostInput' + data[indextr].id" type="number" step="0.01" min="0" v-model="editingCostValue" @keyup.enter="editCost" @keyup.escape="dontEditCost" icon-pack="feather" icon="icon-dollar-sign" placeholder="Amount" class="is-label-placeholder"></vs-input>
                        <span v-else>€ {{data[indextr].cost}} <span v-if="isAdmin" class="underline" @click="showEditCostInput(data[indextr].id)">(edit)</span></span>
                    </vs-td>

                    <vs-td :data="data[indextr].extra_cost" v-if="canViewStats">
                        <vs-input v-if="isEditingExtraCost && editingObjectId == data[indextr].id" :ref="'editExtraCostInput' + data[indextr].id" type="number" step="0.01" min="0" v-model="editingExtraCostValue" @keyup.enter="editExtraCost" @keyup.escape="dontEditExtraCost" icon-pack="feather" icon="icon-dollar-sign" placeholder="Amount" class="is-label-placeholder"></vs-input>
                        <span v-else>€ {{data[indextr].extra_cost}} <span class="underline" @click="showEditExtraCostInput(data[indextr].id)">(edit)</span></span>
                    </vs-td>

                    <vs-td :data="data[indextr].profit" v-if="isAdmin">
                        <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">€ {{data[indextr].profit}}</vs-chip>
                    </vs-td>

                    <vs-td>
                        <vs-button style="width: 30px !important; height: 30px !important;" :color="(data[indextr].notes == '') ? 'primary' : 'warning'" :type="(data[indextr].notes == '') ? 'border' : 'filled'" icon-pack="feather" icon="icon-message-square" @click="startEditingNote(data[indextr].id)"></vs-button>
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td>{{total_stats.visits}}</vs-td>
                <vs-td>{{total_stats.leads}}</vs-td>
                <vs-td>{{total_stats.tq}}</vs-td>
                <vs-td v-if="isAdmin">€ {{total_stats.commission}}</vs-td>
                <vs-td>€ {{total_stats.cost}}</vs-td>
                <vs-td v-if="canViewStats">€ {{total_stats.extra_cost}}</vs-td>
                <vs-td v-if="isAdmin">€ {{total_stats.profit}}</vs-td>
                <vs-td></vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>

        <!-- Notes popup -->
        <vs-popup title="Notes" :active.sync="showNotesPopup">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3 note">
                    <div class="vx-col w-full">
                        <div>
                            <vs-textarea v-model="notes"></vs-textarea>
                            <div class="flex" style="float: right;">
                                <vs-button color="success" type="filled" icon-pack="feather" icon="icon-check" @click="editNote"></vs-button>
                                <vs-button color="dark" type="filled" icon-pack="feather" icon="icon-x" @click="editNoteCancel"></vs-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>
    </vx-card>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'

export default {
  components: {
    VueApexCharts,
  },
  data () {
    return {
      objects: [],
      selected: [],
      deleted: [],

      maxItems: 20,

      filters: {
        date_from: '',
        date_to: '',
      },
      showFilters: false,

      showNotesPopup: false,
      editingObjectId: -1,
      editingObject: null,
      notes: '',
      isEditingCommission: false,
      editingCommissionValue: 0,
      isEditingCost: false,
      editingCostValue: 0,
      isEditingExtraCost: false,
      editingExtraCostValue: 0,

      total_stats: {
        visits: 0,
        leads: 0,
        tq: 0,
        commission: 0,
        cost: 0,
        extra_cost: 0,
        profit: 0,
      },

      showChart: false,
      maxChartLines: 10,
      themeColors: {
          admin: ['#EA5455', '#FFC73B', '#28C76F', '#FF9F43', '#1E1E1E'],
          mediaBuyer: ['#EA5455', '#FFC73B', '#28C76F', '#FF9F43', '#1E1E1E'],
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
              this.$store.getters.userHasRole('mediabuyer') || this.$store.getters.userHasRole('buyer'))
    },
    isAdmin() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin'))
    },
  },
  methods: {
    getObjectIndexById(id) {
      var index = -1;
      for (let i = 0; i < this.objects.length; i++) {
        if (this.objects[i].id == id) {
          index = i
          break
        }
      }
      return index
    },
    async retrieveObjects() {
      this.selected = []
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

      // Retrieve user's report
      const response = await this.$http.get('/api/v1/my-reports', { headers, params: data })
      this.objects = response.data.data

      for (let i = 0; i < this.objects.length; i++) {
        if(this.objects[i].notes === null) {
          this.objects[i].notes = ''
        }
      }
      this.calculateStats()
      this.loadChart()

      this.$vs.loading.close()
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
      }
      this.filterDatesShortcut('this_month')
    },
    applyFilters() {
      this.retrieveObjects()
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
    startEditingNote(id) {
      this.editingObjectId = id
      this.editingObject = this.objects[this.getObjectIndexById(id)]
      this.notes = this.editingObject.notes
      this.showNotesPopup = true
    },
    async editNote() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        day: this.editingObject.day,
        notes: this.notes
      }
      const response = await this.$http.put('/api/v1/my-reports', data, { headers })
      let index = this.getObjectIndexById(this.editingObjectId)
      this.objects[index].notes = this.notes
      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Notes updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    editNoteCancel() {
      this.notes = this.editingObject.notes
    },
    showEditCommissionInput(id) {
      let index = this.getObjectIndexById(id)

      this.isEditingCommission = true
      this.editingCommissionValue = this.objects[index].commission
      this.editingObjectId = id
      this.editingObject = this.objects[index]

      let refValue = 'editCommissionInput' + id
      this.$nextTick(() => {
        let inputEl = this.$refs[refValue][0].$el.querySelector('input')
        inputEl.focus()
      });
    },
    async editCommission() {
      this.$vs.loading()

      let index = this.getObjectIndexById(this.editingObjectId)

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        day: this.editingObject.day,
        commission: this.editingCommissionValue,
      }
      const response = await this.$http.put('/api/v1/my-reports', data, { headers })
      this.objects[index].commission = this.editingCommissionValue
      this.objects[index].profit = response.data.data.profit
      this.isEditingCommission = false
      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Commission updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    dontEditCommission() {
      this.isEditingCommission = false
    },
    showEditCostInput(id) {
      let index = this.getObjectIndexById(id)

      this.isEditingCost = true
      this.editingCostValue = this.objects[index].cost
      this.editingObjectId = id
      this.editingObject = this.objects[index]

      let refValue = 'editCostInput' + id
      this.$nextTick(() => {
        let inputEl = this.$refs[refValue][0].$el.querySelector('input')
        inputEl.focus()
      });
    },
    async editCost() {
      this.$vs.loading()

      let index = this.getObjectIndexById(this.editingObjectId)

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        day: this.editingObject.day,
        cost: this.editingCostValue,
      }
      const response = await this.$http.put('/api/v1/my-reports', data, { headers })
      this.objects[index].cost = this.editingCostValue
      this.objects[index].profit = response.data.data.profit
      this.isEditingCost = false
      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Cost updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    dontEditCost() {
      this.isEditingCost = false
    },
    showEditExtraCostInput(id) {
      let index = this.getObjectIndexById(id)

      this.isEditingExtraCost = true
      this.editingExtraCostValue = this.objects[index].extra_cost
      this.editingObjectId = id
      this.editingObject = this.objects[index]

      let refValue = 'editExtraCostInput' + id
      this.$nextTick(() => {
        let inputEl = this.$refs[refValue][0].$el.querySelector('input')
        inputEl.focus()
      });
    },
    async editExtraCost() {
      this.$vs.loading()

      let index = this.getObjectIndexById(this.editingObjectId)

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        day: this.editingObject.day,
        extra_cost: this.editingExtraCostValue,
      }
      const response = await this.$http.put('/api/v1/my-reports', data, { headers })
      this.objects[index].extra_cost = this.editingExtraCostValue
      this.objects[index].profit = response.data.data.profit
      this.isEditingExtraCost = false
      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Extra cost updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    dontEditExtraCost() {
      this.isEditingExtraCost = false
    },
    calculateStats() {
      this.total_stats = {
        visits: 0,
        leads: 0,
        tq: 0,
        commission: 0,
        cost: 0,
        extra_cost: 0,
        profit: 0,
      }

      for (var i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        this.total_stats.visits += parseInt(obj.visits)
        this.total_stats.leads += parseInt(obj.leads)
        this.total_stats.commission += parseFloat(obj.commission)
        this.total_stats.cost += parseFloat(obj.cost)
        this.total_stats.extra_cost += parseFloat(obj.extra_cost)
        this.total_stats.tq += parseInt(obj.tq)
        this.total_stats.profit += parseFloat(obj.profit)
      }
      if (this.objects.length > 0) {
        this.total_stats.tq = parseInt(this.total_stats.tq / this.objects.length)
      }

      // Format total stats
      this.total_stats.commission = this.total_stats.commission.toFixed(2)
      this.total_stats.cost = this.total_stats.cost.toFixed(2)
      this.total_stats.extra_cost = this.total_stats.extra_cost.toFixed(2)
      this.total_stats.profit = this.total_stats.profit.toFixed(2)
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
      let commission_values = []
      let total_cost_values = []
      let profit_values = []
      for(let j = 0; j < numDots; j++) {
        commission_values.push(0)
        total_cost_values.push(0)
        profit_values.push(0)
      }
      for(let j = 0; j < stats.length; j++) {
        let stat = stats[j]

        let commission = 0
        let total_cost = 0
        let profit = 0

        commission = parseFloat(stat.commission)
        total_cost = parseFloat(stat.cost) + parseFloat(stat.extra_cost)
        profit = parseFloat(stat.profit)

        let index = categories.indexOf(stat.day)

        if (index >= 0) {
          commission_values[index] = commission.toFixed(2)
          total_cost_values[index] = total_cost.toFixed(2)
          profit_values[index] = profit.toFixed(2)
        }
      }

      let themeColors = []
      if (this.isAdmin) {
        themeColors = this.themeColors.admin
        series.push({
          name: "Total cost",
          data: total_cost_values,
        })
        series.push({
          name: "Commission",
          data: commission_values,
        })
        series.push({
          name: "Profit",
          data: profit_values,
        })
      } else {
        themeColors = this.themeColors.mediaBuyer
        series.push({
          name: "Total cost",
          data: total_cost_values,
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
  },
  created() {
    this.resetFilters()
  },
  mounted () {
  }
}

</script>
