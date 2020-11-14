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

        <!-- TODO: Fix this
        <div class="vx-row mb-2">
            <div class="vx-col w-1/3">
            </div>
            <div class="vx-col w-1/3 text-center">
                <vs-select
                    autocomplete
                    @input="onFbAccountSwitch"
                    label="Switch Fb Account"
                    v-model="fbAccountSwitch"
                    >
                    <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in fbAccountSwitchOptions" />
                </vs-select>
            </div>
            <div class="vx-col w-1/3">
            </div>
        </div>
        -->

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
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
                <div class="vx-col md:w-1/3 w-full">
                    <label>Status</label>
                    <vs-select autocomplete v-model="filters.status" class="w-full select-large">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in statusOptions" class="w-full" />
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

        <div v-if="view == 'normal'">
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
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <vs-th sort-key="spent">Spent</vs-th>
                    <vs-th sort-key="result">Result</vs-th>
                    <vs-th sort-key="cpa">CPA</vs-th>
                    <vs-th sort-key="cr">CR</vs-th>
                    <vs-th sort-key="cpm">CPM</vs-th>
                    <vs-th sort-key="ctr">CTR</vs-th>
                    <vs-th sort-key="cpc">CPC</vs-th>
                    <vs-th sort-key="daily_budget">Budget</vs-th>
                    <vs-th>Actions</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td :data="data[indextr].name">
                            {{data[indextr].name}}
                        </vs-td>

                        <vs-td :data="data[indextr].status">
                            <vs-switch color="success" v-model="data[indextr].status == 'ACTIVE'" vs-icon-off="pause" vs-icon-on="done" @input="switchCampaignStatus(indextr)">
                                <span slot="on">Active</span>
                                <span slot="off">Paused</span>
                            </vs-switch>
                        </vs-td>

                        <vs-td :data="data[indextr].spent">
                            € {{data[indextr].spent.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].result">
                            {{data[indextr].result}}
                        </vs-td>

                        <vs-td :data="data[indextr].cpa">
                            € {{data[indextr].cpa.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].cr">
                            {{data[indextr].cr.toFixed(2)}}%
                        </vs-td>

                        <vs-td :data="data[indextr].cpm">
                            € {{data[indextr].cpm.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].ctr">
                            {{data[indextr].ctr.toFixed(2)}}%
                        </vs-td>

                        <vs-td :data="data[indextr].cpc">
                            € {{data[indextr].cpc.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].daily_budget">
                            € {{data[indextr].daily_budget.toFixed(2)}}
                        </vs-td>

                        <vs-td>
                            <div class="flex items-center">
                                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-dollar-sign"></vs-button>
                                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-minus-circle"></vs-button>
                            </div>
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>
                        € {{total_stats.spent.toFixed(2)}}
                    </vs-td>
                    <vs-td>
                        {{total_stats.result}}
                    </vs-td>
                    <vs-td>
                        € {{total_stats.cpa.toFixed(2)}}
                    </vs-td>
                    <vs-td>
                        {{total_stats.cr.toFixed(2)}}%
                    </vs-td>
                    <vs-td>
                        € {{total_stats.cpm.toFixed(2)}}
                    </vs-td>
                    <vs-td>
                        {{total_stats.ctr.toFixed(2)}}%
                    </vs-td>
                    <vs-td>
                        € {{total_stats.cpc.toFixed(2)}}
                    </vs-td>
                    <vs-td>
                        € {{total_stats.daily_budget.toFixed(2)}}
                    </vs-td>
                    <vs-td></vs-td>
                    <!-- END TOTAL STATS-->
                </template>
            </vs-table>
        </div>

    </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import VueApexCharts from 'vue-apexcharts'

export default {
  components: {
    'v-select': vSelect,
    VueApexCharts,
  },
  props: {
    fb_account_id: Number,
  },
  data () {
    return {
      view: 'normal', // TODO: Change campaigns/ads

      fb_account: {},
      objects: [],
      selected: [],
      deleted: [],

      maxItems: 20,

      editingObject: -1,

      campaigns: [],
      networks: [],
      products: [],
      traffic_sources: [],

      statusOptions: ['ACTIVE', 'PAUSED'],

      total_stats: {
        spent: 0,
        result: 0,
        cpa: 0,
        impressions: 0,
        clicks: 0,
        cr: 0,
        cpm: 0,
        ctr: 0,
        cpc: 0,
        daily_budget: 0,
      },
      dateFormat: 'yyyy-MM-dd',
      filters: {
        date_from: '',
        date_to: '',
        status: '',
      },
      showFilters: false,
      showNotesPopup: false,
      isEditingNote: false,
      editingNoteId: -1,
      editingNoteIndex: -1,
      editingNoteBody: '',
      creatingNoteBody: '',

      // TODO: Fb Account switch
      fbAccountSwitchOptions: [],
      fbAccountSwitch: 1,
    }
  },
  watch: {
    fb_account_id: function(newVal, oldVal) {
      this.applyFilters()
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
    canViewStats() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin') ||
              this.$store.getters.userHasRole('mediabuyer'))
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
    calculateStats() {
      let spent = 0
      let result = 0
      let cpa = 0
      let impressions = 0
      let clicks = 0
      let cr = 0
      let cpm = 0
      let ctr = 0
      let cpc = 0
      let daily_budget = 0

      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]
        spent += obj.spent
        result += obj.result
        impressions += obj.impressions
        clicks += obj.clicks
        daily_budget += obj.daily_budget
      }
      cpa = (result > 0) ? (spent/result) : spent
      cr = (clicks > 0) ? ((result/clicks)*100) : ((result > 0) ? 9999.99 : 0)
      cpm = (impressions > 0) ? ((spent*1000)/impressions) : (spent*1000)
      ctr = (impressions > 0) ? ((clicks/impressions)*100) : ((clicks > 0) ? 9999.99 : 0)
      cpc = (clicks > 0) ? (spent/clicks) : spent

      this.total_stats = {
        spent: spent,
        result: result,
        cpa: cpa,
        impressions: impressions,
        clicks: clicks,
        cr: cr,
        cpm: cpm,
        ctr: ctr,
        cpc: cpc,
        daily_budget: daily_budget,
      }
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        status: '',
      }
      this.filterDatesShortcut('this_month')
    },
    applyFilters() {
      this.$vs.loading()

      this.selected = []

      let timeRange = {}
      if (this.filters.date_from != '') {
        timeRange['since'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        timeRange['until'] = this.filters.date_to
      }
      timeRange = JSON.stringify(timeRange)
      
      let fields = 'account_id,name'
      fields += ',campaigns.time_range(' + timeRange + '){id,name,status,result,spent,impressions,clicks,bid_strategy,daily_budget}'

      // Retrieve campaigns
      this.objects = []
      this.$http.get('https://graph.facebook.com/v9.0/me/adaccounts?access_token=' + this.fb_account.token + '&fields=' + fields)
        .then((response) => {
          for (let j = 0; j < response.data.data.length; j++) {
            let adAccount = response.data.data[j]

            if (adAccount.hasOwnProperty('campaigns') && adAccount.campaigns.hasOwnProperty('data') && Array.isArray(adAccount.campaigns.data)) {
              for (let i = 0; i < adAccount.campaigns.data.length; i++) {
                let campaign = adAccount.campaigns.data[i]

                if (this.filters.status != '') {
                  if (campaign.status != this.filters.status) {
                    continue
                  }
                }
                
                let spent = parseFloat(campaign.spent/100)
                let cpa = (campaign.result > 0) ? (spent/campaign.result) : spent
                let cr = (campaign.clicks > 0) ? ((campaign.result/campaign.clicks)*100) : ((campaign.result > 0) ? 9999.99 : 0)
                let cpm = (campaign.impressions > 0) ? ((spent*1000)/campaign.impressions) : (spent*1000)
                let ctr = (campaign.impressions > 0) ? ((campaign.clicks/campaign.impressions)*100) : ((campaign.clicks > 0) ? 9999.99 : 0)
                let cpc = (campaign.clicks > 0) ? (spent/campaign.clicks) : spent

                let daily_budget = 0
                let bid_strategy = ''
                if (campaign.hasOwnProperty('daily_budget')) {
                  daily_budget = parseFloat(parseInt(campaign.daily_budget) / 100)
                }
                if (campaign.hasOwnProperty('bid_strategy')) {
                  bid_strategy = campaign.bid_strategy
                }

                this.objects.push({
                  account_id: campaign.account_id,
                  id: campaign.id,
                  name: campaign.name,
                  status: campaign.status,
                  spent: spent,
                  result: campaign.result,
                  cpa: cpa,
                  impressions: campaign.impressions,
                  clicks: campaign.clicks,
                  cr: cr,
                  cpm: cpm,
                  ctr: ctr,
                  cpc: cpc,
                  daily_budget: daily_budget,
                  bid_strategy: bid_strategy,
                })
              }
            }
          }
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
    filterDatesShortcut(period) {
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
    formatDateTime(dateObj) {
      let formattedDate = this.formatDate(dateObj)

      let hh = String(dateObj.getHours()).padStart(2, '0')
      let ii = String(dateObj.getMinutes()).padStart(2, '0')
      let ss = String(dateObj.getSeconds()).padStart(2, '0')

      let formattedTime = hh + ':' + ii + ':' + ss
      return formattedDate + ' ' + formattedTime
    },
    switchCampaignStatus(index) {
      let campaign = this.objects[index]

      let oldStatus = campaign.status
      let status = (campaign.status == 'ACTIVE') ? 'PAUSED' : 'ACTIVE'
      this.objects[index].status = status

      // Change fb campaign status
      this.$http.post('https://graph.facebook.com/v9.0/' + campaign.id + '?status=' + campaign.status + '&access_token=' + this.fb_account.token)
        .then((response) => {
          if (response.data.success) {
            this.objects[index].status = status
          } else {
            this.objects[index].status = oldStatus
          }
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Campaign status updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
        })
        .catch((error) => {
          this.objects[index].status = oldStatus
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
    onFbAccountSwitch(value) {
      this.$parent.switchFbAccount(value)
    },
  },
  created() {
    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve Fb Accounts
    this.$http.get('/api/3rd/fb-accounts', { headers })
      .then((response) => {
        this.fbAccountSwitchOptions = []
        for (let i = 0; i < response.data.data.length; i++) {
          let obj = response.data.data[i]
          this.fbAccountSwitchOptions.push({
            label: obj.name,
            value: obj.id,
          });
          if (this.fb_account_id == obj.id) {
            this.fb_account = obj
          }
        }
        this.fbAccountSwitch = this.fb_account.id
        this.resetFilters()
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
  mounted () {
  }
}

</script>

<style>
.note {
    border: 1px solid gray;
    background-color: #eaeaea;
    border-radius: 0.5rem;
}
</style>
