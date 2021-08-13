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

        <div class="vx-row mb-2" v-if="view == 'campaigns' || view == 'adgroups' || view == 'ads'">
            <div class="vx-col w-1/3">
            </div>
            <div class="vx-col w-1/3 text-center">
                <vs-select
                    autocomplete
                    @input="onAdvertiserSwitch"
                    label="Switch Advertiser"
                    v-model="advertiserSwitch"
                    >
                    <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in advertisers" />
                </vs-select>
            </div>
            <div class="vx-col w-1/3">
            </div>
        </div>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <span class="btn-group flex items-center mr-2">
                <vs-button @click="changeView('advertisers')" :color="(view == 'advertisers') ? 'success' : 'primary'">Advertisers</vs-button>
                <vs-button @click="changeView('campaigns')" :color="(view == 'campaigns') ? 'success' : 'primary'">Campaigns</vs-button>
                <vs-button @click="changeView('adgroups')" :color="(view == 'adgroups') ? 'success' : 'primary'">Adgroups</vs-button>
                <vs-button @click="changeView('ads')" :color="(view == 'ads') ? 'success' : 'primary'">Ads</vs-button>
                <vs-button @click="changeView('rules')" :color="(view == 'rules') ? 'success' : 'primary'">Rules</vs-button>
            </span>

            <vs-button v-if="view == 'advertisers'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showCreateAdvertiserForm"></vs-button>
            <vs-button v-if="view == 'campaigns' || view == 'adgroups' || view == 'ads'" :disabled="!((view == 'campaigns') && (selectedCampaigns.length > 0))" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-dollar-sign" @click="showUpdateFormCost"></vs-button>
            <vs-button v-if="view == 'rules'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showCreateRuleForm"></vs-button>
            <vs-button v-if="view == 'rules'" :disabled="selectedRules.length <= 0" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-menu" @click="showBulkEditRuleForm"></vs-button>
            <!--
            <vs-button v-if="view == 'campaigns'" :disabled="!((view == 'campaigns') && (selectedCampaigns.length > 0))" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-trending-up" @click="fetchTrend('campaigns')"></vs-button>
            <vs-button v-if="view == 'adgroups'" :disabled="!((view == 'adgroups') && (selectedAdgroups.length > 0))" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-trending-up" @click="fetchTrend('adgroups')"></vs-button>
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

        <vue-context ref="advertiser_context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.advertiser_context_menu.close();changeView('campaigns', data.id);" class="flex items-center text-sm">
                <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show Campaigns</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.advertiser_context_menu.close();showEditAdvertiserForm(data);" class="flex items-center text-sm">
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.advertiser_context_menu.close();deleteAdvertiser(data);" class="flex items-center text-sm">
                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Delete</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.advertiser_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vue-context ref="campaign_context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.campaign_context_menu.close();changeView('adgroups', data.id);" class="flex items-center text-sm">
                <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show Adgroups</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.campaign_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vue-context ref="adgroup_context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.campaign_context_menu.close();changeView('ads', data.id);" class="flex items-center text-sm">
                <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show Ads</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.adgroup_context_menu.close();editBudgetPopup(data);" class="flex items-center text-sm">
                <feather-icon icon="DollarSignIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit Budget</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.adgroup_context_menu.close();editBidPopup(data);" class="flex items-center text-sm">
                <feather-icon icon="MinusCircleIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit bid</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.adgroup_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vue-context ref="ad_context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.ad_context_menu.close();viewAdPopup(data);" class="flex items-center text-sm">
                <feather-icon icon="EyeIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">View ad</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.ad_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vue-context ref="rule_context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.rule_context_menu.close();showCampaignList(data);" class="flex items-center text-sm">
                <feather-icon icon="MenuIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Campaigns list</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.rule_context_menu.close();runRule(data);" class="flex items-center text-sm">
                <feather-icon icon="SettingsIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Run now</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.rule_context_menu.close();switchStatusRule(data);" class="flex items-center text-sm">
                <feather-icon icon="EyeIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Enable/Disable</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.rule_context_menu.close();cloneRule(data);" class="flex items-center text-sm">
                <feather-icon icon="CopyIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Clone</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.rule_context_menu.close();showEditRuleForm(data);" class="flex items-center text-sm">
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.rule_context_menu.close();deleteRule(data);" class="flex items-center text-sm">
                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Delete</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.rule_context_menu.close()" class="flex items-center text-sm">
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

            <!-- Advertisers table -->
            <vs-table v-if="view == 'advertisers'" stripe multiple v-model="selectedAdvertisers" :max-items="maxItems" pagination search :data="advertisers" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="id">Id</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td class="column-no-padding" :data="data[indextr].name">
                            <div @contextmenu.prevent="$refs.advertiser_context_menu.open($event, data[indextr])">
                                <vs-chip
                                    transparent
                                    class="disabled-object"
                                    :color="campaignStatusColor[data[indextr].status]">
                                    <vs-avatar icon-pack="feather" icon="icon-user" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].id">
                            {{data[indextr].id}}
                        </vs-td>

                    </vs-tr>

                </template>
            </vs-table>

            <!-- Campaigns table -->
            <vs-table v-if="view == 'campaigns'" stripe multiple v-model="selectedCampaigns" :max-items="maxItems" pagination search :data="campaigns" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="effective_status">Status</vs-th>
                    <vs-th sort-key="spent">Spent</vs-th>
                    <vs-th sort-key="conversion">Conversion</vs-th>
                    <vs-th sort-key="cpa">CPA</vs-th>
                    <vs-th sort-key="cr">CR</vs-th>
                    <vs-th sort-key="cpm">CPM</vs-th>
                    <vs-th sort-key="profit">Profit</vs-th>
                    <vs-th sort-key="roi">ROI</vs-th>
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

                        <vs-td class="column-no-padding" :data="data[indextr].effective_status">
                            <vs-switch
                                :color="campaignStatusColor[data[indextr].status]"
                                v-model="data[indextr].effective_status == 'ACTIVE' || data[indextr].effective_status == 'REVIEW' || data[indextr].effective_status == 'DISABLED'"
                                vs-icon-off="pause" vs-icon-on="done"
                                @input="switchStatus('campaign', data[indextr].id)">
                                <span slot="on" v-if="data[indextr].effective_status == 'ACTIVE'">Active</span>
                                <span slot="on" v-else-if="data[indextr].effective_status == 'REVIEW'">Review</span>
                                <span slot="on" v-else-if="data[indextr].effective_status == 'DISABLED'">Disabled</span>
                                <span slot="off">Paused</span>
                            </vs-switch>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].spent">
                            € {{data[indextr].spent.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].conversion">
                            {{data[indextr].conversion}}
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

                        <vs-td class="column-no-padding" :data="data[indextr].profit">
                            € {{data[indextr].profit.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].roi">
                            € {{data[indextr].roi.toFixed(2)}}
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
                        {{total_stats.conversion}}
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
                        € {{total_stats.profit.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.roi.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.budget.toFixed(2)}}
                    </vs-td>
                    <!-- END TOTAL STATS-->
                </template>
            </vs-table>

            <!-- Adgroups table -->
            <vs-table v-if="view == 'adgroups'" stripe multiple v-model="selectedAdgroups" :max-items="maxItems" pagination search :data="adgroups" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="effective_status">Status</vs-th>
                    <vs-th sort-key="spent">Spent</vs-th>
                    <vs-th sort-key="conversion">Conversion</vs-th>
                    <vs-th sort-key="cpa">CPA</vs-th>
                    <vs-th sort-key="cr">CR</vs-th>
                    <vs-th sort-key="cpm">CPM</vs-th>
                    <vs-th sort-key="profit">Profit</vs-th>
                    <vs-th sort-key="roi">ROI</vs-th>
                    <vs-th sort-key="budget">Budget</vs-th>
                    <vs-th sort-key="conversion_bid">Bid</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td class="column-no-padding" :data="data[indextr].name">
                            <div @contextmenu.prevent="$refs.adgroup_context_menu.open($event, data[indextr])">
                                <vs-chip
                                    transparent
                                    class="disabled-object"
                                    :color="adgroupStatusColor[data[indextr].status]">
                                    <vs-avatar icon-pack="feather" icon="icon-user" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].effective_status">
                            <vs-switch
                                :color="adgroupStatusColor[data[indextr].status]"
                                v-model="data[indextr].effective_status == 'ACTIVE' || data[indextr].effective_status == 'REVIEW' || data[indextr].effective_status == 'DISABLED'"
                                vs-icon-off="pause" vs-icon-on="done"
                                @input="switchStatus('adgroup', data[indextr].id)">
                                <span slot="on" v-if="data[indextr].effective_status == 'ACTIVE'">Active</span>
                                <span slot="on" v-else-if="data[indextr].effective_status == 'REVIEW'">Review</span>
                                <span slot="on" v-else-if="data[indextr].effective_status == 'DISABLED'">Disabled</span>
                                <span slot="off">Paused</span>
                            </vs-switch>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].spent">
                            € {{data[indextr].spent.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].conversion">
                            {{data[indextr].conversion}}
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

                        <vs-td class="column-no-padding" :data="data[indextr].profit">
                            € {{data[indextr].profit.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].roi">
                            € {{data[indextr].roi.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].budget">
                            <span v-if="data[indextr].cbo">
                                CBO
                            </span>
                            <span v-else>
                                € {{data[indextr].budget.toFixed(2)}}
                            </span>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].conversion_bid">
                            € {{data[indextr].conversion_bid.toFixed(2)}}
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
                        {{total_stats.conversion}}
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
                        € {{total_stats.profit.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.roi.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.budget.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <!-- END TOTAL STATS-->
                </template>
            </vs-table>

            <!-- Ads table -->
            <vs-table v-if="view == 'ads'" stripe multiple v-model="selectedAds" :max-items="maxItems" pagination search :data="ads" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="effective_status">Status</vs-th>
                    <vs-th sort-key="spent">Spent</vs-th>
                    <vs-th sort-key="conversion">Conversion</vs-th>
                    <vs-th sort-key="cpa">CPA</vs-th>
                    <vs-th sort-key="cr">CR</vs-th>
                    <vs-th sort-key="cpm">CPM</vs-th>
                    <vs-th sort-key="profit">Profit</vs-th>
                    <vs-th sort-key="roi">ROI</vs-th>
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

                        <vs-td class="column-no-padding" :data="data[indextr].effective_status">
                            <vs-switch
                                :color="adStatusColor[data[indextr].status]"
                                v-model="data[indextr].effective_status == 'ACTIVE' || data[indextr].effective_status == 'REVIEW' || data[indextr].effective_status == 'DISABLED'"
                                vs-icon-off="pause" vs-icon-on="done"
                                @input="switchStatus('ad', data[indextr].id)">
                                <span slot="on" v-if="data[indextr].effective_status == 'ACTIVE'">Active</span>
                                <span slot="on" v-else-if="data[indextr].effective_status == 'REVIEW'">Review</span>
                                <span slot="on" v-else-if="data[indextr].effective_status == 'DISABLED'">Disabled</span>
                                <span slot="off">Paused</span>
                            </vs-switch>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].spent">
                            € {{data[indextr].spent.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].conversion">
                            {{data[indextr].conversion}}
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

                        <vs-td class="column-no-padding" :data="data[indextr].profit">
                            € {{data[indextr].profit.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].roi">
                            € {{data[indextr].roi.toFixed(2)}}
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
                        {{total_stats.conversion}}
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
                        € {{total_stats.profit.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.roi.toFixed(2)}}
                    </vs-td>
                    <!-- END TOTAL STATS-->
                </template>
            </vs-table>

            <!-- Rules table -->
            <vs-table v-if="view == 'rules'" stripe multiple v-model="selectedRules" :max-items="maxItems" pagination search :data="rules" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="id">ID</vs-th>
                    <vs-th sort-key="name" ref="sort_by_name">Name</vs-th>
                    <vs-th>Campaigns</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <vs-th sort-key="frequency">Frequency</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td class="column-no-padding" :data="data[indextr].name">
                            {{data[indextr].id}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].name">
                            <div @contextmenu.prevent="$refs.rule_context_menu.open($event, data[indextr])">
                                <vs-chip
                                    transparent
                                    class="disabled-object">
                                    <vs-avatar icon-pack="feather" icon="icon-user" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].value.campaign_ids.length">
                            {{data[indextr].value.campaign_ids.length}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].status">
                            <vs-switch
                                :color="(data[indextr].status == 'active') ? 'success' : 'danger'"
                                v-model="data[indextr].status == 'active'"
                                vs-icon-off="pause" vs-icon-on="done"
                                @input="switchStatusRule(data[indextr])">
                                <span slot="on" v-if="data[indextr].status == 'active'">Active</span>
                                <span slot="off">Disabled</span>
                            </vs-switch>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].frequency">
                            {{data[indextr].frequency}}
                        </vs-td>

                    </vs-tr>

                </template>
            </vs-table>

        </div>

        <!-- Edit budget popup -->
        <vs-popup title="Edit budget" :active.sync="showPopup['edit_budget']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{editingObject.name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Budget</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="editingObject.budget" />
                </div>
                <!--
                <div class="vx-col w-full mt-2" v-if="(editingObject.object_type == 'campaign') && (editingObject.hasOwnProperty('cbo')) && (!editingObject.cbo)">
                    <span>* This campaign is non-CBO: this daily budget will be assigned to every adset of this campaign.</span>
                </div>
                -->
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="editBudget" color="primary" type="filled" :disabled="!isValidForm('edit_budget')">Update budget</vs-button>
            </div>
        </vs-popup>

        <!-- Edit bid popup -->
        <vs-popup title="Edit bid" :active.sync="showPopup['edit_bid']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{editingObject.name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Strategy</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select v-model="editingObject.bid_strategy" :disabled="true" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Bid</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="editingObject.conversion_bid" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="editBid" color="primary" type="filled" :disabled="!isValidForm('edit_bid')">Update bid</vs-button>
            </div>
        </vs-popup>

        <!-- Update form cost popup -->
        <vs-popup title="Update form cost" :active.sync="showPopup['update_form_cost']">
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Total amount spent</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    € {{updateFormTotalCost.toFixed(2)}}
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Form</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select
                      autocomplete
                      v-model="updateFormId"
                      class="w-full">
                        <vs-select-item :key="index" :value="item.id" :text="item.id + ' - ' + item.name" v-for="(item, index) in forms" />
                    </vs-select>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Form association</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                   <vs-checkbox v-model="updateFormAssociate">Associate</vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Date</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="updateFormDate" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateFormCost" color="primary" type="filled" :disabled="!isValidForm('update_form_cost')">Update Form Cost</vs-button>
            </div>
        </vs-popup>

        <!-- View ad popup -->
        <vs-popup title="View Ad" :active.sync="showPopup['view_ad']">

            <div>
                <div class="vx-row mb-6 flex items-center">
                    <vs-button class="mr-auto ml-auto">{{ad.name}}</vs-button>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                    </div>
                    <div class="vx-col sm:w-1/2 w-full mt-2">
                        <img v-if="adImage.url != ''" :src="adImage.url" class="w-full">
                        <img v-else src="https://www.tucanomacchine.it/wp-content/uploads/2019/07/480px-No_image_available.svg.png" class="w-full">
                    </div>
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <span>{{ad.ad_text}}</span>
                    </div>
                </div>

                <!--

                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <span><b>{{viewingObject.title}}</b></span>
                    </div>
                </div>
                
                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <vs-table :data="[{ctr:viewingObject.ctr,cpc:viewingObject.cpc,cpa:viewingObject.cpa}]">
                            <template slot="thead">
                                <vs-th sort-key="ctr">CTR</vs-th>
                                <vs-th sort-key="cpc">CPC</vs-th>
                                <vs-th sort-key="cpa">CPA</vs-th>
                                <vs-th>Actions</vs-th>
                            </template>

                            <template slot-scope="{data}">
                                <vs-tr :key="indextr" v-for="(tr, indextr) in data" >
                                    <vs-td v-if="viewingObject.hasOwnProperty('ctr')">
                                        {{data[indextr].ctr.toFixed(2)}}%
                                    </vs-td>
                                    <vs-td v-if="viewingObject.hasOwnProperty('cpc')">
                                        € {{data[indextr].cpc.toFixed(2)}}
                                    </vs-td>
                                    <vs-td v-if="viewingObject.hasOwnProperty('cpa')">
                                        € {{data[indextr].cpa.toFixed(2)}}
                                    </vs-td>
                                    <vs-td>
                                        <div class="flex items-center">
                                            <vs-button :disabled="viewingObject.post_url == ''" @click="openNewTab(viewingObject.post_url)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-facebook"></vs-button>
                                            <vs-button :disabled="viewingObject.url == ''" @click="openNewTab(viewingObject.url)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-link"></vs-button>
                                            <vs-button @click="saveAdBegin(viewingObject)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-save"></vs-button>
                                        </div>
                                    </vs-td>
                                </vs-tr>
                            </template>
                            
                        </vs-table>
                    </div>
                </div>
            </div>

            <div v-else>
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                        <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="viewAdSaving = false"></vs-button>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                    </div>
                    <div class="vx-col sm:w-1/2 w-full mt-2">
                        <vs-select v-model="viewAdSavingProduct" class="w-full select-large" label="Product" v-validate="'required'" autocomplete>
                            <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in productsOptions" class="w-full" />
                        </vs-select>
                    </div>
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                    </div>
                    <div class="vx-col sm:w-1/2 w-full mt-2">
                        <vs-select v-model="viewAdSavingType" class="w-full select-large" label="Type" v-validate="'required'">
                            <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in typesOptions" class="w-full" />
                        </vs-select>
                    </div>
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                    </div>
                </div>

                <div class="vx-row mb-6" v-if="viewAdSavingType == 'video'">
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                    </div>
                    <div class="vx-col sm:w-1/2 w-full mt-2">
                        <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-image" class="w-full" @click="showFilePicker('main')">Upload media</vs-button>
                        <div class="vx-col w-full flex items-center">
                            <span class="ml-auto mr-auto">{{ (fileToUpload.length == 0) ? 'No file selected' : fileToUpload.name }}</span>
                        </div>
                        <div class="vx-col w-full flex items-center">
                            <span class="text-danger">{{ errors.first('file') }}</span>
                        </div>
                    </div>
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                    </div>
                    <input type="file" ref="fileUpload" class="hidden" :accept="viewAdSavingType + '/*'" @change="onFileUploadChange">
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                    </div>
                    <div class="vx-col sm:w-1/2 w-full mt-2">
                        {{ (viewAdSavingType == 'video') ? 'Preview' : 'Media' }}
                    </div>
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                    </div>
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                    </div>
                    <div class="vx-col sm:w-1/2 w-full mt-2">
                        <img ref="viewAdSavingImg" v-if="viewingObject.image_url != ''" :src="viewingObject.image_url" class="w-full">
                        <img ref="viewAdSavingImg" v-else src="https://www.tucanomacchine.it/wp-content/uploads/2019/07/480px-No_image_available.svg.png" class="w-full">
                    </div>
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full mt-2">
                        CTR
                    </div>
                    <div class="vx-col sm:w-1/3 w-full mt-2">
                        CPC
                    </div>
                    <div class="vx-col sm:w-1/3 w-full mt-2">
                        CPA
                    </div>
                    <div class="vx-col sm:w-1/3 w-full mt-2">
                        <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="viewAdSavingCtr" />
                    </div>
                    <div class="vx-col sm:w-1/3 w-full mt-2">
                        <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="viewAdSavingCpc" />
                    </div>
                    <div class="vx-col sm:w-1/3 w-full mt-2">
                        <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="viewAdSavingCpa" />
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <vs-button class="mt-12 mr-auto ml-auto" @click="adSave" color="primary" type="filled" :disabled="!isValidForm('save_ad')">Save Ad</vs-button>
                </div>
                -->
            </div>
            
        </vs-popup>
 
        <!-- Create advertiser popup -->
        <vs-popup title="Create advertiser" :active.sync="showPopup['create_advertiser']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{advertiser.name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="advertiser.name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>ID</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="advertiser.id" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createAdvertiser" color="primary" type="filled" :disabled="!isValidForm('create_advertiser')">Create advertiser</vs-button>
            </div>
        </vs-popup>

        <!-- Edit advertiser popup -->
        <vs-popup title="Edit advertiser" :active.sync="showPopup['edit_advertiser']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{advertiser.name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="advertiser.name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>ID</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="advertiser.id" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateAdvertiser" color="primary" type="filled" :disabled="!isValidForm('edit_advertiser')">Update advertiser</vs-button>
            </div>
        </vs-popup>

        <!-- Create rule popup -->
        <vs-popup title="Create rule" :active.sync="showPopup['create_rule']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{rule.name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="rule.name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Frequency</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="rule.frequency" class="w-full select-large">
                        <vs-select-item :key="f.value" :value="f.value" :text="f.label" v-for="(f, fIndex) in ruleFrequencyOptions" class="w-full" />
                    </vs-select>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Advertiser</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="rule.advertiser_id" class="w-full select-large" @input="refreshCampaignsOptions()">
                        <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in advertisers" class="w-full" />
                    </vs-select>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Campaigns</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete multiple v-model="rule.campaign_ids" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in campaignsOptions" class="w-full" />
                    </vs-select>
                </div>
            </div>

            <hr>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Time</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="rule.time" class="w-full select-large">
                        <vs-select-item :key="tIndex" :value="t.value" :text="t.label" v-for="(t, tIndex) in ruleTimeOptions" class="w-full" />
                    </vs-select>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                    <span>Rules</span>
                </div>
            </div>

            <div class="vx-row mb-6" :key="'rule_' + ruleIndex" v-for="(ruleItem, ruleIndex) in rule.rules">
                <div class="vx-col sm:w-2/5 w-full">
                    <vs-select autocomplete v-model="rule.rules[ruleIndex].metric" class="w-full select-large">
                        <vs-select-item :key="m.value" :value="m.value" :text="m.label" v-for="(m, mIndex) in ruleMetricOptions" class="w-full" />
                    </vs-select>
                </div>
                <div class="vx-col sm:w-1/5 w-full">
                    <vs-select autocomplete v-model="rule.rules[ruleIndex].comparison" class="w-full select-large">
                        <vs-select-item :key="c" :value="c" :text="c" v-for="(c, cIndex) in ruleComparisonOptions" class="w-full" />
                    </vs-select>
                </div>
                <div class="vx-col sm:w-1/5 w-full">
                    <vs-input class="w-full" icon-no-border v-model="rule.rules[ruleIndex].value" />
                </div>
                <div class="vx-col sm:w-1/5 w-full">
                    <vs-button class="mr-auto ml-auto" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="removeRule(ruleIndex)"></vs-button>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="addRule"></vs-button>
            </div>

            <hr>

            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                    <span>Actions</span>
                </div>
            </div>

            <div class="vx-row mb-6" :key="'action_' + actionIndex" v-for="(actionItem, actionIndex) in rule.actions">
                <div class="vx-col sm:w-2/5 w-full">
                    <vs-select autocomplete v-model="rule.actions[actionIndex].type" class="w-full select-large">
                        <vs-select-item :key="a.value" :value="a.value" :text="a.label" v-for="(a, aIndex) in ruleActionOptions" class="w-full" />
                    </vs-select>
                </div>
                <div class="vx-col sm:w-1/5 w-full">
                    <vs-input class="w-full" icon-no-border v-model="rule.actions[actionIndex].value" />
                </div>
                <div class="vx-col sm:w-1/5 w-full">
                    <vs-input class="w-full" icon-no-border v-model="rule.actions[actionIndex].max_value" />
                </div>
                <div class="vx-col sm:w-1/5 w-full">
                    <vs-button class="mr-auto ml-auto" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="removeAction(actionIndex)"></vs-button>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="addAction"></vs-button>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createRule" color="primary" type="filled" :disabled="!isValidForm('create_rule')">Create rule</vs-button>
            </div>
        </vs-popup>

        <!-- Edit rule popup -->
        <vs-popup title="Edit rule" :active.sync="showPopup['edit_rule']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{rule.name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="rule.name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Frequency</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="rule.frequency" class="w-full select-large">
                        <vs-select-item :key="f.value" :value="f.value" :text="f.label" v-for="(f, fIndex) in ruleFrequencyOptions" class="w-full" />
                    </vs-select>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Advertiser</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="rule.advertiser_id" class="w-full select-large" @input="refreshCampaignsOptions()">
                        <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in advertisers" class="w-full" />
                    </vs-select>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Campaigns</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete multiple v-model="rule.campaign_ids" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in campaignsOptions" class="w-full" />
                    </vs-select>
                </div>
            </div>

            <hr>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Time</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="rule.time" class="w-full select-large">
                        <vs-select-item :key="tIndex" :value="t.value" :text="t.label" v-for="(t, tIndex) in ruleTimeOptions" class="w-full" />
                    </vs-select>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                    <span>Rules</span>
                </div>
            </div>

            <div class="vx-row mb-6" :key="'rule_' + ruleIndex" v-for="(ruleItem, ruleIndex) in rule.rules">
                <div class="vx-col sm:w-2/5 w-full">
                    <vs-select autocomplete v-model="rule.rules[ruleIndex].metric" class="w-full select-large">
                        <vs-select-item :key="m.value" :value="m.value" :text="m.label" v-for="(m, mIndex) in ruleMetricOptions" class="w-full" />
                    </vs-select>
                </div>
                <div class="vx-col sm:w-1/5 w-full">
                    <vs-select autocomplete v-model="rule.rules[ruleIndex].comparison" class="w-full select-large">
                        <vs-select-item :key="c" :value="c" :text="c" v-for="(c, cIndex) in ruleComparisonOptions" class="w-full" />
                    </vs-select>
                </div>
                <div class="vx-col sm:w-1/5 w-full">
                    <vs-input class="w-full" icon-no-border v-model="rule.rules[ruleIndex].value" />
                </div>
                <div class="vx-col sm:w-1/5 w-full">
                    <vs-button class="mr-auto ml-auto" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="removeRule(ruleIndex)"></vs-button>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="addRule"></vs-button>
            </div>

            <hr>

            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                    <span>Actions</span>
                </div>
            </div>

            <div class="vx-row mb-6" :key="'action_' + actionIndex" v-for="(actionItem, actionIndex) in rule.actions">
                <div class="vx-col sm:w-2/5 w-full">
                    <vs-select autocomplete v-model="rule.actions[actionIndex].type" class="w-full select-large">
                        <vs-select-item :key="a.value" :value="a.value" :text="a.label" v-for="(a, aIndex) in ruleActionOptions" class="w-full" />
                    </vs-select>
                </div>
                <div class="vx-col sm:w-1/5 w-full">
                    <vs-input class="w-full" icon-no-border v-model="rule.actions[actionIndex].value" />
                </div>
                <div class="vx-col sm:w-1/5 w-full">
                    <vs-input class="w-full" icon-no-border v-model="rule.actions[actionIndex].max_value" />
                </div>
                <div class="vx-col sm:w-1/5 w-full">
                    <vs-button class="mr-auto ml-auto" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="removeAction(actionIndex)"></vs-button>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="addAction"></vs-button>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateRule" color="primary" type="filled" :disabled="!isValidForm('edit_rule')">Update rule</vs-button>
            </div>
        </vs-popup>

        <!-- Bulk edit rule popup -->
        <vs-popup title="Bulk edit rule" :active.sync="showPopup['bulk_edit_rule']">

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Advertiser</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="rule.advertiser_id" class="w-full select-large" @input="refreshCampaignsOptions()">
                        <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in advertisers" class="w-full" />
                    </vs-select>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Campaigns</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete multiple v-model="rule.campaign_ids" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in campaignsOptions" class="w-full" />
                    </vs-select>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="bulkUpdateRules" color="primary" type="filled" :disabled="!isValidForm('bulk_edit_rule')">Bulk update</vs-button>
            </div>
        </vs-popup>

        <!-- Campaign ids list view popup -->
        <vs-popup title="Campaign ids list" :active.sync="showPopup['campaign_ids']">
            <div class="vx-row mb-6 flex items-center">
                <h2>Campaign ids list</h2>
            </div>

            <div class="vx-row mb-6">
                <vs-col v-for="(item, index) in campaignsList" :key="index">
                    <vs-checkbox v-model="campaignsListUpdated" :vs-value="item.id">{{ item.name }}</vs-checkbox>
                </vs-col>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateCampaignsList" color="primary" type="filled">Update campaigns list</vs-button>
            </div>
        </vs-popup>

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

function forceJson(o) {
  if (typeof o == 'string') {
    return JSON.parse(o)
  }
  return(o)
}

function intersect(a, b) {
  let setA = new Set(a)
  let setB = new Set(b)
  let intersection = new Set([...setA].filter(x => setB.has(x)))
  return Array.from(intersection)
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
      view: 'advertisers',

      selectedAdvertisers: [],
      selectedCampaigns: [],
      selectedAdgroups: [],
      selectedAds: [],
      selectedRules: [],

      deleted: [],

      maxItems: 20,

      advertisersLoaded: false,
      campaignsLoaded: false,
      adgroupsLoaded: false,
      adsLoaded: false,
      rulesLoaded: false,

      advertisers: [],
      campaigns: [],
      adgroups: [],
      ads: [],
      rules: [],

      advertiserSwitch: null,
      currentAdvertiserId: null,
      currentCampaignId: null,
      currentAdgroupId: null,

      rule: {
        name: '',
        advertiser_id: '',
        campaign_ids: [],
        rules: [],
        actions: [],
        frequency: 'hourly',
        time: 'lifetime',
      },
      campaignsOptions: [],
      currAdvId: null,

      total_stats: {
        spent: 0,
        conversion: 0,
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
        'CAMPAIGN_STATUS_DISABLE'              : 'warning',
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
        'ADGROUP_STATUS_CAMPAIGN_DISABLE'      : 'warning',
        'ADGROUP_STATUS_DISABLE'               : 'warning',
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
        'AD_STATUS_CAMPAIGN_DISABLE'     : 'warning',
        'AD_STATUS_ADGROUP_DISABLE'      : 'warning',
        'AD_STATUS_DISABLE'              : 'warning',
        'AD_STATUS_DELIVERY_OK'          : 'success',
        'AD_STATUS_ALL'                  : 'danger',
        'AD_STATUS_NOT_DELETE'           : 'danger',
      },

      effectiveStatus: {
        'CAMPAIGN_STATUS_DELETE'               : 'DISABLED',
        'CAMPAIGN_STATUS_ADVERTISER_AUDIT_DENY': 'DISABLED',
        'CAMPAIGN_STATUS_ADVERTISER_AUDIT'     : 'REVIEW',
        'ADVERTISER_CONTRACT_PENDING'          : 'REVIEW',
        'CAMPAIGN_STATUS_BUDGET_EXCEED'        : 'ACTIVE',
        'CAMPAIGN_STATUS_DISABLE'              : 'PAUSED',
        'CAMPAIGN_STATUS_ENABLE'               : 'ACTIVE',
        'CAMPAIGN_STATUS_ALL'                  : 'DISABLED',
        'CAMPAIGN_STATUS_NOT_DELETE'           : 'DISABLED',

        'ADGROUP_STATUS_DELETE'                : 'DISABLED',
        'ADGROUP_STATUS_CAMPAIGN_DELETE'       : 'DISABLED',
        'ADGROUP_STATUS_ADVERTISER_AUDIT_DENY' : 'DISABLED',
        'ADGROUP_STATUS_ADVERTISER_AUDIT'      : 'REVIEW',
        'ADVERTISER_CONTRACT_PENDING'          : 'REVIEW',
        'ADGROUP_STATUS_CAMPAIGN_EXCEED'       : 'ACTIVE',
        'ADGROUP_STATUS_BUDGET_EXCEED'         : 'ACTIVE',
        'ADGROUP_STATUS_BALANCE_EXCEED'        : 'DISABLED',
        'ADGROUP_STATUS_AUDIT_DENY'            : 'DISABLED',
        'ADGROUP_STATUS_REAUDIT'               : 'REVIEW',
        'ADGROUP_STATUS_AUDIT'                 : 'REVIEW',
        'ADGROUP_STATUS_CREATE'                : 'ACTIVE',
        'ADGROUP_STATUS_NOT_START'             : 'DISABLED',
        'ADGROUP_STATUS_TIME_DONE'             : 'ACTIVE',
        'ADGROUP_STATUS_CAMPAIGN_DISABLE'      : 'PAUSED',
        'ADGROUP_STATUS_DISABLE'               : 'PAUSED',
        'ADGROUP_STATUS_DELIVERY_OK'           : 'ACTIVE',
        'ADGROUP_STATUS_SHADOW_ADGROUP_REAUDIT': 'REVIEW',
        'ADGROUP_STATUS_ALL'                   : 'DISABLED',
        'ADGROUP_STATUS_NOT_DELETE'            : 'DISABLED',

        'AD_STATUS_CAMPAIGN_DELETE'            : 'DISABLED',
        'AD_STATUS_ADGROUP_DELETE'             : 'DISABLED',
        'AD_STATUS_DELETE'                     : 'DISABLED',
        'AD_STATUS_ADVERTISER_AUDIT_DENY'      : 'DISABLED',
        'AD_STATUS_ADVERTISER_AUDIT'           : 'REVIEW',
        'ADVERTISER_CONTRACT_PENDING'          : 'REVIEW',
        'AD_STATUS_BALANCE_EXCEED'             : 'DISABLED',
        'AD_STATUS_CAMPAIGN_EXCEED'            : 'ACTIVE',
        'AD_STATUS_BUDGET_EXCEED'              : 'ACTIVE',
        'AD_STATUS_AUDIT'                      : 'REVIEW',
        'AD_STATUS_REAUDIT'                    : 'REVIEW',
        'AD_STATUS_AUDIT_DENY'                 : 'DISABLED',
        'AD_STATUS_ADGROUP_AUDIT_DENY'         : 'DISABLED',
        'AD_STATUS_NOT_START'                  : 'DISABLED',
        'AD_STATUS_DONE'                       : 'ACTIVE',
        'AD_STATUS_CAMPAIGN_DISABLE'           : 'PAUSED',
        'AD_STATUS_ADGROUP_DISABLE'            : 'PAUSED',
        'AD_STATUS_DISABLE'                    : 'PAUSED',
        'AD_STATUS_DELIVERY_OK'                : 'ACTIVE',
        'AD_STATUS_ALL'                        : 'DISABLED',
        'AD_STATUS_NOT_DELETE'                 : 'DISABLED',
      },

      bidTypes: [
        'BID_TYPE_CUSTOM',
        'BID_TYPE_MAX_CONVERSION',
        'BID_TYPE_NO_BID',
      ],

      showPopup: {
        'create_advertiser': false,
        'edit_advertiser': false,
        'edit_budget': false,
        'edit_bid': false,
        'update_form_cost': false,
        'view_ad': false,
        'create_rule': false,
        'edit_rule': false,
        'bulk_edit_rule': false,
        'campaign_ids': false,
      },

      editingObject: '',
      editingAdvertiser: null,
      advertiser: {
        id: '',
        name: '',
      },

      updateFormId: '',
      updateFormTotalCost: 0,
      updateFormAssociate: false,
      updateFormDate: '',

      formsLoaded: false,
      forms: [],

      ad: {},
      adImage: {},

      ruleFrequencyOptions: [
        { value: 'every_five_minutes',    label: 'Every 5 minutes' },
        { value: 'every_ten_minutes',     label: 'Every 10 minutes' },
        { value: 'every_fifteen_minutes', label: 'Every 15 minutes' },
        { value: 'every_thirty_minutes',  label: 'Every 30 minutes' },
        { value: 'hourly',                label: 'Every hour' },
        { value: 'every_three_hours',     label: 'Every 3 hours' },
        { value: 'daily',                 label: 'Daily' },
      ],

      ruleTimeOptions: [
        { value: 'current_day',        label: 'Current day' },
        { value: 'last_1_days',  label: 'Last day' },
        { value: 'last_2_days',  label: 'Last 2 days' },
        { value: 'last_3_days',  label: 'Last 3 days' },
        { value: 'last_7_days',  label: 'Last 7 days' },
        { value: 'last_14_days', label: 'Last 14 days' },
        { value: 'last_30_days', label: 'Last 30 days' },
        { value: 'lifetime',     label: 'Lifetime' },
      ],

      ruleMetricOptions: [
        { value: 'impressions',         label: 'impressions' },
        { value: 'spend',               label: 'spent' },
        { value: 'cost_per_conversion', label: 'cost_per_conversion' },
        { value: 'conversion',          label: 'conversions' },
        { value: 'conversion_rate',     label: 'CR' },
        //{ value: 'clicks',      label: 'clicks' }, // TODO: We don't have clicks metric right now

        // TODO: Special metrics
        /*
        { value: 'roi',                 label: 'roi' },
        { value: 'profit',              label: 'profit' },
        */
      ],

      ruleComparisonOptions: [
        '<', '<=', '==', '>=', '>',
      ],

      ruleActionOptions: [
        { value: 'inc_bid',      label: 'Increase bid' },
        { value: 'dec_bid',      label: 'Decrease bid' },
        { value: 'inc_budget',   label: 'Increase budget' },
        { value: 'dec_budget',   label: 'Decrease budget' },
        { value: 'enable',       label: 'Enable' },
        { value: 'disable',      label: 'Disable' },
      ],

      campaignsList: [],
      campaignsListUpdated: [],
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
      /*
       * TODO: BUG:
       * How to correctly calculate CR, CPM etc..?
       */
      let spent = 0
      let conversion = 0
      let cpa = 0
      let impressions = 0
      let clicks = 0
      let cr = 0
      let cpm = 0
      let ctr = 0
      let cpc = 0
      let budget = 0.0
      let profit = 0.0
      let roi = 0.0

      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]
        spent += obj.spent
        conversion += obj.conversion
        impressions += obj.impressions
        //clicks += obj.clicks
        budget += obj.budget
      }
      cpa = (conversion > 0) ? (spent/conversion) : spent
      cr  = (clicks > 0) ? ((conversion/clicks)*100) : ((conversion > 0) ? 9999.99 : 0)
      // TODO
      //cpm = (impressions > 0) ? ((spent*1000)/impressions) : (spent*1000)
      ctr = (impressions > 0) ? ((clicks/impressions)*100) : ((clicks > 0) ? 9999.99 : 0)
      cpc = (clicks > 0) ? (spent/clicks) : spent

      this.total_stats = {
        spent: spent,
        conversion: conversion,
        cpa: cpa,
        impressions: impressions,
        clicks: clicks,
        cr: cr,
        cpm: cpm,
        ctr: ctr,
        cpc: cpc,
        budget: budget,
        profit: profit,
        roi: roi,
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
    async loadAdvertisers() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let params = {
      }

      this.advertisers = []
      const response = await this.$http.get('/api/3rd/tiktok/advertisers', { headers: headers, params: params })
      this.advertisers = response.data.data

      this.advertisersLoaded = true

      this.$vs.loading.close()
    },
    async loadCampaigns() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let params = {
      }

      if (this.filters.date_from != '') {
        params.filter_date_from = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        params.filter_date_to = this.filters.date_to
      }

      if (this.currentAdvertiserId !== null) {
        params.advertiser_id = this.currentAdvertiserId
      }

      this.campaigns = []
      const response = await this.$http.get('/api/3rd/tiktok/campaigns', { headers: headers, params: params })

      for (let objectIndex = 0; objectIndex < response.data.data.length; objectIndex++) {
        let object = response.data.data[objectIndex]

        if (this.filters.status != '' && object.status != this.filters.status) {
          continue
        }

        let objectToPush  = object
        objectToPush.id   = object.campaign_id
        objectToPush.name = object.campaign_name
        objectToPush.effective_status = this.effectiveStatus[object.status]

        let spent = 0.0
        let conversion = 0
        let cpa = 0.0
        let cr = 0.0
        let cpm = 0.0
        let impressions = 0
        let profit = 0.0
        let roi = 0.0

        if (object.hasOwnProperty('reports') && object.reports.hasOwnProperty('metrics')) {
          let reports = object.reports
          if (reports.metrics.hasOwnProperty('spend')) {
            spent = parseFloat(reports.metrics.spend)
          }
          if (reports.metrics.hasOwnProperty('conversion')) {
            conversion = parseInt(reports.metrics.conversion)
          }
          if (reports.metrics.hasOwnProperty('cost_per_conversion')) {
            cpa = parseFloat(reports.metrics.cost_per_conversion)
          }
          if (reports.metrics.hasOwnProperty('conversion_rate')) {
            cr = parseFloat(reports.metrics.conversion_rate)
          }
          if (reports.metrics.hasOwnProperty('cpm')) {
            cpm = parseFloat(reports.metrics.cpm)
          }
          if (reports.metrics.hasOwnProperty('impressions')) {
            impressions = parseFloat(reports.metrics.impressions)
          }
        }

        objectToPush.spent  = spent
        objectToPush.conversion = conversion
        objectToPush.cpa    = cpa
        objectToPush.cr     = cr
        objectToPush.cpm    = cpm
        objectToPush.profit = profit
        objectToPush.roi    = roi

        this.campaigns.push(objectToPush)
      }

      this.campaignsLoaded = true

      this.$vs.loading.close()
    },
    async loadAdgroups() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let params = {
      }

      if (this.filters.date_from != '') {
        params.filter_date_from = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        params.filter_date_to = this.filters.date_to
      }

      if (this.currentCampaignId !== null) {
        params.filter_campaign = this.currentCampaignId
      }
      if (this.currentAdvertiserId !== null) {
        params.advertiser_id = this.currentAdvertiserId
      }

      this.adgroups = []
      const response = await this.$http.get('/api/3rd/tiktok/adgroups', { headers: headers, params: params })

      for (let objectIndex = 0; objectIndex < response.data.data.length; objectIndex++) {
        let object = response.data.data[objectIndex]

        if (this.filters.status != '' && object.status != this.filters.status) {
          continue
        }

        let objectToPush      = object
        objectToPush.id     = object.adgroup_id
        objectToPush.name   = object.adgroup_name
        objectToPush.effective_status = this.effectiveStatus[object.status]

        let spent = 0.0
        let conversion = 0
        let cpa = 0.0
        let cr = 0.0
        let cpm = 0.0
        let impressions = 0
        let profit = 0.0
        let roi = 0.0

        if (object.hasOwnProperty('reports') && object.reports.hasOwnProperty('metrics')) {
          let reports = object.reports
          if (reports.metrics.hasOwnProperty('spend')) {
            spent = parseFloat(reports.metrics.spend)
          }
          if (reports.metrics.hasOwnProperty('conversion')) {
            conversion = parseInt(reports.metrics.conversion)
          }
          if (reports.metrics.hasOwnProperty('cost_per_conversion')) {
            cpa = parseFloat(reports.metrics.cost_per_conversion)
          }
          if (reports.metrics.hasOwnProperty('conversion_rate')) {
            cr = parseFloat(reports.metrics.conversion_rate)
          }
          if (reports.metrics.hasOwnProperty('cpm')) {
            cpm = parseFloat(reports.metrics.cpm)
          }
          if (reports.metrics.hasOwnProperty('impressions')) {
            impressions = parseFloat(reports.metrics.impressions)
          }
        }

        objectToPush.spent  = spent
        objectToPush.conversion = conversion
        objectToPush.cpa    = cpa
        objectToPush.cr     = cr
        objectToPush.cpm    = cpm
        objectToPush.profit = profit
        objectToPush.roi    = roi

        this.adgroups.push(objectToPush)
      }

      this.adgroupsLoaded = true

      this.$vs.loading.close()
    },
    async loadAds() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let params = {
      }

      if (this.filters.date_from != '') {
        params.filter_date_from = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        params.filter_date_to = this.filters.date_to
      }

      if (this.currentAdgroupId !== null) {
        params.filter_adgroup = this.currentAdgroupId
      }
      if (this.currentAdvertiserId !== null) {
        params.advertiser_id = this.currentAdvertiserId
      }

      this.ads = []
      const response = await this.$http.get('/api/3rd/tiktok/ads', { headers: headers, params: params })

      for (let objectIndex = 0; objectIndex < response.data.data.length; objectIndex++) {
        let object = response.data.data[objectIndex]

        if (this.filters.status != '' && object.status != this.filters.status) {
          continue
        }

        let objectToPush    = object
        objectToPush.id     = object.ad_id
        objectToPush.name   = object.ad_name
        objectToPush.effective_status = this.effectiveStatus[object.status]

        let spent = 0.0
        let conversion = 0
        let cpa = 0.0
        let cr = 0.0
        let cpm = 0.0
        let impressions = 0
        let profit = 0.0
        let roi = 0.0

        if (object.hasOwnProperty('reports') && object.reports.hasOwnProperty('metrics')) {
          let reports = object.reports
          if (reports.metrics.hasOwnProperty('spend')) {
            spent = parseFloat(reports.metrics.spend)
          }
          if (reports.metrics.hasOwnProperty('conversion')) {
            conversion = parseInt(reports.metrics.conversion)
          }
          if (reports.metrics.hasOwnProperty('cost_per_conversion')) {
            cpa = parseFloat(reports.metrics.cost_per_conversion)
          }
          if (reports.metrics.hasOwnProperty('conversion_rate')) {
            cr = parseFloat(reports.metrics.conversion_rate)
          }
          if (reports.metrics.hasOwnProperty('cpm')) {
            cpm = parseFloat(reports.metrics.cpm)
          }
          if (reports.metrics.hasOwnProperty('impressions')) {
            impressions = parseFloat(reports.metrics.impressions)
          }
        }

        objectToPush.spent  = spent
        objectToPush.conversion = conversion
        objectToPush.cpa    = cpa
        objectToPush.cr     = cr
        objectToPush.cpm    = cpm
        objectToPush.profit = profit
        objectToPush.roi    = roi

        this.ads.push(objectToPush)
      }

      this.adsLoaded = true

      this.$vs.loading.close()
    },
    async loadRules() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.rules = []
      const response = await this.$http.get('/api/3rd/tiktok/rules', { headers: headers })
      this.rules = response.data.data

      // Sort rules by name (by defaul)
      this.rules.sort(function (a, b) {
        if (a.name < b.name) {
          return -1
        } else if (a.name > b.name) {
          return 1
        } else {
          return 0
        }
      })

      this.rulesLoaded = true

      this.$vs.loading.close()
    },
    async applyFilters() {
      this.selectedAdvertisers = []
      this.selectedCampaigns = []
      this.selectedAdgroups = []
      this.selectedAds = []
      this.selectedRules = []

      this.advertisersLoaded = false
      this.campaignsLoaded = false
      this.adgroupsLoaded = false
      this.adsLoaded = false
      this.rulesLoaded = false

      this.changeView(this.view, this.currentAdvertiserId)

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
    async changeView(view, parentId = null) {
      // TODO: to fix
      //this.onPageChanged(1)
      //this.gridApi.paginationGoToPage(0)

      if ((view == 'adgroups' || view == 'ads') &&
        (this.currentAdvertiserId == null)) {
        return
      }
      if ((view == 'campaigns') && (parentId == null && this.currentAdvertiserId == null)) {
        return
      }

      this.view = view

      if (view == 'advertisers') {
        if (!this.advertisersLoaded) {
          await this.loadAdvertisers()
        }
      } else if (view == 'campaigns') {
        if (!this.campaignsLoaded || this.currentAdvertiserId != parentId) {
          if (parentId != null) {
            this.currentAdvertiserId = parentId
            this.advertiserSwitch = this.currentAdvertiserId
          }
          await this.loadCampaigns()
        }
      } else if (view == 'adgroups') {
        if (!this.adgroupsLoaded || this.currentCampaignId != parentId) {
          this.currentCampaignId = parentId
          await this.loadAdgroups()
        }
      } else if (view == 'ads') {
        if (!this.adsLoaded || this.currentAdgroupId != parentId) {
          this.currentAdgroupId = parentId
          await this.loadAds()
        }
      } else if (view == 'rules') {
        if (!this.rulesLoaded) await this.loadRules()
      }

      this.calculateStats()
    },
    async switchStatus(type, id) {
      this.$vs.loading()

      let newStatus = 'DISABLE'

      let objIndex = this.getObjectIndexById(this.objects, id)
      let obj = this.objects[objIndex]
      if (obj.effective_status != 'ACTIVE') {
        newStatus = 'ENABLE'
      }

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let params = {
        status: newStatus,
      }
      if (this.currentAdvertiserId !== null) {
        params.advertiser_id = this.currentAdvertiserId
      }

      try {
        if(type == 'campaign') {
          const response = await this.$http.put('/api/3rd/tiktok/campaigns/' + id, params, { headers: headers })
        } else if(type == 'adgroup') {
          const response = await this.$http.put('/api/3rd/tiktok/adgroups/' + id, params, { headers: headers })
        } else if(type == 'ad') {
          const response = await this.$http.put('/api/3rd/tiktok/ads/' + id, params, { headers: headers })
        }
      } catch (error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot switch status',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()

      this.applyFilters()
    },
    async loadAdImage(id) {
      this.$vs.loading()

      let result = false
      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        let params = {
          image_id: id,
        }
        if (this.currentAdvertiserId !== null) {
          params.advertiser_id = this.currentAdvertiserId
        }
        const response = await this.$http.get('/api/3rd/tiktok/images', { headers: headers, params: params })
        this.adImage = response.data.data
        result = true
      } catch(error) {
        result = false
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot load ad image',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
      return result
    },
    async viewAdPopup(ad) {
      const adImageLoaded = await this.loadAdImage(ad.image_ids[0])
      if(adImageLoaded) {
        this.ad = ad
        this.showPopup['view_ad'] = true
      }
    },
    async showUpdateFormCost() {
      if (this.selectedCampaigns.length == 0) {
        return
      }

      this.updateFormTotalCost = 0
      for (let i = 0; i < this.selectedCampaigns.length; i++) {
        let campaign = this.selectedCampaigns[i]
        this.updateFormTotalCost += parseFloat(campaign.spent)
      }
      this.updateFormDate = this.filters.date_to

      if (!this.formsLoaded) {
        this.$vs.loading()
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }

        // Retrieve forms
        const response = await this.$http.get('/api/v1/forms', { headers })
        this.$vs.loading.close()
        this.forms = response.data.data
        if (this.forms.length > 0) {
          this.updateFormId = this.forms[0].id
        }
        this.formsLoaded = true
        this.showPopup['update_form_cost'] = true
      } else {
        this.showPopup['update_form_cost'] = true
      }
    },
    isValidForm(type) {
      if (type == 'create_advertiser' || type == 'edit_advertiser') {
        if (this.advertiser.id != '' && this.advertiser.name != '') {
          return true
        }
      } else if (type == 'edit_budget') {
        if (this.editingObject.hasOwnProperty('budget') &&
            this.editingObject.budget > 0) {
          return true
        }
      } else if (type == 'edit_bid') {
        if (this.editingObject.hasOwnProperty('conversion_bid') &&
            this.editingObject.conversion_bid > 0) {
          return true
        }
      } else if (type == 'update_form_cost') {
        if (this.updateFormId != '' && this.updateFormId > 0 &&
            this.updateFormTotalCost > 0 && this.updateFormDate != '') {
          return true
        }
      } else if (type == 'create_rule' || type == 'edit_rule') {
        if (this.rule.name != '' && this.rule.frequency != '' &&
            this.rule.advertiser_id != '' &&
            this.rule.campaign_ids.length >= 0 &&
            this.rule.rules.length > 0 && this.rule.time != '') {
          return true
        }
      } else if (type == 'bulk_edit_rule') {
        if (this.rule.advertiser_id != '' &&
            this.rule.campaign_ids.length >= 0) {
          return true
        }
      }
      return false
    },
    async updateFormCost() {
      this.$vs.loading()
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        day: this.updateFormDate,
        cost_to_add: this.updateFormTotalCost,
      }

      // Form cost update
      await this.$http.put('/api/v1/forms/' + this.updateFormId + '/stats-add', data, { headers })

      // Form association
      /*
      if (this.updateFormAssociate) {
        let fbCampaigns = []
        for (let i = 0; i < this.selectedCampaigns.length; i++) {
          fbCampaigns.push(this.selectedCampaigns[i].id)
        }
        data = {
          fb_campaigns: fbCampaigns,
          form_id: this.updateFormId,
        }
        const fbCampaignsResponse = await this.$http.post('/api/3rd/fb-campaigns/associate', data, { headers })
        for (let fbCampaignIndex = 0; fbCampaignIndex < fbCampaignsResponse.data.data.length; fbCampaignIndex++) {
          let fbCampaign = fbCampaignsResponse.data.data[fbCampaignIndex]
          this.campaigns[this.getObjectIndexById(this.campaigns, fbCampaign.id)].form = fbCampaign.form
        }
      }
      */

      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Form total cost updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.showPopup['update_form_cost'] = false
    },
    editBudgetPopup(data) {
      this.editingObject = data
      this.showPopup['edit_budget'] = true
    },
    editBidPopup(data) {
      this.editingObject = data
      if (!this.editingObject.hasOwnProperty('conversion_bid'))
        this.editingObject.conversion_bid = 0.0
      this.editingObject.bid_strategy = 'BID_TYPE_CUSTOM'
      this.showPopup['edit_bid'] = true
    },
    async editBudget() {
      this.$vs.loading()

      let budget = parseInt(this.editingObject.budget)

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let params = {
        budget: budget,
      }
      if (this.currentAdvertiserId !== null) {
        params.advertiser_id = this.currentAdvertiserId
      }

      try {
        const response = await this.$http.put('/api/3rd/tiktok/adgroups/' + this.editingObject.id, params, { headers: headers })
        this.$vs.notify({
          title: 'Success',
          text: 'Budget updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
        this.showPopup['edit_budget'] = false
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update budget',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }
      this.$vs.loading.close()
    },
    async editBid() {
      this.$vs.loading()

      let bid = parseInt(this.editingObject.conversion_bid)
      let bidStrategy = this.editingObject.bid_strategy

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let params = {
        bid: bid,
        bid_strategy: bidStrategy,
      }
      if (this.currentAdvertiserId !== null) {
        params.advertiser_id = this.currentAdvertiserId
      }

      try {
        const response = await this.$http.put('/api/3rd/tiktok/adgroups/' + this.editingObject.id, params, { headers: headers })
        this.$vs.notify({
          title: 'Success',
          text: 'Budget updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
        this.showPopup['edit_bid'] = false
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update bid',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    showCreateRuleForm() {
      this.rule = {
        name: '',
        advertiser_id: '',
        campaign_ids: [],
        rules: [],
        actions: [],
        frequency: 'hourly',
        time: 'lifetime',
      }
      this.addRule()
      this.addAction()
      this.showPopup['create_rule'] = true
    },
    showEditRuleForm(rule) {
      if (this.advertisers.length == 0) {
        return
      }

      let ruleTime = 'lifetime'
      if (rule.value.hasOwnProperty('time')) {
        ruleTime = rule.value.time
      }

      this.rule = {
        id: rule.id,
        name: rule.name,
        advertiser_id: rule.value.advertiser_id,
        campaign_ids: forceJson(rule.value.campaign_ids),
        rules: forceJson(rule.value.rules),
        actions: forceJson(rule.value.actions),
        frequency: rule.frequency,
        time: ruleTime,
      }
      this.currAdvId = rule.value.advertiser_id

      this.showPopup['edit_rule'] = true
      this.refreshCampaignsOptions()
    },
    addRule() {
      this.rule.rules.push({
        metric: 'impressions',
        comparison: '<',
        value: 10,
      })
    },
    removeRule(index) {
      this.rule.rules.splice(index, 1)
    },
    addAction() {
      this.rule.actions.push({
        type: 'inc_bid',
        value: 1,
        max_value: 10,
      })
    },
    removeAction(index) {
      this.rule.actions.splice(index, 1)
    },
    async refreshCampaignsOptions() {
      this.$vs.loading()

      if (this.rule.advertiser_id != this.currAdvId) {
        this.rule.campaign_ids = []
      }

      this.campaignsOptions = []
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let params = {
        reports: 0,
        advertiser_id: this.rule.advertiser_id,
      }

      this.campaignsOptions = []
      const response = await this.$http.get('/api/3rd/tiktok/campaigns', { headers: headers, params: params })

      for (let objectIndex = 0; objectIndex < response.data.data.length; objectIndex++) {
        let object = response.data.data[objectIndex]

        if (this.filters.status != '' && object.status != this.filters.status) {
          continue
        }

        let objectToPush  = object
        objectToPush.id   = object.campaign_id
        objectToPush.name = object.campaign_name

        this.campaignsOptions.push(objectToPush)
      }

      this.currAdvId = this.rule.advertiser_id

      this.$vs.loading.close()
    },
    async createRule() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        name: this.rule.name,
        advertiser_id: this.rule.advertiser_id,
        campaign_ids: this.rule.campaign_ids,
        rules: JSON.stringify(this.rule.rules),
        actions: JSON.stringify(this.rule.actions),
        frequency: this.rule.frequency,
        time: this.rule.time,
      }
      try {
        const response = await this.$http.post('/api/3rd/tiktok/rules', data, { headers })
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot create rule',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
        return
      }

      this.showPopup['create_rule'] = false
      this.$vs.notify({
        title: 'Success',
        text: 'Rule created successfully',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.applyFilters()

      this.$vs.loading.close()
    },
    async cloneRule(rule) {
      let value = rule.value
      if (typeof(value) == 'string') {
        value = JSON.parse(value)
      }

      let name = rule.name + ' - Copy'
      let advertiserId = value.advertiser_id
      let campaignIds = value.campaign_ids
      if (typeof(campaignIds) == 'string') {
        campaignIds = JSON.parse(campaignIds)
      }
      let rules = value.rules
      if (typeof(rules) == 'string') {
        rules = JSON.parse(rules)
      }
      let actions = value.actions
      if (typeof(actions) == 'string') {
        actions = JSON.parse(actions)
      }
      let frequency = rule.frequency
      let time = ''
      if (value.hasOwnProperty('time')) {
        time = value.time
      }

      this.rule = {
        name: name,
        advertiser_id: advertiserId,
        campaign_ids: campaignIds,
        rules: rules,
        actions: actions,
        frequency: this.rule.frequency,
      }
      if (time !== '') {
        this.rule.time = time
      }

      await this.createRule()
    },
    async updateRule() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        name: this.rule.name,
        advertiser_id: this.rule.advertiser_id,
        campaign_ids: this.rule.campaign_ids,
        rules: JSON.stringify(this.rule.rules),
        actions: JSON.stringify(this.rule.actions),
        frequency: this.rule.frequency,
        time: this.rule.time,
      }
      try {
        const response = await this.$http.put('/api/3rd/tiktok/rules/' + this.rule.id, data, { headers })
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update rule',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
        return
      }

      this.showPopup['edit_rule'] = false
      this.$vs.notify({
        title: 'Success',
        text: 'Rule update successfully',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.applyFilters()

      this.$vs.loading.close()
    },
    async switchStatusRule(rule) {
      this.$vs.loading.close()

      this.selectedRules = []

      let newStatus = 'disabled'
      if (rule.status == 'disabled') {
        newStatus = 'active'
      }

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        status: newStatus
      }
      try {
        const response = await this.$http.put('/api/3rd/tiktok/rules/' + rule.id, data, { headers })
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the rule',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
        return
      }

      this.$vs.notify({
        title: 'Success',
        text: 'Rule updated successfully',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.applyFilters()

      this.$vs.loading()
    },
    async deleteRule(rule) {
      this.$vs.loading.close()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      try {
        const response = await this.$http.delete('/api/3rd/tiktok/rules/' + rule.id, { headers })
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot delete the rule',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
        return
      }

      this.$vs.notify({
        title: 'Success',
        text: 'Rule deleted successfully',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.applyFilters()

      this.$vs.loading()
    },
    async runRule(rule) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      try {
        const response = await this.$http.post('/api/3rd/tiktok/rules/' + rule.id + '/run', {}, { headers })
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot run the rule',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
        return
      }

      this.$vs.notify({
        title: 'Success',
        text: 'Rule executed',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.applyFilters()

      this.$vs.loading.close()
    },
    showCreateAdvertiserForm() {
      this.advertiser = {
        id: '',
        name: '',
      }
      this.showPopup['create_advertiser'] = true
    },
    showEditAdvertiserForm(adv) {
      this.advertiser = {
        id: adv.id,
        name: adv.name,
      }
      this.editingAdvertiser = adv
      this.showPopup['edit_advertiser'] = true
    },
    async createAdvertiser() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        id: this.advertiser.id,
        name: this.advertiser.name,
      }
      try {
        const response = await this.$http.post('/api/3rd/tiktok/advertisers', data, { headers })
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot create advertiser',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
        return
      }

      this.$vs.notify({
        title: 'Success',
        text: 'Advertiser created',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.showPopup['create_advertiser'] = false
      this.applyFilters()

      this.$vs.loading.close()
    },
    async updateAdvertiser() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        id: this.advertiser.id,
        name: this.advertiser.name,
      }
      try {
        const response = await this.$http.put('/api/3rd/tiktok/advertisers/' + this.editingAdvertiser.id, data, { headers })
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update advertiser',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
        return
      }

      this.$vs.notify({
        title: 'Success',
        text: 'Advertiser updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.showPopup['edit_advertiser'] = false
      this.applyFilters()

      this.$vs.loading.close()
    },
    async deleteAdvertiser(adv) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      try {
        const response = await this.$http.delete('/api/3rd/tiktok/advertisers/' + adv.id, { headers })
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot delete the advertiser',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
        return
      }

      this.$vs.notify({
        title: 'Success',
        text: 'Advertiser deleted successfully',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.applyFilters()

      this.$vs.loading.close()
    },
    showBulkEditRuleForm() {
      if (this.selectedRules.length <= 0) {
        return
      }

      let advId = this.selectedRules[0].value.advertiser_id
      for (let i = 0; i < this.selectedRules.length; i++) {
        if (this.selectedRules[0].value.advertiser_id != advId) {
          return
        }
      }

      let bulkCampaignIds = this.selectedRules[0].value.campaign_ids
      for (let i = 0; i < this.selectedRules.length; i++) {
        bulkCampaignIds = intersect(this.selectedRules[i].value.campaign_ids, bulkCampaignIds)
      }

      this.rule = {
        name: '',
        advertiser_id: advId,
        campaign_ids: bulkCampaignIds,
        rules: [],
        actions: [],
        frequency: 'hourly',
        time: 'lifetime',
      }
      this.currAdvId = advId

      this.showPopup['bulk_edit_rule'] = true
      this.refreshCampaignsOptions()
    },
    async bulkUpdateRules() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        advertiser_id: this.rule.advertiser_id,
        campaign_ids: this.rule.campaign_ids,
      }
      for (let i = 0; i < this.selectedRules.length; i++) {
        let rule = this.selectedRules[i]
        try {
          const response = await this.$http.put('/api/3rd/tiktok/rules/' + rule.id, data, { headers })
        } catch(error) {
          this.$vs.notify({
            title: 'Error',
            text: 'Cannot update all the selected rules',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
          this.$vs.loading.close()
          return
        }
      }

      this.$vs.notify({
        title: 'Success',
        text: 'Rules updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.showPopup['bulk_edit_rule'] = false
      this.$vs.loading.close()

      this.applyFilters()
    },
    async showCampaignList(rule) {
      this.currAdvId = null
      this.rule.id = rule.id
      this.rule.advertiser_id = rule.value.advertiser_id
      await this.refreshCampaignsOptions()

      this.campaignsList = []
      this.campaignsListUpdated = []
      let campaignIds = rule.value.campaign_ids
      for (let i = 0; i < this.campaignsOptions.length; i++) {
        if (campaignIds.includes(this.campaignsOptions[i].id)) {
          this.campaignsList.push(this.campaignsOptions[i])
          this.campaignsListUpdated.push(this.campaignsOptions[i].id)
        }
      }

      this.showPopup['campaign_ids'] = true
    },
    async updateCampaignsList() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        advertiser_id: this.rule.advertiser_id,
        campaign_ids: this.campaignsListUpdated,
      }

      try {
        const response = await this.$http.put('/api/3rd/tiktok/rules/' + this.rule.id, data, { headers })
      } catch(error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the rule',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
        return
      }

      this.$vs.notify({
        title: 'Success',
        text: 'Rule updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.showPopup['campaign_ids'] = false
      this.$vs.loading.close()

      this.applyFilters()
    },
    onAdvertiserSwitch() {
      this.changeView('campaigns', this.advertiserSwitch)
    },
  },
  async created() {
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
