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
            <!--<vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create')">Create new</vs-button>-->
            <RouterLink to="/dashboard/forms/builder">
                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus">Create new</vs-button> 
            </RouterLink>
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="deleteSelected">Delete</vs-button> 
        </p>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters">Filters</vs-button> 
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
                <div class="vx-col w-full">
                    <label>From</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_from" />
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col w-full">
                    <label>To</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_to" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col w-full">
                    <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
                    <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
                </div>
            </div>
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

        <vs-table multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Id</vs-th>
                <vs-th sort-key="user.id">User ID</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="campaign.name">Campaign</vs-th>
                <vs-th sort-key="network.name">Network</vs-th>
                <vs-th sort-key="product.name">Product</vs-th>
                <vs-th sort-key="traffic_source.name">Traffic Source</vs-th>
                <vs-th sort-key="visits">Visits</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="total_cost">Cost</vs-th>
                <vs-th sort-key="total_commission">Commission</vs-th>
                <vs-th sort-key="profit">Profit</vs-th>
                <vs-th sort-key="roi">ROI</vs-th>
                <vs-th sort-key="cr">CR</vs-th>
                <vs-th sort-key="cpv">CPV</vs-th>
                <vs-th sort-key="epv">EPV</vs-th>
                <vs-th sort-key="cpl">CPL</vs-th>
                <vs-th>Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td :data="data[indextr].user.id">
                        {{data[indextr].user.id}}
                    </vs-td>

                    <vs-td :data="data[indextr].name">
                        {{data[indextr].name}}
                    </vs-td>

                    <vs-td :data="data[indextr].campaign.name">
                        {{data[indextr].campaign.name}}
                    </vs-td>

                    <vs-td :data="data[indextr].network.name">
                        {{data[indextr].network.name}}
                    </vs-td>

                    <vs-td :data="data[indextr].product.name">
                        {{data[indextr].product.name}}
                    </vs-td>

                    <vs-td :data="data[indextr].traffic_source.name">
                        {{data[indextr].traffic_source.name}}
                    </vs-td>

                    <vs-td :data="data[indextr].visits">
                        {{data[indextr].visits}}
                    </vs-td>

                    <vs-td :data="data[indextr].leads">
                        {{data[indextr].leads}}
                    </vs-td>

                    <vs-td :data="data[indextr].total_cost">
                        <span>€ {{data[indextr].total_cost}}</span>
                    </vs-td>

                    <vs-td :data="data[indextr].total_commission">
                        € {{data[indextr].total_commission}}
                    </vs-td>

                    <vs-td :data="data[indextr].profit">
                        <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : 'danger'">€ {{data[indextr].profit}}</vs-chip>
                    </vs-td>

                    <vs-td :data="data[indextr].roi">
                        <vs-chip transparent :color="(data[indextr].roi > 0) ? 'success' : 'danger'">{{data[indextr].roi}} %</vs-chip>
                    </vs-td>

                    <vs-td :data="data[indextr].cr">
                        {{data[indextr].cr}} %
                    </vs-td>

                    <vs-td :data="data[indextr].cpv">
                        € {{data[indextr].cpv}}
                    </vs-td>

                    <vs-td :data="data[indextr].epv">
                        <vs-chip transparent :color="(data[indextr].epv > 0) ? 'success' : 'danger'">€ {{data[indextr].epv}}</vs-chip>
                    </vs-td>

                    <vs-td :data="data[indextr].cpl">
                        € {{data[indextr].cpl}}
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center">
                            <vs-button @click="showFormStats(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-activity"></vs-button>
                            <vs-button @click="showLandingsStats(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-globe"></vs-button>
                            <vs-button @click="showFormCode(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-code"></vs-button>
                            <vs-button @click="goToFormEditor(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-edit"></vs-button>
                            <vs-button @click="deleteObject(data[indextr].id)" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash"></vs-button>
                        </div>
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td>
                    {{total_stats.visits}}
                </vs-td>
                <vs-td>
                    {{total_stats.leads}}
                </vs-td>
                <vs-td>
                    € {{total_stats.total_cost}}
                </vs-td>
                <vs-td>
                    € {{total_stats.total_commission}}</vs-td>
                <vs-td>
                    <vs-chip transparent :color="(total_stats.profit > 0) ? 'success' : 'danger'">€ {{total_stats.profit}}</vs-chip>
                </vs-td>
                <vs-td>
                    <vs-chip transparent :color="(total_stats.roi > 0) ? 'success' : 'danger'">{{total_stats.roi}} %</vs-chip>
                </vs-td>
                <vs-td>
                    {{total_stats.cr}} %
                </vs-td>
                <vs-td>
                    € {{total_stats.cpv}}
                </vs-td>
                <vs-td>
                    <vs-chip transparent :color="(total_stats.epv > 0) ? 'success' : 'danger'">€ {{total_stats.epv}}</vs-chip>
                </vs-td>
                <vs-td>
                    € {{total_stats.cpl}}
                </vs-td>
                <vs-td>
                </vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>

        <!-- Form code popup -->
        <vs-popup title="Form code" :active.sync="showPopup['code']">
            <vs-textarea v-model="formCode" disabled height="10rem" />
            <div class="vx-row mt-6">
                <vs-button class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-copy" v-clipboard:copy="formCode" v-clipboard:success="onCodeCopied">Copy to clipboard</vs-button>
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
                    <span>Campaign</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <v-select :options="campaigns" v-model="newObject['create'].campaign" />
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
                    <span>Campaign</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <v-select :options="campaigns" v-model="newObject['edit'].campaign" />
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

    </vx-card>
