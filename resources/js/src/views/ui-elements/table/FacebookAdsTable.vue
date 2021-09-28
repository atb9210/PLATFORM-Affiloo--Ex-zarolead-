<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Facebook Ads" code-toggler>

        <div v-if="!loggedIn" class="h-48 flex flex-wrap content-center items-center text-center">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-facebook" @click="login"> Login with Facebook</vs-button>
        </div>

        <div v-else>

            <p class="flex items-center mb-4">
                <span class="btn-group flex items-center mr-2">
                    <vs-button @click="changeView('ad_accounts')" :color="(view == 'ad_accounts') ? 'success' : 'primary'">Ad accounts</vs-button>
                    <vs-button @click="changeView('campaigns')" :color="(view == 'campaigns') ? 'success' : 'primary'">Campaigns</vs-button>
                    <vs-button @click="changeView('adsets')" :color="(view == 'adsets') ? 'success' : 'primary'">Adsets</vs-button>
                    <vs-button @click="changeView('ads')" :color="(view == 'ads') ? 'success' : 'primary'">Ads</vs-button>
                </span>
            </p>
            <vue-context ref="ad_account_context_menu" v-slot="{ data }">
                <li>
                  <a href="#" @click="$refs.ad_account_context_menu.close();changeView('campaigns', data.id);" class="flex items-center text-sm">
                    <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Show Campaigns</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.ad_account_context_menu.close()" class="flex items-center text-sm">
                    <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Close</span>
                  </a>
                </li>
            </vue-context>

            <vue-context ref="campaign_context_menu" v-slot="{ data }">
                <li>
                  <a href="#" @click="$refs.campaign_context_menu.close();changeView('adsets', data.id);" class="flex items-center text-sm">
                    <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Show Adsets</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.campaign_context_menu.close()" class="flex items-center text-sm">
                    <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Close</span>
                  </a>
                </li>
            </vue-context>

            <vue-context ref="adset_context_menu" v-slot="{ data }">
                <li>
                  <a href="#" @click="$refs.adset_context_menu.close();changeView('ads', data.id);" class="flex items-center text-sm">
                    <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Show Ads</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.adset_context_menu.close()" class="flex items-center text-sm">
                    <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Close</span>
                  </a>
                </li>
            </vue-context>

            <vue-context ref="ad_context_menu" v-slot="{ data }">
                <li>
                  <a href="#" @click="$refs.ad_context_menu.close();viewAd(data);" class="flex items-center text-sm">
                    <feather-icon icon="EyeIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">View Ad</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.ad_context_menu.close()" class="flex items-center text-sm">
                    <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Close</span>
                  </a>
                </li>
            </vue-context>

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

            <!-- Ad accounts table -->
            <vs-table v-if="view == 'ad_accounts'" stripe multiple v-model="selectedAdAccounts" :max-items="maxItems" pagination search :data="adAccounts" :currentPage="currentPage" @change-page="onPageChanged">
               <template slot="thead">
                   <vs-th sort-key="id">ID</vs-th>
                   <vs-th sort-key="name">Name</vs-th>
               </template>

               <template slot-scope="{data}">
                   <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                       <vs-td class="column-no-padding" :data="item.id">
                         {{ item.id }}
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.name">
                           <div @contextmenu.prevent="$refs.ad_account_context_menu.open($event, item)">
                               <vs-chip transparent>
                                   <vs-avatar icon-pack="feather" icon="icon-user" />
                                   {{item.name}}
                               </vs-chip>
                           </div>
                       </vs-td>

                   </vs-tr>

                   <!-- TOTAL STATS -->
                   <vs-td class="column-no-padding"></vs-td>
                   <vs-td class="column-no-padding"></vs-td>
                   <vs-td class="column-no-padding">{{ data.length }}</vs-td>
                   <!-- END TOTAL STATS-->
               </template>
            </vs-table>

            <!-- Campaigns table -->
            <vs-table v-if="view == 'campaigns'" stripe multiple v-model="selectedCampaigns" :max-items="maxItems" pagination search :data="campaigns" :currentPage="currentPage" @change-page="onPageChanged">
               <template slot="thead">
                   <vs-th sort-key="id">ID</vs-th>
                   <vs-th sort-key="name">Name</vs-th>
                   <vs-th sort-key="status">Status</vs-th>
                   <vs-th sort-key="objective">Objective</vs-th>
                   <vs-th sort-key="spent">Spent</vs-th>
                   <vs-th sort-key="impressions">Impressions</vs-th>
                   <vs-th sort-key="cpm">CPM</vs-th>
               </template>

               <template slot-scope="{data}">
                   <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                       <vs-td class="column-no-padding" :data="item.id">
                         {{ item.id }}
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.name">
                           <div @contextmenu.prevent="$refs.campaign_context_menu.open($event, item)">
                               <vs-chip transparent>
                                   <vs-avatar icon-pack="feather" icon="icon-box" />
                                   {{item.name}}
                               </vs-chip>
                           </div>
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.status">
                         <vs-switch
                              :color="statusColor[item.status]"
                              v-model="item.status == 'ACTIVE'"
                              vs-icon-off="pause" vs-icon-on="done"
                              @input="switchStatus('campaign', item)">
                              <span slot="on" v-if="item.status == 'ACTIVE'">Active</span>
                              <span slot="off">Paused</span>
                          </vs-switch>
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.objective">
                         {{ item.objective }}
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.spent">
                         € {{ item.spent.toFixed(2) }}
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.impressions">
                         {{ item.impressions }}
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.cpm">
                         € {{ item.cpm.toFixed(2) }}
                       </vs-td>

                   </vs-tr>

                   <!-- TOTAL STATS -->
                   <vs-td class="column-no-padding"></vs-td>
                   <vs-td class="column-no-padding"></vs-td>
                   <vs-td class="column-no-padding">{{ data.length }}</vs-td>
                   <vs-td class="column-no-padding"></vs-td>
                   <vs-td class="column-no-padding"></vs-td>
                   <vs-td class="column-no-padding">€ {{ totalStats.spent.toFixed(2) }}</vs-td>
                   <vs-td class="column-no-padding">{{ totalStats.impressions }}</vs-td>
                   <vs-td class="column-no-padding">€ {{ totalStats.cpm.toFixed(2) }}</vs-td>
                   <!-- END TOTAL STATS-->
               </template>
            </vs-table>

            <!-- Adsets table -->
            <vs-table v-if="view == 'adsets'" stripe multiple v-model="selectedAdsets" :max-items="maxItems" pagination search :data="adsets" :currentPage="currentPage" @change-page="onPageChanged">
               <template slot="thead">
                   <vs-th sort-key="id">ID</vs-th>
                   <vs-th sort-key="name">Name</vs-th>
                   <vs-th sort-key="spent">Spent</vs-th>
                   <vs-th sort-key="impressions">Impressions</vs-th>
                   <vs-th sort-key="cpm">CPM</vs-th>
               </template>

               <template slot-scope="{data}">
                   <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                       <vs-td class="column-no-padding" :data="item.id">
                         {{ item.id }}
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.name">
                           <div @contextmenu.prevent="$refs.adset_context_menu.open($event, item)">
                               <vs-chip transparent>
                                   <vs-avatar icon-pack="feather" icon="icon-box" />
                                   {{item.name}}
                               </vs-chip>
                           </div>
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.spent">
                         € {{ item.spent.toFixed(2) }}
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.impressions">
                         {{ item.impressions }}
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.cpm">
                         € {{ item.cpm.toFixed(2) }}
                       </vs-td>

                   </vs-tr>

                   <!-- TOTAL STATS -->
                   <vs-td class="column-no-padding"></vs-td>
                   <vs-td class="column-no-padding"></vs-td>
                   <vs-td class="column-no-padding">{{ data.length }}</vs-td>
                   <vs-td class="column-no-padding">€ {{ totalStats.spent.toFixed(2) }}</vs-td>
                   <vs-td class="column-no-padding">{{ totalStats.impressions }}</vs-td>
                   <vs-td class="column-no-padding">€ {{ totalStats.cpm.toFixed(2) }}</vs-td>
                   <!-- END TOTAL STATS-->
               </template>
            </vs-table>

            <!-- Ads table -->
            <vs-table v-if="view == 'ads'" stripe multiple v-model="selectedAds" :max-items="maxItems" pagination search :data="ads" :currentPage="currentPage" @change-page="onPageChanged">
               <template slot="thead">
                   <vs-th sort-key="id">ID</vs-th>
                   <vs-th sort-key="name">Name</vs-th>
                   <vs-th sort-key="spent">Spent</vs-th>
                   <vs-th sort-key="impressions">Impressions</vs-th>
                   <vs-th sort-key="cpm">CPM</vs-th>
               </template>

               <template slot-scope="{data}">
                   <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                       <vs-td class="column-no-padding" :data="item.id">
                         {{ item.id }}
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.name">
                           <div @contextmenu.prevent="$refs.ad_context_menu.open($event, item)">
                               <vs-chip transparent>
                                   <vs-avatar icon-pack="feather" icon="icon-box" />
                                   {{item.name}}
                               </vs-chip>
                           </div>
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.spent">
                         € {{ item.spent.toFixed(2) }}
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.impressions">
                         {{ item.impressions }}
                       </vs-td>

                       <vs-td class="column-no-padding" :data="item.cpm">
                         € {{ item.cpm.toFixed(2) }}
                       </vs-td>

                   </vs-tr>

                   <!-- TOTAL STATS -->
                   <vs-td class="column-no-padding"></vs-td>
                   <vs-td class="column-no-padding"></vs-td>
                   <vs-td class="column-no-padding">{{ data.length }}</vs-td>
                   <vs-td class="column-no-padding">€ {{ totalStats.spent.toFixed(2) }}</vs-td>
                   <vs-td class="column-no-padding">{{ totalStats.impressions }}</vs-td>
                   <vs-td class="column-no-padding">€ {{ totalStats.cpm.toFixed(2) }}</vs-td>
                   <!-- END TOTAL STATS-->
               </template>
            </vs-table>

            <!-- View ad popup -->
            <vs-popup title="View Ad" :active.sync="showPopup['view_ad']">

                <div>
                    <div class="vx-row mb-6 flex items-center">
                        <vs-button class="mr-auto ml-auto">{{ ad.name }}</vs-button>
                    </div>

                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/4 w-full mt-2">
                        </div>
                        <div class="vx-col sm:w-1/2 w-full mt-2">
                            <img v-if="ad.image_url != ''" :src="ad.image_url" class="w-full">
                            <img v-else src="https://www.tucanomacchine.it/wp-content/uploads/2019/07/480px-No_image_available.svg.png" class="w-full">
                        </div>
                        <div class="vx-col sm:w-1/4 w-full mt-2">
                        </div>
                    </div>

                    <div class="vx-row mb-6">
                        <div class="vx-col w-full">
                            <span>{{ ad.body }}</span>
                        </div>
                    </div>

                    <div class="vx-row mb-6">
                        <div class="vx-col w-full">
                            <span><b>{{ ad.title }}</b></span>
                        </div>
                    </div>
                    
                    <div class="vx-row mb-6">
                        <div class="vx-col w-full">
                            <vs-table :data="[{spent:ad.spent,impressions:ad.impressions,cpm:ad.cpm}]">
                                <template slot="thead">
                                    <vs-th sort-key="spent">Spent</vs-th>
                                    <vs-th sort-key="impressions">Impressions</vs-th>
                                    <vs-th sort-key="cpm">CPM</vs-th>
                                    <vs-th>Actions</vs-th>
                                </template>

                                <template slot-scope="{data}">
                                    <vs-tr :key="indextr" v-for="(tr, indextr) in data" >
                                        <vs-td v-if="ad.hasOwnProperty('spent')">
                                            {{data[indextr].spent.toFixed(2)}}%
                                        </vs-td>
                                        <vs-td v-if="ad.hasOwnProperty('impressions')">
                                            € {{data[indextr].impressions.toFixed(2)}}
                                        </vs-td>
                                        <vs-td v-if="ad.hasOwnProperty('cpm')">
                                            € {{data[indextr].cpm.toFixed(2)}}
                                        </vs-td>
                                        <vs-td>
                                            <div class="flex items-center">
                                                <vs-button :disabled="ad.post_url == ''" @click="openNewTab(ad.post_url)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-facebook"></vs-button>
                                                <vs-button :disabled="ad.url == ''" @click="openNewTab(ad.url)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-link"></vs-button>
                                            </div>
                                        </vs-td>
                                    </vs-tr>
                                </template>
                                
                            </vs-table>
                        </div>
                    </div>
                </div>

            </vs-popup>   

        </div>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'

