<template>
    <vx-card title="Leads" code-toggler>

        <p class="flex items-center mb-4">
            <vx-tooltip text="Filters" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Copy phone numbers" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-copy" @click="copyPhoneNumbers()"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Delete selected leads" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>
            </vx-tooltip>

            <vx-tooltip text="Back" position="top">
                <vs-button v-if="view == 'order_builder' || view == 'order_editor'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="changeView('leads')"></vs-button>
            </vx-tooltip>

            <vx-tooltip text="Only confirmed" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-check-circle" @click="setFiltersStatuses(['confirmed'])"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Only carts" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-shopping-cart" @click="setFiltersStatuses(['unprocessed'])"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Only waiting payment" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-clock" @click="setFiltersStatuses(['waiting_payment'])"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="No answer and to recall" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-phone" @click="setFiltersStatuses(['to_recall','no_answer'])"></vs-button>
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
                    <label>Status</label>
                    <vs-select autocomplete multiple class="w-full" v-model="filters.statuses">
                        <vs-select-item :key="'statusOption' + sIndex" :value="status" :text="status" v-for="(status, sIndex) in statuses" />
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

        <div v-if="view == 'leads'">

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

            <vue-context ref="lead_context_menu" v-slot="{ data }">
                <li>
                  <a href="#" @click="$refs.lead_context_menu.close();showInfo(data)" class="flex items-center text-sm">
                    <feather-icon icon="InfoIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Show info</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.lead_context_menu.close();changeView('order_builder', data)" class="flex items-center text-sm">
                    <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Edit</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.lead_context_menu.close();deleteLead(data)" class="flex items-center text-sm">
                    <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Delete</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.lead_context_menu.close()" class="flex items-center text-sm">
                    <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Close</span>
                  </a>
                </li>
            </vue-context>

            <!-- Leads table -->
            <vs-table stripe multiple v-model="selectedLeads" :max-items="maxItems" pagination search :data="leads" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="id">ID</vs-th>
                    <vs-th sort-key="created_at">Date</vs-th>
                    <vs-th sort-key="full_name">Name</vs-th>
                    <vs-th sort-key="phone">Phone</vs-th>
                    <vs-th sort-key="product_name">Product</vs-th>
                    <vs-th sort-key="payment_method">Method</vs-th>
                    <vs-th sort-key="price">Price</vs-th>
                    <vs-th sort-key="quantity">Qty</vs-th>
                    <vs-th sort-key="total">Total</vs-th>
                    <vs-th>Items</vs-th>
                    <vs-th sort-key="contact">Contact</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <vs-th sort-key="paid">Paid</vs-th>
                    <vs-th sort-key="notes">Notes</vs-th>
                    <vs-th>Actions</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="item" :key="'tableRow' + indextr" v-for="(item, indextr) in data">

                        <vs-td class="column-no-padding" :data="item.id">
                            {{item.id}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.created_at">
                            {{formatDateTime(new Date(item.created_at))}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.full_name">
                            <div @contextmenu.prevent="$refs.lead_context_menu.open($event, item)">
                                <vx-tooltip :title="item.full_name" :text="hasAddressInfo(item) ? getAddress(item) : 'Address undefined'" position="left">
                                    <vs-chip>
                                        {{item.full_name}}
                                    </vs-chip>
                                </vx-tooltip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding">
                            <vs-chip v-if="item.hasOwnProperty('phone') && item.phone != ''">
                                <span @click="copyToClipboard(item.phone)" @dblclick="whatsappMessage(item)">
                                    {{item.phone}}
                                </span>
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.product_name">
                            <vs-chip>
                                {{item.product_name}}
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.payment_method">
                            {{item.payment_method}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.price">
                            € {{ item.price.toFixed(2) }}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.quantity">
                            {{ item.quantity }}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.total">
                            € {{ item.total.toFixed(2) }}
                        </vs-td>

                        <vs-td class="column-no-padding">
                            <vx-tooltip v-if="item.info !== null && item.info.hasOwnProperty('products') && item.info.products !== null && item.info.products.length > 0" title="Items" :text="getProductsList(item)">
                                <vs-chip>
                                    {{item.info.products.length}} items
                                </vs-chip>
                            </vx-tooltip>
                            <vs-chip v-else>
                                0 items
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.contact">
                            <vs-checkbox v-model="data[indextr].contact" @change="updateContact(data[indextr])">{{item.call_count}}</vs-checkbox>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.status">
                            <fast-editable-value-select
                                class="font-bold"
                                :options="statuses"
                                v-model="newStatus"
                                @onEdit="newStatus = data[indextr].status"
                                @onUpdate="updateStatus(data[indextr])">
                                  <vs-chip :color="statusColor[data[indextr].status]">
                                      {{item.status}}
                                  </vs-chip>
                            </fast-editable-value-select>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.paid">
                            <span v-if="item.paid">Yes</span>
                            <span v-else>No</span>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.notes">
                            <vx-tooltip :text="item.notes" position="top">
                                <vs-avatar :color="(item.notes === null ||item.notes === '') ? 'primary' : 'warning'" size="24px" @click="showNotes(item)" icon-pack="feather" icon="icon-message-square" />
                            </vx-tooltip>
                        </vs-td>

                        <vs-td class="column-no-padding">
                            <div class="flex items-center">
                                <vs-dropdown class="mr-2 dropdown font-bold">
                                    <!--
                                    <vs-button class="btn-drop" type="filled" icon="expand_more">Actions</vs-button>
                                    -->
                                    <vs-chip color="primary">
                                        Actions
                                        <vs-icon icon-pack="feather" icon="icon-chevron-down"></vs-icon>
                                    </vs-chip>

                                    <vs-dropdown-menu class="dropdown-menu">
                                        <vs-dropdown-item @click="changeView('order_builder', item)">
                                            <vs-icon icon-pack="feather" icon="icon-edit"></vs-icon>
                                            Edit
                                        </vs-dropdown-item>
                                        <vs-dropdown-item @click="showNotes(item)">
                                            <vs-icon icon-pack="feather" icon="icon-message-square"></vs-icon>
                                            Show Notes
                                        </vs-dropdown-item>
                                        <vs-dropdown-item @click="fastCreateDelivery(item)">
                                            <vs-icon icon-pack="feather" icon="icon-package"></vs-icon>
                                            Create Delivery
                                        </vs-dropdown-item>
                                        <vs-dropdown-item @click="showInfo(item)">
                                            <vs-icon icon-pack="feather" icon="icon-info"></vs-icon>
                                            Show Info
                                        </vs-dropdown-item>
                                        <vs-dropdown-item @click="showPaymentLink(item)">
                                            <vs-icon icon-pack="feather" icon="icon-dollar-sign"></vs-icon>
                                            Show Payment Link
                                        </vs-dropdown-item>
                                        <vs-dropdown-item @click="whatsappMessage(item)">
                                            <vs-icon icon-pack="feather" icon="icon-message-circle"></vs-icon>
                                            Send Whatsapp Message
                                        </vs-dropdown-item>
                                    </vs-dropdown-menu>
                                </vs-dropdown>

                            </div>
                        </vs-td>
                        
                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{data.length}}</vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">€ {{totalStats.price.toFixed(2)}}</vs-td>
                    <vs-td class="column-no-padding">{{totalStats.quantity}}</vs-td>
                    <vs-td class="column-no-padding">€ {{totalStats.total.toFixed(2)}}</vs-td>
                    <vs-td class="column-no-padding">{{totalStats.items}} items</vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>

                </template>
            </vs-table>

        </div>

        <!-- Order builder -->
        <div v-if="view == 'order_builder'">
            <OrderBuilderComponent :customers="[]" :products="variations" :carriers="carriers" :prefilled="lead" @onOrderCreated="onOrderCreated" />
        </div>

        <!-- Order editor -->
        <div v-if="view == 'order_editor'">
            <OrderEditorComponent :order="order" :products="variations" />
        </div>

        <!-- Notes popup -->
        <vs-popup title="Notes" :active.sync="showPopup['notes']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3">
                    <div class="vx-col w-full">
                        <h3>Notes</h3>
                        <vs-textarea v-model="lead.notes"></vs-textarea>
                        <div class="flex" style="float: right;">
                            <vs-button color="success" type="filled" icon-pack="feather" icon="icon-check" @click="updateNotes(lead)"></vs-button>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>

        <!-- Lead info popup -->
        <vs-popup title="Lead info" :active.sync="showPopup['info']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3" v-for="(value, key, index) in lead.info" :key="'leadInfo' + index">
                    <div class="vx-col md:w-1/2 w-full">
                        <b>{{key}}</b>
                    </div>
                    <div class="vx-col md:w-1/2 w-full">
                        {{value}}
                    </div>
                </div>
            </div>
        </vs-popup>

        <!-- Payment link popup -->
        <vs-popup title="Payment link" :active.sync="showPopup['payment_link']">
            <div class="vx-row mb-3">
                <div class="vx-col w-full">
                    <vs-input :disabled="true" class="w-full" icon-pack="feather" icon="icon-link" icon-no-border :value="paymentLink" />
                </div>
                <div class="vx-col w-full">
                    <vs-button class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-copy" v-clipboard:copy="paymentLink" v-clipboard:success="onCodeCopied">Copy to clipboard</vs-button>
                </div>
            </div>
        </vs-popup>

        <!-- Recall popup -->
        <vs-popup title="Recall info" :active.sync="showPopup['recall']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3">
                    <div class="vx-col w-full">
                        <label>Recall date</label>
                        <vs-input type="datetime-local" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="recall.date" />
                    </div>
                </div>
                <div class="vx-col w-full">
                    <vs-button class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-save" @click="saveRecall(recall)">Save</vs-button>
                </div>
            </div>
        </vs-popup>

        <!-- Send message popup -->
        <vs-popup title="Send message" :active.sync="showPopup['send_message']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3">
                    <div class="vx-col w-full">
                        <h3>Message</h3>
                        <vs-textarea v-model="message"></vs-textarea>
                        <div class="flex" style="float: right;">
                            <vs-button color="success" type="filled" icon-pack="feather" icon="icon-check" @click="sendBulkMessage()"></vs-button>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'

import { WarehouseLead, WarehouseProduct, WarehouseShippingCarrier, Customer } from 'zarolead'

function getCurrencyValue(s) {
  if (typeof(s) === 'number') {
    return s;
  } else if (typeof(s) === 'string') {
    return parseFloat(s.replace(',', '.'))
  } else {
    return 0;
  }
}

function stringContainsNumber(_input) {
  let string1 = String(_input);
  for (let i = 0; i < string1.length; i++) {
    if (!isNaN(string1.charAt(i)) && !(string1.charAt(i) === " ")) {
      return true;
    }
  }
  return false;
}

export default {
  components: {
    VueContext,
  },
  data () {
    return {
      warehouseLeadClient: null,
      warehouseProductClient: null,
      warehouseShippingCarrierClient: null,
      customerClient: null,

      maxItems: 150,
      view: 'leads',

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        statuses: ['confirmed'],
      },

      showPopup: {
        'notes': false,
        'info': false,
        'payment_link': false,
        'recall': false,
        'send_message': false,
      },

      leads: [],
      selectedLeads: [],
      products: [],
      variations: [],
      carriers: [],

      lead: {},

      statuses: [
        'unprocessed',
        'to_recall',
        'no_asnwer',
        'fake',
        'cancelled',
        'duplicate',
        'waiting_payment',
        'confirmed',
      ],
      statusColor: {
        'unprocessed'    : '',
        'to_recall'      : 'primary',
        'no_answer'      : '#555555',
        'fake'           : '#cc8877',
        'cancelled'      : 'danger',
        'duplicate'      : 'warning',
        'waiting_payment': 'primary',
        'confirmed'      : 'success',
      },

      totalStats: {
        price: 0.0,
        quantity: 0,
        total: 0.0,
        items: 0,
      },

      newStatus: '',
      newValue: '',

      paymentLink: '',

      recall: {
        date: '',
      },

      receivers: [],
      message: '',

      order: {},
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
      return this.leads
    },
  },
  methods: {
    // Frontend functions
    onPageChanged(val) {
      this.currentPage = val
    },
    async changeView(view, item = null) {
      if (view == 'leads') {
        this.$vs.loading()
        this.leads = await this.warehouseLeadClient.fetch(this.filters)

        for (let i = 0; i < this.leads.length; i++) {
          let obj = this.leads[i]

          obj.full_name = obj.first_name + ' ' + obj.last_name

          obj.product_name = ''
          if (obj.product !== null) {
            obj.product_name = obj.product.name
          }

          let productSku = null
          if (obj.product.sku !== null && obj.product.sku != '') {
            productSku = obj.product.sku
          } else if (obj.product.variations !== null && obj.product.variations.length > 0) {
            productSku = obj.product.variations[0].sku
          }
          if (productSku === null) {
            // TODO: Log error
          }
          obj.product = this.getVariationBySku(productSku)

          obj.price = 0.0
          obj.total = 0.0
          obj.quantity = 0
          obj.payment_method = ''
          obj.contact = false

          let info = obj.info
          if (info === null) {
            info = {}
          }

          if (info.hasOwnProperty('price')) {
            obj.price = parseFloat(info.price)
          }
          if (info.hasOwnProperty('total')) {
            obj.total = parseFloat(info.total)
          }
          if (info.hasOwnProperty('quantity')) {
            obj.quantity = parseInt(info.quantity)
          }
          if (info.hasOwnProperty('payment_method')) {
            obj.payment_method = info.payment_method
          }
          if (info.hasOwnProperty('contact')) {
            obj.contact = info.contact
          }

          this.leads[i] = obj
        }

        this.calculateStats()
        this.view = view
        this.$vs.loading.close()
      } else if (view == 'order_builder') {
        if (item.customer !== null) {
          await this.changeView('order_editor', item)
        } else {
          this.lead = item
          this.view = view
        }
      } else if (view == 'order_editor') {
        this.$vs.loading()

        this.lead = item

        const customer = await this.customerClient.show(this.lead.customer.id)
        this.order = customer.orders[0]
        // TODO: Check if customer.orders.length > 0

        this.$vs.loading.close()

        this.view = view
      }
    },
    getObjectById(id, objects = null) {
      if (objects === null) {
        objects = this.objects
      }
      for (let i = 0; i < objects.length; i++) {
        if (objects[i].id === id) {
          return objects[i]
        }
      }
      return null
    },
    getVariationBySku(sku) {
      for (let i = 0; i < this.variations.length; i++) {
        if (this.variations[i].sku === sku) {
          return this.variations[i]
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
        statuses: ['confirmed'],
      }
      await this.filterDatesShortcut('this_month')
    },
    async setFiltersStatuses(statuses) {
      this.filters.statuses = []
      for (let i = 0; i < statuses.length; i++) {
        this.filters.statuses.push(statuses[i])
      }
      await this.applyFilters()
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
      // TODO: NOTE: -1 because of the timezone?
      // TODO: Now it should be right without the -1
      let hh = String(dateObj.getHours() - 1).padStart(2, '0')
      let ii = String(dateObj.getMinutes()).padStart(2, '0')
      
      let formattedDate = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + ii

      return formattedDate
    },
    formatDateTimeISO8601(dateObj) {
      return dateObj.toISOString()
    },
    calculateStats() {
      let price = 0.0
      let quantity = 0
      let total = 0.0
      let items = 0

      for (let i = 0; i < this.leads.length; i++) {
        let lead = this.leads[i]
        price += lead.price
        quantity += lead.quantity
        total += lead.total
        if (lead.info !== null && lead.info.hasOwnProperty('products') && lead.info.products !== null) {
          items += lead.info.products.length
        }
      }

      // Average price
      price = price / this.leads.length

      this.totalStats.price = price
      this.totalStats.quantity = quantity
      this.totalStats.total = total
      this.totalStats.items = items
    },
    openNewTab(url) {
      window.open(url, '_blank').focus()
    },
    whatsappMessage(lead) {
      if (lead.hasOwnProperty('phone')) {
        let phone = lead.phone
        if (phone[0] != '+') {
          phone = '+39' + phone
        }
        let url = 'https://api.whatsapp.com/send?phone=' + phone
        this.openNewTab(url)
      }
    },

    async updateStatus(lead) {
      this.$vs.loading()

      let params = {
        status: this.newStatus,
      }
      await this.warehouseLeadClient.update(lead.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead\'s status updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      if (this.newStatus === 'to recall') {
        this.recall.date = ''
        this.lead = lead
        this.showPopup['recall'] = true
      }

      this.$vs.loading.close()

      this.applyFilters()
    },
    // TODO: HERE
    // TODO: Is this necessary?
    showNotes(lead) {
      this.lead = lead
      this.showPopup['notes'] = true
    },
    async updateNotes(lead) {
      this.$vs.loading()

      let params = {
        notes: lead.notes,
      }
      await this.warehouseLeadClient.update(lead.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead\'s notes updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.showPopup['notes'] = false
      this.$vs.loading.close()

      this.applyFilters()
    },
    async updateContact(lead) {
      this.$vs.loading()

      let info = lead.info
      if (info === null) {
        info = {}
      }
      info.contact = lead.contact
      if (!info.hasOwnProperty('call_count')) {
        info.call_count = 0
      }
      if (lead.contact === true) {
        info.call_count += 1
      }
      let params = {
        info: JSON.stringify(info),
      }
      await this.warehouseLeadClient.update(lead.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead\'s contact updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.$vs.loading.close()

      this.applyFilters()
    },

    findAddress() {
      let street_address = ''
      let city = ''
      let postcode = ''
      let province = ''
      let state = ''
      let country = ''

      let loc = this.order.location
      if (loc.hasOwnProperty('street') && loc.hasOwnProperty('street_number')) {
        street_address = loc.street + ' ' + loc.street_number
      }
      if (loc.hasOwnProperty('city')) {
        city = loc.city
      }
      if (loc.hasOwnProperty('postcode')) {
        postcode = loc.postcode
      }
      if (loc.hasOwnProperty('province')) {
        province = loc.province
      }
      if (loc.hasOwnProperty('state')) {
        state = loc.state
      }
      if (loc.hasOwnProperty('country')) {
        country = loc.country
      }

      let address = street_address + ', ' + city + ' (' + province + ') ' + postcode + ', ' + state + ', ' + country

      this.order.shipping.street_address = street_address
      this.order.shipping.city = city
      this.order.shipping.postcode = postcode
      this.order.shipping.province = province
      this.order.shipping.state = state
      this.order.shipping.country = country
      this.order.shipping.address = address
    },

    mapProduct(p) {
      let obj = {
        id: p.id,
        name: p.name,  
        sku: p.sku,
        quantity: 0,
        price: 0.0,
      }

      if (obj.sku === null || obj.sku == '') {
        if (p.hasOwnProperty('variations') && p.variations !== null && p.variations.length > 0) {
          obj.sku = p.variations[0].sku
          obj.quantity = parseInt(p.variations[0].quantity)
          obj.price = parseFloat(p.variations[0].price)
        }
      }

      return obj
    },
    async fetchProducts() {
      let products = await this.warehouseProductClient.fetch()

      this.products = []
      for (let i = 0; i < products.length; i++) {
        let p = products[i]
        this.products.push(this.mapProduct(p))
      }

      this.variations = []
      for (let i = 0; i < products.length; i++) {
        let product = products[i]
        for (let j = 0; j < product.variations.length; j++) {
          let variation = product.variations[j]
          this.variations.push({
            id: variation.id,
            name: product.name + ' ' + variation.name,
            sku: variation.sku,
            price: parseFloat(variation.price),
            quantity: parseInt(variation.quantity),
          })
        }
      }
    },
    async fetchCarriers() {
      this.carriers = await this.warehouseShippingCarrierClient.fetch()
    },
    async updatePrice(lead) {
      this.$vs.loading()

      let info = lead.info
      info.price = this.newValue
      let params = {
        info: JSON.stringify(info),
      }
      await this.warehouseLeadClient.update(lead.id, params)

      this.$vs.loading.close()

      this.changeView('leads')
    },
    async updateQuantity(lead) {
      this.$vs.loading()

      let info = lead.info
      info.quantity = this.newValue
      let params = {
        info: JSON.stringify(info),
      }
      await this.warehouseLeadClient.update(lead.id, params)

      this.$vs.loading.close()

      this.changeView('leads')
    },
    async updateTotal(lead) {
      this.$vs.loading()

      let info = lead.info
      info.total = this.newValue
      let params = {
        info: JSON.stringify(info),
      }
      await this.warehouseLeadClient.update(lead.id, params)

      this.$vs.loading.close()

      this.changeView('leads')
    },
    async deleteLead(lead) {
      this.$vs.loading()
      await this.warehouseLeadClient.delete(lead.id)
      this.$vs.loading.close()
      this.changeView('leads')
    },
    async deleteLeads(leads) {
      for (let i = 0; i < leads.length; i++) {
        await this.warehouseLeadClient.delete(leads[i].id)
      }
    },
    async deleteSelected() {
      this.$vs.loading()
      await this.deleteLeads(this.selectedLeads)
      this.selectedLeads = []
      this.$vs.loading.close()
      this.changeView('leads')
    },
    confirmDeletePopup() {
      if (this.selectedLeads.length == 0) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Archive confirmation',
        text: 'Are you sure you want to archive ' + this.selectedLeads.length + ' object(s)?',
        accept: this.deleteSelected
      })
    },
    async showInfo(lead) {
      this.lead = lead
      this.showPopup['info'] = true
    },
    getPaymentLink(leadHash) {
      let url = 'https://' + window.location.hostname + '/leads/' + leadHash + '/checkout/new'
      return url
    },
    showPaymentLink(lead) {
      this.lead = lead
      let url = this.getPaymentLink(this.lead.hash)
      this.paymentLink = url
      this.showPopup['payment_link'] = true
    },
    onCodeCopied() {
      this.$vs.notify({
        title: 'Success',
        text: 'Copied to the clipboard',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    async copyToClipboard(toCopy, notificationMessage = 'Copied to the clipboard') {
      await await window.navigator.clipboard.writeText(toCopy)
      this.$vs.notify({
        title: 'Success',
        text: notificationMessage,
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    async copyPhoneNumbers() {
      if (this.selectedLeads.length <= 0) {
        return
      }

      let toCopy = ''
      for (let i = 0; i < this.selectedLeads.length; i++) {
        toCopy += this.selectedLeads[i].phone + '\n'
      }

      await this.copyToClipboard(toCopy)
    },
    async sendMessage(phone, message) {
      // TODO: HERE
      const result = await this.warehouseLeadClient.sendMessage(phone, message)
      return result
    },
    confirmSendPaymentLinkViaMessage() {
      this.$vs.dialog({
        type: 'confirm',
        color: 'primary',
        title: 'Send Payment link confirmation',
        text: 'Are you sure you want to send the payment link via message (Whatsapp)?',
        accept: this.sendPaymentLinkViaMessage
      })
    },
    async sendPaymentLinkViaMessage() {
      this.$vs.loading()

      let paymentLink = this.getPaymentLink(this.order.lead_hash)

      let phone = this.order.phone
      let message = 'Link di pagamento: ' + paymentLink

      const result = await this.sendMessage(phone, message)

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Message sent',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Error while sending the message',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    hasAddressInfo(lead) {
      if (lead.address_line1 !== null && lead.address_line1 != '' &&
          lead.city !== null && lead.city != '') {
        return true
      }
      return false
    },
    getAddress(lead) {
      return (lead.address_line1 + ', ' + lead.city + '(' + lead.zone + ')')
    },
    getProductsList(lead) {
      let productName = lead.product_name
      let s = ''
      if (lead.hasOwnProperty('info') && lead.info !== null &&
          lead.info.hasOwnProperty('products') && lead.info.products !== null) {
        for (let i = 0; i < lead.info.products.length; i++) {
          let p = lead.info.products[i]
          if (i > 0) {
            s += ' - '
          }
          let pName = productName
          if (p.hasOwnProperty('name') && p.name !== null) {
            pName = p.name
          }
          s += pName + '(x' + p.quantity + ')'
        }
      }
      return s
    },
    async saveRecall(recall) {
      let recallDate = this.formatDateTime(new Date(recall.date))
      if (this.lead.notes === null) {
        this.lead.notes = ''
      }
      if (this.lead.notes !== '') {
        this.lead.notes += "\n"
      }
      this.lead.notes += 'Recall date: ' + recallDate
      await this.updateNotes(this.lead)

      this.$vs.notify({
        title: 'Success',
        text: 'Recall info saved',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.showPopup['recall'] = false
    },
    openSendMessagePopup(receivers) {
      this.receivers = receivers
      this.message = ''
      this.showPopup['send_message'] = true
    },
    async sendBulkMessage() {
      this.$vs.loading()
      for (let i = 0; i < this.receivers.length; i++) {
        let r = this.receivers[i]
        if (!r.hasOwnProperty('phone') || r.phone === null || r.phone === '') {
          continue
        }

        await this.sendMessage(r.phone, this.message)
      }
      this.$vs.notify({
        title: 'Success',
        text: 'Messages sent successfully',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.showPopup['send_message'] = false
      this.$vs.loading.close()
    },
    async onOrderCreated(order) {
      this.$vs.loading()

      await this.warehouseLeadClient.update(this.lead.id, {
        status: 'confirmed',
        customer_id: order.customer.id,
        first_name: order.customer.first_name,
        last_name: order.customer.last_name,
        phone: order.customer.phone,
        email: order.customer.email,
        address_line1: order.customer.address_line1,
        address_line2: order.customer.address_line2,
        city: order.customer.city,
        postcode: order.customer.postcode,
        zone: order.customer.zone,
        country: order.customer.country,
      })

      this.$vs.loading.close()

      this.$vs.notify({
        title: 'Success',
        text: 'Lead confirmed',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      await this.changeView('leads')
    },
  },
  async created() {
    this.warehouseLeadClient = new WarehouseLead(this.$zarolead)
    this.warehouseProductClient = new WarehouseProduct(this.$zarolead)
    this.warehouseShippingCarrierClient = new WarehouseShippingCarrier(this.$zarolead)
    this.customerClient = new Customer(this.$zarolead)

    this.$vs.loading()

    await this.fetchProducts()
    await this.fetchCarriers()
    await this.resetFilters()

    this.$vs.loading.close()
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

.carrier-icon {
  width: 24px !important;
}

.dropdown {
  font-family: "Montserrat", Helvetica, Arial, sans-serif;
}

.dropdown-menu {
  min-width: 200px !important;
}

</style>
