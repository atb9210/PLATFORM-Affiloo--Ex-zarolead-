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

        <div class="vx-row mb-2" v-if="view == 'adaccounts' || view == 'campaigns' || view == 'adsets' || view == 'ads'">
            <div class="vx-col w-1/3">
            </div>
            <div class="vx-col w-1/3 text-center">
                <my-select
                    :autocomplete="true"
                    @input="onAdvertiserSwitch"
                    label="Switch Advertiser"
                    v-model="advertiserSwitch"
                    :options="advertisers" />
            </div>
            <div class="vx-col w-1/3">
            </div>
        </div>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <span class="btn-group flex items-center mr-2">
                <vs-button @click="changeView('advertisers')" :color="(view == 'advertisers') ? 'success' : 'primary'">Advertisers</vs-button>
                <vs-button @click="changeView('adaccounts')" :color="(view == 'adaccounts') ? 'success' : 'primary'">Ad Accounts</vs-button>
                <vs-button @click="changeView('campaigns')" :color="(view == 'campaigns') ? 'success' : 'primary'">Campaigns</vs-button>
                <vs-button @click="changeView('adsets')" :color="(view == 'adsets') ? 'success' : 'primary'">Adsets</vs-button>
                <vs-button @click="changeView('ads')" :color="(view == 'ads') ? 'success' : 'primary'">Ads</vs-button>
            </span>

            <vs-button v-if="view == 'advertisers'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showCreateAdvertiserForm"></vs-button>
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
                    <!--
                    <vs-select autocomplete v-model="filters.status" class="w-full select-large">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in statusOptions" class="w-full" />
                    </vs-select>
                    -->
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
              <a href="#" @click="$refs.advertiser_context_menu.close();changeView('adaccounts', data.id);" class="flex items-center text-sm">
                <feather-icon icon="UserIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show Ad Accounts</span>
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

        <vue-context ref="adaccount_context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.adaccount_context_menu.close();changeView('campaigns', data.id);" class="flex items-center text-sm">
                <feather-icon icon="UserIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show Campaigns</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.adaccount_context_menu.close()" class="flex items-center text-sm">
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

            <!-- Advertisers table -->
            <vs-table v-if="view == 'advertisers'" stripe multiple v-model="selectedAdvertisers" :max-items="maxItems" pagination search :data="advertisers" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="id">Zaro Id</vs-th>
                    <vs-th sort-key="name">Name</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td class="column-no-padding" :data="data[indextr].id">
                            {{data[indextr].id}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].name">
                            <div @contextmenu.prevent="$refs.advertiser_context_menu.open($event, data[indextr])">
                                <vs-chip transparent>
                                    <vs-avatar icon-pack="feather" icon="icon-user" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                    </vs-tr>

                </template>
            </vs-table>

            <!-- Ad accounts table -->
            <vs-table v-if="view == 'adaccounts'" stripe multiple v-model="selectedAdAccounts" :max-items="maxItems" pagination search :data="adAccounts" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="spend">Spend</vs-th>
                    <vs-th sort-key="conversions">Conversions</vs-th>
                    <vs-th sort-key="cpm">CPM</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td class="column-no-padding" :data="data[indextr].name">
                            <div @contextmenu.prevent="$refs.adaccount_context_menu.open($event, data[indextr])">
                                <vs-chip transparent>
                                    <vs-avatar icon-pack="feather" icon="icon-user" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].spend">
                            € {{data[indextr].spend.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].conversions">
                            {{data[indextr].conversions}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].cpm">
                            € {{data[indextr].cpm.toFixed(2)}}
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{data.length}}</vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.spend.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{total_stats.conversions}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.cpm.toFixed(2)}}
                    </vs-td>
                    <!-- END TOTAL STATS-->

                </template>
            </vs-table>

            <!-- Campaigns table -->
            <vs-table v-if="view == 'campaigns'" stripe multiple v-model="selectedCampaigns" :max-items="maxItems" pagination search :data="campaigns" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <vs-th sort-key="spend">Spend</vs-th>
                    <vs-th sort-key="conversions">Conversions</vs-th>
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
                            <div @contextmenu.prevent="$refs.advertiser_context_menu.open($event, data[indextr])">
                                <vs-chip transparent>
                                    <vs-avatar icon-pack="feather" icon="icon-user" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].status">
                            STATUS (TODO)
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].spend">
                            € {{data[indextr].spend.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].conversions">
                            {{data[indextr].conversions}}
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
                            {{data[indextr].roi.toFixed(2)}}%
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].budget">
                            € {{data[indextr].budget.toFixed(2)}}
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{data.length}}</vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.spend.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{total_stats.conversions}}
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
                        {{total_stats.roi.toFixed(2)}}%
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.budget.toFixed(2)}}
                    </vs-td>
                    <!-- END TOTAL STATS-->

                </template>
            </vs-table>

            <!-- Adsets table -->
            <vs-table v-if="view == 'adsets'" stripe multiple v-model="selectedAdsets" :max-items="maxItems" pagination search :data="adsets" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <vs-th sort-key="spend">Spend</vs-th>
                    <vs-th sort-key="conversions">Conversions</vs-th>
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
                            <div @contextmenu.prevent="$refs.advertiser_context_menu.open($event, data[indextr])">
                                <vs-chip transparent>
                                    <vs-avatar icon-pack="feather" icon="icon-user" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].status">
                            STATUS (TODO)
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].spend">
                            € {{data[indextr].spend.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].conversions">
                            {{data[indextr].conversions}}
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
                            {{data[indextr].roi.toFixed(2)}}%
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].budget">
                            € {{data[indextr].budget.toFixed(2)}}
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{data.length}}</vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.spend.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{total_stats.conversions}}
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
                        {{total_stats.roi.toFixed(2)}}%
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
                    <vs-th sort-key="spend">Spend</vs-th>
                    <vs-th sort-key="conversions">Conversions</vs-th>
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
                            <div @contextmenu.prevent="$refs.advertiser_context_menu.open($event, data[indextr])">
                                <vs-chip transparent>
                                    <vs-avatar icon-pack="feather" icon="icon-user" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].status">
                            STATUS (TODO)
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].spend">
                            € {{data[indextr].spend.toFixed(2)}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].conversions">
                            {{data[indextr].conversions}}
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
                            {{data[indextr].roi.toFixed(2)}}%
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].budget">
                            € {{data[indextr].budget.toFixed(2)}}
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{data.length}}</vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.spend.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{total_stats.conversions}}
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
                        {{total_stats.roi.toFixed(2)}}%
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{total_stats.budget.toFixed(2)}}
                    </vs-td>
                    <!-- END TOTAL STATS-->

                </template>
            </vs-table>

        </div>

        <!-- Create advertiser popup -->
        <vs-popup title="Create advertiser" :active.sync="showPopup['create_advertiser']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{(advertiser.name == '') ? 'auto' : advertiser.name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name (empty = name on Facebook)</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="advertiser.name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Access Token</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="advertiser.accessToken" />
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
                    <span>Access Token</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="advertiser.accessToken" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateAdvertiser" color="primary" type="filled" :disabled="!isValidForm('edit_advertiser')">Update advertiser</vs-button>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import VueApexCharts from 'vue-apexcharts'