export default {
  name: 'FacebookAdsTable',
  components: {
    VueContext,
  },
  props: {
  },
  data () {
    return {
      appId: '1031757410984629',
      loggedIn: false,
      FB: {},
      fbApiVersion: 'v12.0',
      accessToken:'',

      view: 'ad_accounts',
      viewLevel: {
        'ad_accounts': 0,
        'campaigns': 1,
        'adsets': 2,
        'ads': 3,
      },

      maxItems: 20,

      adAccounts: [],
      selectedAdAccounts: [],

      campaigns: [],
      selectedCampaigns: [],

      adsets: [],
      selectedAdsets: [],

      ads: [],
      selectedAds: [],

      statusColor: {
        'ACTIVE': 'success',
        'PAUSED': 'warning',
        'DELETED': 'danger',
        'ARCHIVED': 'warning',
      },

      totalStats: {
        spent: 0.0,
        impressions: 0,
        cpm: 0.0,
      },

      showPopup: {
        'view_ad': false,
      },

      campaign: {},
      adset: {},
      ad: {},
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
    objects () {
      if (this.view == 'ad_accounts') {
        return this.adAccounts
      } else if (this.view == 'campaigns') {
        return this.campaigns
      } else if (this.view == 'adsets') {
        return this.adsets
      } else if (this.view == 'ads') {
        return this.ads
      } else {
        return []
      }
    },
    selectedObjects() {
      if (this.view == 'ad_accounts') {
        return this.selectedAdAccounts
      } else if (this.view == 'campaigns') {
        return this.selectedCampaigns
      } else if (this.view == 'adsets') {
        return this.selectedAdsets
      } else if (this.view == 'ads') {
        return this.selectedAds
      } else {
        return []
      }
    },
    fbApiUrl() {
      return 'https://graph.facebook.com/' + this.fbApiVersion
    },
  },
  methods: {
    onPageChanged(val) {
      this.currentPage = val
    },
    async changeView(view, parentId = null) {
      if (view == 'ad_accounts') {
        this.view = view
        await this.loadAdAccounts()
      } else if (view == 'campaigns') {
        this.view = view
        if (parentId !== null) {
          await this.loadCampaigns(parentId)
        }
        this.calculateStats()
      } else if (view == 'adsets') {
        this.view = view
        if (parentId !== null) {
          await this.loadAdsets(parentId)
        }
        this.calculateStats()
      } else if (view == 'ads') {
        this.view = view
        if (parentId !== null) {
          await this.loadAds(parentId)
        }
        this.calculateStats()
      }
    },
    getObjectIndexById(id) {
      for (let i = 0; i < this.objects.length; i++) {
        if (this.objects[i].id == id) {
          return i
        }
      }
      return -1
    },

    login() {
      this.FB.login((response) => {
        if (response.authResponse) {
          this.accessToken = response.authResponse.accessToken
          this.loggedIn = true
          this.changeView('ad_accounts')

          // DEBUG TODO: to delete
          console.log("Logged in")
        } else {
          this.loggedIn = false

          // DEBUG TODO: to delete
          console.log("Not logged in")
        }
      })
    },
    async loadAdAccounts() {
      this.$vs.loading()

      this.adAccounts = []
      this.selectedAdAccounts = []

      let params = {
        access_token: this.accessToken,
        fields: 'id,name,adaccounts{id,name}',
      }
      const response = await this.$http.get(this.fbApiUrl + '/me', { params: params })
      if (response !== null && !response.hasOwnProperty('error')) {
          this.adAccounts = response.data.adaccounts.data
      }

      this.$vs.loading.close()
    },
    async loadCampaigns(adAccountId) {
      this.$vs.loading()

      this.campaigns = []
      this.selectedCampaigns = []

      // TODO: time range => filters
      let timeRange = {
        since: '2020-09-28',
        until: '2021-09-28',
      }
      // TODO: What about date_preset??
      let params = {
        access_token: this.accessToken,
        fields: 'id,name,status,effective_status,objective,insights.time_range(' + JSON.stringify(timeRange) + '){spend,impressions,cpm,actions,action_values,conversions,conversion_values,cost_per_conversion}',
        time_range: timeRange,
        //date_preset: 'maximum', // TODO: What about this
      }
      const response = await this.$http.get(this.fbApiUrl + '/' + adAccountId + '/campaigns', { params: params })
      if (response !== null && !response.hasOwnProperty('error')) {
          this.campaigns = response.data.data
      }

      for (let i = 0; i < this.campaigns.length; i++) {
        let obj = this.campaigns[i]

        let spent = 0.0
        let impressions = 0
        let cpm = 0.0

        if (obj.hasOwnProperty('insights')) {
          let stats = obj.insights.data[0]

          spent = parseFloat(stats.spend)
          impressions = parseInt(stats.impressions)
          cpm = parseFloat(stats.cpm)
        }

        obj.spent = spent
        obj.impressions = impressions
        obj.cpm = cpm

        this.campaigns[i] = obj
      }

      this.$vs.loading.close()
    },
    async loadAdsets(campaignId) {
      this.$vs.loading()

      this.adsets = []
      this.selectedAdsets = []

      // TODO: time range => filters
      let timeRange = {
        since: '2020-09-28',
        until: '2021-09-28',
      }
      let params = {
        access_token: this.accessToken,
        fields: 'id,name,insights.time_range(' + JSON.stringify(timeRange) + '){spend,impressions,cpm,actions,action_values,conversions,conversion_values,cost_per_conversion}',
        time_range: timeRange,
        //date_preset: 'maximum',
      }
      const response = await this.$http.get(this.fbApiUrl + '/' + campaignId + '/adsets', { params: params })
      if (response !== null && !response.hasOwnProperty('error')) {
          this.adsets = response.data.data
      }

      for (let i = 0; i < this.adsets.length; i++) {
        let obj = this.adsets[i]

        let spent = 0.0
        let impressions = 0
        let cpm = 0.0

        if (obj.hasOwnProperty('insights')) {
          let stats = obj.insights.data[0]

          spent = parseFloat(stats.spend)
          impressions = parseInt(stats.impressions)
          cpm = parseFloat(stats.cpm)
        }

        obj.spent = spent
        obj.impressions = impressions
        obj.cpm = cpm

        this.adsets[i] = obj
      }

      this.$vs.loading.close()
    },
    async loadAds(adsetId) {
      this.$vs.loading()

      this.ads = []
      this.selectedAds = []

      // TODO: time range => filters
      let timeRange = {
        since: '2020-09-28',
        until: '2021-09-28',
      }
      let params = {
        access_token: this.accessToken,
        fields: 'id,name,creative,insights.time_range(' + JSON.stringify(timeRange) + '){spend,impressions,cpm,actions,action_values,conversions,conversion_values,cost_per_conversion}',
        time_range: timeRange,
        //date_preset: 'maximum',
      }
      const response = await this.$http.get(this.fbApiUrl + '/' + adsetId + '/ads', { params: params })
      if (response !== null && !response.hasOwnProperty('error')) {
          this.ads = response.data.data
      }

      for (let i = 0; i < this.ads.length; i++) {
        let obj = this.ads[i]

        let spent = 0.0
        let impressions = 0
        let cpm = 0.0

        if (obj.hasOwnProperty('insights')) {
          let stats = obj.insights.data[0]

          spent = parseFloat(stats.spend)
          impressions = parseInt(stats.impressions)
          cpm = parseFloat(stats.cpm)
        }

        obj.spent = spent
        obj.impressions = impressions
        obj.cpm = cpm

        this.ads[i] = obj
      }

      this.$vs.loading.close()
    },
    async switchStatus(objectType, obj) {
      let oldStatus = obj.status
      if (oldStatus == 'DISABLED') return

      let newStatus = 'PAUSED'
      if (oldStatus == 'REVIEW' || oldStatus == 'ACTIVE') {
        newStatus = 'PAUSED'
      } else if (oldStatus == 'PAUSED') {
        newStatus = 'ACTIVE'
      } else {
        return
      }

      this.$vs.loading()
      try {
        let params = {
          access_token: this.accessToken,
          status: newStatus,
        }
        const response = await this.$http.post(this.fbApiUrl + '/' + obj.id, params)

        let objIndex = this.getObjectIndexById(obj.id)
        if (objIndex >= 0) {
          obj.status = newStatus
          if (objectType == 'campaign') {
            this.campaigns[objIndex] = obj
          } else if (objectType == 'adset') {
            this.adsets[objIndex] = obj
          } else if (objectType == 'ad') {
            this.ads[objIndex] = obj
          }
        }

        this.$vs.notify({
          title: 'Success',
          text: objectType.charAt(0).toUpperCase() + objectType.slice(1) + ' status updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: error.message,
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }
      this.$vs.loading.close()
    },

    openNewTab(url) {
      if (url != '') {
        window.open(url)
      }
    },
    calculateStats() {
      let spent = 0.0
      let impressions = 0
      let cpm = 0.0

      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        spent += obj.spent
        impressions += obj.impressions
      }
      cpm = (impressions > 0) ? (spent*1000/impressions) : (spent*1000)

      this.totalStats = {
        spent: spent,
        impressions: impressions,
        cpm: cpm,
      }
    },
    async viewAd(ad) {
      if (!ad.hasOwnProperty('creative') ||
          !ad.creative.hasOwnProperty('id')) {
        return
      }

      this.$vs.loading()

      let params = {
        access_token: this.accessToken,
        fields: 'id,call_to_action_type,title,body,image_url,link_url,object_url',
      }
      const response = await this.$http.get(this.fbApiUrl + '/' + ad.creative.id, { params: params })

      this.ad = ad

      if (response.data.hasOwnProperty('call_to_action_type')) {
        this.ad.call_to_action_type = response.data.call_to_action_type
      }
      if (response.data.hasOwnProperty('title')) {
        this.ad.title = response.data.title
      }
      if (response.data.hasOwnProperty('body')) {
        this.ad.body = response.data.body
      }
      if (response.data.hasOwnProperty('image_url')) {
        this.ad.image_url = response.data.image_url
      }
      if (response.data.hasOwnProperty('link_url')) {
        this.ad.link_url = response.data.link_url
      }
      if (response.data.hasOwnProperty('object_url')) {
        this.ad.object_url = response.data.object_url
      }

      // TODO: How to get url and post url??
      this.ad.url = ''
      this.ad.post_url = ''

      this.showPopup['view_ad'] = true

      this.$vs.loading.close()
    },
  },
  mounted() {
    let _this = this
    this.$nextTick(() => {
      window.fbAsyncInit = function () {
        window.FB.init({
          appId: '1031757410984629',
          xfbml: true,
          version: _this.fbApiVersion
        })
        window.FB.AppEvents.logPageView()
        _this.FB = window.FB

        // DEBUG TODO: to delete
        console.log('FB SDK initialized')
      };

      (function (d, s, id) {
        let js, fjs = d.getElementsByTagName(s)[0]
        if (d.getElementById(id)) { return }
        js = d.createElement(s); js.id = id
        js.src = '//connect.facebook.net/en_US/sdk.js'
        fjs.parentNode.insertBefore(js, fjs)
      }(document, 'script', 'facebook-jssdk'));
    })
  }
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
