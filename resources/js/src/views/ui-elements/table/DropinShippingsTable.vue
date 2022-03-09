<template>
    <vx-card title="Dropin Shippings" code-toggler>

        <p class="flex items-center mb-4">
            <vx-tooltip text="Show filters" position="top">
                <vs-button v-if="view == 'shippings'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Refresh selected shippings" position="top">
                <vs-button v-if="view == 'shippings'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" :disabled="selectedShippings.length <= 0" @click="refreshShippings(selectedShippings)"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Import shippings" position="top">
                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-download" @click="refreshShippingsTable"></vs-button>
            </vx-tooltip>
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
        <vs-table v-if="view == 'shippings'" stripe multiple v-model="selectedShippings" :max-items="maxItems" pagination search :data="shippings" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">ID</vs-th>
                <vs-th sort-key="order_id">Order ID</vs-th>
                <vs-th sort-key="created_at">Creation Date</vs-th>
                <vs-th sort-key="name">Customer</vs-th>
                <vs-th sort-key="phone">Phone</vs-th>
                <vs-th sort-key="payment_method">Payment</vs-th>
                <vs-th sort-key="status">Dropin Status</vs-th>
                <vs-th sort-key="tracking_url">Tracking</vs-th>
                <vs-th sort-key="packeta_status">Packeta Status</vs-th>
                <vs-th sort-key="carrier_status_name">Carrier Status</vs-th>
                <vs-th sort-key="delivered_at">Delivery Date</vs-th>
                <vs-th sort-key="amount">Amount</vs-th>
                <vs-th sort-key="delivery_price">Shipping Price</vs-th>
                <vs-th sort-key="tax">VAT</vs-th>
                <vs-th sort-key="cod_amount">COD amount</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                    <vs-td class="column-no-padding" :data="item.id">
                        <div @contextmenu.prevent="$refs.shipping_context_menu.open($event, item)">
                            <vs-chip>
                                #{{item.id}}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.order_id">
                        {{ item.order_id }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.created_at">
                        {{ formatDate(new Date(item.created_at)) }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.name">
                        {{ item.name }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.phone">
                        {{ item.phone }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.payment_method">
                        {{ item.payment_method }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.staus">
                        {{ item.status }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.tracking_url">
                        <a :href="item.tracking_url">
                            {{ getTrackingIdFromUrl(item.tracking_url) }}
                        </a>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.packeta_status">
                        {{ item.packeta_status }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.carrier_status_name">
                        {{ item.carrier_status_name }}
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

                    <vs-td class="column-no-padding" :data="item.tax">
                        € {{ item.tax.toFixed(2) }}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.cod_amount">
                        € {{ item.cod_amount.toFixed(2) }}
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
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding">€ {{totalStats.amount.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{totalStats.delivery_price.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{totalStats.tax.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{totalStats.cod_amount.toFixed(2)}}</vs-td>

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

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        dropin_statuses: [],
        packeta_statuses: [],
        carrier_statuses: [],
        amount_min: 0,
        amount_max: 100,
      },

      shippings: [],
      selectedShippings: [],

      showPopup: {
      },

      totalStats: {
        amount: 0.0,
        delivery_price: 0.0,
        tax: 0.0,
        cod_amount: 0.0,
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
        }

        this.calculateTotalStats()
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
    calculateTotalStats() {
      this.totalStats = {
        amount: 0.0,
        delivery_price: 0.0,
        tax: 0.0,
        cod_amount: 0.0,
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
        if (obj.hasOwnProperty('cod_amount')) {
          this.totalStats.cod_amount += parseFloat(obj.cod_amount)
        }
      }
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
  white-space: nowrap;
}
</style>
