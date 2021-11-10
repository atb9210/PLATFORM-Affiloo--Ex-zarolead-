<template>
    <vx-card title="Leads" code-toggler>

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
                  <a href="#" @click="$refs.lead_context_menu.close()" class="flex items-center text-sm">
                    <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Close</span>
                  </a>
                </li>
            </vue-context>

            <!-- Customers table -->
            <vs-table v-if="view == 'leads'" stripe multiple v-model="selectedLeads" :max-items="maxItems" pagination search :data="leads" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="id">ID</vs-th>
                    <vs-th sort-key="full_name">Name</vs-th>
                    <vs-th sort-key="phone">Phone</vs-th>
                    <vs-th sort-key="sku">SKU</vs-th>
                    <vs-th sort-key="price">Price</vs-th>
                    <vs-th sort-key="quantity">Quantity</vs-th>
                    <vs-th sort-key="total">Total</vs-th>
                    <vs-th sort-key="contact">Contact</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <vs-th sort-key="notes">Notes</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                        <vs-td class="column-no-padding" :data="item.id">
                            {{item.id}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.name">
                            <div @contextmenu.prevent="$refs.lead_context_menu.open($event, item)">
                                <vs-chip>
                                    {{item.full_name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding">
                            <vs-chip v-if="item.hasOwnProperty('phone') && item.phone != ''">
                                <span @dblclick="whatsappMessage(item)">
                                    {{item.phone}}
                                </span>
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.sku">
                            {{item.sku}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.price">
                            € {{item.price.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.quantity">
                            {{item.quantity}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.total">
                            € {{item.total.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.contact">
                            <vs-checkbox v-model="data[indextr].contact" @change="updateContact(data[indextr])" />
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.status">
                            <fast-editable-value-select
                                :options="leadStatuses"
                                v-model="newStatus"
                                @onEdit="newStatus = data[indextr].status"
                                @onUpdate="updateStatus(data[indextr])">
                                  <vs-chip :color="leadStatusColor[item.status]">
                                      {{item.status}}
                                  </vs-chip>
                            </fast-editable-value-select>
                        </vs-td>

                        <vs-td class="column-no-padding">
                            <div class="flex items-center">
                                <vx-tooltip :text="item.notes" position="top">
                                    <vs-button class="mr-2" :color="item.notes != '' ? 'primary' : 'warning'" @click="showNotes(item)" icon-pack="feather" icon="icon-flag" />
                                </vx-tooltip>
                                <vx-tooltip text="Create order" position="top">
                                    <vs-button class="mr-2" color="primary" icon-pack="feather" icon="icon-package" @click="changeView('order_builder', item)" />
                                </vx-tooltip>
                                <vx-tooltip text="Send Whatsapp message" position="top">
                                    <vs-button class="mr-2" color="primary" @click="whatsappMessage(item)" icon-pack="feather" icon="icon-message-circle" />
                                </vx-tooltip>
                            </div>
                        </vs-td>
                        
                    </vs-tr>
                </template>
            </vs-table>

        </div>

        <!-- Order builder -->
        <div v-if="view == 'order_builder'">
            <div class="w-full pr-2 pl-2">
                <h2>Contact data</h2>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.first_name" placeholder="First Name" />
                </div>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.last_name" placeholder="Last Name" />
                </div>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.phone" placeholder="Phone" />
                </div>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.email" placeholder="Email" />
                </div>

                <hr class="mt-3 mb-3">

                <h2>Shipping</h2>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.shipping.first_name" placeholder="First Name" />
                </div>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.shipping.last_name" placeholder="Last Name" />
                </div>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.shipping.phone" placeholder="Phone" />
                </div>
                <div class="vx-row mb-6">
                    <vs-input class="w-full" type="text" v-model="order.shipping.email" placeholder="Email" />
                </div>
                <div class="vx-row mb-3">
                    <LocationInput v-model="order.location" @onEdit="findAddress" />
                </div>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.shipping.street_address" placeholder="Street address" />
                </div>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.shipping.city" placeholder="City" />
                </div>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.shipping.postcode" placeholder="Post code" />
                </div>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.shipping.province" placeholder="Province" />
                </div>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.shipping.state" placeholder="State" />
                </div>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="text" v-model="order.shipping.country" placeholder="Country" />
                </div>

                <hr class="mt-3 mb-3">

                <h2>Products</h2>
                <div class="vx-row mb-3 w-full">
                    <div class="vx-row mb-3 w-full" v-for="(product, pIndex) in order.products">
                        <div class="vx-col w-5/6">
                          <vs-select class="w-full" v-model="order.products[pIndex]">
                              <vs-select-item :key="poIndex" :value="productOption.id" :text="productOption.name" v-for="(productOption, poIndex) in products" />
                          </vs-select>
                        </div>
                        <div class="vx-col w-1/6">
                            <vs-button color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="removeProduct(pIndex)"></vs-button>
                        </div>
                    </div>
                    <div>
                        <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="addProduct"></vs-button>
                    </div>
                </div>

                <hr class="mt-3 mb-3">

                <h2>Insurance</h2>
                <div class="vx-row mb-3">
                    <vs-input class="w-full" type="number" step="0.01" min="0.0" v-model="order.insurance" @input="refreshOrderReviewTotal" placeholder="Insurance (Ex. 4.99)" />
                </div>

                <hr class="mt-3 mb-3">

                <h2>Review</h2>
                <div class="vx-row w-full mb-3 font-bold">
                    <div class="vx-col w-full md:w-1/4">
                        Name
                    </div>
                    <div class="vx-col w-full md:w-1/4">
                        Quantity
                    </div>
                    <div class="vx-col w-full md:w-1/4">
                        Unit Price
                    </div>
                    <div class="vx-col w-full md:w-1/4">
                        Total Price
                    </div>
                </div>

                <div class="vx-row w-full mb-3" v-for="(pRev, prIndex) in orderReviewProducts" :key="prIndex">
                    <div class="vx-col w-full md:w-1/4">
                        {{pRev.name}}
                    </div>
                    <div class="vx-col w-full md:w-1/4">
                        x{{pRev.quantity}}
                    </div>
                    <div class="vx-col w-full md:w-1/4">
                        € {{pRev.unit_price}}
                    </div>
                    <div class="vx-col w-full md:w-1/4">
                        € {{(pRev.unit_price*pRev.quantity).toFixed(2)}}
                    </div>
                </div>

                <div class="vx-row w-full mb-3" v-if="parseFloat(order.insurance) > 0.0">
                    <div class="vx-col w-full md:w-1/4">
                        Insurance
                    </div>
                    <div class="vx-col w-full md:w-1/4">
                        x1
                    </div>
                    <div class="vx-col w-full md:w-1/4">
                        € {{order.insurance}}
                    </div>
                    <div class="vx-col w-full md:w-1/4">
                        € {{order.insurance}}
                    </div>
                </div>

                <div class="vx-row w-full mt-3 mb-3 font-bold">
                    <div class="vx-col w-full md:w-1/4">
                    </div>
                    <div class="vx-col w-full md:w-1/4">
                    </div>
                    <div class="vx-col w-full md:w-1/4">
                    </div>
                    <div class="vx-col w-full md:w-1/4">
                        ----------------------<br>
                        <fast-editable-value
                            v-model="order.newTotal"
                            @onEdit="order.newTotal = order.total"
                            @onUpdate="updateOrderTotal()">
                              € {{order.total}}
                        </fast-editable-value>
                    </div>
                </div>

                <div class="vx-row w-full mt-3 mb-3 font-bold">
                    <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="createOrder">Create Order</vs-button>
                </div>

            </div>
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
      view: 'leads',

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
      },

      showPopup: {
        'notes': false,
      },

      leads: [],
      selectedLeads: [],
      lead: {},
      order: {
        first_name: '',
        last_name: '',
        phone: '',
        email: '',
        shipping: {
          first_name: '',
          last_name: '',
          phone: '',
          email: '',
          notes: '',
          street_address: '',
          city: '',
          province: '',
          state: '',
          country: '',
          postcode: '',
          address: '',
        },
        location: {},
        products: [],
        insurance: '0.0',
        review: {
          products: [],
        },
        total: '0.0',
        newTotal: '0.0',
      },

      products: [],

      leadStatuses: [
        'confirmed',
        'no answer',
        'cancelled',
      ],
      leadStatusColor: {
        'confirmed': 'success',
        'no answer': 'warning',
        'cancelled': 'danger',
      },

      newStatus: '',
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
    orderReviewProducts() {
      let revProducts = []

      for (let i = 0; i < this.order.products.length; i++) {
        let pId = this.order.products[i]

        if (pId === '') {
          continue
        }

        if (!revProducts.hasOwnProperty(pId)) {
          let p = this.getProductById(pId)
          revProducts[pId] = {
            name: p.name,
            quantity: 1,
            unit_price: p.price,
          }
        } else {
          revProducts[pId].quantity += 1
        }
      }

      let toReturn = []
      for (const [key, value] of Object.entries(revProducts)) {
        toReturn.push(value)
      }

      return toReturn
    },
  },
  methods: {
    // Frontend functions
    onPageChanged(val) {
      this.currentPage = val
    },
    async changeView(view, item = null) {
      this.$vs.loading()

      if (view == 'leads') {
        this.leads = await this.ecommerce.fetchLeads()
        this.view = view
      } else if (view == 'order_builder') {
        await this.fetchProducts()
        let lead = item
        this.order = {
          first_name: lead.first_name,
          last_name: lead.last_name,
          phone: lead.phone,
          email: lead.email,
          shipping: {
            first_name: lead.first_name,
            last_name: lead.last_name,
            phone: lead.phone,
            email: '',
            notes: '',
            street_address: '',
            city: '',
            province: '',
            state: '',
            country: '',
            postcode: '',
            address: '',
          },
          location: {},
          products: [],
          insurance: '0.0',
          review: {
            products: [],
          },
          total: '0.0',
          newTotal: '0.0',
        }
        this.view = view
      }

      this.$vs.loading.close()
    },
    getObjectById(id) {
      for (let i = 0; i < this.leads.length; i++) {
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
      await this.ecommerce.updateLead(lead.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead\'s status updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()

      this.applyFilters()
    },
    showNotes(lead) {
      this.lead = lead
      this.showPopup['notes'] = true
    },
    async updateNotes(lead) {
      this.$vs.loading()

      let params = {
        notes: lead.notes,
      }
      await this.ecommerce.updateLead(lead.id, params)

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
      info.contact = lead.contact
      let params = {
        info: JSON.stringify(info),
      }
      await this.ecommerce.updateLead(lead.id, params)

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
        street_address = loc.street_number + ' ' + loc.street
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
      return {
        id: p.id,
        name: p.name,  
        sku: p.sku,
        quantity_stock: parseInt(p.quantity_stock),
        price: parseFloat(p.min_price),
      }
    },
    async fetchProducts() {
      let products = await this.ecommerce.fetchStockProducts()

      this.products = []
      for (let i = 0; i < products.length; i++) {
        let p = products[i]

        if (p.status != 'active' || p.quantity_stock == 0) {
          continue
        }

        this.products.push(this.mapProduct(p))
      }
    },
    addProduct() {
      this.order.products.push('')
      this.refreshOrderReviewTotal()
    },
    removeProduct(index) {
      this.order.products.splice(index, 1)
      this.refreshOrderReviewTotal()
    },
    getProductById(id) {
      let p = null
      for (let i = 0; i < this.products.length; i++) {
        if (this.products[i].id == id) {
          p = this.products[i]
          break
        }
      }
      return p
    },
    updateOrderTotal() {
      this.order.total = this.order.newTotal
    },
    refreshOrderReviewTotal() {
      let revProducts = []

      for (let i = 0; i < this.order.products.length; i++) {
        let pId = this.order.products[i]

        if (pId === '') {
          continue
        }

        if (!revProducts.hasOwnProperty(pId)) {
          let p = this.getProductById(pId)
          revProducts[pId] = {
            name: p.name,
            quantity: 1,
            unit_price: p.price,
          }
        } else {
          revProducts[pId].quantity += 1
        }
      }

      let toReturn = []
      for (const [key, value] of Object.entries(revProducts)) {
        toReturn.push(value)
      }

      revProducts = toReturn
      let total = 0.0

      for (let i = 0; i < revProducts.length; i++) {
        let p = revProducts[i]
        total += parseFloat(p.unit_price)*parseInt(p.quantity)
      }

      total += parseFloat(this.order.insurance)

      this.order.total = total.toFixed(2)
    },
    async createOrder() {
      this.$vs.loading()

      let wooProducts = await this.ecommerce.fetchProducts()

      let o = this.order
      let billing = {
        first_name: o.first_name,
        last_name: o.last_name,
        address1: o.shipping.street_address,
        address2: '',
        city: o.shipping.city,
        state: o.shipping.province,
        postcode: o.shipping.postcode,
        country: o.shipping.country,
        email: o.email,
        phone: o.phone,
      }
      let shipping = {
        first_name: o.shipping.first_name,
        last_name: o.shipping.last_name,
        address1: o.shipping.street_address,
        address2: '',
        city: o.shipping.city,
        state: o.shipping.province,
        postcode: o.shipping.postcode,
        country: o.shipping.country,
      }

      let line_items_tmp = []
      for (let i = 0; i < o.products; i++) {
        // TODO: HERE
        let p = this.getProductById(o.products[i])

        let woop = null
        for (let j = 0; j < wooProducts.length; j++) {
          if (wooProducts[j].sku == p.sku) {
            woop = wooProducts[j]
            break
          }
        }

        if (woop == null) {
          continue
        }

        if (line_items_tmp.hasOwnProperty(woop.id)) {
          line_items_tmp[woop.id] += 1
        } else {
          line_items_tmp[woop.id] = 1
        }
      }

      let line_items = []
      for (const [key, value] of Object.entries(line_items_tmp)) {
        line_items.push({
          'product_id': key,
          'quantity': value,
        })
      }

      let params = {
        payment_method: 'cod',
        billing: billing,
        shipping: shipping,
        line_items: line_items,
      }
      let order = await this.ecommerce.createOrder(params)
      if (order === null) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot create the order',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      } else {
        let orderPushed = await this.ecommerce.pushOrder(order.id)
        if (orderPushed) {
          this.$vs.notify({
            title: 'Success',
            text: 'Order created',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
        } else {
          this.$vs.notify({
            title: 'Error',
            text: 'Order created but not pushed',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        }
      }

      this.$vs.loading.close()

      this.changeView('leads')
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
