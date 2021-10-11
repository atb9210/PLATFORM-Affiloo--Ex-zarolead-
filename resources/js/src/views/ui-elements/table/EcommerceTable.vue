<template>
    <vx-card title="Table" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <span class="btn-group flex items-center mr-2">
                <vs-button @click="changeView('orders')" :color="(view == 'orders') ? 'success' : 'primary'">Orders</vs-button>
                <vs-button @click="changeView('customers')" :color="(view == 'customers') ? 'success' : 'primary'">Customers</vs-button>
                <vs-button @click="changeView('products')" :color="(view == 'products') ? 'success' : 'primary'">Products</vs-button>
            </span>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-dollar-sign" @click="calculateProfit">Calc Profit</vs-button>
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

        <div v-if="showProfit" class="mb-12 vx-row">
            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="PackageIcon" :statistic="this.stats.completedOrders" statisticTitle="Completed orders" color="primary"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="PackageIcon" :statistic="this.stats.refundedOrders" statisticTitle="Refunded orders" color="warning"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="PackageIcon" :statistic="this.stats.failedOrders" statisticTitle="Failed orders" color="danger"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="PackageIcon" :statistic="this.stats.cancelledOrders" statisticTitle="Cancelled orders" color="danger"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="DollarSignIcon" :statistic="'€ ' + this.stats.revenue.toFixed(2)" statisticTitle="Revenue" color="primary"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="DollarSignIcon" :statistic="'€ ' + this.stats.profit.toFixed(2)" statisticTitle="Profit" color="success"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="PercentIcon" :statistic="'€ ' + this.stats.vatTotal.toFixed(2)" statisticTitle="VAT Total" color="danger"></statistics-card-line>
            </div>

            <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
                <statistics-card-line icon="PercentIcon" :statistic="'€ ' + this.stats.paymentGatewayFees.toFixed(2)" statisticTitle="Payment Gateway fees" color="danger"></statistics-card-line>
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

        <vue-context ref="order_context_menu" v-slot="{ data }">
            <!--
            <li>
              <a href="#" @click="$refs.order_context_menu.close();editForm(data.id);" class="flex items-center text-sm">
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.order_context_menu.close();deleteObject(data.id);" class="flex items-center text-sm">
                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Delete</span>
              </a>
            </li>
            -->
            <li>
              <a href="#" @click="$refs.order_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vue-context ref="product_context_menu" v-slot="{ data }">
            <!--
            <li>
              <a href="#" @click="$refs.product_context_menu.close();editForm(data.id);" class="flex items-center text-sm">
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.product_context_menu.close();deleteObject(data.id);" class="flex items-center text-sm">
                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Delete</span>
              </a>
            </li>
            -->
            <li>
              <a href="#" @click="$refs.product_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vue-context ref="customer_context_menu" v-slot="{ data }">
            <!--
            <li>
              <a href="#" @click="$refs.customer_context_menu.close();editForm(data.id);" class="flex items-center text-sm">
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.customer_context_menu.close();deleteObject(data.id);" class="flex items-center text-sm">
                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Delete</span>
              </a>
            </li>
            -->
            <li>
              <a href="#" @click="$refs.customer_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <!-- Orders table -->
        <vs-table v-if="view == 'orders'" stripe multiple v-model="selectedOrders" :max-items="maxItems" pagination search :data="orders" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Id</vs-th>
                <vs-th sort-key="date">Date</vs-th>
                <vs-th sort-key="products">Products</vs-th>
                <vs-th sort-key="total">Amount</vs-th>
                <vs-th sort-key="status">Status</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                    <vs-td class="column-no-padding" :data="item.id">
                        #{{item.id}}
                    </vs-td>
                    
                    <vs-td class="column-no-padding" :data="item.date">
                        {{formatDate(item.date)}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.products">
                        <div @contextmenu.prevent="$refs.order_context_menu.open($event, item)">
                            <vs-chip>
                                {{item.products}}
                            </vs-chip>
                        </div>
                    </vs-td>
                    
                    <vs-td class="column-no-padding" :data="item.id">
                        {{item.currency_symbol}} {{item.total}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.status">
                        <vs-chip :color="orderStatusColor[item.status]">
                            {{item.status}}
                        </vs-chip>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Products table -->
        <vs-table v-if="view == 'products'" stripe multiple v-model="selectedProducts" :max-items="maxItems" pagination search :data="products" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Id</vs-th>
                <vs-th sort-key="name">name</vs-th>
                <vs-th sort-key="price">Price</vs-th>
                <vs-th sort-key="status">Status</vs-th>
                <vs-th sort-key="stock_status">Stock Status</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                    <vs-td class="column-no-padding" :data="item.id">
                        {{item.id}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.name">
                        <div @contextmenu.prevent="$refs.product_context_menu.open($event, item)">
                            <vs-chip>
                                {{item.name}}
                            </vs-chip>
                        </div>
                    </vs-td>
                    
                    <vs-td class="column-no-padding" :data="item.id">
                        € {{item.price}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.status">
                        <vs-chip :color="productStatusColor[item.status]">
                            {{item.status}}
                        </vs-chip>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.stock_status">
                        <vs-chip :color="productStockStatusColor[item.stock_status]">
                            {{item.stock_status}}
                        </vs-chip>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Customers table -->
        <vs-table v-if="view == 'customers'" stripe multiple v-model="selectedCustomers" :max-items="maxItems" pagination search :data="customers" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Id</vs-th>
                <vs-th sort-key="name">name</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                    <vs-td class="column-no-padding" :data="item.id">
                        {{item.id}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.name">
                        <div @contextmenu.prevent="$refs.customer_context_menu.open($event, item)">
                            <vs-chip>
                                {{item.name}}
                            </vs-chip>
                        </div>
                    </vs-td>
                    
                </vs-tr>
            </template>
        </vs-table>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'

export default {
  components: {
    VueContext,
    StatisticsCardLine,
  },
  data () {
    return {
      maxItems: 20,
      view: 'orders',

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
      },

      orders: [],
      selectedOrders: [],

      customers: [],
      selectedCustomers: [],

      products: [],
      selectedProducts: [],

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

      productStatuses: [
        'publish',
        'pending',
        'draft',
      ],
      productStatusColor: {
        'publish': 'success',
        'pending': 'primary',
        'draft':   'warning',
      },

      productStockStatuses: [
        'instock',
        'outofstock',
      ],
      productStockStatusColor: {
        'instock':    'success',
        'outofstock': 'danger',
      },

      showProfit: false,
      stats: {},
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
      if (this.view == 'orders') {
        return this.orders
      } else if (this.view == 'customers') {
        return this.customers
      }
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

      if (view == 'orders') {
        this.orders = await this.fetchOrders()
        this.view = view
      } else if (view == 'products') {
        this.products = await this.fetchProducts()
        this.view = view
      } else if (view == 'customers') {
        this.customers = await this.fetchCustomers()
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
      this.$vs.loading()

      this.orders = await this.fetchOrders()

      let completedOrders = 0
      let refundedOrders = 0
      let failedOrders = 0
      let cancelledOrders = 0

      let paymentGatewayFees = 0.0
      let revenue = 0.0
      let profit = 0.0

      for (let i = 0; i < this.orders.length; i++) {
        let obj = this.orders[i]

        // DEBUG
        // TODO: to uncomment
        /*
        if (obj.status === 'refunded') {
          refundedOrders++
          continue
        } else if (obj.status === 'failed') {
          failedOrders++
          continue
        } else if (obj.status === 'cancelled') {
          cancelledOrders++
          continue
        } else if (obj.status !== 'completed') {
          continue
        }
        */

        completedOrders++

        let orderTotal = parseFloat(obj.total)

        revenue += orderTotal
        paymentGatewayFees += 0.3 + 0.029*orderTotal
      }

      // Subtract payment gateway fees from revenue
      profit = revenue - paymentGatewayFees

      // Subtract VAT from revenue
      let vat = 0.22
      let vatTotal = profit*vat
      profit -= vatTotal

      this.stats = {
        completedOrders: completedOrders,
        refundedOrders: refundedOrders,
        failedOrders: failedOrders,
        cancelledOrders: cancelledOrders,
        revenue: revenue,
        profit: profit,
        paymentGatewayFees: paymentGatewayFees,
        vatTotal: vatTotal,
      }

      this.showProfit = true

      this.$vs.loading.close()
    },

    // Backend functions
    async fetchOrders() {
      let objects = []

      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        let params = {}

        if (this.filters.date_from != '') {
          params.filter_date_from = this.filters.date_from
        }
        if (this.filters.date_to != '') {
          params.filter_date_to = this.filters.date_to
        }

        const response = await this.$http.get('/api/3rd/ecommerce/orders', { headers, params })
        if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
          objects = response.data.data
        }

        for (let i = 0; i < objects.length; i++) {
          let obj = objects[i]

          obj.date = new Date(obj.date_created);

          obj.products = ''
          if (obj.hasOwnProperty('line_items')) {
            if (obj.line_items.length > 0) {
              obj.products += obj.line_items[0].name
            }
            for (let j = 1; j < obj.line_items.length; j++) {
              obj.products += ', ' + obj.line_items[j].name
            }
          }

          obj.customer = null
          if (obj.hasOwnProperty('customer_id')) {
            obj.customer = this.getProductById(obj.customer_id)
          }

          if (obj.hasOwnProperty('total')) {
            obj.total = parseFloat(obj.total)
          }
        }

        /*objects = objects.map((obj) => {
          if (obj.hasOwnProperty('total')) {
            obj.total = parseFloat(obj.total)
          }
          return obj
        })*/
      } catch (error) {
        // Nothing
      }

      return objects
    },
    async fetchCustomers() {
      let objects = []

      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        const response = await this.$http.get('/api/3rd/ecommerce/customers', { headers })
        if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
          objects = response.data.data
        }
      } catch (error) {
        // Nothing
      }

      return objects
    },
    async fetchProducts() {
      let objects = []

      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        const response = await this.$http.get('/api/3rd/ecommerce/products', { headers })
        if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
          objects = response.data.data
        }
      } catch (error) {
        // Nothing
      }

      return objects
    },
  },
  async created() {
    this.$vs.loading()

    await this.resetFilters()

    this.$vs.loading.close()
  },
}

</script>
