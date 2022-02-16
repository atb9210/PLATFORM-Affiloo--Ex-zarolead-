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
                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="refreshMonitorsReports(selectedMonitors)" :disabled="selectedMonitors.length <= 0"></vs-button>
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

        <vue-context ref="monitor_context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.monitor_context_menu.close();goToActionsLog(data.id);" class="flex items-center text-sm">
                <feather-icon icon="ListIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">View Actions Log</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.monitor_context_menu.close();showMonitorStrategyPopup(data);" class="flex items-center text-sm">
                <feather-icon icon="TargetIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit Strategy</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.monitor_context_menu.close();runStrategy(data);" class="flex items-center text-sm">
                <feather-icon icon="PlayIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Run Strategy</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.monitor_context_menu.close();showMonitorCampaignsPopup(data);" class="flex items-center text-sm">
                <feather-icon icon="LayoutIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit Campaigns</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.monitor_context_menu.close();confirmSingleDeletePopup(data);" class="flex items-center text-sm">
                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Delete</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.monitor_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

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

            <!-- Monitors table -->
            <vs-table v-if="view == 'monitors'" stripe multiple v-model="selectedMonitors" :max-items="maxItems" pagination search :data="monitors" :currentPage="currentPage" @change-page="onPageChanged">

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
                            <div @contextmenu.prevent="$refs.monitor_context_menu.open($event, data[indextr])">
                                <vs-chip transparent>
                                    <vs-avatar icon-pack="feather" icon="icon-user" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </div>
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

        <!-- Monitor strategy popup -->
        <vs-popup title="Monitor Strategy" :active.sync="showPopup['edit_monitor_strategy']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{monitor.name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Stop Bid</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-checkbox v-model="monitor.strategy['bid.stop'].enabled">{{monitor.strategy['bid.stop'].enabled ? 'Enabled' : 'Disabled'}}</vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Today Spent</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" class="w-full" icon-pack="feather" icon="icon-chevron-right" icon-no-border v-model="monitor.strategy['bid.stop'].spent" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Lifetime Spent</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" class="w-full" icon-pack="feather" icon="icon-chevron-right" icon-no-border v-model="monitor.strategy['bid.stop'].lifetime_spent" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Conversions (number)</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="1" min="0" class="w-full" icon-pack="feather" icon="icon-chevron-left" icon-no-border v-model="monitor.strategy['bid.stop'].conversions" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Times per day (max)</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="1" min="0" class="w-full" icon-pack="feather" icon="icon-play" icon-no-border v-model="monitor.strategy['bid.stop'].times_per_day" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Time check (TODO: Does not work for now)</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <my-select v-model="monitor.strategy['bid.stop'].time_check" :options="monitorStrategyTimeCheckOptions" class="w-full select-large" :autocomplete="true" />
                </div>
            </div>

            <hr>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Down Bid</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-checkbox v-model="monitor.strategy['bid.down'].enabled">{{monitor.strategy['bid.down'].enabled ? 'Enabled' : 'Disabled'}}</vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Bid variation</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="monitor.strategy['bid.down'].variation" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Minimum Bid</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="monitor.strategy['bid.down'].min_bid" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Today Spent</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" class="w-full" icon-pack="feather" icon="icon-chevron-right" icon-no-border v-model="monitor.strategy['bid.down'].spent" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Lifetime Spent</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" class="w-full" icon-pack="feather" icon="icon-chevron-right" icon-no-border v-model="monitor.strategy['bid.down'].lifetime_spent" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Conversions (number)</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="1" min="0" class="w-full" icon-pack="feather" icon="icon-chevron-left" icon-no-border v-model="monitor.strategy['bid.down'].conversions" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Times per day (max)</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="1" min="0" class="w-full" icon-pack="feather" icon="icon-play" icon-no-border v-model="monitor.strategy['bid.down'].times_per_day" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Time check (TODO: Does not work for now)</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <my-select v-model="monitor.strategy['bid.down'].time_check" :options="monitorStrategyTimeCheckOptions" class="w-full select-large" :autocomplete="true" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateMonitorStrategy(monitor)" color="primary" type="filled" :disabled="!isValidForm('edit_monitor_strategy')">Update strategy</vs-button>
            </div>
        </vs-popup>

        <!-- Edit monitor campaigns -->
        <vs-popup title="Monitor campaigns" :active.sync="showPopup['edit_monitor_campaigns']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3 w-full font-bold">
                    <div class="vx-col md:w-1/6 w-full">
                    </div>
                    <div class="vx-col md:w-5/6 w-full">
                        Name
                    </div>
                </div>
                <div class="vx-row mb-3 w-full text-sm" v-for="(campaign, cIndex) in campaigns" :key="'editCampaigns' + cIndex">
                    <div class="vx-col md:w-1/6 w-full">
                        <vs-checkbox v-model="campaigns[cIndex].selected" @change="updateMonitorCampaigns(monitor, campaigns)" />
                    </div>
                    <div class="vx-col md:w-5/6 w-full">
                        {{campaign.name}}
                    </div>
                </div>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'

import Facebook from 'facebook'

