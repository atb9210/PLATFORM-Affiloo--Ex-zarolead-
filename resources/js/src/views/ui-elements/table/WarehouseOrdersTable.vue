<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Orders" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button v-if="view == 'orders'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="changeView('create')"></vs-button>
            <vs-button v-if="view == 'orders'" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup" :disabled="selected.length <= 0"></vs-button>
            <vs-button v-if="view == 'create' || view == 'edit'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="changeView('orders')"></vs-button>
            <vs-button v-if="view == 'chat'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="changeView('orders')"></vs-button>
        </p>

        <!-- Orders table -->
        <div v-if="view == 'orders'">
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
                <li>
                  <a href="#" @click="$refs.order_context_menu.close();changeView('edit', data)" class="flex items-center text-sm">
                    <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Edit</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.order_context_menu.close();showChat(data)" class="flex items-center text-sm">
                    <feather-icon icon="MessageCircleIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Whatsapp Chat</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.order_context_menu.close()" class="flex items-center text-sm">
                    <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Close</span>
                  </a>
                </li>
            </vue-context>

            <vs-table stripe multiple v-model="selectedOrders" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="id">Id</vs-th>
                    <vs-th sort-key="full_name">Name</vs-th>
                    <vs-th sort-key="customer.phone">Phone</vs-th>
                    <vs-th sort-key="shipping.country">GEO</vs-th>
                    <vs-th>Method</vs-th>
                    <vs-th sort-key="amount">Amount</vs-th>
                    <vs-th>Lines</vs-th>
                    <vs-th sort-key="shipping.id">Shipping ID</vs-th>
                    <vs-th sort-key="shipping.status">Shipping Status</vs-th>
                    <vs-th sort-key="shipping.created_at">Shipping Date</vs-th>
                    <vs-th sort-key="shipping.delivered_at">Delivery Date</vs-th>
                    <vs-th sort-key="shipping.carrier.name">Carrier</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                        <vs-td class="column-no-padding" :data="data[indextr].id">
                            {{data[indextr].id}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].full_name">
                            <div @contextmenu.prevent="$refs.order_context_menu.open($event, item)">
                                <vx-tooltip :title="item.full_name" :text="hasAddress(item.customer) ? getAddress(item.customer) : 'Address undefined'" position="left">
                                    <vs-chip>
                                        {{item.full_name}}
                                    </vs-chip>
                                </vx-tooltip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].customer.phone">
                            {{data[indextr].customer.phone}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].shipping.country">
                            {{data[indextr].shipping.country}}
                        </vs-td>

                        <vs-td class="column-no-padding">
                            <!-- TODO: -->
                            Online/Cod
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].amount">
                            € {{item.amount.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding">
                            <vx-tooltip :title="item.lines.length + ' lines'" :text="getOrderLinesSummary(item.lines)" position="left">
                                <vs-chip>
                                    {{item.lines.length}} lines
                                </vs-chip>
                            </vx-tooltip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].shipping.id">
                            {{item.shipping.id}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].shipping.status">
                            {{item.shipping.status}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].shipping.created_at">
                            <span v-if="item.shipping.created_at !== null && item.shipping.created_at != ''">
                                {{utils.formatDate(new Date(item.shipping.created_at))}}
                            </span>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].shipping.delivered_at">
                            <span v-if="item.shipping.delivered_at !== null && item.shipping.delivered_at != ''">
                                {{utils.formatDate(new Date(item.shipping.delivered_at))}}
                            </span>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].shipping.carrier.name">
                            {{item.shipping.carrier.name}}
                        </vs-td>

                    </vs-tr>
                </template>
            </vs-table>
        </div>

        <!-- Create or edit order -->
        <div v-if="view == 'create'" class="w-full">
          <OrderBuilderComponent :customers="customers" :products="variations" :carriers="carriers" @onOrderCreated="onOrderCreated" @onOrderUpdated="onOrderUpdated" />
        </div>

        <!-- Edit the order -->
        <div v-if="view == 'edit'" class="w-full">
          <OrderEditorComponent :order="order" :products="variations" @onOrderCreated="onOrderCreated" @onOrderUpdated="onOrderUpdated" />
        </div>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'
import { Customer, WarehouseProduct, WarehouseOrder, WarehouseShipping, WarehouseShippingCarrier } from 'zarolead'
import * as utils from 'utils'

