<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="TikTok Ads" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <span class="btn-group flex items-center mr-2">
                <vs-button @click="changeView('campaigns')" :color="(view == 'campaigns') ? 'success' : 'primary'">Campaigns</vs-button>
                <vs-button @click="changeView('adgroups')" :color="(view == 'adgroups') ? 'success' : 'primary'">Adsets</vs-button>
                <vs-button @click="changeView('ads')" :color="(view == 'ads') ? 'success' : 'primary'">Ads</vs-button>
            </span>

            <!--
            <vs-button :disabled="!((view == 'campaigns') && (selectedCampaigns.length > 0))" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-dollar-sign" @click="showUpdateFormCost"></vs-button>
            <vs-button v-if="view == 'campaigns'" :disabled="!((view == 'campaigns') && (selectedCampaigns.length > 0))" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-trending-up" @click="fetchTrend('campaigns')"></vs-button>
            <vs-button v-if="view == 'adgroups'" :disabled="!((view == 'adgroups') && (selectedAdsets.length > 0))" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-trending-up" @click="fetchTrend('adgroups')"></vs-button>
            <vs-button v-if="view == 'ads'" :disabled="!((view == 'ads') && (selectedAds.length > 0))" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-trending-up" @click="fetchTrend('ads')"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-activity" @click="fetchAdvancedStats()"></vs-button>
            -->
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

        <vue-context ref="campaign_context_menu" v-slot="{ data }">
            <!--
            <li>
              <a href="#" @click="$refs.campaign_context_menu.close();editBudgetPopup(data.id);" class="flex items-center text-sm">
                <feather-icon icon="DollarSignIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit Budget</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.campaign_context_menu.close();editBidPopup(data.id);" class="flex items-center text-sm">
                <feather-icon icon="MinusCircleIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit bid</span>
              </a>
            </li>
            -->
            <li>
              <a href="#" @click="$refs.campaign_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vue-context ref="adset_context_menu" v-slot="{ data }">
            <!--
            <li>
              <a href="#" @click="$refs.adset_context_menu.close();editBudgetPopup(data.id);" class="flex items-center text-sm">
                <feather-icon icon="DollarSignIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit Budget</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.adset_context_menu.close();editBidPopup(data.id);" class="flex items-center text-sm">
                <feather-icon icon="MinusCircleIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit bid</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.adset_context_menu.close();duplicateAdsetPopup(data.id);" class="flex items-center text-sm">
                <feather-icon icon="CopyIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Duplicate</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.adset_context_menu.close();duplicateBidderAdsetPopup(data.id);" class="flex items-center text-sm">
                <feather-icon icon="CopyIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Bidder</span>
              </a>
            </li>
            -->
            <li>
              <a href="#" @click="$refs.adset_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vue-context ref="ad_context_menu" v-slot="{ data }">
            <!--
            <li>
              <a href="#" @click="$refs.ad_context_menu.close();viewAdPopup(data.id);" class="flex items-center text-sm">
                <feather-icon icon="EyeIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">View ad</span>
              </a>
            </li>
            -->
            <li>
              <a href="#" @click="$refs.ad_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <div>
            <vs-dropdown vs-trigger-click class="cursor-pointer">
                <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
                    <span class="mr-2">{{ currentPage * maxItems - (maxItems - 1) }} - {{ campaigns.length - currentPage * maxItems > 0 ? currentPage * maxItems : campaigns.length }} of {{ campaigns.length }}</span>
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

            <!-- Campaigns table -->
            <vs-table v-if="view == 'campaigns'" stripe multiple v-model="selectedCampaigns" :max-items="maxItems" pagination search :data="campaigns" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <vs-th sort-key="spent">Spent</vs-th>
                    <vs-th sort-key="result">Result</vs-th>
                    <vs-th sort-key="cpa">CPA</vs-th>
                    <vs-th sort-key="cr">CR</vs-th>
                    <vs-th sort-key="cpm">CPM</vs-th>
                    <vs-th sort-key="budget">Budget</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td class="column-no-padding" :data="data[indextr].name">
                            <div @contextmenu.prevent="$refs.campaign_context_menu.open($event, data[indextr])">
                                <vs-chip
                                    transparent
                                    class="disabled-object"
                                    :color="campaignStatusColor[data[indextr].status]">
                                    <vs-avatar icon-pack="feather" icon="icon-user" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].status">
                            <vs-switch
                                :color="campaignStatusColor[data[indextr].status]"
                                v-model="data[indextr].status == 'ACTIVE' || data[indextr].status == 'REVIEW' || data[indextr].status == 'DISABLED'"
                                vs-icon-off="pause" vs-icon-on="done"
                                @input="switchStatus('campaign', data[indextr].id)">
                                <span slot="on" v-if="data[indextr].status == 'ACTIVE'">Active</span>
                                <span slot="on" v-else-if="data[indextr].status == 'REVIEW'">Review</span>
                                <span slot="on" v-else="data[indextr].status == 'DISABLED'">Disabled</span>
                                <span slot="off">Paused</span>
                            </vs-switch>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].spent">
                            € {{data[indextr].spent.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].result">
                            {{data[indextr].result}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].cpa">
                            € {{data[indextr].cpa.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].cr">
                            {{data[indextr].cr.toFixed(2)}}%
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].cpm">
                            € {{data[indextr].cpm.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].budget">
                            € {{data[indextr].budget.toFixed(2)}}
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{data.length}}</vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.spent.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{total_stats.result}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.cpa.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{total_stats.cr.toFixed(2)}}%
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.cpm.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.budget.toFixed(2)}}
                    </vs-td>
                    <!-- END TOTAL STATS-->
                </template>
            </vs-table>

            <!-- Adgroups table -->
            <vs-table v-if="view == 'adgroups'" stripe multiple v-model="selectedAdsets" :max-items="maxItems" pagination search :data="adgroups" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <vs-th sort-key="spent">Spent</vs-th>
                    <vs-th sort-key="result">Result</vs-th>
                    <vs-th sort-key="cpa">CPA</vs-th>
                    <vs-th sort-key="cr">CR</vs-th>
                    <vs-th sort-key="cpm">CPM</vs-th>
                    <vs-th sort-key="budget">Budget</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td class="column-no-padding" :data="data[indextr].name">
                            <div @contextmenu.prevent="$refs.adset_context_menu.open($event, data[indextr])">
                                <vs-chip
                                    transparent
                                    class="disabled-object"
                                    :color="adgroupStatusColor[data[indextr].status]">
                                    <vs-avatar icon-pack="feather" icon="icon-user" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].status">
                            <vs-switch
                                :color="adgroupStatusColor[data[indextr].status]"
                                v-model="data[indextr].status == 'ACTIVE' || data[indextr].status == 'REVIEW' || data[indextr].status == 'DISABLED'"
                                vs-icon-off="pause" vs-icon-on="done"
                                @input="switchStatus('adset', data[indextr].id)">
                                <span slot="on" v-if="data[indextr].status == 'ACTIVE'">Active</span>
                                <span slot="on" v-else-if="data[indextr].status == 'REVIEW'">Review</span>
                                <span slot="on" v-else="data[indextr].status == 'DISABLED'">Disabled</span>
                                <span slot="off">Paused</span>
                            </vs-switch>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].spent">
                            € {{data[indextr].spent.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].result">
                            {{data[indextr].result}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].cpa">
                            € {{data[indextr].cpa.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].cr">
                            {{data[indextr].cr.toFixed(2)}}%
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].cpm">
                            € {{data[indextr].cpm.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].budget">
                            <span v-if="data[indextr].cbo">
                                CBO
                            </span>
                            <span v-else>
                                € {{data[indextr].budget.toFixed(2)}}
                            </span>
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{data.length}}</vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.spent.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{total_stats.result}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.cpa.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{total_stats.cr.toFixed(2)}}%
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.cpm.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.budget.toFixed(2)}}
                    </vs-td>
                    <!-- END TOTAL STATS-->
                </template>
            </vs-table>

            <!-- Ads table -->
            <vs-table v-if="view == 'ads'" stripe multiple v-model="selectedAds" :max-items="maxItems" pagination search :data="ads" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <vs-th sort-key="spent">Spent</vs-th>
                    <vs-th sort-key="result">Result</vs-th>
                    <vs-th sort-key="cpa">CPA</vs-th>
                    <vs-th sort-key="cr">CR</vs-th>
                    <vs-th sort-key="cpm">CPM</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td class="column-no-padding" :data="data[indextr].name">
                            <div @contextmenu.prevent="$refs.ad_context_menu.open($event, data[indextr])">
                                <vs-chip
                                    transparent
                                    class="disabled-object"
                                    :color="adStatusColor[data[indextr].status]">
                                    <vs-avatar icon-pack="feather" icon="icon-user" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].status">
                            <vs-switch
                                :color="adStatusColor[data[indextr].status]"
                                v-model="data[indextr].status == 'ACTIVE' || data[indextr].status == 'REVIEW' || data[indextr].status == 'DISABLED'"
                                vs-icon-off="pause" vs-icon-on="done"
                                @input="switchStatus('ad', data[indextr].id)">
                                <span slot="on" v-if="data[indextr].status == 'ACTIVE'">Active</span>
                                <span slot="on" v-else-if="data[indextr].status == 'REVIEW'">Review</span>
                                <span slot="on" v-else="data[indextr].status == 'DISABLED'">Disabled</span>
                                <span slot="off">Paused</span>
                            </vs-switch>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].spent">
                            € {{data[indextr].spent.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].result">
                            {{data[indextr].result}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].cpa">
                            € {{data[indextr].cpa.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].cr">
                            {{data[indextr].cr.toFixed(2)}}%
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].cpm">
                            € {{data[indextr].cpm.toFixed(2)}}
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{data.length}}</vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.spent.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{total_stats.result}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.cpa.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{total_stats.cr.toFixed(2)}}%
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.cpm.toFixed(2)}}
                    </vs-td>
                    <!-- END TOTAL STATS-->
                </template>
            </vs-table>

        </div>

    </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import VueApexCharts from 'vue-apexcharts'