export default {
  name: 'FacebookMonitorsTable',
  components: {
    VueContext,
  },
  data () {
    return {
      maxItems: 20,

      view: 'monitors',

      monitors: [],
      selectedMonitors: [],

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        advertiser: '',
        form: '',
      },

      showPopup: {
        'edit_monitor_strategy': false,
        'edit_monitor_campaigns': false,
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

      monitor: {},
      monitorStrategyTimeCheckOptions: [
        'every_thirty_minutes',
        'hourly',
        'every_two_hours',
      ],

      campaigns: [],
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
      if (this.view === 'monitors') {
        return this.monitors
      } else if (this.view === 'actions') {
        return this.actions
      }
      return []
    },
    selectedObjects() {
      if (this.view === 'monitors') {
        return this.selectedMonitors
      } else if (this.view === 'actions') {
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

      this.selectedMonitors = []
      this.monitors = []

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

      this.monitors = await this.facebook.fetchMonitors(filters)

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
    confirmSingleDeletePopup(monitor) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Delete confirmation',
        text: 'Are you sure you want to delete the monitor "' + monitor.name + '"?',
        accept: function () {
          this.deleteMonitor(monitor)
        }.bind(this),
      })
    },
    async deleteMonitor(monitor) {
      this.$vs.loading()

      const result = await this.facebook.deleteMonitor(monitor.id)

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Monitor deleted',
          iconPack: 'feather',
          icon: 'icon-trash',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot delete the monitor',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    confirmDeletePopup() {
      if (this.selectedMonitors.length == 0) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Delete confirmation',
        text: 'Are you sure you want to delete ' + this.selectedMonitors.length + ' object(s)?',
        accept: this.deleteSelected
      })
    },
    async deleteSelected() {
      if (this.selectedMonitors.length == 0) return

      this.$vs.loading()

      for (let i = 0; i < this.selectedMonitors.length; i++) {
        await this.facebook.deleteMonitor(this.selectedMonitors[i].id)
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

      for (let i = 0; i < this.monitors.length; i++) {
        let obj = this.monitors[i]

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
        for (let i = 0; i < this.selectedMonitors.length; i++) {
          let m = this.selectedMonitors[i]
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
    isValidForm(type) {
      if (type === 'edit_monitor_strategy') {
        return true
      }
      return false
    },
    showMonitorStrategyPopup(monitor) {
      this.monitor = monitor
      this.showPopup['edit_monitor_strategy'] = true
    },
    async updateMonitorStrategy(monitor) {
      this.$vs.loading()

      // NOTE: bid.down variation must always be negative because it is a
      // decrease of the bid amount
      if (monitor.strategy['bid.down'].variation > 0) {
        monitor.strategy['bid.down'].variation *= -1
      }

      let params = {
        strategy: JSON.stringify(monitor.strategy),
      }
      const updatedMonitor = await this.facebook.updateMonitor(monitor.id, params)

      let result = (updatedMonitor !== null)
      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Monitor strategy updated',
          iconPack: 'feather',
          icon: 'icon-target',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the monitor strategy',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.showPopup['edit_monitor_strategy'] = false

      this.$vs.loading.close()

      await this.applyFilters()
    },
    async runStrategy(monitor) {
      this.$vs.loading()

      let result = false
      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        const response = await this.$http.get('/test/monitor/' + monitor.id + '/run', { headers })
        result = (response.data === 'true' || response.data === true)
      } catch (error) {
        // Nothing
      }

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Monitor strategy applied',
          iconPack: 'feather',
          icon: 'icon-facebook',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot run monitor strategy',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    goToActionsLog(monitorId) {
      this.$router.push({name: 'facebook-monitor-actions', params: {facebook_monitor_id: monitorId}})
        .catch(error => {
          this.$router.push({name: 'facebook-monitor-actions', params: {facebook_monitor_id: monitorId}})
        })
    },
    async showMonitorCampaignsPopup(monitor) {
      this.monitor = monitor

      let adAccountId = null
      let campaignIds = null
      for (const [key, value] of Object.entries(monitor.campaigns)) {
        adAccountId = key
        campaignIds = value
        break
      }
      if (adAccountId === null) {
        return
      }
      if (campaignIds === null) {
        campaignIds = []
      }

      this.$vs.loading()

      let advertiser = await this.facebook.fetchAdvertiser(monitor.advertiser.id)
      if (advertiser === null) {
        // TODO: Log
      }
      this.facebook.setAccessToken(advertiser.access_token)

      this.campaigns = await this.facebook.fetchCampaigns(adAccountId)
      for (let i = 0; i < this.campaigns.length; i++) {
        let campaign = this.campaigns[i]

        campaign.selected = false
        if (campaignIds.includes(campaign.id)) {
          campaign.selected = true
        }

        this.campaigns[i] = campaign
      }

      this.$vs.loading.close()

      this.showPopup['edit_monitor_campaigns'] = true
    },
    async updateMonitorCampaigns(monitor, campaigns) {
      let adAccountId = null
      for (const [key, value] of Object.entries(monitor.campaigns)) {
        adAccountId = key
        break
      }
      if (adAccountId === null) {
        return
      }

      this.$vs.loading()

      let campaignIds = []
      for (let i = 0; i < this.campaigns.length; i++) {
        let campaign = this.campaigns[i]
        if (campaign.selected) {
          campaignIds.push(campaign.id)
        }
      }
      let paramsCampaigns = {}
      paramsCampaigns[adAccountId] = campaignIds

      let params = {
        campaigns: JSON.stringify(paramsCampaigns)
      }
      const result = await this.facebook.updateMonitor(monitor.id, params)

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Monitor campaigns updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update monitor campaigns',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
  },
  async created() {
    this.facebook = new Facebook(localStorage.getItem('accessToken'))

    this.monitor.strategy = this.facebook.getMonitorDefaultStrategy()

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
