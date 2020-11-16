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
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-eye" @click="checkStatusGlobal" v-if="!showInvoicesTableFlag"></vs-button>
            <vs-button class="mr-2" color="success" type="filled" icon-pack="feather" icon="icon-download" @click="downloadInvoices" v-else></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="showInvoicesTableFlag ? 'icon-users' : 'icon-book-open'" @click="showInvoicesTable"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create')"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-database" @click="showForm('multi_create')"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-upload" @click="showForm('import')"></vs-button>
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>
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

        <vs-dropdown v-if="!showInvoicesTableFlag" vs-trigger-click class="cursor-pointer">
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

        <vs-table v-if="!showInvoicesTableFlag" stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

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
                <vs-th>Actions</vs-th>
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
                        {{data[indextr].name}}
                    </vs-td>

                    <vs-td :data="data[indextr].product.name">
                        {{data[indextr].product.name}}
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center" v-if="data[indextr].info != null && data[indextr].info.hasOwnProperty('bm')">
                            <vs-chip v-for="(card, index) in getCardsArray(data[indextr].info['bm'])" :key="index">
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
                        <div class="flex items-center" v-if="data[indextr].info != null && data[indextr].info.hasOwnProperty('bm')">
                            <vx-tooltip :text="bm.name + ' (' + bm.id + ')'" v-for="(bm, index) in data[indextr].info['bm']" :key="index">
                                <vs-avatar size="24px" @click="showBmInfo(indextr, index)" icon-pack="feather" icon="icon-briefcase" :color="(bm.active > 0) ? 'success' : ((bm.warning > 0) ? 'warning' : 'danger')" />
                            </vx-tooltip>
                        </div>
                    </vs-td>

                    <vs-td>
                        <div v-if="typeof data[indextr].amount_spent != 'undefined'">
                            {{formatFbCurrency(data[indextr].amount_spent)}}
                        </div>
                    </vs-td>

                    <vs-td>
                        <div v-if="typeof data[indextr].leads != 'undefined'">
                            {{data[indextr].leads}}
                        </div>
                    </vs-td>

                    <vs-td>
                        <div v-if="typeof data[indextr].cpl != 'undefined'">
                            {{data[indextr].cpl.toFixed(2)}}
                        </div>
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center">
                            <vs-button @click="checkStatus(indextr)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-eye"></vs-button>
                            <vs-button @click="showAds(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-activity"></vs-button>
                            <vs-button @click="editForm(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-edit"></vs-button>
                            <vs-button @click="deleteObject(data[indextr].id)" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash"></vs-button>
                        </div>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <vs-table v-else stripe multiple v-model="selectedInvoices" search :data="invoices" ref="invoicesTable">
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
                    <vs-button @click="checkStatus(bmInfoObjIndex)" class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw"></vs-button>
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

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import JSZip from 'jszip'
import { generateInvoicePDF } from '@assets/utils/fb-invoice'

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
  data () {
    return {
      objects: [],
      selected: [],
      deleted: [],

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

      showPopup: {
        'create': false,
        'multi_create': false,
        'edit': false,
        'import': false,
        'bm_info': false,
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
      checked: [],

      showInvoicesTableFlag: false,
      invoices: [],
      invoicesTotalStats: {
        amount: 0,
      },
      selectedInvoices: [],
      pdfDocs: [],

      bmInfoObj: {},
      bmInfoObjIndex: 0,
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
    getObjectIndexById(id) {
      let index = -1;
      this.objects.forEach((obj, i) => {
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
      let obj = this.objects[this.getObjectIndexById(id)]
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
    deleteObject(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$http.delete('/api/3rd/fb-accounts/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Tag deleted',
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
    checkStatus(objIndex) {
      this.invoices = []
      this.invoicesTotalStats = {
        amount: 0,
      }

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
            }
          })

          // Get FB ad accounts
          let since = this.filters.date_from
          let until = this.filters.date_to
          let sinceDate = new Date(since + ' 00:00:00')
          let untilDate = new Date(until + ' 23:59:59')
          let sinceTimestamp = parseInt(sinceDate.valueOf() / 1000)
          let untilTimestamp = parseInt(untilDate.valueOf() / 1000)
          let fields = 'account_id,name,business,account_status,funding_source_details,amount_spent'
          fields += ',transactions.time_range({"since":"' + since + '", "until":"' + until +'"})'
          fields += ',ads.time_range({"since":"' + since + '", "until":"' + until +'"}){result,spent}'
          this.$http.get('https://graph.facebook.com/v5.0/me/adaccounts?access_token=' + obj.token + '&fields=' + fields)
            .then((response) => {
              let mainAdAccountActive = true
              let totalAmountSpent = 0
              let totalResult = 0
              let transactions = []
              response.data.data.forEach((adAccount) => {
                if (adAccount.hasOwnProperty('business') && adAccount.business.hasOwnProperty('id') &&
                    (adAccount.business.id in businesses)) {
                  businesses[adAccount.business.id].total += 1
                  if (adAccount.account_status == 1) {
                    businesses[adAccount.business.id].active += 1
                  }
                }
                if (adAccount.hasOwnProperty('funding_source_details') && adAccount.funding_source_details.hasOwnProperty('display_string') &&
                  (adAccount.business.id in businesses)) {
                  if (!businesses[adAccount.business.id].funding_sources.includes(adAccount.funding_source_details.display_string)) {
                    businesses[adAccount.business.id].funding_sources.push(adAccount.funding_source_details.display_string)
                  }
                }
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
                      if ((adAccount.business.id in businesses) && businesses[adAccount.business.id].funding_sources.length > 0) {
                        fSource = businesses[adAccount.business.id].funding_sources[0]
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
              })

              this.dynamicStats["id" + obj.id] = {
                amount_spent: 0,
                leads: 0,
                cpl: 0,
                transactions: [],
              }

              this.dynamicStats["id" + obj.id].amount_spent = totalAmountSpent
              this.dynamicStats["id" + obj.id].leads = totalResult
              this.dynamicStats["id" + obj.id].cpl = (totalResult > 0) ? ((totalAmountSpent/100)/totalResult) : (totalAmountSpent)
              this.dynamicStats["id" + obj.id].transactions = transactions

              let info = obj.info
              if (info == null) {
                info = {}
              }
              info["bm"] = []
              for (const [key, value] of Object.entries(businesses)) {
                info["bm"].push(value)
              }

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

      if (this.selected.length == 0) return

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
              }
            })

            // Get FB ad accounts
            let since = this.filters.date_from
            let until = this.filters.date_to
            let sinceDate = new Date(since + ' 00:00:00')
            let untilDate = new Date(until + ' 23:59:59')
            let sinceTimestamp = parseInt(sinceDate.valueOf() / 1000)
            let untilTimestamp = parseInt(untilDate.valueOf() / 1000)
            let fields = 'account_id,name,business,account_status,funding_source_details,amount_spent'
            fields += ',transactions.time_range({"since":"' + since + '", "until":"' + until +'"})'
            fields += ',ads.time_range({"since":"' + since + '", "until":"' + until +'"}){result,spent}'
            this.$http.get('https://graph.facebook.com/v5.0/me/adaccounts?access_token=' + obj.token + '&fields=' + fields)
              .then((response) => {
                let mainAdAccountActive = true
                let totalAmountSpent = 0
                let totalResult = 0
                let transactions = []
                response.data.data.forEach((adAccount) => {
                  if (adAccount.hasOwnProperty('business') && adAccount.business.hasOwnProperty('id') &&
                      (adAccount.business.id in businesses)) {
                    businesses[adAccount.business.id].total += 1
                    if (adAccount.account_status == 1) {
                      businesses[adAccount.business.id].active += 1
                    }
                  }
                  if (adAccount.hasOwnProperty('funding_source_details') && adAccount.funding_source_details.hasOwnProperty('display_string') &&
                    (adAccount.business.id in businesses)) {
                    if (!businesses[adAccount.business.id].funding_sources.includes(adAccount.funding_source_details.display_string)) {
                      businesses[adAccount.business.id].funding_sources.push(adAccount.funding_source_details.display_string)
                    }
                  }
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
                        if ((adAccount.business.id in businesses) && businesses[adAccount.business.id].funding_sources.length > 0) {
                          fSource = businesses[adAccount.business.id].funding_sources[0]
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
                })

                this.dynamicStats["id" + obj.id] = {
                  amount_spent: 0,
                  leads: 0,
                  cpl: 0,
                  transactions: [],
                }

                this.dynamicStats["id" + obj.id].amount_spent = totalAmountSpent
                this.dynamicStats["id" + obj.id].leads = totalResult
                this.dynamicStats["id" + obj.id].cpl = (totalResult > 0) ? ((totalAmountSpent/100)/totalResult) : (totalAmountSpent)
                this.dynamicStats["id" + obj.id].transactions = transactions

                let info = obj.info
                if (info == null) {
                  info = {}
                }
                info["bm"] = []
                for (const [key, value] of Object.entries(businesses)) {
                  info["bm"].push(value)
                }

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
                    this.deleted.push(obj.id)
                    if (this.deleted.length == this.selected.length) {
                      this.$vs.loading.close()
                    }
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
                this.deleted.push(obj.id)
                if (this.deleted.length == this.selected.length) {
                  this.$vs.loading.close()
                }
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
    formatFbCurrency(integerNumber) {
      return (integerNumber / 100).toFixed(2)
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
    getCardsArray(bm) {
      let cardsArray = []
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
    showInvoicesTable() {
      if (!this.showInvoicesTableFlag) {
        this.showInvoicesTableFlag = true
      } else {
        this.showInvoicesTableFlag = false
      }
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
        let invoiceDate = this.formatDate(d) + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds()

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
          'amount': invoice.amount,
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
    },
    showBmInfo(index, bmIndex) {
      this.bmInfoObjIndex = index
      let bm = this.objects[index].info['bm'][bmIndex]

      // TODO: to delete
      console.log("bm.id", bm.id)

      // TODO: HERE
      this.bmInfoObj = bm
      this.showPopup['bm_info'] = true
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
