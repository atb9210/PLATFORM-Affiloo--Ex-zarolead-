<template>
    <vx-card title="Leads" code-toggler>

        <p class="flex items-center mb-4">
            <vx-tooltip text="Filters" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Create a new lead" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="changeView('order_builder')"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Copy phone numbers" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-copy" :disabled="selectedLeads.length <= 0" @click="copyPhoneNumbers(selectedLeads)"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Copy IDs" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-copy" :disabled="selectedLeads.length <= 0" @click="copyIds(selectedLeads)"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Copy selected leads CSV" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-file-text" @click="exportLeadsAsCSV(selectedLeads)" :disabled="selectedLeads.length <= 0"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Send message (bulk)" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-message-circle" @click="openSendMessagePopup(selectedLeads)" :disabled="!(selectedLeads.length > 0 && selectedLeads.length <= 10)"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Change status to unprocessed" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="newStatus = 'unprocessed';confirmUpdateStatusBulk(selectedLeads)" :disabled="selectedLeads.length <= 0"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Delete selected leads" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Back" position="top">
                <vs-button v-if="view == 'order_editor' || view == 'order_builder'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="changeView('leads')"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Open Chat (new tab)" position="top">
                <vs-button v-if="view == 'order_editor'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-message-circle" @click="openLeadChat(order)"></vs-button>
            </vx-tooltip>

            <vx-tooltip text="All leads" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-globe" @click="setFiltersStatuses([])"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Only confirmed" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-check-circle" @click="setFiltersStatuses(['confirmed'])"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Only carts" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-shopping-cart" @click="setFiltersStatuses(['to do','abandoned'])"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Only waiting payment" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-clock" @click="setFiltersStatuses(['waiting payment'])"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="No answer and to recall" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-phone" @click="setFiltersStatuses(['to recall','no answer'], true)"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Only unprocessed" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="success" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="setFiltersStatuses(['unprocessed'])"></vs-button>
            </vx-tooltip>
            <vx-tooltip text="Only complaint" position="top">
                <vs-button v-if="view == 'leads'" class="mr-2" color="warning" type="filled" icon-pack="feather" icon="icon-alert-triangle" @click="setFiltersStatuses(['complaint'])"></vs-button>
            </vx-tooltip>

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
                <div class="vx-col w-1/2">
                    <label>Product</label>
                    <my-select v-model="filters.product" :options="dropinProductOptions" :autocomplete="true" class="w-full select-large" />
                </div>
                <div class="vx-col w-1/2">
                    <label>Assoc. Product</label>
                    <my-select v-model="filters.product_id" :options="productOptions" :autocomplete="true" class="w-full select-large" />
                </div>
                <div class="vx-col w-1/2">
                    <label>Status</label>
                    <my-select v-model="filters.statuses" :options="leadStatuses" :autocomplete="true" multiple class="w-full select-large" />
                </div>
                <div class="vx-col w-1/2">
                    <label>Order Status</label>
                    <my-select v-model="filters.order_status" :options="orderStatusOptions" :autocomplete="true" class="w-full select-large" />
                </div>
            </div>

            <div class="vx-row mt-6">
                <div class="vx-col w-full">
                    <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
                    <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
                </div>
            </div>
        </div>

        <div v-if="view == 'leads'">

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

            <vue-context ref="lead_context_menu" v-slot="{ data }">
                <li>
                  <a href="#" @click="$refs.lead_context_menu.close();changeView('order_editor', data)" class="flex items-center text-sm">
                    <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Edit</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.lead_context_menu.close();openSendMessagePopup([data])"class="flex items-center text-sm">
                    <feather-icon icon="MessageCircleIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Send message (Whatsapp)</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.lead_context_menu.close();showPaymentLink(data)"class="flex items-center text-sm">
                    <feather-icon icon="DollarSignIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Show payment link</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.lead_context_menu.close();deleteLead(data)" class="flex items-center text-sm">
                    <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Delete</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.lead_context_menu.close()" class="flex items-center text-sm">
                    <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Close</span>
                  </a>
                </li>
            </vue-context>

            <vue-context ref="phone_context_menu" v-slot="{ data }">
                <li>
                  <a href="#" @click="$refs.phone_context_menu.close();openLeadChat(data)" class="flex items-center text-sm">
                    <feather-icon icon="MessageCircleIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Whatsapp Chat</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.phone_context_menu.close()" class="flex items-center text-sm">
                    <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Close</span>
                  </a>
                </li>
            </vue-context>

            <!-- Leads table -->
            <vs-table stripe multiple v-model="selectedLeads" :max-items="maxItems" pagination search :data="leads" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="id">ID</vs-th>
                    <vs-th sort-key="order_id">Order ID</vs-th>
                    <vs-th sort-key="created_at">Date</vs-th>
                    <vs-th sort-key="full_name">Name</vs-th>
                    <vs-th sort-key="phone">Phone</vs-th>
                    <vs-th sort-key="product_id">Ass. Prod.</vs-th>
                    <vs-th sort-key="product_name">Product</vs-th>
                    <vs-th sort-key="gateway">Method</vs-th>
                    <vs-th sort-key="price">Price</vs-th>
                    <vs-th sort-key="quantity">Qty</vs-th>
                    <vs-th sort-key="total">Total</vs-th>
                    <vs-th>Items</vs-th>
                    <vs-th sort-key="contact">Contact</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <vs-th sort-key="paid">Paid</vs-th>
                    <vs-th sort-key="notes">Notes</vs-th>
                    <vs-th sort-key="completed_at">Completed Date</vs-th>
                    <!--<vs-th>Actions</vs-th>-->
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="item" :key="'tableRow' + indextr" v-for="(item, indextr) in data">

                        <vs-td class="column-no-padding" :data="item.id">
                            {{item.id}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.order_id">
                            <fast-editable-value
                                v-model="newValue"
                                @onEdit="newValue = (item.order_id != '' && item.order_id != null) ? item.order_id : ''"
                                @onUpdate="updateOrderId(data[indextr])">
                                {{ (item.order_id != '' && item.order_id != null) ? item.order_id : 'none' }}
                            </fast-editable-value>
                        </vs-td>

                        <vs-td v-if="!showRecallDate" class="column-no-padding" :data="item.created_at">
                            {{formatDateTime(new Date(item.created_at))}}
                        </vs-td>
                        <vs-td v-else class="column-no-padding" :data="item.recall_at">
                            <vs-chip :color="(item.recall_at != '') ? 'warning' : ''">
                               <span @dblclick="showDateTimeInfoPopup(item, 'recall_at')">
                                   {{(item.recall_at != '') ? ('Recall: ' + formatDateTime(new Date(item.created_at))) : 'recall date not set'}}
                               </span>
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.full_name">
                            <div @contextmenu.prevent="$refs.lead_context_menu.open($event, item)">
                                <vx-tooltip :title="item.full_name" :text="hasAddressInfo(item) ? getAddress(item) : 'Address undefined'" position="left">
                                    <vs-chip>
                                        {{item.full_name}}
                                    </vs-chip>
                                </vx-tooltip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding">
                            <div v-if="item.hasOwnProperty('phone') && item.phone != ''" @contextmenu.prevent="$refs.phone_context_menu.open($event, item)" @dblclick="whatsappMessage(item)">
                                <vx-tooltip :title="Last message" :text="(item.info.whatsapp.last_message_body) ? item.info.whatsapp.last_message_body : ''" position="left">
                                  <vs-chip :color="(item.has_new_message) ? 'warning' : ''">
                                      <vs-avatar icon-pack="feather" icon="icon-message-circle" />
                                      {{ item.phone }}
                                  </vs-chip>
                                </vx-tooltip>
                            </div>
                            <!-- TODO: Old code -->
                            <!--
                            <vs-chip v-if="item.hasOwnProperty('phone') && item.phone != ''">
                                <span @click="copyToClipboard(item.phone)" @dblclick="whatsappMessage(item)">
                                    {{item.phone}}
                                </span>
                            </vs-chip>
                            -->
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.product_id">
                            <vs-chip v-if="item.product !== null">
                                {{item.product.name}}
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.product_name">
                            <vs-chip>
                                {{item.product_name}}
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.gateway">
                            {{(item.gateway != '') ? gatewayShortNames[item.gateway] : ''}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.price">
                            <fast-editable-value
                                v-model="newValue"
                                @onEdit="newValue = data[indextr].price.toFixed(2)"
                                @onUpdate="updatePrice(data[indextr])">
                                  € {{ item.price.toFixed(2) }}
                            </fast-editable-value>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.quantity">
                            <fast-editable-value
                                v-model="newValue"
                                @onEdit="newValue = data[indextr].quantity"
                                @onUpdate="updateQuantity(data[indextr])">
                                  {{ item.quantity }}
                            </fast-editable-value>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.total">
                            <fast-editable-value
                                v-model="newValue"
                                @onEdit="newValue = data[indextr].total.toFixed(2)"
                                @onUpdate="updateTotal(data[indextr])">
                                  € {{ item.total.toFixed(2) }}
                            </fast-editable-value>
                        </vs-td>

                        <vs-td class="column-no-padding">
                            <vx-tooltip v-if="item.info !== null && item.info.hasOwnProperty('products') && item.info.products !== null && item.info.products.length > 0" title="Items" :text="getProductsList(item)">
                                <vs-chip>
                                    {{item.num_items}} items
                                </vs-chip>
                            </vx-tooltip>
                            <vs-chip v-else>
                                0 items
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.contact">
                            <vs-checkbox v-model="data[indextr].contact" @change="updateContact(data[indextr])">{{item.call_count}}</vs-checkbox>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.status">
                            <fast-editable-value-select
                                class="font-bold"
                                :options="leadStatusOptions"
                                v-model="newStatus"
                                @onEdit="newStatus = data[indextr].status"
                                @onUpdate="confirmUpdateStatus(data[indextr])">
                                  <vs-chip :color="leadStatusColor[data[indextr].status]">
                                      {{item.status}}
                                  </vs-chip>
                            </fast-editable-value-select>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.paid">
                            <vs-avatar class="icon-paid" icon-pack="feather" icon="icon-dollar-sign" :color="(item.paid) ? 'success' : '#878787'"  @dblclick="confirmTogglePaid(item)" />
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.notes">
                            <!-- TODO: Old code -->
                            <!--
                            <vx-tooltip :text="item.notes" position="top">
                                <vs-avatar :color="(item.notes === null ||item.notes === '') ? 'primary' : 'warning'" size="24px" @click="showNotes(item)" icon-pack="feather" icon="icon-message-square" />
                            </vx-tooltip>
                            -->
                            <vs-chip :color="(item.notes === null || item.notes == '') ? '' : 'warning'">
                                <span @dblclick="showNotes(item)">
                                    {{((item.notes === null || item.notes == '') ? 'Create note' : item.notes).substring(0, 20)}}{{((item.notes !== null && item.notes.length > 20) ? '...' : '')}}
                                </span>
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.completed_at">
                            <vs-chip :color="(item.completed_at != '') ? 'success' : ''">
                               <span @dblclick="showDateTimeInfoPopup(item, 'completed_at')">
                                   {{(item.completed_at != '') ? formatDateTime(new Date(item.completed_at)) : 'not completed'}}
                               </span>
                            </vs-chip>
                        </vs-td>

                        <!--
                        <vs-td class="column-no-padding">
                            <div class="flex items-center">
                                <vs-dropdown class="mr-2 dropdown font-bold">
                                    <vs-chip color="primary">
                                        Actions
                                        <vs-icon icon-pack="feather" icon="icon-chevron-down"></vs-icon>
                                    </vs-chip>

                                    <vs-dropdown-menu class="dropdown-menu">
                                        <vs-dropdown-item @click="changeView('order_editor', item)">
                                            <vs-icon icon-pack="feather" icon="icon-edit"></vs-icon>
                                            Edit
                                        </vs-dropdown-item>
                                        <vs-dropdown-item @click="showNotes(item)">
                                            <vs-icon icon-pack="feather" icon="icon-message-square"></vs-icon>
                                            Show Notes
                                        </vs-dropdown-item>
                                        <vs-dropdown-item @click="showPaymentLink(item)">
                                            <vs-icon icon-pack="feather" icon="icon-dollar-sign"></vs-icon>
                                            Show Payment Link
                                        </vs-dropdown-item>
                                        <vs-dropdown-item @click="whatsappMessage(item)">
                                            <vs-icon icon-pack="feather" icon="icon-message-circle"></vs-icon>
                                            Send Whatsapp Message
                                        </vs-dropdown-item>
                                    </vs-dropdown-menu>
                                </vs-dropdown>
                            </div>
                        </vs-td>
                        -->
                        
                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{data.length}}</vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">€ {{totalStats.price.toFixed(2)}}</vs-td>
                    <vs-td class="column-no-padding">{{totalStats.quantity}}</vs-td>
                    <vs-td class="column-no-padding">€ {{totalStats.total.toFixed(2)}}</vs-td>
                    <vs-td class="column-no-padding">{{totalStats.items}} items</vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <!--<vs-td class="column-no-padding"></vs-td>-->

                </template>
            </vs-table>

        </div>

        <!-- Order editor -->
        <div v-if="view == 'order_editor'">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row w-full">
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <h2>Contact data</h2>
                    </div>
                    <div class="vx-col mb-3 md:w-1/4 w-full">
                    </div>
                    <div class="vx-col mb-3 md:w-1/4 w-full">
                        <fast-editable-value-select
                            class="font-bold"
                            :options="leadStatuses"
                            v-model="newStatus"
                            @onEdit="newStatus = order.status"
                            @onUpdate="updateStatusOrderEditor()">
                              <vs-chip :color="leadStatusColor[order.status]">
                                  {{order.status}}
                              </vs-chip>
                        </fast-editable-value-select>
                    </div>
                </div>
                <div class="vx-row w-full">
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.first_name" placeholder="First Name" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.last_name" placeholder="Last Name" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.phone" placeholder="Phone" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.email" placeholder="Email" />
                    </div>
                </div>

                <hr class="mt-3 mb-3">

                <h2>Shipping</h2>
                <div class="vx-row w-full">
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.first_name" placeholder="First Name" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.last_name" placeholder="Last Name" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.phone" placeholder="Phone" />
                    </div>
                    <div class="vx-col mb-6 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.email" placeholder="Email" />
                    </div>
                    <div class="vx-col mb-3 w-full">
                        <LocationInput v-model="order.location" @onEdit="findAddress" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.street_address" placeholder="Street address" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.city" placeholder="City" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.postcode" placeholder="Post code" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.province" placeholder="Province" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.state" placeholder="State" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.country" placeholder="Country" />
                    </div>
                    <div class="vx-col mt-3 mb-3 w-full">
                        <vs-input icon-before="true" icon-pack="feather" icon="icon-message-square" class="w-full" type="text" v-model="order.shipping.notes" placeholder="Notes" />
                    </div>
                </div>

                <hr class="mt-3 mb-3">

                <h2>Payment method</h2>
                <div class="vx-row w-full mb-3 font-bold">
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <my-select v-model="order.gateway" :options="gateways" class="w-full select-large" :autocomplete="true" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-button v-if="order.gateway === 'online' && order.phone != ''" class="w-full" color="primary" type="filled" icon-pack="feather" icon="icon-message-square" @click="confirmSendPaymentLinkViaMessage">Send payment link via Whatsapp</vs-button>
                    </div>
                </div>

                <hr class="mt-3 mb-3">

                <h2>Products</h2>
                <div class="vx-row w-full mb-3 font-bold">
                    <div class="vx-col w-full md:w-1/12">
                        <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showAddProductPopup"></vs-button>
                    </div>
                    <div class="vx-col w-full md:w-1/3">
                        Name
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        Quantity
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        Unit Price
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        Total Price
                    </div>
                    <div class="vx-col w-full md:w-1/12">
                        Upsell
                    </div>
                </div>

                <div class="vx-row w-full mb-3" v-for="(product, pIndex) in order.products" :key="'reviewProductOption' + pIndex">
                    <div class="vx-col w-full md:w-1/12">
                        <vs-button color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="removeProduct(pIndex)"></vs-button>
                    </div>
                    <div class="vx-col w-full md:w-1/3">
                        {{product.name}}
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        <fast-editable-value v-model="product.quantity" @onUpdate="refreshOrderReviewTotal()">
                            x{{product.quantity}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        <fast-editable-value v-model="product.price" @onUpdate="refreshOrderReviewTotal()">
                            € {{product.price}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        € {{(product.total).toFixed(2)}}
                    </div>
                    <div class="vx-col w-full md:w-1/12">
                        <vs-checkbox v-model="order.products[pIndex].upsell" />
                    </div>
                </div>

                <div class="vx-row w-full mb-3" v-for="(acc, aIndex) in order.accessories" :key="'accessoyOption' + aIndex">
                    <div class="vx-col w-full md:w-1/12">
                        <vs-checkbox v-model="order.accessories[aIndex].enabled" @change="refreshOrderReviewTotal()" />
                    </div>
                    <div class="vx-col w-full md:w-1/3">
                        {{acc.name}}
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        <fast-editable-value v-model="order.accessories[aIndex].quantity" @onUpdate="refreshOrderReviewTotal()">
                            x{{order.accessories[aIndex].quantity}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        <fast-editable-value v-model="order.accessories[aIndex].price" @onUpdate="refreshOrderReviewTotal()">
                            € {{order.accessories[aIndex].price.toFixed(2)}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        € {{order.accessories[aIndex].total.toFixed(2)}}
                    </div>
                    <div class="vx-col w-full md:w-1/12">
                    </div>
                </div>

                <!--
                <div class="vx-row w-full mb-3">
                    <div class="vx-col w-full md:w-1/6">
                        Insurance
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        x1
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        <fast-editable-value v-model="order.insurance" @onUpdate="refreshOrderReviewTotal()">
                            € {{order.insurance}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        <fast-editable-value v-model="order.insurance" @onUpdate="refreshOrderReviewTotal()">
                            € {{order.insurance}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                    </div>
                </div>
                -->

                <div class="vx-row w-full mt-3 mb-3 font-bold">
                    <div class="vx-col w-full md:w-1/12">
                    </div>
                    <div class="vx-col w-full md:w-1/3">
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        ----------------------<br>
                        <fast-editable-value
                            v-model="order.newTotal"
                            @onEdit="order.newTotal = order.total"
                            @onUpdate="updateOrderTotal()">
                              € {{order.total}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/12">
                    </div>
                </div>

                <div class="vx-row w-full mt-3 mb-3 font-bold items-center flex">
                    <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-save" @click="updateLeadInfo(order)" :disabled="!canUpdateLeadInfo(order)">Save</vs-button>
                </div>

            </div>
        </div>

        <!-- Order builder -->
        <div v-if="view == 'order_builder'">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row w-full">
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <h2>Contact data</h2>
                    </div>
                    <div class="vx-col mb-3 md:w-1/4 w-full">
                    </div>
                    <div class="vx-col mb-3 md:w-1/4 w-full">
                        <fast-editable-value-select
                            class="font-bold"
                            :options="leadStatuses"
                            v-model="newStatus"
                            @onEdit="newStatus = order.status"
                            @onUpdate="updateStatusOrderBuilder()">
                              <vs-chip :color="leadStatusColor[order.status]">
                                  {{order.status}}
                              </vs-chip>
                        </fast-editable-value-select>
                    </div>
                </div>
                <div class="vx-row w-full">
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.first_name" placeholder="First Name" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.last_name" placeholder="Last Name" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.phone" placeholder="Phone" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.email" placeholder="Email" />
                    </div>
                </div>

                <hr class="mt-3 mb-3">

                <h2>Shipping</h2>
                <div class="vx-row w-full">
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.first_name" placeholder="First Name" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.last_name" placeholder="Last Name" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.phone" placeholder="Phone" />
                    </div>
                    <div class="vx-col mb-6 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.email" placeholder="Email" />
                    </div>
                    <div class="vx-col mb-3 w-full">
                        <LocationInput v-model="order.location" @onEdit="findAddress" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.street_address" placeholder="Street address" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.city" placeholder="City" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.postcode" placeholder="Post code" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.province" placeholder="Province" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.state" placeholder="State" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-input class="w-full" type="text" v-model="order.shipping.country" placeholder="Country" />
                    </div>
                    <div class="vx-col mt-3 mb-3 w-full">
                        <vs-input icon-before="true" icon-pack="feather" icon="icon-message-square" class="w-full" type="text" v-model="order.shipping.notes" placeholder="Notes" />
                    </div>
                </div>

                <hr class="mt-3 mb-3">

                <h2>Payment method</h2>
                <div class="vx-row w-full mb-3 font-bold">
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <my-select v-model="order.gateway" :options="gateways" class="w-full select-large" :autocomplete="true" />
                    </div>
                    <div class="vx-col mb-3 md:w-1/2 w-full">
                        <vs-button v-if="order.gateway === 'online' && order.phone != ''" class="w-full" color="primary" type="filled" icon-pack="feather" icon="icon-message-square" @click="confirmSendPaymentLinkViaMessage">Send payment link via Whatsapp</vs-button>
                    </div>
                </div>

                <hr class="mt-3 mb-3">

                <h2>Products</h2>
                <div class="vx-row w-full mb-3 font-bold">
                    <div class="vx-col w-full md:w-1/12">
                        <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showAddProductPopup"></vs-button>
                    </div>
                    <div class="vx-col w-full md:w-1/3">
                        Name
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        Quantity
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        Unit Price
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        Total Price
                    </div>
                    <div class="vx-col w-full md:w-1/12">
                        Upsell
                    </div>
                </div>

                <div class="vx-row w-full mb-3" v-for="(product, pIndex) in order.products" :key="'reviewProductOption' + pIndex">
                    <div class="vx-col w-full md:w-1/12">
                        <vs-button color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="removeProduct(pIndex)"></vs-button>
                    </div>
                    <div class="vx-col w-full md:w-1/3">
                        {{product.name}}
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        <fast-editable-value v-model="product.quantity" @onUpdate="refreshOrderReviewTotal()">
                            x{{product.quantity}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        <fast-editable-value v-model="product.price" @onUpdate="refreshOrderReviewTotal()">
                            € {{product.price}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        € {{(product.total).toFixed(2)}}
                    </div>
                    <div class="vx-col w-full md:w-1/12">
                        <vs-checkbox v-model="order.products[pIndex].upsell" />
                    </div>
                </div>

                <div class="vx-row w-full mb-3" v-for="(acc, aIndex) in order.accessories" :key="'accessoyOption' + aIndex">
                    <div class="vx-col w-full md:w-1/12">
                        <vs-checkbox v-model="order.accessories[aIndex].enabled" @change="refreshOrderReviewTotal()" />
                    </div>
                    <div class="vx-col w-full md:w-1/3">
                        {{acc.name}}
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        <fast-editable-value v-model="order.accessories[aIndex].quantity" @onUpdate="refreshOrderReviewTotal()">
                            x{{order.accessories[aIndex].quantity}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        <fast-editable-value v-model="order.accessories[aIndex].price" @onUpdate="refreshOrderReviewTotal()">
                            € {{order.accessories[aIndex].price.toFixed(2)}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        € {{order.accessories[aIndex].total.toFixed(2)}}
                    </div>
                    <div class="vx-col w-full md:w-1/12">
                    </div>
                </div>

                <!--
                <div class="vx-row w-full mb-3">
                    <div class="vx-col w-full md:w-1/6">
                        Insurance
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        x1
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        <fast-editable-value v-model="order.insurance" @onUpdate="refreshOrderReviewTotal()">
                            € {{order.insurance}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        <fast-editable-value v-model="order.insurance" @onUpdate="refreshOrderReviewTotal()">
                            € {{order.insurance}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                    </div>
                </div>
                -->

                <div class="vx-row w-full mt-3 mb-3 font-bold">
                    <div class="vx-col w-full md:w-1/12">
                    </div>
                    <div class="vx-col w-full md:w-1/3">
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                    </div>
                    <div class="vx-col w-full md:w-1/6">
                        ----------------------<br>
                        <fast-editable-value
                            v-model="order.newTotal"
                            @onEdit="order.newTotal = order.total"
                            @onUpdate="updateOrderTotal()">
                              € {{order.total}}
                        </fast-editable-value>
                    </div>
                    <div class="vx-col w-full md:w-1/12">
                    </div>
                </div>

                <div class="vx-row w-full mt-3 mb-3 font-bold items-center flex">
                    <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-save" @click="createLeadFromInfo(order)" :disabled="!canCreateLeadFromInfo(order)">Save</vs-button>
                </div>

            </div>
        </div>
        
        <!-- Notes popup -->
        <vs-popup title="Notes" :active.sync="showPopup['notes']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3">
                    <div class="vx-col w-full">
                        <h3>Notes</h3>
                        <vs-textarea v-model="lead.notes"></vs-textarea>
                        <div class="flex" style="float: right;">
                            <vs-button color="success" type="filled" icon-pack="feather" icon="icon-check" @click="updateNotes(lead)"></vs-button>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>

        <!-- Add product popup -->
        <vs-popup fullscreen title="Add products" :active.sync="showPopup['add_product']">

            <div v-if="productView == 'normal'" class="w-full pr-2 pl-2">

                <vs-table stripe search :data="dropinProducts">

                    <template slot="thead">
                        <vs-th></vs-th>
                        <vs-th sort-key="full_name">Name</vs-th>
                        <vs-th sort-key="variations">Variation</vs-th>
                        <vs-th sort-key="sku">SKU</vs-th>
                        <vs-th sort-key="category">Category</vs-th>
                        <vs-th sort-key="sale_price">Suggested Price</vs-th>
                        <vs-th sort-key="dropshipping_price">Dropshipping Price</vs-th>
                        <vs-th sort-key="quantity">Qty</vs-th>
                        <vs-th>Actions</vs-th>
                    </template>

                    <template slot-scope="{data}">
                        <vs-tr :data="item" :key="'addProductsRow' + indextr" v-for="(item, indextr) in data">

                            <vs-td class="column-no-padding">
                                <vs-checkbox :value="hasProduct(item)" @change="toggleProduct(item)" />
                            </vs-td>

                            <vs-td class="column-no-padding" :data="item.name">
                                {{item.name}}
                            </vs-td>

                            <vs-td class="column-no-padding" :data="item.variations">
                                {{item.variations}}
                            </vs-td>

                            <vs-td class="column-no-padding" :data="item.sku">
                                {{item.sku}}
                            </vs-td>

                            <vs-td class="column-no-padding" :data="item.category">
                                {{item.category}}
                            </vs-td>

                            <vs-td class="column-no-padding" :data="item.sale_price">
                                € {{ item.sale_price.toFixed(2) }}
                            </vs-td>

                            <vs-td class="column-no-padding" :data="item.dropshipping_price">
                                € {{ item.dropshipping_price.toFixed(2) }}
                            </vs-td>

                            <vs-td class="column-no-padding" :data="item.quantity">
                                {{item.quantity}}
                            </vs-td>

                            <vs-td class="column-no-padding">
                                <vs-avatar color="primary" size="24px" @click="showProductInfo(item)" icon-pack="feather" icon="icon-info" />
                            </vs-td>

                        </vs-tr>

                    </template>
                </vs-table>

                <!-- TODO: Old code -->
                <!--
                <div class="vx-row mb-3 w-full font-bold">
                    <div class="vx-col md:w-1/6 w-full">
                    </div>
                    <div class="vx-col md:w-1/4 w-full">
                        Name
                    </div>
                    <div class="vx-col md:w-1/6 w-full">
                        Q.ty
                    </div>
                    <div class="vx-col md:w-1/4 w-full">
                        SKU
                    </div>
                    <div class="vx-col md:w-1/6 w-full">
                    </div>
                </div>
                <div class="vx-row mb-3 w-full text-sm" v-for="(product, pIndex) in dropinProducts" :key="'addProductOption' + pIndex">
                    <div class="vx-col md:w-1/6 w-full">
                        <vs-checkbox :value="hasProduct(product)" @change="toggleProduct(product)" />
                    </div>
                    <div class="vx-col md:w-1/4 w-full">
                        {{product.name}} {{product.variations}}
                    </div>
                    <div class="vx-col md:w-1/6 w-full">
                        {{product.quantity}}
                    </div>
                    <div class="vx-col md:w-1/4 w-full">
                        {{product.sku}}
                    </div>
                    <div class="vx-col md:w-1/6 w-full">
                        <vs-avatar color="primary" size="24px" @click="showProductInfo(product)" icon-pack="feather" icon="icon-info" />
                    </div>
                </div>
                -->
            </div>

            <div v-else-if="productView == 'product_info'">
                <div class="vx-row mb-6">
                    <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="productView = 'normal'"></vs-button>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                        <img v-if="productImageIndex > 0" src="/images/arrow-left.png" class="w-full nextPrevProductImage" @click="previousProductImage()">
                    </div>
                    <div class="vx-col sm:w-1/2 w-full mt-2">
                        <img v-if="product.hasOwnProperty('images') && product.images !== null && product.images.length > 0" :src="product.images[productImageIndex]" class="w-full">
                        <img v-else src="https://www.tucanomacchine.it/wp-content/uploads/2019/07/480px-No_image_available.svg.png" class="w-full">
                    </div>
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                        <img v-if="product.hasOwnProperty('images') && product.images !== null && (productImageIndex + 1 < product.images.length)" src="/images/arrow-right.png" class="w-full nextPrevProductImage" @click="nextProductImage()">
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <strong>{{ product.name }} ({{ product.sku }})</strong>
                    </div>
                </div>

                <div class="vx-row mb-6" v-if="product.description != ''">
                    <div class="vx-col w-full">
                        <span>{{ product.description }}</span>
                    </div>
                </div>

                <div class="vx-row mb-6" v-if="product.variations != ''">
                    <div class="vx-col w-full">
                        <span>Variations: {{ product.variations }}</span>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col md:1/2 w-full">
                        <span>Sugg. Price: € {{ (product.hasOwnProperty('sale_price') ? product.sale_price.toFixed(2) : 0.0) }}</span>
                    </div>
                    <div class="vx-col md:1/2 w-full">
                        <span>Drop. Price: € {{ (product.hasOwnProperty('dropshipping_price') ? product.dropshipping_price.toFixed(2) : 0.0) }}</span>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <span>Stock Quantity: {{ product.quantity }}</span>
                    </div>
                </div>
                
            </div>

        </vs-popup>

        <!-- Lead info popup -->
        <vs-popup title="Lead info" :active.sync="showPopup['lead_info']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3" v-for="(value, key, index) in lead.delivery" :key="'leadInfo' + index">
                    <div class="vx-col md:w-1/2 w-full">
                        <b>{{key}}</b>
                    </div>
                    <div class="vx-col md:w-1/2 w-full">
                        {{value}}
                    </div>
                </div>
            </div>
        </vs-popup>

        <!-- Payment link popup -->
        <vs-popup title="Payment link" :active.sync="showPopup['payment_link']">
            <div class="vx-row mb-3">
                <div class="vx-col w-full">
                    <vs-input :disabled="true" class="w-full" icon-pack="feather" icon="icon-link" icon-no-border :value="paymentLink" />
                </div>
                <div class="vx-col w-full">
                    <vs-button class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-copy" v-clipboard:copy="paymentLink" v-clipboard:success="onCodeCopied">Copy to clipboard</vs-button>
                </div>
            </div>
        </vs-popup>

        <!-- Date time info popup -->
        <vs-popup title="Set date time info" :active.sync="showPopup['date_time_info']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3">
                    <div class="vx-col w-full">
                        <label>Date time</label>
                        <vs-input type="datetime-local" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="dateTimeInfo.date" />
                    </div>
                </div>
                <div class="vx-col w-full">
                    <vs-button class="mr-auto ml-auto" color="primary" type="filled" icon-pack="feather" icon="icon-save" @click="saveDateTimeInfo(dateTimeInfo, dateTimeInfoType)">Save</vs-button>
                </div>
            </div>
        </vs-popup>

        <!-- Send message popup -->
        <vs-popup title="Send message" :active.sync="showPopup['send_message']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3">
                    <div class="vx-col w-full">
                        <h3>Message</h3>
                        <vs-textarea v-model="message"></vs-textarea>
                        <div class="flex" style="float: right;">
                            <vs-button color="success" type="filled" icon-pack="feather" icon="icon-check" @click="sendBulkMessage()"></vs-button>
                        </div>
                    </div>
                </div>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'

import Dropin from 'dropin'

function getCurrencyValue(s) {
  if (typeof(s) === 'number') {
    return s;
  } else if (typeof(s) === 'string') {
    return parseFloat(s.replace(',', '.'))
  } else {
    return 0;
  }
}

function stringContainsNumber(_input) {
  let string1 = String(_input);
  for (let i = 0; i < string1.length; i++) {
    if (!isNaN(string1.charAt(i)) && !(string1.charAt(i) === " ")) {
      return true;
    }
  }
  return false;
}

export default {
  components: {
    VueContext,
  },
  data () {
    return {
      dropin: null,

      maxItems: 150,
      view: 'leads',

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        statuses: ['to do','unprocessed','confirmed'],
        product: '',
        product_id: '',
        order_status: '',
      },
      showRecallDate: false,

      showPopup: {
        'notes': false,
        'add_product': false,
        'lead_info': false,
        'create_delivery': false,
        'payment_link': false,
        'date_time_info': false,
        'send_message': false,
      },

      leads: [],
      selectedLeads: [],
      lead: {},
      order: {
        id: '',
        status: '',
        lead_id: '',
        first_name: '',
        last_name: '',
        phone: '',
        email: '',
        shipping: {
          first_name: '',
          last_name: '',
          phone: '',
          email: '',
          notes: '',
          street_address: '',
          city: '',
          province: '',
          state: '',
          country: '',
          postcode: '',
          address: '',
          notes: '',
        },
        location: {},
        gateway: '',
        products: [],
        accessories: [],
        review: {
          products: [],
        },
        total: '0.0',
        newTotal: '0.0',

        completed_at: '',
      },

      products: [],
      dropinProducts: [],

      leadStatuses: [
        'complaint',
        'refunded',
        'unprocessed',
        'shipped',
        'delivered',
        'return',
        'stock',
        'confirmed',
        'no answer',
        'cancelled',
        'fake',
        'duplicate',
        'to recall',
        'to do',
        'waiting payment',
        'abandoned',
      ],
      leadStatusColor: {
        'complaint'      : 'danger',
        'refunded'       : 'warning',
        'unprocessed'    : '',
        'shipped'        : 'primary',
        'delivered'      : 'success',
        'return'         : '',
        'stock'          : '',
        'confirmed'      : 'success',
        'no answer'      : '#555555',
        'cancelled'      : 'danger',
        'fake'           : '#cc8877',
        'duplicate'      : 'warning',
        'to recall'      : 'primary',
        'to do'          : '',
        'waiting payment': 'primary',
        'abandoned'      : '',
      },
      leadStatusOptions: [
        'confirmed',
        'to do',
        'to recall',
        'no answer',
        'cancelled',
        'fake',
        'duplicate',
        'waiting payment',
        'stock',
      ],

      deliveryStatuses: [
        'not sent',
        'error',
        'sent',
      ],
      deliveryStatusColor: {
        'not sent': 'primary',
        'error'   : 'danger',
        'sent'    : 'success',
      },

      orderStatuses: [
        { name: 'Unprocessed',         id: 'imported' },
        { name: 'To prepare',          id: 'to_prepare' },
        { name: 'Waiting for carrier', id: 'prepared_to_ship' },
        { name: 'Assigned to carrier', id: 'waiting_carrier' },
        { name: 'Shipped',             id: 'shipped' },
        { name: 'Delivered',           id: 'delivered' },
        { name: 'Return',              id: 'return' },
        { name: 'Invalid',             id: 'invalid' },
        { name: 'Out of stock',        id: 'out_of_stock' },
        { name: 'Duplicate',           id: 'duplicate' },
        { name: 'Lead',                id: 'lead' },
      ],
      orderStatusOptions: [
        { label: 'Unprocessed',         value: 'imported' },
        { label: 'To prepare',          value: 'to_prepare' },
        { label: 'Waiting for carrier', value: 'prepared_to_ship' },
        { label: 'Assigned to carrier', value: 'waiting_carrier' },
        { label: 'Shipped',             value: 'shipped' },
        { label: 'Delivered',           value: 'delivered' },
        { label: 'Return',              value: 'return' },
        { label: 'Invalid',             value: 'invalid' },
        { label: 'Out of stock',        value: 'out_of_stock' },
        { label: 'Duplicate',           value: 'duplicate' },
        { label: 'Lead',                value: 'lead' },
      ],

      carriers: [
        'brt',
        'liccardi',
        'gls',
        'crono',
      ],
      carrierIcon: {
        'brt'     : 'https://app.elogy.io/assets/img/carriers/brt.png',
        'liccardi': 'https://app.elogy.io/assets/img/carriers/liccardi.png',
        'gls'     : 'https://app.elogy.io/assets/img/carriers/gls.png',
        'crono'   : 'https://app.elogy.io/assets/img/carriers/crono.png',
      },

      gateways: [
        'cashondelivery',
        'online',
      ],
      gatewayShortNames: {
        'cashondelivery': 'cod',
        'online'        : 'online',
      },

      accessories: [
        { name: 'Insurance',     quantity: 1, price: 1.90, total: 1.90, enabled: false },
        { name: 'Fast delivery', quantity: 1, price: 2.50, total: 2.50, enabled: false },
        { name: 'Gift',          quantity: 1, price: 4.99, total: 4.99, enabled: false },
      ],

      totalStats: {
        price: 0.0,
        quantity: 0,
        total: 0.0,
        items: 0,
      },

      newStatus: '',
      newValue: '',

      paymentSuccessUrl: '',
      paymentCancelUrl: '',
      paymentLink: '',

      dateTimeInfo: {
        date: '',
      },
      dateTimeInfoType: 'recall_at',

      receivers: [],
      message: '',

      dropinGateways: {
        'cashondelivery': 'Cod',
        'online'        : 'Prepaid',
      },
      dropinDefaultEmail: 'mdjordje82@gmail.com',

      productView: 'normal',
      product: {},
      productImageIndex: 0,

      productOptions: [],
      dropinProductOptions: [],
    }
  },
  computed: {
    paginationPageSize() {
      if (this.gridApi) return this.gridApi.paginationGetPageSize()
      else return 50
    },
    totalPages() {
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
    objects() {
      return this.leads
    },
    // TODO: old code
    /*
    paymentLink() {
      if (this.lead === null) {
        return ''
      }

      let url = 'https://' + window.location.hostname + '/leads/' + this.lead.id + '/checkout'
      url += '?success_url=' + this.paymentSuccessUrl
      url += '&cancel_url=' + this.paymentCancelUrl

      return url
    },
    */
  },
  methods: {
    // Frontend functions
    onPageChanged(val) {
      this.currentPage = val
    },
    async changeView(view, item = null) {
      if (view == 'leads') {
        this.$vs.loading()
        this.selectedLeads = []
        this.leads = await this.dropin.fetchLeads(this.filters, this.dropinProducts)

        for (let i = 0; i < this.leads.length; i++) {
          let lead = this.leads[i]
          let product = null
          if (lead.hasOwnProperty('product_id') && lead.product_id !== null && lead.product_id != '') {
            for (let j = 0; j < this.products.length; j++) {
              let p = this.products[j]
              if (p.id == lead.product_id) {
                product = p
                break
              }
            }
          }
          lead.product = product
          this.leads[i] = lead
        }

        this.calculateStats()
        this.view = view
        this.$vs.loading.close()
      } else if (view == 'order_editor') {
        let lead = item
        this.loadOrderInfo(lead)
        this.view = view
      } else if (view == 'order_builder') {
        this.emptyOrderInfo()
        this.view = view
      }
    },
    getObjectById(id) {
      for (let i = 0; i < this.leads.length; i++) {
        if (this.objects[i].id === id) {
          return this.objects[i]
        }
      }
      return null
    },
    async applyFilters() {
      await this.changeView(this.view)
    },
    async resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        statuses: ['to do','unprocessed','confirmed'],
        product: '',
        product_id: '',
        order_status: '',
      }
      await this.filterDatesShortcut('this_month')
    },
    async setFiltersStatuses(statuses, showRecallDate = false) {
      this.filters.statuses = []
      for (let i = 0; i < statuses.length; i++) {
        this.filters.statuses.push(statuses[i])
      }
      this.showRecallDate = showRecallDate
      await this.applyFilters()
    },
    async filterDatesShortcut(period) {
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
        await this.applyFilters()
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
      let dd = String(dateObj.getDate()).padStart(2, '0')
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0')
      let yyyy = dateObj.getFullYear()
      let hh = String(dateObj.getHours() - 1).padStart(2, '0') // TODO: Why -1? BECAUSE OF THE TIMEZONE!!!
      let ii = String(dateObj.getMinutes()).padStart(2, '0')
      
      let formattedDate = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + ii

      return formattedDate
    },
    formatDateTimeISO8601(dateObj) {
      return dateObj.toISOString()
    },
    calculateStats() {
      let price = 0.0
      let quantity = 0
      let total = 0.0
      let items = 0

      for (let i = 0; i < this.leads.length; i++) {
        let lead = this.leads[i]
        price += lead.price
        quantity += lead.quantity
        total += lead.total
        if (lead.info !== null && lead.info.hasOwnProperty('products') && lead.info.products !== null) {
          items += lead.info.products.length
        }
      }

      // Average price
      price = price / this.leads.length

      this.totalStats.price = price
      this.totalStats.quantity = quantity
      this.totalStats.total = total
      this.totalStats.items = items
    },
    openNewTab(url) {
      window.open(url, '_blank').focus()
    },
    whatsappMessage(lead) {
      if (lead.hasOwnProperty('phone')) {
        let phone = ''
        for (let i = 0; i < lead.phone.length; i++) {
          let c = lead.phone[i]
          if (c == '+' || (c >= '0' && c <= '9')) {
            phone += c
          }
        }
        if (phone[0] != '+') {
          phone = '+39' + phone
        }
        let url = 'https://api.whatsapp.com/send?phone=' + phone
        this.openNewTab(url)
      }
    },

    confirmUpdateStatus(lead) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'primary',
        title: 'Status change confirmation',
        text: 'Are you sure you want to change the status from "' + lead.status + '" to "' + this.newStatus + '"?',
        accept: function () {
          this.updateStatus(lead)
        }.bind(this),
      })
    },
    async updateStatus(lead) {
      this.$vs.loading()

      let params = {
        status: this.newStatus,
      }
      await this.dropin.updateLead(lead.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead\'s status updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      if (this.newStatus === 'to recall') {
        this.dateTimeInfo.date = ''
        Object.assign(this.lead, lead)
        this.showPopup['date_time_info'] = true
      }

      this.$vs.loading.close()

      this.applyFilters()
    },
    confirmUpdateStatusBulk(leads) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'primary',
        title: 'Status change confirmation',
        text: 'Are you sure you want to change the status of the selected leads to "' + this.newStatus + '"?',
        accept: function () {
          this.updateStatusBulk(leads)
        }.bind(this),
      })
    },
    async updateStatusBulk(leads) {
      let numLeads = leads.length
      if (numLeads <= 0) return

      this.$vs.loading()

      let params = {
        status: this.newStatus,
      }
      for (let i = 0; i < numLeads; i++) {
        await this.dropin.updateLead(leads[i].id, params)
      }

      this.$vs.notify({
        title: 'Success',
        text: 'Leads status updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      // TODO: What about the 'to recall'
      // Don't worry for now because we only change to 'unprocessed' in bulk
      /*
      if (this.newStatus === 'to recall') {
        this.dateTimeInfo.date = ''
        Object.assign(this.lead, lead)
        this.showPopup['date_time_info'] = true
      }
      */

      this.$vs.loading.close()

      this.applyFilters()
    },
    confirmTogglePaid(lead) {
      let prevPaid = (lead.paid) ? 'paid' : 'not paid'
      let nextPaid = (!lead.paid) ? 'paid' : 'not paid'
      this.$vs.dialog({
        type: 'confirm',
        color: 'primary',
        title: 'Paid status change confirmation',
        text: 'Are you sure you want to change the paid status from "' + prevPaid + '" to "' + nextPaid + '"?',
        accept: function () {
          this.togglePaid(lead)
        }.bind(this),
      })
    },
    async togglePaid(lead) {
      this.$vs.loading()

      let info = lead.info
      if (info === null) {
        info = {}
      }
      info.paid = !lead.paid
      let params = {
        info: JSON.stringify(info),
      }
      if (info.paid) {
        params.status = 'confirmed'
      }
      await this.dropin.updateLead(lead.id, params)

      this.$vs.loading.close()

      await this.applyFilters()
    },
    async updateStatus(lead) {
      this.$vs.loading()

      let params = {
        status: this.newStatus,
      }
      await this.dropin.updateLead(lead.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead\'s status updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      if (this.newStatus === 'to recall') {
        this.dateTimeInfo.date = ''
        Object.assign(this.lead, lead)
        this.showPopup['date_time_info'] = true
      }

      this.$vs.loading.close()

      this.applyFilters()
    },
    async updateStatusOrderEditor() {
      this.$vs.loading()

      this.order.status = this.newStatus
      let params = {
        status: this.newStatus,
      }
      await this.dropin.updateLead(this.order.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead\'s status updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()
    },
    updateStatusOrderBuilder() {
      this.order.status = this.newStatus
    },
    showNotes(lead) {
      // Clone the object (don't use the one already existing)
      Object.assign(this.lead, lead)
      this.showPopup['notes'] = true
    },
    async updateNotes(lead) {
      this.$vs.loading()

      let params = {
        notes: lead.notes,
      }
      await this.dropin.updateLead(lead.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead\'s notes updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.showPopup['notes'] = false
      this.$vs.loading.close()

      this.applyFilters()
    },
    async updateContact(lead) {
      this.$vs.loading()

      let info = lead.info
      if (info === null) {
        info = {}
      }
      info.contact = lead.contact
      if (!info.hasOwnProperty('call_count')) {
        info.call_count = 0
      }
      if (lead.contact === true) {
        info.call_count += 1
      }
      let params = {
        info: JSON.stringify(info),
      }
      await this.dropin.updateLead(lead.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead\'s contact updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.$vs.loading.close()

      this.applyFilters()
    },

    findAddress() {
      let street_address = ''
      let city = ''
      let postcode = ''
      let province = ''
      let state = ''
      let country = ''

      let loc = this.order.location
      if (loc.hasOwnProperty('street') && loc.hasOwnProperty('street_number')) {
        street_address = loc.street + ' ' + loc.street_number
      }
      if (loc.hasOwnProperty('city')) {
        city = loc.city
      }
      if (loc.hasOwnProperty('postcode')) {
        postcode = loc.postcode
      }
      if (loc.hasOwnProperty('province')) {
        province = loc.province
      }
      if (loc.hasOwnProperty('state')) {
        state = loc.state
      }
      if (loc.hasOwnProperty('country')) {
        country = loc.country
      }

      let address = street_address + ', ' + city + ' (' + province + ') ' + postcode + ', ' + state + ', ' + country

      this.order.shipping.street_address = street_address
      this.order.shipping.city = city
      this.order.shipping.postcode = postcode
      this.order.shipping.province = province
      this.order.shipping.state = state
      this.order.shipping.country = country
      this.order.shipping.address = address
    },
    async fetchProducts() {
      this.dropinProducts = await this.dropin.fetchProducts()

      this.dropinProductOptions = []
      for (let i = 0; i < this.dropinProducts.length; i++) {
        let p = this.dropinProducts[i]
        this.dropinProductOptions.push({
          label: p.name,
          value: p.sku,
        })
      }

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken'),
      }
      const response = await this.$http.get('/api/v1/products', { headers })
      this.products = response.data.data
      this.productOptions = []
      for (let i = 0; i < this.products.length; i++) {
        let p = this.products[i]
        this.productOptions.push({
          label: p.name,
          value: p.id,
        })
      }
    },
    removeProduct(index) {
      this.order.products.splice(index, 1)
      this.refreshOrderReviewTotal()
    },
    getProductById(id) {
      let p = null
      for (let i = 0; i < this.dropinProducts.length; i++) {
        if (this.dropinProducts[i].id == id) {
          p = this.dropinProducts[i]
          break
        }
      }
      return p
    },
    updateOrderTotal() {
      this.order.total = this.order.newTotal
    },
    refreshOrderReviewTotal() {
      // TODO: old code
      /*
      let revProducts = []

      for (let i = 0; i < this.order.products.length; i++) {
        let pId = this.order.products[i]

        if (pId === '') {
          continue
        }

        if (!revProducts.hasOwnProperty(pId)) {
          let p = this.getProductById(pId)
          revProducts[pId] = {
            name: p.name,
            quantity: 1,
            unit_price: p.price,
          }
        } else {
          revProducts[pId].quantity += 1
        }
      }

      let toReturn = []
      for (const [key, value] of Object.entries(revProducts)) {
        toReturn.push(value)
      }

      revProducts = toReturn
      let total = 0.0

      for (let i = 0; i < revProducts.length; i++) {
        let p = revProducts[i]
        total += parseFloat(p.unit_price)*parseInt(p.quantity)
      }
      */

      let totalProducts = 0.0
      for (let i = 0; i < this.order.products.length; i++) {
        let p = this.order.products[i]
        let subtotal = 0.0
        let price = getCurrencyValue(p.price)
        let quantity = parseInt(p.quantity)
        subtotal = price*quantity
        this.order.products[i].total = subtotal
        totalProducts += subtotal
      }

      let totalAccessories = 0.0
      for (let i = 0; i < this.order.accessories.length; i++) {
        let a = this.order.accessories[i]
        let subtotal = 0.0
        let price = getCurrencyValue(a.price)
        let quantity = parseInt(a.quantity)
        subtotal = price*quantity
        this.order.accessories[i].total = subtotal
        if (a.enabled) {
          totalAccessories += subtotal
        }
      }

      // TODO: old code
      //let insurance = getCurrencyValue(this.order.insurance)

      let total = totalProducts + totalAccessories

      this.order.total = total.toFixed(2)
    },
    async updateLeadInfoNoLoading(leadInfo) {
      let lead = this.getObjectById(leadInfo.lead_id)
      let info = lead.info
      let o = leadInfo

      info.shipping = {
        first_name: o.shipping.first_name,
        last_name: o.shipping.last_name,
        phone: o.shipping.phone,
        email: o.shipping.email,
        street_address: o.shipping.street_address,
        city: o.shipping.city,
        state: o.shipping.state,
        province: o.shipping.province,
        postcode: o.shipping.postcode,
        country: o.shipping.country,
        notes: o.shipping.notes,
      }

      info.products = o.products
      info.accessories = o.accessories
      // TODO: old code
      //info.insurance = o.insurance
      info.total = o.total
      info.gateway = o.gateway

      let params = {
        first_name: o.first_name,
        last_name: o.last_name,
        phone: o.phone,
        email: o.email,
        info: JSON.stringify(info),
      }
      await this.dropin.updateLead(lead.id, params)
    },
    // TODO: Old code
    /*
    async updateLeadInfo(leadInfo) {
      this.$vs.loading()

      await this.updateLeadInfoNoLoading(leadInfo)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead saved',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()
    },
    */
    async updateLeadInfo(leadInfo) {
      this.$vs.loading()

      await this.updateLeadInfoNoLoading(this.order)

      let o = this.order

      let productsTotal = 0.0
      let products = []
      for (let i = 0; i < o.products.length; i++) {
        let p = o.products[i]
        let subtotal = getCurrencyValue(p.total)
        productsTotal += subtotal
        products.push({
          sku: p.sku,
          quantity: p.quantity,
          subtotal: subtotal.toFixed(2),
        })
      }

      let taxTotal = 0.0
      let shippingPrice = 0.0
      let totalPrice = productsTotal + shippingPrice + taxTotal
      if (o.total !== null && o.total !== '' && getCurrencyValue(o.total) > 0) {
        totalPrice = getCurrencyValue(o.total)
      }
      let subtotalPrice = totalPrice // TODO: What about this?

      let shippingPhone = o.phone
      let shippingEmail = o.email
      if (o.shipping.hasOwnProperty('phone') && o.shipping.phone !== null && o.shipping.phone !== '') {
        shippingPhone = o.shipping.phone
      }
      if (o.shipping.hasOwnProperty('email') && o.shipping.email !== null && o.shipping.email !== '') {
        shippingEmail = o.shipping.email
      }

      let params = {
        country: 'IT',
        total_price: totalPrice.toFixed(2),
        subtotal_price: subtotalPrice.toFixed(2),
        shipping_price: shippingPrice.toFixed(2),
        total_tax: taxTotal.toFixed(2),
        tax_included: 1,
        gateway: o.gateway,
        currency: 'EUR',
        customer: {
          first_name: o.first_name,
          last_name:  o.last_name,
          phone:      o.phone,
          email:      o.email,
        },
        shipping_first_name:    o.shipping.first_name,
        shipping_last_name:     o.shipping.last_name,
        shipping_address1:      o.shipping.street_address,
        shipping_address2:      '',
        shipping_city:          o.shipping.city,
        shipping_zip:           o.shipping.postcode,
        shipping_province_code: o.shipping.province,
        shipping_country_code:  o.shipping.country,
        shipping_phone:         shippingPhone,
        shipping_email:         shippingEmail,
        shipping_notes:         o.shipping.notes,
        products: products,
      }

      this.$vs.notify({
        title: 'Success',
        text: 'Lead updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()
    },
    loadOrderInfo(lead) {
      let first_name = lead.first_name
      let last_name = lead.last_name
      let phone = lead.phone
      let email = lead.email
      let products = []
      let accessories = [...this.accessories]

      let street_address = ''
      let city = ''
      let province = ''
      let state = ''
      let country = ''
      let postcode = ''
      let address = ''
      let notes = ''
      let gateway = 'cashondelivery'

      if (lead.info !== null) {
        if (lead.info.hasOwnProperty('products')) {
          products = lead.info.products
        }
        if (lead.info.hasOwnProperty('accessories')) {
          accessories = lead.info.accessories
        }

        if (lead.info.hasOwnProperty('shipping') && lead.info.shipping !== null) {
          if (lead.info.shipping.hasOwnProperty('first_name')) {
            first_name = lead.info.shipping.first_name
          }
          if (lead.info.shipping.hasOwnProperty('last_name')) {
            last_name = lead.info.shipping.last_name
          }
          if (lead.info.shipping.hasOwnProperty('phone')) {
            phone = lead.info.shipping.phone
          }
          if (lead.info.shipping.hasOwnProperty('email')) {
            email = lead.info.shipping.email
          }
          if (lead.info.shipping.hasOwnProperty('street_address')) {
            street_address = lead.info.shipping.street_address
          }
          if (lead.info.shipping.hasOwnProperty('city')) {
            city = lead.info.shipping.city
          }
          if (lead.info.shipping.hasOwnProperty('province')) {
            province = lead.info.shipping.province
          }
          if (lead.info.shipping.hasOwnProperty('state')) {
            state = lead.info.shipping.state
          }
          if (lead.info.shipping.hasOwnProperty('country')) {
            country = lead.info.shipping.country
          }
          if (lead.info.shipping.hasOwnProperty('postcode')) {
            postcode = lead.info.shipping.postcode
          }
          if (lead.info.shipping.hasOwnProperty('address')) {
            address = lead.info.shipping.address
          }
          if (lead.info.shipping.hasOwnProperty('notes')) {
            notes = lead.info.shipping.notes
          }
        }

        if (lead.info.hasOwnProperty('gateway') && lead.info.gateway != '') {
          gateway = lead.info.gateway
        } else if (lead.info.hasOwnProperty('delivery') && lead.info.delivery !== null) {
          if (lead.info.delivery.hasOwnProperty('gateway') && lead.info.delivery.gateway != '') {
            gateway = lead.info.delivery.gateway
          }
        }
      }

      this.order = {
        id: lead.id,
        status: lead.status,
        lead_id: lead.id,
        lead_hash: lead.hash,
        first_name: lead.first_name,
        last_name: lead.last_name,
        phone: lead.phone,
        email: lead.email,
        shipping: {
          first_name: first_name,
          last_name: last_name,
          phone: phone,
          email: email,
          notes: lead.notes,
          street_address: street_address,
          city: city,
          province: province,
          state: state,
          country: country,
          postcode: postcode,
          address: address,
          notes: notes,
        },
        location: {},
        gateway: gateway,
        products: products,
        accessories: accessories,
        review: {
          products: [],
        },
        total: '0.0',
        newTotal: '0.0',

        completed_at: '',
      }

      if (lead.info !== null && lead.info.hasOwnProperty('total') &&
          parseFloat(lead.info.total) > 0) {
        this.order.total = lead.info.total
      } else {
        this.refreshOrderReviewTotal()
      }
    },
    emptyOrderInfo() {
      this.order = {
        status: 'to do',
        first_name: '',
        last_name: '',
        phone: '',
        email: '',
        shipping: {
          first_name: '',
          last_name: '',
          phone: '',
          email: '',
          notes: '',
          street_address: '',
          city: '',
          province: '',
          state: '',
          country: '',
          postcode: '',
          address: '',
          notes: '',
        },
        location: {},
        gateway: 'cashondelivery',
        products: [],
        accessories: [
          { name: 'Insurance',     quantity: 1, price: 1.90, total: 1.90, enabled: false },
          { name: 'Fast delivery', quantity: 1, price: 2.50, total: 2.50, enabled: false },
          { name: 'Gift',          quantity: 1, price: 4.99, total: 4.99, enabled: false },
        ],
        review: {
          products: [],
        },
        total: '0.0',
        newTotal: '0.0',

        completed_at: '',
      }
    },
    async createLeadFromInfo(order) {
      this.$vs.loading()

      let o = order

      let info = {}
      info.shipping = {
        first_name: o.shipping.first_name,
        last_name: o.shipping.last_name,
        phone: o.shipping.phone,
        email: o.shipping.email,
        street_address: o.shipping.street_address,
        city: o.shipping.city,
        state: o.shipping.state,
        province: o.shipping.province,
        postcode: o.shipping.postcode,
        country: o.shipping.country,
        notes: o.shipping.notes,
      }
      info.products = o.products
      info.accessories = o.accessories
      info.total = o.total
      info.gateway = o.gateway
      info.payment_method = o.payment_method

      let quantity = 0
      for (let i = 0; i < o.products.length; i++) {
        let p = o.products[i]
        quantity += parseInt(p.quantity)
      }

      let params = {
        first_name: o.first_name,
        last_name: o.last_name,
        phone: o.phone,
        email: o.email,
        info: JSON.stringify(info),
        status: o.status,
        sku: o.products[0].sku,
        product_name: o.products[0].name,
        total: o.total,
        price: o.total,
        quantity: quantity,
      }
      const leadCreated = await this.dropin.createLead(params)

      let result = (leadCreated !== null)
      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Lead created',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot create the lead',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },

    async updatePrice(lead) {
      this.$vs.loading()

      let info = lead.info
      info.price = this.newValue
      let params = {
        info: JSON.stringify(info),
      }
      await this.dropin.updateLead(lead.id, params)

      this.$vs.loading.close()

      this.changeView('leads')
    },
    async updateQuantity(lead) {
      this.$vs.loading()

      let info = lead.info
      info.quantity = this.newValue
      let params = {
        info: JSON.stringify(info),
      }
      await this.dropin.updateLead(lead.id, params)

      this.$vs.loading.close()

      this.changeView('leads')
    },
    async updateTotal(lead) {
      this.$vs.loading()

      let info = lead.info
      info.total = this.newValue
      let params = {
        info: JSON.stringify(info),
      }
      await this.dropin.updateLead(lead.id, params)

      this.$vs.loading.close()

      this.changeView('leads')
    },
    async deleteLead(lead) {
      this.$vs.loading()
      await this.dropin.deleteLead(lead.id)
      this.$vs.loading.close()
      this.changeView('leads')
    },
    async deleteLeads(leads) {
      for (let i = 0; i < leads.length; i++) {
        await this.dropin.deleteLead(leads[i].id)
      }
    },
    async deleteSelected() {
      this.$vs.loading()
      await this.deleteLeads(this.selectedLeads)
      this.selectedLeads = []
      this.$vs.loading.close()
      this.changeView('leads')
    },
    confirmDeletePopup() {
      if (this.selectedLeads.length == 0) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Archive confirmation',
        text: 'Are you sure you want to archive ' + this.selectedLeads.length + ' object(s)?',
        accept: this.deleteSelected
      })
    },
    showAddProductPopup() {
      this.productView = 'normal'
      this.showPopup['add_product'] = true
    },
    toggleProduct(product) {
      for (let i = 0; i < this.order.products.length; i++) {
        let p = this.order.products[i]
        if (product.id === p.id) {
          this.removeProduct(i)
          return
        }
      }

      let quantity = 1
      let price = parseFloat(product.price)
      let total = price*quantity
      this.order.products.push({
        id: product.id,
        name: product.name,
        sku: product.sku,
        price: price,
        quantity: 1,
        total: total,
        upsell: false,
      })
      this.refreshOrderReviewTotal()
    },
    hasProduct(product) {
      for (let i = 0; i < this.order.products.length; i++) {
        let p = this.order.products[i]
        if (product.id === p.id) {
          return true
        }
      }
      return false
    },
    canUpdateLeadInfo(leadInfo) {
      let canCreate = true

      // Check shipping contact
      if (leadInfo !== null &&
          leadInfo.hasOwnProperty('shipping') && leadInfo.shipping !== null &&
          leadInfo.shipping.first_name != '' && leadInfo.shipping.last_name != '' &&
          leadInfo.shipping.phone != '') {
        canCreate = canCreate && true
      } else {
        canCreate = false
      }

      // Check if street address has number
      if (leadInfo !== null &&
          leadInfo.hasOwnProperty('shipping') && leadInfo.shipping !== null &&
          leadInfo.shipping.hasOwnProperty('street_address') && leadInfo.shipping.street_address !== null &&
          leadInfo.shipping.street_address != '' && leadInfo.shipping.city != '' &&
          leadInfo.shipping.province != '' && leadInfo.shipping.postcode != '' &&
          leadInfo.shipping.state != '' && leadInfo.shipping.country != ''
      ) {
        canCreate = canCreate && stringContainsNumber(leadInfo.shipping.street_address)
      } else {
        canCreate = false
      }

      return canCreate
    },
    canCreateLeadFromInfo(leadInfo) {
      let canCreate = true

      // Check shipping contact
      if (leadInfo !== null &&
          leadInfo.hasOwnProperty('shipping') && leadInfo.shipping !== null &&
          leadInfo.shipping.first_name != '' && leadInfo.shipping.last_name != '' &&
          leadInfo.shipping.phone != '') {
        canCreate = canCreate && true
      } else {
        canCreate = false
      }

      // Check if street address has number
      if (leadInfo !== null &&
          leadInfo.hasOwnProperty('shipping') && leadInfo.shipping !== null &&
          leadInfo.shipping.hasOwnProperty('street_address') && leadInfo.shipping.street_address !== null &&
          leadInfo.shipping.street_address != '' && leadInfo.shipping.city != '' &&
          leadInfo.shipping.province != '' && leadInfo.shipping.postcode != '' &&
          leadInfo.shipping.state != '' && leadInfo.shipping.country != ''
      ) {
        canCreate = canCreate && stringContainsNumber(leadInfo.shipping.street_address)
      } else {
        canCreate = false
      }

      return canCreate
    },
    getPaymentLink(leadHash, paymentSuccessUrl = '', paymentCancelUrl = '') {
      let url = 'https://' + window.location.hostname + '/leads/' + leadHash + '/checkout'
      return url
    },
    showPaymentLink(lead) {
      this.lead = lead
      let url = this.getPaymentLink(this.lead.hash, this.paymentSuccessUrl, this.paymentCancelUrl)
      this.paymentLink = url
      this.showPopup['payment_link'] = true
    },
    onCodeCopied() {
      this.$vs.notify({
        title: 'Success',
        text: 'Copied to the clipboard',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    async copyToClipboard(toCopy, notificationMessage = 'Copied to the clipboard') {
      await window.navigator.clipboard.writeText(toCopy)
      this.$vs.notify({
        title: 'Success',
        text: notificationMessage,
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    async copyPhoneNumbers(objects) {
      if (objects.length <= 0) {
        return
      }

      let toCopy = ''
      for (let i = 0; i < objects.length; i++) {
        toCopy += objects[i].phone + '\n'
      }

      await this.copyToClipboard(toCopy)
    },
    async copyIds(objects) {
      if (objects.length <= 0) {
        return
      }

      let toCopy = ''
      for (let i = 0; i < objects.length; i++) {
        toCopy += objects[i].id + '\n'
      }

      await this.copyToClipboard(toCopy)
    },
    async sendMessage(phone, message) {
      const result = await this.dropin.sendMessage(phone, message)
      return result
    },
    confirmSendPaymentLinkViaMessage() {
      this.$vs.dialog({
        type: 'confirm',
        color: 'primary',
        title: 'Send Payment link confirmation',
        text: 'Are you sure you want to send the payment link via message (Whatsapp)?',
        accept: this.sendPaymentLinkViaMessage
      })
    },
    async sendPaymentLinkViaMessage() {
      this.$vs.loading()

      let paymentLink = this.getPaymentLink(this.order.lead_hash)

      let phone = this.order.phone
      let message = 'Link di pagamento: ' + paymentLink

      const result = await this.sendMessage(phone, message)

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Message sent',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Error while sending the message',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    hasAddressInfo(lead) {
      if (lead.hasOwnProperty('info') && lead.info !== null) {
        if (lead.info.hasOwnProperty('shipping') && lead.info.shipping !== null) {
          let shipping = lead.info.shipping
          if (shipping.hasOwnProperty('street_address') && shipping.street_address !== null &&
              shipping.hasOwnProperty('city') && shipping.city !== null &&
              shipping.hasOwnProperty('province') && shipping.province !== null) {
            return true
          }
        }
      }
      return false
    },
    getAddress(lead) {
      let shipping = lead.info.shipping
      return (shipping.street_address + ', ' + shipping.city + ' (' + shipping.province + ')')
    },
    getProductsList(lead) {
      let productName = lead.product_name
      let s = ''
      if (lead.hasOwnProperty('info') && lead.info !== null &&
          lead.info.hasOwnProperty('products') && lead.info.products !== null) {
        for (let i = 0; i < lead.info.products.length; i++) {
          let p = lead.info.products[i]
          if (i > 0) {
            s += ' - '
          }
          let pName = productName
          if (p.hasOwnProperty('name') && p.name !== null) {
            pName = p.name
          }
          s += pName + '(x' + p.quantity + ')'
        }
      }
      return s
    },
    showDateTimeInfoPopup(lead, type) {
      let currentDateTime = ''
      if (lead.info !== null && lead.info.hasOwnProperty(type)) {
        currentDateTime = lead.info[type]
      }
      this.dateTimeInfo.date = currentDateTime
      this.dateTimeInfoType = type
      Object.assign(this.lead, lead)
      this.showPopup['date_time_info'] = true
    },
    async saveDateTimeInfo(dateTimeInfo, type) {
      let setDateTime = new Date(dateTimeInfo.date)
      // NOTE TODO: Be careful! This + 2 is needed because js is stupid enough to
      // pick the correct date but subtract 2 hours. Idk why, but it seems so stupid.
      // TODO: What happens when timezone changes again (only +1 for example),
      // we need a more general code.
      setDateTime.setHours(setDateTime.getHours() + 2)
      let formattedDateTime = this.formatDateTime(setDateTime)

      this.$vs.loading()
      let info = this.lead.info
      if (info === null) {
        info = {}
      }
      info[type] = formattedDateTime

      // DEBUG TODO: to delete
      console.log(type, info[type])

      const result = await this.dropin.updateLead(this.lead.id, { info: JSON.stringify(info) })
      this.$vs.loading.close()

      // TODO: Old code
      /*
      if (this.lead.notes === null) {
        this.lead.notes = ''
      }
      if (this.lead.notes !== '') {
        this.lead.notes += "\n"
      }
      this.lead.notes += 'Recall date: ' + formattedDateTime
      await this.updateNotes(this.lead)
      */

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Date time info saved',
          iconPack: 'feather',
          icon: 'icon-phone',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot save date time info',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.showPopup['date_time_info'] = false

      await this.applyFilters()
    },
    openSendMessagePopup(receivers) {
      this.receivers = receivers
      this.message = ''
      this.showPopup['send_message'] = true
    },
    async sendBulkMessage() {
      this.$vs.loading()
      for (let i = 0; i < this.receivers.length; i++) {
        let r = this.receivers[i]
        if (!r.hasOwnProperty('phone') || r.phone === null || r.phone === '') {
          continue
        }

        await this.sendMessage(r.phone, this.message)
      }
      this.$vs.notify({
        title: 'Success',
        text: 'Messages sent successfully',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.showPopup['send_message'] = false
      this.$vs.loading.close()
    },
    getLeadsAsCSV(leads) {
      let content = ''

      for (let i = 0; i < leads.length; i++) {
        let lead = leads[i]

        let email = null
        if (lead.info.shipping.hasOwnProperty('email') && lead.info.shipping.email !== null && lead.info.shipping.email != '') {
          email = lead.info.shipping.email
        } else if (lead.email !== null && lead.email != '') {
          email = lead.email
        }
        if (email === null || email == '') {
          email = this.dropinDefaultEmail
        }

        let phone = null
        if (lead.info.shipping.hasOwnProperty('phone') && lead.info.shipping.phone !== null && lead.info.shipping.phone != '') {
          phone = lead.info.shipping.phone
        } else if (lead.phone !== null && lead.phone != '') {
          phone = lead.phone
        }

        let productsString = '['
        for (let pIndex = 0; pIndex < lead.products.length; pIndex++) {
          let product = lead.products[pIndex]

          if (pIndex > 0) {
            productsString += ','
          }
          productsString += '['
          productsString += '""' + product.sku + '"",""' + product.quantity + '""'
          productsString += ']'
        }
        productsString += ']'

        let shippingNotes = ''
        if (lead.info.shipping.hasOwnProperty('notes') && lead.info.shipping.notes !== null) {
          shippingNotes = lead.info.shipping.notes
        }

        content += '"' + this.formatDate(new Date(lead.created_at)) + '",'
        content += '"' + this.dropinGateways[lead.gateway] + '",'
        content += lead.info.order_id + ','
        content += '"' + lead.total + '",'
        content += '"' + productsString + '",'
        content += '"' + lead.full_name + '",'
        content += '"' + lead.info.shipping.street_address + '",'
        content += '"' + lead.info.shipping.city + ' (' + lead.info.shipping.province + ')' + '",'
        content += lead.info.shipping.postcode + ','
        content += '"' + lead.info.shipping.country + '",'
        content += '"' + email + '",'
        content += '"' + phone + '",'
        content += '"' + shippingNotes + '",'

        content += "\n"
      }

      return content
    },
    async copyLeadsCSV(leads) {
      let content = this.getLeadsAsCSV(leads)
      await this.copyToClipboard(content)
    },
    async exportLeadsAsCSV(leads) {
      let content = this.getLeadsAsCSV(leads)
      let filename = 'dropin_leads.csv'

      let element = document.createElement('a')
      element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(content))
      element.setAttribute('download', filename)
      element.style.display = 'none'
      document.body.appendChild(element)
      element.click()
      document.body.removeChild(element)
    },
    showProductInfo(product) {
      this.product = product
      this.productImageIndex = 0
      this.productView = 'product_info'
    },
    previousProductImage() {
      this.productImageIndex -= 1
      if (this.productImageIndex < 0) {
        this.productImageIndex = 0
      }
    },
    nextProductImage() {
      this.productImageIndex += 1
      if (this.productImageIndex >= this.product.images.length) {
        this.productImageIndex = this.product.images.length - 1
      }
    },
    async openLeadChat(lead) {
      this.openNewTab('/dashboard/dropin/leads/' + lead.id + '/chat')
      // TODO: Old code
      // TODO: to delete
      /*
      this.$vs.loading()

      this.lead = lead
      this.chatMessages = await this.dropin.fetchLeadMessages(lead.id)

      // Refresh Chat every 10 seconds
      this.timer = setInterval(() => {
        this.refreshChatMessagesBackground()
      }, 10 * 1000)

      this.$vs.loading.close()
      this.view = 'chat'
      */
    },
    // TODO: Old code
    // TODO: to delete
    /*
    async refreshChatMessages() {
      this.$vs.loading()
      this.chatMessages = await this.dropin.fetchLeadMessages(this.lead.id)
      this.$vs.loading.close()
    },
    async refreshChatMessagesBackground() {
      this.chatMessages = await this.dropin.fetchLeadMessages(this.lead.id)
    },
    */

    async updateOrderId(lead) {
      this.$vs.loading()

      let info = lead.info
      if (info === null) {
        info = {}
      }
      let value = null
      if (this.newValue != '') {
        value = parseInt(this.newValue)
      }
      info.order_id = value
      let params = {
        info: JSON.stringify(info),
      }
      let object = await this.dropin.updateLead(lead.id, params)
      let result = (object !== null)

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Lead successfully updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the lead',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()

      await this.applyFilters()
    },
  },
  async created() {
    this.dropin = new Dropin(localStorage.getItem('accessToken'))

    this.$vs.loading()

    await this.fetchProducts()
    await this.resetFilters()

    this.$vs.loading.close()
  },
}

</script>

<style scoped>

.column-no-padding {
  padding-top: 0px !important;
  padding-bottom: 0px !important;
  font-size: 0.8em;
  white-space: nowrap;
}

/*
.customer_popover {
  z-index: 999999 !important;
  position: fixed !important;
}

.items_popover {
  z-index: 999999 !important;
  position: fixed !important;
}
*/

.carrier-icon {
  width: 24px !important;
}

.dropdown {
  font-family: "Montserrat", Helvetica, Arial, sans-serif;
}

.dropdown-menu {
  min-width: 200px !important;
}

.nextPrevProductImage {
  cursor: pointer;
}
.nextPrevProductImage:hover {
  filter: brightness(0.9);
}

.icon-paid {
  width: 20px !important;
  height: 20px !important;
  margin: 0px;
  padding: 0px;
}

</style>

