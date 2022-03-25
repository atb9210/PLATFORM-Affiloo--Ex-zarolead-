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

            <vs-table stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="id">Id</vs-th>
                    <vs-th sort-key="full_name">Name</vs-th>
                    <vs-th>Actions</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                        <vs-td class="column-no-padding" :data="data[indextr].id">
                            {{data[indextr].id}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].full_name">
                            <div @contextmenu.prevent="$refs.order_context_menu.open($event, item)">
                                <vx-tooltip :title="item.full_name" :text="hasAddressInfo(item) ? getAddress(item) : 'Address undefined'" position="left">
                                    <vs-chip>
                                        {{item.full_name}}
                                    </vs-chip>
                                </vx-tooltip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding">
                            <div class="flex items-center">
                                <vs-button @click="changeView('edit', item)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-edit"></vs-button>
                            </div>
                        </vs-td>

                    </vs-tr>
                </template>
            </vs-table>
        </div>

        <!-- Create or edit order -->
        <div v-if="view == 'create'" class="w-full">
          <OrderBuilderComponent :customers="customers" :products="variations" @onOrderCreated="onOrderCreated" @onOrderUpdated="onOrderUpdated" />
        </div>

        <!-- Edit the order -->
        <div v-if="view == 'edit'" class="w-full">
          <OrderEditorComponent :order="order" :products="variations" @onOrderCreated="onOrderCreated" @onOrderUpdated="onOrderUpdated" />
        </div>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'
import { Customer, WarehouseProduct, WarehouseOrder } from 'zarolead'
import * as utils from 'utils'

function forcePhoneFormat(val) {
  let phonePrefix = '+(39)'
  let pos = val.indexOf(phonePrefix)
  if (pos < 0) {
    val = phonePrefix + val;
  }

  let newVal = phonePrefix
  for (let i = phonePrefix.length; i < val.length; i++) {
    if ((val[i] >= '0' && val[i] <= '9') || val[i] === ' ') {
      newVal += val[i]
    }
  }

  return newVal
}

function checkPhone(phone) {
  phone = phone.split(' ').join('')
  if (phone.length < 8) {
      return false
  }

  for (let i = 0; i < phone.length; i++) {
    if (!(phone[i] >= '0' && phone[i] <= '9') &&
      phone[i] !== '+' && phone[i] !== '(' && phone[i] !== ')') {
      return false
    }
  }

  return true
}

function cloneObject(obj) {
  let res = {}
  for (const [key, value] of Object.entries(obj)) {
    res[key] = value
  }
  return res
}

export default {
  components: {
    VueContext,
  },
  data () {
    return {
      customerClient: null,
      warehouseProductClient: null,
      warehouseOrderClient: null,

      view: 'orders',

      orders: [],
      selectedOrders: [],

      maxItems: 20,

      order: {},
      customers: [],
      products: [],
      variations: [],
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
    clearForm(type) {
      this.newObject[type] = {
        first_name: '',
        last_name: '',
        phone: '',
        email: '',
        address_line1: '',
        city: '',
        postcode: '',
        zone: '',
        country: 'IT',
      }
    },
    isValidForm(type) {
      if (type == 'create' || type == 'edit') {
        let obj = this.newObject[type]
        return (obj.first_name.length > 0 && obj.last_name.length > 0 &&
                obj.phone.length > 0 && checkPhone(obj.phone))
      }
      return false
    },
    async createObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let obj = this.newObject['create']
      let params = {
        first_name: obj.first_name,
        last_name: obj.last_name,
        phone: obj.phone,
        email: obj.email,
        address_line1: obj.address_line1,
        city: obj.city,
        postcode: obj.postcode,
        zone: obj.zone,
        country: obj.country,
      }

      // Create warehouse order
      let result = false
      try {
        const response = await this.$http.post('/api/v1/warehouse/orders', params, { headers: headers })
        result = true
      } catch (error) {
        // Nothing
      }

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Warehouse order created',
          iconPack: 'feather',
          icon: 'icon-user-plus',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot create the order',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
          
      this.clearForm('create')
      this.view = 'orders'
      await this.changeView('orders')
    },
    async updateObject() {
      if (this.editingObject === null) return

      this.$vs.loading()

      let id = this.editingObject.id
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let obj = this.newObject['edit']
      let params = {
        first_name: obj.first_name,
        last_name: obj.last_name,
        phone: obj.phone,
        email: obj.email,
        address_line1: obj.address_line1,
        city: obj.city,
        postcode: obj.postcode,
        zone: obj.zone,
        country: obj.country,
      }

      let result = false
      try {
        const response = await this.$http.put('/api/v1/warehouse/orders/' + id, params, { headers })
        result = true
      } catch (error) {
        // Nothing
      }

      this.$vs.loading.close()

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Warehouse order updated',
          iconPack: 'feather',
          icon: 'icon-user-check',
          color: 'success'
        })
        this.clearForm('edit')
        await this.changeView('orders')
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the order',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }
    },
    findAddress(type) {
      let address_line1 = ''
      let city = ''
      let postcode = ''
      let zone = ''
      let country = ''

      let loc = this.locationFound
      if (loc.hasOwnProperty('street') && loc.hasOwnProperty('street_number')) {
        address_line1 = loc.street + ' ' + loc.street_number
      }
      if (loc.hasOwnProperty('city')) {
        city = loc.city
      }
      if (loc.hasOwnProperty('postcode')) {
        postcode = loc.postcode
      }
      if (loc.hasOwnProperty('province')) {
        zone = loc.province
      }
      if (loc.hasOwnProperty('country')) {
        country = loc.country
      }

      this.newObject[type].address_line1 = address_line1
      this.newObject[type].city = city
      this.newObject[type].postcode = postcode
      this.newObject[type].zone = zone
      this.newObject[type].country = country
    },
    async doAction(type) {
      if (type == 'create') {
        await this.createObject()
      } else if (type == 'edit') {
        await this.updateObject()
      }
    },
    forcePhoneFormat(type) {
      this.newObject[type].phone = forcePhoneFormat(this.newObject[type].phone)
    },
    hasAddressInfo(order) {
      if (order.address_line1 !== null && order.address_line1 != '') {
        return true
      }
      return false
    },
    getAddress(order) {
      let address = order.address_line1
      if (order.city !== null && order.city != '') {
        address += ' ' + order.city
      }
      if (order.zone !== null && order.zone != '') {
        address += ' (' + order.zone + ')'
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
      let result = (order !== null)

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Warehouse order created',
          iconPack: 'feather',
          icon: 'icon-user-plus',
          color: 'success'
        })
        this.changeView('orders')
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot create the order',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }
    },
    async onOrderUpdated(order) {
      let result = (order !== null)

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Warehouse order updated',
          iconPack: 'feather',
          icon: 'icon-user-plus',
          color: 'success'
        })
        this.changeView('orders')
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the order',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }
    },
  },
  async created() {
    this.customerClient = new Customer(this.$zarolead)
    this.warehouseProductClient = new WarehouseProduct(this.$zarolead)
    this.warehouseOrderClient = new WarehouseOrder(this.$zarolead)

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
