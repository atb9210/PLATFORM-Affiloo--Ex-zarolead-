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
                  <a href="#" @click="$refs.campaign_context_menu.close();editBidPopup(data);" class="flex items-center text-sm">
                    <feather-icon icon="DollarSignIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Edit bid</span>
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
                  <a href="#" @click="$refs.campaign_context_menu.close();editBidPopup(data);" class="flex items-center text-sm">
                    <feather-icon icon="DollarSignIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Edit bid</span>
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
                    <vs-th sort-key="budget">Budget</vs-th>
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

                        <vs-td class="column-no-padding" :data="data[indextr].daily_budget">
                            <editable-value
                                v-model="data[indextr].new_daily_budget"
                                @onEdit="data[indextr].new_daily_budget = data[indextr].daily_budget"
                                @onUpdate="updateBudget('campaign', data[indextr])">
                                <span v-if="item.cbo">
                                    € {{ item.daily_budget.toFixed(2) }}
                                </span>
                                <span v-else>
                                    ABO
                                </span>
                            </editable-value>
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
                    <vs-td class="column-no-padding"></vs-td>
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
                    <vs-th sort-key="budget">Budget</vs-th>
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

                        <vs-td class="column-no-padding" :data="data[indextr].daily_budget">
                            <editable-value
                                v-model="data[indextr].new_daily_budget"
                                @onEdit="data[indextr].new_daily_budget = data[indextr].daily_budget"
                                @onUpdate="updateBudget('adset', data[indextr])">
                                <span v-if="item.cbo">
                                    CBO
                                </span>
                                <span v-else>
                                    € {{ item.daily_budget.toFixed(2) }}
                                </span>
                            </editable-value>
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{ data.length }}</vs-td>
                    <vs-td class="column-no-padding">€ {{ totalStats.spent.toFixed(2) }}</vs-td>
                    <vs-td class="column-no-padding">{{ totalStats.impressions }}</vs-td>
                    <vs-td class="column-no-padding">€ {{ totalStats.cpm.toFixed(2) }}</vs-td>
                    <vs-td class="column-no-padding"></vs-td>
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

            <!-- Edit bid popup -->
            <vs-popup title="Edit budget" :active.sync="showPopup['edit_bid']">
                <div class="vx-row mb-6 flex items-center">
                    <vs-button class="mr-auto ml-auto">{{ editingObject.name }}</vs-button>
                </div>

                <div class="vx-row mb-6" v-if="(editingObject.object_type == 'adset' && editingObject.hasOwnProperty('cbo') && editingObject.cbo)">
                    <div class="vx-col w-full mt-2">
                        <span>* The campaign of this adset is CBO: you cannot choose bid type for the adset.</span>
                    </div>
                </div>
                <div class="vx-row mb-6" v-else>
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Bid Type</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-select v-model="editingObject.bid_strategy" class="w-full">
                            <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in bidOptions" />
                        </vs-select>
                    </div>
                </div>
                
                <div class="vx-row mb-6" v-if="editingObject.bid_strategy != 'LOWEST_COST_WITHOUT_CAP'">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Bid Amount</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="editingObject.bid_amount" />
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col w-full mt-2" v-if="(editingObject.object_type == 'campaign' && editingObject.hasOwnProperty('cbo') && !editingObject.cbo)">
                        <span>* This campaign is non-CBO: this bid will be assigned to every adset of this campaign.</span>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <vs-button class="mt-12 mr-auto ml-auto" @click="updateBid" color="primary" type="filled" :disabled="!isValidForm('edit_bid')">Update budget</vs-button>
                </div>
            </vs-popup>

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

      editingObject: {},

      bidOptions: [
        { value:'LOWEST_COST_WITHOUT_CAP',  label: 'Lowest cost' },
        { value:'LOWEST_COST_WITH_BID_CAP', label: 'Lowest cost with bid cap' },
        { value:'COST_CAP',                 label: 'Cost cap' },
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
        this.$vs.loading()
        this.selectedAdAccounts = []
        this.adAccounts = await this.fetchAdAccounts()
        this.$vs.loading.close()
      } else if (view == 'campaigns') {
        this.view = view
        if (parentId !== null) {
          this.$vs.loading()
          this.selectedCampaigns = []
          this.campaigns = await this.fetchCampaigns(parentId)
          this.$vs.loading.close()
        }
        this.calculateStats()
      } else if (view == 'adsets') {
        this.view = view
        if (parentId !== null) {
          this.$vs.loading()
          this.selectedAdsets = []
          this.adsets = await this.fetchAdsets(parentId)
          this.$vs.loading.close()
        }
        this.calculateStats()
      } else if (view == 'ads') {
        this.view = view
        if (parentId !== null) {
          this.$vs.loading()
          this.selectedAds = []
          this.ads = await this.fetchAds(parentId)
          this.$vs.loading.close()
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
    isValidForm(type) {
      if (type == 'edit_bid') {
        if (this.editingObject.hasOwnProperty('bid_strategy') &&
            this.editingObject.bid_strategy != '') {
          return ((this.editingObject.bid_strategy == 'LOWEST_COST_WITHOUT_CAP') ||
                  (this.editingObject.bid_strategy != 'LOWEST_COST_WITHOUT_CAP') && this.editingObject.bid_amount > 0)
        }
      }
      return false
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
    async fetchAdAccounts(insights = true) {
      let adAccounts = []

      let params = {
        access_token: this.accessToken,
        fields: 'id,name,adaccounts{id,name}',
      }
      const response = await this.$http.get(this.fbApiUrl + '/me', { params: params })
      if (response !== null && !response.hasOwnProperty('error')) {
          adAccounts = response.data.adaccounts.data
      }

      for (let i = 0; i < adAccounts.length; i++) {
        let obj = adAccounts[i]
        obj.object_type = 'ad_account'
        adAccounts[i] = obj
      }

      return adAccounts
    },
    async fetchCampaigns(adAccountId, insights = true) {
      let campaigns = []

      // TODO: time range => filters
      let timeRange = {
        since: '2020-09-28',
        until: '2021-09-28',
      }
      // TODO: What about date_preset??
      let params = {
        access_token: this.accessToken,
        fields: 'id,name,status,effective_status,objective,daily_budget,bid_strategy,bid_amount',
        time_range: timeRange,
        //date_preset: 'maximum', // TODO: What about this
      }
      if (insights) {
        params.fields += ',insights.time_range(' + JSON.stringify(timeRange) + '){spend,impressions,cpm,actions,action_values,conversions,conversion_values,cost_per_conversion}'
      }
      const response = await this.$http.get(this.fbApiUrl + '/' + adAccountId + '/campaigns', { params: params })
      if (response !== null && !response.hasOwnProperty('error')) {
          campaigns = response.data.data
      }

      for (let i = 0; i < campaigns.length; i++) {
        let obj = campaigns[i]
        obj.object_type = 'campaign'

        obj.cbo = (obj.hasOwnProperty('daily_budget') && obj.hasOwnProperty('bid_strategy'))

        let daily_budget = 0.0
        let bid_amount = 0.0
        let bid_strategy = ''
        if (obj.hasOwnProperty('daily_budget')) {
          daily_budget = parseInt(obj.daily_budget)/100
        }
        if (obj.hasOwnProperty('bid_amount')) {
          bid_amount = parseInt(obj.bid_amount)/100
        }
        if (obj.hasOwnProperty('bid_strategy')) {
          bid_strategy = obj.bid_strategy
        }
        obj.daily_budget = daily_budget
        obj.bid_amount = bid_amount
        obj.bid_strategy = bid_strategy
        obj.new_daily_budget = daily_budget

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

        campaigns[i] = obj
      }

      return campaigns
    },
    async fetchAdsets(campaignId, insights = true) {
      let adsets = []

      // TODO: time range => filters
      let timeRange = {
        since: '2020-09-28',
        until: '2021-09-28',
      }
      let params = {
        access_token: this.accessToken,
        fields: 'id,name,status,effective_status,objective,daily_budget,bid_strategy,bid_amount',
        time_range: timeRange,
        //date_preset: 'maximum',
      }
      if (insights) {
        params.fields += ',insights.time_range(' + JSON.stringify(timeRange) + '){spend,impressions,cpm,actions,action_values,conversions,conversion_values,cost_per_conversion}'
      }
      const response = await this.$http.get(this.fbApiUrl + '/' + campaignId + '/adsets', { params: params })
      if (response !== null && !response.hasOwnProperty('error')) {
          adsets = response.data.data
      }

      for (let i = 0; i < adsets.length; i++) {
        let obj = adsets[i]
        obj.object_type = 'adset'

        obj.cbo = !(obj.hasOwnProperty('daily_budget'))

        let daily_budget = 0.0
        let bid_amount = 0.0
        let bid_strategy = ''
        if (obj.hasOwnProperty('daily_budget')) {
          daily_budget = parseInt(obj.daily_budget)/100
        }
        if (obj.hasOwnProperty('bid_amount')) {
          bid_amount = parseInt(obj.bid_amount)/100
        }
        if (obj.hasOwnProperty('bid_strategy')) {
          bid_strategy = obj.bid_strategy
        }
        obj.daily_budget = daily_budget
        obj.bid_amount = bid_amount
        obj.bid_strategy = bid_strategy
        obj.new_daily_budget = daily_budget

        // TODO: to delete
        /*
        console.log('obj', obj)
        console.log('daily_budget', daily_budget)
        */

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

        adsets[i] = obj
      }

      return adsets
    },
    async fetchAds(adsetId, insights = true) {
      let ads = []

      // TODO: time range => filters
      let timeRange = {
        since: '2020-09-28',
        until: '2021-09-28',
      }
      let params = {
        access_token: this.accessToken,
        fields: 'id,name,creative',
        time_range: timeRange,
        //date_preset: 'maximum',
      }
      if (insights) {
        params.fields += ',insights.time_range(' + JSON.stringify(timeRange) + '){spend,impressions,cpm,actions,action_values,conversions,conversion_values,cost_per_conversion}'
      }
      const response = await this.$http.get(this.fbApiUrl + '/' + adsetId + '/ads', { params: params })
      if (response !== null && !response.hasOwnProperty('error')) {
          ads = response.data.data
      }

      for (let i = 0; i < ads.length; i++) {
        let obj = ads[i]
        obj.object_type = 'adset'

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

        ads[i] = obj
      }

      return ads
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
    async updateBudget(type, obj) {
      this.$vs.loading()

      try {
        let newDailyBudget = parseInt(parseFloat(obj.new_daily_budget.replace(',', '.'))*100)

        let params = {
          access_token: this.accessToken,
          daily_budget: newDailyBudget,
        }
        if (type == 'campaign') {
          if (obj.cbo) {
            await this.$http.post(this.fbApiUrl + '/' + obj.id, params)
          } else {
            let adsets = await this.fetchAdsets(obj.id, false)
            for (let i = 0; i < adsets.length; i++) {
              let adset = adsets[i]
              await this.$http.post(this.fbApiUrl + '/' + adset.id, params)
            }
          }
        } else if (type == 'adset') {
          await this.$http.post(this.fbApiUrl + '/' + obj.id, params)
        }

        this.$vs.notify({
          title: 'Success',
          text: 'Daily budget updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Daily budget cannot be modified',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    editBidPopup(obj) {
      this.editingObject = obj
      this.showPopup['edit_bid'] = true
    },
    updateBid() {
      this.$vs.loading()

      try {
        let bidAmount = parseInt(this.editingObject.bid_amount * 100)
        let bidStrategy = this.editingObject.bid_strategy

        // TODO: HERE

        if (this.editingObject.object_type == 'campaign') {
          if (this.editingObject.cbo) {
            // TODO: Implement
          } else {
            // TODO: Implement
          }
        } else {
          // TODO: Implement
        }

        this.$vs.notify({
          title: 'Success',
          text: 'Bid amount updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Bid amount cannot be modified',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

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
