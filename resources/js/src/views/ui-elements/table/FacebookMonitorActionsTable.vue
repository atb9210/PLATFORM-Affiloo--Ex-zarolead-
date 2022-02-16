<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Facebook Monitor Actions" code-toggler>

        <div class="vx-row mb-2" v-if="view == 'actions'">
            <div class="vx-col w-1/3">
            </div>
            <div class="vx-col w-1/3 text-center">
                <my-select
                    :autocomplete="true"
                    @input="onMonitorSwitch"
                    label="Switch Monitor"
                    v-model="monitorSwitch"
                    :options="monitorSwitchOptions" />
            </div>
            <div class="vx-col w-1/3">
            </div>
        </div>

        <p class="flex items-center mb-4">
            <vx-tooltip text="Go Back" position="top">
                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="goToMonitorsPage()"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Filters" position="top">
                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
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
                <div class="vx-col w-1/2">
                    <label>Action Type</label>
                    <my-select v-model="filters.action_type" :options="actionTypes" class="w-full select-large" :autocomplete="true" />
                </div>
                <div class="vx-col w-1/2">
                    <label>Campaign Id</label>
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="filters.campaign" />
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

            <!-- Actions table -->
            <vs-table v-if="view == 'actions'" stripe multiple v-model="selectedActions" :max-items="maxItems" pagination search :data="actions" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="id">Id</vs-th>
                    <vs-th sort-key="created_at">Date</vs-th>
                    <vs-th sort-key="campaign_id">Campaign</vs-th>
                    <vs-th sort-key="action_type">Action</vs-th>
                    <vs-th sort-key="spent">Spent</vs-th>
                    <vs-th sort-key="conversions">Conversions</vs-th>
                    <vs-th sort-key="conversions_value">Commissions</vs-th>
                    <vs-th sort-key="end_roi">ROI</vs-th>
                    <vs-th sort-key="end_roi_campaign">ROI Camp.</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td :data="data[indextr].id">
                            {{data[indextr].id}}
                        </vs-td>

                        <vs-td :data="data[indextr].created_at">
                            {{formatDateTime(new Date(data[indextr].created_at))}}
                        </vs-td>

                        <vs-td :data="data[indextr].campaign_id">
                            {{data[indextr].campaign_id}}
                        </vs-td>

                        <vs-td :data="data[indextr].action_type">
                            {{data[indextr].action_type}}
                        </vs-td>

                        <vs-td :data="data[indextr].spent">
                            € {{data[indextr].spent.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].conversions">
                            {{data[indextr].conversions}}
                        </vs-td>

                        <vs-td :data="data[indextr].conversions_value">
                            € {{data[indextr].conversions_value.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].end_roi">
                            € {{data[indextr].end_roi.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].end_roi_campaign">
                            € {{data[indextr].end_roi_campaign.toFixed(2)}}
                        </vs-td>

                    </vs-tr>

                </template>
            </vs-table>
        </div>

    </vx-card>
</template>

<script>
import Facebook from 'facebook'

export default {
  name: 'FacebookMonitorActionsTable',
  components: {
  },
  props: {
    monitor_id: Number,
  },
  data () {
    return {
      maxItems: 20,

      view: 'actions',

      actions: [],
      selectedActions: [],

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        action_type: '',
        campaign: '',
      },

      monitors: [],
      monitor: {},

      monitorSwitch: '',
      monitorSwitchOptions: [],

      actionTypes: [
        'bid.stop',
        'bid.down',
      ],
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
      if (this.view === 'actions') {
        return this.actions
      }
      return []
    },
    selectedObjects() {
      if (this.view === 'actions') {
        return this.selectedActions
      }
      return []
    },
  },
  methods: {
    getObjectIndexById(id, objects) {
      var index = -1;
      objects.forEach((obj, i) => {
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

      this.selectedActions = []
      this.actions = []

      let filters = {}
      if (this.filters.date_from != '') {
        filters.filter_date_from = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        filters.filter_date_to = this.filters.date_to
      }
      if (this.filters.action_type != '') {
        filters.filter_action_type = this.filters.action_type
      }
      if (this.filters.campaign != '') {
        filters.filter_campaign = this.filters.campaign
      }

      this.actions = await this.facebook.fetchMonitorActions(this.monitor.id, filters)

      this.$vs.loading.close()
    },
    async resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        action_type: '',
        campaign: '',
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
    goToMonitorsPage() {
      this.$router.push({name: 'facebook-monitors'})
        .catch(error => {
          this.$router.push({name: 'facebook-monitors'})
        })
    },
    async onMonitorSwitch() {
      this.$vs.loading()
      this.monitor_id = this.monitorSwitch.id
      
      this.$vs.loading.close()
    },
    onMonitorSwitch(value) {
      this.$parent.switchMonitor(value)
    },
  },
  async created() {
    this.facebook = new Facebook(localStorage.getItem('accessToken'))

    this.$vs.loading()
    this.monitors = await this.facebook.fetchMonitors({ reports: 0 })
    this.$vs.loading.close()

    let currentMonitor = null
    for (let i = 0; i < this.monitors.length; i++) {
      let monitor = this.monitors[i]
      this.monitorSwitchOptions.push({
        label: monitor.name,
        value: monitor.id,
      })
      if (monitor.id == this.monitor_id) {
        currentMonitor = this.monitors[i].id
      }
    }
    if (currentMonitor !== null) {
      this.monitorSwitch = currentMonitor
    }
    this.monitor = await this.facebook.fetchMonitor(this.monitor_id)
    if (this.monitor === null) {
      this.goToMonitorsPage()
    }

    await this.filterDatesShortcut('today')
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
