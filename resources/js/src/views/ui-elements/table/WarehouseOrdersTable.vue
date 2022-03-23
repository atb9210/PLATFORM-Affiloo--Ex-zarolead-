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
            <vs-button v-if="(['create','edit']).includes(view)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="view = 'orders'"></vs-button>
            <vs-button v-if="view == 'chat'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="view = 'orders'"></vs-button>
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
        <div v-if="(['create','edit']).includes(view)" class="w-full">
          <OrderBuilderComponent :customers="customers" :products="products" @onOrderCreated="onOrderCreated" @onOrderUpdated="onOrderUpdated" />
        </div>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'
import { Customer, WarehouseProduct, WarehouseOrder } from 'zarolead'

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
      productClient: null,
      orderClient: null,

      view: 'orders',

      objects: [],
      selected: [],

      maxItems: 20,

      editingObject: null,

      newObject: {
        'create': {
          first_name: '',
          last_name: '',
          phone: '',
          email: '',
          address_line1: '',
          city: '',
          postcode: '',
          zone: '',
          country: 'IT',
        },
        'edit': {
          first_name: '',
          last_name: '',
          phone: '',
          email: '',
          address_line1: '',
          city: '',
          postcode: '',
          zone: '',
          country: 'IT',
        },
      },

      locationFound: {},

      zoneOptions: [
        { label: 'AGRIGENTO', value: 'AG' },
        { label: 'ALESSANDRIA', value: 'AL' },
        { label: 'ANCONA', value: 'AN' },
        { label: 'AOSTA', value: 'AO' },
        { label: 'AREZZO', value: 'AR' },
        { label: 'ASCOLI PICENO', value: 'AP' },
        { label: 'ASTI', value: 'AT' },
        { label: 'AVELLINO', value: 'AV' },
        { label: 'BARI', value: 'BA' },
        { label: 'BARLETTA-ANDRIA-TRANI', value: 'BT' },
        { label: 'BELLUNO', value: 'BL' },
        { label: 'BENEVENTO', value: 'BN' },
        { label: 'BERGAMO', value: 'BG' },
        { label: 'BIELLA', value: 'BI' },
        { label: 'BOLOGNA', value: 'BO' },
        { label: 'BOLZANO', value: 'BZ' },
        { label: 'BRESCIA', value: 'BS' },
        { label: 'BRINDISI', value: 'BR' },
        { label: 'CAGLIARI', value: 'CA' },
        { label: 'CALTANISSETTA', value: 'CL' },
        { label: 'CAMPOBASSO', value: 'CB' },
        { label: 'CARBONIA-IGLESIAS', value: 'CI' },
        { label: 'CASERTA', value: 'CE' },
        { label: 'CATANIA', value: 'CT' },
        { label: 'CATANZARO', value: 'CZ' },
        { label: 'CHIETI', value: 'CH' },
        { label: 'COMO', value: 'CO' },
        { label: 'COSENZA', value: 'CS' },
        { label: 'CREMONA', value: 'CR' },
        { label: 'CROTONE', value: 'KR' },
        { label: 'CUNEO', value: 'CN' },
        { label: 'ENNA', value: 'EN' },
        { label: 'FERMO', value: 'FM' },
        { label: 'FERRARA', value: 'FE' },
        { label: 'FIRENZE', value: 'FI' },
        { label: 'FOGGIA', value: 'FG' },
        { label: 'FORLI-CESENA', value: 'FC' },
        { label: 'FROSINONE', value: 'FR' },
        { label: 'GENOVA', value: 'GE' },
        { label: 'GORIZIA', value: 'GO' },
        { label: 'GROSSETO', value: 'GR' },
        { label: 'IMPERIA', value: 'IM' },
        { label: 'ISERNIA', value: 'IS' },
        { label: 'LA SPEZIA', value: 'SP' },
        { label: 'L’AQUILA', value: 'AQ' },
        { label: 'LATINA', value: 'LT' },
        { label: 'LECCE', value: 'LE' },
        { label: 'LECCO', value: 'LC' },
        { label: 'LIVORNO', value: 'LI' },
        { label: 'LODI', value: 'LO' },
        { label: 'LUCCA', value: 'LU' },
        { label: 'MACERATA', value: 'MC' },
        { label: 'MANTOVA', value: 'MN' },
        { label: 'MASSA-CARRARA', value: 'MS' },
        { label: 'MATERA', value: 'MT' },
        { label: 'MEDIO CAMPIDANO', value: 'VS' },
        { label: 'MESSINA', value: 'ME' },
        { label: 'MILANO', value: 'MI' },
        { label: 'MODENA', value: 'MO' },
        { label: 'MONZA E BRIANZA', value: 'MB' },
        { label: 'NAPOLI', value: 'NA' },
        { label: 'NOVARA', value: 'NO' },
        { label: 'NUORO', value: 'NU' },
        { label: 'OGLIASTRA', value: 'OG' },
        { label: 'OLBIA-TEMPIO', value: 'OT' },
        { label: 'ORISTANO', value: 'OR' },
        { label: 'PADOVA', value: 'PD' },
        { label: 'PALERMO', value: 'PA' },
        { label: 'PARMA', value: 'PR' },
        { label: 'PAVIA', value: 'PV' },
        { label: 'PERUGIA', value: 'PG' },
        { label: 'PESARO E URBINO', value: 'PU' },
        { label: 'PESCARA', value: 'PE' },
        { label: 'PIACENZA', value: 'PC' },
        { label: 'PISA', value: 'PI' },
        { label: 'PISTOIA', value: 'PT' },
        { label: 'PORDENONE', value: 'PN' },
        { label: 'POTENZA', value: 'PZ' },
        { label: 'PRATO', value: 'PO' },
        { label: 'RAGUSA', value: 'RG' },
        { label: 'RAVENNA', value: 'RA' },
        { label: 'REGGIO CALABRIA', value: 'RC' },
        { label: 'REGGIO EMILIA', value: 'RE' },
        { label: 'RIETI', value: 'RI' },
        { label: 'RIMINI', value: 'RN' },
        { label: 'ROMA', value: 'RM' },
        { label: 'ROVIGO', value: 'RO' },
        { label: 'SALERNO', value: 'SA' },
        { label: 'SASSARI', value: 'SS' },
        { label: 'SAVONA', value: 'SV' },
        { label: 'SIENA', value: 'SI' },
        { label: 'SIRACUSA', value: 'SR' },
        { label: 'SONDRIO', value: 'SO' },
        { label: 'TARANTO', value: 'TA' },
        { label: 'TERAMO', value: 'TE' },
        { label: 'TERNI', value: 'TR' },
        { label: 'TORINO', value: 'TO' },
        { label: 'TRAPANI', value: 'TP' },
        { label: 'TRENTO', value: 'TN' },
        { label: 'TREVISO', value: 'TV' },
        { label: 'TRIESTE', value: 'TS' },
        { label: 'UDINE', value: 'UD' },
        { label: 'VARESE', value: 'VA' },
        { label: 'VENEZIA', value: 'VE' },
        { label: 'VERBANO-CUSIO-OSSOLA', value: 'VB' },
        { label: 'VERCELLI', value: 'VC' },
        { label: 'VERONA', value: 'VR' },
        { label: 'VIBO VALENTIA', value: 'VV' },
        { label: 'VICENZA', value: 'VI' },
        { label: 'VITERBO', value: 'VT' },
      ],

      countryOptions: [
        'IT',
      ],

      order: {},
      chatMessages: [],
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
  },
  methods: {
    getObjectIndexById(id) {
      let index = -1;
      this.objects.forEach((obj, i) => {
        if (obj.id == id) {
          index = i
          return
        }
      })
      return index
    },
    async fetchObjects() {
      this.selected = []

      this.$vs.loading()

      this.objects = await this.orderClient.fetch()
      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]
        obj.full_name = obj.first_name + ' ' + obj.last_name
        this.objects[i] = obj
      }

      this.$vs.loading.close()
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

      this.selected = []
      this.$vs.notify({
        title: 'Success',
        text: numSelected + ' Warehouse order(s) deleted',
        iconPack: 'feather',
        icon: 'icon-user-x',
        color: 'success'
      })
      this.$vs.loading.close()
      await this.fetchObjects()
    },
    async changeView(type, value = null) {
      if (type == 'orders') {
        await this.fetchObjects()
      } else if (type == 'create') {
        this.order = {}
      } else if (type == 'edit') {
        if (value === null) {
          return
        }
        this.order = value
      }
      this.view = type
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
      await this.fetchObjects()
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
        this.view = 'orders'
        await this.fetchObjects()
        this.editingObject = null
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
      let orderClient = new WarehouseOrder(localStorage.getItem('accessToken'))

      this.chatMessages = await orderClient.getChatMessages(order.id)
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
    this.orderClient = new WarehouseOrder(this.$zarolead)
    this.productClient = new WarehouseProduct(this.$zarolead)

    this.$vs.loading()
    this.customers = await this.customerClient.fetch()
    this.products = await this.productClient.fetch()
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