</template>

<script>
import vSelect from 'vue-select'

export default {
  components: {
    'v-select': vSelect,
  },
  data () {
    return {
      objects: [],
      selected: [],
      deleted: [],

      maxItems: 20,

      editingObject: -1,

      showPopup: {
        'create': false,
        'edit': false,
        'code': false,
      },
      newObject: {
        'create': {
          name: '',
          campaign: '',
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
          campaign: '',
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
      formCode: '',

      campaigns: [],
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
      filters: {
        date_from: '',
        date_to: '',
      },
      showFilters: false,
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
  },
  methods: {
    getObjectIndexById(id) {
      var index = -1;
      this.objects.forEach((obj, i) => {
        if (obj.id == id) {
          index = i
          return
        }
      })
      return index
    },
    retrieveObjects() {
      this.selected = []
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

        // TODO: to delete
        // HERE
      let data = {
          'filter_date_from': '2020-06-23',
          'filter_date_to':   '2020-06-23',
      }

      // Retrieve forms
      this.$http.get('/api/v1/forms', { headers, params: data })
        .then((response) => {
          this.objects = response.data.data
          this.$vs.loading.close()
          console.log(this.objects)
          this.calculateStats();
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
    deleteSelected() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      this.deleted = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.selected.forEach((form) => {
        this.$http.delete('/api/v1/forms/' + form.id, { headers })
          .then((response) => {
            this.deleted.push(form.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Form(s) deleted',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.selected = []
              this.deleted = []
              this.retrieveObjects()
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
    },
    clearForm(type) {
      this.newObject[type] = {
        name: '',
        campaign: '',
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
      return (obj.name.length > 0
        && obj.campaign != '' && obj.campaign.id >= 1
        && obj.network != '' && obj.network.id >= 1
        && obj.product != '' && obj.product.id >= 1
        && obj.quantity.length > 0
        && obj.traffic_source != '' && obj.traffic_source.id >= 1)
    },
    showFormCode(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Retrieve form code
      this.$http.get('/api/v1/forms/' + id + '/code', { headers: headers })
        .then((response) => {
          this.formCode = response.data.data
          this.$vs.loading.close()
          this.showPopup['code'] = true
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
    createObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.newObject['create'].name,
        campaign_id: this.newObject['create'].campaign.id,
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
          this.retrieveObjects()
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
      let obj = this.objects[this.getObjectIndexById(id)]
      this.newObject['edit'] = {
        name: obj.name,
        campaign: {
          id: obj.campaign.id,
          label: obj.campaign.name,
        },
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
        campaign_id: this.newObject['edit'].campaign.id,
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
          this.retrieveObjects()
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
            text: 'Form deleted',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.retrieveObjects()
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
    showFormStats(id) {
      this.$router.push({name: 'form-stats', params: {form_id: id}})
    },
    showLandingsStats(id) {
      this.$router.push({name: 'landings-stats', params: {form_id: id}})
    },
    goToFormEditor(id) {
      this.$router.push({name: 'form-editor', params: {form_id: id}})
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

      for (var i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        let profit = parseFloat(obj.stats.total_commission) - parseFloat(obj.stats.total_cost)
        let roi = parseFloat((obj.stats.total_commission > 0 && obj.stats.total_cost == 0) ? 9999 : (profit/obj.stats.total_cost)*100)
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

        // Format calculated stats
        profit = profit.toFixed(2)
        roi = roi.toFixed(2)
        cr = cr.toFixed(2)
        cpv = cpv.toFixed(2)
        epv = epv.toFixed(2)
        cpl = cpl.toFixed(2)

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
      let roi = parseFloat((obj.total_commission > 0 && obj.total_cost == 0) ? 9999 : (profit/obj.total_cost)*100)
      let cr = 0.0
      if (obj.leads == 0) cr = 0.0
      else if (obj.visits == 0) cr = 100.0
      else cr = parseFloat((obj.leads/obj.visits)*100.0)
      let cpv = parseFloat((obj.visits == 0) ? obj.total_cost : obj.total_cost/obj.visits)
      let epv = parseFloat((obj.visits == 0) ? obj.total_commission : obj.total_commission/obj.visits)
      let cpl = parseFloat((obj.leads == 0) ? obj.total_cost : obj.total_cost/obj.leads)

      // Format calculated stats
      profit = profit.toFixed(2)
      roi = roi.toFixed(2)
      cr = cr.toFixed(2)
      cpv = cpv.toFixed(2)
      epv = epv.toFixed(2)
      cpl = cpl.toFixed(2)

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
      }
      this.applyFilters()
    },
    applyFilters() {
      this.$vs.loading()

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

      // Retrieve form stats
      this.$http.get('/api/v1/forms', { headers, params: data })
        .then((response) => {
          this.objects = response.data.data
          this.calculateStats()
          this.$vs.loading.close()
          console.log(this.objects)
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
  },
  created() {
    this.retrieveObjects()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve traffic sources
    this.$vs.loading()
    this.$http.get('/api/v1/traffic-sources/', { headers })
      .then((response) => {
        this.traffic_sources = []
        response.data.data.forEach((traffic_source) => {
          this.traffic_sources.push({
            id: traffic_source.id,
            label: traffic_source.name,
          })
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })


    // Retrieve products
    this.$vs.loading()
    this.$http.get('/api/v1/products/', { headers })
      .then((response) => {
        this.products = []
        response.data.data.forEach((product) => {
          this.products.push({
            id: product.id,
            label: product.name,
          })
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

    // Retrieve networks
    this.$vs.loading()
    this.$http.get('/api/v1/networks/', { headers })
      .then((response) => {
        this.networks = []
        response.data.data.forEach((network) => {
          this.networks.push({
            id: network.id,
            label: network.name,
          })
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

    // Retrieve campaigns
    this.$vs.loading()
    this.$http.get('/api/v1/campaigns/', { headers })
      .then((response) => {
        this.campaigns = []
        response.data.data.forEach((campaign) => {
          this.campaigns.push({
            id: campaign.id,
            label: campaign.name,
          })
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

    // Retrieve forms
    this.$vs.loading()
    this.$http.get('/api/v1/forms/', { headers })
      .then((response) => {
        this.forms = []
        response.data.data.forEach((form) => {
          this.forms.push({
            id: form.id,
            label: form.name,
          })
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })
  },
  mounted () {
  }
}

</script>
