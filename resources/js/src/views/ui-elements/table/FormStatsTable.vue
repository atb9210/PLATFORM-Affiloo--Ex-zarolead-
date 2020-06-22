<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Form Stats" code-toggler>

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

        <vs-table multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="day">Day</vs-th>
                <vs-th sort-key="visits">Visits</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="total_cost">Cost</vs-th>
                <vs-th sort-key="total_commission">Commission</vs-th>
                <vs-th sort-key="profit">Profit</vs-th>
                <vs-th sort-key="roi">ROI</vs-th>
                <vs-th sort-key="cr">CR</vs-th>
                <vs-th sort-key="cpv">CPV</vs-th>
                <vs-th sort-key="epv">EPV</vs-th>
                <vs-th sort-key="cpl">CPL</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].day">
                        {{data[indextr].day}}
                    </vs-td>

                    <vs-td :data="data[indextr].visits">
                        {{data[indextr].visits}}
                    </vs-td>

                    <vs-td :data="data[indextr].leads">
                        {{data[indextr].leads}}
                    </vs-td>

                    <vs-td :data="data[indextr].total_cost">
                        <vs-input v-if="isEditingCost && editingObject == data[indextr].id" type="number" step="0.01" min="0" v-model="editingCostValue" @keyup.enter="editCost" icon-pack="feather" icon="icon-dollar-sign" placeholder="Amount" class="is-label-placeholder"></vs-input>
                        <span v-else>€ {{data[indextr].total_cost}} <span class="underline" @click="showEditCostInput(data[indextr].id)">(edit)</span></span>
                    </vs-td>

                    <vs-td :data="data[indextr].total_commission">
                        € {{data[indextr].total_commission}}
                    </vs-td>

                    <vs-td :data="data[indextr].profit">
                        <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : 'danger'">€ {{data[indextr].profit}}</vs-chip>
                    </vs-td>

                    <vs-td :data="data[indextr].roi">
                        <vs-chip transparent :color="(data[indextr].roi > 0) ? 'success' : 'danger'">{{data[indextr].roi}} %</vs-chip>
                    </vs-td>

                    <vs-td :data="data[indextr].cr">
                        {{data[indextr].cr}} %
                    </vs-td>

                    <vs-td :data="data[indextr].cpv">
                        € {{data[indextr].cpv}}
                    </vs-td>

                    <vs-td :data="data[indextr].epv">
                        <vs-chip transparent :color="(data[indextr].epv > 0) ? 'success' : 'danger'">€ {{data[indextr].epv}}</vs-chip>
                    </vs-td>

                    <vs-td :data="data[indextr].cpl">
                        € {{data[indextr].cpl}}
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td>
                    {{total_stats.visits}}
                </vs-td>
                <vs-td>
                    {{total_stats.leads}}
                </vs-td>
                <vs-td>
                    € {{total_stats.total_cost}}
                </vs-td>
                <vs-td>
                    € {{total_stats.total_commission}}</vs-td>
                <vs-td>
                    <vs-chip transparent :color="(total_stats.profit > 0) ? 'success' : 'danger'">€ {{total_stats.profit}}</vs-chip>
                </vs-td>
                <vs-td>
                    <vs-chip transparent :color="(total_stats.roi > 0) ? 'success' : 'danger'">{{total_stats.roi}} %</vs-chip>
                </vs-td>
                <vs-td>
                    {{total_stats.cr}} %
                </vs-td>
                <vs-td>
                    € {{total_stats.cpv}}
                </vs-td>
                <vs-td>
                    <vs-chip transparent :color="(total_stats.epv > 0) ? 'success' : 'danger'">€ {{total_stats.epv}}</vs-chip>
                </vs-td>
                <vs-td>
                    € {{total_stats.cpl}}
                </vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>
    </vx-card>
</template>

<script>
import vSelect from 'vue-select'