import { VueContext } from 'vue-context'

// TODO: Move this function into a utilities package?
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

export default {
  name: 'TikTokAdsTable',
  components: {
    'v-select': vSelect,
    VueApexCharts,
    VueContext,
  },
  props: {
  },
  data () {
    return {
      view: 'campaigns',

      selectedCampaigns: [],
      selectedAdsets: [],
      selectedAds: [],

      deleted: [],

      maxItems: 20,

      campaignsLoaded: false,
      adsetsLoaded: false,
      adsLoaded: false,

      campaigns: [],
      adgroups: [],
      ads: [],

      api: {
        accessToken: 'e191120b6cd3f1edf434f5c1527284799ef2839c',
        appId: '6922555156701642753',
        secret: '49aad18291b3c0fd96441b863adbc02630b5518c',
      },

      advertisers: [],

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
        budget: 0,
      },
      dateFormat: 'yyyy-MM-dd',
      filters: {
        date_from: '',
        date_to: '',
        status: '',
      },
      showFilters: false,

      advertiserStatuses: [
        'STATUS_DISABLE',
        'STATUS_PENDING_CONFIRM',
        'STATUS_PENDING_VERIFIED',
        'STATUS_CONFIRM_FAIL',
        'STATUS_ENABLE',
        'STATUS_CONFIRM_FAIL_END',
        'STATUS_PENDING_CONFIRM_MODIFY',
        'STATUS_CONFIRM_MODIFY_FAIL',
        'STATUS_LIMIT',
        'STATUS_WAIT_FOR_BPM_AUDIT',
        'STATUS_WAIT_FOR_PUBLIC_AUTH',
        'STATUS_SELF_SERVICE_UNAUDITED',
        'STATUS_CONTRACT_PENDING',
      ],

      campaignStatuses: [
        'CAMPAIGN_STATUS_DELETE',
        'CAMPAIGN_STATUS_ADVERTISER_AUDIT_DENY',
        'CAMPAIGN_STATUS_ADVERTISER_AUDIT_DENY',
        'ADVERTISER_CONTRACT_PENDING',
        'CAMPAIGN_STATUS_BUDGET_EXCEED',
        'CAMPAIGN_STATUS_DISABLE',
        'CAMPAIGN_STATUS_ENABLE',
        'CAMPAIGN_STATUS_ALL',
        'CAMPAIGN_STATUS_NOT_DELETE',
      ],

      adgroupStatuses: [
        'ADGROUP_STATUS_DELETE',
        'ADGROUP_STATUS_CAMPAIGN_DELETE',
        'ADGROUP_STATUS_ADVERTISER_AUDIT_DENY',
        'ADGROUP_STATUS_ADVERTISER_AUDIT_DENY',
        'ADVERTISER_CONTRACT_PENDING',
        'ADGROUP_STATUS_CAMPAIGN_EXCEED',
        'ADGROUP_STATUS_BUDGET_EXCEED',
        'ADGROUP_STATUS_BALANCE_EXCEED',
        'ADGROUP_STATUS_AUDIT_DENY',
        'ADGROUP_STATUS_REAUDIT',
        'ADGROUP_STATUS_AUDIT',
        'ADGROUP_STATUS_CREATE',
        'ADGROUP_STATUS_NOT_START',
        'ADGROUP_STATUS_TIME_DONE',
        'ADGROUP_STATUS_CAMPAIGN_DISABLE',
        'ADGROUP_STATUS_DISABLE',
        'ADGROUP_STATUS_DELIVERY_OK',
        'ADGROUP_STATUS_SHADOW_ADGROUP_REAUDIT',
        'ADGROUP_STATUS_ALL',
        'ADGROUP_STATUS_NOT_DELETE',
      ],

      adStatuses: [
        'AD_STATUS_CAMPAIGN_DELETE',
        'AD_STATUS_ADGROUP_DELETE',
        'AD_STATUS_DELETE',
        'AD_STATUS_ADVERTISER_AUDIT_DENY',
        'AD_STATUS_ADVERTISER_AUDIT_DENY',
        'ADVERTISER_CONTRACT_PENDING',
        'AD_STATUS_BALANCE_EXCEED',
        'AD_STATUS_CAMPAIGN_EXCEED',
        'AD_STATUS_BUDGET_EXCEED',
        'AD_STATUS_AUDIT',
        'AD_STATUS_REAUDIT',
        'AD_STATUS_AUDIT_DENY',
        'AD_STATUS_ADGROUP_AUDIT_DENY',
        'AD_STATUS_NOT_START',
        'AD_STATUS_DONE',
        'AD_STATUS_CAMPAIGN_DISABLE',
        'AD_STATUS_ADGROUP_DISABLE',
        'AD_STATUS_DISABLE',
        'AD_STATUS_DELIVERY_OK',
        'AD_STATUS_ALL',
        'AD_STATUS_NOT_DELETE',
      ],

      advertiserStatusColor: {
        'STATUS_DISABLE'               : 'danger',
        'STATUS_PENDING_CONFIRM'       : 'warning',
        'STATUS_PENDING_VERIFIED'      : 'success',
        'STATUS_CONFIRM_FAIL'          : 'danger',
        'STATUS_ENABLE'                : 'success',
        'STATUS_CONFIRM_FAIL_END'      : 'danger',
        'STATUS_PENDING_CONFIRM_MODIFY': 'warning',
        'STATUS_CONFIRM_MODIFY_FAIL'   : 'danger',
        'STATUS_LIMIT'                 : 'warning',
        'STATUS_WAIT_FOR_BPM_AUDIT'    : 'warning',
        'STATUS_WAIT_FOR_PUBLIC_AUTH'  : 'warning',
        'STATUS_SELF_SERVICE_UNAUDITED': 'warning',
        'STATUS_CONTRACT_PENDING'      : 'warning',
      },

      campaignStatusColor: {
        'CAMPAIGN_STATUS_DELETE'               : 'danger',
        'CAMPAIGN_STATUS_ADVERTISER_AUDIT_DENY': 'danger',
        'CAMPAIGN_STATUS_ADVERTISER_AUDIT'     : 'warning',
        'ADVERTISER_CONTRACT_PENDING'          : 'warning',
        'CAMPAIGN_STATUS_BUDGET_EXCEED'        : 'success',
        'CAMPAIGN_STATUS_DISABLE'              : 'danger',
        'CAMPAIGN_STATUS_ENABLE'               : 'success',
        'CAMPAIGN_STATUS_ALL'                  : 'danger',
        'CAMPAIGN_STATUS_NOT_DELETE'           : 'danger',
      },

      adgroupStatusColor: {
        'ADGROUP_STATUS_DELETE'                : 'danger',
        'ADGROUP_STATUS_CAMPAIGN_DELETE'       : 'danger',
        'ADGROUP_STATUS_ADVERTISER_AUDIT_DENY' : 'danger',
        'ADGROUP_STATUS_ADVERTISER_AUDIT'      : 'warning',
        'ADVERTISER_CONTRACT_PENDING'          : 'warning',
        'ADGROUP_STATUS_CAMPAIGN_EXCEED'       : 'success',
        'ADGROUP_STATUS_BUDGET_EXCEED'         : 'success',
        'ADGROUP_STATUS_BALANCE_EXCEED'        : 'danger',
        'ADGROUP_STATUS_AUDIT_DENY'            : 'danger',
        'ADGROUP_STATUS_REAUDIT'               : 'warning',
        'ADGROUP_STATUS_AUDIT'                 : 'warning',
        'ADGROUP_STATUS_CREATE'                : 'success',
        'ADGROUP_STATUS_NOT_START'             : 'danger',
        'ADGROUP_STATUS_TIME_DONE'             : 'success',
        'ADGROUP_STATUS_CAMPAIGN_DISABLE'      : 'danger',
        'ADGROUP_STATUS_DISABLE'               : 'danger',
        'ADGROUP_STATUS_DELIVERY_OK'           : 'success',
        'ADGROUP_STATUS_SHADOW_ADGROUP_REAUDIT': 'warning',
        'ADGROUP_STATUS_ALL'                   : 'danger',
        'ADGROUP_STATUS_NOT_DELETE'            : 'danger',
      },

      adStatusColor: {
        'AD_STATUS_CAMPAIGN_DELETE'      : 'danger',
        'AD_STATUS_ADGROUP_DELETE'       : 'danger',
        'AD_STATUS_DELETE'               : 'danger',
        'AD_STATUS_ADVERTISER_AUDIT_DENY': 'danger',
        'AD_STATUS_ADVERTISER_AUDIT'     : 'warning',
        'ADVERTISER_CONTRACT_PENDING'    : 'warning',
        'AD_STATUS_BALANCE_EXCEED'       : 'danger',
        'AD_STATUS_CAMPAIGN_EXCEED'      : 'success',
        'AD_STATUS_BUDGET_EXCEED'        : 'success',
        'AD_STATUS_AUDIT'                : 'warning',
        'AD_STATUS_REAUDIT'              : 'warning',
        'AD_STATUS_AUDIT_DENY'           : 'danger',
        'AD_STATUS_ADGROUP_AUDIT_DENY'   : 'danger',
        'AD_STATUS_NOT_START'            : 'danger',
        'AD_STATUS_DONE'                 : 'success',
        'AD_STATUS_CAMPAIGN_DISABLE'     : 'danger',
        'AD_STATUS_ADGROUP_DISABLE'      : 'danger',
        'AD_STATUS_DISABLE'              : 'danger',
        'AD_STATUS_DELIVERY_OK'          : 'success',
        'AD_STATUS_ALL'                  : 'danger',
        'AD_STATUS_NOT_DELETE'           : 'danger',
      },
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
    objects() {
      if (this.view == 'campaigns') {
        return this.campaigns
      } else if (this.view == 'adgroups') {
        return this.adgroups
      } else if (this.view == 'ads') {
        return this.ads
      } else {
        return []
      }
    },
    statusOptions() {
      if (this.view == 'campaigns') {
        return this.campaignStatuses
      } else if (this.view == 'adgroups') {
        return this.adgroupStatuses
      } else if (this.view == 'ads') {
        return this.adStatuses
      } else {
        return []
      }
    },
  },
  methods: {
    getObjectIndexById(objects, id) {
      var index = -1
      for (let i = 0; i < objects.length; i++) {
        if (objects[i].id == id) {
          index = i
          break
        }
      }
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
      let budget = 0

      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]
        spent += obj.spent
        result += obj.result
        impressions += obj.impressions
        //clicks += obj.clicks
        budget += obj.budget
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
        budget: budget,
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
    async tiktokRequestPaginated(url, params) {
      let data = []

      let headers = {
        'Access-Token': this.api.accessToken,
      }

      let totalPages = 2
      let currentPage = 1
      while (currentPage < totalPages) {
        params.page_size = 1000
        params.page = currentPage
        const response = await this.$http.get(url, { headers: headers, params: params })
        if (response.data.code !== 0) {
          totalPages = 0
          continue
        }
        currentPage = response.data.data.page_info.page
        totalPages = response.data.data.page_info.total_page
        for (let i = 0; i < response.data.data.list.length; i++) {
          data.push(response.data.data.list[i])
        }
      }

      return data
    },
    async fetchAdvertisers() {
      // TODO: This is a momentary patch because I don't know how to get ad accounts
      this.advertisers = [
        { id: '6937588729175392257' },
      ]
      return
    },
    async loadCampaigns() {
      this.$vs.loading()

      let headers = {
        'Access-Token': this.api.accessToken,
      }
      this.campaigns = []
      for (let advertiserIndex = 0; advertiserIndex < this.advertisers.length; advertiserIndex++) {
        let advertiser = this.advertisers[advertiserIndex]

        let params = {
          advertiser_id: advertiser.id,
        }
        const data = await this.tiktokRequestPaginated('https://ads.tiktok.com/open_api/v1.2/campaign/get/', params)

        for (let campaignIndex = 0; campaignIndex < data.length; campaignIndex++) {
          let campaign = data[campaignIndex]

          if (this.filters.status != '' && campaign.status != this.filters.status) {
            continue
          }

          let campaignToPush = campaign
          campaignToPush.id     = campaign.campaign_id
          campaignToPush.name   = campaign.campaign_name
          campaignToPush.spent  = 0.0
          campaignToPush.result = 0
          campaignToPush.cpa    = 0.0
          campaignToPush.cr     = 0.0
          campaignToPush.cpm    = 0.0
          this.campaigns.push(campaignToPush)
        }
      }

      let reportsParams = {
        advertiser_id: this.advertisers[0].id, // TODO: This is a momentary patch
        report_type: 'BASIC',
        data_level: 'AUCTION_CAMPAIGN',
        dimensions: JSON.stringify(['campaign_id']),
        metrics: ['spend', 'result', 'cost_per_conversion', 'conversion_rate', 'cpm', 'impressions'],
        lifetime: true,
      }
      if (this.filters.date_from != '') {
        reportsParams.start_date = this.filters.date_from
        reportsParams.lifetime = false
      }
      if (this.filters.date_from != '') {
        reportsParams.end_date = this.filters.date_to
        reportsParams.lifetime = false
      }

      const reports = await this.tiktokRequestPaginated('https://ads.tiktok.com/open_api/v1.2/reports/integrated/get/', reportsParams)
      for (let reportIndex = 0; reportIndex < reports.length; reportIndex++) {
        let report = reports[reportIndex]

        let spent = 0.0
        let result = 0
        let cpa = 0.0
        let cr = 0.0
        let cpm = 0.0
        let impressions = 0

        if (report.hasOwnProperty('metrics')) {
          if (report.metrics.hasOwnProperty('spend')) {
            spent = parseFloat(report.metrics.spend)
          }
          if (report.metrics.hasOwnProperty('result')) {
            result = parseInt(report.metrics.result)
          }
          if (report.metrics.hasOwnProperty('cost_per_conversion')) {
            cpa = parseFloat(report.metrics.cost_per_conversion)
          }
          if (report.metrics.hasOwnProperty('conversion_rate')) {
            cr = parseFloat(report.metrics.conversion_rate)
          }
          if (report.metrics.hasOwnProperty('cpm')) {
            cpm = parseFloat(report.metrics.cpm)
          }
          if (report.metrics.hasOwnProperty('impressions')) {
            impressions = parseFloat(report.metrics.impressions)
          }
        }

        let campaignIndex = this.getObjectIndexById(this.campaigns, report.dimensions.campaign_id)
        this.campaigns[campaignIndex].spent       = spent
        this.campaigns[campaignIndex].result      = result
        this.campaigns[campaignIndex].cpa         = cpa
        this.campaigns[campaignIndex].cr          = cr
        this.campaigns[campaignIndex].cpm         = cpm
        this.campaigns[campaignIndex].impressions = impressions
      }

      this.$vs.loading.close()
    },
    async loadAdgroups() {
      this.$vs.loading()

      let headers = {
        'Access-Token': this.api.accessToken,
      }
      this.adgroups = []
      for (let advertiserIndex = 0; advertiserIndex < this.advertisers.length; advertiserIndex++) {
        let advertiser = this.advertisers[advertiserIndex]

        let params = {
          advertiser_id: advertiser.id,
        }
        const data = await this.tiktokRequestPaginated('https://ads.tiktok.com/open_api/v1.2/adgroup/get/', params)
        for (let adgroupIndex = 0; adgroupIndex < data.length; adgroupIndex++) {
          let adgroup = data[adgroupIndex]

          if (this.filters.status != '' && adgroup.status != this.filters.status) {
            continue
          }

          let adgroupToPush = adgroup
          adgroupToPush.id     = adgroup.adgroup_id
          adgroupToPush.name   = adgroup.adgroup_name
          adgroupToPush.spent  = 0.0
          adgroupToPush.result = 0
          adgroupToPush.cpa    = 0.0
          adgroupToPush.cr     = 0.0
          adgroupToPush.cpm    = 0.0
          this.adgroups.push(adgroupToPush)
        }
      }

      let reportsParams = {
        advertiser_id: this.advertisers[0].id, // TODO: This is a momentary patch
        report_type: 'BASIC',
        data_level: 'AUCTION_CAMPAIGN',
        dimensions: JSON.stringify(['adgroup_id']),
        metrics: ['spend', 'result', 'cost_per_conversion', 'conversion_rate', 'cpm', 'impressions'],
        lifetime: true,
      }
      if (this.filters.date_from != '') {
        reportsParams.start_date = this.filters.date_from
        reportsParams.lifetime = false
      }
      if (this.filters.date_from != '') {
        reportsParams.end_date = this.filters.date_to
        reportsParams.lifetime = false
      }

      const reports = await this.tiktokRequestPaginated('https://ads.tiktok.com/open_api/v1.2/reports/integrated/get/', reportsParams)
      for (let reportIndex = 0; reportIndex < reports.length; reportIndex++) {
        let report = reports[reportIndex]

        let spent = 0.0
        let result = 0
        let cpa = 0.0
        let cr = 0.0
        let cpm = 0.0
        let impressions = 0

        if (report.hasOwnProperty('metrics')) {
          if (report.metrics.hasOwnProperty('spend')) {
            spent = parseFloat(report.metrics.spend)
          }
          if (report.metrics.hasOwnProperty('result')) {
            result = parseInt(report.metrics.result)
          }
          if (report.metrics.hasOwnProperty('cost_per_conversion')) {
            cpa = parseFloat(report.metrics.cost_per_conversion)
          }
          if (report.metrics.hasOwnProperty('conversion_rate')) {
            cr = parseFloat(report.metrics.conversion_rate)
          }
          if (report.metrics.hasOwnProperty('cpm')) {
            cpm = parseFloat(report.metrics.cpm)
          }
          if (report.metrics.hasOwnProperty('impressions')) {
            impressions = parseFloat(report.metrics.impressions)
          }
        }

        let adgroupIndex = this.getObjectIndexById(this.adgroups, report.dimensions.adgroup_id)
        this.adgroups[adgroupIndex].spent      = spent
        this.adgroups[adgroupIndex].result     = result
        this.adgroups[adgroupIndex].cpa        = cpa
        this.adgroups[adgroupIndex].cr         = cr
        this.adgroups[adgroupIndex].cpm        = cpm
        this.adgroups[adgroupIndex].impressions = impressions
      }

      this.$vs.loading.close()
    },
    async loadAds() {
      this.$vs.loading()

      let headers = {
        'Access-Token': this.api.accessToken,
      }
      this.ads = []
      for (let advertiserIndex = 0; advertiserIndex < this.advertisers.length; advertiserIndex++) {
        let advertiser = this.advertisers[advertiserIndex]

        let params = {
          advertiser_id: advertiser.id,
        }
        const data = await this.tiktokRequestPaginated('https://ads.tiktok.com/open_api/v1.2/ad/get/', params)
        for (let adIndex = 0; adIndex < data.length; adIndex++) {
          let ad = data[adIndex]

          if (this.filters.status != '' && ad.status != this.filters.status) {
            continue
          }

          let adToPush = ad
          adToPush.id     = ad.ad_id
          adToPush.name   = ad.ad_name
          adToPush.spent  = 0.0
          adToPush.result = 0
          adToPush.cpa    = 0.0
          adToPush.cr     = 0.0
          adToPush.cpm    = 0.0
          this.ads.push(adToPush)
        }
      }

      let reportsParams = {
        advertiser_id: this.advertisers[0].id, // TODO: This is a momentary patch
        report_type: 'BASIC',
        data_level: 'AUCTION_CAMPAIGN',
        dimensions: JSON.stringify(['ad_id']),
        metrics: ['spend', 'result', 'cost_per_conversion', 'conversion_rate', 'cpm', 'impressions'],
        lifetime: true,
      }
      if (this.filters.date_from != '') {
        reportsParams.start_date = this.filters.date_from
        reportsParams.lifetime = false
      }
      if (this.filters.date_from != '') {
        reportsParams.end_date = this.filters.date_to
        reportsParams.lifetime = false
      }

      const reports = await this.tiktokRequestPaginated('https://ads.tiktok.com/open_api/v1.2/reports/integrated/get/', reportsParams)
      for (let reportIndex = 0; reportIndex < reports.length; reportIndex++) {
        let report = reports[reportIndex]

        let spent = 0.0
        let result = 0
        let cpa = 0.0
        let cr = 0.0
        let cpm = 0.0

        if (report.hasOwnProperty('metrics')) {
          if (report.metrics.hasOwnProperty('spend')) {
            spent = parseFloat(report.metrics.spend)
          }
          if (report.metrics.hasOwnProperty('result')) {
            result = parseInt(report.metrics.result)
          }
          if (report.metrics.hasOwnProperty('cost_per_conversion')) {
            cpa = parseFloat(report.metrics.cost_per_conversion)
          }
          if (report.metrics.hasOwnProperty('conversion_rate')) {
            cr = parseFloat(report.metrics.conversion_rate)
          }
          if (report.metrics.hasOwnProperty('cpm')) {
            cpm = parseFloat(report.metrics.cpm)
          }
          if (report.metrics.hasOwnProperty('impressions')) {
            impressions = parseFloat(report.metrics.impressions)
          }
        }

        let adIndex = this.getObjectIndexById(this.ads, report.dimensions.ad_id)
        this.ads[adIndex].spent       = spent
        this.ads[adIndex].result      = result
        this.ads[adIndex].cpa         = cpa
        this.ads[adIndex].cr          = cr
        this.ads[adIndex].cpm         = cpm
        this.ads[adIndex].impressions = impressions
      }

      this.$vs.loading.close()
    },
    async applyFilters() {
      this.selectedCampaigns = []
      this.selectedAdsets = []
      this.selectedAds = []

      this.campaignsLoaded = false
      this.adsetsLoaded = false
      this.adsLoaded = false

      await this.loadCampaigns()
      await this.loadAdgroups()
      await this.loadAds()

      this.calculateStats()
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
    async changeView(view) {
      // TODO: to fix
      //this.onPageChanged(1)
      //this.gridApi.paginationGoToPage(0)

      this.view = view

      /*
      if (view == 'campaigns') {
        if (!this.campaignsLoaded) await this.loadCampaigns()
      } else if (view == 'adgroups') {
        if (!this.adsetsLoaded) await this.loadAdgroups()
      } else if (view == 'ads') {
        if (!this.adsLoaded) await this.loadAds()
      }
      */

      this.calculateStats()
      this.$nextTick(() => {
        this.showTrend = false
      })
    },
  },
  async created() {
    if (location.host == 'beta2.zarolead.com') {
      this.api.accessToken = '843140c3c74c2a73a033a4b3338ffa30f1d6c75d'
    }
    await this.fetchAdvertisers()
    this.resetFilters()
  },
  mounted () {
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
