<template>
    <vx-card title="Orders" code-toggler>

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

        <popover class="customer_popover" :name="'customer_popover_' + order.id" event="hover" v-for="(order, index) in orders" :key="index">
            <div><b>{{ order.customer_name }}</b></div>
            <div>{{ order.billing.address_1 }}, {{ order.billing.postcode }} {{ order.billing.city }} ({{ order.billing.state }})</div>
            <div>{{ order.billing.phone }}</div>
        </popover>

        <!-- Orders table -->
        <vs-table v-if="view == 'orders'" stripe multiple v-model="selectedOrders" :max-items="maxItems" pagination search :data="orders" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Id</vs-th>
                <vs-th sort-key="date">Date</vs-th>
                <vs-th sort-key="customer_name">Customer</vs-th>
                <vs-th sort-key="num_items">Items</vs-th>
                <vs-th sort-key="total">Amount</vs-th>
                <vs-th sort-key="payment_method">Payment Method</vs-th>
                <vs-th sort-key="status">Status</vs-th>
                <vs-th sort-key="">Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                    <vs-td class="column-no-padding" :data="item.id">
                        #{{item.id}}
                    </vs-td>
                    
                    <vs-td class="column-no-padding" :data="item.date">
                        {{formatDate(item.date)}} ({{dateTimeAgo(item.date)}})
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.customer_name">
                        <vs-chip v-popover="{name:'customer_popover_' + item.id}">
                            {{item.customer_name}}
                        </vs-chip>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.num_items">
                        <vx-tooltip :text="item.products" position="bottom">
                            <span>{{item.num_items}} {{ item.num_items == 1 ? 'item' : 'items' }}</span>
                        </vx-tooltip>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.total">
                        {{item.currency_symbol}} {{item.total}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.payment_method">
                        {{item.payment_method}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.status">
                        <fast-editable-value-select
                            :options="orderStatuses"
                            v-model="newStatus"
                            @onEdit="newStatus = data[indextr].status"
                            @onUpdate="updateStatus(data[indextr])">
                              <vs-chip :color="orderStatusColor[item.status]">
                                  {{item.status}}
                              </vs-chip>
                        </fast-editable-value-select>
                    </vs-td>

                    <vs-td class="column-no-padding">
                        <div class="flex items-center">
                            <vx-tooltip text="Info" position="top">
                                <vs-button class="mr-2" color="primary" @click="showInfo(item)" icon-pack="feather" icon="icon-help-circle" />
                            </vx-tooltip>
                            <vx-tooltip :text="item.notes" position="top">
                                <vs-button class="mr-2" color="primary" @click="showNotes(item)" icon-pack="feather" icon="icon-message-circle" />
                            </vx-tooltip>
                            <vx-tooltip text="** Genera spedizione **" position="top">
                                <vs-button class="mr-2" color="primary" icon-pack="feather" icon="icon-package" />
                            </vx-tooltip>
                            <vx-tooltip text="Edit product" position="top">
                                <vs-button class="mr-2" color="primary" icon-pack="feather" icon="icon-edit" @click="goToEditOrderPage(item)" />
                            </vx-tooltip>
                        </div>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Order info popup -->
        <vs-popup title="Order info" :active.sync="showPopup['info']">
            <div class="vx-row mb-6" :key="index" v-for="(value, name, index) in order">
                <div class="vx-col sm:w-1/2 w-full">
                    <span><b>{{name}}</b></span>
                </div>
                <div class="vx-col sm:w-1/2 w-full">
                    <span>{{value}}</span>
                </div>
            </div>
        </vs-popup>

        <!-- Notes popup -->
        <vs-popup title="Notes" :active.sync="showPopup['notes']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3">
                    <div class="vx-col w-full">
                        <h3>Notes</h3>
                        <vs-textarea v-model="order.notes"></vs-textarea>
                        <div class="flex" style="float: right;">
                            <vs-button color="success" type="filled" icon-pack="feather" icon="icon-check" @click="updateNotes(order)"></vs-button>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'

import Ecommerce from 'ecommerce'

import TimeAgo from 'javascript-time-ago'
import en from 'javascript-time-ago/locale/en.json'
TimeAgo.addDefaultLocale(en)

export default {
  components: {
    StatisticsCardLine,
  },
  data () {
    return {
      ecommerce: null,
      timeAgo: null,

      maxItems: 20,
      view: 'orders',

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
      },

      showPopup: {
        'info': false,
        'notes': false,
      },

      orders: [],
      selectedOrders: [],

      order: {},

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

      newStatus: 'pending',
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
      return this.orders
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
        this.orders = await this.ecommerce.fetchOrders(this.filters)
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
    dateTimeAgo(dateObj) {
      return this.timeAgo.format(dateObj)
    },
    showInfo(order) {
      this.order = order
      this.showPopup['info'] = true
    },
    showNotes(order) {
      this.order = order
      this.showPopup['notes'] = true
    },
    async updateNotes(order) {
      this.$vs.loading()

      order.meta_data.push({
        id: 999,
        key: '_zl_notes',
        value: order.notes,
      })
      let params = {
        meta_data: order.meta_data,
      }
      await this.ecommerce.updateOrder(order.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Order\'s notes updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.showPopup['notes'] = false
      this.$vs.loading.close()

      this.applyFilters()
    },
    async updateStatus(order) {
      this.$vs.loading()

      let params = {
        status: this.newStatus,
      }
      await this.ecommerce.updateOrder(order.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Order\'s status updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.$vs.loading.close()

      this.applyFilters()
    },
    goToEditOrderPage(order) {
      let url = 'https://homygadget.com/wp-admin/post.php?post=' + order.id + '&action=edit'
      window.open(url, '_blank').focus()
    },
  },
  async created() {
    this.ecommerce = new Ecommerce(localStorage.getItem('accessToken'))
    this.timeAgo = new TimeAgo('en-US')

    this.$vs.loading()

    await this.resetFilters()

    this.$vs.loading.close()
  },
}

</script>

<style>

.customer_popover {
  z-index: 999999 !important;
  position: fixed !important;
}

</style>
