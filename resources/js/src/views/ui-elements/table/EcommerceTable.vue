<template>
    <vx-card title="Dashboard" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-dollar-sign" @click="changeView('payment_methods')">Payment methods</vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="changeView('sync_csv')">Sync CSV</vs-button>
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

            <!-- Revenue data -->
            <stats-card :title="'€ ' + stats.revenue.toFixed(2)" body="Revenue" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="'€ ' + stats.taxTotal.toFixed(2)" body="Tax Total" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <div class="vx-col w-full sm:w-full xl:w-1/2 mb-base">
            </div>

            <!-- Profit data -->
            <stats-card :title="'€ ' + stats.revenueNet.toFixed(2)" body="Net Revenue" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="'€ ' + stats.costTotal.toFixed(2)" body="Cost Total" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="'€ ' + stats.profit.toFixed(2)" body="Profit" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="stats.profitMargin.toFixed(2) + '%'" body="Profit margin" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <div class="vx-col w-full sm:w-full xl:w-full mb-base">
            </div>

            <!-- Order data -->
            <stats-card :title="stats.numOrders.toString()" body="Orders" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="'€ ' + stats.avgOrderTotal.toFixed(2)" body="AVG Order Value" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="'€ ' + stats.avgOrderCost.toFixed(2)" body="AVG Order Cost" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="'€ ' + stats.avgOrderProfit.toFixed(2)" body="AVG Order Profit" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <div class="vx-col w-full sm:w-full xl:w-full mb-base">
            </div>

            <!-- Cost data -->
            <stats-card :title="'€ ' + stats.cogCostTotal.toFixed(2)" body="COG Cost Total" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="'€ ' + stats.shippingCostTotal.toFixed(2)" body="Shipping Cost Total" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="'€ ' + stats.adSpentTotal.toFixed(2)" body="Total Ad Spent" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="'€ ' + stats.transactionFees.toFixed(2)" body="Payment Gateway fees" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="'€ ' + stats.extraCostTotal.toFixed(2)" body="Extra cost" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <div class="vx-col w-full sm:w-1/2 xl:w-3/4 mb-base">
            </div>

            <!-- Inventory data -->
            <stats-card :title="stats.inventoryStockQty.toString()" body="Stock Q.ty" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="'€ ' + stats.inventoryStockCost.toFixed(2)" body="Stock Cost" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <stats-card :title="'€ ' + stats.inventoryStockValue.toFixed(2)" body="Stock Value" class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" />

            <div class="vx-col w-full sm:w-1/2 xl:w-1/4 mb-base">
            </div>

        </div>
        
        <div v-if="view == 'payment_methods'" class="mb-12 vx-row">
            <div class="w-full mb-base" v-for="(item, index) in paymentMethods" :key="index">
                <div class="vx-row md:w-1/2 flex items-center">
                    <div class="vx-col w-full">
                        {{ item.id }}
                    </div>

                    <div class="vx-col w-full md:w-2/5">
                        <vs-input class="w-full" icon-pack="feather" icon="icon-dollar-sign" type="number" min="0.0" step="0.01" v-model="paymentMethods[index].fixed_fee" />
                    </div>

                    <div class="vx-col w-full md:w-1/5">
                        <div class="w-full text-center">+</div>
                    </div>

                    <div class="vx-col w-full md:w-2/5">
                        <vs-input class="w-full" icon-pack="feather" icon="icon-percent" type="number" min="0.0" step="0.01" v-model="paymentMethods[index].flat_fee" />
                    </div>
                </div>
            </div>
            <vs-button color="primary" icon-pack="feather" icon="icon-save" @click="updatePaymentMethodsFees">Save changes</vs-button>
        </div>

        <div v-if="view == 'sync_csv'" class="mb-12 vx-row">
            <p>
                <h1>Sync CSV</h1>

                <div class="vx-row w-full">
                    <div class="vx-col w-full">
                        Advertising
                    </div>
                    <div class="vx-col w-full">
                        <vs-input class="w-full" type="text" icon-pack="feather" icon="icon-link" v-model="advertisingCsv" />
                    </div>
                </div>

                <div class="vx-row w-full">
                    <div class="vx-col w-full">
                        Shipping
                    </div>
                    <div class="vx-col w-full">
                        <vs-input class="w-full" type="text" icon-pack="feather" icon="icon-link" v-model="shippingCsv" />
                    </div>
                </div>

                <div class="vx-row w-full">
                    <div class="vx-col w-full">
                        Extra cost
                    </div>
                    <div class="vx-col w-full">
                        <vs-input class="w-full" type="text" icon-pack="feather" icon="icon-link" v-model="extraCostCsv" />
                    </div>
                </div>

                <div class="vx-row w-full">
                    <vs-button color="primary" icon-pack="feather" icon="icon-refresh-cw" @click="updateCsv">Sync now</vs-button>
                </div>
            </p>
        </div>

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

      products: [],
      paymentMethods: [],

      advertisingCsv: '',
      shippingCsv: '',
      extraCostCsv: '',

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
        revenue: 0.0,
        taxTotal: 0.0,
        revenueNet: 0.0,
        costTotal: 0.0,
        profit: 0.0,
        profitMargin: 0.0,
        numOrders: 0,
        avgOrderTotal: 0.0,
        avgOrderCost: 0.0,
        avgOrderProfit: 0.0,
        cogCostTotal: 0.0,
        shippingTotal: 0.0,
        shippingCostTotal: 0.0,
        adSpentTotal: 0.0,
        transactionFees: 0.0,
        extraCostTotal: 0.0,
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
      } else if (view == 'payment_methods') {
        this.paymentMethods = await this.ecommerce.fetchPaymentMethods()
        this.view = view
      } else if (view == 'sync_csv') {
        this.advertisingCsv = await this.ecommerce.getAdvertisingCsv()
        this.shippingCsv = await this.ecommerce.getShippingCsv()
        this.extraCostCsv = await this.ecommerce.getExtraCostCsv()
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

      // Build payment methods fee table
      let pm = await this.ecommerce.fetchPaymentMethods()
      let paymentMethodsFeesTable = {}
      for (let i = 0; i < pm.length; i++) {
        paymentMethodsFeesTable[pm[i].id] = {
          fixed: parseFloat(pm[i].fixed_fee),
          flat: parseFloat(pm[i].flat_fee),
        }
      }

      let revenue = 0.0
      let taxTotal = 0.0
      let revenueNet = 0.0
      let costTotal = 0.0
      let profit = 0.0
      let profitMargin = 0.0
      let numOrders = 0
      let avgOrderTotal = 0.0
      let avgOrderCost = 0.0
      let avgOrderProfit = 0.0
      let cogCostTotal = 0.0
      let shippingTotal = 0.0
      let shippingCostTotal = 0.0
      let adSpentTotal = 0.0
      let transactionFees = 0.0
      let extraCostTotal = 0.0

      // Get ad spent
      let adSpent = await this.ecommerce.getAdSpent(this.filters)
      if (adSpent.hasOwnProperty('total') && adSpent['total'] != '') {
        adSpentTotal = parseFloat(adSpent['total'])
      }

      // Get shipping cost
      let shippingCost = await this.ecommerce.getExtraCost(this.filters)
      if (shippingCost.hasOwnProperty('total') && shippingCost['total'] != '') {
        shippingCostTotal = parseFloat(shippingCost['total'])
      }

      // Get extra cost
      let extraCost = await this.ecommerce.getExtraCost(this.filters)
      if (extraCost.hasOwnProperty('total') && extraCost['total'] != '') {
        extraCostTotal = parseFloat(extraCost['total'])
      }

      for (let i = 0; i < orders.length; i++) {
        let obj = orders[i]

        if (!this.filters.status.includes(obj.status)) {
          continue
        }

        numOrders++

        let orderTotal = parseFloat(obj.total)

        revenue += orderTotal

        if (paymentMethodsFeesTable.hasOwnProperty(obj.payment_method)) {
          let pm = paymentMethodsFeesTable[obj.payment_method]
          transactionFees += pm.fixed + pm.flat*orderTotal
        }

        if (obj.hasOwnProperty('total_tax') && obj.total_tax != '') {
          taxTotal += parseFloat(obj.total_tax)
        }

        if (obj.hasOwnProperty('shipping_total') && obj.shipping_total != '') {
          shippingTotal += parseFloat(obj.shipping_total)
        }

        for (let j = 0; j < obj.line_items.length; j++) {
          let item = obj.line_items[j]

          if(item.hasOwnProperty('cog_item_total_cost') && item.cog_item_total_cost != '') {
            cogCostTotal += parseFloat(item.cog_item_total_cost)
          }
        }
      }

      revenueNet = revenue - taxTotal
      costTotal = cogCostTotal + shippingCostTotal + adSpentTotal + transactionFees
      profit = revenueNet - costTotal

      if (revenue > 0) {
        profitMargin = (profit / revenue)*100
      }

      if (numOrders > 0) {
        avgOrderTotal = revenueNet / numOrders
        avgOrderCost = costTotal / numOrders
        avgOrderProfit = profit / numOrders
      }

      // Inventory stats
      let inventoryStockQty = 0
      let inventoryStockCost = 0.0
      let inventoryStockValue = 0.0
      for (let i = 0; i < this.products.length; i++) {
        let p = this.products[i]

        if (p.stock_quantity != null) {
          inventoryStockQty += parseInt(p.stock_quantity)
        }
        inventoryStockCost += parseFloat(p.cog_cost)
        inventoryStockValue += parseFloat(p.price)
      }

      this.stats = {
        revenue: revenue,
        taxTotal: taxTotal,
        revenueNet: revenueNet,
        costTotal: costTotal,
        profit: profit,
        profitMargin: profitMargin,
        numOrders: numOrders,
        avgOrderTotal: avgOrderTotal,
        avgOrderCost: avgOrderCost,
        avgOrderProfit: avgOrderProfit,
        cogCostTotal: cogCostTotal,
        shippingTotal: shippingTotal,
        shippingCostTotal: shippingCostTotal,
        adSpentTotal: adSpentTotal,
        transactionFees: transactionFees,
        extraCostTotal: extraCostTotal,

        inventoryStockQty: inventoryStockQty,
        inventoryStockCost: inventoryStockCost,
        inventoryStockValue: inventoryStockValue,
      }
    },

    async updatePaymentMethodsFees() {
      this.$vs.loading()

      for (let i = 0; i < this.paymentMethods.length; i++) {
        let pm = this.paymentMethods[i]
        await this.ecommerce.updatePaymentMethodFees(pm.id, pm.fixed_fee, pm.flat_fee)
      }

      this.$vs.notify({
        title: 'Success',
        text: 'Fees updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()
    },

    async updateCsv() {
      this.$vs.loading()

      await this.ecommerce.updateAdvertisingCsv(this.advertisingCsv)
      await this.ecommerce.updateShippingCsv(this.shippingCsv)
      await this.ecommerce.updateExtraCostCsv(this.extraCostCsv)

      this.$vs.notify({
        title: 'Success',
        text: 'CSVs synced',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()
    },
  },
  async created() {
    this.ecommerce = new Ecommerce(localStorage.getItem('accessToken'))

    this.$vs.loading()

    this.products = await this.ecommerce.fetchProducts()

    await this.resetFilters()

    this.$vs.loading.close()
  },
}

</script>
