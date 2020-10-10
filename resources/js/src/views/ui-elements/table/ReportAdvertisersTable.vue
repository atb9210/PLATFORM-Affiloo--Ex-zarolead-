<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Advertisers report" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
        </p>

        <div v-if="showFilters" class="mb-12">

            <div class="vx-row mb-2">
                <div class="vx-col w-3-12">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('this_month')" icon-pack="feather" icon="icon-calendar">This Month</vs-button>
                </div>
                <div class="vx-col w-3-12">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('last_month')" icon-pack="feather" icon="icon-calendar">Last Month</vs-button>
                </div>
            </div>

            <div class="vx-row mb-2">
                <div class="vx-col w-1/2">
                    <label>Month</label>
                    <vs-select v-model="filters.month" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in monthsOptions" class="w-full" />
                    </vs-select>
                </div>
                <div class="vx-col w-1/2">
                    <label>Year</label>
                    <vs-select v-model="filters.year" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in yearsOptions" class="w-full" />
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

        <vs-table stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="user.name">Advertiser</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="total_cost">Cost</vs-th>
                <vs-th sort-key="commission">Commission</vs-th>
                <vs-th sort-key="approved_commission">Approved</vs-th>
                <vs-th sort-key="approved_commission_percentage">% Approved</vs-th>
                <vs-th sort-key="net_profit">Net profit</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].user.name">
                        {{data[indextr].user.name}}
                    </vs-td>

                    <vs-td :data="data[indextr].leads">
                        {{data[indextr].leads}}
                    </vs-td>

                    <vs-td :data="data[indextr].total_cost">
                        {{data[indextr].total_cost}} €
                    </vs-td>

                    <vs-td :data="data[indextr].commission">
                        {{data[indextr].commission}} €
                    </vs-td>

                    <vs-td :data="data[indextr].approved_commission">
                        <vs-input v-if="isEditing && editingObjectId == data[indextr].id" type="number" step="0.01" min="0" :max="data[indextr].commission" v-model="editingValue" @keyup.enter="edit" icon-pack="feather" icon="icon-dollar-sign" placeholder="Amount" class="is-label-placeholder"></vs-input>
                        <span v-else>{{data[indextr].approved_commission}} € <span class="underline" @click="showEditInput(data[indextr].id)">(edit)</span></span>
                    </vs-td>

                    <vs-td :data="data[indextr].approved_commission_percentage">
                        {{data[indextr].approved_commission_percentage}} %
                    </vs-td>

                    <vs-td :data="data[indextr].net_profit">
                        {{data[indextr].net_profit}} €
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td>
                    {{total_stats.leads}}
                </vs-td>
                <vs-td>
                    {{total_stats.total_cost}} €
                </vs-td>
                <vs-td>
                    {{total_stats.commission}} €
                </vs-td>
                <vs-td>
                    {{total_stats.approved_commission}} €
                </vs-td>
                <vs-td>
                    {{total_stats.approved_commission_percentage}} %
                </vs-td>
                <vs-td>
                    {{total_stats.net_profit}} €
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
  data () {
    return {
      objects: [],
      objectsIds: [],

      selected: [],
      deleted: [],

      maxItems: 20,

      total_stats: {
        leads: 0,
        commission: 0.0,
        cost: 0.0,
        extra_cost: 0.0,
        profit: 0.0,
        roi: 0.0,
      },

      filters: {
        month: '',
        year: '',
      },
      showFilters: false,

      users: [],
      roles: [
        { id: 1, label: 'developer' },
        { id: 2, label: 'admin' },
        { id: 3, label: 'mediabuyer' },
        { id: 4, label: 'affiliate' },
        { id: 5, label: 'advertiser' },
        { id: 6, label: 'accountant' },
      ],

      monthsOptions: [
        { id: 1, label: 'January' },
        { id: 2, label: 'February' },
        { id: 3, label: 'March' },
        { id: 4, label: 'April' },
        { id: 5, label: 'May' },
        { id: 6, label: 'June' },
        { id: 7, label: 'July' },
        { id: 8, label: 'August' },
        { id: 9, label: 'September' },
        { id: 10, label: 'October' },
        { id: 11, label: 'November' },
        { id: 12, label: 'December' },
      ],
      yearsOptions: [],

      isEditing: false,
      editingObjectId: -1,
      editingObject: null,
      editingValue: 0,
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
      var index = -1
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
      let data = {}
      if (this.filters.month != '') {
        data['month'] = this.filters.month
      }
      if (this.filters.year != '') {
        data['year'] = this.filters.year
      }

      // Retrieve users' monthly report
      this.$http.get('/api/v1/reports/advertisers', { headers, params: data })
        .then((response) => {
          this.objects = []
          response.data.data.forEach((obj) => {
            let total_cost = parseFloat(obj.total_cost)
            let commission = parseFloat(obj.commission)
            let approved_commission = parseFloat(obj.approved_commission)
            let approved_commission_percentage = (commission > 0) ? ((approved_commission / commission)*100) : 0
            let net_profit = approved_commission - total_cost

            total_cost = total_cost.toFixed(2)
            commission = commission.toFixed(2)

            approved_commission = approved_commission.toFixed(2)
            approved_commission_percentage = approved_commission_percentage.toFixed(2)
            net_profit = net_profit.toFixed(2)

            this.objects.push({
              id: obj.id,
              user: obj.user,
              month: obj.month,
              year: obj.year,
              visits: obj.visits,
              leads: obj.leads,
              total_cost: total_cost,
              commission: commission,
              approved_commission: approved_commission,
              approved_commission_percentage: approved_commission_percentage,
              net_profit: net_profit,
            })
          })

          this.calculateStats()
          this.$vs.loading.close()
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
    onPageChanged(val) {
      this.currentPage = val
    },
    resetFilters() {
      this.filters = {
        month: '',
        year: '',
      }
      this.filterDatesShortcut('this_month')
    },
    applyFilters() {
      this.retrieveObjects()
    },
    filterDatesShortcut(period) {
      let invalidPeriod = false

      switch (period) {
        case 'this_month': {
          let today = new Date()
          this.filters.month = today.getMonth() + 1
          this.filters.year = today.getFullYear()
        } break
        case 'last_month': {
          let dateObj = new Date()
          let newDateObj = new Date(dateObj.getFullYear(), dateObj.getMonth() - 1, 1)
          this.filters.month = newDateObj.getMonth() + 1
          this.filters.year = newDateObj.getFullYear()
        } break
        default: {
          invalidPeriod = true
        } break
      }

      if(!invalidPeriod) {
        this.applyFilters()
      }
    },
    formatDate(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0')
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0') //January is 0!
      let yyyy = dateObj.getFullYear()
      
      let formattedDate = yyyy + '-' + mm + '-' + dd
      return formattedDate
    },
    calculateStats() {
      this.total_stats = {
        leads: 0,
        total_cost: 0,
        commission: 0,
        approved_commission: 0,
        approved_commission_percentage: 0,
        net_profit: 0,
      }

      for (var i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        this.total_stats.leads += parseInt(obj.leads)
        this.total_stats.total_cost += parseFloat(obj.total_cost)
        this.total_stats.commission += parseFloat(obj.commission)
        this.total_stats.approved_commission += parseFloat(obj.approved_commission)
        this.total_stats.net_profit += parseFloat(obj.net_profit)
      }
      this.total_stats.approved_commission_percentage = (this.total_stats.commission > 0) ? ((this.total_stats.approved_commission / this.total_stats.commission)*100) : 0

      // Format calculated stats
      this.total_stats.total_cost = this.total_stats.total_cost.toFixed(2)
      this.total_stats.commission = this.total_stats.commission.toFixed(2)
      this.total_stats.approved_commission = this.total_stats.approved_commission.toFixed(2)
      this.total_stats.approved_commission_percentage = this.total_stats.approved_commission_percentage.toFixed(2)
      this.total_stats.net_profit = this.total_stats.net_profit.toFixed(2)
    },
    showEditInput(id) {
      let index = this.getObjectIndexById(id)

      this.isEditing = true
      this.editingValue = this.objects[index].approved_commission
      this.editingObjectId = id
      this.editingObject = this.objects[index]
    },
    edit() {
      this.$vs.loading()

      let index = this.getObjectIndexById(this.editingObjectId)

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        month: this.editingObject.month,
        year: this.editingObject.year,
        approved_commission: this.editingValue,
      }
      this.$http.put('/api/v1/reports/advertisers/' + this.editingObject.user.id, data, { headers })
        .then((response) => {
          this.objects[index].approved_commission = this.editingValue
          this.isEditing = false
          this.$vs.loading.close()
          this.retrieveObjects()
          this.$vs.notify({
            title: 'Success',
            text: 'Approved commission updated',
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
  },
  created() {
    this.yearsOptions = []
    let dateObj = new Date()
    let currentYear = dateObj.getFullYear()
    for (let i = 10; i >= 0; i--) {
      this.yearsOptions.push({
        id: currentYear - i,
        label: currentYear - i,
      })
    }

    this.resetFilters()
  },
  mounted () {
  }
}

</script>
