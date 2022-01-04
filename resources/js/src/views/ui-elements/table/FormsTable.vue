<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Forms" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-pie-chart" @click="showChart = !showChart"></vs-button>
            <RouterLink to="/dashboard/forms/builder" v-if="canCreateAndEdit">
                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus"></vs-button>
            </RouterLink>
            <vs-button :disabled="selected.length <= 0" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-dollar-sign" @click="showUpdateFormCost"></vs-button>
            <vs-button v-if="canCreateAndEdit" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>
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
                <div class="vx-col md:w-1/3 w-full" v-if="canFilterAsAdmin">
                    <label>User</label>
                    <v-select :options="userOptions" v-model="filters.user" />
                </div>
                <div class="vx-col md:w-1/3 w-full">
                    <label>Traffic Source</label>
                    <v-select :options="traffic_sources" v-model="filters.traffic_source" />
                </div>
                <div class="vx-col md:w-1/3 w-full">
                    <label>Tag</label>
                    <v-select :options="tagOptions" v-model="filters.tag" />
                </div>
                <div class="vx-col md:w-1/3 w-full">
                    <label>Status</label>
                    <v-select :options="statusOptions" v-model="filters.status" />
                </div>
            </div>
            <div class="vx-row mt-6">
                <div class="vx-col w-full">
                    <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
                    <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
                </div>
            </div>
        </div>

        <div v-if="showChart">
            <vue-apex-charts ref="chart" type="line" height="350" :options="lineAreaChartSpline.chartOptions" :series="lineAreaChartSpline.series"></vue-apex-charts> 
        </div>

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

        <vue-context ref="context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.context_menu.close();showFormStats(data.id);" class="flex items-center text-sm">
                <feather-icon icon="ActivityIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show stats</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close();showLandingUrlPopup(data.id);" class="flex items-center text-sm">
                <feather-icon icon="GlobeIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show URL</span>
              </a>
            </li>
            <li v-if="canCreateAndEdit">
              <a href="#" @click="$refs.context_menu.close();goToFormEditor(data.id);" class="flex items-center text-sm">
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit</span>
              </a>
            </li>
            <li v-if="canCreateAndEdit">
              <a href="#" @click="$refs.context_menu.close();cloneForm(data.id);" class="flex items-center text-sm">
                <feather-icon icon="CopyIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Clone</span>
              </a>
            </li>
            <!--
            <li v-if="data.archived == 1">
              <a href="#" @click="$refs.context_menu.close();restoreForm(data.id);" class="flex items-center text-sm">
                <feather-icon icon="UpdateIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Restore</span>
              </a>
            </li>
            -->
            <li>
              <a href="#" @click="$refs.context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vs-table stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="formatted_name">Name</vs-th>
                <vs-th sort-key="archived">Status</vs-th>
                <vs-th sort-key="visits">Visits</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="total_cost">Cost</vs-th>
                <vs-th v-if="isAdmin" sort-key="total_commission">Commission</vs-th>
                <vs-th v-if="isAdmin" sort-key="profit">Profit</vs-th>
                <vs-th v-if="isAdmin" sort-key="roi">ROI</vs-th>
                <vs-th sort-key="cr">CR</vs-th>
                <vs-th v-if="canViewStats" sort-key="cpv">CPV</vs-th>
                <vs-th v-if="canViewStats" sort-key="epv">EPV</vs-th>
                <vs-th sort-key="cpl">CPL</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td class="column-no-padding" :data="data[indextr].formatted_name">
                        <div @contextmenu.prevent="$refs.context_menu.open($event, data[indextr])">
                            <vs-chip>
                                <vs-avatar :src="data[indextr].traffic_source.icon"/>
                                {{ data[indextr].formatted_name }}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].archived">
                        <vs-switch
                            :color="(data[indextr].archived == 0) ? 'success' : 'danger'"
                            v-model="data[indextr].archived == 0"
                            vs-icon-off="pause" vs-icon-on="done"
                            @input="switchArchivedPopup(data[indextr].id)">
                            <span slot="on">Active</span>
                            <span slot="off">Archived</span>
                        </vs-switch>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].visits">
                        {{data[indextr].visits}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].leads">
                        {{data[indextr].leads}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].total_cost">
                        <span>€ {{data[indextr].total_cost.toFixed(2)}}</span>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].total_commission" v-if="isAdmin">
                        € {{data[indextr].total_commission.toFixed(2)}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].profit" v-if="isAdmin">
                        <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : 'danger'">€ {{data[indextr].profit.toFixed(2)}}</vs-chip>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].roi" v-if="isAdmin">
                        <vs-chip transparent :color="(data[indextr].roi > 0) ? 'success' : 'danger'">{{data[indextr].roi.toFixed(2)}} %</vs-chip>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].cr">
                        {{data[indextr].cr.toFixed(2)}} %
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].cpv" v-if="canViewStats">
                        € {{data[indextr].cpv.toFixed(2)}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].epv" v-if="canViewStats">
                        <vs-chip transparent :color="(data[indextr].epv > 0) ? 'success' : 'danger'">€ {{data[indextr].epv.toFixed(2)}}</vs-chip>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].cpl">
                        € {{data[indextr].cpl.toFixed(2)}}
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding">
                    {{total_stats.visits}}
                </vs-td>
                <vs-td class="column-no-padding">
                    {{total_stats.leads}}
                </vs-td>
                <vs-td class="column-no-padding">
                    € {{total_stats.total_cost.toFixed(2)}}
                </vs-td>
                <vs-td class="column-no-padding" v-if="isAdmin">
                    € {{total_stats.total_commission.toFixed(2)}}
                </vs-td>
                <vs-td class="column-no-padding" v-if="isAdmin">
                    <vs-chip transparent :color="(total_stats.profit > 0) ? 'success' : 'danger'">€ {{total_stats.profit.toFixed(2)}}</vs-chip>
                </vs-td>
                <vs-td class="column-no-padding" v-if="isAdmin">
                    <vs-chip transparent :color="(total_stats.roi > 0) ? 'success' : 'danger'">{{total_stats.roi.toFixed(2)}} %</vs-chip>
                </vs-td>
                <vs-td class="column-no-padding">
                    {{total_stats.cr.toFixed(2)}} %
                </vs-td>
                <vs-td class="column-no-padding" v-if="canViewStats">
                    € {{total_stats.cpv.toFixed(2)}}
                </vs-td>
                <vs-td class="column-no-padding" v-if="canViewStats">
                    <vs-chip transparent :color="(total_stats.epv > 0) ? 'success' : 'danger'">€ {{total_stats.epv.toFixed(2)}}</vs-chip>
                </vs-td>
                <vs-td class="column-no-padding">
                    € {{total_stats.cpl.toFixed(2)}}
                </vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>

        <!-- Landing URL popup -->
        <vs-popup title="Landing URL" :active.sync="showPopup['landing_url']">
            <div class="vx-row mb-2" v-for="(item, index) in landingUrlObjects">
                <div class="vx-col w-1/3">
                    {{item.name}}
                </div>
                <div class="vx-col w-1/2">
                    <a href="item.url">{{item.url}}</a>
                </div>
                <div class="vx-col w-1/6">
                    <p class="flex items-center">
                        <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-copy" v-clipboard:copy="item.url" v-clipboard:success="onUrlCopied"></vs-button>
                        <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-code" @click="getFormCode(currentObject.id, item.id)"></vs-button>
                    </p>
                </div>
            </div>

            <div class="vx-row mb-2 p-2">
                <vs-textarea v-model="formCode" disabled height="10rem" />
                <p class="flex items-center">
                    <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-copy" v-clipboard:copy="formCode" v-clipboard:success="onCodeCopied"></vs-button>
                </p>
            </div>
        </vs-popup>

        <!-- Create popup -->
        <vs-popup title="Create Form" :active.sync="showPopup['create']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" color="primary">{{newObject['create'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Network</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <v-select :options="networks" v-model="newObject['create'].network" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Product</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <v-select :options="products" v-model="newObject['create'].product" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Price</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create'].price" />
                </div>
            </div>


            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Commission</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create'].commission" />
                </div>
            </div>


            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Sub commission</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['create'].sub_commission" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Quantity</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create'].quantity" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Traffic Source</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <v-select :options="traffic_sources" v-model="newObject['create'].traffic_source" />
                </div>
            </div>

            <hr class="mt-3 mb-3">

            <!-- FB Pixel -->
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Facebook Pixel</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-facebook" icon-no-border v-model="newObject['create'].fb_pixel" />
                </div>
            </div>

            <!-- Worldfilia -->
            <div class="vx-row mb-6" v-if="newObject['create'].network.label == 'Worldfilia'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>API Key</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['create'].api_key" />
                </div>
            </div>
            <div class="vx-row mb-6" v-if="newObject['create'].network.label == 'Worldfilia'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Source ID</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['create'].source_id" />
                </div>
            </div>
            <div class="vx-row mb-6" v-if="newObject['create'].network.label == 'Worldfilia'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>API Json URL</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-link-2" icon-no-border v-model="newObject['create'].api_call_url" />
                </div>
            </div>

            <!-- Amsped -->
            <div class="vx-row mb-6" v-if="newObject['create'].network.label == 'Amsped'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Product Reference</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['create'].product_reference" />
                </div>
            </div>
            <div class="vx-row mb-6" v-if="newObject['create'].network.label == 'Amsped'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Sub ID</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['create'].sub_id" />
                </div>
            </div>
            <div class="vx-row mb-6" v-if="newObject['create'].network.label == 'Amsped'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>API Json URL</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-link-2" icon-no-border v-model="newObject['create'].api_call_url" />
                </div>
            </div>

            <!-- Affiliation Park -->
            <div class="vx-row mb-6" v-if="newObject['create'].network.label == 'Affiliation Park'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Offer ID</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['create'].offer_id" />
                </div>
            </div>
            <div class="vx-row mb-6" v-if="newObject['create'].network.label == 'Affiliation Park'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Sub ID</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['create'].sub_id" />
                </div>
            </div>
            <div class="vx-row mb-6" v-if="newObject['create'].network.label == 'Affiliation Park'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>API Json URL</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-link-2" icon-no-border v-model="newObject['create'].api_call_url" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Edit Form" :active.sync="showPopup['edit']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" color="primary">{{newObject['edit'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Network</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <v-select :options="networks" v-model="newObject['edit'].network" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Product</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <v-select :options="products" v-model="newObject['edit'].product" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Price</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit'].price" />
                </div>
            </div>


            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Commission</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit'].commission" />
                </div>
            </div>


            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Sub commission</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="newObject['edit'].sub_commission" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Quantity</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['edit'].quantity" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Traffic Source</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <v-select :options="traffic_sources" v-model="newObject['edit'].traffic_source" />
                </div>
            </div>

            <hr class="mt-3 mb-3">

            <!-- FB Pixel -->
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Facebook Pixel</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-facebook" icon-no-border v-model="newObject['edit'].fb_pixel" />
                </div>
            </div>

            <!-- Worldfilia -->
            <div class="vx-row mb-6" v-if="newObject['edit'].network.label == 'Worldfilia'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>API Key</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['edit'].api_key" />
                </div>
            </div>
            <div class="vx-row mb-6" v-if="newObject['edit'].network.label == 'Worldfilia'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Source ID</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['edit'].source_id" />
                </div>
            </div>
            <div class="vx-row mb-6" v-if="newObject['edit'].network.label == 'Worldfilia'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>API Json URL</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-link-2" icon-no-border v-model="newObject['edit'].api_call_url" />
                </div>
            </div>

            <!-- Amsped -->
            <div class="vx-row mb-6" v-if="newObject['edit'].network.label == 'Amsped'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Product Reference</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['edit'].product_reference" />
                </div>
            </div>
            <div class="vx-row mb-6" v-if="newObject['edit'].network.label == 'Amsped'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Sub ID</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['edit'].sub_id" />
                </div>
            </div>
            <div class="vx-row mb-6" v-if="newObject['edit'].network.label == 'Amsped'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>API Json URL</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-link-2" icon-no-border v-model="newObject['edit'].api_call_url" />
                </div>
            </div>

            <!-- Affiliation Park -->
            <div class="vx-row mb-6" v-if="newObject['edit'].network.label == 'Affiliation Park'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Offer ID</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['edit'].offer_id" />
                </div>
            </div>
            <div class="vx-row mb-6" v-if="newObject['edit'].network.label == 'Affiliation Park'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Sub ID</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['edit'].sub_id" />
                </div>
            </div>
            <div class="vx-row mb-6" v-if="newObject['edit'].network.label == 'Affiliation Park'">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>API Json URL</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-link-2" icon-no-border v-model="newObject['edit'].api_call_url" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateObject" color="primary" type="filled" :disabled="!isValidForm('edit')">Update</vs-button>
            </div>
        </vs-popup>

        <!-- Update form cost popup -->
        <vs-popup title="Update form cost" :active.sync="showPopup['update_cost']">
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Cost</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="editingCostValue" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Date</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="editingCostDay" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateFormCost" color="primary" type="filled" :disabled="!isValidForm('update_cost')">Update</vs-button>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import VueApexCharts from 'vue-apexcharts'
import { VueContext } from 'vue-context'

export default {
  components: {
    'v-select': vSelect,
    VueApexCharts,
    VueContext,
  },
  data () {
    return {
      allObjects: [],
      objects: [],
      selected: [],

      currentObject: null,

      trafficSourcesShortcuts: {
        'Facebook': 'FB',
        'Google ads': 'GL',
        'TikTok': 'TK',
        'SMS': 'SMS',
        'Taboola': 'TBL',
        'Outbrain': 'OUT',
        'Yahoo Gemini': 'YHO',
        'MGID': 'MGID',
      },

      maxItems: 20,

      editingObject: -1,

      showPopup: {
        'create': false,
        'edit': false,
        'code': false,
        'landing_url': false,
        'update_cost': false,
      },
      newObject: {
        'create': {
          name: '',
          network: '',
          product: '',
          quantity: '',
          price: 0,
          commission: 0,
          sub_commission: 0,
          traffic_source: '',

          fb_pixel: '',
          sub_id: '',
          api_call_url: '',

          // Worldfilia
          api_key: '',
          source_id: '',

          // Amsped
          product_reference: '',

          // Affiliation Park
          offer_id: '',
        },
        'edit': {
          name: '',
          network: '',
          product: '',
          quantity: '',
          price: 0,
          commission: 0,
          sub_commission: 0,
          traffic_source: '',

          fb_pixel: '',
          sub_id: '',
          api_call_url: '',

          // Worldfilia
          api_key: '',
          source_id: '',

          // Amsped
          product_reference: '',

          // Affiliation Park
          offer_id: '',
        },
      },

      networks: [],
      products: [],
      traffic_sources: [],

      total_stats: {
        visits: 0,
        leads: 0,
        total_cost: 0,
        total_commission: 0,
        profit: 0,
        roi: 0,
        cr: 0,
        cpv: 0,
        epv: 0,
        cpl: 0,
      },
      dateFormat: 'yyyy-MM-dd',
      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        tag: '',
        traffic_source: '',
        status: 'active',
        user: '',
      },
      userOptions: [],
      tagOptions: [],
      statusOptions: [
        'active', 'traffic', 'archived', 'all',
      ],

      codeFilters: {
        landing: '',
      },
      landingOptions: [],
      formCodeId: -1,
      formCode: '',

      showChart: false,
      maxChartLines: 10,
      themeColors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#1E1E1E'],
      lineAreaChartSpline: {
        series: [],
        chartOptions: {
          chart: {
            type: 'line',
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            curve: 'smooth'
          },
          colors: this.themeColors,
          xaxis: {
            categories: [],
          },
          tooltip: {
            x: {
              format: 'yyyy-MM-dd'
            }
          },
        }
      },

      landingUrlObjects: [],

      editingCostValue: 0.0,
      editingCostDay: '',
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
      },
    },
    isAdmin() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin'))
    },
    canViewStats() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin') ||
              this.$store.getters.userHasRole('mediabuyer') || this.$store.getters.userHasRole('buyer'))
    },
    canCreateAndEdit() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin') ||
              this.$store.getters.userHasRole('mediabuyer'))
    },
    canFilterAsAdmin() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin'))
    },
  },
  methods: {
    getObjectIndexById(id) {
      var index = -1;
      for (let i = 0; i < this.allObjects.length; i++) {
        if (this.allObjects[i].id == id) {
          index = i
          break
        }
      }
      return index
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    confirmDeletePopup() {
      if (this.selected.length == 0) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Archive confirmation',
        text: 'Are you sure you want to archive ' + this.selected.length + ' object(s)?',
        accept: this.deleteSelected
      })
    },
    switchArchivedPopup(id) {
      let obj = this.allObjects[this.getObjectIndexById(id)]
      if (obj.archived == 0) {
        this.deleteObject(id)
      } else {
        this.restoreObject(id)
      }
    },
    deleteObject(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$http.delete('/api/v1/forms/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Form archived',
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
    },
    restoreObject(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$http.get('/api/v1/forms/' + id + '/restore', { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Form restored',
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
    },
    async deleteSelected() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      for (let i = 0; i < this.selected.length; i++) {
        let form = this.selected[i]
        await this.$http.delete('/api/v1/forms/' + form.id, { headers })
      }

      this.$vs.notify({
        title: 'Success',
        text: this.selected.length + ' Form(s) archived',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.selected = []

      this.$vs.loading.close()

      this.applyFilters()
    },
    showForm(type) {
      this.showPopup[type] = true
    },
    clearForm(type) {
      this.newObject[type] = {
        name: '',
        network: '',
        product: '',
        quantity: '',
        price: 0,
        commission: 0,
        sub_commission: 0,
        traffic_source: '',

        fb_pixel: '',
        sub_id: '',
        api_call_url: '',

        // Worldfilia
        api_key: '',
        source_id: '',

        // Amsped
        product_reference: '',

        // Affiliation Park
        offer_id: '',
      }
    },
    isValidForm(type) {
      let obj = this.newObject[type]
      if (type == 'update_cost') {
        return (this.editingCostValue != '' && this.editingCostDay != '')
      } else {
        return (obj.name.length > 0
          && obj.network != '' && obj.network.id >= 1
          && obj.product != '' && obj.product.id >= 1
          && obj.quantity.length > 0
          && obj.traffic_source != '' && obj.traffic_source.id >= 1)
      }
    },
    createObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.newObject['create'].name,
        network_id: this.newObject['create'].network.id,
        product_id: this.newObject['create'].product.id,
        quantity: this.newObject['create'].quantity,
        price: this.newObject['create'].price,
        commission: this.newObject['create'].commission,
        sub_commission: this.newObject['create'].sub_commission,
        traffic_source_id: this.newObject['create'].traffic_source.id,
        
        network_params: {
        }
      }
      if (this.newObject['create'].network.label == 'Worldfilia') {
        data.network_params = {
          fb_pixel: this.newObject['create'].fb_pixel,
          api_call_url: this.newObject['create'].api_call_url,
          api_call_method: 'POST',
          api_key: this.newObject['create'].api_key,
          source_id: this.newObject['create'].source_id,
        }
      } else if (this.newObject['create'].network.label == 'Amsped') {
        data.network_params = {
          fb_pixel: this.newObject['create'].fb_pixel,
          api_call_url: this.newObject['create'].api_call_url,
          api_call_method: 'POST',
          sub_id: this.newObject['create'].sub_id,
          product_reference: this.newObject['create'].product_reference,
        }
      } else if (this.newObject['create'].network.label == 'Affiliation Park') {
        data.network_params = {
          fb_pixel: this.newObject['create'].fb_pixel,
          api_call_url: this.newObject['create'].api_call_url,
          api_call_method: 'GET',
          sub_id: this.newObject['create'].sub_id,
          offer_id: this.newObject['create'].offer_id,
        }
      }
      data.network_params = JSON.stringify(data.network_params)

      // Create form
      this.$http.post('/api/v1/forms', data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Form created',
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
      let obj = this.allObjects[this.getObjectIndexById(id)]
      this.newObject['edit'] = {
        name: obj.name,
        network: {
          id: obj.network.id,
          label: obj.network.name,
        },
        product: {
          id: obj.product.id,
          label: obj.product.name,
        },
        quantity: obj.quantity,
        price: obj.price,
        commission: obj.commission,
        sub_commission: obj.sub_commission,
        traffic_source: {
          id: obj.traffic_source.id,
          label: obj.traffic_source.name,
        },

        fb_pixel: '',
        api_call_url: '',
        sub_id: '',

        // Worldfilia
        api_key: '',
        source_id: '',

        // Amsped
        product_reference: '',

        // Affiliation Park
        offer_id: '',
      }
      if (obj.hasOwnProperty('network_params')) {
        if (obj.network_params.hasOwnProperty('fb_pixel')) {
          this.newObject['edit'].fb_pixel = obj.network_params.fb_pixel
        }
        if (obj.network_params.hasOwnProperty('api_call_url')) {
          this.newObject['edit'].api_call_url = obj.network_params.api_call_url
        }
        if (obj.network.name == 'Worldfilia') {
          if (obj.network_params.hasOwnProperty('api_key')) {
            this.newObject['edit'].api_key = obj.network_params.api_key
          }
          if (obj.network_params.hasOwnProperty('source_id')) {
            this.newObject['edit'].source_id = obj.network_params.source_id
          }
        } else if (obj.network.name == 'Amsped') {
          if (obj.network_params.hasOwnProperty('sub_id')) {
            this.newObject['edit'].sub_id = obj.network_params.sub_id
          }
          if (obj.network_params.hasOwnProperty('product_reference')) {
            this.newObject['edit'].product_reference = obj.network_params.product_reference
          }
        } else if (obj.network.name == 'Affiliation Park') {
          if (obj.network_params.hasOwnProperty('sub_id')) {
            this.newObject['edit'].sub_id = obj.network_params.sub_id
          }
          if (obj.network_params.hasOwnProperty('offer_id')) {
            this.newObject['edit'].offer_id = obj.network_params.offer_id
          }
        }
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
        network_id: this.newObject['edit'].network.id,
        product_id: this.newObject['edit'].product.id,
        quantity: this.newObject['edit'].quantity,
        price: this.newObject['edit'].price,
        commission: this.newObject['edit'].commission,
        sub_commission: this.newObject['edit'].sub_commission,
        traffic_source_id: this.newObject['edit'].traffic_source.id,
      }
      if (this.newObject['edit'].network.label == 'Worldfilia') {
        data.network_params = {
          fb_pixel: this.newObject['edit'].fb_pixel,
          api_call_url: this.newObject['edit'].api_call_url,
          api_call_method: 'POST',
          api_key: this.newObject['edit'].api_key,
          source_id: this.newObject['edit'].source_id,
        }
      } else if (this.newObject['edit'].network.label == 'Amsped') {
        data.network_params = {
          fb_pixel: this.newObject['edit'].fb_pixel,
          api_call_url: this.newObject['edit'].api_call_url,
          api_call_method: 'POST',
          sub_id: this.newObject['edit'].sub_id,
          product_reference: this.newObject['edit'].product_reference,
        }
      } else if (this.newObject['edit'].network.label == 'Affiliation Park') {
        data.network_params = {
          fb_pixel: this.newObject['edit'].fb_pixel,
          api_call_url: this.newObject['edit'].api_call_url,
          api_call_method: 'GET',
          sub_id: this.newObject['edit'].sub_id,
          offer_id: this.newObject['edit'].offer_id,
        }
      }
      data.network_params = JSON.stringify(data.network_params)

      this.$http.put('/api/v1/forms/' + id, data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Form updated',
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
    showFormStats(id) {
      this.$router.push({name: 'form-stats', params: {form_id: id}})
        .catch(error => {
          this.$router.push({name: 'form-stats', params: {form_id: id}})
        })
    },
    goToFormEditor(id) {
      this.$router.push({name: 'form-editor', params: {form_id: id}})
        .catch(error => {
          this.$router.push({name: 'form-editor', params: {form_id: id}})
        })
    },
    onCodeCopied() {
      this.$vs.notify({
        title: 'Success',
        text: 'Form code copied',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    calculateStats() {
      this.total_stats = {
        visits: 0,
        leads: 0,
        total_cost: 0,
        total_commission: 0,
        profit: 0,
        roi: 0,
        cr: 0,
        cpv: 0,
        epv: 0,
        cpl: 0,
      }

      for (let i = 0; i < this.objects.length; i++) {
        this.objects[i].stats = {
          visits: 0,
          leads: 0,
          total_commission: 0,
          //total_sub_commission: 0, // TODO: Is this useful?
          total_cost: 0,
        }
      
        for (let j = 0; j < this.objects[i].daily_stats.length; j++) {
          this.objects[i].stats.visits += parseInt(this.objects[i].daily_stats[j].visits)
          this.objects[i].stats.leads += parseInt(this.objects[i].daily_stats[j].leads)
          this.objects[i].stats.total_commission += parseFloat(this.objects[i].daily_stats[j].total_commission)
          this.objects[i].stats.total_cost += parseFloat(this.objects[i].daily_stats[j].total_cost)
        }

        let obj = this.objects[i]

        let profit = parseFloat(obj.stats.total_commission) - parseFloat(obj.stats.total_cost)
        let roi = parseFloat((obj.stats.total_cost > 0 && profit > 0) ? (profit/parseFloat(obj.stats.total_cost))*100 : ((profit == 0) ? 0 : 9999))
        if (profit < 0) roi = -roi
        let cr = 0.0
        if (obj.stats.leads == 0) cr = 0.0
        else if (obj.stats.visits == 0) cr = 100.0
        else cr = parseFloat((obj.stats.leads/obj.stats.visits)*100.0)
        let cpv = parseFloat((obj.stats.visits == 0) ? obj.stats.total_cost : obj.stats.total_cost/obj.stats.visits)
        let epv = parseFloat((obj.stats.visits == 0) ? obj.stats.total_commission : obj.stats.total_commission/obj.stats.visits)
        let cpl = parseFloat((obj.stats.leads == 0) ? obj.stats.total_cost : obj.stats.total_cost/obj.stats.leads)

        this.total_stats.visits += parseInt(obj.stats.visits)
        this.total_stats.leads += parseInt(obj.stats.leads)
        this.total_stats.total_cost += parseFloat(obj.stats.total_cost),
        this.total_stats.total_commission += parseFloat(obj.stats.total_commission),

        this.objects[i].stats.profit = profit
        this.objects[i].stats.roi = roi
        this.objects[i].stats.cr = cr
        this.objects[i].stats.cpv = cpv
        this.objects[i].stats.epv = epv
        this.objects[i].stats.cpl = cpl

        // NOTE: This is useful for table sorting since the table is not able to
        // sort nested property (e.g. stats.*)
        this.objects[i].visits = parseInt(obj.stats.visits)
        this.objects[i].leads = parseInt(obj.stats.leads)
        this.objects[i].total_commission = parseFloat(obj.stats.total_commission)
        this.objects[i].total_cost = parseFloat(obj.stats.total_cost)
        this.objects[i].profit = profit
        this.objects[i].roi = roi
        this.objects[i].cr = cr
        this.objects[i].cpv = cpv
        this.objects[i].epv = epv
        this.objects[i].cpl = cpl
      }

      // Total stats
      let obj = this.total_stats

      let profit = obj.total_commission - obj.total_cost
      let roi = parseFloat((obj.total_cost > 0 && profit > 0) ? (profit/obj.total_cost)*100 : ((profit == 0) ? 0 : 9999))
      if (profit < 0) roi = -roi
      let cr = 0.0
      if (obj.leads == 0) cr = 0.0
      else if (obj.visits == 0) cr = 100.0
      else cr = parseFloat((obj.leads/obj.visits)*100.0)
      let cpv = parseFloat((obj.visits == 0) ? obj.total_cost : obj.total_cost/obj.visits)
      let epv = parseFloat((obj.visits == 0) ? obj.total_commission : obj.total_commission/obj.visits)
      let cpl = parseFloat((obj.leads == 0) ? obj.total_cost : obj.total_cost/obj.leads)

      this.total_stats.profit = profit
      this.total_stats.roi = roi
      this.total_stats.cr = cr
      this.total_stats.cpv = cpv
      this.total_stats.epv = epv
      this.total_stats.cpl = cpl
    },
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        tag: '',
        traffic_source: '',
        status: 'active',
        user: '',
      }
      this.filterDatesShortcut('last_7_days')
    },
    applyFilters() {
      this.$vs.loading()

      this.selected = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        'get_stats': 1,
      }
      if (this.filters.date_from != '') {
        data['filter_date_from'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        data['filter_date_to'] = this.filters.date_to
      }

      // Retrieve forms
      this.$http.get('/api/v1/forms', { headers, params: data })
        .then((response) => {
          this.allObjects = response.data.data

          this.objects = []
          for(let i = 0; i < this.allObjects.length; i++ ) {
            let obj = this.allObjects[i]
            obj.formatted_name = this.formatFormName(obj)

            let flag = false
            switch (this.filters.status) {
              case 'active':
                flag = (obj.archived != 1)
                break
              case 'traffic':
                flag = (obj.stats.visits > 0)
                break
              case 'archived':
                flag = (obj.archived == 1)
                break
              case 'all':
              default:
                flag = true
                break
            }

            if (this.filters.tag != '') {
              flag = flag && (obj.product.tag.id == this.filters.tag.id)
            }

            if (this.filters.traffic_source != '') {
              flag = flag && (obj.traffic_source.id == this.filters.traffic_source.id)
            }

            if (this.filters.user != '') {
              flag = flag && (obj.user.id == this.filters.user.id)
            }

            if (flag) {
              this.objects.push(obj)
            }

            let tsIndex = -1
            for (let j = 0; j < this.traffic_sources.length; j++) {
              if (this.traffic_sources[j].id == this.allObjects[i].traffic_source.id) {
                tsIndex = j
                break
              }
            }
            if (tsIndex == -1) {
              this.allObjects[i].traffic_source.icon = ''
            } else {
              this.allObjects[i].traffic_source.icon = this.traffic_sources[tsIndex].icon
            }
          }

          this.objects.sort((a, b) => {
            if (a.traffic_source.id < b.traffic_source.id) {
              return -1
            } else if (a.traffic_source.id > b.traffic_source.id) {
              return 1
            } else {
              return 0
            }
          })

          this.calculateStats()

          this.loadChart()
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
        case 'last_7_days': {
          let dateObjTo = new Date();
          let dateObjFrom = new Date();
          dateObjFrom.setDate(dateObjTo.getDate() - 6)
          this.filters.date_from = this.formatDate(dateObjFrom);
          this.filters.date_to = this.formatDate(dateObjTo);
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
    loadChart() {
      let maxNum = Math.min(this.maxChartLines, this.objects.length)
      if (maxNum == 0) {
        return
      }

      // Chart X axis
      let dateObj = new Date()
      let startDay = new Date(this.filters.date_from)
      let endDay = new Date(this.filters.date_to)

      let diffTime = Math.abs(endDay - startDay);
      let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
      let numDots = diffDays + 1

      let currentDay = new Date(this.filters.date_from)
      let categories = []
      for(let i = 0; i < numDots; i++) {
        categories.push(this.formatDate(currentDay))
        currentDay.setDate(currentDay.getDate() + 1)
      }
      this.lineAreaChartSpline.chartOptions = {
        chart: {
          type: 'line',
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth'
        },
        colors: this.themeColors,
        xaxis: {
          categories: categories,
        },
        tooltip: {
          x: {
            format: 'yyyy-MM-dd'
          }
        },
      }

      // Retrieve forms' stats and set chart Y axis
      this.lineAreaChartSpline.series = []
      let series = []

      for(let i = 0; i < maxNum; i++) {
        let form = this.objects[i]

        let stats = form.daily_stats

        // Calculate CPV and EPV
        let values = []
        for(let j = 0; j < numDots; j++) {
          values.push(0)
        }
        for(let j = 0; j < stats.length; j++) {
          let stat = stats[j]

          let cpv = 0
          let epv = 0
          let ratio = 0

          cpv = parseFloat((stat.visits == 0) ? stat.total_cost : stat.total_cost/stat.visits)
          epv = parseFloat((stat.visits == 0) ? stat.total_commission : stat.total_commission/stat.visits)

          ratio = ((cpv == 0) ? epv : epv / cpv)

          let index = categories.indexOf(stat.day)

          if (index >= 0) {
            values[index] = ratio.toFixed(2)
          }

        }

        series.push({
          name: form.name,
          data: values,
        })
      }
      this.lineAreaChartSpline.series = series
    },
    cloneForm(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        form_id: id
      }

      this.$http.post('/api/v1/forms/' + id + '/clone', data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Form created',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          let newForm = response.data.data
          this.$router.push({name: 'form-editor', params: {form_id: newForm.id}})
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
    restoreForm(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$http.get('/api/v1/forms/' + id + '/restore', { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Form restored',
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
    },
    showLandingUrlPopup(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let obj = this.allObjects[this.getObjectIndexById(id)]

      this.currentObject = obj

      let data = {}
      if (this.filters.product != '') {
        data['filter_product'] = obj.product.id
      }

      this.landingUrlObjects = []
      this.$http.get('/api/v1/landings', { headers, params: data })
        .then((response) => {
          response.data.data.forEach((landing) => {
            let url = landing.url
            let separator = '?'
            if (url.includes('?')) {
              separator = '&'
            }
            url = url + separator + 'id=' + obj.id
            this.landingUrlObjects.push({
              id: landing.id,
              name: landing.name,
              url: url,
            })
          })

          this.$vs.loading.close()
          this.showPopup['landing_url'] = true
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
    onUrlCopied() {
      this.$vs.notify({
        title: 'Success',
        text: 'Landing URL copied',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    onCodeCopied() {
      this.$vs.notify({
        title: 'Success',
        text: 'Code copied',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    showUpdateFormCost() {
      this.editingCostValue = 0.0
      this.editingCostDay = this.formatDate(new Date())
      this.showPopup['update_cost'] = true
    },
    updateFormCost() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        day: this.editingCostDay,
        total_cost: this.editingCostValue,
      }

      let counter = 0
      this.selected.forEach((obj) => {
        this.$http.put('/api/v1/forms/' + obj.id + '/stats-edit', data, { headers })
          .then((response) => {
            counter += 1
            if (counter >= this.selected.length) {
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Success',
                text: 'Forms cost upadated',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.showPopup['update_cost'] = false
              this.applyFilters()
            }
          })
          .catch((error) => {
            counter += 1
            if (counter >= this.selected.length) {
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Success',
                text: 'Forms cost upadated',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.showPopup['update_cost'] = false
              this.applyFilters()
            }
          })
      })
    },
    formatFormName(form) {
      let shortcut = 'NULL'
      if (this.trafficSourcesShortcuts.hasOwnProperty(form.traffic_source.name)) {
        shortcut = this.trafficSourcesShortcuts[form.traffic_source.name]
      }
      return shortcut + ' - ' + form.name
    },
    async fetchFormCode(formId, landingId) {
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
          default_form: formId,
      }

      // Retrieve form code snippet
      let formCode = null
      try {
        const response = await this.$http.get('/api/v1/landings/' + landingId + '/code', { params: data, headers: headers })
        formCode = response.data.data
      } catch (error) {
        formCode = null
      }

      return formCode
    },
    async getFormCode(formId, landingId) {
      this.$vs.loading()

      const formCode = await this.fetchFormCode(formId, landingId)
      this.formCode = formCode

      this.$vs.loading.close()
    },
  },
  created() {
    this.resetFilters()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve traffic sources
    this.$http.get('/api/v1/traffic-sources', { headers })
      .then((response) => {
        this.traffic_sources = []
        response.data.data.forEach((traffic_source) => {
          this.traffic_sources.push({
            id: traffic_source.id,
            label: traffic_source.name,
            icon: traffic_source.icon,
          })
        })

        // Retrieve networks
        this.$http.get('/api/v1/networks', { headers })
          .then((response) => {
            this.networks = []
            response.data.data.forEach((network) => {
              this.networks.push({
                id: network.id,
                label: network.name,
              })
            })

            // Retrieve tags
            this.$http.get('/api/v1/tags', { headers })
              .then((response) => {
                this.tagOptions = []
                response.data.data.forEach((tag) => {
                  this.tagOptions.push({
                    id: tag.id,
                    label: tag.name,
                  })
                })

                // Retrieve products
                this.$http.get('/api/v1/products', { headers })
                  .then((response) => {
                    this.products = []
                    response.data.data.forEach((product) => {
                      this.products.push({
                        id: product.id,
                        label: product.name,
                      })
                    })

                    if (this.canFilterAsAdmin) {
                      // Retrieve users
                      this.$http.get('/api/v1/users', { headers })
                        .then((response) => {
                          this.userOptions = []
                          response.data.data.forEach((user) => {
                            this.userOptions.push({
                              id: user.id,
                              label: user.email,
                            })
                          })
                        })
                        .catch((error) => {
                        })
                    }
                  })
                  .catch((error) => {
                  })
              })
              .catch((error) => {
              })
          })
          .catch((error) => {
          })
      })
      .catch((error) => {
      })
  },
  mounted () {
  }
}

</script>

<style scoped>
.column-no-padding {
  padding-top: 0px !important;
  padding-bottom: 0px !important;
  font-size: 0.8em;
  white-space: nowrap;
}
</style>
