<template>
    <vx-card title="Dashboard" code-toggler>

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
                <div class="vx-col w-1/2">
                    <label>From</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_from" />
                </div>
                <div class="vx-col w-1/2">
                    <label>To</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_to" />
                </div>
            </div>

            <div class="vx-row mb-2">
              <div class="vx-col w-full">
                <vs-select
                    class="w-full"
                    multiple
                    autocomplete
                    label="Order status"
                    v-model="filters.status"
                    >
                    <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in orderStatuses" />
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

        <div v-if="view == 'dashboard'" class="mb-12 vx-row">
            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="PackageIcon" :statistic="stats.numOrders" statisticTitle="Orders" color="primary"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="DollarSignIcon" :statistic="'€ ' + stats.revenue.toFixed(2)" statisticTitle="Revenue" color="primary"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="DollarSignIcon" :statistic="'€ ' + stats.profit.toFixed(2)" statisticTitle="Profit" color="success"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="PercentIcon" :statistic="'€ ' + stats.taxTotal.toFixed(2)" statisticTitle="Tax Total" color="danger"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="PercentIcon" :statistic="'€ ' + stats.paymentGatewayFees.toFixed(2)" statisticTitle="Payment Gateway fees" color="danger"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="PackageIcon" :statistic="'€ ' + stats.avgOrderTotal.toFixed(2)" statisticTitle="AVG Order Value" color="danger"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="LayoutIcon" :statistic="'€ ' + stats.adSpent.toFixed(2)" statisticTitle="Total Ad Spent" color="danger"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="PercentIcon" :statistic="stats.profitMargin.toFixed(2) + '%'" statisticTitle="Profit margin" color="success"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="PercentIcon" :statistic="stats.roas.toFixed(2) + '%'" statisticTitle="ROAS" color="success"></statistics-card-line>
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


    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'

import Ecommerce from 'ecommerce'

export default {
  components: {
    VueContext,
    StatisticsCardLine,
  },
  data () {
    return {
      ecommerce: null,

      maxItems: 20,
      view: 'dashboard',

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        status: ['completed','pending','processing'],
      },

      orders: [],
      selectedOrders: [],

      orderStatuses: [
        'pending',
        'processing',
        'on-hold',
        'completed',
        'cancelled',
        'refunded',
        'failed',
      ],
      orderStatusColor: {
        'pending':    'primary',
        'processing': 'primary',
        'on-hold':    'warning',
        'completed':  'success',
        'cancelled':  'danger',
        'refunded':   'success',
        'failed':     'danger',
      },

      stats: {
        numOrders: 0,
        revenue: 0.0,
        profit: 0.0,
        paymentGatewayFees: 0.0,
        taxTotal: 0.0,
        avgOrderTotal: 0.0,
        adSpent: 0.0,
        profitMargin: 0.0,
        roas: 0.0,
      },
    }
  },
  computed: {
    paginationPageSize() {
      if (this.gridApi) return this.gridApi.paginationGetPageSize()
      else return 50
    },
    totalPages() {
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
    objects() {
      return []
    },
  },
  methods: {
    // Frontend functions
    onPageChanged(val) {
      this.currentPage = val
    },
    async changeView(view) {
      this.$vs.loading()

      if (view == 'dashboard') {
        await this.calculateProfit()
        this.view = view
      }

      this.$vs.loading.close()
    },
    getObjectById(id) {
      for (let i = 0; i < this.orders.length; i++) {
        if (this.objects[i].id === id) {
          return this.objects[i]
        }
      }
      return null
    },
    getOrderById(id) {
      for (let i = 0; i < this.orders.length; i++) {
        if (this.orders[i].id === id) {
          return this.orders[i]
        }
      }
      return null
    },
    getProductById(id) {
      for (let i = 0; i < this.products.length; i++) {
        if (this.products[i].id === id) {
          return this.products[i]
        }
      }
      return null
    },
    getCustomerById(id) {
      for (let i = 0; i < this.customers.length; i++) {
        if (this.customers[i].id === id) {
          return this.customers[i]
        }
      }
      return null
    },
    async applyFilters() {
      await this.changeView(this.view)
    },
    async resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        status: ['completed','pending','processing'],
      }
      await this.filterDatesShortcut('this_month')
    },
    async filterDatesShortcut(period) {
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
        await this.applyFilters()
      }
    },
    formatDate(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0')
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0')
      let yyyy = dateObj.getFullYear()
      
      let formattedDate = yyyy + '-' + mm + '-' + dd
      return formattedDate
    },
    async calculateProfit() {
      let orders = await this.ecommerce.fetchOrders(this.filters)

      let paymentGatewayFeesTable = {
        'paypal': {
          fixed: 0.3,
          rate:  0.029,
        },
        'stripe': {
          fixed: 0.3,
          rate:  0.029,
        },
      }

      let numOrders = 0
      let avgOrderTotal = 0.0
      let paymentGatewayFees = 0.0
      let revenue = 0.0
      let taxTotal = 0.0
      let profit = 0.0
      let profitMargin = 0.0
      let roas = 0.0
      let adSpent = {}

      // TODO: Get ad spent
      let csvUrl = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSJCULZurYAQAGkak5mbvxjQthxjXQiB5XXk83gLua_pn_EqENBpS6J8EWzxGTkrs6MThi1ksRe5NCz/pub?gid=0&single=true&output=csv'
      adSpent = await this.ecommerce.getAdSpentFromCsv(csvUrl)

      for (let i = 0; i < orders.length; i++) {
        let obj = orders[i]

        if (!this.filters.status.includes(obj.status)) {
          continue
        }

        numOrders++

        let orderTotal = parseFloat(obj.total)

        revenue += orderTotal

        if (paymentGatewayFeesTable.hasOwnProperty(obj.payment_method)) {
          let pm = paymentGatewayFeesTable[obj.payment_method]
          paymentGatewayFees += pm.fixed + pm.rate*orderTotal
        }

        if (obj.hasOwnProperty('tax_total')) {
          taxTotal += obj.tax_total
        }
      }

      if (numOrders > 0) {
        avgOrderTotal = revenue / numOrders
      }

      profit = revenue - paymentGatewayFees - taxTotal - adSpent['total']

      if (revenue > 0) {
        profitMargin = (profit / revenue)*100
      }

      if (adSpent['total'] > 0) {
        roas = (profit / adSpent['total'])*100
      } else if (profit > 0) {
        roas = 9999.99
      }

      this.stats = {
        numOrders: numOrders,
        revenue: revenue,
        profit: profit,
        paymentGatewayFees: paymentGatewayFees,
        taxTotal: taxTotal,
        avgOrderTotal: avgOrderTotal,
        adSpent: adSpent['total'],
        profitMargin: profitMargin,
        roas: roas,
      }
    },
  },
  async created() {
    this.ecommerce = new Ecommerce(localStorage.getItem('accessToken'))

    this.$vs.loading()

    await this.resetFilters()

    this.$vs.loading.close()
  },
}

</script>
