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
            <span class="btn-group flex items-center mr-2">
                <vs-button @click="changeView('campaigns')" :color="(view == 'campaigns') ? 'success' : 'primary'">Campaigns</vs-button>
                <vs-button @click="changeView('adsets')" :color="(view == 'adsets') ? 'success' : 'primary'">Adsets</vs-button>
                <vs-button @click="changeView('ads')" :color="(view == 'ads') ? 'success' : 'primary'">Ads</vs-button>
            </span>
            <vs-button :disabled="!((view == 'campaigns') && (selectedCampaigns.length > 0))" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-dollar-sign" @click="showUpdateFormCost"></vs-button>
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
                    <vs-th sort-key="ctr">CTR</vs-th>
                    <vs-th sort-key="cpc">CPC</vs-th>
                    <vs-th sort-key="roas">ROAS</vs-th>
                    <vs-th sort-key="daily_budget">Budget</vs-th>
                    <vs-th>Actions</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td :data="data[indextr].name">
                            {{data[indextr].name}}
                        </vs-td>

                        <vs-td :data="data[indextr].status">
                            <vs-switch
                                :color="statusColors[data[indextr].status]"
                                v-model="data[indextr].status == 'ACTIVE' || data[indextr].status == 'REVIEW' || data[indextr].status == 'DISABLED'"
                                vs-icon-off="pause" vs-icon-on="done"
                                @input="switchStatus('campaign', data[indextr].id)">
                                <span slot="on" v-if="data[indextr].status == 'ACTIVE'">Active</span>
                                <span slot="on" v-else-if="data[indextr].status == 'REVIEW'">Review</span>
                                <span slot="on" v-else="data[indextr].status == 'DISABLED'">Disabled</span>
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

                        <vs-td :data="data[indextr].roas">
                            € {{data[indextr].roas.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].daily_budget">
                            <span v-if="data[indextr].cbo">
                                € {{data[indextr].daily_budget.toFixed(2)}}
                            </span>
                            <span v-else>
                                Adset budget
                            </span>
                        </vs-td>

                        <vs-td>
                            <div class="flex items-center">
                                <vs-button @click="editBudgetPopup(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-dollar-sign"></vs-button>
                                <vs-button @click="editBidPopup(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-minus-circle"></vs-button>
                            </div>
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>{{data.length}}</vs-td>
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

            <!-- Adsets table -->
            <vs-table v-if="view == 'adsets'" stripe multiple v-model="selectedAdsets" :max-items="maxItems" pagination search :data="adsets" :currentPage="currentPage" @change-page="onPageChanged">

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
                    <vs-th sort-key="roas">ROAS</vs-th>
                    <vs-th sort-key="daily_budget">Budget</vs-th>
                    <vs-th>Actions</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td :data="data[indextr].name">
                            {{data[indextr].name}}
                        </vs-td>

                        <vs-td :data="data[indextr].status">
                            <vs-switch
                                :color="statusColors[data[indextr].status]"
                                v-model="data[indextr].status == 'ACTIVE' || data[indextr].status == 'REVIEW' || data[indextr].status == 'DISABLED'"
                                vs-icon-off="pause" vs-icon-on="done"
                                @input="switchStatus('adset', data[indextr].id)">
                                <span slot="on" v-if="data[indextr].status == 'ACTIVE'">Active</span>
                                <span slot="on" v-else-if="data[indextr].status == 'REVIEW'">Review</span>
                                <span slot="on" v-else="data[indextr].status == 'DISABLED'">Disabled</span>
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

                        <vs-td :data="data[indextr].roas">
                            € {{data[indextr].roas.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].daily_budget">
                            <span v-if="data[indextr].cbo">
                                Campaign budget
                            </span>
                            <span v-else>
                                € {{data[indextr].daily_budget.toFixed(2)}}
                            </span>
                        </vs-td>

                        <vs-td>
                            <div class="flex items-center">
                                <vs-button @click="editBudgetPopup(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-dollar-sign"></vs-button>
                                <vs-button @click="editBidPopup(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-minus-circle"></vs-button>
                            </div>
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>{{data.length}}</vs-td>
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
                    <vs-th sort-key="ctr">CTR</vs-th>
                    <vs-th sort-key="cpc">CPC</vs-th>
                    <vs-th sort-key="roas">ROAS</vs-th>
                    <vs-th>Actions</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td :data="data[indextr].name">
                            {{data[indextr].name}}
                        </vs-td>

                        <vs-td :data="data[indextr].status">
                            <vs-switch
                                :color="statusColors[data[indextr].status]"
                                v-model="data[indextr].status == 'ACTIVE' || data[indextr].status == 'REVIEW' || data[indextr].status == 'DISABLED'"
                                vs-icon-off="pause" vs-icon-on="done"
                                @input="switchStatus('ad', data[indextr].id)">
                                <span slot="on" v-if="data[indextr].status == 'ACTIVE'">Active</span>
                                <span slot="on" v-else-if="data[indextr].status == 'REVIEW'">Review</span>
                                <span slot="on" v-else="data[indextr].status == 'DISABLED'">Disabled</span>
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

                        <vs-td :data="data[indextr].roas">
                            € {{data[indextr].roas.toFixed(2)}}
                        </vs-td>

                        <vs-td>
                            <div class="flex items-center">
                                <vs-button @click="viewAdPopup(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-eye"></vs-button>
                            </div>
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td></vs-td>
                    <vs-td></vs-td>
                    <vs-td>{{data.length}}</vs-td>
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
                    <vs-td></vs-td>
                    <!-- END TOTAL STATS-->
                </template>
            </vs-table>

            <!-- Edit budget popup -->
            <vs-popup title="Edit budget" :active.sync="showPopup['edit_budget']">
                <div class="vx-row mb-6 flex items-center">
                    <vs-button class="mr-auto ml-auto">{{editingObject.name}}</vs-button>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Daily Budget</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="editingObject.daily_budget" />
                    </div>
                    <div class="vx-col w-full mt-2" v-if="(editingObject.object_type == 'campaign') && (editingObject.hasOwnProperty('cbo')) && (!editingObject.cbo)">
                        <span>* This campaign is non-CBO: this daily budget will be assigned to every adset of this campaign.</span>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <vs-button class="mt-12 mr-auto ml-auto" @click="editBudget" color="primary" type="filled" :disabled="!isValidForm('edit_budget')">Update budget</vs-button>
                </div>
            </vs-popup>

            <!-- Edit bid popup -->
            <vs-popup title="Edit budget" :active.sync="showPopup['edit_bid']">
                <div class="vx-row mb-6 flex items-center">
                    <vs-button class="mr-auto ml-auto">{{editingObject.name}}</vs-button>
                </div>

                <div class="vx-row mb-6" v-if="((editingObject.object_type == 'adset') && (editingObject.hasOwnProperty('cbo')) && (editingObject.cbo))">
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
                        <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="editingBidAmount" />
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col w-full mt-2" v-if="(editingObject.object_type == 'campaign') && (editingObject.hasOwnProperty('cbo')) && (!editingObject.cbo)">
                        <span>* This campaign is non-CBO: this bid will be assigned to every adset of this campaign.</span>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <vs-button class="mt-12 mr-auto ml-auto" @click="editBid" color="primary" type="filled" :disabled="!isValidForm('edit_bid')">Update budget</vs-button>
                </div>
            </vs-popup>

            <!-- View ad popup -->
            <vs-popup title="View Ad" :active.sync="showPopup['view_ad']">

                <div v-if="!viewAdSaving">
                    <div class="vx-row mb-6 flex items-center">
                        <vs-button class="mr-auto ml-auto">{{viewingObject.name}}</vs-button>
                    </div>

                    <div class="vx-row mb-6">
                        <div class="vx-col sm:w-1/4 w-full mt-2">
                        </div>
                        <div class="vx-col sm:w-1/2 w-full mt-2">
                            <img v-if="viewingObject.image_url != ''" :src="viewingObject.image_url" class="w-full">
                            <img v-else src="https://www.tucanomacchine.it/wp-content/uploads/2019/07/480px-No_image_available.svg.png" class="w-full">
                        </div>
                        <div class="vx-col sm:w-1/4 w-full mt-2">
                        </div>
                    </div>

                    <div class="vx-row mb-6">
                        <div class="vx-col w-full">
                            <span>{{viewingObject.body}}</span>
                        </div>
                    </div>

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
                </div>
                
            </vs-popup>

            <!-- Update form cost popup -->
            <vs-popup title="Update form cost" :active.sync="showPopup['update_form_cost']">
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Total amount spent</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        € {{this.updateFormTotalCost.toFixed(2)}}
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
      view: 'campaigns',

      fb_account: {},

      selectedCampaigns: [],
      selectedAdsets: [],
      selectedAds: [],

      deleted: [],

      maxItems: 20,

      campaigns: [],
      adsets: [],
      ads: [],

      statusOptions: ['ACTIVE', 'PAUSED', 'REVIEW'],
      statusColors: {
        'ACTIVE': 'success',
        'REVIEW': 'warning',
        'DISABLED': 'danger',
      },

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

      editingObject: {},
      viewingObject: {},
      showPopup: {
        'edit_budget': false,
        'edit_bid': false,
        'view_ad': false,
        'update_form_cost': false,
      },
      bidOptions: [
        { value:'LOWEST_COST_WITHOUT_CAP',  label: 'Lowest cost' },
        { value:'LOWEST_COST_WITH_BID_CAP', label: 'Lowest cost with bid cap' },
        { value:'COST_CAP',                 label: 'Cost cap' },
      ],
      editingBidAmount: 0,

      updateFormId: '',
      updateFormTotalCost: 0,
      updateFormDate: '',
      formsLoaded: false,
      forms: [],

      noImageUrl: '',

      productsOptions: [],
      productsLoaded: false,
      typesOptions: ['image', 'video'],
      fileToUpload: '',
      viewAdSaving: false,
      viewAdSavingTrafficSource: '',
      viewAdSavingProduct: '',
      viewAdSavingType: '',
      viewAdSavingMedia: '',
      viewAdSavingPreview: '',
      viewAdSavingTitle: '',
      viewAdSavingText: '',
      viewAdSavingCtr: 0,
      viewAdSavingCpc: 0,
      viewAdSavingCpa: 0,
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
    objects() {
      if (this.view == 'campaigns') {
        return this.campaigns
      } else if (this.view == 'adsets') {
        return this.adsets
      } else if (this.view == 'ads') {
        return this.ads
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
    sortByStatus() {
      let arr = []
      let orderedArr = []

      // Sort campaigns
      arr = this.campaigns
      orderedArr = []
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status == 'ACTIVE') {
          orderedArr.push(arr[i])
        }
      }
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status == 'REVIEW') {
          orderedArr.push(arr[i])
        }
      }
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status == 'PAUSED') {
          orderedArr.push(arr[i])
        }
      }
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status == 'DISABLED') {
          orderedArr.push(arr[i])
        }
      }
      this.campaigns = orderedArr

      // Sort adsets
      arr = this.adsets
      orderedArr = []
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status == 'ACTIVE') {
          orderedArr.push(arr[i])
        }
      }
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status == 'REVIEW') {
          orderedArr.push(arr[i])
        }
      }
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status == 'PAUSED') {
          orderedArr.push(arr[i])
        }
      }
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status == 'DISABLED') {
          orderedArr.push(arr[i])
        }
      }
      this.adsets = orderedArr

      // Sort ads
      arr = this.ads
      orderedArr = []
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status == 'ACTIVE') {
          orderedArr.push(arr[i])
        }
      }
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status == 'REVIEW') {
          orderedArr.push(arr[i])
        }
      }
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status == 'PAUSED') {
          orderedArr.push(arr[i])
        }
      }
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status == 'DISABLED') {
          orderedArr.push(arr[i])
        }
      }
      this.ads = orderedArr
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

      this.selectedCampaigns = []
      this.selectedAdsets = []
      this.selectedAds = []

      let timeRange = {}
      if (this.filters.date_from != '') {
        timeRange['since'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        timeRange['until'] = this.filters.date_to
      }
      timeRange = JSON.stringify(timeRange)
      
      let fields = 'id,name,account_status'
      fields += ',campaigns.time_range(' + timeRange + ')\{id,name,status,effective_status,objective,result,spent,impressions,insights{inline_link_click_ctr,cpc,website_purchase_roas},clicks,bid_strategy,daily_budget'
      fields += ',adsets.time_range(' + timeRange + ')\{id,name,status,effective_status,result,spent,impressions,insights{inline_link_click_ctr,cpc,website_purchase_roas},clicks,bid_strategy,bid_amount,daily_budget'
      fields += ',ads.time_range(' + timeRange + ')\{id,name,status,effective_status,result,spent,impressions,insights{inline_link_click_ctr,cpc,website_purchase_roas},clicks,bid_strategy,bid_amount,daily_budget'
      fields += ',creative.thumbnail_width(512).thumbnail_height(512)\{image_url,body,title,call_to_action_type,object_url,link_url,thumbnail_url,effective_object_story_id\}'
      fields += '\}'
      fields += '\}'
      fields += '\}'

      // Retrieve campaigns
      this.campaigns = []
      this.adsets = []
      this.ads = []
      this.$http.get('https://graph.facebook.com/v9.0/me/adaccounts?access_token=' + this.fb_account.token + '&fields=' + fields + '&thumbnail_width=512&thumbnail_height=512')
        .then((response) => {
          for (let j = 0; j < response.data.data.length; j++) {
            let adAccount = response.data.data[j]

            if (adAccount.hasOwnProperty('campaigns') && adAccount.campaigns.hasOwnProperty('data') && Array.isArray(adAccount.campaigns.data)) {
              for (let i = 0; i < adAccount.campaigns.data.length; i++) {
                let campaign = adAccount.campaigns.data[i]

                let campaignEffectiveStatus = campaign.effective_status
                if (campaignEffectiveStatus == 'ACTIVE') {
                  campaignEffectiveStatus = 'REVIEW'
                }
                if (adAccount.account_status == 2) {
                  campaignEffectiveStatus = 'DISABLED'
                }

                if (this.filters.status != '') {
                  if (this.filters.status == 'REVIEW') {
                    if (campaign.effective_status != 'REVIEW') {
                      continue
                    }
                  } else if (campaign.effective_status != this.filters.status) {
                    continue
                  }
                }
                
                let spent = parseFloat(campaign.spent/100)
                let cpa = (campaign.result > 0) ? (spent/campaign.result) : spent
                let cr = (campaign.clicks > 0) ? ((campaign.result/campaign.clicks)*100) : ((campaign.result > 0) ? 9999.99 : 0)
                let cpm = (campaign.impressions > 0) ? ((spent*1000)/campaign.impressions) : (spent*1000)
                let ctr = 0.0
                let cpc = 0.0
                let roas = 0.0

                if (campaign.hasOwnProperty('insights') && campaign.insights.hasOwnProperty('data') &&
                    campaign.insights.data.length > 0)
                {
                    if (campaign.insights.data[0].hasOwnProperty('inline_link_click_ctr')) {
                      ctr = parseFloat(campaign.insights.data[0].inline_link_click_ctr)
                    }
                    if (campaign.insights.data[0].hasOwnProperty('cpc')) {
                      cpc = parseFloat(campaign.insights.data[0].cpc)
                    }
                    if (campaign.insights.data[0].hasOwnProperty('website_purchase_roas')) {
                      roas = parseFloat(campaign.insights.data[0].website_purchase_roas[0].value)
                    }
                }

                let cbo = (campaign.hasOwnProperty('daily_budget') && campaign.hasOwnProperty('bid_strategy'))
                let daily_budget = 0
                let bid_strategy = ''
                if (campaign.hasOwnProperty('daily_budget')) {
                  daily_budget = parseFloat(parseInt(campaign.daily_budget) / 100)
                }
                if (campaign.hasOwnProperty('bid_strategy')) {
                  bid_strategy = campaign.bid_strategy
                }

                // Get adsets
                let adsets = []
                if (campaign.hasOwnProperty('adsets')) {
                  for (let adsetIndex = 0; adsetIndex < campaign.adsets.data.length; adsetIndex++) {
                    let adset = campaign.adsets.data[adsetIndex]

                    let adsetEffectiveStatus = adset.effective_status
                    if (adAccount.account_status == 2) {
                      adsetEffectiveStatus = 'DISABLED'
                    }
                    if (adsetEffectiveStatus == 'ACTIVE') {
                      adsetEffectiveStatus = 'REVIEW'
                    }

                    // Get ads
                    let ads = []
                    if (adset.hasOwnProperty('ads')) {
                      for (let adIndex = 0; adIndex < adset.ads.data.length; adIndex++) {
                        let ad = adset.ads.data[adIndex]

                        let adEffectiveStatus = ad.effective_status
                        if (adAccount.account_status == 2) {
                          adEffectiveStatus = 'DISABLED'
                        }
                        if (adsetEffectiveStatus == 'REVIEW' && adEffectiveStatus == 'ACTIVE') {
                          adsetEffectiveStatus = 'ACTIVE'
                        }

                        let adSpent = parseFloat(ad.spent/100)
                        let adCpa = (ad.result > 0) ? (adSpent/ad.result) : adSpent
                        let adCr = (ad.clicks > 0) ? ((ad.result/ad.clicks)*100) : ((ad.result > 0) ? 9999.99 : 0)
                        let adCpm = (ad.impressions > 0) ? ((adSpent*1000)/ad.impressions) : (adSpent*1000)
                        let adCtr = 0.0
                        let adCpc = 0.0
                        let adRoas = 0.0

                        if (ad.hasOwnProperty('insights') && ad.insights.hasOwnProperty('data') &&
                            ad.insights.data.length > 0)
                        {
                            if (ad.insights.data[0].hasOwnProperty('inline_link_click_ctr')) {
                              adCtr = parseFloat(ad.insights.data[0].inline_link_click_ctr)
                            }
                            if (ad.insights.data[0].hasOwnProperty('cpc')) {
                              adCpc = parseFloat(ad.insights.data[0].cpc)
                            }
                            if (ad.insights.data[0].hasOwnProperty('website_purchase_roas')) {
                              adRoas = parseFloat(ad.insights.data[0].website_purchase_roas[0].value)
                            }
                        }

                        let adDailyBudget = 0
                        let adBidStrategy = ''
                        if (ad.hasOwnProperty('daily_budget')) {
                          adDailyBudget = parseFloat(parseInt(ad.daily_budget) / 100)
                        }
                        if (ad.hasOwnProperty('bid_strategy')) {
                          adBidStrategy = ad.bid_strategy
                        }

                        let adUrl = ''
                        if (ad.creative.hasOwnProperty('object_url') && ad.creative.object_url != null && ad.creative.object_url != '') {
                          adUrl = ad.creative.object_url
                        }
                        let adLinkUrl = ''
                        if (ad.creative.hasOwnProperty('link_url') && ad.creative.link_url != null && ad.creative.link_url != '') {
                          adLinkUrl = ad.creative.link_url
                        }
                        if (adLinkUrl == '') {
                          adLinkUrl = 'https://www.facebook.com/' + ad.creative.effective_object_story_id
                        }

                        let adImageUrl = ''
                        if (ad.creative.hasOwnProperty('image_url') && ad.creative.image_url != null && ad.creative.image_url != '') {
                          adImageUrl = ad.creative.image_url
                        }
                        let thumbnailUrl = ''
                        if (ad.creative.hasOwnProperty('thumbnail_url') && ad.creative.thumbnail_url != null && ad.creative.thumbnail_url != '') {
                          thumbnailUrl = ad.creative.thumbnail_url
                        }
                        if (adImageUrl == '') {
                          adImageUrl = thumbnailUrl
                        }

                        let adToPush = {
                          object_type: 'ad',
                          campaign_id: campaign.id,
                          adset_id: adset.id,
                          id: ad.id,
                          name: ad.name,
                          status: adEffectiveStatus,
                          spent: adSpent,
                          result: (campaign.objective == 'PAGE_LIKES' || campaign.objective == 'POST_ENGAGEMENT') ? 0 : ad.result, // TODO: This is a momentary patch (it will no count page likes)
                          cpa: adCpa,
                          impressions: ad.impressions,
                          clicks: ad.clicks,
                          cr: adCr,
                          cpm: adCpm,
                          ctr: adCtr,
                          cpc: adCpc,
                          roas: adRoas,
                          daily_budget: adDailyBudget,
                          bid_strategy: adBidStrategy,
                          image_url: adImageUrl,
                          body: ad.creative.body,
                          title: ad.creative.title,
                          call_to_action_type: ad.creative.call_to_action_type,
                          url: adUrl,
                          post_url: adLinkUrl,
                        }
                        ads.push(adToPush)
                        this.ads.push(adToPush)
                      }
                    }

                    let adsetSpent = parseFloat(adset.spent/100)
                    let adsetCpa = (adset.result > 0) ? (adsetSpent/adset.result) : adsetSpent
                    let adsetCr = (adset.clicks > 0) ? ((adset.result/adset.clicks)*100) : ((adset.result > 0) ? 9999.99 : 0)
                    let adsetCpm = (adset.impressions > 0) ? ((adsetSpent*1000)/adset.impressions) : (adsetSpent*1000)
                    let adsetCtr = 0.0
                    let adsetCpc = 0.0
                    let adsetRoas = 0.0

                    if (adset.hasOwnProperty('insights') && adset.insights.hasOwnProperty('data') &&
                        adset.insights.data.length > 0)
                    {
                        if (adset.insights.data[0].hasOwnProperty('inline_link_click_ctr')) {
                          adsetCtr = parseFloat(adset.insights.data[0].inline_link_click_ctr)
                        }
                        if (adset.insights.data[0].hasOwnProperty('cpc')) {
                          adsetCpc = parseFloat(adset.insights.data[0].cpc)
                        }
                        if (adset.insights.data[0].hasOwnProperty('website_purchase_roas')) {
                          adsetRoas = parseFloat(adset.insights.data[0].website_purchase_roas[0].value)
                        }
                    }

                    let adsetDailyBudget = 0
                    let adsetBidStrategy = ''
                    if (adset.hasOwnProperty('daily_budget')) {
                      adsetDailyBudget = parseFloat(parseInt(adset.daily_budget) / 100)
                    }
                    if (adset.hasOwnProperty('bid_strategy')) {
                      adsetBidStrategy = adset.bid_strategy
                    }

                    if (campaignEffectiveStatus == 'REVIEW' && adsetEffectiveStatus == 'ACTIVE') {
                      campaignEffectiveStatus = 'ACTIVE'
                    }

                    let adsetToPush = {
                      object_type: 'adset',
                      campaign_id: campaign.id,
                      id: adset.id,
                      name: adset.name,
                      status: adsetEffectiveStatus,
                      spent: adsetSpent,
                      result: (campaign.objective == 'PAGE_LIKES' || campaign.objective == 'POST_ENGAGEMENT') ? 0 : adset.result, // TODO: This is a momentary patch (it will no count page likes)
                      cpa: adsetCpa,
                      impressions: adset.impressions,
                      clicks: adset.clicks,
                      cr: adsetCr,
                      cpm: adsetCpm,
                      ctr: adsetCtr,
                      cpc: adsetCpc,
                      roas: adsetRoas,
                      cbo: cbo,
                      daily_budget: adsetDailyBudget,
                      bid_strategy: adsetBidStrategy,
                      ads: ads,
                    }
                    adsets.push(adsetToPush)
                    this.adsets.push(adsetToPush)

                    // TODO: to delete
                    console.log("adsetToPush:", adsetToPush)
                  }
                }

                let campaignToPush = {
                  object_type: 'campaign',
                  account_id: adAccount.id,
                  id: campaign.id,
                  name: campaign.name,
                  status: campaignEffectiveStatus,
                  spent: spent,
                  result: (campaign.objective == 'PAGE_LIKES' || campaign.objective == 'POST_ENGAGEMENT') ? 0 : campaign.result, // TODO: This is a momentary patch (it will no count page likes)
                  cpa: cpa,
                  impressions: campaign.impressions,
                  clicks: campaign.clicks,
                  cr: cr,
                  cpm: cpm,
                  ctr: ctr,
                  cpc: cpc,
                  roas: roas,
                  cbo: cbo,
                  daily_budget: daily_budget,
                  bid_strategy: bid_strategy,
                  adsets: adsets,
                }
                this.campaigns.push(campaignToPush)

                // TODO: to delete
                console.log(campaignToPush)
              }
            }
          }

          this.calculateStats()
          this.sortByStatus()
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
    switchStatus(what, id) {
      let obj = {}
      let index = -1
      if (what == 'campaign') {
        for (let i = 0; i < this.campaigns.length; i++) {
          if (this.campaigns[i].id == id) {
            index = i
            break
          }
        }
        obj = this.campaigns[index]
      } else if (what == 'adset') {
        for (let i = 0; i < this.adsets.length; i++) {
          if (this.adsets[i].id == id) {
            index = i
            break
          }
        }
        obj = this.adsets[index]
      } else if (what == 'ad') {
        for (let i = 0; i < this.ads.length; i++) {
          if (this.ads[i].id == id) {
            index = i
            break
          }
        }
        obj = this.ads[index]
      } else {
        return
      }

      let oldStatus = obj.status
      if (oldStatus == 'DISABLED') return

      let status = 'PAUSED'
      if (oldStatus == 'REVIEW' || oldStatus == 'ACTIVE') {
        status = 'PAUSED'
      } else if (oldStatus == 'PAUSED') {
        status = 'ACTIVE'
      } else {
        return
      }

      if (what == 'campaign') {
        this.campaigns[index].status = status
      } else if (what == 'adset') {
        this.adsets[index].status = status
      } else if (what == 'ad') {
        this.ads[index].status = status
      }

      // Change fb campaign/adset/ad status
      this.$http.post('https://graph.facebook.com/v9.0/' + obj.id + '?status=' + obj.status + '&access_token=' + this.fb_account.token)
        .then((response) => {
          if (response.data.success) {
            if (what == 'campaign') {
              this.campaigns[index].status = status
            } else if (what == 'adset') {
              this.adsets[index].status = status
            } else if (what == 'ad') {
              this.ads[index].status = status
            }
          } else {
            if (what == 'campaign') {
              this.campaigns[index].status = oldStatus
            } else if (what == 'adset') {
              this.adsets[index].status = oldStatus
            } else if (what == 'ad') {
              this.ads[index].status = oldStatus
            }
          }
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: what.charAt(0).toUpperCase() + what.slice(1) + ' status updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
        })
        .catch((error) => {
          if (what == 'campaign') {
            this.campaigns[index].status = oldStatus
          } else if (what == 'adset') {
            this.adsets[index].status = oldStatus
          } else if (what == 'ad') {
            this.ads[index].status = oldStatus
          }
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
    isValidForm(type) {
      if (type == 'edit_budget') {
        if (this.editingObject.hasOwnProperty('daily_budget') &&
            this.editingObject.daily_budget > 0) {
          return true
        }
      } else if (type == 'edit_bid') {
        if (this.editingObject.hasOwnProperty('bid_strategy') &&
            this.editingObject.bid_strategy != '') {
          return ((this.editingObject.bid_strategy == 'LOWEST_COST_WITHOUT_CAP') ||
                  (this.editingObject.bid_strategy != 'LOWEST_COST_WITHOUT_CAP') && this.editingBidAmount > 0)
        }
      } else if (type == 'update_form_cost') {
        if (this.updateFormId != '' && this.updateFormId > 0 &&
            this.updateFormTotalCost > 0 && this.updateFormDate != '') {
          return true
        }
      } else if (type == 'save_ad') {
        if (this.viewAdSavingType != 'video') {
          return true
        } else {
          if (this.fileToUpload != '') {
            return true
          }
        }
      }
      return false
    },
    editBudgetPopup(id) {
      this.editingObject = this.objects[this.getObjectIndexById(this.objects, id)]
      this.showPopup['edit_budget'] = true
    },
    editBidPopup(id) {
      this.editingObject = this.objects[this.getObjectIndexById(this.objects, id)]
      this.showPopup['edit_bid'] = true
    },
    viewAdPopup(id) {
      this.viewingObject = this.objects[this.getObjectIndexById(this.objects, id)]
      this.showPopup['view_ad'] = true
    },
    editBudget() {
      let dailyBudget = parseInt(this.editingObject.daily_budget * 100)

      this.$vs.loading()

      if (this.editingObject.object_type == 'campaign') {
        if (this.editingObject.hasOwnProperty('cbo') && this.editingObject.cbo) {
          // Change campaign daily budget
          this.$http.post('https://graph.facebook.com/v9.0/' + this.editingObject.id + '?daily_budget=' + dailyBudget + '&access_token=' + this.fb_account.token)
            .then((response) => {
              this.editingObject = {}
              this.showPopup['edit_budget'] = false
              this.$vs.loading.close()
              this.applyFilters()
              this.$vs.notify({
                title: 'Success',
                text: 'Campaign daily budget updated',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
            })
            .catch((error) => {
              this.editingObject = {}
              this.showPopup['edit_budget'] = false
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Error',
                text: 'Campaign daily budget cannot be modified',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'danger'
              })
            })
        } else {
          // Change all adsets daily budget of this campaign
          let adsetCounter = 0
          let errorCounter = 0
          for (let i = 0; i < this.editingObject.adsets.length; i++) {
            let adset = this.editingObject.adsets[i]
            this.$http.post('https://graph.facebook.com/v9.0/' + adset.id + '?daily_budget=' + dailyBudget + '&access_token=' + this.fb_account.token)
              .then((response) => {
                adsetCounter++
                if (adsetCounter == this.editingObject.adsets.length) {
                  this.editingObject = {}
                  this.showPopup['edit_budget'] = false
                  this.$vs.loading.close()
                  this.applyFilters()
                  if (errorCounter == 0) {
                    this.$vs.notify({
                      title: 'Success',
                      text: 'Adsets daily budget updated',
                      iconPack: 'feather',
                      icon: 'icon-alert-circle',
                      color: 'success'
                    })
                  } else {
                    this.$vs.notify({
                      title: 'Error',
                      text: errorCounter + ' errors occured while updating adsets daily buget',
                      iconPack: 'feather',
                      icon: 'icon-alert-circle',
                      color: 'danger'
                    })
                  }
                }
              })
              .catch((error) => {
                adsetCounter++
                errorCounter++
                if (adsetCounter == this.editingObject.adsets.length) {
                  this.editingObject = {}
                  this.showPopup['edit_budget'] = false
                  this.$vs.loading.close()
                  this.applyFilters()
                  this.$vs.notify({
                    title: 'Error',
                    text: errorCounter + ' errors occured while updating adsets daily buget',
                    iconPack: 'feather',
                    icon: 'icon-alert-circle',
                    color: 'danger'
                  })
                }
              })
          }
        }
      } else if (this.editingObject.object_type == 'adset') {
        // Edit adset daily budget
        this.$http.post('https://graph.facebook.com/v9.0/' + this.editingObject.id + '?daily_budget=' + dailyBudget + '&access_token=' + this.fb_account.token)
          .then((response) => {
            this.editingObject = {}
            this.showPopup['edit_budget'] = false
            this.$vs.loading.close()
            this.applyFilters()
            this.$vs.notify({
              title: 'Success',
              text: 'Adset daily budget updated',
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'success'
            })
          })
          .catch((error) => {
            this.editingObject = {}
            this.showPopup['edit_budget'] = false
            this.$vs.loading.close()
            this.$vs.notify({
              title: 'Error',
              text: 'Adset daily budget cannot be modified',
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'danger'
            })
          })
      } else {
        this.$vs.loading.close()
      }
    },
    editBid() {
      let bidAmount = parseInt(this.editingBidAmount * 100)
      let bidStrategy = this.editingObject.bid_strategy

      this.$vs.loading()
      if (this.editingObject.object_type == 'campaign') {
        if (this.editingObject.cbo) {
          // Change campaign bid_strategy
          this.$http.post('https://graph.facebook.com/v9.0/' + this.editingObject.id + '?bid_strategy=' + bidStrategy + '&access_token=' + this.fb_account.token)
            .then((response) => {
              // Change all adsets bid amount of this campaign
              if (bidStrategy != 'LOWEST_COST_WITHOUT_CAP') {
                let adsetCounter = 0
                let errorCounter = 0
                for (let i = 0; i < this.editingObject.adsets.length; i++) {
                  let adset = this.editingObject.adsets[i]

                  let params = ''
                  params += '?bid_amount=' + bidAmount
                  params += '&access_token=' + this.fb_account.token
                  this.$http.post('https://graph.facebook.com/v9.0/' + adset.id + params)
                    .then((response) => {
                      adsetCounter++
                      if (adsetCounter == this.editingObject.adsets.length) {
                        this.editingObject = {}
                        this.showPopup['edit_bid'] = false
                        this.$vs.loading.close()
                        this.applyFilters()
                        if (errorCounter == 0) {
                          this.$vs.notify({
                            title: 'Success',
                            text: 'Adsets bid updated',
                            iconPack: 'feather',
                            icon: 'icon-alert-circle',
                            color: 'success'
                          })
                        } else {
                          this.$vs.notify({
                            title: 'Error',
                            text: errorCounter + ' errors occured while updating adsets bid',
                            iconPack: 'feather',
                            icon: 'icon-alert-circle',
                            color: 'danger'
                          })
                        }
                      }
                    })
                    .catch((error) => {
                      adsetCounter++
                      errorCounter++
                      if (adsetCounter == this.editingObject.adsets.length) {
                        this.editingObject = {}
                        this.showPopup['edit_bid'] = false
                        this.$vs.loading.close()
                        this.applyFilters()
                        this.$vs.notify({
                          title: 'Error',
                          text: errorCounter + ' errors occured while updating adsets bid',
                          iconPack: 'feather',
                          icon: 'icon-alert-circle',
                          color: 'danger'
                        })
                      }
                    })
                }
              }
            })
            .catch((error) => {
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Error',
                text: 'Campaign daily budget cannot be modified',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'danger'
              })
            })
        } else {
          // Change all adsets bid amount and bid strategy of this campaign
          let adsetCounter = 0
          let errorCounter = 0
          for (let i = 0; i < this.editingObject.adsets.length; i++) {
            let adset = this.editingObject.adsets[i]

            let params = ''
            params += '?bid_strategy=' + bidStrategy
            if (bidStrategy != 'LOWEST_COST_WITHOUT_CAP') {
              params += '&bid_amount=' + bidAmount
            }
            params += '&access_token=' + this.fb_account.token
            this.$http.post('https://graph.facebook.com/v9.0/' + adset.id + params)
              .then((response) => {
                adsetCounter++
                if (adsetCounter == this.editingObject.adsets.length) {
                  this.editingObject = {}
                  this.showPopup['edit_bid'] = false
                  this.$vs.loading.close()
                  this.applyFilters()
                  if (errorCounter == 0) {
                    this.$vs.notify({
                      title: 'Success',
                      text: 'Adsets bid updated',
                      iconPack: 'feather',
                      icon: 'icon-alert-circle',
                      color: 'success'
                    })
                  } else {
                    this.$vs.notify({
                      title: 'Error',
                      text: errorCounter + ' errors occured while updating adsets bid',
                      iconPack: 'feather',
                      icon: 'icon-alert-circle',
                      color: 'danger'
                    })
                  }
                }
              })
              .catch((error) => {
                adsetCounter++
                errorCounter++
                if (adsetCounter == this.editingObject.adsets.length) {
                  this.editingObject = {}
                  this.showPopup['edit_bid'] = false
                  this.$vs.loading.close()
                  this.applyFilters()
                  this.$vs.notify({
                    title: 'Error',
                    text: errorCounter + ' errors occured while updating adsets bid',
                    iconPack: 'feather',
                    icon: 'icon-alert-circle',
                    color: 'danger'
                  })
                }
              })
          }
        }
      } else if (this.editingObject.object_type == 'adset') {
        let params = ''
        params += '?access_token=' + this.fb_account.token
        if (!this.editingObject.cbo) {
          params += '&bid_strategy=' + bidStrategy
          if (bidStrategy != 'LOWEST_COST_WITHOUT_CAP') {
            params += '&bid_amount=' + bidAmount
          }
        } else {
          params += '&bid_amount=' + bidAmount
        }
        let doRequestFlag = true
        let campaign = this.campaigns[this.getObjectIndexById(this.campaigns, this.editingObject.campaign_id)]
        if (campaign.bid_strategy == 'LOWEST_COST_WITHOUT_CAP') {
          doRequestFlag = false
        }
        if (doRequestFlag) {
          this.$http.post('https://graph.facebook.com/v9.0/' + this.editingObject.id + params)
            .then((response) => {
              this.editingObject = {}
              this.showPopup['edit_bid'] = false
              this.$vs.loading.close()
              this.applyFilters()
              this.$vs.notify({
                title: 'Success',
                text: 'Adset bid updated',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
            })
            .catch((error) => {
              this.editingObject = {}
              this.showPopup['edit_bid'] = false
              this.$vs.loading.close()
              this.applyFilters()
              this.$vs.notify({
                title: 'Error',
                text: 'An error occured while updating adsets bid',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'danger'
              })
            })
        } else {
          this.$vs.loading.close()
        }
      } else {
        this.$vs.loading.close()
      }
    },
    changeView(view) {
      this.$vs.loading()

      // TODO: to fix
      //this.onPageChanged(1)
      //this.gridApi.paginationGoToPage(0)

      this.view = view
      this.calculateStats()
      this.$nextTick(() => {
        this.$vs.loading.close()
      })
    },
    openNewTab(url) {
      if (url != '') {
        window.open(url)
      }
    },
    saveAdBegin(ad) {
      this.viewAdSavingTrafficSource = 1
      this.viewAdSavingType = 'image' // TODO: What about the type?
      this.viewAdSavingMedia = ''
      this.viewAdSavingPreview = ''
      this.viewAdSavingTitle = this.viewingObject.title
      this.viewAdSavingText = this.viewingObject.body
      this.viewAdSavingCtr = parseFloat(this.viewingObject.ctr.toFixed(2))
      this.viewAdSavingCpc = parseFloat(this.viewingObject.cpc.toFixed(2))
      this.viewAdSavingCpa = parseFloat(this.viewingObject.cpa.toFixed(2))

      if (!this.productsLoaded) {
        this.$vs.loading()
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        this.$http.get('/api/v1/products', { headers })
          .then((response) => {
            this.productsOptions = []
            response.data.data.forEach((product) => {
              this.productsOptions.push({
                id: product.id,
                label: product.id + ' - ' + product.name,
              })
              this.viewAdSavingProduct = product.id
            })
            this.productsLoaded = true
            this.$vs.loading.close()
            this.viewAdSaving = true
          })
          .catch((error) => {
          })
      } else {
        this.viewAdSaving = true
      }
    },
    showUpdateFormCost() {
      if (this.selectedCampaigns.length == 0) {
        return
      }

      this.updateFormTotalCost = 0
      this.selectedCampaigns.forEach((selCampaign) => {
        this.updateFormTotalCost += parseFloat(selCampaign.spent)
      })
      this.updateFormDate = this.filters.date_to

      if (!this.formsLoaded) {
        this.$vs.loading()
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }

        // Retrieve forms
        this.$http.get('/api/v1/forms', { headers })
          .then((response) => {
            this.$vs.loading.close()
            this.forms = response.data.data
            if (this.forms.length > 0) {
              this.updateFormId = this.forms[0].id
            }
            this.formsLoaded = true
            this.showPopup['update_form_cost'] = true
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
      } else {
        this.showPopup['update_form_cost'] = true
      }
    },
    updateFormCost() {
      this.$vs.loading()
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        day: this.updateFormDate,
        cost_to_add: this.updateFormTotalCost,
      }

      // Retrieve forms
      this.$http.put('/api/v1/forms/' + this.updateFormId + '/stats-add', data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Form total cost updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.showPopup['update_form_cost'] = false
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
    adSave() {
      this.$vs.loading()

      let pmId = 0
      let pmPreviewId = 0

      let img = this.$refs["viewAdSavingImg"]
      let url = img.src
      fetch(url)
        .then(r => r.blob())
        .then((blob) => {
          let fileToUpload = new File([blob], "file_to_upload.jpg")

          let headers = {
            'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
            'Content-Type': 'multipart/form-data',
          }
          let config = {
            headers: headers,
          }

          let formData = new FormData()
          formData.append('product_id', this.viewAdSavingProduct)
          formData.append('type', 'image')
          formData.append('file', fileToUpload)
          this.$http.post('/api/v1/product-medias', formData, config)
            .then((response) => {
              pmId = response.data.data.id

              if (this.viewAdSavingType == 'video') {
                pmPreviewId = pmId

                let formData = new FormData()
                formData.append('product_id', 1)
                formData.append('type', this.viewAdSavingType)
                formData.append('file', this.fileToUpload)
                this.$http.post('/api/v1/product-medias', formData, config)
                  .then((response) => {
                    pmId = response.data.data.id

                    let headers = {
                      'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
                    }
                    let data = {}
                    data['traffic_source_id'] = this.viewAdSavingTrafficSource
                    data['product_id'] = this.viewAdSavingProduct
                    data['product_media_id'] = pmId
                    data['ctr'] = this.viewAdSavingCtr
                    data['cpc'] = this.viewAdSavingCpc
                    data['cpa'] = this.viewAdSavingCpa
                    data['info'] = JSON.stringify({
                      title: this.viewAdSavingTitle,
                      text: this.viewAdSavingText,
                    })
                    data['product_media_preview_id'] = pmPreviewId
                    this.$http.post('/api/v1/ads', data, { headers })
                      .then((response) => {
                        this.$vs.loading.close()
                        this.$vs.notify({
                          title: 'Success',
                          text: 'Ad saved',
                          iconPack: 'feather',
                          icon: 'icon-alert-circle',
                          color: 'success'
                        })
                        this.viewAdSaving = false
                        this.showPopup['view_ad'] = false
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
              } else {
                let headers = {
                  'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
                }
                let data = {}
                data['traffic_source_id'] = this.viewAdSavingTrafficSource
                data['product_id'] = this.viewAdSavingProduct
                data['product_media_id'] = pmId
                data['ctr'] = this.viewAdSavingCtr
                data['cpc'] = this.viewAdSavingCpc
                data['cpa'] = this.viewAdSavingCpa
                data['info'] = JSON.stringify({
                  title: this.viewAdSavingTitle,
                  text: this.viewAdSavingText,
                })
                this.$http.post('/api/v1/ads', data, { headers })
                  .then((response) => {
                    this.$vs.loading.close()
                    this.$vs.notify({
                      title: 'Success',
                      text: 'Ad saved',
                      iconPack: 'feather',
                      icon: 'icon-alert-circle',
                      color: 'success'
                    })
                    this.viewAdSaving = false
                    this.showPopup['view_ad'] = false
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
              }
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
        })
    },
    showFilePicker() {
      this.$refs.fileUpload.click()
    },
    onFileUploadChange(event) {
      this.fileToUpload = event.target.files[0]
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
