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
                <vs-button @click="changeView('accounts')" :color="(view == 'accounts') ? 'success' : 'primary'">Accounts</vs-button>
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
                        <div @contextmenu.prevent="$refs.context_menu.open($event, data[indextr])" @click="showAdAccounts(data[indextr].id)">
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
                <div class="vx-col w-full">
                    Ad accounts:
                </div>
                <table class="vx-col w-full">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Limit</th>
                        <th>Cards</th>
                    </tr>
                    </thead>

                    <tr v-for="(item, indextr) in bmInfoObj.ad_accounts">
                        <td>
                            <vs-td :data="item.status">
                                <vs-chip transparent :color="item.status == 2 ? 'danger' : 'success'">{{item.name}}</vs-chip>
                            </vs-td>
                        </td>
                        <td>{{item.adtrust_dsl}}</td>
                        <td>
                            <div class="flex items-center">
                                <vs-chip v-for="(card, index) in item.funding_sources" :key="index">
                                    <vs-avatar v-if="getCardIcon(card) != ''" :src="getCardIcon(card)" />{{getCardValue(card)}}
                                </vs-chip>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Ad accounts popup -->
        <vs-popup title="Business manager info" :active.sync="showPopup['ad_accounts']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">Ad accounts</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                    Ad accounts:
                </div>
                <table class="vx-col w-full">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Limit</th>
                        <th>Cards</th>
                    </tr>
                    </thead>

                    <tr v-for="(item, indextr) in adAccounts">
                        <td>
                            <vs-td :data="item.status">
                                <vs-chip transparent :color="item.status == 2 ? 'danger' : 'success'">{{item.name}}</vs-chip>
                            </vs-td>
                        </td>
                        <td>{{item.adtrust_dsl}}</td>
                        <td>
                            <div class="flex items-center">
                                <vs-chip v-for="(card, index) in item.funding_sources" :key="index">
                                    <vs-avatar v-if="getCardIcon(card) != ''" :src="getCardIcon(card)" />{{getCardValue(card)}}
                                </vs-chip>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

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
        'ad_accounts': false,
        'edit_budget': false,
        'edit_bid': false,
        'view_ad': false,
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
      this.showPopup[type] = true
      if (type == 'multi_create') {
        for (let i = 0; i < 3; i++) {
          this.multiCreate.push({
            name: '',
            user: '',
            token: '',
          })
        }
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
      console.log("YE")
      this.adAccounts = obj.info['ad_accounts']
      console.log("YE2")
      this.showPopup['ad_accounts'] = true
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
  },
  created() {
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
</style>
