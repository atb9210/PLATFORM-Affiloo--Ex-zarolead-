<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Facebook Monitors" code-toggler>

        <p class="flex items-center mb-4">
            <vx-tooltip text="Filters" position="top">
                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Refresh reports" position="top">
                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="refreshMonitorsReports(selected)" :disabled="selected.length <= 0"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Delete Monitor" position="top">
                <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>
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
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col md:w-1/2 w-full">
                    <label>Advertiser</label>
                    <my-select v-model="filters.advertiser" :options="advertisers" :autocomplete="true" class="w-full select-large" />
                </div>
                <div class="vx-col md:w-1/2 w-full">
                    <label>Form</label>
                    <my-select v-model="filters.form" :options="forms" :autocomplete="true" class="w-full select-large" />
                </div>
            </div>
            <div class="vx-row mt-6">
                <div class="vx-col w-full">
                    <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
                    <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
                </div>
            </div>
        </div>

        <div>
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
                    <vs-th sort-key="id">Id</vs-th>
                    <vs-th sort-key="advertiser.name">Advertiser</vs-th>
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="numOfCampaigns">Campaigns</vs-th>
                    <vs-th sort-key="views">Views</vs-th>
                    <vs-th sort-key="spent">Spent</vs-th>
                    <vs-th sort-key="averageSpent">S/C</vs-th>
                    <vs-th sort-key="conversions">Conversions</vs-th>
                    <vs-th sort-key="conversions_value">Commissions</vs-th>
                    <vs-th sort-key="profit">Profit</vs-th>
                    <vs-th sort-key="roi">ROI</vs-th>
                    <vs-th sort-key="cpc">CPC</vs-th>
                    <vs-th sort-key="epc">EPC</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td :data="data[indextr].id">
                            {{data[indextr].id}}
                        </vs-td>

                        <vs-td :data="data[indextr].advertiser.name">
                            {{data[indextr].advertiser.name}}
                        </vs-td>

                        <vs-td :data="data[indextr].name">
                            {{data[indextr].name}}
                        </vs-td>

                        <vs-td :data="data[indextr].numOfCampaigns">
                            {{data[indextr].numOfCampaigns}}
                        </vs-td>

                        <vs-td :data="data[indextr].views">
                            {{data[indextr].views}}
                        </vs-td>

                        <vs-td :data="data[indextr].spent">
                            € {{data[indextr].spent.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].averageSpent">
                            € {{data[indextr].averageSpent.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].conversions">
                            {{data[indextr].conversions}}
                        </vs-td>

                        <vs-td :data="data[indextr].conversions_value">
                            € {{data[indextr].conversions_value.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].profit">
                            € {{data[indextr].profit.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].roi">
                            {{data[indextr].roi.toFixed(2)}}%
                        </vs-td>

                        <vs-td :data="data[indextr].cpc">
                            € {{data[indextr].cpc.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].epc">
                            € {{data[indextr].epc.toFixed(2)}}
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{data.length}}</vs-td>
                    <vs-td class="column-no-padding">
                        {{totalStats.numOfCampaigns}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{totalStats.views}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{totalStats.spent.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{totalStats.averageSpent.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{totalStats.conversions}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{totalStats.conversions_value.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{totalStats.profit.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{totalStats.roi.toFixed(2)}}%
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{totalStats.cpc.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{totalStats.epc.toFixed(2)}}
                    </vs-td>
                    <!-- END TOTAL STATS-->

                </template>
            </vs-table>
        </div>

    </vx-card>
</template>

<script>
import Facebook from 'facebook'

export default {
  data () {
    return {
      maxItems: 20,

      objects: [],
      selected: [],

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        advertiser: '',
        form: '',
      },

      totalStats: {
        numOfCampaigns: 0,
        views: 0,
        spent: 0.0,
        averageSpent: 0.0,
        conversions: 0,
        conversions_value: 0.0,
        profit: 0.0,
        roi: 0.0,
        cpc: 0.0,
        epc: 0.0,
      },

      advertisers: [],
      forms: [],
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
    onPageChanged(val) {
      this.currentPage = val
    },
    async applyFilters() {
      this.$vs.loading()

      this.selected = []
      this.objects = []

      let filters = {}
      if (this.filters.advertiser != '') {
        filters.filter_advertiser = this.filters.advertiser.id
      }
      if (this.filters.form != '') {
        filters.filter_form = this.filters.form.id
      }
      if (this.filters.date_from != '') {
        filters.filter_date_from = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        filters.filter_date_to = this.filters.date_to
      }
      filters.reports = 1

      this.objects = await this.facebook.fetchMonitors(filters)

      this.calculateStats()

      this.$vs.loading.close()
    },
    async resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        advertiser: '',
        form: '',
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

      await this.applyFilters()
    },
    formatDate(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0')
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0') //January is 0!
      let yyyy = dateObj.getFullYear()
      
      let formattedDate = yyyy + '-' + mm + '-' + dd
      return formattedDate
    },
    formatDateTime(dateObj) {
      let formattedDate = this.formatDate(dateObj)

      let hh = String(dateObj.getHours()).padStart(2, '0')
      let ii = String(dateObj.getMinutes()).padStart(2, '0')
      let ss = String(dateObj.getSeconds()).padStart(2, '0')

      let formattedTime = hh + ':' + ii + ':' + ss
      return formattedDate + ' ' + formattedTime
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
      if (this.selected.length == 0) return

      this.$vs.loading()

      for (let i = 0; i < this.selected.length; i++) {
        await this.facebook.deleteMonitor(this.selected[i].id)
      }

      this.$vs.loading.close()

      await this.applyFilters()
    },
    async fetchForms() {
      let objects = []

      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        const response = await this.$http.get('/api/v1/forms', { headers })
        objects = response.data.data
      } catch (error) {
        // Nothing
      }

      return objects
    },
    calculateStats() {
      let numOfCampaigns = 0
      let views = 0
      let spent = 0
      let averageSpent = 0.0
      let conversions = 0
      let conversions_value = 0
      let profit = 0.0
      let roi = 0.0
      let cpc = 0.0
      let epc = 0.0

      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        numOfCampaigns += obj.numOfCampaigns
        views += obj.views
        spent += obj.spent
        conversions += obj.conversions
        conversions_value += obj.conversions_value
        profit += obj.profit
      }
      roi = (spent > 0) ? (((profit/spent))*100) : 0

      averageSpent = ((numOfCampaigns > 0) ? (spent/numOfCampaigns) : 0.0)
      cpc = ((views > 0) ? (spent/views) : 0.0)
      epc = ((views > 0) ? (conversions_value/views) : 0.0)

      this.totalStats = {
        numOfCampaigns: numOfCampaigns,
        views: views,
        spent: spent,
        averageSpent: averageSpent,
        conversions: conversions,
        conversions_value: conversions_value,
        profit: profit,
        roi: roi,
        cpc: cpc,
        epc: epc,
      }
    },
    async refreshMonitorsReports(monitors) {
      this.$vs.loading()

      let result = false
      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        for (let i = 0; i < this.selected.length; i++) {
          let m = this.selected[i]
          let yesterday = new Date(new Date().setDate(new Date().getDate()-1))
          let day = this.formatDate(yesterday)

          let params = {
            monitor: m.id,
            day: day,
          }
          const response = await this.$http.get('/test/monitor', { headers, params })
          let res = (response.data === 'true' || response.data === true)
          if (!res) {
            result = false
            break
          }
        }
        result = true
      } catch (error) {
        // Nothing
      }

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Monitor(s) reports updated',
          iconPack: 'feather',
          icon: 'icon-facebook',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update monitors reports',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()

      await this.applyFilters()
    },
  },
  async created() {
    this.facebook = new Facebook(localStorage.getItem('accessToken'))
    await this.resetFilters()

    this.$vs.loading()

    this.advertisers = await this.facebook.fetchAdvertisers()

    // TODO: Fetch this now or when trying to create a new monitor?
    this.forms = await this.fetchForms()

    this.$vs.loading.close()
  },
}

</script>

<style scoped>
.column-no-padding {
  padding-top: 0px !important;
  padding-bottom: 0px !important;
  white-space: nowrap;
}
.disabled-object {
  color: #363636 !important;
}
</style>
