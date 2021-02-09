<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="FB Accounts" code-toggler>

        <p class="flex items-center mb-4">
            <span class="btn-group flex items-center mr-2">
                <vs-button @click="changeView('accounts')" :color="(view == 'accounts') ? 'success' : 'primary'">Fb Accounts</vs-button>
                <vs-button :color="(view == 'ad_accounts') ? 'success' : 'primary'">Ad accounts</vs-button>
                <vs-button @click="changeView('campaigns')" :color="(view == 'campaigns') ? 'success' : 'primary'">Campaigns</vs-button>
                <vs-button @click="changeView('ads')" :color="(view == 'ads') ? 'success' : 'primary'">Ads</vs-button>
                <vs-button @click="changeView('invoices')" :color="(view == 'invoices') ? 'success' : 'primary'">Invoices</vs-button>
            </span>
        </p>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters" v-if="view == 'accounts'"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-eye" @click="checkStatusGlobal" v-if="view == 'accounts'"></vs-button>
            <vs-button class="mr-2" color="success" type="filled" icon-pack="feather" icon="icon-download" @click="downloadInvoices" v-if="view == 'invoices'"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create')" v-if="view == 'accounts'"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-database" @click="showForm('multi_create')" v-if="view == 'accounts'"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-upload" @click="showForm('import')" v-if="view == 'accounts'"></vs-button>
            <vs-button class="mr-2" color="success" type="filled" icon-pack="feather" icon="icon-play" @click="showForm('launch_ad')" v-if="view == 'ad_accounts'"></vs-button>
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup" v-if="view == 'accounts'"></vs-button>
        </p>

        <div v-if="showFilters" class="mb-12">
            <div class="vx-row mb-2">
                <div class="vx-col">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('today')" icon-pack="feather" icon="icon-calendar">Today</vs-button>
                </div>
                <div class="vx-col">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('yesterday')" icon-pack="feather" icon="icon-calendar">Yesterday</vs-button>
                </div>
                <div class="vx-col">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('this_month')" icon-pack="feather" icon="icon-calendar">This Month</vs-button>
                </div>
                <div class="vx-col">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('last_month')" icon-pack="feather" icon="icon-calendar">Last Month</vs-button>
                </div>
            </div>

            <div class="vx-row mb-2">
                <div class="vx-col md:w-1/2 w-full">
                    <label>From</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_from" />
                </div>
                <div class="vx-col md:w-1/2 w-full">
                    <label>To</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_to" />
                </div>
            </div>

            <div class="vx-row mb-2">
                <div class="vx-col md:w-1/3 w-full" v-if="canCreateAsAdmin">
                    <label>User</label>
                    <vs-select autocomplete v-model="filters.user" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in userOptions" class="w-full" />
                    </vs-select>
                </div>
                <div class="vx-col md:w-1/3 w-full">
                    <label>Status</label>
                    <vs-select autocomplete v-model="filters.status" class="w-full select-large">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in statusOptionsForFilters" class="w-full" />
                    </vs-select>
                </div>
                <div class="vx-col md:w-1/3 w-full">
                    <label>Product</label>
                    <vs-select autocomplete v-model="filters.product" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in productOptions" class="w-full" />
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

        <vs-dropdown v-if="(view != 'invoices')" vs-trigger-click class="cursor-pointer">
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

        <vue-context ref="context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.context_menu.close();checkStatus(data.id);" class="flex items-center text-sm">
                <feather-icon icon="EyeIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Check status</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close();showAdAccounts(data.id);" class="flex items-center text-sm">
                <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show ad accounts</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close();showAds(data.id);" class="flex items-center text-sm">
                <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show ads</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close();editForm(data.id);" class="flex items-center text-sm">
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <!-- Accounts table -->
        <vs-table v-if="view == 'accounts'" stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="user.email">User</vs-th>
                <vs-th sort-key="status">Status</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="product.name">Product</vs-th>
                <vs-th sort-key="">Cards</vs-th>
                <vs-th sort-key="token_status">Token Status</vs-th>
                <vs-th sort-key="">BM</vs-th>
                <vs-th sort-key="amount_spent">Spent</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="cpl">CPL</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].user.email">
                        {{data[indextr].user.email}}
                    </vs-td>

                    <vs-td :data="data[indextr].status">
                        <vs-chip transparent :color="statusColors[data[indextr].status]">{{data[indextr].status}}</vs-chip>
                    </vs-td>

                    <vs-td :data="data[indextr].name">
                        <div @contextmenu.prevent="$refs.context_menu.open($event, data[indextr])">
                            <vs-chip
                                transparent
                                :color="((data[indextr].token_status == 'active') ? (adAccountStatusColors[getAdAccountStatus(data[indextr].info)]) : 'danger')">
                                {{data[indextr].name}}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td :data="data[indextr].product.name">
                        {{data[indextr].product.name}}
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center" v-if="data[indextr].info != null && data[indextr].info.hasOwnProperty('bm')">
                            <vs-chip v-for="(card, index) in getCardsArray(data[indextr].info)" :key="index">
                                <vs-avatar v-if="getCardIcon(card) != ''" :src="getCardIcon(card)" />{{getCardValue(card)}}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td :data="data[indextr].token_status">
                        <vs-chip v-if="data[indextr].token_status != '' && data[indextr].token_status != null" transparent :color="(data[indextr].token_status == 'active') ? 'success' : 'danger'">
                            {{data[indextr].token_status}}
                        </vs-chip>
                    </vs-td>

                    <vs-td>
                        <div class="bm-badge flex items-center" v-if="data[indextr].info != null && data[indextr].info.hasOwnProperty('bm')">
                            <vx-tooltip :text="bm.name + ' (' + bm.id + ')'" v-for="(bm, index) in data[indextr].info['bm']" :key="index">
                                <vs-avatar size="24px" @click="showBmInfo(data[indextr].id, index)" icon-pack="feather" icon="icon-briefcase" :color="(bm.active > 0) ? 'success' : ((bm.warning > 0) ? 'warning' : 'danger')" />
                            </vx-tooltip>
                        </div>
                    </vs-td>

                    <vs-td>
                        <div v-if="typeof data[indextr].amount_spent != 'undefined'">
                            € {{data[indextr].amount_spent.toFixed(2)}}
                        </div>
                    </vs-td>

                    <vs-td>
                        <div v-if="typeof data[indextr].leads != 'undefined'">
                            {{data[indextr].leads}}
                        </div>
                    </vs-td>

                    <vs-td>
                        <div v-if="typeof data[indextr].cpl != 'undefined'">
                            € {{data[indextr].cpl.toFixed(2)}}
                        </div>
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td>
                </vs-td>
                <vs-td>
                </vs-td>
                <vs-td>
                </vs-td>
                <vs-td>
                </vs-td>
                <vs-td>
                </vs-td>
                <vs-td>
                </vs-td>
                <vs-td>
                </vs-td>
                <vs-td>
                </vs-td>
                <vs-td>
                    € {{totalStats.amount_spent.toFixed(2)}}
                </vs-td>
                <vs-td>
                    {{totalStats.leads}}
                </vs-td>
                <vs-td>
                    € {{totalStats.cpl.toFixed(2)}}
                </vs-td>
                <!-- END TOTAL STATS -->
            </template>
        </vs-table>

        <!-- Invoices table -->
        <vs-table v-if="view == 'invoices'" stripe multiple v-model="selectedInvoices" search :data="invoices" ref="invoicesTable">
            <template slot="thead">
                <vs-th sort-key="time">Date</vs-th>
                <vs-th sort-key="id">Invoice ID</vs-th>
                <vs-th sort-key="amountFloat">Amount</vs-th>
                <vs-th sort-key="currency">Currency</vs-th>
                <vs-th sort-key="">Preview</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].time">
                        {{formatDate(new Date(data[indextr].time * 1000))}}
                    </vs-td>

                    <vs-td :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td :data="data[indextr].amountFloat">
                        {{data[indextr].amount}}
                    </vs-td>

                    <vs-td :data="data[indextr].currency">
                        {{data[indextr].currency}}
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center">
                            <vs-button @click="showInvoicePreview(indextr)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-eye"></vs-button>
                        </div>
                    </vs-td>

                </vs-tr>

                <vs-td>
                </vs-td>

                <vs-td>
                </vs-td>

                <vs-td>
                </vs-td>

                <vs-td>
                    {{invoicesTotalStats.amount.toFixed(2)}}
                </vs-td>

                <vs-td>
                </vs-td>

                <vs-td>
                </vs-td>

            </template>
        </vs-table>

        <!-- Campaigns table -->
        <vs-table v-if="view == 'campaigns'" stripe multiple v-model="selectedAds" :max-items="maxItems" pagination search :data="campaigns" :currentPage="currentPage" @change-page="onPageChanged">

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
                        <vs-switch :color="(data[indextr].status == 'ACTIVE') ? 'success' : 'warning'" v-model="data[indextr].status == 'ACTIVE' || data[indextr].status == 'PENDING_REVIEW'" vs-icon-off="pause" vs-icon-on="done" @input="switchStatus('campaign', data[indextr].id)">
                            <span slot="on" v-if="data[indextr].status == 'ACTIVE'">Active</span>
                            <span slot="on" v-else>Review</span>
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
                    € {{total_ads_stats.spent.toFixed(2)}}
                </vs-td>
                <vs-td>
                    {{total_ads_stats.result}}
                </vs-td>
                <vs-td>
                    € {{total_ads_stats.cpa.toFixed(2)}}
                </vs-td>
                <vs-td>
                    {{total_ads_stats.cr.toFixed(2)}}%
                </vs-td>
                <vs-td>
                    € {{total_ads_stats.cpm.toFixed(2)}}
                </vs-td>
                <vs-td>
                    {{total_ads_stats.ctr.toFixed(2)}}%
                </vs-td>
                <vs-td>
                    € {{total_ads_stats.cpc.toFixed(2)}}
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
                <vs-th>Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].name">
                        {{data[indextr].name}}
                    </vs-td>

                    <vs-td :data="data[indextr].status">
                        <vs-switch :color="(data[indextr].status == 'ACTIVE') ? 'success' : 'warning'" v-model="data[indextr].status == 'ACTIVE' || data[indextr].status == 'PENDING_REVIEW'" vs-icon-off="pause" vs-icon-on="done" @input="switchStatus('ad', data[indextr].id)">
                            <span slot="on" v-if="data[indextr].status == 'ACTIVE'">Active</span>
                            <span slot="on" v-else>Review</span>
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
                    € {{total_ads_stats.spent.toFixed(2)}}
                </vs-td>
                <vs-td>
                    {{total_ads_stats.result}}
                </vs-td>
                <vs-td>
                    € {{total_ads_stats.cpa.toFixed(2)}}
                </vs-td>
                <vs-td>
                    {{total_ads_stats.cr.toFixed(2)}}%
                </vs-td>
                <vs-td>
                    € {{total_ads_stats.cpm.toFixed(2)}}
                </vs-td>
                <vs-td>
                    {{total_ads_stats.ctr.toFixed(2)}}%
                </vs-td>
                <vs-td>
                    € {{total_ads_stats.cpc.toFixed(2)}}
                </vs-td>
                <vs-td></vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>

        <!-- Ad accounts table -->
        <vs-table v-if="view == 'ad_accounts'" stripe multiple v-model="selectedAdAccounts" search :data="adAccounts">

            <template slot="thead">
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="adtrust_dsl">Limit</vs-th>
                <vs-th>Cards</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">
                    <vs-td :data="tr.status">
                        <vs-chip transparent :color="tr.status == 2 ? 'danger' : 'success'">{{tr.name}}</vs-chip>
                    </vs-td>
                    <vs-td>{{tr.adtrust_dsl}}</vs-td>
                    <vs-td>
                        <div class="flex items-center">
                            <vs-chip v-for="(card, index) in tr.funding_sources" :key="index">
                                <vs-avatar v-if="getCardIcon(card) != ''" :src="getCardIcon(card)" />{{getCardValue(card)}}
                            </vs-chip>
                        </div>
                    </vs-td>
                </vs-tr>
            </template>
        </vs-table>

        <!-- Create popup -->
        <vs-popup title="Create FB account" :active.sync="showPopup['create']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{newObject['create'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].name" />
                    <span class="text-danger">{{ errors.first('create.name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Status*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['create'].status" class="w-full select-large">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in statusOptions" class="w-full" />
                    </vs-select>
                    <span class="text-danger">{{ errors.first('create.status') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Product</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['create'].product" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in productOptions" class="w-full" />
                    </vs-select>
                    <span class="text-danger">{{ errors.first('create.product_id') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6" v-if="canCreateAsAdmin">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>User</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['create'].user" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in userOptions" class="w-full" />
                    </vs-select>
                    <span class="text-danger">{{ errors.first('create.user_id') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Notes</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-textarea v-model="newObject['create'].notes"></vs-textarea>
                    <span class="text-danger">{{ errors.first('create.notes') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Token</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create'].token" />
                    <span class="text-danger">{{ errors.first('create.token') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>User Agent</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['create'].userAgent" />
                    <span class="text-danger">{{ errors.first('create.user_agent') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Proxy</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                        <template slot="prepend">
                            <div class="prepend-text">
                                <vs-checkbox v-model="newObject['create'].proxyEnabled"></vs-checkbox>
                            </div>
                        </template>
                        <vs-input class="w-full" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['create'].proxy" placeholder="host::port::user::password" />
                    </vx-input-group>
                    <span class="text-danger">{{ errors.first('create.proxy') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Multi create popup -->
        <vs-popup title="Create FB accounts" :active.sync="showPopup['multi_create']">
            <div class="vx-row mb-3 mr-2 ml-2" v-for="(obj, index) in multiCreate">
                <div :class="'vx-col w-full' + (this.canCreateAsAdmin) ? ' md:w-1/3' : ' md:w-1/2'">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="multiCreate[index].name" placeholder="Name" />
                </div>
                <div :class="'vx-col w-full' + (this.canCreateAsAdmin) ? ' md:w-1/3' : ' md:w-1/2'">
                    <vs-select autocomplete v-model="multiCreate[index].user" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in userOptions" class="w-full" />
                    </vs-select>
                </div>
                <div :class="'vx-col w-full' + (this.canCreateAsAdmin) ? ' md:w-1/3' : ' md:w-1/2'">
                    <vs-textarea class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="multiCreate[index].notes" placeholder="Notes" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="md:w-1/3"></div>
                <div class="md:w-1/3"></div>
                <vs-button class="md:w-1/3" icon-pack="feather" icon="icon-plus" @click="multiCreateAdd()">Add</vs-button>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="multiCreateFinish" color="primary" type="filled" :disabled="!isValidForm('multi_create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Edit FB account" :active.sync="showPopup['edit']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{newObject['edit'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].name" />
                    <span class="text-danger">{{ errors.first('edit.name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Status*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['edit'].status" class="w-full select-large">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in statusOptions" class="w-full" />
                    </vs-select>
                    <span class="text-danger">{{ errors.first('edit.status') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Product</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['edit'].product" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in productOptions" class="w-full" />
                    </vs-select>
                    <span class="text-danger">{{ errors.first('edit.product_id') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6" v-if="canCreateAsAdmin">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>User</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['edit'].user" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in userOptions" class="w-full" />
                    </vs-select>
                    <span class="text-danger">{{ errors.first('edit.user_id') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Notes</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-textarea v-model="newObject['edit'].notes"></vs-textarea>
                    <span class="text-danger">{{ errors.first('edit.notes') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Token</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['edit'].token" />
                    <span class="text-danger">{{ errors.first('edit.token') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>User Agent</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['edit'].userAgent" />
                    <span class="text-danger">{{ errors.first('edit.user_agent') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Proxy</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                        <template slot="prepend">
                            <div class="prepend-text">
                                <vs-checkbox v-model="newObject['edit'].proxyEnabled"></vs-checkbox>
                            </div>
                        </template>
                        <vs-input class="w-full" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['edit'].proxy" placeholder="host::port::user::password" />
                    </vx-input-group>
                    <span class="text-danger">{{ errors.first('edit.proxy') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateObject" color="primary" type="filled" :disabled="!isValidForm('edit')">Update</vs-button>
            </div>
        </vs-popup>

        <!-- Import popup -->
        <vs-popup title="Import FB accounts from gologin" :active.sync="showPopup['import']">
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Profiles' IDs</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-textarea v-model="profileIds"></vs-textarea>
                    <span class="text-danger">{{ errors.first('import.profile_ids') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="importProfiles" color="primary" type="filled" :disabled="profileIds == ''">Import</vs-button>
            </div>
        </vs-popup>

        <!-- Business manager info popup -->
        <vs-popup title="Business manager info" :active.sync="showPopup['bm_info']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{bmInfoObj.name}} (ID: {{bmInfoObj.id}})</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col w-full flex items-center mb-2">
                    <a class="mr-auto ml-auto" :href="'https://business.facebook.com/home/accounts?business_id=' + bmInfoObj.id">
                        https://business.facebook.com/home/accounts?business_id={{bmInfoObj.id}}
                    </a>
                </div>
                <div class="vx-col w-1/3">
                </div>
                <div class="vx-col w-1/3 flex items-center">
                    <vs-button @click="checkStatus(bmInfoObjId)" class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw"></vs-button>
                    <vs-button v-clipboard:copy="'https://business.facebook.com/home/accounts?business_id=' + bmInfoObj.id" v-clipboard:success="onCopy" class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-copy"></vs-button>
                </div>
                <div class="vx-col w-1/3">
                </div>
            </div>

            <hr>

            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                    Invitation links:
                </div>
            </div>

            <hr>

            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                    Invitation links:
                </div>
            </div>

            <hr>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Edit budget popup -->
        <vs-popup title="Edit budget" :active.sync="showPopup['edit_budget']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{editingCampaign.name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Daily Budget</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="editingCampaign.daily_budget" />
                </div>
                <div class="vx-col w-full mt-2" v-if="(editingCampaign.object_type == 'campaign') && (editingCampaign.hasOwnProperty('cbo')) && (!editingCampaign.cbo)">
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
                <vs-button class="mr-auto ml-auto">{{editingCampaign.name}}</vs-button>
            </div>

            <div class="vx-row mb-6" v-if="((editingCampaign.object_type == 'adset') && (editingCampaign.hasOwnProperty('cbo')) && (editingCampaign.cbo))">
                <div class="vx-col w-full mt-2">
                    <span>* The campaign of this adset is CBO: you cannot choose bid type for the adset.</span>
                </div>
            </div>
            <div class="vx-row mb-6" v-else>
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Bid Type</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select v-model="editingCampaign.bid_strategy" class="w-full">
                        <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in bidOptions" />
                    </vs-select>
                </div>
            </div>
            
            <div class="vx-row mb-6" v-if="editingCampaign.bid_strategy != 'LOWEST_COST_WITHOUT_CAP'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Bid Amount</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="editingBidAmount" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col w-full mt-2" v-if="(editingCampaign.object_type == 'campaign') && (editingCampaign.hasOwnProperty('cbo')) && (!editingCampaign.cbo)">
                    <span>* This campaign is non-CBO: this bid will be assigned to every adset of this campaign.</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="editBid" color="primary" type="filled" :disabled="!isValidForm('edit_bid')">Update budget</vs-button>
            </div>
        </vs-popup>

        <!-- View ad popup -->
        <vs-popup title="View Ad" :active.sync="showPopup['view_ad']">
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
                                        <vs-button @click="saveAd(viewingObject)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-save"></vs-button>
                                    </div>
                                </vs-td>
                            </vs-tr>
                        </template>
                        
                    </vs-table>
                </div>
            </div>
            
        </vs-popup>

        <!-- Launch ad popup -->
        <vs-popup title="Launch Ad" :active.sync="showPopup['launch_ad']">

            <div v-if="launchAdView == 'normal'">
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Page ID</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="launchAdPageId" />
                    </div>
                </div>
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Campaign name</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="launchAdCampaignName" />
                    </div>
                </div>
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Status</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-select autocomplete v-model="launchAdCampaignStatus" class="w-full select-large">
                            <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in launchAdCampaignStatusOptions" class="w-full" />
                        </vs-select>
                    </div>
                </div>
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Objective</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-select autocomplete v-model="launchAdCampaignObjective" class="w-full select-large">
                            <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in launchAdCampaignObjectiveOptions" class="w-full" />
                        </vs-select>
                    </div>
                </div>
                <div class="vx-row mb-6" v-if="launchAdCampaignObjective == 'CONVERSIONS'">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Pixel ID</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="launchAdPixelId" />
                    </div>
                </div>
                <div class="vx-row mb-6" v-if="launchAdCampaignObjective == 'CONVERSIONS'">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Conversion event</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-select autocomplete v-model="launchAdConversionEvent" class="w-full select-large">
                            <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in launchAdConversionEventOptions" class="w-full" />
                        </vs-select>
                    </div>
                </div>
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Budget</span>
                    </div>
                    <div class="vx-col sm:w-1/3 w-full">
                        <vs-select autocomplete v-model="launchAdCampaignBudgetStrategy" class="w-full select-large">
                            <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in launchAdCampaignBudgetStrategyOptions" class="w-full" />
                        </vs-select>
                    </div>
                    <div class="vx-col sm:w-1/3 w-full">
                        <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="launchAdCampaignBudget" />
                    </div>
                </div>
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Bid</span>
                    </div>
                    <div class="vx-col sm:w-1/3 w-full">
                        <vs-select autocomplete v-model="launchAdCampaignBidStrategy" class="w-full select-large">
                            <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in launchAdCampaignBidStrategyOptions" class="w-full" />
                        </vs-select>
                    </div>
                    <div class="vx-col sm:w-1/3 w-full">
                        <vs-input v-if="launchAdCampaignBidStrategy != 'LOWEST_COST_WITHOUT_CAP'" type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="launchAdCampaignBid" />
                    </div>
                </div>

                <hr>

                <div class="vx-row mb-6 mt-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Gender</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-select autocomplete v-model="launchAdTargetingGender" class="w-full select-large">
                            <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in launchAdTargetingGenderOptions" class="w-full" />
                        </vs-select>
                    </div>
                </div>
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Age</span>
                    </div>
                    <div class="vx-col sm:w-1/3 w-full">
                        <vs-input type="number" min="0" step="1" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="launchAdTargetingAgeMin" />
                    </div>
                    <div class="vx-col sm:w-1/3 w-full">
                        <vs-input type="number" min="0" step="1" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="launchAdTargetingAgeMax" />
                    </div>
                </div>
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Country</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-select autocomplete v-model="launchAdTargetingCountry" class="w-full select-large">
                            <vs-select-item :key="index" :value="item.value" :text="item.label" v-for="(item, index) in launchAdTargetingCountryOptions" class="w-full" />
                        </vs-select>
                    </div>
                </div>

                <hr>

                <!-- TODO: Creative info -->

                <hr>

                <!-- TODO: HERE -->
                <div class="vx-row mb-6 mt-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Title</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="launchAdTitle" />
                    </div>
                </div>
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Text</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-textarea v-model="launchAdText"></vs-textarea>
                    </div>
                </div>
                <div class="vx-row mb-6 mt-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>URL</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="launchAdUrl" />
                    </div>
                </div>
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full">
                        <span>Image/Video</span>
                    </div>
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-image" class="w-full" @click="launchAdView = 'upload'">Choose medias</vs-button>
                        <div>
                            <!--<span class="ml-auto mr-auto">{{ (launchAdFilesToUpload.length == 0) ? 'No file selected' : ((launchAdFilesToUpload.length == 1) ? launchAdFilesToUpload[0].name : launchAdFilesToUpload.length + ' files') }}</span>-->
                            <span class="ml-auto mr-auto">{{ (selectedProductMedias.length == 0) ? 'No media selected' : selectedProductMedias.length + ' media(s) selected' }}</span>
                        </div>
                    </div>
                    <!--
                    <div class="vx-col sm:w-2/3 w-full">
                        <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-image" class="w-full" @click="showFilePicker">Upload</vs-button>
                        <div>
                            <span class="ml-auto mr-auto">{{ (launchAdFilesToUpload.length == 0) ? 'No file selected' : ((launchAdFilesToUpload.length == 1) ? launchAdFilesToUpload[0].name : launchAdFilesToUpload.length + ' files') }}</span>
                        </div>
                        <input type="file" ref="adFileUpload" class="hidden" :accept="'image/*,video/*'" multiple @change="onFilesUploadChange">
                    </div>
                    -->
                </div>

                <div class="vx-row mb-6">
                    <vs-button class="mt-12 mr-auto ml-auto" @click="launchAd" color="primary" type="filled" :disabled="!isValidForm('launch_ad')">Launch Ad(s)</vs-button>
                </div>
            </div>

            <div v-if="launchAdView == 'upload'">
                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="(selectedProductMedias.length > 0) ? 'icon-check' : 'icon-arrow-left'" @click="launchAdView = 'normal'"></vs-button>
                <!-- TODO: Upload from product medias -->
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/3 w-full" :class="productMediaIsSelected(index) ? 'selected-product-media' : ''" v-for="(item, index) in productMedias" @click="selectProductMedia(index)">
                        <img class="w-full" :src="(item.type == 'image') ? item.url : videoThumbnail">
                        <center>{{item.type}} ({{item.pathname}})</center>
                    </div>
                </div>
            </div>

        </vs-popup>

    </vx-card>
</template>

<script>
import JSZip from 'jszip'
import { generateInvoicePDF } from '@assets/utils/fb-invoice'

import { VueContext } from 'vue-context'

function generateInvoices(invoices) {
  // Generate PDFs
  return new Promise((resolve, reject) => {
    let pdfDocs = []
    for (let i = 0; i < invoices.length; i++) {
      pdfDocs.push(generateInvoicePDF(invoices[i]))
    }
    resolve(pdfDocs)
  })
}

const toBase64 = file => new Promise((resolve, reject) => {
  const reader = new FileReader();
  reader.readAsDataURL(file);
  reader.onload = () => resolve(reader.result);
  reader.onerror = error => reject(error);
});

export default {
  components: {
    VueContext,
  },
  data () {
    return {
      view: 'accounts',

      objects: [],
      deleted: [],

      selected: [],
      selectedInvoices: [],
      selectedAds: [],
      selectedAdAccounts: [],

      maxItems: 20,

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        user: '',
        status: 'run',
        product: '',
      },

      statusOptionsForFilters: ['all', 'setup', 'warm', 'ready', 'run', 'ban'],
      statusOptions: ['setup', 'warm', 'ready', 'run', 'ban'],
      userOptions: [],
      productOptions: [],
      myId: 0,

      editingObject: -1,
      editingCampaign: {},
      bidOptions: [
        { value:'LOWEST_COST_WITHOUT_CAP',  label: 'Lowest cost' },
        { value:'LOWEST_COST_WITH_BID_CAP', label: 'Lowest cost with bid cap' },
        { value:'COST_CAP',                 label: 'Cost cap' },
      ],
      editingBidAmount: 0,

      showPopup: {
        'create': false,
        'multi_create': false,
        'edit': false,
        'import': false,
        'bm_info': false,
        'edit_budget': false,
        'edit_bid': false,
        'view_ad': false,
        'launch_ad': false,
      },
      newObject: {
        'create': {
          user: '',
          name: '',
          product: '',
          status: 'setup',
          notes: '',
          token: '',
          userAgent: '',
          proxyEnabled: false,
          proxy: '',
        },
        'edit': {
          user: '',
          name: '',
          product: '',
          status: '',
          notes: '',
          token: '',
          userAgent: '',
          proxyEnabled: false,
          proxy: '',
        },
      },
      multiCreate: [],

      fastEdit: {
        'index': -1,
        'cards': {
          enabled: false,
          value: '',
        },
      },

      profileIds: '',

      adAccountStatusColors: {
        'active': 'success',
        'unsettled': 'warning',
      },
      statusColors: {
        'setup': 'primary',
        'warm': 'dark',
        'ready': 'warning',
        'run': 'success',
        'ban': 'danger',
      },

      dynamicStats: {
        'date_from': '',
        'date_to': '',
      },
      totalStats: {
        amount_spent: 0,
        leads: 0,
        cpl: 0,
      },
      checked: [],

      invoices: [],
      invoicesTotalStats: {
        amount: 0,
      },
      pdfDocs: [],

      bmInfoObj: {},
      bmInfoObjId: 0,
      adAccounts: [],

      campaigns: [],
      ads: [],
      total_ads_stats: {
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

      viewingObject: {},

      currentAccessToken: '',
      launchAdView: 'normal',

      launchAdPageId: '',

      launchAdCampaignName: '',
      launchAdCampaignStatus: 'ACTIVE',
      launchAdCampaignStatusOptions: [ 'ACTIVE', 'PAUSED' ],
      launchAdCampaignObjectiveOptions: [
        { value: 'LINK_CLICKS', label: 'Link clicks' },
        { value: 'CONVERSIONS', label: 'Conversions' },
        { value: 'LEAD_GENERATION', label: 'Lead generation' },
        { value: 'PAGE_LIKES', label: 'Page likes' },
        { value: 'POST_ENGAGEMENT', label: 'Post engagement' },
        { value: 'REACH', label: 'Reach' },
      ],
      launchAdCampaignObjective: 'LINK_CLICKS',
      launchAdPixelId: '',
      launchAdConversionEventOptions: [
        { value: 'SUBSCRIBE', label: 'Subscribe' },
        { value: 'ADD_TO_CART', label: 'Add to cart' },
        { value: 'PURCHASE', label: 'Purchase' },
        { value: 'LEAD', label: 'Lead' },
        { value: 'COMPLETE_REGISTRATION', label: 'Complete registration' },
      ],
      launchAdConversionEvent: 'PURCHASE',

      launchAdCampaignBudgetStrategyOptions: [ 'CBO', 'Adset' ],
      launchAdCampaignBudgetStrategy: 'Adset',
      launchAdCampaignBudget: 0,
      launchAdCampaignBidStrategyOptions: [
        { value: 'LOWEST_COST_WITHOUT_CAP', label: 'Lowest cost' },
        { value: 'LOWEST_COST_WITH_BID_CAP', label: 'Bid cap' },
        { value: 'COST_CAP', label: 'Cost cap' },
      ],
      launchAdCampaignBidStrategy: 'LOWEST_COST_WITHOUT_CAP',
      launchAdCampaignBid: 1,

      launchAdTargetingGenderOptions: [ 'All', 'Male', 'Female' ],
      launchAdTargetingGender: 'All',
      launchAdTargetingAgeMin: 25,
      launchAdTargetingAgeMax: 0,
      launchAdTargetingCountryOptions: [
        { value: 'IT', label: 'Italy'},
        { value: 'US', label: 'United States'},
      ],
      launchAdTargetingCountry: 'IT',

      launchAdTitle: '',
      launchAdText: '',
      launchAdUrl: '',

      launchAdFilesToUpload: [],

      productMedias: [],
      selectedProductMedias: [],
      videoThumbnail: 'data:image/png;base64,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',
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
    canCreateAsAdmin() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin') ||
              this.$store.getters.userHasRole('account_manager'))
    },
  },
  methods: {
    getObjectIndexById(objs, id) {
      let index = -1;
      objs.forEach((obj, i) => {
        if (obj.id == id) {
          index = i
          return
        }
      })
      return index
    },
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        user: '',
        status: 'run',
        product: '',
      }
      this.filterDatesShortcut('this_month')
    },
    applyFilters() {
      this.$vs.loading()

      this.selected = []

      if (this.dynamicStats['date_from'] != this.filters.date_from || this.dynamicStats['date_to'] != this.filters.date_to) {
        this.dynamicStats = {
          'date_from': this.filters.date_from,
          'date_to': this.filters.date_to,
        }
      }

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {}
      if (this.filters.date_from != '') {
        data['filter_date_from'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        data['filter_date_to'] = this.filters.date_to
      }
      if (this.filters.user != '') {
        data['filter_user'] = this.filters.user
      }
      if (this.filters.status != '' && this.filters.status != 'all') {
        data['filter_status'] = this.filters.status
      }
      if (this.filters.product != '') {
        data['filter_product'] = this.filters.product
      }

      // Retrieve fb accounts
      this.$http.get('/api/3rd/fb-accounts', { headers, params: data })
        .then((response) => {
          this.objects = response.data.data
          if (Object.keys(this.dynamicStats).length > 2) {
            for (let i = 0; i < this.objects.length; i++) {
              if (Object.keys(this.dynamicStats).includes("id" + this.objects[i].id)) {
                this.objects[i].amount_spent = this.dynamicStats["id" + this.objects[i].id].amount_spent
                this.objects[i].leads = this.dynamicStats["id" + this.objects[i].id].leads
                this.objects[i].cpl = this.dynamicStats["id" + this.objects[i].id].cpl
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
    onPageChanged(val) {
      this.currentPage = val
    },
    confirmDeletePopup() {
      if (this.selected.length == 0) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Delete confirmation',
        text: 'Are you sure you want to delete ' + this.selected.length + ' object(s)?',
        accept: this.deleteSelected
      })
    },
    deleteSelected() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      this.deleted = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.selected.forEach((tag) => {
        this.$http.delete('/api/3rd/fb-accounts/' + tag.id, { headers })
          .then((response) => {
            this.deleted.push(tag.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' FB account(s) deleted',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.selected = []
              this.deleted = []
              this.applyFilters()
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
    showForm(type) {
      if (type == 'multi_create') {
        for (let i = 0; i < 3; i++) {
          this.multiCreate.push({
            name: '',
            user: '',
            token: '',
          })
        }
        this.showPopup[type] = true
      } else if (type == 'launch_ad') {
        this.launchAdView = 'normal'

        // Retrieve product medias
        this.$vs.loading()
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        this.$http.get('/api/v1/product-medias', { headers })
          .then((response) => {
            this.productMedias = response.data.data
            this.showPopup[type] = true
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
      } else {
        this.showPopup[type] = true
      }
    },
    clearForm(type) {
      if (type == 'multi_create') {
        this.multiCreate = []
      } else {
        this.newObject[type] = {
          name: '',
          product: '',
          status: 'setup',
          user: '',
          notes: '',
          token: '',
        }
        if (this.canCreateAsAdmin) {
          this.newObject[type].user = this.myId
        }
      }
    },
    isValidForm(type) {
      let isValid = false
      if (type == 'multi_create') {
        let flag = false
        for (let i = 0; i < this.multiCreate.length; i++) {
          if (this.multiCreate[i].name != '') {
            flag = true
            break
          }
        }
        return flag
      } else if (type == 'edit_budget') {
        isValid = (this.editingCampaign.hasOwnProperty('daily_budget') &&
            this.editingCampaign.daily_budget > 0)
      } else if (type == 'edit_bid') {
        if (this.editingCampaign.hasOwnProperty('bid_strategy') &&
            this.editingCampaign.bid_strategy != '') {
          return ((this.editingCampaign.bid_strategy == 'LOWEST_COST_WITHOUT_CAP') ||
                  (this.editingCampaign.bid_strategy != 'LOWEST_COST_WITHOUT_CAP') && this.editingBidAmount > 0)
        }
      } else if (type == 'launch_ad') {
        return (this.launchAdCampaignName != '' &&
                this.launchAdCampaignBudget > 0 && this.launchAdCampaignBid >= 0 &&
                this.launchAdTargetingAgeMin >= 0 && this.launchAdTargetingAgeMax >= 0 &&
                this.launchAdTitle != '' && this.launchAdText != '' &&
                this.selectedProductMedias.length > 0)
                //this.launchAdFilesToUpload.length > 0)
      } else {
        isValid = (this.newObject[type].name.length > 0)
        if (this.canCreateAsAdmin) {
          isValid = isValid && (this.newObject[type].user != '')
        }
      }
      return isValid
    },
    createObject() {
      this.$vs.loading()
      this.errors.clear()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.newObject['create'].name,
        status: this.newObject['create'].status,
        user_id: this.newObject['create'].user,
        notes: this.newObject['create'].notes,
        proxy_enabled: this.newObject['create'].proxyEnabled,
        proxy: this.newObject['create'].proxy,
      }
      if (this.newObject['create'].product != '' && this.newObject['create'].product > 0) {
        data.product_id = this.newObject['create'].product
      }
      if (this.newObject['create'].token != '') {
        data.token = this.newObject['create'].token
      }
      if (this.newObject['create'].userAgent != '') {
        data.user_agent = this.newObject['create'].userAgent
      }

      // Create fb account
      this.$http.post('/api/3rd/fb-accounts', data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'FB account created',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.clearForm('create')
          this.showPopup['create'] = false
          this.applyFilters()
        })
        .catch((error) => {
          this.$vs.loading.close()
          for (const [key, value] of Object.entries(error.response.data.errors)) {
            for (let i = 0; i < value.length; i++) {
              this.errors.add({
                scope: 'create',
                field: key,
                msg: value[i]
              })
            }
          }
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    },
    editForm(id) {
      let obj = this.objects[this.getObjectIndexById(this.objects, id)]
      this.newObject['edit'] = {
        name: obj.name,
        product: obj.product.id,
        status: obj.status,
        user: obj.user.id,
        notes: obj.notes,
        token: obj.token,
        userAgent: '',
        proxyEnabled: false,
        proxy: '',
      }
      if (obj.profile.hasOwnProperty('user_agent')) {
        this.newObject['edit'].userAgent = obj.profile['user_agent']
      }
      if (obj.profile.hasOwnProperty('proxy_enabled')) {
        this.newObject['edit'].proxyEnabled = obj.profile['proxy_enabled']
      }
      if (obj.profile.hasOwnProperty('proxy')) {
        this.newObject['edit'].proxy = obj.profile['proxy']
      }
      this.editingObject = obj.id
      this.showForm('edit')
    },
    updateObject() {
      if (this.editingObject <= 0) return

      let id = this.editingObject

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        name: this.newObject['edit'].name,
        status: this.newObject['edit'].status,
        user_id: this.newObject['edit'].user,
        notes: this.newObject['edit'].notes,
        token: this.newObject['edit'].token,
        user_agent: this.newObject['edit'].userAgent,
        proxy_enabled: this.newObject['edit'].proxyEnabled,
        proxy: this.newObject['edit'].proxy,
      }
      if (this.newObject['edit'].product != '' && this.newObject['edit'].product > 0) {
        data.product_id = this.newObject['edit'].product
      }

      this.$http.put('/api/3rd/fb-accounts/' + id, data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'FB account updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.applyFilters()
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
      this.showPopup['edit'] = false
      this.editingObject = -1
    },
    firstCharacters(str, numOfCharacters) {
      if (typeof str != 'string') {
        return ''
      }
      if (numOfCharacters <= 0) {
        return str
      }
      if (str.length <= numOfCharacters) {
        return str
      } else {
        return str.substr(0, numOfCharacters) + '...'
      }
    },
    enableFastEdit(what, index) {
      this.fastEdit['index'] = index
      this.fastEdit[what].enabled = true
    },
    doFastEdit(event, what) {
      if (what == 'cards' && !event.ctrlKey) {
        return
      }
      this.$vs.loading()
      this.errors.clear()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let index = this.fastEdit['index']
      let data = {
        cards: this.objects[index].cards,
      }

      // Update notes and token
      this.$http.put('/api/3rd/fb-accounts/' + this.objects[index].id, data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'FB account updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.fastEdit[what].enabled = false
          this.objects[index] = response.data.data
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
    checkStatus(id) {
      let objIndex = this.getObjectIndexById(this.objects, id)
      this.invoices = []
      this.invoicesTotalStats = {
        amount: 0,
      }

      this.campaigns = []
      this.ads = []

      let obj = this.objects[objIndex]
      if (obj.token == null || obj.token == '') {
        return
      }

      // Check FB API token status and get business accounts
      this.$vs.loading()
      this.$http.get('https://graph.facebook.com/me/businesses?access_token=' + obj.token)
        .then((response) => {
          let businesses = {}
          response.data.data.forEach((bm) => {
            businesses[bm.id] = {
              id: bm.id,
              name: bm.name,
              active: 0,
              warning: 0,
              total: 0,
              funding_sources: [],
              ad_accounts: [],
            }
          })
          let adAccounts = []

          // Get FB ad accounts
          let since = this.filters.date_from
          let until = this.filters.date_to
          let sinceDate = new Date(since + ' 00:00:00')
          let untilDate = new Date(until + ' 23:59:59')
          let sinceTimestamp = parseInt(sinceDate.valueOf() / 1000)
          let untilTimestamp = parseInt(untilDate.valueOf() / 1000)
          let fields = 'account_id,name,business,account_status,funding_source_details,amount_spent,adtrust_dsl'
          fields += ',transactions.time_range({"since":"' + since + '", "until":"' + until +'"})'
          fields += ',campaigns.time_range({"since":"' + since + '", "until":"' + until +'"})\{id,name,status,effective_status,objective,result,spent,impressions,clicks,bid_strategy,daily_budget'
          //fields += ',ads.time_range({"since":"' + since + '", "until":"' + until +'"}){result,spent}'
          fields += ',ads.time_range({"since":"' + since + '", "until":"' + until +'"})\{id,name,status,effective_status,result,spent,impressions,clicks,bid_strategy,bid_amount,daily_budget'
          fields += ',creative\{image_url,body,title,call_to_action_type,object_url,link_url\}'
          fields += '\}'
          fields += '\}'

          this.$http.get('https://graph.facebook.com/v5.0/me/adaccounts?access_token=' + obj.token + '&fields=' + fields)
            .then((response) => {
              let totalAmountSpent = 0
              let totalResult = 0
              let transactions = []
              this.dynamicStats["id" + obj.id] = {
                amount_spent: 0,
                leads: 0,
                cpl: 0,
                transactions: [],
              }

              response.data.data.forEach((adAccount) => {
                let adAccountToPush = {
                  id: adAccount.account_id,
                  name: adAccount.name,
                  funding_sources: [],
                  business: adAccount.business,
                  status: adAccount.account_status,
                  adtrust_dsl: adAccount.adtrust_dsl,
                }

                let adAccountHasBusiness = false
                if (adAccount.hasOwnProperty('business') && adAccount.business.hasOwnProperty('id') &&
                    (adAccount.business.id in businesses)) {
                  adAccountHasBusiness = true
                  businesses[adAccount.business.id].total += 1
                  if (adAccount.account_status == 1) {
                    businesses[adAccount.business.id].active += 1
                  }
                }
                if (adAccount.hasOwnProperty('funding_source_details') && adAccount.funding_source_details.hasOwnProperty('display_string')) {
                  if (adAccountHasBusiness && (adAccount.business.id in businesses)) {
                    if (!businesses[adAccount.business.id].funding_sources.includes(adAccount.funding_source_details.display_string)) {
                      businesses[adAccount.business.id].funding_sources.push(adAccount.funding_source_details.display_string)
                    }
                  }
                  if (!adAccountToPush.funding_sources.includes(adAccount.funding_source_details.display_string)) {
                    adAccountToPush.funding_sources.push(adAccount.funding_source_details.display_string)
                  }
                }
                /*
                if (adAccount.hasOwnProperty('ads') && adAccount.ads.hasOwnProperty('data') && Array.isArray(adAccount.ads.data)) {
                  for (let adsIndex = 0; adsIndex < adAccount.ads.data.length; adsIndex++) {
                    if (adAccount.ads.data[adsIndex].hasOwnProperty('spent')) {
                      totalAmountSpent += parseInt(adAccount.ads.data[adsIndex].spent)
                    }
                    if (adAccount.ads.data[adsIndex].hasOwnProperty('result')) {
                      totalResult += parseInt(adAccount.ads.data[adsIndex].result)
                    }
                  }
                }
                */
                if (adAccount.hasOwnProperty('transactions') && adAccount.transactions.hasOwnProperty('data') && Array.isArray(adAccount.transactions.data)) {
                  for (let tIndex = 0; tIndex < adAccount.transactions.data.length; tIndex++) {
                    let t = adAccount.transactions.data[tIndex]
                    if (t.time < sinceTimestamp || t.time > untilTimestamp) {
                      continue
                    }
                    transactions.push({
                      id: t.id,
                      dateTime: this.formatDate(new Date(t.time * 1000)),
                      amount: t.amount.total_amount,
                      status: t.status,
                    })

                    if (t.status == 'completed') {
                      let fSource = 'Credit card payment'
                      if (adAccountHasBusiness && (adAccount.business.id in businesses) && businesses[adAccount.business.id].funding_sources.length > 0) {
                        fSource = businesses[adAccount.business.id].funding_sources[0]
                      } else if (adAccountToPush.funding_sources.length > 0) {
                        fSource = adAccountToPush.funding_sources[0]
                      }
                      this.invoices.push({
                        'id': t.id,
                        'ad_account_id': adAccount.account_id,
                        'ad_account_name': adAccount.name,
                        'time': t.time,
                        'funding_source': fSource,
                        'reference_number': t.tracking_id,
                        'amount': t.amount.total_amount,
                        'amountFloat': parseFloat(t.amount.total_amount),
                        'currency': t.amount.currency,
                        'billing_reason': t.billing_reason,
                        'billing_start_time': t.billing_start_time,
                        'billing_end_time': t.billing_end_time,
                        'vat_invoice_id': t.vat_invoice_id,
                      })
                      this.invoicesTotalStats.amount += parseFloat(t.amount.total_amount)
                    }
                  }
                }

                // Collect campaigns
                if (adAccount.hasOwnProperty('campaigns')) {
                  for (let adIndex = 0; adIndex < adAccount.campaigns.data.length; adIndex++) {
                    let campaign = adAccount.campaigns.data[adIndex]
 
                    let spent = parseFloat(campaign.spent/100)
                    let cpa = (campaign.result > 0) ? (spent/campaign.result) : spent
                    let cr = (campaign.clicks > 0) ? ((campaign.result/campaign.clicks)*100) : ((campaign.result > 0) ? 9999.99 : 0)
                    let cpm = (campaign.impressions > 0) ? ((spent*1000)/campaign.impressions) : (spent*1000)
                    let ctr = (campaign.impressions > 0) ? ((campaign.clicks/campaign.impressions)*100) : ((campaign.clicks > 0) ? 9999.99 : 0)
                    let cpc = (campaign.clicks > 0) ? (spent/campaign.clicks) : spent

                    let cbo = (campaign.hasOwnProperty('daily_budget') && campaign.hasOwnProperty('bid_strategy'))
                    let daily_budget = 0
                    let bid_strategy = ''
                    if (campaign.hasOwnProperty('daily_budget')) {
                      daily_budget = parseFloat(parseInt(campaign.daily_budget) / 100)
                    }
                    if (campaign.hasOwnProperty('bid_strategy')) {
                      bid_strategy = campaign.bid_strategy
                    }

                    let adAlreadyExists = false
                    for (let j = 0; j < this.campaigns.length; j++) {
                      if (this.campaigns[j].id == campaign.id) {
                        adAlreadyExists = true
                        break
                      }
                    }

                    if (!adAlreadyExists) {
                      let campaignToPush = {
                        object_type: 'campaign',
                        fb_account_token: obj.token,
                        account_id: adAccount.id,
                        id: campaign.id,
                        name: campaign.name,
                        status: campaign.effective_status,
                        spent: spent,
                        result: (campaign.objective == 'PAGE_LIKES') ? 0 : campaign.result, // TODO: This is a momentary patch (it will no count page likes)
                        cpa: cpa,
                        impressions: campaign.impressions,
                        clicks: campaign.clicks,
                        cr: cr,
                        cpm: cpm,
                        ctr: ctr,
                        cpc: cpc,
                        cbo: cbo,
                        daily_budget: daily_budget,
                        bid_strategy: bid_strategy,
                      }
                      this.campaigns.push(campaignToPush)
                    }

                    // Collect ads
                    if (campaign.hasOwnProperty('ads')) {
                      for (let adIndex = 0; adIndex < campaign.ads.data.length; adIndex++) {
                        let ad = campaign.ads.data[adIndex]

                        let adSpent = parseFloat(ad.spent/100)
                        let adCpa = (ad.result > 0) ? (adSpent/ad.result) : adSpent
                        let adCr = (ad.clicks > 0) ? ((ad.result/ad.clicks)*100) : ((ad.result > 0) ? 9999.99 : 0)
                        let adCpm = (ad.impressions > 0) ? ((adSpent*1000)/ad.impressions) : (adSpent*1000)
                        let adCtr = (ad.impressions > 0) ? ((ad.clicks/ad.impressions)*100) : ((ad.clicks > 0) ? 9999.99 : 0)
                        let adCpc = (ad.clicks > 0) ? (adSpent/ad.clicks) : adSpent

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
                        let adImageUrl = ''
                        if (ad.creative.hasOwnProperty('image_url') && ad.creative.image_url != null && ad.creative.image_url != '') {
                          adImageUrl = ad.creative.image_url
                        }

                        let adAlreadyExists = false
                        for (let j = 0; j < this.ads.length; j++) {
                          if (this.ads[j].id == ad.id) {
                            adAlreadyExists = true
                            break
                          }
                        }

                        if (!adAlreadyExists) {
                          let adToPush = {
                            object_type: 'ad',
                            fb_account_token: obj.token,
                            account_id: adAccount.id,
                            id: ad.id,
                            name: ad.name,
                            status: ad.effective_status,
                            spent: adSpent,
                            result: (campaign.objective == 'PAGE_LIKES') ? 0 : ad.result, // TODO: This is a momentary patch (it will no count page likes)
                            cpa: adCpa,
                            impressions: ad.impressions,
                            clicks: ad.clicks,
                            cr: adCr,
                            cpm: adCpm,
                            ctr: adCtr,
                            cpc: adCpc,
                            daily_budget: adDailyBudget,
                            bid_strategy: adBidStrategy,
                            image_url: adImageUrl,
                            body: ad.creative.body,
                            title: ad.creative.title,
                            call_to_action_type: ad.creative.call_to_action_type,
                            url: adUrl,
                            post_url: adLinkUrl,
                          }
                          this.ads.push(adToPush)

                          totalAmountSpent += parseFloat(adToPush.spent)
                          totalResult += parseInt(adToPush.result)
                        }
                      }
                    }
                  }
                }

                adAccounts.push(adAccountToPush)

                if (adAccountHasBusiness) {
                  businesses[adAccount.business.id].ad_accounts.push(adAccountToPush)
                }
              })

              this.dynamicStats["id" + obj.id].amount_spent = parseFloat(totalAmountSpent)
              this.dynamicStats["id" + obj.id].leads = parseInt(totalResult)
              this.dynamicStats["id" + obj.id].cpl = ((totalResult > 0) ? (parseFloat(totalAmountSpent)/totalResult) : parseFloat(totalAmountSpent))
              this.dynamicStats["id" + obj.id].transactions = parseInt(transactions)

              let info = obj.info
              if (info == null) {
                info = {}
              }
              info["bm"] = []
              for (const [key, value] of Object.entries(businesses)) {
                info["bm"].push(value)
              }
              info["ad_accounts"] = adAccounts

              // Update fb account object (token status and businesses status)
              let headers = {
                Authorization: 'Bearer ' + localStorage.getItem('accessToken')
              }
              let data = {
                token_status: 'active',
                info: JSON.stringify(info),
              }

              this.$http.put('/api/3rd/fb-accounts/' + obj.id, data, { headers })
                .then((response) => {
                  this.calculateStats()
                  this.$vs.loading.close()
                  this.$vs.notify({
                    title: 'Success',
                    text: 'Token is active',
                    iconPack: 'feather',
                    icon: 'icon-alert-circle',
                    color: 'success'
                  })
                  this.applyFilters()
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
              // TODO: This is maybe not useful anymore
                /*
              // Update fb account token status
              let headers = {
                Authorization: 'Bearer ' + localStorage.getItem('accessToken')
              }
              let data = {
                token_status: 'active',
              }

              this.$http.put('/api/3rd/fb-accounts/' + obj.id, data, { headers })
                .then((response) => {
                  this.$vs.loading.close()
                  this.$vs.notify({
                    title: 'Success',
                    text: 'Token is active',
                    iconPack: 'feather',
                    icon: 'icon-alert-circle',
                    color: 'success'
                  })
                  this.applyFilters()
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
                */

              // TODO: to delete
              console.log("My fkin error")
              console.log(error)
            })
        })
        .catch((error) => {
          // FB API token is disabled
          let headers = {
            Authorization: 'Bearer ' + localStorage.getItem('accessToken')
          }

          let info = obj.info
          if (info == null) {
            info = {}
          }
          if (info.hasOwnProperty('bm')) {
            for (let i = 0; i < info['bm'].length; i++) {
              if (obj.token_status == 'active') {
                if (info['bm'][i].active > 0) {
                  info['bm'][i].warning = info['bm'][i].active
                  info['bm'][i].active = 0
                }
              }
            }
          }
          let data = {
            token_status: 'disabled',
            info: JSON.stringify(info),
          }
          this.$http.put('/api/3rd/fb-accounts/' + obj.id, data, { headers })
            .then((response) => {
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Error',
                text: 'Token is disabled',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'danger'
              })
              this.applyFilters()
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
    checkStatusGlobal() {
      this.invoices = []
      this.invoicesTotalStats = {
        amount: 0,
      }
      this.campaigns = []
      this.ads = []

      if (this.selected.length == 0) return

      let accountsChecked = 0

      // Check FB API token status and get business accounts
      this.$vs.loading()
      this.deleted = []
      this.selected.forEach((obj) => {
        this.$http.get('https://graph.facebook.com/me/businesses?access_token=' + obj.token)
          .then((response) => {
            let businesses = {}
            response.data.data.forEach((bm) => {
              businesses[bm.id] = {
                id: bm.id,
                name: bm.name,
                active: 0,
                warning: 0,
                total: 0,
                funding_sources: [],
                ad_accounts: [],
              }
            })
            let adAccounts = []

            // Get FB ad accounts
            let since = this.filters.date_from
            let until = this.filters.date_to
            let sinceDate = new Date(since + ' 00:00:00')
            let untilDate = new Date(until + ' 23:59:59')
            let sinceTimestamp = parseInt(sinceDate.valueOf() / 1000)
            let untilTimestamp = parseInt(untilDate.valueOf() / 1000)
            let fields = 'account_id,name,business,account_status,funding_source_details,amount_spent'
            fields += ',transactions.time_range({"since":"' + since + '", "until":"' + until +'"})'
            fields += ',campaigns.time_range({"since":"' + since + '", "until":"' + until +'"})\{id,name,status,effective_status,objective,result,spent,impressions,clicks,bid_strategy,daily_budget'
            //fields += ',ads.time_range({"since":"' + since + '", "until":"' + until +'"}){result,spent}'
            fields += ',ads.time_range({"since":"' + since + '", "until":"' + until +'"})\{id,name,status,effective_status,result,spent,impressions,clicks,bid_strategy,bid_amount,daily_budget'
            fields += ',creative\{image_url,body,title,call_to_action_type,object_url,link_url\}'
            fields += '\}'
            fields += '\}'

            this.$http.get('https://graph.facebook.com/v5.0/me/adaccounts?access_token=' + obj.token + '&fields=' + fields)
              .then((response) => {
                let totalAmountSpent = 0
                let totalResult = 0
                let transactions = []
                this.dynamicStats["id" + obj.id] = {
                  amount_spent: 0,
                  leads: 0,
                  cpl: 0,
                  transactions: [],
                }

                response.data.data.forEach((adAccount) => {
                  let adAccountToPush = {
                    id: adAccount.account_id,
                    name: adAccount.name,
                    funding_sources: [],
                    status: adAccount.account_status,
                    adtrust_dsl: adAccount.adtrust_dsl,
                  }

                  let adAccountHasBusiness = false
                  if (adAccount.hasOwnProperty('business') && adAccount.business.hasOwnProperty('id') &&
                      (adAccount.business.id in businesses)) {
                    adAccountHasBusiness = true
                    businesses[adAccount.business.id].total += 1
                    if (adAccount.account_status == 1) {
                      businesses[adAccount.business.id].active += 1
                    }
                  }
                  if (adAccount.hasOwnProperty('funding_source_details') && adAccount.funding_source_details.hasOwnProperty('display_string')) {
                    if (adAccountHasBusiness && (adAccount.business.id in businesses)) {
                      if (!businesses[adAccount.business.id].funding_sources.includes(adAccount.funding_source_details.display_string)) {
                        businesses[adAccount.business.id].funding_sources.push(adAccount.funding_source_details.display_string)
                      }
                    }
                    if (!adAccountToPush.funding_sources.includes(adAccount.funding_source_details.display_string)) {
                      adAccountToPush.funding_sources.push(adAccount.funding_source_details.display_string)
                    }
                  }
                  /*
                  if (adAccount.hasOwnProperty('ads') && adAccount.ads.hasOwnProperty('data') && Array.isArray(adAccount.ads.data)) {
                    for (let adsIndex = 0; adsIndex < adAccount.ads.data.length; adsIndex++) {
                      if (adAccount.ads.data[adsIndex].hasOwnProperty('spent')) {
                        totalAmountSpent += parseInt(adAccount.ads.data[adsIndex].spent)
                      }
                      if (adAccount.ads.data[adsIndex].hasOwnProperty('result')) {
                        totalResult += parseInt(adAccount.ads.data[adsIndex].result)
                      }
                    }
                  }
                  */
                  if (adAccount.hasOwnProperty('transactions') && adAccount.transactions.hasOwnProperty('data') && Array.isArray(adAccount.transactions.data)) {
                    for (let tIndex = 0; tIndex < adAccount.transactions.data.length; tIndex++) {
                      let t = adAccount.transactions.data[tIndex]
                      if (t.time < sinceTimestamp || t.time > untilTimestamp) {
                        continue
                      }
                      transactions.push({
                        id: t.id,
                        dateTime: this.formatDate(new Date(t.time * 1000)),
                        amount: t.amount.total_amount,
                        status: t.status,
                      })

                      if (t.status == 'completed') {
                        let fSource = 'Credit card payment'
                        if (adAccountHasBusiness && (adAccount.business.id in businesses) && businesses[adAccount.business.id].funding_sources.length > 0) {
                          fSource = businesses[adAccount.business.id].funding_sources[0]
                        } else if (adAccountToPush.funding_sources.length > 0) {
                          fSource = adAccountToPush.funding_sources[0]
                        }
                        this.invoices.push({
                          'id': t.id,
                          'ad_account_id': adAccount.account_id,
                          'ad_account_name': adAccount.name,
                          'time': t.time,
                          'funding_source': fSource,
                          'reference_number': t.tracking_id,
                          'amount': t.amount.total_amount,
                          'amountFloat': parseFloat(t.amount.total_amount),
                          'currency': t.amount.currency,
                          'billing_reason': t.billing_reason,
                          'billing_start_time': t.billing_start_time,
                          'billing_end_time': t.billing_end_time,
                          'vat_invoice_id': t.vat_invoice_id,
                        })
                        this.invoicesTotalStats.amount += parseFloat(t.amount.total_amount)
                      }
                    }
                  }

                  // Collect campaigns
                  if (adAccount.hasOwnProperty('campaigns')) {
                    for (let adIndex = 0; adIndex < adAccount.campaigns.data.length; adIndex++) {
                      let campaign = adAccount.campaigns.data[adIndex]
 
                      let spent = parseFloat(campaign.spent/100)
                      let cpa = (campaign.result > 0) ? (spent/campaign.result) : spent
                      let cr = (campaign.clicks > 0) ? ((campaign.result/campaign.clicks)*100) : ((campaign.result > 0) ? 9999.99 : 0)
                      let cpm = (campaign.impressions > 0) ? ((spent*1000)/campaign.impressions) : (spent*1000)
                      let ctr = (campaign.impressions > 0) ? ((campaign.clicks/campaign.impressions)*100) : ((campaign.clicks > 0) ? 9999.99 : 0)
                      let cpc = (campaign.clicks > 0) ? (spent/campaign.clicks) : spent

                      let cbo = (campaign.hasOwnProperty('daily_budget') && campaign.hasOwnProperty('bid_strategy'))
                      let daily_budget = 0
                      let bid_strategy = ''
                      if (campaign.hasOwnProperty('daily_budget')) {
                        daily_budget = parseFloat(parseInt(campaign.daily_budget) / 100)
                      }
                      if (campaign.hasOwnProperty('bid_strategy')) {
                        bid_strategy = campaign.bid_strategy
                      }

                      let adAlreadyExists = false
                      for (let j = 0; j < this.campaigns.length; j++) {
                        if (this.campaigns[j].id == campaign.id) {
                          adAlreadyExists = true
                          break
                        }
                      }

                      if (!adAlreadyExists) {
                        let campaignToPush = {
                          object_type: 'campaign',
                          fb_account_token: obj.token,
                          account_id: adAccount.id,
                          id: campaign.id,
                          name: campaign.name,
                          status: campaign.effective_status,
                          spent: spent,
                          result: (campaign.objective == 'PAGE_LIKES') ? 0 : campaign.result, // TODO: This is a momentary patch (it will no count page likes)
                          cpa: cpa,
                          impressions: campaign.impressions,
                          clicks: campaign.clicks,
                          cr: cr,
                          cpm: cpm,
                          ctr: ctr,
                          cpc: cpc,
                          cbo: cbo,
                          daily_budget: daily_budget,
                          bid_strategy: bid_strategy,
                        }
                        this.campaigns.push(campaignToPush)
                      }

                      // Collect ads
                      if (campaign.hasOwnProperty('ads')) {
                        for (let adIndex = 0; adIndex < campaign.ads.data.length; adIndex++) {
                          let ad = campaign.ads.data[adIndex]

                          let adSpent = parseFloat(ad.spent/100)
                          let adCpa = (ad.result > 0) ? (adSpent/ad.result) : adSpent
                          let adCr = (ad.clicks > 0) ? ((ad.result/ad.clicks)*100) : ((ad.result > 0) ? 9999.99 : 0)
                          let adCpm = (ad.impressions > 0) ? ((adSpent*1000)/ad.impressions) : (adSpent*1000)
                          let adCtr = (ad.impressions > 0) ? ((ad.clicks/ad.impressions)*100) : ((ad.clicks > 0) ? 9999.99 : 0)
                          let adCpc = (ad.clicks > 0) ? (adSpent/ad.clicks) : adSpent

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
                          let adImageUrl = ''
                          if (ad.creative.hasOwnProperty('image_url') && ad.creative.image_url != null && ad.creative.image_url != '') {
                            adImageUrl = ad.creative.image_url
                          }

                          let adAlreadyExists = false
                          for (let j = 0; j < this.ads.length; j++) {
                            if (this.ads[j].id == ad.id) {
                              adAlreadyExists = true
                              break
                            }
                          }

                          if (!adAlreadyExists) {
                            let adToPush = {
                              object_type: 'ad',
                              fb_account_token: obj.token,
                              id: ad.id,
                              name: ad.name,
                              status: ad.effective_status,
                              spent: adSpent,
                              result: (campaign.objective == 'PAGE_LIKES') ? 0 : ad.result, // TODO: This is a momentary patch (it will no count page likes)
                              cpa: adCpa,
                              impressions: ad.impressions,
                              clicks: ad.clicks,
                              cr: adCr,
                              cpm: adCpm,
                              ctr: adCtr,
                              cpc: adCpc,
                              daily_budget: adDailyBudget,
                              bid_strategy: adBidStrategy,
                              image_url: adImageUrl,
                              body: ad.creative.body,
                              title: ad.creative.title,
                              call_to_action_type: ad.creative.call_to_action_type,
                              url: adUrl,
                              post_url: adLinkUrl,
                            }
                            this.ads.push(adToPush)

                            totalAmountSpent += parseFloat(adToPush.spent)
                            totalResult += parseInt(adToPush.result)
                          }
                        }
                      }
                    }
                  }

                  adAccounts.push(adAccountToPush)

                  if (adAccountHasBusiness) {
                    businesses[adAccount.business.id].ad_accounts.push(adAccountToPush)
                  }
                })

                this.dynamicStats["id" + obj.id].amount_spent = parseFloat(totalAmountSpent)
                this.dynamicStats["id" + obj.id].leads = parseInt(totalResult)
                this.dynamicStats["id" + obj.id].cpl = ((totalResult > 0) ? (parseFloat(totalAmountSpent)/totalResult) : parseFloat(totalAmountSpent))
                this.dynamicStats["id" + obj.id].transactions = parseInt(transactions)

                let info = obj.info
                if (info == null) {
                  info = {}
                }
                info["bm"] = []
                for (const [key, value] of Object.entries(businesses)) {
                  info["bm"].push(value)
                }
                info["ad_accounts"] = adAccounts

                // Update fb account object (token status and businesses status)
                let headers = {
                  Authorization: 'Bearer ' + localStorage.getItem('accessToken')
                }
                let data = {
                  token_status: 'active',
                  info: JSON.stringify(info),
                }
                this.$http.put('/api/3rd/fb-accounts/' + obj.id, data, { headers })
                  .then((response) => {
                    accountsChecked += 1
                    if (accountsChecked == this.selected.length) {
                      this.$vs.loading.close()
                      this.$vs.notify({
                        title: 'Success',
                        text: 'Status checked',
                        iconPack: 'feather',
                        icon: 'icon-alert-circle',
                        color: 'success'
                      })
                      this.applyFilters()
                    }
                  })
                  .catch((error) => {
                    accountsChecked += 1
                    if (accountsChecked == this.selected.length) {
                      this.$vs.loading.close()
                      this.$vs.notify({
                        title: 'Success',
                        text: 'Status checked',
                        iconPack: 'feather',
                        icon: 'icon-alert-circle',
                        color: 'success'
                      })
                      this.applyFilters()
                    }
                  })
              })
              .catch((error) => {
                // Update fb account token status
                let headers = {
                  Authorization: 'Bearer ' + localStorage.getItem('accessToken')
                }
                let data = {
                  token_status: 'active',
                }

                this.$http.put('/api/3rd/fb-accounts/' + obj.id, data, { headers })
                  .then((response) => {
                    accountsChecked += 1
                    if (accountsChecked == this.selected.length) {
                      this.$vs.loading.close()
                      this.$vs.notify({
                        title: 'Success',
                        text: 'Status checked',
                        iconPack: 'feather',
                        icon: 'icon-alert-circle',
                        color: 'success'
                      })
                      this.applyFilters()
                    }
                  })
                  .catch((error) => {
                    accountsChecked += 1
                    if (accountsChecked == this.selected.length) {
                      this.$vs.loading.close()
                      this.$vs.notify({
                        title: 'Success',
                        text: 'Status checked',
                        iconPack: 'feather',
                        icon: 'icon-alert-circle',
                        color: 'success'
                      })
                      this.applyFilters()
                    }
                  })
              })
          })
          .catch((error) => {
            // FB API token is disabled
            let headers = {
              Authorization: 'Bearer ' + localStorage.getItem('accessToken')
            }

            let info = obj.info
            if (info == null) {
              info = {}
            }
            if (info.hasOwnProperty('bm')) {
              for (let i = 0; i < info['bm'].length; i++) {
                if (obj.token_status == 'active') {
                  if (info['bm'][i].active > 0) {
                    info['bm'][i].warning = info['bm'][i].active
                    info['bm'][i].active = 0
                  }
                }
              }
            }
            let data = {
              token_status: 'disabled',
              info: JSON.stringify(info),
            }
            this.$http.put('/api/3rd/fb-accounts/' + obj.id, data, { headers })
              .then((response) => {
                accountsChecked += 1
                if (accountsChecked == this.selected.length) {
                  this.$vs.loading.close()
                  this.$vs.notify({
                    title: 'Success',
                    text: 'Status checked',
                    iconPack: 'feather',
                    icon: 'icon-alert-circle',
                    color: 'success'
                  })
                  this.applyFilters()
                }
              })
              .catch((error) => {
                accountsChecked += 1
                if (accountsChecked == this.selected.length) {
                  this.$vs.loading.close()
                  this.$vs.notify({
                    title: 'Success',
                    text: 'Status checked',
                    iconPack: 'feather',
                    icon: 'icon-alert-circle',
                    color: 'success'
                  })
                  this.applyFilters()
                }
              })
          })
      })
    },
    importProfiles() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let pIds = []
      let lines = this.profileIds.split('\n')
      for (let i = 0; i < lines.length; i++) {
        lines[i] = lines[i].replace('\r', '')
        if (lines[i] != '') {
          pIds.push(lines[i])
        }
      }

      let data = {
        gologin_ids: pIds,
      }

      this.$http.post('/api/3rd/fb-accounts/import', data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'FB account imported',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.profileIds = ''
          this.showPopup['import'] = false
          this.applyFilters()
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
    multiCreateAdd() {
      this.multiCreate.push({
        name: '',
        user: '',
        token: '',
      })
    },
    multiCreateFinish() {
      this.$vs.loading()

      let valid = []
      this.multiCreate.forEach((obj) => {
        if (obj.name != '') {
          let validObj = {
            name: obj.name,
            user_id: this.myId,
            notes: obj.notes,
          }
          if (obj.user != '') {
            validObj.user_id = obj.user
          }
          valid.push(validObj)
        }
      })

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        data: JSON.stringify(valid)
      }

      // Create multiple fb accounts
      this.$http.post('/api/3rd/fb-accounts/multiple', data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'FB account(s) created',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.clearForm('multi_create')
          this.showPopup['multi_create'] = false
          this.applyFilters()
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
      let invalidPeriod = false;

      switch (period) {
        case 'today': {
          let today = new Date();
          this.filters.date_from = this.formatDate(today);
          this.filters.date_to = this.formatDate(today);
        } break;
        case 'yesterday': {
          let dateObj = new Date();
          dateObj.setDate(dateObj.getDate() - 1);
          this.filters.date_from = this.formatDate(dateObj);
          this.filters.date_to = this.formatDate(dateObj);
        } break;
        case 'this_month': {
          let dateObj = new Date();
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1);
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0);
          this.filters.date_from = this.formatDate(fromObj);
          this.filters.date_to = this.formatDate(toObj);
        } break;
        case 'last_month': {
          let dateObj = new Date();
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth() - 1, 1);
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 0);
          this.filters.date_from = this.formatDate(fromObj);
          this.filters.date_to = this.formatDate(toObj);
        } break;
        default: {
          invalidPeriod = true;
        } break;
      }

      if(!invalidPeriod) {
        this.applyFilters();
      }
    },
    formatDate(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0');
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0'); //January is 0!
      let yyyy = dateObj.getFullYear();
      
      let formattedDate = yyyy + '-' + mm + '-' + dd;
      return formattedDate;
    },
    formatDateTime(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0')
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0') //January is 0!
      let yyyy = dateObj.getFullYear()
      
      let formattedDate = yyyy + '-' + mm + '-' + dd

      let hh = String(dateObj.getHours()).padStart(2, '0')
      let ii = String(dateObj.getMinutes()).padStart(2, '0')
      let ss = String(dateObj.getSeconds()).padStart(2, '0')

      let formattedDateTime = formattedDate + ' ' + hh + ':' + ii + ':' + ss

      return formattedDateTime;
    },
    getCardsArray(info) {
      let cardsArray = []

      if (info.hasOwnProperty('bm')) {
        let bm = info["bm"]
        for (let i = 0; i < bm.length; i++) {
          if (!bm[i].hasOwnProperty('funding_sources')) {
            continue
          }
          for (let j = 0; j < bm[i].funding_sources.length; j++) {
            if (cardsArray.includes(bm[i].funding_sources[j])) {
              continue
            }
            cardsArray.push(bm[i].funding_sources[j])
          }
        }
      }

      if (info.hasOwnProperty('ad_accounts')) {
        for (let i = 0; i < info.ad_accounts.length; i++) {
          if (!info.ad_accounts[i].hasOwnProperty('funding_sources')) {
            continue
          }
          for (let j = 0; j < info.ad_accounts[i].funding_sources.length; j++) {
            if (cardsArray.includes(info.ad_accounts[i].funding_sources[j])) {
              continue
            }
            cardsArray.push(info.ad_accounts[i].funding_sources[j])
          }
        }
      }

      return cardsArray
    },
    getCardIcon(card) {
      let cardIcons = {
        'Mastercard': 'https://cdn.vox-cdn.com/thumbor/ttHUTh41dvZk5BDu3yJOZvjHx94=/1400x1400/filters:format(jpeg)/cdn.vox-cdn.com/uploads/chorus_asset/file/13674554/Mastercard_logo.jpg',
        'VISA': 'https://pngimg.com/uploads/visa/visa_PNG27.png',
      }
      for (const [key, value] of Object.entries(cardIcons)) {
        if (card.includes(key)) {
          return value
        }
      }
      return ''
    },
    getCardValue(card) {
      let cardValue = card
      let cardTypes = [ 'Mastercard ', 'VISA ', ]
      for (let i = 0; i < cardTypes.length; i++) {
        cardValue = cardValue.replace(cardTypes[i], '')
      }
      return cardValue
    },
    
    showInvoicePreview(index) {
      this.$vs.loading()
      this.$nextTick(() => {
        setTimeout(() => {
          let doc = generateInvoicePDF(this.invoices[index])
          this.$vs.loading.close()
          window.open(doc.output('bloburl'), '_blank')
        }, 100)
      })
    },
    downloadInvoices() {
      this.$vs.loading()
      
      let numInvoicesUploaded = 0

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken'),
      }

      for (let i = 0; i < this.selectedInvoices.length; i++) {
        let invoice = this.selectedInvoices[i]

        let d = new Date(invoice.time * 1000)
        let invoiceDate = this.formatDateTime(d)

        let info = {
          'ad_account_name': invoice.ad_account_name,
          'ad_account_id': invoice.ad_account_id,
          'funding_source': invoice.funding_source,
          'reference_number': invoice.reference_number,
          'billing_reason': invoice.billing_reason,
          'billing_start_time': invoice.billing_start_time,
          'billing_end_time': invoice.billing_end_time,
        }
        let data = {
          'transaction_id': invoice.id,
          'invoice_date': invoiceDate,
          'number': invoice.vat_invoice_id,
          'language': 'eng',
          'status': 'paid',
          'amount': parseFloat(invoice.amount),
          'currency': invoice.currency.toLowerCase(),
          'info': JSON.stringify(info)
        }

        // Create invoice
        this.$http.post('/api/v1/invoices', data, { headers: headers })
          .then((response) => {
            numInvoicesUploaded++
            if (numInvoicesUploaded >= this.selectedInvoices.length) {
              this.$nextTick(() => {
                setTimeout(() => {
                  generateInvoices(this.selectedInvoices)
                    .then((pdfDocs) => {
                      this.pdfDocs = pdfDocs

                      // Create zip file and download
                      let zip = new JSZip()
                      for (let i = 0; i < this.selectedInvoices.length; i++) {
                        let invoice = this.selectedInvoices[i]
                        let d = new Date(invoice.time * 1000)
                        let filename = ''
                        filename += this.formatDate(d) + 'T' + d.getHours() + '-' + d.getMinutes()
                        filename += ' Transaction #' + invoice.id
                        filename += '.pdf'
                        zip.folder(d.getFullYear().toString())
                          .folder((d.getMonth() + 1).toString().padStart(2, '0'))
                          .file(filename, this.pdfDocs[i].output('arraybuffer'))
                      }
                      zip.generateAsync({type:"blob"}).then(function (blob) {
                        let url = window.URL.createObjectURL(blob)
                        let a = document.createElement("a")
                        document.body.appendChild(a)
                        a.style = "display: none"
                        a.href = url
                        a.download = "invoices.zip"
                        a.click()
                        window.URL.revokeObjectURL(url)
                        this.$vs.loading.close()
                        this.$vs.notify({
                          title: 'Success',
                          text: numInvoicesUploaded + ' invoices uploaded',
                          iconPack: 'feather',
                          icon: 'icon-alert-circle',
                          color: 'success'
                        })
                      }.bind(this), function (err) {
                        // TODO: to delete
                        // This should be here only for debug purposes
                        console.log(err)

                        this.$vs.loading.close()
                        this.$vs.notify({
                          title: 'Error',
                          text: 'Error while downloading PDFs',
                          iconPack: 'feather',
                          icon: 'icon-alert-circle',
                          color: 'danger'
                        })
                      }.bind(this));
                    })
                    .catch((log) => {
                      this.$vs.loading.close()
                      this.$vs.notify({
                        title: 'Error',
                        text: 'Error while generating PDFs',
                        iconPack: 'feather',
                        icon: 'icon-alert-circle',
                        color: 'danger'
                      })
                    })
                }, 100)
              })
            }
          })
          .catch((error) => {
            // TODO: to delete
            // This should be here only for debug purposes
            console.log(error.response)

            numInvoicesUploaded++
            if (numInvoicesUploaded >= this.selectedInvoices.length) {
              this.$nextTick(() => {
                setTimeout(() => {
                  generateInvoices(this.selectedInvoices)
                    .then((pdfDocs) => {
                      this.pdfDocs = pdfDocs

                      // Create zip file and download
                      let zip = new JSZip()
                      for (let i = 0; i < this.selectedInvoices.length; i++) {
                        let invoice = this.selectedInvoices[i]
                        let d = new Date(invoice.time * 1000)
                        let filename = ''
                        filename += this.formatDate(d) + 'T' + d.getHours() + '-' + d.getMinutes()
                        filename += ' Transaction #' + invoice.id
                        filename += '.pdf'
                        zip.folder(d.getFullYear().toString())
                          .folder((d.getMonth() + 1).toString().padStart(2, '0'))
                          .file(filename, this.pdfDocs[i].output('arraybuffer'))
                      }
                      zip.generateAsync({type:"blob"}).then(function (blob) {
                        let url = window.URL.createObjectURL(blob)
                        let a = document.createElement("a")
                        document.body.appendChild(a)
                        a.style = "display: none"
                        a.href = url
                        a.download = "invoices.zip"
                        a.click()
                        window.URL.revokeObjectURL(url)
                        this.$vs.loading.close()
                        this.$vs.notify({
                          title: 'Success',
                          text: numInvoicesUploaded + ' invoices uploaded',
                          iconPack: 'feather',
                          icon: 'icon-alert-circle',
                          color: 'success'
                        })
                      }.bind(this), function (err) {
                        // TODO: to delete
                        // This should be here only for debug purposes
                        console.log(err)

                        this.$vs.loading.close()
                        this.$vs.notify({
                          title: 'Error',
                          text: 'Error while downloading PDFs',
                          iconPack: 'feather',
                          icon: 'icon-alert-circle',
                          color: 'danger'
                        })
                      }.bind(this));
                    })
                    .catch((log) => {
                      this.$vs.loading.close()
                      this.$vs.notify({
                        title: 'Error',
                        text: 'Error while generating PDFs',
                        iconPack: 'feather',
                        icon: 'icon-alert-circle',
                        color: 'danger'
                      })
                    })
                }, 100)
              })
            }
          })
      }
    },
    showAds(id) {
      this.$router.push({name: 'fb-ads', params: {fb_account_id: id}})
        .catch(error => {
          this.$router.push({name: 'fb-ads', params: {fb_account_id: id}})
        })
    },
    showBmInfo(id, bmIndex) {
      this.bmInfoObjId = id
      let objIndex = this.getObjectIndexById(this.objects, id)
      let obj = this.objects[objIndex]
      let bm = obj.info['bm'][bmIndex]
      this.bmInfoObj = bm
      this.showPopup['bm_info'] = true
    },
    showAdAccounts(id) {
      let objIndex = this.getObjectIndexById(this.objects, id)
      let obj = this.objects[objIndex]
      this.adAccounts = obj.info['ad_accounts']
      this.currentAccessToken = obj.token
      this.selectedAdAccounts = []
      this.view = 'ad_accounts'
    },
    onCopy() {
      this.$vs.notify({
        title: 'Success',
        text: 'Copied',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    getAdAccountStatus(info) {
      let result = 'disabled'
      if (info != null && info.hasOwnProperty('ad_accounts') && info.ad_accounts.length > 0) {
        for (let i = 0; i < info.ad_accounts.length; i++) {
          if (info.ad_accounts[i].status == 1) {
            result = 'active'
            break
          }
          if(info.ad_accounts[i].status == 3) {
            result = 'unsettled'
          }
        }
      }
      return result
    },
    calculateStats() {
      this.totalStats =  {
        amount_spent: 0,
        leads: 0,
        cpl: 0,
      }

      for (let i = 0; i < this.objects.length; i++) {
        if (this.objects[i].hasOwnProperty('amount_spent')) {
          this.totalStats.amount_spent += this.objects[i].amount_spent
        }
        if (this.objects[i].hasOwnProperty('leads')) {
          this.totalStats.leads += this.objects[i].leads
        }
      }

      this.totalStats.cpl = ((this.totalStats.leads > 0) ? (parseFloat(this.totalStats.amount_spent)/this.totalStats.leads) : parseFloat(this.totalStats.amount_spent))
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
    calculateAdsStats() {
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

      for (let i = 0; i < this.ads.length; i++) {
        let obj = this.ads[i]
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

      this.total_ads_stats = {
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
        obj = this.ads[index]
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

      let fb_account_token = obj.fb_account_token
      if (fb_account_token == '') {
          return
      }

      let oldStatus = obj.status
      let status = (obj.status == 'ACTIVE') ? 'PAUSED' : 'ACTIVE'
      if (what == 'campaign') {
        this.campaigns[index].status = status
      } else if (what == 'ad') {
        this.ads[index].status = status
      }

      // Change fb campaign/adset/ad status
      this.$http.post('https://graph.facebook.com/v9.0/' + obj.id + '?status=' + obj.status + '&access_token=' + fb_account_token)
        .then((response) => {
          if (response.data.success) {
            if (what == 'campaign') {
              this.campaigns[index].status = status
            } else if (what == 'ad') {
              this.ads[index].status = status
            }
          } else {
            if (what == 'campaign') {
              this.campaigns[index].status = oldStatus
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
    viewAdPopup(id) {
      let adIndex = -1
      for (let i = 0; i < this.ads.length; i++) {
        if (this.ads[i].id == id) {
          adIndex = i
          break
        }
      }

      if (adIndex == -1) return

      this.viewingObject = this.ads[adIndex]
      this.showPopup['view_ad'] = true
    },
    editBudgetPopup(id) {
      this.editingCampaign = this.campaigns[this.getObjectIndexById(this.campaigns, id)]
      this.showPopup['edit_budget'] = true
    },
    editBidPopup(id) {
      this.editingCampaign = this.campaigns[this.getObjectIndexById(this.campaigns, id)]
      this.showPopup['edit_bid'] = true
    },
    editBudget() {
      let dailyBudget = parseInt(this.editingCampaign.daily_budget * 100)

      this.$vs.loading()

      if (this.editingCampaign.object_type == 'campaign') {
        if (this.editingCampaign.hasOwnProperty('cbo') && this.editingCampaign.cbo) {
          // Change campaign daily budget
          this.$http.post('https://graph.facebook.com/v9.0/' + this.editingCampaign.id + '?daily_budget=' + dailyBudget + '&access_token=' + this.fb_account.token)
            .then((response) => {
              this.editingCampaign = {}
              this.showPopup['edit_budget'] = false
              this.$vs.loading.close()
              //this.applyFilters() // TODO: What about this
              this.$vs.notify({
                title: 'Success',
                text: 'Campaign daily budget updated',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
            })
            .catch((error) => {
              this.editingCampaign = {}
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
          for (let i = 0; i < this.editingCampaign.adsets.length; i++) {
            let adset = this.editingCampaign.adsets[i]
            this.$http.post('https://graph.facebook.com/v9.0/' + adset.id + '?daily_budget=' + dailyBudget + '&access_token=' + this.fb_account.token)
              .then((response) => {
                adsetCounter++
                if (adsetCounter == this.editingCampaign.adsets.length) {
                  this.editingCampaign = {}
                  this.showPopup['edit_budget'] = false
                  this.$vs.loading.close()
                  //this.applyFilters() // TODO: What about this?
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
                if (adsetCounter == this.editingCampaign.adsets.length) {
                  this.editingCampaign = {}
                  this.showPopup['edit_budget'] = false
                  this.$vs.loading.close()
                  //this.applyFilters() // TODO: What about this
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
      } else if (this.editingCampaign.object_type == 'adset') {
        // Edit adset daily budget
        this.$http.post('https://graph.facebook.com/v9.0/' + this.editingCampaign.id + '?daily_budget=' + dailyBudget + '&access_token=' + this.fb_account.token)
          .then((response) => {
            this.editingCampaign = {}
            this.showPopup['edit_budget'] = false
            this.$vs.loading.close()
            //this.applyFilters() // TODO: What about this
            this.$vs.notify({
              title: 'Success',
              text: 'Adset daily budget updated',
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'success'
            })
          })
          .catch((error) => {
            this.editingCampaign = {}
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
      let bidStrategy = this.editingCampaign.bid_strategy

      this.$vs.loading()
      if (this.editingCampaign.object_type == 'campaign') {
        if (this.editingCampaign.cbo) {
          // Change campaign bid_strategy
          this.$http.post('https://graph.facebook.com/v9.0/' + this.editingCampaign.id + '?bid_strategy=' + bidStrategy + '&access_token=' + this.fb_account.token)
            .then((response) => {
              // Change all adsets bid amount of this campaign
              if (bidStrategy != 'LOWEST_COST_WITHOUT_CAP') {
                let adsetCounter = 0
                let errorCounter = 0
                for (let i = 0; i < this.editingCampaign.adsets.length; i++) {
                  let adset = this.editingCampaign.adsets[i]

                  let params = ''
                  params += '?bid_amount=' + bidAmount
                  params += '&access_token=' + this.fb_account.token
                  this.$http.post('https://graph.facebook.com/v9.0/' + adset.id + params)
                    .then((response) => {
                      adsetCounter++
                      if (adsetCounter == this.editingCampaign.adsets.length) {
                        this.editingCampaign = {}
                        this.showPopup['edit_bid'] = false
                        this.$vs.loading.close()
                        //this.applyFilters() // TODO: What about this
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
                      if (adsetCounter == this.editingCampaign.adsets.length) {
                        this.editingCampaign = {}
                        this.showPopup['edit_bid'] = false
                        this.$vs.loading.close()
                        //this.applyFilters() // TODO: What about this
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
          for (let i = 0; i < this.editingCampaign.adsets.length; i++) {
            let adset = this.editingCampaign.adsets[i]

            let params = ''
            params += '?bid_strategy=' + bidStrategy
            if (bidStrategy != 'LOWEST_COST_WITHOUT_CAP') {
              params += '&bid_amount=' + bidAmount
            }
            params += '&access_token=' + this.fb_account.token
            this.$http.post('https://graph.facebook.com/v9.0/' + adset.id + params)
              .then((response) => {
                adsetCounter++
                if (adsetCounter == this.editingCampaign.adsets.length) {
                  this.editingCampaign = {}
                  this.showPopup['edit_bid'] = false
                  this.$vs.loading.close()
                  //this.applyFilters() // TODO: What about this
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
                if (adsetCounter == this.editingCampaign.adsets.length) {
                  this.editingCampaign = {}
                  this.showPopup['edit_bid'] = false
                  this.$vs.loading.close()
                  //this.applyFilters() // TODO: What about this
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
      } else if (this.editingCampaign.object_type == 'adset') {
        let params = ''
        params += '?access_token=' + this.fb_account.token
        if (!this.editingCampaign.cbo) {
          params += '&bid_strategy=' + bidStrategy
          if (bidStrategy != 'LOWEST_COST_WITHOUT_CAP') {
            params += '&bid_amount=' + bidAmount
          }
        } else {
          params += '&bid_amount=' + bidAmount
        }
        let doRequestFlag = true
        let campaign = this.campaigns[this.getObjectIndexById(this.campaigns, this.editingCampaign.campaign_id)]
        if (campaign.bid_strategy == 'LOWEST_COST_WITHOUT_CAP') {
          doRequestFlag = false
        }
        if (doRequestFlag) {
          this.$http.post('https://graph.facebook.com/v9.0/' + this.editingCampaign.id + params)
            .then((response) => {
              this.editingCampaign = {}
              this.showPopup['edit_bid'] = false
              this.$vs.loading.close()
              //this.applyFilters() // TODO: What about this
              this.$vs.notify({
                title: 'Success',
                text: 'Adset bid updated',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
            })
            .catch((error) => {
              this.editingCampaign = {}
              this.showPopup['edit_bid'] = false
              this.$vs.loading.close()
              //this.applyFilters() // TODO: What about this
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
    showFilePicker() {
      this.$refs.adFileUpload.click()
    },
    onFilesUploadChange(event) {
      this.launchAdFilesToUpload = event.target.files
    },
    getFileType(f) {
      return f.type.split("/")[0]
    },
    launchAd() {
      // TODO: to delete
      /*
      let videoUrl = 'https://beta2.zarolead.com/storage/product_medias/product_109/V1.mp4'
      let adAccount = this.selectedAdAccounts[0]
      let tok = this.currentAccessToken
      console.log("adAccount", adAccount)
      let fileToUpload = this.launchAdFilesToUpload[0]
      toBase64(fileToUpload)
        .then((result) => {
          let toSearch = 'base64,'
          let fileBase64 = result.substring(result.indexOf(toSearch) + toSearch.length, result.length)
          let data = {
            access_token: tok,
            name: fileToUpload.name,
            file_url: videoUrl,
            //video_file_chuck: fileBase64,
          }
        })
      return
      */


      this.$vs.loading()

      let counter = 0

      let accessToken = this.currentAccessToken

      this.selectedAdAccounts.forEach((adAccount) => {
        // Create campaign
        let data = {
          access_token: accessToken,
          name: this.launchAdCampaignName,
          objective: this.launchAdCampaignObjective,
          status: this.launchAdCampaignStatus,
          special_ad_categories: [],
        }
        if (this.launchAdCampaignBudgetStrategy == 'CBO') {
          data.bid_strategy = this.launchAdCampaignBidStrategy
          data.daily_budget = this.launchAdCampaignBudget*100
        }

        this.$http.post('https://graph.facebook.com/v9.0/act_' + adAccount.id + '/campaigns', data)
          .then((response) => {
            let campaignId = response.data.id

            // Create adset
            let data = {
              access_token: accessToken,
              name: 'Adset',
              optimization_goal: this.launchAdCampaignObjective,
              billing_event: 'IMPRESSIONS', // TODO: What about this?
              campaign_id: campaignId,
              targeting: {},
              status: this.launchAdCampaignStatus,
            }
            if (this.launchAdCampaignBidStrategy != 'LOWEST_COST_WITHOUT_CAP') {
              data.bid_amount = this.launchAdCampaignBid
            }
            if (this.launchAdCampaignBudgetStrategy != 'CBO') {
              data.bid_strategy = this.launchAdCampaignBidStrategy
              data.daily_budget = this.launchAdCampaignBudget*100
            }
            if (this.launchAdTargetingAgeMin > 0) {
              data.targeting.age_min = this.launchAdTargetingAgeMin
            }
            if (this.launchAdTargetingAgeMax > 0) {
              data.targeting.age_max = this.launchAdTargetingAgeMax
            }
            data.targeting.geo_locations = { countries: [] }
            data.targeting.geo_locations.countries.push(this.launchAdTargetingCountry)
            if (this.launchAdTargetingGender == 'Male') {
              data.targeting.genders = [1]
            } else if (this.launchAdTargetingGender == 'Female') {
              data.targeting.genders = [2]
            }

            if (this.launchAdCampaignObjective == 'CONVERSIONS') {
              data.promoted_object = {
                custom_event_type: this.launchAdConversionEvent,
                pixel_id: this.launchAdPixelId,
              }
              data.optimization_goal = 'OFFSITE_CONVERSIONS'
            }

            this.$http.post('https://graph.facebook.com/v9.0/act_' + adAccount.id + '/adsets', data)
              .then((response) => {
                let adsetId = response.data.id

                // Create ad images and ad videos

                // TODO: HERE
                this.selectedProductMedias.forEach((pmIndex) => {
                  let productMedia = this.productMedias[pmIndex]

                  let pageId = this.launchAdPageId // TODO: Better
                  let data = {
                    access_token: accessToken,
                    adset_id: adsetId,
                    name: 'ad',
                    status: this.launchAdCampaignStatus,
                  }

                  if (productMedia.type == 'image') {
                    // TODO: to delete
                    console.log("Image")

                    data.creative = {
                      name: 'ad creative',
                      object_story_spec: {
                        link_data: {
                          call_to_action: { type: 'LEARN_MORE', value: { link: this.launchAdUrl } },
                          link: this.launchAdUrl,
                          name: this.launchAdTitle,
                          message: this.launchAdText,
                          image_url: productMedia.url,
                        },
                        page_id: pageId,
                      },
                    }
                    this.$http.post('https://graph.facebook.com/v9.0/act_' + adAccount.id + '/ads', data)
                      .then((response) => {
                        counter += 1
                        if (counter == this.selectedAdAccounts.length) {
                          this.$vs.loading.close()
                        }
                      })
                      .catch((error) => {
                        // TODO: Log the error
                        console.log("error:", error)
                      })
                  } else if (productMedia.type == 'video') {
                    // TODO: to delete
                    console.log("Video")

                    let videoData = {
                      access_token: accessToken,
                      //name: fileToUpload.name,
                      file_url: productMedia.url,
                    }
                    this.$http.post('https://graph.facebook.com/v9.0/act_' + adAccount.id + '/advideos', videoData)
                      .then((response) => {
                        let videoId = response.data.id
                        data.creative = {
                          name: 'ad creative',
                          object_story_spec: {
                            video_data: {
                              call_to_action: { type: 'LEARN_MORE', value: { link: this.launchAdUrl } },
                              link: this.launchAdUrl,
                              name: this.launchAdTitle,
                              message: this.launchAdText,
                              video_id: videoId,
                              image_url: 'https://beta2.zarolead.com/storage/product_medias/product_109/F1.jpg', // TODO: What about this (video preview)
                            },
                            page_id: pageId,
                          },
                        }
                        let intervalFunction = setInterval(function() {
                          this.$http.post('https://graph.facebook.com/v9.0/act_' + adAccount.id + '/ads', data)
                            .then((response) => {
                              clearInterval(intervalFunction)
                              counter += 1
                              if (counter == this.selectedAdAccounts.length) {
                                this.$vs.loading.close()
                              }
                            })
                            .catch((error) => {
                              // TODO: Log the error
                              console.log("error:", error)
                            })
                        }, 3000)
                      })
                      .catch((error) => {
                        // TODO: Log the error
                        console.log("error:", error)
                      })
                  } else {
                    // TODO: to delete
                    console.log("None")

                    return
                  }
                })

                /*
                // TODO: This is for file upload
                let medias = []
                this.launchAdFilesToUpload.forEach((fileToUpload) => {
                  if (this.getFileType(fileToUpload) == 'image') {
                    toBase64(fileToUpload)
                      .then((result) => {
                        let toSearch = 'base64,'
                        let imageBase64 = result.substring(result.indexOf(toSearch) + toSearch.length, result.length)
                        let data = {
                          access_token: accessToken,
                          name: fileToUpload.name,
                          bytes: imageBase64,
                        }
                        let apiDest = ''
                        if (this.getFileType(fileToUpload) == 'image') {
                          apiDest = 'adimages'
                        } else if (this.getFileType(fileToUpload) == 'video') {
                          apiDest = 'advideos'
                        } else {
                          // TODO: log error
                          console.log('Invalid file type')
                          return
                        }
                        this.$http.post('https://graph.facebook.com/v9.0/act_' + adAccount.id + '/' + apiDest, data)
                          .then((response) => {

                              // TODO: to delete
                              console.log("response", response)

                              if (this.getFileType(fileToUpload) == 'image') {
                                for (const [key, value] of Object.entries(response.data.images)) {
                                  medias.push({
                                    type: 'image',
                                    hash: value.hash,
                                  })
                                }
                              } else if (this.getFileType(fileToUpload) == 'video') {
                                media.push({
                                  type: 'video',
                                  id: response.data.id,
                                })
                              }

                              if (medias.length == this.launchAdFilesToUpload.length) {
                                medias.forEach((media) => {
                                  let pageId = this.launchAdPageId // TODO: Better
                                  let data = {
                                    access_token: accessToken,
                                    adset_id: adsetId,
                                    name: 'ad',
                                    creative: {
                                      name: 'ad creative',
                                      object_story_spec: {
                                        link_data: {
                                          call_to_action: { type: 'LEARN_MORE', value: { link: this.launchAdUrl } },
                                          link: this.launchAdUrl,
                                          name: this.launchAdTitle,
                                          message: this.launchAdText,
                                        },
                                        page_id: pageId,
                                      },
                                    },
                                    status: this.launchAdCampaignStatus,
                                  }

                                  if (media.type == 'image') {
                                    data.creative.object_story_spec.link_data.image_hash = media.hash
                                  } else if (media.type == 'video') {
                                    // TODO: HERE
                                  } else {
                                    return
                                  }
                                  this.$http.post('https://graph.facebook.com/v9.0/act_' + adAccount.id + '/ads', data)
                                    .then((response) => {
                                      counter += 1
                                      if (counter == this.selectedAdAccounts.length) {
                                        this.$vs.loading.close()
                                      }
                                    })
                                    .catch((error) => {
                                      // TODO: Log the error
                                      console.log("error:", error)
                                    })
                                })
                              }
                          })
                    .catch((error) => {
                      // TODO: Log the error
                      console.log("error:", error)
                    })
                      })
                      .catch((error) => {
                        // TODO: Log the error
                        console.log("error:", error)
                      })
                  }
                })
                */
              })
              .catch((error) => {
                // TODO: Log the error
                console.log("error:", error)
              })
          })
          .catch((error) => {
            // TODO: Log the error
            console.log("error:", error)
          })
      })
    },
    productMediaIsSelected(index) {
      return this.selectedProductMedias.includes(index)
    },
    selectProductMedia(index) {
      if (this.selectedProductMedias.includes(index)) {
        let toRemove = this.selectedProductMedias.indexOf(index)
        this.selectedProductMedias.splice(toRemove, 1);
      } else {
        this.selectedProductMedias.push(index)
      }
    }
  },
  created() {
    // TODO: to delete
    let accessToken = 'EAABsbCS1iHgBAI2cionMU0DazQwHqPma1qQJMZArxWG5BpQTDqdX0OxvxSpKRZCmZAV7o5nFgctQQX9tiZAIK1qdXehn5v6CsJP0TCKW9qAz8GGYORCqIwpfqyZAwHNo2XOnEDZAfc6fE4T6m5Tgtp5ymBAhnVT9GZBfm41Ge5O2qiGEZBNIzSp7'
    let accountId = '300688817943793'

    /*
    this.$http.get('https://graph.facebook.com/v4.0/me/adaccounts?fields=promote_pages{Baccess_token,id}&access_token=' + accessToken)
      .then((response) => {
      })
      .catch((error) => {
      })
    */

    /*
    // Create campaign
    let data = {
      name: 'This is my campaign',
      objective: 'LINK_CLICKS',
      status: 'PAUSED',
      special_ad_categories: [],
    }
    this.$http.post('https://graph.facebook.com/v9.0/act_' + accountId + '/campaigns?access_token=' + accessToken, data)
      .then((response) => {
      })
      .catch((error) => {
      })
    */

    /*
    // Create ad image
    let imageBase64 = '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'
    let data = {
      access_token: accessToken,
      name: 'testimg.png',
      bytes: imageBase64,
    }
    this.$http.post('https://graph.facebook.com/v9.0/act_' + accountId + '/adimages', data)
      .then((response) => {
      })
      .catch((error) => {
      })
    */

    /*
    // Create adset
    let campaignId = '23847141639580005'
    let data = {
      access_token: accessToken,
      name: 'This is my adset',
      optimization_goal: 'REACH',
      billing_event: 'IMPRESSIONS',
      bid_amount: 2,
      daily_budget: 1000,
      campaign_id: campaignId,
      targeting: '{"age_min":30,"geo_locations":{"countries":["IT"]}}',
    }
    this.$http.post('https://graph.facebook.com/v9.0/act_' + accountId + '/adsets', data)
      .then((response) => {
      })
      .catch((error) => {
      })
    */

      /*
    this.$http.get('https://graph.facebook.com/v9.0/act_847852266000787/campaigns?fields=id,name&access_token=' + accessToken)
      .then((response) => {
      })
      .catch((error) => {
      })
      */
      /*
    this.$http.get('https://graph.facebook.com/v9.0/me/adaccounts?fields=owned_pages{id,name}&access_token=' + accessToken)
      .then((response) => {
      })
      .catch((error) => {
      })
      */

    // Create ad
    /*
    let imageHash = 'e7ec8f42242d281a704d628d65cc3dec'
    let adsetId = '23847141655970005'
    let pageId = '868161949864521'
    let data = {
      access_token: accessToken,
      adset_id: adsetId,
      name: 'This is my ad',
      creative: {
        name: 'This is my creative',
        object_story_spec: {
          link_data: {
            call_to_action: { type: 'LEARN_MORE', value: { link: 'https://www.petsmart.com/' } },
            link: 'https://www.petsmart.com/',
            message: 'Try lets go',
            image_hash: imageHash,
          },
          page_id: pageId,
        },
      },
      status: 'PAUSED',
    }
    this.$http.post('https://graph.facebook.com/v9.0/act_' + accountId + '/ads', data)
      .then((response) => {
      })
      .catch((error) => {
      })
    */

    let userInfo = this.$store.getters.getUserInfo()
    this.myId = userInfo.id
    this.newObject['create'].user = this.myId

    this.resetFilters()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve products
    this.$vs.loading()
    this.$http.get('/api/v1/products', { headers })
      .then((response) => {
        this.productOptions = []
        response.data.data.forEach((product) => {
          this.productOptions.push({
            id: product.id,
            label: product.name,
          })
        })

        if (this.canCreateAsAdmin) {
          // Retrieve users
          this.$http.get('/api/v1/users', { headers })
            .then((response) => {
              response.data.data.forEach((user) => {
                this.userOptions.push({
                  id: user.id,
                  label: user.email,
                })
              })
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
  },
  mounted () {
  }
}

</script>

<style>
.bm-badge > .con-vs-tooltip > .con-vs-avatar {
  margin-top: 0px;
  margin-bottom: 0px;
}
.selected-product-media {
    border: 3px solid red;
}
</style>