export default {
  components: {
    'v-select': vSelect,
  },
  props: {
    form_id: Number,
  },
  data () {
    return {
      objects: [],
      selected: [],
      deleted: [],

      maxItems: 20,

      isEditingCost: false,
      editingCostValue: 0,

      editingObject: -1,

      showPopup: {
        'create': false,
        'edit': false,
      },
      newObject: {
        'create': {
          name: '',
          campaign: '',
          network: '',
          product: '',
          quantity: '',
          price: 0,
          commission: 0,
          sub_commission: 0,
          traffic_source: '',

          fb_pixel: '',
          sub_id: '',
          api_call_url: '',

          // Worldfilia
          source_id: '',

          // Amsped
          product_reference: '',

          // Affiliation Park
          offer_id: '',
        },
        'edit': {
          name: '',
          campaign: '',
          network: '',
          product: '',
          quantity: '',
          price: 0,
          commission: 0,
          sub_commission: 0,
          traffic_source: '',

          fb_pixel: '',
          sub_id: '',
          api_call_url: '',

          // Worldfilia
          source_id: '',

          // Amsped
          product_reference: '',

          // Affiliation Park
          offer_id: '',
        },
      },

      campaigns: [],
      networks: [],
      products: [],
      traffic_sources: [],

      total_stats: {
        visits: 0,
        leads: 0,
        total_cost: 0,
        total_commission: 0,
        profit: 0,
        roi: 0,
        cr: 0,
        cpv: 0,
        epv: 0,
        cpl: 0,
      },
    }
  },
  watch: {
    form_id: function(newVal, oldVal) {
      this.retrieveObjects()
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
      var index = -1;
      this.objects.forEach((obj, i) => {
        if (obj.id == id) {
          index = i
          return
        }
      })
      return index
    },
    retrieveObjects() {
      this.selected = []
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Retrieve forms
      this.$http.get('/api/v1/forms/' + this.form_id + '/stats', { headers })
        .then((response) => {
          this.objects = response.data.data
          this.calculateStats()
          this.$vs.loading.close()
          console.log(this.objects)
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    },
    calculateStats() {
      this.total_stats = {
        visits: 0,
        leads: 0,
        total_cost: 0,
        total_commission: 0,
        profit: 0,
        roi: 0,
        cr: 0,
        cpv: 0,
        epv: 0,
        cpl: 0,
      }

      for (var i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        let profit = obj.total_commission - obj.total_cost
        let roi = parseFloat((obj.total_commission > 0 && obj.total_cost == 0) ? 9999 : (profit/obj.total_cost)*100)
        let cr = 0.0
        if (obj.leads == 0) cr = 0.0
        else if (obj.visits == 0) cr = 100.0
        else cr = parseFloat((obj.leads/obj.visits)*100.0)
        let cpv = parseFloat((obj.visits == 0) ? obj.total_cost : obj.total_cost/obj.visits)
        let epv = parseFloat((obj.visits == 0) ? obj.total_commission : obj.total_commission/obj.visits)
        let cpl = parseFloat((obj.leads == 0) ? obj.total_cost : obj.total_cost/obj.leads)

        this.total_stats.visits += obj.visits
        this.total_stats.leads += obj.leads
        this.total_stats.total_cost += parseFloat(obj.total_cost),
        this.total_stats.total_commission += parseFloat(obj.total_commission),

        // Format calculated stats
        profit = profit.toFixed(2)
        roi = roi.toFixed(2)
        cr = cr.toFixed(2)
        cpv = cpv.toFixed(2)
        epv = epv.toFixed(2)
        cpl = cpl.toFixed(2)

        this.objects[i].profit = profit
        this.objects[i].roi = roi
        this.objects[i].cr = cr
        this.objects[i].cpv = cpv
        this.objects[i].epv = epv
        this.objects[i].cpl = cpl
      }

      // Total stats
      let obj = this.total_stats

      let profit = obj.total_commission - obj.total_cost
      let roi = parseFloat((obj.total_commission > 0 && obj.total_cost == 0) ? 9999 : (profit/obj.total_cost)*100)
      let cr = 0.0
      if (obj.leads == 0) cr = 0.0
      else if (obj.visits == 0) cr = 100.0
      else cr = parseFloat((obj.leads/obj.visits)*100.0)
      let cpv = parseFloat((obj.visits == 0) ? obj.total_cost : obj.total_cost/obj.visits)
      let epv = parseFloat((obj.visits == 0) ? obj.total_commission : obj.total_commission/obj.visits)
      let cpl = parseFloat((obj.leads == 0) ? obj.total_cost : obj.total_cost/obj.leads)

      // Format calculated stats
      profit = profit.toFixed(2)
      roi = roi.toFixed(2)
      cr = cr.toFixed(2)
      cpv = cpv.toFixed(2)
      epv = epv.toFixed(2)
      cpl = cpl.toFixed(2)

      this.total_stats.profit = profit
      this.total_stats.roi = roi
      this.total_stats.cr = cr
      this.total_stats.cpv = cpv
      this.total_stats.epv = epv
      this.total_stats.cpl = cpl
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    showEditCostInput(id) {
      let index = this.getObjectIndexById(id)

      this.isEditingCost = true
      this.editingCostValue = this.objects[index].total_cost
      this.editingObject = id
    },
    editCost() {
      this.$vs.loading()

      let index = this.getObjectIndexById(this.editingObject)

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        form_stats_id: this.editingObject,
        total_cost: this.editingCostValue,
      }
      this.$http.put('/api/v1/forms/' + this.objects[index].form.id + '/stats', data, { headers })
        .then((response) => {
          this.objects[index].total_cost = this.editingCostValue
          this.isEditingCost = false
          this.calculateStats()
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Total cost updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    },
    deleteSelected() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      this.deleted = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.selected.forEach((form) => {
        this.$http.delete('/api/v1/forms/' + form.id, { headers })
          .then((response) => {
            this.deleted.push(form.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Form(s) deleted',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.selected = []
              this.deleted = []
              this.retrieveObjects()
            }
          })
          .catch((error) => {
            this.$vs.loading.close()
            this.$vs.notify({
              title: 'Error',
              text: error.message,
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'danger'
            })
          })
      })
    },
    showForm(type) {
      this.showPopup[type] = true
    },
    clearForm(type) {
      this.newObject[type] = {
        name: '',
        campaign: '',
        network: '',
        product: '',
        quantity: '',
        price: 0,
        commission: 0,
        sub_commission: 0,
        traffic_source: '',

        fb_pixel: '',
        sub_id: '',
        api_call_url: '',

        // Worldfilia
        source_id: '',

        // Amsped
        product_reference: '',

        // Affiliation Park
        offer_id: '',
      }
    },
    isValidForm(type) {
      let obj = this.newObject[type]
      return (obj.name.length > 0
        && obj.campaign != '' && obj.campaign.id >= 1
        && obj.network != '' && obj.network.id >= 1
        && obj.product != '' && obj.product.id >= 1
        && obj.quantity.length > 0
        && obj.traffic_source != '' && obj.traffic_source.id >= 1)
    },
    createObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.newObject['create'].name,
        campaign_id: this.newObject['create'].campaign.id,
        network_id: this.newObject['create'].network.id,
        product_id: this.newObject['create'].product.id,
        quantity: this.newObject['create'].quantity,
        price: this.newObject['create'].price,
        commission: this.newObject['create'].commission,
        sub_commission: this.newObject['create'].sub_commission,
        traffic_source_id: this.newObject['create'].traffic_source.id,
        
        network_params: {
        }
      }
      if (this.newObject['create'].network.label == 'Worldfilia') {
        data.network_params = {
          fb_pixel: this.newObject['create'].fb_pixel,
          api_call_url: this.newObject['create'].api_call_url,
          source_id: this.newObject['create'].source_id,
        }
      } else if (this.newObject['create'].network.label == 'Amsped') {
        data.network_params = {
          fb_pixel: this.newObject['create'].fb_pixel,
          api_call_url: this.newObject['create'].api_call_url,
          sub_id: this.newObject['create'].sub_id,
          product_reference: this.newObject['create'].product_reference,
        }
      } else if (this.newObject['create'].network.label == 'Affiliation Park') {
        data.network_params = {
          fb_pixel: this.newObject['create'].fb_pixel,
          api_call_url: this.newObject['create'].api_call_url,
          sub_id: this.newObject['create'].sub_id,
          offer_id: this.newObject['create'].offer_id,
        }
      }
      data.network_params = JSON.stringify(data.network_params)

      // Retrieve forms
      this.$http.post('/api/v1/forms', data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Form created',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.clearForm('create')
          this.showPopup['create'] = false
          this.retrieveObjects()
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    },
    editForm(id) {
      let obj = this.objects[this.getObjectIndexById(id)]
      this.newObject['edit'] = {
        name: obj.name,
        campaign: {
          id: obj.campaign.id,
          label: obj.campaign.name,
        },
        network: {
          id: obj.network.id,
          label: obj.network.name,
        },
        product: {
          id: obj.product.id,
          label: obj.product.name,
        },
        quantity: obj.quantity,
        price: obj.price,
        commission: obj.commission,
        sub_commission: obj.sub_commission,
        traffic_source: {
          id: obj.traffic_source.id,
          label: obj.traffic_source.name,
        },

        fb_pixel: '',
        api_call_url: '',
        sub_id: '',

        // Worldfilia
        source_id: '',

        // Amsped
        product_reference: '',

        // Affiliation Park
        offer_id: '',
      }
      if (obj.hasOwnProperty('network_params')) {
        if (obj.network_params.hasOwnProperty('fb_pixel')) {
          this.newObject['edit'].fb_pixel = obj.network_params.fb_pixel
        }
        if (obj.network_params.hasOwnProperty('api_call_url')) {
          this.newObject['edit'].api_call_url = obj.network_params.api_call_url
        }
        if (obj.network.name == 'Worldfilia') {
          if (obj.network_params.hasOwnProperty('source_id')) {
            this.newObject['edit'].source_id = obj.network_params.source_id
          }
        } else if (obj.network.name == 'Amsped') {
          if (obj.network_params.hasOwnProperty('sub_id')) {
            this.newObject['edit'].sub_id = obj.network_params.sub_id
          }
          if (obj.network_params.hasOwnProperty('product_reference')) {
            this.newObject['edit'].product_reference = obj.network_params.product_reference
          }
        } else if (obj.network.name == 'Affiliation Park') {
          if (obj.network_params.hasOwnProperty('sub_id')) {
            this.newObject['edit'].sub_id = obj.network_params.sub_id
          }
          if (obj.network_params.hasOwnProperty('offer_id')) {
            this.newObject['edit'].offer_id = obj.network_params.offer_id
          }
        }
      }
      this.editingObject = obj.id
      this.showForm('edit')
    },
    updateObject() {
      if (this.editingObject <= 0) return

      let id = this.editingObject

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        name: this.newObject['edit'].name,
        campaign_id: this.newObject['edit'].campaign.id,
        network_id: this.newObject['edit'].network.id,
        product_id: this.newObject['edit'].product.id,
        quantity: this.newObject['edit'].quantity,
        price: this.newObject['edit'].price,
        commission: this.newObject['edit'].commission,
        sub_commission: this.newObject['edit'].sub_commission,
        traffic_source_id: this.newObject['edit'].traffic_source.id,
      }
      if (this.newObject['edit'].network.label == 'Worldfilia') {
        data.network_params = {
          fb_pixel: this.newObject['edit'].fb_pixel,
          api_call_url: this.newObject['edit'].api_call_url,
          source_id: this.newObject['edit'].source_id,
        }
      } else if (this.newObject['edit'].network.label == 'Amsped') {
        data.network_params = {
          fb_pixel: this.newObject['edit'].fb_pixel,
          api_call_url: this.newObject['edit'].api_call_url,
          sub_id: this.newObject['edit'].sub_id,
          product_reference: this.newObject['edit'].product_reference,
        }
      } else if (this.newObject['edit'].network.label == 'Affiliation Park') {
        data.network_params = {
          fb_pixel: this.newObject['edit'].fb_pixel,
          api_call_url: this.newObject['edit'].api_call_url,
          sub_id: this.newObject['edit'].sub_id,
          offer_id: this.newObject['edit'].offer_id,
        }
      }
      data.network_params = JSON.stringify(data.network_params)

      this.$http.put('/api/v1/forms/' + id, data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Form updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.retrieveObjects()
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
      this.showPopup['edit'] = false
      this.editingObject = -1
    },
    deleteObject(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$http.delete('/api/v1/forms/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Form deleted',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.retrieveObjects()
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    },
  },
  created() {
    this.retrieveObjects()
  },
  mounted () {
  }
}

</script>
