<template>
    <div>

    <div class="w-full pr-2 pl-2">
        <h2 class="mb-3">Customer</h2>

        <div class="vx-row w-full">
            <div class="vx-col mb-3 w-full">
                <vs-checkbox v-model="useExistingCustomer">Use existing customer</vs-checkbox>
            </div>
        </div>

        <div class="vx-row w-full" v-if="useExistingCustomer">
            <div class="vx-col mb-3 w-full">
                <my-select v-model="existingCustomer" :options="customers" class="w-full select-large" autocomplete />
            </div>
        </div>
        <CustomerBuilderFormComponent v-else v-model="newCustomer" />

    </div>

    <hr class="mt-3 mb-6">

    <div class="w-full">
        <h2 class="mb-3">Order Lines</h2>
        <OrderLinesEditorComponent :order="order" :products="products" />
    </div>

    <div class="w-full pr-2 pl-2">
        <div class="vx-row w-full mt-3 mb-3 font-bold items-center flex">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-package" @click="createOrder" :disabled="!isValidForm()">Create Order</vs-button>
        </div>
    </div>

    </div>

</template>

<script>
import { Customer, WarehouseOrder } from 'zarolead'

export default {
  name: 'OrderBuilderComponent',
  components: {
  },
  props: {
    customers: { type: Array, required: true },
    products: { type: Array, required: true },
  },
  computed: {
    customer() {
      if (this.useExistingCustomer) {
        return this.existingCustomer
      }
      return this.newCustomer
    },
  },
  data () {
    return {
      useExistingCustomer: true,

      order: {
        lines: [],
        total: 0.0,
      },

      existingCustomer: {},
      newCustomer: {
        first_name: '',
        last_name: '',
        phone: '',
        email: '',
        address_line1: '',
        address_line2: '',
        city: '',
        postcode: '',
        zone: '',
        country: '',
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

    }
  },
  methods: {
    isValidPhone(phone) {
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
    },
    isValidZone(zone) {
      let isValid = false

      for (let i = 0; i < this.zoneOptions.length; i++) {
        let z = this.zoneOptions[i].value
        if (z == zone) {
          isValid = true
          break
        }
      }

      return isValid
    },
    isValidCountry(country) {
      let isValid = false

      isValid = (this.countryOptions.includes(country))

      return isValid
    },
    isValidForm() {
      let isValidCustomer = false
      let cust = this.customer
      if (this.useExistingCustomer) {
        for (let i = 0; i < this.customers.length; i++) {
          let c = this.customers[i]
          if (cust.hasOwnProperty('id') && cust.id == c.id) {
            isValidCustomer = true
            break
          }
        }
      } else {
        isValidCustomer = (cust.first_name.length > 0 && cust.last_name.length > 0 &&
                           cust.phone.length > 0 && this.isValidPhone(cust.phone))
      }

      // Check address validity
      isValidCustomer = (isValidCustomer && cust.address_line1.length > 0 &&
                         cust.city.length > 0 && cust.postcode.length > 0 &&
                         cust.zone.length > 0 && cust.country.length > 0 &&
                         this.isValidZone(cust.zone) && this.isValidCountry(cust.country))

      let isValidOrder = (this.order.lines.length > 0)

      return (isValidCustomer && isValidOrder)
    },
    async createOrder() {
      this.$vs.loading()

      // Create/select the customer
      let customer = null
      if (this.useExistingCustomer) {
        customer = this.existingCustomer
      } else {
        let customerClient = new Customer(localStorage.getItem('accessToken'))
        customer = await customerClient.create(this.newCustomer)
      }
      if (customer === null) {
        // TODO: Log error
      }

      // Create the order
      let lines = []
      for (let i = 0; i < this.order.lines.length; i++) {
        let line = this.order.lines[i]
        let quantity = parseInt(line.quantity)
        let fullPrice = parseFloat(line.unit_price)*quantity
        let price = fullPrice
        let discount = 0.0
        lines.push({
          product_id: line.product.id,
          quantity: quantity,
          full_price: fullPrice,
          discount: discount,
          price: price,
        })
      }
      let orderClient = new WarehouseOrder(localStorage.getItem('accessToken'))
      let order = await orderClient.create({
        customer_id: customer.id,
        lines: JSON.stringify(lines),
      })

      let result = (order !== null)
      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Warehouse order created',
          iconPack: 'feather',
          icon: 'icon-package',
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
    },
  },
  updated () {
  },
  mounted () {
  }
}
</script>
