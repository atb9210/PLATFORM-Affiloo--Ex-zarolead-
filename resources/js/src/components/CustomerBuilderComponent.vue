<template>
    <div class="w-full pr-2 pl-2">

        <CustomerBuilderFormComponent v-model="customer" />

        <div class="vx-row w-full mt-3 mb-3 font-bold items-center flex">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="createCustomer" :disabled="!isValidForm()">Create Order</vs-button>
        </div>

    </div>

</template>

<script>
import { Customer } from 'zarolead'

export default {
  name: 'CustomerBuilderComponent',
  props: {
    value: {
      type: Object,
      required: false,
      default: {},
    },
  },
  data () {
    return {
      editMode: false,

      customer: {
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
    checkPhone(phone) {
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
    isValidForm() {
      let cust = this.customer
      let isValidCustomer = (cust.first_name.length > 0 && cust.last_name.length > 0 &&
                             cust.phone.length > 0 && this.checkPhone(cust.phone))
      return isValidCustomer
    },
    async createCustomer() {
      this.$vs.loading()

      let customerClient = new Customer(this.$zarolead)

      let params = {
        first_name: this.customer.first_name,
        last_name: this.customer.last_name,
        phone: this.customer.phone,
        email: this.customer.email,
        address_line1: this.customer.address_line1,
        address_line2: this.customer.address_line2,
        city: this.customer.city,
        postcode: this.customer.postcode,
        zone: this.customer.zone,
        country: this.customer.country,
      }

      if (this.editMode) {
        let customer = await customerClient.update(this.customer.id, params)
        this.$emit('onCustomerUpdated', customer)
      } else {
        let customer = await customerClient.create(params)
        this.$emit('onCustomerCreated', customer)
      }

      this.$vs.loading.close()
    },
  },
  created() {
    if (this.value !== null) {
      // Add not existing keys
      for (const [key, value] of Object.entries(this.customer)) {
        if (!this.value.hasOwnProperty(key)) {
          this.value[key] = value
        }
      }
      this.customer = this.value
    }

    if (this.customer.hasOwnProperty('id')) {
      this.editMode = true
    }
  },
  updated () {
  },
  mounted () {
  }
}
</script>

<style lang="scss" scoped>
.icon-button {
  cursor: pointer;
}
</style>
