<template>
    <vx-card title="Dropin Shippings" code-toggler>

        <p class="flex items-center mb-4">
            <vx-tooltip text="Show filters" position="top">
                <vs-button v-if="view == 'shippings'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Show stats" position="top">
                <vs-button v-if="view == 'shippings'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-pie-chart" @click="showStats = !showStats"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Refresh selected shippings" position="top">
                <vs-button v-if="view == 'shippings'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" :disabled="selectedShippings.length <= 0" @click="refreshShippings(selectedShippings)"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Import shippings" position="top">
                <vs-button v-if="view == 'shippings'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-download" @click="refreshShippingsTable"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Mark shippings" position="top">
                <vs-button v-if="view == 'shippings'" class="mr-2" color="warning" type="filled" icon-pack="feather" icon="icon-bookmark" :disabled="selectedShippings.length <= 0" @click="markShippings(selectedShippings)"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Copy phone numbers" position="top">
                <vs-button v-if="view == 'shippings'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-copy" :disabled="selectedShippings.length <= 0" @click="copyPhoneNumbers(selectedShippings)"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Copy IDs" position="top">
                <vs-button v-if="view == 'shippings'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-copy" :disabled="selectedShippings.length <= 0" @click="copyIds(selectedShippings)"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Send Tracking URL via Whatsapp" position="top">
                <vs-button v-if="view == 'shippings'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-message-circle" :disabled="selectedShippings.length <= 0" @click="sendTrackingUrl(selectedShippings)"></vs-button>
            </vx-tooltip>
        </p>

        <div v-if="showStats" class="vx-row">
          <!-- TODO: HERE -->
          <div class="vx-col w-full sm:w-1/2 md:w-1/5 mb-base">
            <vx-card>
                <h2>ALL</h2>
                <div>{{totalStats.orders['ALL'].count}}</div>
                <div>€ {{totalStats.orders['ALL'].total.toFixed(2)}}</div>
                <div>% {{totalStats.orders['ALL'].percent.toFixed(2)}}</div>
            </vx-card>
          </div>

          <div class="vx-col w-full sm:w-1/2 md:w-1/5 mb-base">
            <vx-card>
                <h2>TO_SHIP</h2>
                <div>{{totalStats.orders['TO_SHIP'].count}}</div>
                <div>€ {{totalStats.orders['TO_SHIP'].total.toFixed(2)}}</div>
                <div>% {{totalStats.orders['TO_SHIP'].percent.toFixed(2)}}</div>
            </vx-card>
          </div>

          <div class="vx-col w-full sm:w-1/2 md:w-1/5 mb-base">
            <vx-card>
                <h2>SHIPPED</h2>
                <div>{{totalStats.orders['SHIPPED'].count}}</div>
                <div>€ {{totalStats.orders['SHIPPED'].total.toFixed(2)}}</div>
                <div>% {{totalStats.orders['SHIPPED'].percent.toFixed(2)}}</div>
            </vx-card>
          </div>

          <div class="vx-col w-full sm:w-1/2 md:w-1/5 mb-base">
            <vx-card>
                <h2>DELIVERED</h2>
                <div>{{totalStats.orders['DELIVERED'].count}}</div>
                <div>€ {{totalStats.orders['DELIVERED'].total.toFixed(2)}}</div>
                <div>% {{totalStats.orders['DELIVERED'].percent.toFixed(2)}}</div>
            </vx-card>
          </div>

          <div class="vx-col w-full sm:w-1/2 md:w-1/5 mb-base">
            <vx-card>
                <h2>RETURNED</h2>
                <div>{{totalStats.orders['RETURNED'].count}}</div>
                <div>€ {{totalStats.orders['RETURNED'].total.toFixed(2)}}</div>
                <div>% {{totalStats.orders['RETURNED'].percent.toFixed(2)}}</div>
            </vx-card>
          </div>

          <div class="vx-col w-full sm:w-1/2 md:w-1/5 mb-base">
            <vx-card>
                <h2>ERROR</h2>
                <div>{{totalStats.orders['ERROR'].count}}</div>
                <div>€ {{totalStats.orders['ERROR'].total.toFixed(2)}}</div>
                <div>% {{totalStats.orders['ERROR'].percent.toFixed(2)}}</div>
            </vx-card>
          </div>

        </div>

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
                <div class="vx-col w-1/2">
                    <label>Dropin Status</label>
                    <my-select v-model="filters.dropin_statuses" :options="dropinStatuses" :autocomplete="true" multiple class="w-full select-large" />
                </div>
                <div class="vx-col w-1/2">
                    <label>Packeta Status</label>
                    <my-select v-model="filters.packeta_statuses" :options="packetaStatuses" :autocomplete="true" multiple class="w-full select-large" />
                </div>
                <div class="vx-col w-1/2">
                    <label>Carrier Status</label>
                    <my-select v-model="filters.carrier_statuses" :options="carrierStatuses" :autocomplete="true" multiple class="w-full select-large" />
                </div>
                <div class="vx-col w-1/2">
                    <label>Product</label>
                    <my-select v-model="filters.products" :options="products" :autocomplete="true" multiple class="w-full select-large" />
                </div>
                <div class="vx-col w-1/2">
                    <label>Method</label>
                    <my-select v-model="filters.payment_method" :options="paymntMethods" :autocomplete="true" class="w-full select-large" />
                </div>
            </div>
            
            <!-- TODO: HERE -->
            <!--
            <div class="vx-row mb-2">
                <vs-slider step="10" min="0" max="200" v-model="filters.amount_min" />
            </div>
            -->
            
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

        <vue-context ref="shipping_context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.shipping_context_menu.close();refreshShippings([data])" class="flex items-center text-sm">
                <feather-icon icon="RefreshCwIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Refresh Shipping Status</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.shipping_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <!-- Shippings table -->
        <vs-table v-if="view == 'shippings'" multiple v-model="selectedShippings" :max-items="maxItems" pagination search :data="shippings" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">ID</vs-th>
                <vs-th sort-key="order_id">Order</vs-th>
                <vs-th sort-key="product">Prod.</vs-th>
                <vs-th sort-key="created_at">Creation Date</vs-th>
                <vs-th sort-key="started_at">Start Date</vs-th>
                <vs-th sort-key="processing_time">Proc. Time</vs-th>
                <vs-th sort-key="name">Customer</vs-th>
                <vs-th sort-key="phone">Phone</vs-th>
                <vs-th sort-key="payment_method">Mtd</vs-th>
                <vs-th sort-key="cod_amount">COD amount</vs-th>
                <vs-th sort-key="status">Dropin Status</vs-th>
                <vs-th sort-key="tracking_url">Track.</vs-th>
                <vs-th sort-key="brt_tracking_url">Brt</vs-th>
                <vs-th sort-key="packeta_status">Pkt Status</vs-th>
                <vs-th sort-key="carrier_status_name">Brt Status</vs-th>
                <vs-th sort-key="status_check_date">Last Check</vs-th>
                <vs-th sort-key="delivered_at">Del. Date</vs-th>
                <vs-th sort-key="amount">Am.t</vs-th>
                <vs-th sort-key="delivery_price">Ship. Price</vs-th>
                <vs-th sort-key="vat">VAT</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data" :class="getRowClass(item)">

                    <vs-td class="column-no-padding" :data="item.id">
                        <div @contextmenu.prevent="$refs.shipping_context_menu.open($event, item)">
                            <vs-chip>
                                #{{item.id}}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.order_id">
                        <fast-editable-value
                            v-model="newValue"
                            @onEdit="newValue = (item.order_id != '' && item.order_id != null) ? item.order_id : ''"
                            @onUpdate="updateOrderId(data[indextr])">
                            {{ (item.order_id != '' && item.order_id != null) ? item.order_id : 'none' }}
                        </fast-editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.product">
                        {{ firstNCharacters(item.product, 15) }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.created_at">
                        {{ formatDate(new Date(item.created_at)) }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.started_at">
                        {{ (item.started_at != '') ? formatDate(new Date(item.started_at)) : '' }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.processing_time">
                        {{ item.processing_time }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.name">
                        {{ firstNCharacters(item.name, 20) }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.phone">
                        {{ item.phone }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.payment_method">
                        {{ item.payment_method }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.cod_amount">
                        € {{ item.cod_amount.toFixed(2) }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.staus">
                        {{ item.status }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.tracking_url">
                        <a :href="item.tracking_url" v-if="item.tracking_url !== null && item.tracking_url != ''">
                            <!--
                            {{ getTrackingIdFromUrl(item.tracking_url) }}
                            -->
                            link
                        </a>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.brt_tracking_url">
                        <a :href="item.brt_tracking_url" v-if="item.brt_tracking_url !== null && item.brt_tracking_url != ''">
                            <!--
                            {{ item.brt_tracking_number }}
                            -->
                            link
                        </a>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.packeta_status">
                        {{ firstNCharacters(item.packeta_status, 15) }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.carrier_status_name">
                        {{ firstNCharacters(item.carrier_status_name, 15) }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.status_check_date">
                        {{ (item.status_check_date != '') ? formatDate(new Date(item.status_check_date)) : '' }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.delivered_at">
                        {{ (item.delivered_at != '') ? formatDate(new Date(item.delivered_at)) : '' }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.amount">
                        € {{ item.amount.toFixed(2) }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.delivery_price">
                        € {{ item.delivery_price.toFixed(2) }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.vat">
                        € {{ item.vat.toFixed(2) }}
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding">{{data.length}}</vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding">€ {{totalStats.cod_amount.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding">€ {{totalStats.amount.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{totalStats.delivery_price.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{totalStats.vat.toFixed(2)}}</vs-td>

            </template>
        </vs-table>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'

import Dropin from 'dropin'

function formatMoney(num) {
  let s = ''
  if (typeof(num) === 'number') {
    s = num.toFixed(2)
  } else {
    s = parseFloat(num.replace(',', '.')).toFixed(2)
  }
  return s.replace(',', '.')
}

export default {
  components: {
    VueContext,
  },
  data () {
    return {
      dropin: null,

      maxItems: 20,
      view: 'shippings',

      shippings: [],
      selectedShippings: [],

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        dropin_statuses: [],
        packeta_statuses: [],
        carrier_statuses: [],
        amount_min: 0,
        amount_max: 100,
        products: [],
        payment_method: '',
      },

      showStats: false,

      showPopup: {
      },

      totalStats: {
        amount: 0.0,
        delivery_price: 0.0,
        tax: 0.0,
        vat: 0.0,
        cod_amount: 0.0,

        orders: {
          'ALL': {
            count: 0,
            total: 0.0,
            percent: 0.0,
          },
          'TO_SHIP': {
            count: 0,
            total: 0.0,
            percent: 0.0,
          },
          'SHIPPED': {
            count: 0,
            total: 0.0,
            percent: 0.0,
          },
          'DELIVERED': {
            count: 0,
            total: 0.0,
            percent: 0.0,
          },
          'RETURNED': {
            count: 0,
            total: 0.0,
            percent: 0.0,
          },
          'ERROR': {
            count: 0,
            total: 0.0,
            percent: 0.0,
          },
        },
      },

      dropinStatuses: [
        'Shipped',
        'Canceled',
        'Ready for shipping',
        'Error',
        'Awaiting payment',
      ],
      packetaStatuses: [
        'Delivered',
        'Expecting a handover by eshop.',
        'Send by courier',
        'Return',
      ],
      carrierStatuses: [
        'CONSEGNATA',
        'RIFIUTA SENZA MOTIVAZIONE',
        'ASSENTE DOPO LASCIATO AVVISO',
      ],

      orders: [],
      products: [],
      paymntMethods: [
        'cod',
        'online',
      ],

      newValue: '',
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
      if (this.view == 'shippings') {
        return this.shippings
      }
      return []
    },
  },
  methods: {
    // Frontend functions
    onPageChanged(val) {
      this.currentPage = val
    },
    async changeView(view, parent = null) {
      this.$vs.loading()

      if (view == 'shippings') {
        await this.fetchOrders()
        this.shippings = await this.dropin.fetchShippings(this.filters)
        for (let i = 0; i < this.shippings.length; i++) {
          let obj = this.shippings[i]

          let dropinStatus = obj.status
          let packetaStatus = obj.packeta_status
          let carrierStatus = this.getCarrierStatus(obj)

          if (dropinStatus !== null && dropinStatus != '' && !this.dropinStatuses.includes(dropinStatus)) {
            this.dropinStatuses.push(dropinStatus)
          }
          if (packetaStatus !== null && packetaStatus != '' && !this.packetaStatuses.includes(packetaStatus)) {
            this.packetaStatuses.push(packetaStatus)
          }
          if (carrierStatus !== null && carrierStatus != '' && !this.carrierStatuses.includes(carrierStatus)) {
            this.carrierStatuses.push(carrierStatus)
          }

          obj.product = ''
          if (obj.info !== null && obj.info.hasOwnProperty('order_id') && obj.info_order_id !== null) {
            let order = this.getObjectById(obj.info.order_id, this.orders)
            if (order !== null) {
              obj.product = order.product
            }
          }
        }

        // Hide cancelled shippings by default
        let filterDropinStatuses = this.filters.dropin_statuses
        if (filterDropinStatuses.length == 0) {
          this.shippings = this.shippings.filter((obj) => {
            if (obj.status == 'Canceled') {
              return false
            }
            return true
          })
        }

        // Products filter
        let filterProducts = this.filters.products
        if (filterProducts.length > 0) {
          this.shippings = this.shippings.filter((obj) => {
            return (filterProducts.includes(obj.product))
          })
        }

        // Method filter
        let filterMethod = this.filters.payment_method
        if (filterMethod !== null && filterMethod != '') {
          this.shippings = this.shippings.filter((obj) => {
            return (obj.payment_method == filterMethod)
          })
        }

        this.refreshSelectedShippings()

        this.calculateTotalStats()
      }

      this.$vs.loading.close()
    },
    getObjectById(id, objects = null) {
      if (objects === null) {
        objects = this.objects
      }
      for (let i = 0; i < objects.length; i++) {
        if (objects[i].id == id) {
          return objects[i]
        }
      }
      return null
    },
    refreshSelectedShippings() {
      let newSelectedShippings = []
      for (let i = 0; i < this.selectedShippings.length; i++) {
        let sel = this.selectedShippings[i]
        let obj = this.getObjectById(sel.id)
        if (obj !== null) {
          newSelectedShippings.push(obj)
        }
      }
      this.selectedShippings = newSelectedShippings
    },
    async applyFilters() {
      await this.changeView(this.view)
    },
    async resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        dropin_statuses: [],
        packeta_statuses: [],
        carrier_statuses: [],
        amount_min: 0,
        amount_max: 100,
        products: [],
        payment_method: '',
      }
      await this.filterDatesShortcut('this_month')
    },
    getTrackingIdFromUrl(tracking_url) {
      if (tracking_url === null || tracking_url == '') {
        return ''
      }
      let u = new URL(tracking_url)
      return u.searchParams.get('id')
    },
    getCarrierStatus(item) {
      if (item.hasOwnProperty('carrier_status') && item.carrier_status !== null) {
        if (item.carrier_status.date != '' && item.carrier_status.status != '') {
          return item.carrier_status.status
        }
      }
      return ''
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
    formatDateTime(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0')
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0')
      let yyyy = dateObj.getFullYear()
      let hh = String(dateObj.getHours() - 1).padStart(2, '0') // TODO: Why -1?
      let ii = String(dateObj.getMinutes()).padStart(2, '0')
      
      let formattedDate = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + ii

      return formattedDate
    },
    async refreshShippings(shippings) {
      let numShippings = shippings.length
      if (numShippings <= 0) {
        return
      }

      this.$vs.loading()
      for (let i = 0; i < numShippings; i++) {
        await this.dropin.refreshShipping(shippings[i].id)
      }
      this.$vs.loading.close()

      await this.applyFilters()
    },
    async refreshShippingsTable() {
      this.$vs.loading()
      await this.dropin.refreshShippingsTable()
      this.$vs.loading.close()
    },
    getEffectiveStatus(shipping) {
      let effectiveStatus = 'TO_SHIP'

      if (shipping.status == 'Error') {
        effectiveStatus = 'ERROR'
      } else if (shipping.hasOwnProperty('carrier_status_changes') && shipping.carrier_status_changes !== null) {

        // We need to first check if there's a CONSEGNATA status also for the return.
        // We don't want to confused that with the real CONSEGNATA status.
        for (let i = 0; i < shipping.carrier_status_changes.length; i++) {
          let status = shipping.carrier_status_changes[i].status
          if (status == 'RESO MITTENTE') {
            effectiveStatus = 'RETURNED'
          }
        }

        if (effectiveStatus != 'RETURNED') {
          for (let i = 0; i < shipping.carrier_status_changes.length; i++) {
            let status = shipping.carrier_status_changes[i].status
            if (status == 'CONSEGNATA') {
              effectiveStatus = 'DELIVERED'
              break
            } else if (status == 'RITIRATA') {
              effectiveStatus = 'SHIPPED'
              break
            }
          }
        }
      }

      return effectiveStatus
    },
    calculateTotalStats() {
      this.totalStats = {
        amount: 0.0,
        delivery_price: 0.0,
        tax: 0.0,
        vat: 0.0,
        cod_amount: 0.0,
        orders: {
          'ALL': {
            count: this.objects.length,
            total: 0.0,
            percent: 100.0,
          },
          'TO_SHIP': {
            count: 0,
            total: 0.0,
            percent: 0.0,
          },
          'SHIPPED': {
            count: 0,
            total: 0.0,
            percent: 0.0,
          },
          'DELIVERED': {
            count: 0,
            total: 0.0,
            percent: 0.0,
          },
          'RETURNED': {
            count: 0,
            total: 0.0,
            percent: 0.0,
          },
          'ERROR': {
            count: 0,
            total: 0.0,
            percent: 0.0,
          },
        },
      }

      let orders = {
        'ALL': {
          count: this.objects.length,
          total: 0.0,
          percent: 100.0,
        },
        'TO_SHIP': {
          count: 0,
          total: 0.0,
          percent: 0.0,
        },
        'SHIPPED': {
          count: 0,
          total: 0.0,
          percent: 0.0,
        },
        'DELIVERED': {
          count: 0,
          total: 0.0,
          percent: 0.0,
        },
        'RETURNED': {
          count: 0,
          total: 0.0,
          percent: 0.0,
        },
        'ERROR': {
          count: 0,
          total: 0.0,
          percent: 0.0,
        },
      }

      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        if (obj.hasOwnProperty('amount')) {
          this.totalStats.amount += parseFloat(obj.amount)
        }
        if (obj.hasOwnProperty('delivery_price')) {
          this.totalStats.delivery_price += parseFloat(obj.delivery_price)
        }
        if (obj.hasOwnProperty('tax')) {
          this.totalStats.tax += parseFloat(obj.tax)
        }
        if (obj.hasOwnProperty('vat')) {
          this.totalStats.vat += parseFloat(obj.vat)
        }
        if (obj.hasOwnProperty('cod_amount')) {
          this.totalStats.cod_amount += parseFloat(obj.cod_amount)
        }

        let effectiveStatus = this.getEffectiveStatus(obj)
        console.log(effectiveStatus)
        orders[effectiveStatus].count += 1
        console.log(effectiveStatus + ' done')
        orders[effectiveStatus].total += parseFloat(obj.amount)
      }
      orders['ALL'].total = this.totalStats.amount

      orders['TO_SHIP'].percent = (orders['TO_SHIP'].count / orders['ALL'].count)*100
      orders['SHIPPED'].percent = (orders['SHIPPED'].count / orders['ALL'].count)*100
      orders['DELIVERED'].percent = (orders['DELIVERED'].count / orders['ALL'].count)*100
      orders['RETURNED'].percent = (orders['RETURNED'].count / orders['ALL'].count)*100
      orders['ERROR'].percent = (orders['ERROR'].count / orders['ALL'].count)*100

      this.totalStats.orders = orders
    },
    async markShippings(shippings) {
      let numShippings = shippings.length

      if (numShippings <= 0) return

      this.$vs.loading()

      for (let i = 0; i < shippings.length; i++) {
        await this.dropin.toggleMarkShipping(shippings[i])
      }

      this.$vs.loading.close()

      this.$vs.notify({
        title: 'Success',
        text: 'Shippings successfully marked',
        iconPack: 'feather',
        icon: 'icon-bookmark',
        color: 'success'
      })

      await this.applyFilters()
    },
    async updateOrderId(shipping) {
      this.$vs.loading()

      let info = shipping.info
      if (info === null) {
        info = {}
      }
      let value = null
      if (this.newValue != '') {
        value = parseInt(this.newValue)
      }
      info.order_id = value
      let params = {
        info: JSON.stringify(info),
      }
      let object = await this.dropin.updateShipping(shipping.id, params)
      let result = (object !== null)

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Shipping successfully updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the shipping',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()

      await this.applyFilters()
    },

    async copyToClipboard(toCopy, notificationMessage = 'Copied to the clipboard') {
      await window.navigator.clipboard.writeText(toCopy)
      this.$vs.notify({
        title: 'Success',
        text: notificationMessage,
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    async copyPhoneNumbers(objects) {
      if (objects.length <= 0) {
        return
      }

      let toCopy = ''
      for (let i = 0; i < objects.length; i++) {
        toCopy += objects[i].phone + '\n'
      }

      await this.copyToClipboard(toCopy)
    },
    async copyIds(objects) {
      if (objects.length <= 0) {
        return
      }

      let toCopy = ''
      for (let i = 0; i < objects.length; i++) {
        toCopy += objects[i].id + '\n'
      }

      await this.copyToClipboard(toCopy)
    },
    firstNCharacters(s, numChars) {
      if (s === null || s == '') {
        return ''
      }
      let res = s.substr(0, numChars)
      if (s.length > numChars) {
        res += '...'
      }
      return res
    },
    async fetchOrders() {
      this.orders = []

      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken'),
        }
        let params = {}
        if (this.filters.date_from != '') {
          let dateFrom = new Date(this.filters.date_from)
          dateFrom.setDate(dateFrom.getDate()-30)
          params.filter_date_from = this.formatDate(dateFrom)
        }
        if (this.filters.date_to != '') {
          params.filter_date_to = this.filters.date_to
        }
        const response = await this.$http.get('/api/v1/orders', { headers, params })
        this.orders = response.data.data
      } catch (error) {
        this.orders = []
      }

      for (let i = 0; i < this.orders.length; i++) {
        let obj = this.orders[i]
        if (obj.product !== null && obj.product != '') {
          if (!this.products.includes(obj.product)) {
            this.products.push(obj.product)
          }
        }
      }

      return this.orders
    },
    getRowClass(shipping) {
      let c = ''
      if (shipping.marked) {
        c = 'row-marked'
      }
      if (shipping.packeta_status == 'Delivered') {
        c = 'row-delivered'
      }
      if (shipping.packeta_status == 'Return') {
        c = 'row-return'
      }
      if (shipping.status == 'Error') {
        c = 'row-error'
      }
      return c
    },
    async sendTrackingUrl(shippings) {
      if (shippings.length == 0) return

      this.$vs.loading()

      for (let i = 0; i < shippings.length; i++) {
        let shipping = shippings[i]

        await this.dropin.sendTrackingUrl(shipping.id)
      }

      this.$vs.notify({
        title: 'Success',
        text: 'Tracking URL(s) sent',
        iconPack: 'feather',
        icon: 'icon-message-circle',
        color: 'success'
      })

      this.$vs.loading.close()
    },
  },
  async created() {
    this.dropin = new Dropin(localStorage.getItem('accessToken'))

    await this.resetFilters()
  },
}

</script>

<style scoped>
.column-no-padding {
  padding-top: 0px !important;
  padding-bottom: 0px !important;
  font-size: 0.8em;
  white-space: nowrap;
}
.row-marked {
  background-color: #ffd382 !important;
}
.row-delivered {
  background-color: #cef5ce !important;
}
.row-return {
  background-color: #f5cece !important;
}
.row-error {
  color: white !important;
  background-color: #c94444 !important;
}
</style>
