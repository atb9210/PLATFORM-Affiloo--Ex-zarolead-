<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Advertisers report" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-pie-chart" @click="showChart = !showChart"></vs-button>
        </p>

        <div v-if="showFilters" class="mb-12">

            <div class="vx-row mb-2">
                <div class="vx-col w-3-12">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('this_month')" icon-pack="feather" icon="icon-calendar">This Month</vs-button>
                </div>
                <div class="vx-col w-3-12">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('last_month')" icon-pack="feather" icon="icon-calendar">Last Month</vs-button>
                </div>
            </div>

            <div class="vx-row mb-2">
                <div class="vx-col w-1/2">
                    <label>Month</label>
                    <vs-select v-model="filters.month" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in monthsOptions" class="w-full" />
                    </vs-select>
                </div>
                <div class="vx-col w-1/2">
                    <label>Year</label>
                    <vs-select v-model="filters.year" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in yearsOptions" class="w-full" />
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
                <vs-th sort-key="user.name">Advertiser</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="total_cost">Cost</vs-th>
                <vs-th sort-key="commission">Commission</vs-th>
                <vs-th sort-key="approved_commission">Approved</vs-th>
                <vs-th sort-key="approved_commission_percentage">% Approved</vs-th>
                <vs-th sort-key="net_profit">Net profit</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].user.name">
                        {{data[indextr].user.name}}
                    </vs-td>

                    <vs-td :data="data[indextr].leads">
                        {{data[indextr].leads}}
                    </vs-td>

                    <vs-td :data="data[indextr].total_cost">
                        {{data[indextr].total_cost.toFixed(2)}} €
                    </vs-td>

                    <vs-td :data="data[indextr].commission">
                        {{data[indextr].commission.toFixed(2)}} €
                    </vs-td>

                    <vs-td :data="data[indextr].approved_commission">
                        <vs-input v-if="isEditing && editingObjectId == data[indextr].id" :ref="'editInput' + data[indextr].id" type="number" step="0.01" min="0" :max="data[indextr].commission" v-model="editingValue" @keyup.enter="edit" @keyup.escape="dontEdit" icon-pack="feather" icon="icon-dollar-sign" placeholder="Amount" class="is-label-placeholder"></vs-input>
                        <span v-else>{{data[indextr].approved_commission.toFixed(2)}} € <span class="underline" @click="showEditInput(data[indextr].id)">(edit)</span></span>
                    </vs-td>

                    <vs-td :data="data[indextr].approved_commission_percentage">
                        {{data[indextr].approved_commission_percentage.toFixed(2)}} %
                    </vs-td>

                    <vs-td :data="data[indextr].net_profit">
                        {{data[indextr].net_profit.toFixed(2)}} €
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td>
                    {{total_stats.leads}}
                </vs-td>
                <vs-td>
                    {{parseFloat(total_stats.total_cost).toFixed(2)}} €
                </vs-td>
                <vs-td>
                    {{parseFloat(total_stats.commission).toFixed(2)}} €
                </vs-td>
                <vs-td>
                    {{parseFloat(total_stats.approved_commission).toFixed(2)}} €
                </vs-td>
                <vs-td>
                    {{parseFloat(total_stats.approved_commission_percentage).toFixed(2)}} %
                </vs-td>
                <vs-td>
                    {{parseFloat(total_stats.net_profit).toFixed(2)}} €
                </vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>
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
  data () {
    return {
      objects: [],
      objectsIds: [],

      selected: [],
      deleted: [],

      maxItems: 20,

      total_stats: {
        leads: 0,
        commission: 0.0,
        cost: 0.0,
        extra_cost: 0.0,
        profit: 0.0,
        roi: 0.0,
      },

      filters: {
        month: '',
        year: '',
      },
      showFilters: false,

      users: [],
      roles: [
        { id: 1, label: 'developer' },
        { id: 2, label: 'admin' },
        { id: 3, label: 'mediabuyer' },
        { id: 4, label: 'affiliate' },
        { id: 5, label: 'advertiser' },
        { id: 6, label: 'accountant' },
      ],

      monthsOptions: [
        { id: 1, label: 'January' },
        { id: 2, label: 'February' },
        { id: 3, label: 'March' },
        { id: 4, label: 'April' },
        { id: 5, label: 'May' },
        { id: 6, label: 'June' },
        { id: 7, label: 'July' },
        { id: 8, label: 'August' },
        { id: 9, label: 'September' },
        { id: 10, label: 'October' },
        { id: 11, label: 'November' },
        { id: 12, label: 'December' },
      ],
      yearsOptions: [],

      isEditing: false,
      editingObjectId: -1,
      editingObject: null,
      editingValue: 0,

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
      let data = {}
      if (this.filters.month != '') {
        data['month'] = this.filters.month
      }
      if (this.filters.year != '') {
        data['year'] = this.filters.year
      }

      // Retrieve users' monthly report
      this.$http.get('/api/v1/reports/advertisers', { headers, params: data })
        .then((response) => {
          this.objects = []
          response.data.data.forEach((obj) => {
            let total_cost = parseFloat(obj.total_cost)
            let commission = parseFloat(obj.commission)
            let approved_commission = parseFloat(obj.approved_commission)
            let approved_commission_percentage = parseFloat((commission > 0) ? ((approved_commission / commission)*100) : 0)
            let net_profit = approved_commission - total_cost

            this.objects.push({
              id: obj.id,
              user: obj.user,
              month: obj.month,
              year: obj.year,
              visits: obj.visits,
              leads: obj.leads,
              total_cost: total_cost,
              commission: commission,
              approved_commission: approved_commission,
              approved_commission_percentage: approved_commission_percentage,
              net_profit: net_profit,
            })
          })

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
    onPageChanged(val) {
      this.currentPage = val
    },
    resetFilters() {
      this.filters = {
        month: '',
        year: '',
      }
      this.filterDatesShortcut('this_month')
    },
    applyFilters() {
      this.retrieveObjects()
    },
    filterDatesShortcut(period) {
      let invalidPeriod = false

      switch (period) {
        case 'this_month': {
          let today = new Date()
          this.filters.month = today.getMonth() + 1
          this.filters.year = today.getFullYear()
        } break
        case 'last_month': {
          let dateObj = new Date()
          let newDateObj = new Date(dateObj.getFullYear(), dateObj.getMonth() - 1, 1)
          this.filters.month = newDateObj.getMonth() + 1
          this.filters.year = newDateObj.getFullYear()
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
    calculateStats() {
      this.total_stats = {
        leads: 0,
        total_cost: 0,
        commission: 0,
        approved_commission: 0,
        approved_commission_percentage: 0,
        net_profit: 0,
      }

      for (var i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        this.total_stats.leads += parseInt(obj.leads)
        this.total_stats.total_cost += parseFloat(obj.total_cost)
        this.total_stats.commission += parseFloat(obj.commission)
        this.total_stats.approved_commission += parseFloat(obj.approved_commission)
        this.total_stats.net_profit += parseFloat(obj.net_profit)
      }
      this.total_stats.approved_commission_percentage = parseFloat((this.total_stats.commission > 0) ? ((this.total_stats.approved_commission / this.total_stats.commission)*100) : 0)
    },
    showEditInput(id) {
      let index = this.getObjectIndexById(id)

      this.isEditing = true
      this.editingValue = this.objects[index].approved_commission.toFixed(2)
      this.editingObjectId = id
      this.editingObject = this.objects[index]

      let refValue = 'editInput' + id
      this.$nextTick(() => {
        let inputEl = this.$refs[refValue][0].$el.querySelector('input')
        inputEl.focus()
      });
    },
    dontEdit() {
      this.isEditing = false
    },
    edit() {
      this.$vs.loading()

      let index = this.getObjectIndexById(this.editingObjectId)

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        month: this.editingObject.month,
        year: this.editingObject.year,
        approved_commission: this.editingValue,
      }
      this.$http.put('/api/v1/reports/advertisers/' + this.editingObject.user.id, data, { headers })
        .then((response) => {
          this.objects[index].approved_commission = this.editingValue
          this.isEditing = false
          this.$vs.loading.close()
          this.retrieveObjects()
          this.$vs.notify({
            title: 'Success',
            text: 'Approved commission updated',
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
    loadChart() {
      // Chart X axis
      let numDots = 12

      let months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

      let currentDate = new Date()
      let endMonth = currentDate.getMonth()
      let startMonth = (endMonth == 11) ? (0) : (endMonth + 1)
      let currentMonth = startMonth

      let categories = []
      for(let i = 0; i < numDots; i++) {
        categories.push(months[currentMonth])
        currentMonth++
        if (currentMonth > 11) {
          currentMonth = 0
        }
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
        total_cost = parseFloat(stat.total_cost)
        profit = parseFloat(stat.profit)

        let index = categories.indexOf(stat.day)

        if (index >= 0) {
          commission_values[index] = commission.toFixed(2)
          total_cost_values[index] = total_cost.toFixed(2)
          profit_values[index] = profit.toFixed(2)
        }
      }

      let themeColors = []
      if (this.canViewStats) {
        themeColors = this.themeColors.admin
        series.push({
          name: "Commission",
          data: commission_values,
        })
        series.push({
          name: "Total cost",
          data: total_cost_values,
        })
      } else {
        themeColors = this.themeColors.mediaBuyer
        series.push({
          name: "Profit",
          data: profit_values,
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
      }
    },
  },
  created() {
    this.yearsOptions = []
    let dateObj = new Date()
    let currentYear = dateObj.getFullYear()
    for (let i = 10; i >= 0; i--) {
      this.yearsOptions.push({
        id: currentYear - i,
        label: currentYear - i,
      })
    }

    this.resetFilters()
  },
  mounted () {
  }
}

</script>