export default {
  components: {
    VueContext,
  },
  data () {
    return {
      utils: null,
      customerClient: null,
      warehouseProductClient: null,
      warehouseOrderClient: null,
      warehouseShippingClient: null,
      warehouseShippingCarrierClient: null,

      view: 'orders',

      orders: [],
      selectedOrders: [],

      maxItems: 20,

      order: {},
      customers: [],
      products: [],
      variations: [],
      carriers: [],
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
    objects() {
      if (this.view == 'orders') {
        return this.orders
      }
      return []
    },
    selected() {
      if (this.view == 'orders') {
        return this.selectedOrders
      }
      return []
    },
  },
  methods: {
    getObjectById(id, objects = null) {
      if (objects === null) {
        objects = this.objects
      }
      for (let i = 0; i < objects.length; i++) {
        if (objects[i].hasOwnProperty('id') && objects[i].id == id) {
          return objects[i]
        }
      }
      return null
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    confirmDeletePopup() {
      if (this.selected.length == 0) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Delete confirmation',
        text: 'Are you sure you want to delete ' + this.selected.length + ' object(s)?',
        accept: this.deleteSelected
      })
    },
    async deleteSelected() {
      let numSelected = this.selected.length
      if (numSelected == 0) return

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      for (let i = 0; i < numSelected; i++) {
        let obj = this.selected[i]
        const response = await this.$http.delete('/api/v1/warehouse/orders/' + obj.id, { headers })
      }

      this.$vs.notify({
        title: 'Success',
        text: numSelected + ' Warehouse order(s) deleted',
        iconPack: 'feather',
        icon: 'icon-user-x',
        color: 'success'
      })
      this.$vs.loading.close()
      await this.changeView('orders')
    },
    async changeView(view, value = null) {
      if (view == 'orders') {
        this.$vs.loading()

        this.selectedOrders = []
        this.orders = await this.warehouseOrderClient.fetch()
        for (let i = 0; i < this.orders.length; i++) {
          let obj = this.orders[i]
          obj.full_name = obj.customer.first_name + ' ' + obj.customer.last_name
          obj.amount = parseFloat(obj.amount)
          this.orders[i] = obj
        }

        this.$vs.loading.close()
      } else if (view == 'create') {
        this.order = {}
      } else if (view == 'edit') {
        this.order = utils.deepCloneObject(value)
      }
      this.view = view
    },
    hasAddress(customer) {
      if (customer.address_line1 !== null && customer.address_line1 != '') {
        return true
      }
      return false
    },
    getAddress(customer) {
      let address = customer.address_line1
      if (customer.city !== null && customer.city != '') {
        address += ' ' + customer.city
      }
      if (customer.zone !== null && customer.zone != '') {
        address += ' (' + customer.zone + ')'
      }
      return address
    },
    async showChat(order) {
      this.$vs.loading()

      this.order = order
      this.chatMessages = await this.warehouseOrderClient.getChatMessages(order.id)
      for (let i = 0; i < this.chatMessages.length; i++) {
        let msg = this.chatMessages[i]

        msg.datetime = new Date(msg.time*1000)

        this.chatMessages[i] = msg
      }

      this.view = 'chat'
      this.$vs.loading.close()
    },
    async onOrderCreated(order) {
      await this.changeView('orders')
    },
    async onOrderUpdated(order) {
      //await this.changeView('orders')
    },
    getOrderLinesSummary(lines) {
      let s = ''
      for (let i = 0; i < lines.length; i++) {
        let line = lines[i]
        if (i > 0) {
          s += ', '
        }
        s += line.product_variation.full_name + '(' + line.quantity + ')'
      }
      return s
    },
  },
  async created() {
    this.utils = utils
    this.customerClient = new Customer(this.$zarolead)
    this.warehouseProductClient = new WarehouseProduct(this.$zarolead)
    this.warehouseOrderClient = new WarehouseOrder(this.$zarolead)
    this.warehouseShippingClient = new WarehouseShipping(this.$zarolead)
    this.warehouseShippingCarrierClient = new WarehouseShippingCarrier(this.$zarolead)

    this.$vs.loading()

    this.customers = await this.customerClient.fetch()
    for (let i = 0; i < this.customers.length; i++) {
      let obj = this.customers[i]
      obj.name = obj.first_name + ' ' + obj.last_name + '  ' + obj.phone
      this.customers[i] = obj
    }

    this.products = await this.warehouseProductClient.fetch()
    this.variations = []
    for (let i = 0; i < this.products.length; i++) {
      let product = this.products[i]
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

    this.carriers = await this.warehouseShippingCarrierClient.fetch()

    this.$vs.loading.close()

    await this.changeView('orders')
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
</style>
