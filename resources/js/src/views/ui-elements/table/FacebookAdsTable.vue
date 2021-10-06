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

        <div class="h-48 flex flex-col justify-center items-center">
            <vs-button v-if="!loggedIn" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-facebook" @click="login">Login with Facebook</vs-button>
            <vs-button v-else class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-facebook" @click="logout">Logout from Facebook</vs-button>
        </div>

        <div v-if="loggedIn">

            <p class="flex items-center mb-4">
                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
                <span class="btn-group flex items-center mr-2">
                    <vs-button @click="changeView('ad_accounts')" :color="(view == 'ad_accounts') ? 'success' : 'primary'">Ad accounts</vs-button>
                    <vs-button @click="changeView('campaigns')" :color="(view == 'campaigns') ? 'success' : 'primary'">Campaigns</vs-button>
                    <vs-button @click="changeView('adsets')" :color="(view == 'adsets') ? 'success' : 'primary'">Adsets</vs-button>
                    <vs-button @click="changeView('ads')" :color="(view == 'ads') ? 'success' : 'primary'">Ads</vs-button>
                    <vs-button @click="changeView('rules')" :color="(view == 'rules') ? 'success' : 'primary'">Rules</vs-button>
                </span>
                <vs-button v-if="view == 'ads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="adLauncherPopup"></vs-button>
                <vs-button v-if="view == 'rules'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showCreateRuleForm"></vs-button>
                <vs-button v-if="view == 'rules'" :disabled="selectedRules.length <= 0" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-menu" @click="showBulkEditRuleForm"></vs-button>
                <vs-button v-if="['campaigns','adsets','ads'].includes(view)" :disabled="selectedObjects.length <= 0" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-copy" @click="copyIds()"></vs-button>
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

            <vue-context ref="ad_account_context_menu" v-slot="{ data }">
                <li>
                  <a href="#" @click="$refs.ad_account_context_menu.close();adAccount = data;changeView('campaigns', data);" class="flex items-center text-sm">
                    <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Show Campaigns</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.ad_account_context_menu.close();adRulesPopup(data);" class="flex items-center text-sm">
                    <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Show Rules</span>
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
                  <a href="#" @click="$refs.campaign_context_menu.close();changeView('adsets', data);" class="flex items-center text-sm">
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
                  <a href="#" @click="$refs.adset_context_menu.close();changeView('ads', data);" class="flex items-center text-sm">
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
                  <a href="#" @click="$refs.campaign_context_menu.close();duplicateAdsetPopup(data);" class="flex items-center text-sm">
                    <feather-icon icon="CopyIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Duplicate</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.campaign_context_menu.close();duplicateAdsetBidderPopup(data);" class="flex items-center text-sm">
                    <feather-icon icon="CopyIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Duplicate (bidder)</span>
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
                    <vs-td class="column-no-padding"></vs-td>
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
                    <vs-th sort-key="profit">Profit</vs-th>
                    <vs-th sort-key="roi">ROI</vs-th>
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
                                :color="statusColors[item.status]"
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

                        <vs-td class="column-no-padding" :data="data[indextr].profit">
                            <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : 'dark')">
                                € {{data[indextr].profit.toFixed(2)}}
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].roi">
                            <vs-chip transparent :color="(data[indextr].roi > 0) ? 'success' : ((data[indextr].roi < 0) ? 'danger' : 'dark')">
                                {{data[indextr].roi.toFixed(2)}}%
                            </vs-chip>
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
                    <vs-td class="column-no-padding">
                        <vs-chip transparent :color="(totalStats.profit > 0) ? 'success' : ((totalStats.profit < 0) ? 'danger' : 'dark')">
                            € {{totalStats.profit.toFixed(2)}}
                        </vs-chip>
                    </vs-td>
                    <vs-td class="column-no-padding">
                        <vs-chip transparent :color="(totalStats.roi > 0) ? 'success' : ((totalStats.roi < 0) ? 'danger' : 'dark')">
                            {{totalStats.roi.toFixed(2)}}%
                        </vs-chip>
                    </vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <!-- END TOTAL STATS-->
                </template>
            </vs-table>

            <!-- Adsets table -->
            <vs-table v-if="view == 'adsets'" stripe multiple v-model="selectedAdsets" :max-items="maxItems" pagination search :data="adsets" :currentPage="currentPage" @change-page="onPageChanged">
                <template slot="thead">
                    <vs-th sort-key="id">ID</vs-th>
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <vs-th sort-key="spent">Spent</vs-th>
                    <vs-th sort-key="impressions">Impressions</vs-th>
                    <vs-th sort-key="cpm">CPM</vs-th>
                    <vs-th sort-key="profit">Profit</vs-th>
                    <vs-th sort-key="roi">ROI</vs-th>
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

                        <vs-td class="column-no-padding" :data="item.status">
                            <vs-switch
                                :color="statusColors[item.status]"
                                v-model="item.status == 'ACTIVE'"
                                vs-icon-off="pause" vs-icon-on="done"
                                @input="switchStatus('campaign', item)">
                                <span slot="on" v-if="item.status == 'ACTIVE'">Active</span>
                                <span slot="off">Paused</span>
                            </vs-switch>
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

                        <vs-td class="column-no-padding" :data="data[indextr].profit">
                            <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : 'dark')">
                                € {{data[indextr].profit.toFixed(2)}}
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].roi">
                            <vs-chip transparent :color="(data[indextr].roi > 0) ? 'success' : ((data[indextr].roi < 0) ? 'danger' : 'dark')">
                                {{data[indextr].roi.toFixed(2)}}%
                            </vs-chip>
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
                    <vs-td class="column-no-padding">{{ data.length }}</vs-td>
                    <vs-td class="column-no-padding">€ {{ totalStats.spent.toFixed(2) }}</vs-td>
                    <vs-td class="column-no-padding">{{ totalStats.impressions }}</vs-td>
                    <vs-td class="column-no-padding">€ {{ totalStats.cpm.toFixed(2) }}</vs-td>
                    <vs-td class="column-no-padding">
                        <vs-chip transparent :color="(totalStats.profit > 0) ? 'success' : ((totalStats.profit < 0) ? 'danger' : 'dark')">
                            € {{totalStats.profit.toFixed(2)}}
                        </vs-chip>
                    </vs-td>
                    <vs-td class="column-no-padding">
                        <vs-chip transparent :color="(totalStats.roi > 0) ? 'success' : ((totalStats.roi < 0) ? 'danger' : 'dark')">
                            {{totalStats.roi.toFixed(2)}}%
                        </vs-chip>
                    </vs-td>
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
                    <vs-th sort-key="profit">Profit</vs-th>
                    <vs-th sort-key="roi">ROI</vs-th>
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

                        <vs-td class="column-no-padding" :data="data[indextr].profit">
                            <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : 'dark')">
                                € {{data[indextr].profit.toFixed(2)}}
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].roi">
                            <vs-chip transparent :color="(data[indextr].roi > 0) ? 'success' : ((data[indextr].roi < 0) ? 'danger' : 'dark')">
                                {{data[indextr].roi.toFixed(2)}}%
                            </vs-chip>
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{ data.length }}</vs-td>
                    <vs-td class="column-no-padding">€ {{ totalStats.spent.toFixed(2) }}</vs-td>
                    <vs-td class="column-no-padding">{{ totalStats.impressions }}</vs-td>
                    <vs-td class="column-no-padding">€ {{ totalStats.cpm.toFixed(2) }}</vs-td>
                    <vs-td class="column-no-padding">
                        <vs-chip transparent :color="(totalStats.profit > 0) ? 'success' : ((totalStats.profit < 0) ? 'danger' : 'dark')">
                            € {{totalStats.profit.toFixed(2)}}
                        </vs-chip>
                    </vs-td>
                    <vs-td class="column-no-padding">
                        <vs-chip transparent :color="(totalStats.roi > 0) ? 'success' : ((totalStats.roi < 0) ? 'danger' : 'dark')">
                            {{totalStats.roi.toFixed(2)}}%
                        </vs-chip>
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
                            {{(data[indextr].value.all_campaigns == true) ? 'All' : data[indextr].value.campaign_ids.length}}
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

            <!-- Duplicate adset popup -->
            <vs-popup title="Duplicate adset" :active.sync="showPopup['duplicate_adset']">
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Number of new adsets</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-input type="number" min="1" step="1" class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="editingObject.adsetNumber" />
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Budget (daily) of each adset</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="editingObject.adsetBudget" />
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <vs-button class="mt-12 mr-auto ml-auto" @click="duplicateAdsetNormal(editingObject)" color="primary" type="filled" :disabled="!isValidForm('duplicate_adset')">Duplicate</vs-button>
                </div>
            </vs-popup>

            <!-- Duplicate (bidder) adset popup -->
            <vs-popup title="Bidder" :active.sync="showPopup['duplicate_adset_bidder']">
                <div class="vx-row mb-6" v-if="editingObject.cbo">
                    <div class="vx-col w-full">
                        <span>* Warning: This adset is from a CBO campaign</span>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/5 w-full">
                        <span>Budget</span>
                    </div>
                    <div class="vx-col sm:w-1/5 w-full">
                        <span>Bid</span>
                    </div>
                    <div class="vx-col sm:w-1/5 w-full">
                        <span>Bid Amount</span>
                    </div>
                    <div class="vx-col sm:w-1/5 w-full">
                        <span>Accelerated</span>
                    </div>
                    <div class="vx-col sm:w-1/5 w-full">
                    </div>
                </div>

                <div class="vx-row mb-6" v-for="(item, index) in editingObject.adsets" :key="index">
                    <div class="vx-col sm:w-1/5 w-full">
                        <vs-input type="number" min="0.0" step="0.1" class="w-full" v-model="item.budget" />
                    </div>
                    <div class="vx-col sm:w-1/5 w-full">
                        <vs-select v-model="item.bid" class="w-full">
                            <vs-select-item :key="optionIndex" :value="option.value" :text="option.label" v-for="(option, optionIndex) in duplicateBidderBidOptions" />
                        </vs-select>
                    </div>
                    <div class="vx-col sm:w-1/5 w-full">
                        <vs-input type="number" min="0.0" step="0.1" class="w-full" v-model="item.bid_amount" />
                    </div>
                    <div class="vx-col sm:w-1/5 w-full">
                        <vs-checkbox v-model="item.accelerated">Acc.</vs-checkbox>
                    </div>
                    <div class="vx-col sm:w-1/5 w-full">
                        <vs-button class="mr-auto ml-auto" @click="removeBidderAdset(index)" color="danger" type="filled" icon-pack="feather" icon="icon-trash"></vs-button>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/5 w-full"></div>
                    <div class="vx-col sm:w-1/5 w-full"></div>
                    <div class="vx-col sm:w-1/5 w-full"></div>
                    <div class="vx-col sm:w-1/5 w-full"></div>
                    <div class="vx-col sm:w-1/5 w-full">
                        <vs-button class="mr-auto ml-auto" @click="addBidderAdset" color="primary" type="filled" icon-pack="feather" icon="icon-plus"></vs-button>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <vs-button class="mt-12 mr-auto ml-auto" @click="duplicateAdsetBidder(editingObject)" color="primary" type="filled" :disabled="!isValidForm('duplicate_adset_bidder')">Duplicate (bidder)</vs-button>
                </div>
            </vs-popup>

            <!-- Ad launcher popup -->
            <vs-popup title="Ad Launcher" :active.sync="showPopup['ad_launcher']">

                <div v-if="ad.view == 'normal'">
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Choose ad template</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-select autocomplete v-model="adTemplate" class="w-full select-large">
                                <vs-select-item :key="index" :value="item" :text="item.name" v-for="(item, index) in adTemplates" class="w-full" />
                            </vs-select>
                            <p class="flex items-center w-full">
                              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-download" @click="loadAdTemplate(adTemplate)"></vs-button>
                              <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="deleteAdTemplate(adTemplate)"></vs-button>
                            </p>
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>New ad template name</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newAdTemplateName" />
                        </div>
                    </div>

                    <hr>

                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Page</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-select autocomplete v-model="ad.pageId" class="w-full select-large">
                                <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in fbAccountPages" class="w-full" />
                            </vs-select>
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Campaign name</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="ad.campaignName" />
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Status</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-select autocomplete v-model="ad.campaignStatus" class="w-full select-large">
                                <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in campaignStatusOptions" class="w-full" />
                            </vs-select>
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Objective</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-select autocomplete v-model="ad.campaignObjective" class="w-full select-large">
                                <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in campaignObjectiveOptions" class="w-full" />
                            </vs-select>
                        </div>
                    </div>
                    <div class="vx-row mb-6" v-if="ad.campaignObjective == 'CONVERSIONS'">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Pixel ID</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="ad.pixelId" disabled />
                        </div>
                    </div>
                    <div class="vx-row mb-6" v-if="ad.campaignObjective == 'CONVERSIONS'">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Conversion event</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-select autocomplete v-model="ad.conversionEvent" class="w-full select-large">
                                <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in conversionEventOptions" class="w-full" />
                            </vs-select>
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Budget</span>
                        </div>
                        <div class="vx-col sm:w-1/3 w-full">
                            <vs-select autocomplete v-model="ad.campaignBudgetStrategy" class="w-full select-large">
                                <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in campaignBudgetStrategyOptions" class="w-full" />
                            </vs-select>
                        </div>
                        <div class="vx-col sm:w-1/3 w-full">
                            <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="ad.campaignBudget" />
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Bid</span>
                        </div>
                        <div class="vx-col sm:w-1/3 w-full">
                            <vs-select autocomplete v-model="ad.campaignBidStrategy" class="w-full select-large">
                                <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in campaignBidStrategyOptions" class="w-full" />
                            </vs-select>
                        </div>
                        <div class="vx-col sm:w-1/3 w-full">
                            <vs-input v-if="ad.campaignBidStrategy != 'LOWEST_COST_WITHOUT_CAP'" type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="ad.campaignBid" />
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Split Adset</span>
                        </div>
                        <div class="vx-col sm:w-1/3 w-full">
                            <vs-select autocomplete v-model="ad.splitAdset" class="w-full select-large">
                                <vs-select-item value="true" text="Yes" class="w-full" />
                                <vs-select-item value="false" text="No" class="w-full" />
                            </vs-select>
                        </div>
                    </div>

                    <hr>

                    <div class="vx-row mb-6 mt-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Gender</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-select autocomplete v-model="ad.targetingGender" class="w-full select-large">
                                <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in targetingGenderOptions" class="w-full" />
                            </vs-select>
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Age</span>
                        </div>
                        <div class="vx-col sm:w-1/3 w-full">
                            <vs-input type="number" min="0" step="1" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="ad.targetingAgeMin" />
                        </div>
                        <div class="vx-col sm:w-1/3 w-full">
                            <vs-input type="number" min="0" step="1" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="ad.targetingAgeMax" />
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Country</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-select autocomplete v-model="ad.targetingCountry" class="w-full select-large">
                                <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in targetingCountryOptions" class="w-full" />
                            </vs-select>
                        </div>
                    </div>

                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Device</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-select autocomplete v-model="ad.targetingDevice" class="w-full select-large">
                                <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in targetingDeviceOptions" class="w-full" />
                            </vs-select>
                            <vs-checkbox v-model="ad.targetingWifi">Only when connected to Wi-Fi</vs-checkbox>
                        </div>
                    </div>

                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Platforms</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <div class="vx-row w-full">
                                <div class="vx-col sm:w-1/2 w-full">
                                    <vs-checkbox v-model="ad.targetingPlatformFacebook">Facebook</vs-checkbox>
                                </div>
                                <div class="vx-col sm:w-1/2 w-full">
                                    <vs-checkbox v-model="ad.targetingPlatformInstagram">Instagram</vs-checkbox>
                                </div>
                            </div>
                            <div class="vx-row w-full">
                                <div class="vx-col sm:w-1/2 w-full">
                                    <vs-checkbox v-model="ad.targetingPlatformAudience">Audience</vs-checkbox>
                                </div>
                                <div class="vx-col sm:w-1/2 w-full">
                                    <vs-checkbox v-model="ad.targetingPlatformMessenger">Messenger</vs-checkbox>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- TODO: Creative info -->

                    <hr>

                    <div class="vx-row mb-6 mt-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Title</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="ad.title" />
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Text</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-textarea v-model="ad.text"></vs-textarea>
                        </div>
                    </div>
                    <div class="vx-row mb-6 mt-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>URL</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="ad.url" />
                            <span>Final URL: {{ad.url}}?pi={pixel_id}</span>
                        </div>
                    </div>
                    <div class="vx-row mb-6 mt-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Conversion domain</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-input class="w-full" icon-pack="feather" icon="icon-globe" icon-no-border v-model="ad.conversionDomain" />
                            <span>Pixel name: {{(this.selectedAdAccounts.length > 0 && this.selectedAdAccounts[0].hasOwnProperty('pixel_name')) ? this.selectedAdAccounts[0].pixel_name : ''}}</span>
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Image/Video</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-image" class="w-full" @click="ad.view = 'upload'">Choose medias</vs-button>
                            <div>
                                <!--<span class="ml-auto mr-auto">{{ (ad.filesToUpload.length == 0) ? 'No file selected' : ((ad.filesToUpload.length == 1) ? ad.filesToUpload[0].name : ad.filesToUpload.length + ' files') }}</span>-->
                                <span class="ml-auto mr-auto">{{ (selectedProductMedias.length == 0) ? 'No media selected' : selectedProductMedias.length + ' media(s) selected' }}</span>
                            </div>
                        </div>
                        <!--
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-image" class="w-full" @click="showFilePicker">Upload</vs-button>
                            <div>
                                <span class="ml-auto mr-auto">{{ (ad.filesToUpload.length == 0) ? 'No file selected' : ((ad.filesToUpload.length == 1) ? ad.filesToUpload[0].name : ad.filesToUpload.length + ' files') }}</span>
                            </div>
                            <input type="file" ref="adFileUpload" class="hidden" :accept="'image/*,video/*'" multiple @change="onFilesUploadChange">
                        </div>
                        -->
                    </div>

                    <div class="vx-row mb-6" v-if="selectedProductMediasHaveVideo()">
                        <div class="vx-col sm:w-1/3 w-full">
                            <span>Video(s) preview</span>
                        </div>
                        <div class="vx-col sm:w-2/3 w-full">
                            <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-image" class="w-full" @click="ad.view = 'upload_preview'">Choose preview</vs-button>
                            <div>
                                <span class="ml-auto mr-auto">{{ (selectedPreview.length == 0) ? 'No media selected' : selectedPreview.length + ' preview selected' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="vx-row mb-6">
                        <vs-button class="mt-12 mr-auto ml-auto" @click="saveAdTemplate" color="primary" type="filled">Save Ad Template</vs-button>
                        <vs-button class="mt-12 mr-auto ml-auto" @click="launchAd" color="primary" type="filled" :disabled="!isValidForm('ad_launcher')">Launch Ad(s)</vs-button>
                    </div>
                </div>

                <div v-if="ad.view == 'upload'">
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="(selectedProductMedias.length > 0) ? 'icon-check' : 'icon-arrow-left'" @click="ad.view = 'normal'"></vs-button>
                        </div>
                        <div class="vx-col sm:w-1/3 w-full">
                            <vs-select autocomplete v-model="productMediasFilterProductId" class="w-full select-large" @change="changeProductMediaFilterProductId">
                                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in productOptions" class="w-full" />
                            </vs-select>
                        </div>
                        <div class="vx-col sm:w-1/3 w-full flex items-center">
                            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="fetchProductMedias"></vs-button>
                            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="openProductMediaPage"></vs-button>
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full" :class="productMediaIsSelected(index) ? 'selected-product-media' : ''" v-for="(item, index) in productMediasFiltered" @click="selectProductMedia(index)" @dblclick="openProductMedia(index)">
                            <img class="w-full" :src="(item.type == 'image') ? item.url : videoThumbnail">
                            <center>{{item.type}} ({{item.pathname}})</center>
                        </div>
                    </div>
                </div>

                <div v-if="ad.view == 'upload_preview'">
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full">
                            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="(selectedPreview.length > 0) ? 'icon-check' : 'icon-arrow-left'" @click="ad.view = 'normal'"></vs-button>
                        </div>
                        <div class="vx-col sm:w-1/3 w-full">
                            <vs-select autocomplete v-model="productMediasFilterProductId" class="w-full select-large" @change="changeProductMediaFilterProductId" disabled="disabled">
                                <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in productOptions" class="w-full" />
                            </vs-select>
                        </div>
                        <div class="vx-col sm:w-1/3 w-full">
                        </div>
                    </div>
                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/3 w-full" :class="(selectedPreview.length == 1 && selectedPreview[0] == index) ? 'selected-product-media' : ''" v-for="(item, index) in productMediasFiltered" @click="selectPreview(index)" @dblclick="openProductMedia(index)" v-if="item.type == 'image'">
                            <img class="w-full" :src="(item.type == 'image') ? item.url : videoThumbnail">
                            <center>{{item.type}} ({{item.pathname}})</center>
                        </div>
                    </div>
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
                        <span>Ad Account</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-select autocomplete v-model="rule.ad_account_id" class="w-full select-large" @input="refreshCampaignsOptions()">
                            <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in adAccounts" class="w-full" />
                        </vs-select>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Campaigns</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full flex">
                        <vs-checkbox v-model="rule.all_campaigns">All</vs-checkbox>
                        <vs-select :disabled="rule.all_campaigns == true" autocomplete multiple v-model="rule.campaign_ids" class="w-full select-large">
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
                        <span>Ad Account</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-select autocomplete v-model="rule.ad_account_id" class="w-full select-large" @input="refreshCampaignsOptions()">
                            <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in adAccounts" class="w-full" />
                        </vs-select>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Campaigns</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full flex">
                        <vs-checkbox v-model="rule.all_campaigns">All</vs-checkbox>
                        <vs-select :disabled="rule.all_campaigns == true" autocomplete multiple v-model="rule.campaign_ids" class="w-full select-large">
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
                        <span>Ad Account</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-select autocomplete v-model="rule.ad_account_id" class="w-full select-large" @input="refreshCampaignsOptions()">
                            <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item, index) in adAccounts" class="w-full" />
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

        </div>

    </vx-card>
</template>

<script>
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

async function convertImgToBase64(url) {
  const data = await fetch(url)
  const blob = await data.blob()
  return new Promise((resolve) => {
    const reader = new FileReader()
    reader.readAsDataURL(blob)
    reader.onloadend = () => {
      const base64data = reader.result
      resolve(base64data)
    }
  })
}

async function convertImgToBase64Raw(url) {
  let dataBase64 = await convertImgToBase64(url)
  let toSearch = 'base64,'
  let result = dataBase64
  result = result.substring(result.indexOf(toSearch) + toSearch.length, result.length)
  return result
}

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
      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        status: '',
      },
      dateFormat: 'yyyy-MM-dd',

      adAccounts: [],
      selectedAdAccounts: [],

      campaigns: [],
      selectedCampaigns: [],

      adsets: [],
      selectedAdsets: [],

      ads: [],
      selectedAds: [],

      rules: [],
      selectedRules: [],

      parent: null,
      currentAdAccount: null,
      currentCampaign: null,
      currentAdset: null,
      currentAd: null,

      statusOptions: ['ACTIVE', 'PAUSED', 'REVIEW'],
      statusColors: {
        'ACTIVE': 'success',
        'PAUSED': 'warning',
        'DELETED': 'danger',
        'ARCHIVED': 'warning',
      },

      totalStats: {
        spent: 0.0,
        impressions: 0,
        cpm: 0.0,
        value: 0.0,
        profit: 0.0,
        roi: 0.0,
      },

      showPopup: {
        'edit_bid': false,
        'view_ad': false,
        'duplicate_adset': false,
        'duplicate_adset_bidder': false,
        'ad_launcher': false,
        'create_rule': false,
        'edit_rule': false,
        'bulk_edit_rule': false,
        'campaign_ids': false,
      },

      adAccount: {},
      campaign: {},
      adset: {},
      ad: {},

      editingObject: {},

      bidOptions: [
        { value:'LOWEST_COST_WITHOUT_CAP',  label: 'Lowest cost' },
        { value:'LOWEST_COST_WITH_BID_CAP', label: 'Lowest cost with bid cap' },
        { value:'COST_CAP',                 label: 'Cost cap' },
      ],
      duplicateBidderBidOptions: [
        { value:'LOWEST_COST_WITH_BID_CAP', label: 'Bid cap' },
        { value:'COST_CAP',                 label: 'Cost cap' },
      ],

      adTemplate: {
        'name': 'new',
        'value': {},
      },
      adTemplates: [],
      newAdTemplateName: '',

      fbAccountPages: [],

      productMedias: [],
      productMediasFiltered: [],
      productMediasFilterProductId: '',

      selectedProductMedias: [],
      selectedPreview: [],
      videoThumbnail: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAkFBMVEX///8EAgQAAAD29vby8vJubm7MzMzv7+/b29u7u7v7+/tTU1MzNTM3NzfOzs739/epqqmcnJzU1NTk5OR/f39lZmVISUiOjo6JiYkxMTEWFRZgYWDn5+coKSi4ubh3d3dBQUGfn59aWVpRUFEgHyAPDg/DxMOTlJOoqaiDg4MrKisjIyM9PD0LCwt5enlFRUUd5XBlAAAIIklEQVR4nO2da1savRaG42KAYdzAAAoKyEERtKL9//9uz1BAxAGyTsnwXnk+tB9au3KXkMM6xZigoKCgoKCgoKCgoKCgoKCgoKCgoNIo6sW1dHX3Va1Wn/v97Nfp3aDTiOOx74FJKF4ORrMFFOu1+TJNa5HvMZLVbqxmO5abIu3+cFFNe74Hi1bUepidYfsN+llN674Hba/6pDq0g/uJ2V/FvodupeU9lu4Astkp+ycZvxPxDj7JRuKb4qSSyS0Hb085HJRzG2nPH/l4W0aodn3j/FJ7cCPEt2V8KRdjpSMwPY8Zb0u0sqavwnxbyGpJDgLLpgbfBhEefMNlqt8r8W0Zl74B54p8/xifvU7VXl8ZcMP45g8w1efbID57umBFt04AN4wNH4C1T0d8G8R394DaS8wxY9/1pWPkFjD/GJ0e48b/c8y3QUzdAXYX7gFzxIErwKXrGbpHrLoBbHgCzBlvXQC++QPMEfU3f8e7xC/EWeW/DZghNnUdVRPfgPnerwnocZE5QBzpAbbKAJghqm0avXIAZohKzo3Iy0mmUEoHuNvSAGanm5YC4Ht5ADPEV/nLVFomwAzxURqwNKvMTuK3/mbJAG+kPal3ZQPMED8lv4q+boRnJXmVqgxLCCi6K1bxgG5cxVLztEYBdDGvpc7gySOBMH5wgCjlC+8QhgqR6T07iNp8SFyH65SBQu5rmEiHvgvsdAQICcvMltAkd9qIEotNjzRG2PqLutoBRoHbMO3OBHuPWKo7VQG4GRtEx8U3oYk0A/2ZpXsmIfHaC4dezdpaNZmB9yFSfU8/CE3S0UTkfRPviSODI8/0WG9zBODk+dEW0pvfhNn1RCVvamPri0H4JUdoEq1zHAD9YJOQx1RAaEz8osMIczLhG3lAhYRamyP8IRPSnTMnCE1lqoJYIwJ26aM5RZjtPwqbI3nDYPiATxNqhCDhhhYZbn/oEJq6+OYIExIhwXnxbfJ8LHoplfS+M0dzZ3B8pBcITXsligjQphC+KhJmxyXRzZHksOlyBnCZ0JjGUI6RdIdaaROaipyTA4BAyIrFWBFm8+SvFCMhZBqxbFsSbjZHEUaC023JMmxNaKKqCCK8oAkfHBEKbY6wQB9r+s4ITSKxOaLdNXVeQA1FaMyYn/aPrsqIeRaRhNnm+IfJCFOkxYljQtNmOjnQCX3M7Bk8YTZtZhxGwJocuSdk1jECrgIsYZYb0AhNxHByIA/fvBMNmdCYFnlzRHrc6szFm0yYh5yJwSBcSibvzMYipFalItNruHl6HMLs/5eyOcITygYlO0GO0CQDPCLyijjwS0iqv8URTn0TZl+UBZIRULcLatxQkNBE78gWN6g44qwEhMZ0UZsjzpHBraCUIdyUkVmPBBefKQuhqds7Oa6U0Jia7eZ4tYS559ZqNFdMaOInm+FcM+HAKoRyvYRdy/4310pov5jiCJ/LQohon4JzYzDdNFKEqPRU3KnN+91iI1z0DXe3YAUPhQgbOM8b8n44906ITvWHIerfZ7q8+YT4YA3MUAaYYQsuYe2D4MXABS68+UtzjUlBU1ihjFSYddscwjmtLAxbyfbiizCm9ijEdlliuqKohPTABeAcUWyHKZFwQs/Dgj/IXOiWB8KYE+tGZ0Uxq9MphEjn4bFFdK8MXnk6nnDJzHLDF6/zfMJYwjG3OAqQIWDDjT4hCfm5X7DGAmbnNid5bblaTwJdwAmFM6yCcwQhwud7ziAhhZZUHYsnTEW6GZCqZVkXKFvCHiuH5sBeEw9oKqz8HSvCCm8LPLRHqunm+NusCBvYGOgZe6RCUnpdlxXhWLDJK3xQAGmtBqwJB5JlbNTOpoxpeonQOmRmZ41a7cxYTc8T0twUZ6xRWylW6P2szxJiQtd21shd2+kX/TOEtqEkhDF6cwy6T/EkYaTQDIRTkb+mjuYUIToNyMoYo9N3Q5ZwrFLMTTqx7WUVQC+yWkDYJqTjWdlitVKinmsKCNn1BqdM8TooUn3fvwj1Hmvhdm0jhtmOCfVaSMMjsxlWQtv1fxK2tF4TuuF/hFSP1CFhXaWPws4QvWPEXqRv4gGhbqMoiR6fpLT9PSGv1OeyHXxhZYEol6gtYUW5tyC/09dGFM/pP8KGgCP0vJk7CUBSwWxOKFA2ecnKUCr1Cr/YQKTa32tnRewZKHwPEJgrndF+GHmWAqR0xHLSvVSwVTKlSau6ZJ8qYzRU0hKrTVuBuKVe4kJnJlwUM8FGWkJ7/aES6nVfRUDsDHVWvKCwsHReCyzBO0E7ab3dxU0cFhMsSI2vLERuhSkr0a3+SNzCSxEpLKPfSkrwmg6Q+0DayftbJeqvPFe49bNsQIWN8Kcir4gOAI3Xl610XrL6LW5RFAPQ1Yu56k8enAD8cPfMugMfTAHg2uWzzu6fRwL2EwFI9RxvjCDyGglKbd1HHY4BP3UPMsVy9zAwwF/9Z4CLRC7gQQM6n6F7uXnUaa14l7goevMxe0BnL6qf0Eo3AAozd7v8Kak+PzLUeTQWq6VOnDebHAM/S2iBUsHW3N98Vc4DMuKay0abMr6RzxW0SMmb3LKa8U3LxpcrafRF1tX8+1eq+XmoeApMyOzHmxPpqJKokrRPh8x+cPFQxul5pF6HBJn9yNPUxw2CpHF6DwjKzd99XLW0ohFKanX+Alzk/Pc3hl8Tlx4KQXUn0/4H7HXElet1PerUrpRup6jXTR+mo/UBaf6pPd1W7zrL+MrhDpW0o6hej+JWq579FlVKvSMEBQUFBQUFBQUFBQUFBQUFBQUF/db/Afm8kjbo5XSmAAAAAElFTkSuQmCC',

      campaignStatusOptions: [ 'ACTIVE', 'PAUSED' ],
      campaignObjectiveOptions: [
        { value: 'LINK_CLICKS', label: 'Link clicks' },
        { value: 'CONVERSIONS', label: 'Conversions' },
        { value: 'LEAD_GENERATION', label: 'Lead generation' },
        { value: 'PAGE_LIKES', label: 'Page likes' },
        { value: 'POST_ENGAGEMENT', label: 'Post engagement' },
        { value: 'REACH', label: 'Reach' },
      ],
      conversionEventOptions: [
        { value: 'SUBSCRIBE', label: 'Subscribe' },
        { value: 'ADD_TO_CART', label: 'Add to cart' },
        { value: 'PURCHASE', label: 'Purchase' },
        { value: 'LEAD', label: 'Lead' },
        { value: 'COMPLETE_REGISTRATION', label: 'Complete registration' },
      ],
      campaignBudgetStrategyOptions: [ 'CBO', 'Adset' ],
      campaignBidStrategyOptions: [
        { value: 'LOWEST_COST_WITHOUT_CAP', label: 'Lowest cost' },
        { value: 'LOWEST_COST_WITH_BID_CAP', label: 'Bid cap' },
        { value: 'COST_CAP', label: 'Cost cap' },
      ],
      targetingGenderOptions: [ 'All', 'Male', 'Female' ],
      targetingCountryOptions: [
        { value: 'IT', label: 'Italy'},
        { value: 'US', label: 'United States'},
      ],
      targetingDeviceOptions: [
        { label: 'All', value: '' },
        { label: 'Mobile', value: 'mobile' },
        { label: 'Desktop', value: 'desktop' },
      ],

      rule: {
        name: '',
        ad_account_id: '',
        campaign_ids: [],
        all_campaigns: false,
        rules: [],
        actions: [],
        frequency: 'hourly',
        time: 'lifetime',
      },
      campaignsOptions: [],
      currAdAccId: null,

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
        { value: 'current_day',  label: 'Current day' },
        { value: 'last_1_days',  label: 'Last day' },
        { value: 'last_2_days',  label: 'Last 2 days' },
        { value: 'last_3_days',  label: 'Last 3 days' },
        { value: 'last_7_days',  label: 'Last 7 days' },
        { value: 'last_14_days', label: 'Last 14 days' },
        { value: 'last_30_days', label: 'Last 30 days' },
        { value: 'lifetime',     label: 'Lifetime' },
      ],

      ruleMetricOptions: [
        { value: 'impressions',         label: 'Impressions' },
        { value: 'spend',               label: 'Spent' },
        { value: 'cmp',                 label: 'CPM' },
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
    async changeView(view, parent = null) {
      this.parent = parent
      if (view == 'ad_accounts') {
        this.view = view
        this.$vs.loading()
        this.selectedAdAccounts = []
        this.adAccounts = await this.fetchAdAccounts()
        this.$vs.loading.close()
      } else if (view == 'campaigns') {
        this.view = view
        if (parent !== null) {
          this.$vs.loading()
          this.selectedCampaigns = []
          this.campaigns = await this.fetchCampaigns(parent.id)
          this.$vs.loading.close()
        }
        this.calculateStats()
      } else if (view == 'adsets') {
        this.view = view
        if (parent !== null) {
          this.$vs.loading()
          this.selectedAdsets = []
          this.adsets = await this.fetchAdsets(parent.id)
          this.$vs.loading.close()
        }
        this.calculateStats()
      } else if (view == 'ads') {
        this.view = view
        if (parent !== null) {
          this.$vs.loading()
          this.selectedAds = []
          this.ads = await this.fetchAds(parent.id)
          this.$vs.loading.close()
        }
        this.calculateStats()
      } else if (view == 'rules') {
        this.view = view
        this.$vs.loading()
        this.rules = await this.fetchRules()
        this.$vs.loading.close()
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
      let value = 0.0
      let profit = 0.0
      let roi = 0.0

      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        spent += obj.spent
        impressions += obj.impressions
        value += obj.value
      }
      cpm = (impressions > 0) ? (spent*1000/impressions) : (spent*1000)
      profit = value - spent
      roi = ((spent > 0) ? ((profit/spent)*100.0) : (profit > 0 ? 9999.99 : 0.0))

      this.totalStats = {
        spent: spent,
        impressions: impressions,
        cpm: cpm,
        value: value,
        profit: profit,
        roi: roi,
      }
    },
    isValidForm(type) {
      if (type == 'edit_bid') {
        if (this.editingObject.hasOwnProperty('bid_strategy') &&
            this.editingObject.bid_strategy != '') {
          return ((this.editingObject.bid_strategy == 'LOWEST_COST_WITHOUT_CAP') ||
                  (this.editingObject.bid_strategy != 'LOWEST_COST_WITHOUT_CAP') && this.editingObject.bid_amount > 0)
        }
      } else if (type == 'duplicate_adset') {
        if (this.editingObject.adsetNumber > 0 && this.editingObject.adsetBudget > 0) {
          return true
        }
      } else if (type == 'duplicate_adset_bidder') {
        if (this.editingObject.hasOwnProperty('adsets') && this.editingObject.adsets.length > 0) {
          for (let i = 0; i < this.editingObject.adsets.length; i++) {
            let obj = this.editingObject.adsets[i]
            if (obj.budget == 0 || obj.bid == '') return false
          }
          return true
        }
      } else if (type == 'create_rule' || type == 'edit_rule') {
        if (this.rule.name != '' && this.rule.frequency != '' &&
            this.rule.ad_account_id != '' &&
            this.rule.campaign_ids.length >= 0 &&
            this.rule.rules.length > 0 && this.rule.time != '') {
          return true
        }
      } else if (type == 'bulk_edit_rule') {
        if (this.rule.ad_account_id != '' &&
            this.rule.campaign_ids.length >= 0) {
          return true
        }
      }
      return false
    },
    applyFilters() {
      this.changeView(this.view, this.parent)
    },
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        status: '',
      }
      this.filterDatesShortcut('this_month')
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
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0')
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

    async checkToken(fbAccessToken) {
      try {
        let params = {
          access_token: fbAccessToken,
          fields: 'id,name',
        }
        const response = await this.$http.get(this.fbApiUrl + '/me', { params: params })
        if (response.hasOwnProperty('data') && response.data.hasOwnProperty('id')) {
          return true
        } else {
          return false
        }
      } catch (error) {
        return false
      }
      return false
    },
    async autoLogin() {
      // Check if user is already logged in
      let fbAccessToken = localStorage.getItem('fb_access_token')
      if (fbAccessToken !== null) {
        // Check if current access token is valid
        let isValid = await this.checkToken(fbAccessToken)
        if (isValid) {
          this.accessToken = fbAccessToken
          this.loggedIn = true
          this.changeView('ad_accounts')
        } else {
          localStorage.removeItem('fb_access_token')
          this.loggedIn = false
        }
      } else {
        localStorage.removeItem('fb_access_token')
        this.loggedIn = false
      }
    },
    login() {
      this.FB.login((response) => {
        if (response.authResponse) {
          this.accessToken = response.authResponse.accessToken
          localStorage.setItem('fb_access_token', this.accessToken)
          this.loggedIn = true
          this.changeView('ad_accounts')

          // DEBUG TODO: to delete
          console.log("Logged in")
        } else {
          this.loggedIn = false
          localStorage.removeItem('fb_access_token')

          // DEBUG TODO: to delete
          console.log("Not logged in")
        }
      })
    },
    logout() {
      localStorage.removeItem('fb_access_token')
      this.loggedIn = false
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

        let hasAdRules = false
        try {
          let adRulesParams = {
            access_token: this.accessToken,
          }
          const adRulesResponse = await this.$http.get(this.fbApiUrl + '/' + obj.id + '/adrules_library', { params: params })
          if (adRulesResponse.data.data.length > 0) {
            hasAdRules = true
          }
        } catch (error) {
          hasAdRules = false
        }

        adAccounts[i] = obj
      }

      return adAccounts
    },
    async fetchCampaigns(adAccountId, insights = true) {
      let campaigns = []

      // TEST DEBUG TODO: Only for debug purposes
      let timeRange = {
        since: '2020-09-28',
        until: '2021-09-28',
      }

      // TODO: to uncomment
      // TODO: What about date_preset??
      /*
      let timeRange = {}
      if (this.filters.date_from != '') {
        timeRange['since'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        timeRange['until'] = this.filters.date_to
      }
      */

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
        let value = 0.0

        if (obj.hasOwnProperty('insights')) {
          let stats = obj.insights.data[0]

          spent = parseFloat(stats.spend)
          impressions = parseInt(stats.impressions)
          cpm = parseFloat(stats.cpm)
        }

        // TODO: get value
        value = 0.0

        obj.spent = spent
        obj.impressions = impressions
        obj.cpm = cpm
        obj.value = value

        obj.profit = obj.value - obj.spent
        obj.roi = ((obj.spent > 0) ? ((obj.profit/obj.spent)*100.0) : (obj.profit > 0 ? 9999.99 : 0.0))

        campaigns[i] = obj
      }

      if (this.filters.status !== '') {
        campaigns = campaigns.filter(campaign => (campaign.status == this.filters.status))
      }

      return campaigns
    },
    async fetchAdsets(campaignId, insights = true) {
      let adsets = []

      // TEST DEBUG TODO: Only for debug purposes
      let timeRange = {
        since: '2020-09-28',
        until: '2021-09-28',
      }

      // TODO: to uncomment
      // TODO: What about date_preset??
      /*
      let timeRange = {}
      if (this.filters.date_from != '') {
        timeRange['since'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        timeRange['until'] = this.filters.date_to
      }
      */

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
        let value = 0.0

        if (obj.hasOwnProperty('insights')) {
          let stats = obj.insights.data[0]

          spent = parseFloat(stats.spend)
          impressions = parseInt(stats.impressions)
          cpm = parseFloat(stats.cpm)
        }

        // TODO: get value
        value = 0.0

        obj.spent = spent
        obj.impressions = impressions
        obj.cpm = cpm
        obj.value = value

        obj.profit = obj.value - obj.spent
        obj.roi = ((obj.spent > 0) ? ((obj.profit/obj.spent)*100.0) : (obj.profit > 0 ? 9999.99 : 0.0))

        adsets[i] = obj
      }

      if (this.filters.status !== '') {
        adsets = adsets.filter(adset => (adset.status == this.filters.status))
      }

      return adsets
    },
    async fetchAds(adsetId, insights = true) {
      let ads = []

      // TEST DEBUG TODO: Only for debug purposes
      let timeRange = {
        since: '2020-09-28',
        until: '2021-09-28',
      }

      // TODO: to uncomment
      // TODO: What about date_preset??
      /*
      let timeRange = {}
      if (this.filters.date_from != '') {
        timeRange['since'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        timeRange['until'] = this.filters.date_to
      }
      */

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
        let value = 0.0

        if (obj.hasOwnProperty('insights')) {
          let stats = obj.insights.data[0]

          spent = parseFloat(stats.spend)
          impressions = parseInt(stats.impressions)
          cpm = parseFloat(stats.cpm)
        }

        // TODO: get value
        value = 0.0

        obj.spent = spent
        obj.impressions = impressions
        obj.cpm = cpm
        obj.value = value

        obj.profit = obj.value - obj.spent
        obj.roi = ((obj.spent > 0) ? ((obj.profit/obj.spent)*100.0) : (obj.profit > 0 ? 9999.99 : 0.0))

        ads[i] = obj
      }

      if (this.filters.status !== '') {
        ads = ads.filter(ads => (ads.status == this.filters.status))
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
    async updateBid() {
      this.$vs.loading()

      try {
        let bidAmount = parseInt(this.editingObject.bid_amount * 100)
        let bidStrategy = this.editingObject.bid_strategy

        let params = {
          access_token: this.accessToken,
          bid_strategy: bidStrategy,
        }
        if (bidStrategy != 'LOWEST_COST_WITHOUT_CAP') {
          params.bid_amount = bidAmount
        }

        let adsets = []
        if (this.editingObject.object_type == 'campaign') {
          adsets = await this.fetchAdsets(this.editingObject.id, false)
          if (this.editingObject.cbo) {
            if (bidStrategy != 'LOWEST_COST_WITHOUT_CAP') {
              params.adset_bid_amounts = {}
              for (let adsetIndex = 0; adsetIndex < adsets.length; adsetIndex++) {
                let adset = adsets[adsetIndex]
                params.adset_bid_amounts[adset.id] = bidAmount
              }
            }
            await this.$http.post(this.fbApiUrl + '/' + this.editingObject.id, params)
          } else {
            for (let i = 0; i < adsets.length; i++) {
              let adset = adsets[i]
              await this.$http.post(this.fbApiUrl + '/' + adset.id, params)
            }
          }
        } else if (this.editingObject.object_type == 'adset') {
          let adset = this.editingObject
          await this.$http.post(this.fbApiUrl + '/' + adset.id, params)
        } else {
          throw 'Invalid object type'
        }

        this.$vs.notify({
          title: 'Success',
          text: 'Bid amount updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } catch(error) {
        console.error(error)
        this.$vs.notify({
          title: 'Error',
          text: 'Bid amount cannot be modified',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.showPopup['edit_bid'] = false
      this.$vs.loading.close()
    },
    duplicateAdsetPopup(obj) {
      this.editingObject = {
        id: obj.id,
        adsetNumber: 1,
        adsetBudget: 1,
      }
      this.showPopup['duplicate_adset'] = true
    },
    async duplicateAdset(id, n) {
      let adsets = []

      // Make a batch request to create N adsets (copying from id)
      let batch = []
      for (let i = 0; i < n; i++) {
        batch.push({
          method: 'POST',
          relative_url: '/' + this.fbApiVersion + '/' + id + '/copies',
          name: 'duplicate_adset_' + i,
          body: 'deep_copy=true',
        })
      }

      let params = {
        access_token: this.accessToken,
        asyncbatch: batch,
      }
      const responseAsyncBatch = await this.$http.post(this.fbApiUrl, params)

      params = {
        access_token: this.accessToken,
        fields: 'id,name,status,result',
      }
      let sessions = responseAsyncBatch.data.async_sessions
      for (let sessionIndex = 0; sessionIndex < sessions.length; sessionIndex++) {
        let session = sessions[sessionIndex]
        let notCompleted = true
        while (notCompleted) {
          await sleep(1000)
          const response = await this.$http.get(this.fbApiUrl + '/' + session.id, { params: params })

          if (response.data.status !== 'COMPLETED' && response.data.status !== 'FAILED') {
            notCompleted = true
            continue
          }

          notCompleted = false
          if (response.data.status === 'COMPLETED') {
            let result = JSON.parse(response.data.result)
            if (result.hasOwnProperty('copied_adset_id')) {
              adsets.push(result.copied_adset_id)
            }
          }
        }
      }

      // DEBUG TODO: to delete
      console.log('adsets', adsets)

      return adsets
    },
    async duplicateAdsetNormal(obj) {
      this.$vs.loading()

      try {
        // Duplicate N times
        let adsets = await this.duplicateAdset(obj.id, obj.adsetNumber)

        // TODO: Check if adsets.length == obj.adsetNumber

        // Edit adset name and daily budget
        let params = {
          access_token: this.accessToken,
          name: 'Duplicate - €' + parseFloat(obj.adsetBudget).toFixed(2),
          daily_budget: parseInt(obj.adsetBudget)*100,
        }
        for (let adsetIndex = 0; adsetIndex < adsets.length; adsetIndex++) {
          let adsetId = adsets[adsetIndex]
          await this.$http.post(this.fbApiUrl + '/' + adsetId, params)
        }

        this.$vs.notify({
          title: 'Success',
          text: 'Adset duplicated ' + obj.adsetNumber + ' times.',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } catch (error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Error while duplicating adset',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    duplicateAdsetBidderPopup(obj) {
      this.editingObject = {
        adsets: [{
          budget: 0.0,
          bid: 'COST_CAP',
          bid_amount: 0.0,
          accelerated: false,
        }],
      }
      this.showPopup['duplicate_adset_bidder'] = true
    },
    removeBidderAdset(index) {
      this.editingObject.adsets.splice(index, 1)
    },
    addBidderAdset() {
      this.editingObject.adsets.push({
        budget: 0.0,
        bid: 'COST_CAP',
        bid_amount: 0.0,
        accelerated: false,
      })
    },
    async duplicateAdsetBidder(obj) {
      this.$vs.loading()

      try {
        // Duplicate N times
        let adsets = await this.duplicateAdset(obj.id, obj.adsets.length)

        // TODO: Check if adsets.length == obj.adsets.length

        // Edit adset name, daily budget, bid strategy and bid amount
        for (let adsetIndex = 0; adsetIndex < adsets.length; adsetIndex++) {
          let adsetId = adsets[adsetIndex]
          let adset = obj.adsets[adsetIndex]

          let bidType = 'Unknown'
          if (adset.bid == 'LOWEST_COST_WITH_BID_CAP') {
            bidType = 'BID_CAP'
          } else if (adset.bid == 'COST_CAP') {
            bidType = 'COST_CAP'
          }
          let params = {
            access_token: this.accessToken,
            name: 'BIDDER - ' + bidType + ' - €' + parseFloat(adset.bid_amount).toFixed(2),
            daily_budget: parseInt(adset.budget*100),
            bid_strategy: adset.bid,
            bid_amount: parseInt(adset.bid_amount*100),
          }
          if (adset.accelerated) {
            params.name += ' - Acc.'
          }
          await this.$http.post(this.fbApiUrl + '/' + adsetId, params)
        }

        this.$vs.notify({
          title: 'Success',
          text: 'Adset duplicated ' + obj.adsetNumber + ' times (bidder).',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } catch (error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Error while duplicating adset (bidder)',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    async fetchAdTemplates() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.adTemplates = []
      const response = await this.$http.get('/api/3rd/fb-ad-templates', { headers })
      this.adTemplates = response.data.data

      this.$vs.loading.close()
    },
    async fetchProductMedias() {
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      const productMediasResponse = await this.$http.get('/api/v1/product-medias', { headers })
      this.productMedias = productMediasResponse.data.data
      this.productMediasFiltered = []
      for (let i = 0; i < this.productMedias.length; i++) {
        if (this.productMedias[i].product.id == this.productMediasFilterProductId) {
          this.productMediasFiltered.push(this.productMedias[i])
        }
      }
    },
    async adLauncherPopup(ad) {
      this.ad = {
        view: 'normal',
        pageId: '',

        campaignName: '',
        campaignStatus: 'ACTIVE',
        campaignObjective: 'LINK_CLICKS',
        pixelId: 'AUTOMATIC',
        
        conversionEvent: 'PURCHASE',

        campaignBudgetStrategy: 'Adset',
        campaignBudget: 0,
        campaignBidStrategy: 'LOWEST_COST_WITHOUT_CAP',
        campaignBid: 1,
        splitAdset: "false",

        targetingGender: 'All',
        targetingAgeMin: 25,
        targetingAgeMax: 0,

        targetingCountry: 'IT',
        targetingDevice: '',
        targetingWifi: true,
        targetingPlatformFacebook: true,
        targetingPlatformInstagram: true,
        targetingPlatformAudience: true,
        targetingPlatformMessenger: true,

        title: '',
        text: '',
        url: '',
        conversionDomain: '',

        filesToUpload: [],

      }

      await this.fetchAdTemplates()

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Retrieve product medias
      await this.fetchProductMedias()

      // Retrieve facebook pages of this account
      let params = {
        access_token: this.accessToken,
        fields: 'id,name,is_published',
        is_promotable: true,
      }
      this.fbAccountPages = []
      const fbPagesResponse = await this.$http.get(this.fbApiUrl + '/me/accounts', { params: params })
      fbPagesResponse.data.data.forEach((page) => {
        if (page.hasOwnProperty('is_published') && page.is_published) {
          this.fbAccountPages.push({
            id: page.id,
            name: page.name,
          })
        }
      })

      this.$vs.loading.close()
      this.showPopup['ad_launcher'] = true
    },
    async launchAd() {
      this.$vs.loading()

      let adAccount = this.adAccount

      let currentAdUrl = this.ad.url
      if (currentAdUrl.includes('?')) {
        currentAdUrl = currentAdUrl + '&'
      } else {
        currentAdUrl = currentAdUrl + '?'
      }
      currentAdUrl = currentAdUrl + 'pi=' + adAccount.pixel

      // Create campaign
      let campaign = null
      let campaignParams = {
        access_token: this.accessToken,
        name: this.ad.campaignName,
        objective: this.ad.campaignObjective,
        status: this.ad.campaignStatus,
        special_ad_categories: [],
      }
      if (this.ad.campaignBudgetStrategy == 'CBO') {
        campaignParams.bid_strategy = this.ad.campaignBidStrategy
        campaignParams.daily_budget = parseInt(parseFloat(this.ad.campaignBudget)*100)
      }

      let params = {
        access_token: this.accessToken,
        fields: 'id,username',
      }
      const pagesResponse = await this.$http.get(this.fbApiUrl + '/' + this.ad.pageId + '/page_backed_instagram_accounts', { params: params })
      if (pagesResponse.hasOwnProperty('data') && pagesResponse.data.data.length == 0) {
        this.$vs.loading.close()
        console.log('Page backed instagram account (PBIA) error')
        return
      }
      let pbiaId = pagesResponse.data.data[0].id

      const campaignResponse = await this.$http.post(this.fbApiUrl + '/' + adAccount.id + '/campaigns', campaignParams)
      if (campaignResponse.hasOwnProperty('data') && campaignResponse.data.hasOwnProperty('id')) {
        campaign = campaignResponse.data
      } else {
        this.$vs.loading.close()
        console.log('Error: cannot create campaign')
        return
      }

      // Create adset
      let adsets = []
      let adsetParams = {
        access_token: this.accessToken,
        name: 'Adset',
        optimization_goal: this.ad.campaignObjective,
        billing_event: 'IMPRESSIONS', // TODO: What about this?
        campaign_id: campaign.id,
        targeting: {},
        status: this.ad.campaignStatus,
      }
      if (this.ad.campaignBidStrategy != 'LOWEST_COST_WITHOUT_CAP') {
        adsetParams.bid_amount = parseInt(parseFloat(this.ad.campaignBid)*100)
      }
      if (this.ad.campaignBudgetStrategy != 'CBO') {
        adsetParams.bid_strategy = this.ad.campaignBidStrategy
        adsetParams.daily_budget = parseInt(parseFloat(this.ad.campaignBudget)*100)
      }
      if (this.ad.targetingAgeMin > 0) {
        adsetParams.targeting.age_min = this.ad.targetingAgeMin
      }
      if (this.ad.targetingAgeMax > 0) {
        adsetParams.targeting.age_max = this.ad.targetingAgeMax
      }
      adsetParams.targeting.geo_locations = { countries: [] }
      adsetParams.targeting.geo_locations.countries.push(this.ad.targetingCountry)
      if (this.ad.targetingGender == 'Male') {
        adsetParams.targeting.genders = [1]
      } else if (this.ad.targetingGender == 'Female') {
        adsetParams.targeting.genders = [2]
      }

      if (this.ad.targetingDevice != '') {
        adsetParams.targeting.device_platforms = []
        adsetParams.targeting.device_platforms.push(this.ad.targetingDevice)
      }
      if (this.ad.targetingWifi) {
        adsetParams.targeting.wireless_carrier = []
        adsetParams.targeting.wireless_carrier.push('Wifi')
      }

      if (this.ad.targetingPlatformFacebook ||
        this.ad.targetingPlatformInstagram ||
        this.ad.targetingPlatformAudience ||
        this.ad.targetingPlatformMessenger) {
        adsetParams.targeting.publisher_platforms = []
        if (this.ad.targetingPlatformFacebook) {
          adsetParams.targeting.publisher_platforms.push('facebook')
        }
        if (this.ad.targetingPlatformInstagram) {
          adsetParams.targeting.publisher_platforms.push('instagram')
        }
        if (this.ad.targetingPlatformMessenger) {
          adsetParams.targeting.publisher_platforms.push('messenger')
        }
        if (this.ad.targetingPlatformAudience) {
          adsetParams.targeting.publisher_platforms.push('audience_network')
        }
      }

      let trackingSpecs = ''
      if (this.ad.campaignObjective == 'CONVERSIONS') {
        adsetParams.promoted_object = {
          custom_event_type: this.ad.conversionEvent,
          pixel_id: adAccount.pixel,
        }
        adsetParams.optimization_goal = 'OFFSITE_CONVERSIONS'
        trackingSpecs = [
          { 'action.type': 'offsite_conversion', 'fb_pixel': adAccount.pixel },
        ]
        adsetParams.tracking_specs = trackingSpecs
      }

      let adsetIds = []

      let adsetsToCreate = 1
      if (this.ad.splitAdset == "true") {
        adsetsToCreate = this.selectedProductMedias.length
      }
      for (let adsetIndexTmp = 0; adsetIndexTmp < adsetsToCreate; adsetIndexTmp++) {
        const adsetResponse = await this.$http.post(this.fbApiUrl + '/' + adAccount.id + '/adsets', adsetParams)

        if (adsetResponse.hasOwnProperty('data') && adsetResponse.data.hasOwnProperty('id')) {
          adsets.push(adsetResponse.data)
        } else {
          this.$vs.loading.close()
          console.log('Error: cannot create adset')
          return
        }
      }

      let previewHash = ''
      if (this.selectedPreview.length > 0) {
        // Create ad preview (upload media on facebook)
        let productMediaPreview = this.productMediasFiltered[this.selectedPreview[0]] // TODO: For now there's only one preview
        let previewBytes = await convertImgToBase64Raw(productMediaPreview.url)
        let adPreviewParams = {
          access_token: this.accessToken,
          name: 'testimg.png',
          bytes: previewBytes,
        }
        const adPreviewResponse = await this.$http.post(this.fbApiUrl + '/' + adAccount.id + '/adimages', adPreviewParams)
        if (adPreviewResponse.hasOwnProperty('data') && adPreviewResponse.data.hasOwnProperty('images')) {
          for (const [key, value] of Object.entries(adPreviewResponse.data.images)) {
            previewHash = value.hash
          }
        } else {
          this.$vs.loading.close()
          console.log('Error: cannot create ad preview image')
          return
        }
      }

      let adsetIndex = 0
      for (let productMediaIndex = 0; productMediaIndex < this.selectedProductMedias.length; productMediaIndex++) {
        let pmIndex = this.selectedProductMedias[productMediaIndex]
        let productMedia = this.productMediasFiltered[pmIndex]

        let adsetId = adset[adsetIndex].id
        adsetIndex++

        // Create ad images and ad videos
        let conversionDomain = this.ad.conversionDomain
        let pageId = this.ad.pageId // TODO: Better
        let adParams = {
          access_token: this.accessToken,
          adset_id: adsetId,
          name: 'ad',
          status: this.ad.campaignStatus,
          conversion_domain: conversionDomain,
        }

        if (productMedia.type == 'image') {
          let bytes = await convertImgToBase64Raw(productMedia.url)
          let adImageParams = {
            access_token: this.accessToken,
            name: 'testimg.png',
            bytes: result,
          }
          const adImageResponse = await this.$http.post(this.fbApiUrl + '/' + adAccount.id + '/adimages', adImageParams)
          let imageHash = ''
          if (adImageResponse.hasOwnProperty('data') && adImageResponse.data.hasOwnProperty('images')) {
            for (const [key, value] of Object.entries(adImageResponse.data.images)) {
              imageHash = value.hash
            }
          } else {
            this.$vs.loading.close()
            console.log('Error: cannot create ad image')
            return
          }
          adParams.creative = {
            name: 'ad creative',
            object_story_spec: {
              link_data: {
                call_to_action: { type: 'LEARN_MORE', value: { link: currentAdUrl } },
                link: currentAdUrl,
                name: this.ad.title,
                message: this.ad.text,
                image_hash: imageHash,
              },
              page_id: pageId,
              instagram_actor_id: pbiaId,
            },
            instagram_actor_id: pbiaId,
          }
          if (trackingSpecs != '') {
            data.tracking_specs = trackingSpecs
          }
        } else if (productMedia.type == 'video') {
          let videoData = {
            access_token: this.accessToken,
            file_url: productMedia.url,
          }
          const videoResponse = await this.$http.post(this.fbApiUrl + '/' + adAccount.id + '/advideos', videoData)
          let videoId = null
          if (videoResponse.hasOwnProperty('data') && videoResponse.data.hasOwnProperty('id')) {
            let videoId = response.data.id
          } else {
            this.$vs.loading.close()
            console.log('Error: cannot create ad video')
            return
          }
          adParams.creative = {
            name: 'ad creative',
            object_story_spec: {
              video_data: {
                call_to_action: { type: 'LEARN_MORE', value: { link: currentAdUrl } },
                link: currentAdUrl,
                title: this.ad.title,
                message: this.ad.text,
                video_id: videoId,
              },
              page_id: pageId,
              instagram_actor_id: pbiaId,
            },
            instagram_actor_id: pbiaId,
          }
          if (previewHash !== '') {
            adParams.creative.object_story_spec.video_data.image_hash = previewHash
          }
        }
        // Create ad
        const adResponse = await this.$http.post(this.fbApiUrl + '/' + adAccount.id + '/ads', adParams)
        if (adResponse.hasOwnProperty('data') && adResponse.data.hasOwnProperty('id')) {
          // Ad created
        } else {
          this.$vs.loading.close()
          console.log('Error: cannot create ad')
          return
        }
      }
    },
    getAdObject() {
      return {
        campaign_name: this.ad.campaignName,
        campaign_status: this.ad.campaignStatus,
        campaign_objective: this.ad.campaignObjective,
        pixel_id: this.ad.pixelId,
        conversion_event: this.ad.conversionEvent,
        campaign_budget_strategy: this.ad.campaignBudgetStrategy,
        campaign_budget: this.ad.campaignBudget,
        campaign_bid_strategy: this.ad.campaignBidStrategy,
        campaign_bid: this.ad.campaignBid,
        split_adset: this.ad.splitAdset,

        targeting_gender: this.ad.targetingGender,
        targeting_age_min: this.ad.targetingAgeMin,
        targeting_age_max: this.ad.targetingAgeMax,
        targeting_country: this.ad.targetingCountry,
        targeting_device: this.ad.targetingDevice,
        targeting_wifi: this.ad.targetingWifi,
        targeting_platform_facebook: this.ad.targetingPlatformFacebook,
        targeting_platform_instagram: this.ad.targetingPlatformInstagram,
        targeting_platform_audience: this.ad.targetingPlatformAudience,
        targeting_platform_messenger: this.ad.targetingPlatformMessenger,

        title: this.ad.title,
        text: this.ad.text,
        url: this.ad.url,
        conversion_domain: this.ad.conversionDomain,
      }
    },
    loadAdTemplate(adTemplate) {
      this.ad.campaignName = adTemplate.value.campaign_name
      this.ad.campaignStatus = adTemplate.value.campaign_status
      this.ad.campaignObjective = adTemplate.value.campaign_objective
      this.ad.pixelId = adTemplate.value.pixel_id
      this.ad.conversionEvent = adTemplate.value.conversion_event
      this.ad.campaignBudgetStrategy = adTemplate.value.campaign_budget_strategy
      this.ad.campaignBudget = adTemplate.value.campaign_budget
      this.ad.campaignBidStrategy = adTemplate.value.campaign_bid_strategy
      this.ad.campaignBid = adTemplate.value.campaign_bid
      this.ad.splitAdset = adTemplate.value.split_adset

      this.ad.targetingGender = adTemplate.value.targeting_gender
      this.ad.targetingAgeMin = adTemplate.value.targeting_age_min
      this.ad.targetingAgeMax = adTemplate.value.targeting_age_max
      this.ad.targetingCountry = adTemplate.value.targeting_country
      this.ad.targetingDevice = adTemplate.value.targeting_device
      this.ad.targetingWifi = adTemplate.value.targeting_wifi
      this.ad.targetingPlatformFacebook = adTemplate.value.targeting_platform_facebook
      this.ad.targetingPlatformInstagram = adTemplate.value.targeting_platform_instagram
      this.ad.targetingPlatformAudience = adTemplate.value.targeting_platform_audience
      this.ad.targetingPlatformMessenger = adTemplate.value.targeting_platform_messenger

      this.ad.title = adTemplate.value.title
      this.ad.text = adTemplate.value.text
      this.ad.url = adTemplate.value.url
      this.ad.conversionDomain = adTemplate.value.conversion_domain

      this.newAdTemplateName = adTemplate.name
    },
    async fetchAdTemplates() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.adTemplates = []
      const response = await this.$http.get('/api/3rd/fb-ad-templates', { headers })
      this.adTemplates = response.data.data

      this.$vs.loading.close()
    },
    async saveAdTemplate() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let value = this.getAdObject()

      let data = {
        name: this.newAdTemplateName,
        value: JSON.stringify(value),
      }

      let alreadyExists = false
      let adTemplateId = ''
      for (let i = 0; i < this.adTemplates.length; i++) {
        if (this.adTemplates[i].name == this.newAdTemplateName) {
          alreadyExists = true
          adTemplateId = this.adTemplates[i].id
        }
      }

      try {
        if (!alreadyExists) {
          const response = await this.$http.post('/api/3rd/fb-ad-templates', data, { headers })
        } else {
          const response = await this.$http.put('/api/3rd/fb-ad-templates/' + adTemplateId, data, { headers })
        }
        this.$vs.notify({
          title: 'Success',
          text: 'Ad template saved',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
        await this.fetchAdTemplates()
      } catch (error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Unknown error while saving ad template',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    async deleteAdTemplate() {
      this.$vs.loading()

      let id = this.adTemplate.id

      if (id <= 0) return

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      await this.$http.delete('/api/3rd/fb-ad-templates/' + id, { headers })

      this.$vs.loading.close()

      await this.fetchAdTemplates()
    },
    showAdAccountPopup(adAccount) {
      this.adAccount = adAccount
      this.showForm('ad_account')
    },
    selectedProductMediasHaveVideo() {
      for (let i = 0; i < this.selectedProductMedias.length; i++) {
        if (this.productMediasFiltered[this.selectedProductMedias[i]].type == 'video') {
          return true
        }
      }
      return false
    },

    // Rules methods
    async fetchRules() {
      let rules = []
      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        const response = await this.$http.get('/api/3rd/facebook/rules', { headers: headers })
        if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
          rules = response.data.data
        }
      } catch (error) {
        // Nothing
      }
      return rules
    },
    showCampaignList(rule) {
      // TODO:
    },
    async runRule(rule) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      try {
        const response = await this.$http.post('/api/3rd/facebook/rules/' + rule.id + '/run', {}, { headers })
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
    async createRule() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        name: this.rule.name,
        access_token: this.accessToken,
        ad_account_id: this.rule.ad_account_id,
        all_campaigns: this.rule.all_campaigns,
        campaign_ids: this.rule.campaign_ids,
        rules: JSON.stringify(this.rule.rules),
        actions: JSON.stringify(this.rule.actions),
        frequency: this.rule.frequency,
        time: this.rule.time,
      }
      try {
        const response = await this.$http.post('/api/3rd/facebook/rules', data, { headers })
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
      let adAccountId = value.ad_account_id
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
        ad_account_id: adAccountId,
        all_campaigns: this.rule.all_campaigns,
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
        access_token: this.accessToken,
        ad_account_id: this.rule.ad_account_id,
        all_campaigns: this.rule.all_campaigns,
        campaign_ids: this.rule.campaign_ids,
        rules: JSON.stringify(this.rule.rules),
        actions: JSON.stringify(this.rule.actions),
        frequency: this.rule.frequency,
        time: this.rule.time,
      }
      try {
        const response = await this.$http.put('/api/3rd/facebook/rules/' + this.rule.id, data, { headers })
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
        const response = await this.$http.put('/api/3rd/facebook/rules/' + rule.id, data, { headers })
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
        const response = await this.$http.delete('/api/3rd/facebook/rules/' + rule.id, { headers })
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
      this.campaignsOptions = await this.fetchCampaigns(this.rule.ad_account_id, false)
      this.$vs.loading.close()
    },
    showCreateRuleForm() {
      this.rule = {
        name: '',
        ad_account_id: '',
        all_campaigns: false,
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
      let ruleTime = 'lifetime'
      if (rule.value.hasOwnProperty('time')) {
        ruleTime = rule.value.time
      }

      this.rule = {
        id: rule.id,
        name: rule.name,
        ad_account_id: rule.value.ad_account_id,
        all_campaigns: (rule.value.hasOwnProperty('all_campaigns') ? rule.value.all_campaigns : false),
        campaign_ids: forceJson(rule.value.campaign_ids),
        rules: forceJson(rule.value.rules),
        actions: forceJson(rule.value.actions),
        frequency: rule.frequency,
        time: ruleTime,
      }
      this.currAdAccId = rule.value.ad_account_id

      this.showPopup['edit_rule'] = true
      this.refreshCampaignsOptions()
    },
    showBulkEditRuleForm() {
      if (this.selectedRules.length <= 0) {
        return
      }

      let adAccId = this.selectedRules[0].value.ad_account_id
      for (let i = 0; i < this.selectedRules.length; i++) {
        if (this.selectedRules[0].value.ad_account_id != adAccId) {
          return
        }
      }

      let bulkCampaignIds = this.selectedRules[0].value.campaign_ids
      for (let i = 0; i < this.selectedRules.length; i++) {
        bulkCampaignIds = intersect(this.selectedRules[i].value.campaign_ids, bulkCampaignIds)
      }

      this.rule = {
        name: '',
        ad_account_id: adAccId,
        campaign_ids: bulkCampaignIds,
        rules: [],
        actions: [],
        frequency: 'hourly',
        time: 'lifetime',
      }
      this.currAdAccId = adAccId

      this.showPopup['bulk_edit_rule'] = true
      this.refreshCampaignsOptions()
    },
    async bulkUpdateRules() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        ad_account_id: this.rule.ad_account_id,
        campaign_ids: this.rule.campaign_ids,
      }
      for (let i = 0; i < this.selectedRules.length; i++) {
        let rule = this.selectedRules[i]
        try {
          const response = await this.$http.put('/api/3rd/facebook/rules/' + rule.id, data, { headers })
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
      this.currAdAccId = null
      this.rule.id = rule.id
      this.rule.ad_account_id = rule.value.ad_account_id
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
        ad_account_id: this.rule.ad_account_id,
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
    async copyIds() {
      let stringToCopy = ''
      let objs = this.selectedObjects
      for (let i = 0; i < objs.length; i++) {
        stringToCopy += objs[i].id + '\n'
      }
      await navigator.clipboard.writeText(stringToCopy)
      this.$vs.notify({
        title: 'Success',
        text: 'Ids copied to the clipboard',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
  },
  async mounted() {
    await this.autoLogin()

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