import { VueContext } from 'vue-context'

import Facebook from 'Facebook'

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

      maxItems: 20,

      selectedAdvertisers: [],
      selectedAdAccounts: [],
      selectedCampaigns: [],
      selectedAdsets: [],
      selectedAds: [],

      advertisersLoaded: false,
      adAccountsLoaded: false,
      campaignsLoaded: false,
      adsetsLoaded: false,
      adsLoaded: false,

      advertisers: [],
      adAccounts: [],
      campaigns: [],
      adsets: [],
      ads: [],

      advertiserSwitch: null,
      currentAdvertiserId: null,
      currentAdAccountId: null,
      currentCampaignId: null,
      currentAdgroupId: null,
      currentAdId: null,

      total_stats: {
        spend: 0,
        conversions: 0,
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

      showPopup: {
        'create_advertiser': false,
        'edit_advertiser': false,
      },

      advertiser: {
        id: '',
        name: '',
        accessToken: '',
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
      if (this.view == 'advertisers') {
        return this.advertisers
      } else {
        return []
      }
    },
    selectedObjects() {
      if (this.view == 'advertisers') {
        return this.selectedAdvertisers
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
      let spend = 0
      let conversions = 0
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

        if (obj.hasOwnProperty('spend')) {
          spend += obj.spend
        }
        if (obj.hasOwnProperty('conversions')) {
          conversions += obj.conversions
        }
        if (obj.hasOwnProperty('impressions')) {
          impressions += obj.impressions
        }
        if (obj.hasOwnProperty('budget')) {
          budget += obj.budget
        }
      }
      cpa = (conversions > 0) ? (spend/conversions) : spend
      cr  = (clicks > 0) ? ((conversions/clicks)*100) : ((conversions > 0) ? 9999.99 : 0)
      // TODO
      //cpm = (impressions > 0) ? ((spend*1000)/impressions) : (spend*1000)
      ctr = (impressions > 0) ? ((clicks/impressions)*100) : ((clicks > 0) ? 9999.99 : 0)
      cpc = (clicks > 0) ? (spend/clicks) : spend

      this.total_stats = {
        spend: spend,
        conversions: conversions,
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

      this.advertisers = await this.facebook.fetchAdvertisers()
      for (let i = 0; i < this.advertisers.length; i++) {
        let obj = this.advertisers[i]
        obj.value = obj.id
        obj.label = obj.name
        this.advertisers[i] = obj

        this.advertiserSwitch = obj.id
      }

      this.advertisersLoaded = true

      this.$vs.loading.close()
    },
    async loadAdAccounts() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      this.adAccounts = await this.facebook.fetchAdAccounts(this.currentAdvertiserId, this.filters)

      this.adAccountsLoaded = true
      this.advertiserSwitch = this.currentAdvertiserId

      this.$vs.loading.close()
    },
    async loadCampaigns(parentId) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.campaigns = await this.facebook.fetchCampaigns(this.currentAdvertiserId, parentId, this.filters)

      this.campaignsLoaded = true
      this.advertiserSwitch = this.currentAdvertiserId

      this.currentAdAccountId = parentId

      this.$vs.loading.close()
    },
    async loadAdsets(parentId) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      this.adsets = await this.facebook.fetchAdsets(this.currentAdvertiserId, parentId, this.filters)

      this.adsetsLoaded = true
      this.advertiserSwitch = this.currentAdvertiserId

      this.currentCampaignId = parentId

      this.$vs.loading.close()
    },
    async loadAds(parentId) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      this.ads = await this.facebook.fetchAds(this.currentAdvertiserId, parentId, this.filters)

      this.adsLoaded = true
      this.advertiserSwitch = this.currentAdvertiserId

      this.currentAdsetId = parentId

      this.$vs.loading.close()
    },
    async applyFilters() {
      this.selectedAdvertisers = []
      this.selectedAdAccounts = []
      this.selectedCampaigns = []
      this.selectedAdsets = []
      this.selectedAds = []

      this.advertisersLoaded = false
      this.adAccountsLoaded = false
      this.campaignsLoaded = false
      this.adsetsLoaded = false
      this.adsLoaded = false

      let parentId = null
      if (this.view === 'adaccounts') {
        parentId = this.currentAdvertiserId
      } else if (this.view === 'campaigns') {
        parentId = this.currentAdAccountId
      } else if (this.view === 'adsets') {
        parentId = this.currentCampaignId
      } else if (this.view === 'ads') {
        parentId = this.currentAdsetId
      }
      this.changeView(this.view, parentId)

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

      if ((view == 'campaigns' || view == 'adsets' || view == 'ads') &&
        (this.currentAdvertiserId == null)) {
        return
      }
      if ((view == 'adaccounts') && (parentId == null && this.currentAdvertiserId == null)) {
        return
      }

      this.view = view

      if (view == 'advertisers') {
        if (!this.advertisersLoaded) {
          await this.loadAdvertisers()
        }
      } else if (view == 'adaccounts') {
        if (parentId !== null) {
          if (parentId !== this.currentAdvertiserId) {
            this.adAccountsLoaded = false
          }
          this.currentAdvertiserId = parentId
        }
        if (!this.adAccountsLoaded) {
          await this.loadAdAccounts()
        }
      } else if (view == 'campaigns') {
        if (!this.campaignsLoaded) {
          await this.loadCampaigns(parentId)
        }
      } else if (view == 'adsets') {
        if (!this.adsetsLoaded) {
          await this.loadAdsets(parentId)
        }
      } else if (view == 'ads') {
        if (!this.adsLoaded) {
          await this.loadAds(parentId)
        }
      }

      this.calculateStats()
    },
    isValidForm(type) {
      if (type == 'create_advertiser' || type == 'edit_advertiser') {
        if (this.advertiser.accessToken != '') {
          return true
        }
      }
      return false
    },
    showCreateAdvertiserForm() {
      this.advertiser = {
        name: '',
        accessToken: '',
      }
      this.showPopup['create_advertiser'] = true
    },
    showEditAdvertiserForm(adv) {
      this.advertiser = {
        id: adv.id,
        name: adv.name,
      }
      this.showPopup['edit_advertiser'] = true
    },
    async createAdvertiser() {
      this.$vs.loading()

      let advertiser = await this.facebook.createAdvertiser(this.advertiser.accessToken.trim(), this.advertiser.name.trim())
      if (advertiser !== null) {
        this.$vs.notify({
          title: 'Success',
          text: 'Advertiser created',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
        this.showPopup['create_advertiser'] = false
        this.applyFilters()
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot create advertiser',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    async updateAdvertiser() {
      this.$vs.loading()

      let advertiser = await this.facebook.updateAdvertiser(this.advertiser.id, this.advertiser.accessToken.trim())
      if (advertiser !== null) {
        this.$vs.notify({
          title: 'Success',
          text: 'Advertiser updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
        this.showPopup['edit_advertiser'] = false
        this.applyFilters()
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update advertiser',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    async deleteAdvertiser(adv) {
      this.$vs.loading()

      let deleted = await this.facebook.deleteAdvertiser(adv.id)
      if (deleted) {
        this.$vs.notify({
          title: 'Success',
          text: 'Advertiser deleted',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
        this.applyFilters()
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot delete advertiser',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },

    // TODO: From now on
    onAdvertiserSwitch() {
      this.changeView('adaccounts', this.advertiserSwitch)
    },
  },
  async created() {
    this.facebook = new Facebook(localStorage.getItem('accessToken'))
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
