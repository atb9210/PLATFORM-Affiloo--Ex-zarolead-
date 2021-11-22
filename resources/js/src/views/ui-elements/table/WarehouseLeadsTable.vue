<template>
    <vx-card title="Leads" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button v-if="view == 'leads'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button v-if="view == 'order_builder'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="changeView('leads')"></vs-button>
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
                    <label>Status</label>
                    <vs-select class="w-full" v-model="filters.status">
                        <vs-select-item :key="sIndex" :value="status" :text="status" v-for="(status, sIndex) in leadStatuses" />
                    </vs-select>
                </div>
                <div class="vx-col w-1/2">
                    <label>Product</label>
                    <vs-select class="w-full" v-model="filters.product">
                        <vs-select-item :key="pIndex" :value="product.sku" :text="product.name" v-for="(product, pIndex) in products" />
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
                  <a href="#" @click="$refs.lead_context_menu.close();showLeadInfo(data)" class="flex items-center text-sm">
                    <feather-icon icon="InfoIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Show info</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.lead_context_menu.close();changeView('order_builder', data)" class="flex items-center text-sm">
                    <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Edit</span>
                  </a>
                </li>
                <li>
                  <a href="#" v-if="data !== null && data.hasOwnProperty('info') && canCreateDelivery(data.info)" @click="$refs.lead_context_menu.close();fastCreateDelivery(data)"class="flex items-center text-sm">
                    <feather-icon icon="PackageIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Create delivery</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.lead_context_menu.close();whatsappMessage(data)"class="flex items-center text-sm">
                    <feather-icon icon="MessageCircleIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Whatsapp message</span>
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

            <popover class="items_popover" :name="'items_popover_' + lead.id" event="hover" v-for="(lead, index) in leads" :key="index">
                <ul v-if="lead.info !== null && lead.info.hasOwnProperty('products') && lead.info.products !== null && lead.info.products.length > 0">
                    <li v-for="(item, pIndex) in lead.info.products" :key="pIndex">
                        {{item.name}} (x{{item.quantity}})
                    </li>
                </ul>
            </popover>

            <!-- Leads table -->
            <vs-table stripe multiple v-model="selectedLeads" :max-items="maxItems" pagination search :data="leads" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="id">ID</vs-th>
                    <vs-th sort-key="created_at">Date</vs-th>
                    <!--<vs-th sort-key="delivery.completed_at">Completed</vs-th>-->
                    <vs-th sort-key="full_name">Name</vs-th>
                    <vs-th sort-key="phone">Phone</vs-th>
                    <!--<vs-th sort-key="sku">SKU</vs-th>-->
                    <vs-th sort-key="product_name">Product</vs-th>
                    <!--<vs-th sort-key="delivery.gateway">Payment</vs-th>-->
                    <vs-th sort-key="price">Price</vs-th>
                    <vs-th sort-key="quantity">Q.ty</vs-th>
                    <vs-th sort-key="total">Total</vs-th>
                    <vs-th>Items</vs-th>
                    <vs-th sort-key="contact">Contact</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <!--<vs-th sort-key="order_status">Order Status</vs-th>-->
                    <!--<vs-th sort-key="carrier">Carrier</vs-th>-->
                    <vs-th sort-key="notes">Actions</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                        <vs-td class="column-no-padding" :data="item.id">
                            {{item.id}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.created_at">
                            {{formatDateTime(new Date(item.created_at))}}
                        </vs-td>

                        <!--
                        <vs-td class="column-no-padding" :data="item.delivery.completed_at">
                            <span v-if="item.delivery.completed_at !== ''">
                                {{formatDateTime(new Date(item.delivery.completed_at))}}
                            </span>
                        </vs-td>
                        -->

                        <vs-td class="column-no-padding" :data="item.full_name">
                            <div @contextmenu.prevent="$refs.lead_context_menu.open($event, item)">
                                <vs-chip>
                                    {{item.full_name}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td class="column-no-padding">
                            <vs-chip v-if="item.hasOwnProperty('phone') && item.phone != ''">
                                <span @dblclick="whatsappMessage(item)">
                                    {{item.phone}}
                                </span>
                            </vs-chip>
                        </vs-td>

                        <!--
                        <vs-td class="column-no-padding" :data="item.sku">
                            {{item.sku}}
                        </vs-td>
                        -->

                        <vs-td class="column-no-padding" :data="item.product_name">
                            <vs-chip>
                                {{item.product_name}}
                            </vs-chip>
                        </vs-td>

                        <!--
                        <vs-td class="column-no-padding" :data="item.delivery.gateway">
                            {{item.delivery.gateway}}
                        </vs-td>
                        -->

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
                            <vs-chip v-if="item.info !== null && item.info.hasOwnProperty('products') && item.info.products !== null && item.info.products.length > 0" v-popover="{name:'items_popover_' + item.id}">
                                {{item.info.products.length}} items
                            </vs-chip>
                            <vs-chip v-else>
                                0 items
                            </vs-chip>
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.contact">
                            <vs-checkbox v-model="data[indextr].contact" @change="updateContact(data[indextr])" />
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.status">
                            <fast-editable-value-select
                                class="font-bold"
                                :options="leadStatuses"
                                v-model="newStatus"
                                @onEdit="newStatus = data[indextr].status"
                                @onUpdate="updateStatus(data[indextr])">
                                  <vs-chip :color="leadStatusColor[item.status]">
                                      {{item.status}}
                                  </vs-chip>
                            </fast-editable-value-select>
                        </vs-td>

                        <!--
                        <vs-td class="column-no-padding" :data="item.order_status">
                            <span>{{item.order_status}}</span>
                        </vs-td>
                        -->

                        <!--
                        <vs-td class="column-no-padding" :data="item.carrier">
                            <div v-if="item.carrier != ''" class="w-full">
                                <img class="carrier-icon" v-if="carrierIcon.hasOwnProperty(item.carrier)" :src="carrierIcon[item.carrier]" />
                                <span v-else>
                                    {{item.carrier}}
                                </span>
                            </div>
                        </vs-td>
                        -->

                        <vs-td class="column-no-padding">
                            <div class="flex items-center">
                                <vx-tooltip :text="item.notes" position="top">
                                    <vs-button class="mr-2" :color="(item.notes === null ||item.notes === '') ? 'primary' : 'warning'" @click="showNotes(item)" icon-pack="feather" icon="icon-flag" />
                                </vx-tooltip>
                                <vx-tooltip text="Edit" position="top">
                                    <vs-button class="mr-2" color="primary" icon-pack="feather" icon="icon-edit" @click="changeView('order_builder', item)" />
                                </vx-tooltip>
                                <vx-tooltip text="Create delivery" position="top">
                                    <vs-button class="mr-2" :color="deliveryStatusColor[item.delivery_status]" icon-pack="feather" icon="icon-package" @click="fastCreateDelivery(item)" :disabled="!canCreateDelivery(item.info)" />
                                </vx-tooltip>
                                <vx-tooltip text="Info" position="top">
                                    <vs-button class="mr-2" color="primary" icon-pack="feather" icon="icon-info" @click="showLeadInfo(item)" />
                                </vx-tooltip>
                                <vx-tooltip text="Send Whatsapp message" position="top">
                                    <vs-button class="mr-2" color="primary" @click="whatsappMessage(item)" icon-pack="feather" icon="icon-message-circle" />
                                </vx-tooltip>
                            </div>
                        </vs-td>
                        
                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <!--<vs-td class="column-no-padding"></vs-td>-->
                    <vs-td class="column-no-padding">{{data.length}}</vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <!--<vs-td class="column-no-padding"></vs-td>-->
                    <vs-td class="column-no-padding"></vs-td>
                    <!--<vs-td class="column-no-padding"></vs-td>-->
                    <vs-td class="column-no-padding">€ {{totalStats.price.toFixed(2)}}</vs-td>
                    <vs-td class="column-no-padding">{{totalStats.quantity}}</vs-td>
                    <vs-td class="column-no-padding">€ {{totalStats.total.toFixed(2)}}</vs-td>
                    <vs-td class="column-no-padding">{{totalStats.items}} items</vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <!--<vs-td class="column-no-padding"></vs-td>-->
                    <!--<vs-td class="column-no-padding"></vs-td>-->
                    <vs-td class="column-no-padding"></vs-td>

                </template>
            </vs-table>

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
                        <vs-select class="w-full" v-model="order.gateway">
                            <vs-select-item :key="gIndex" :value="gItem" :text="gItem" v-for="(gItem, gIndex) in gateways" />
                        </vs-select>
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

                <div class="vx-row w-full mb-3" v-for="(product, pIndex) in order.products" :key="pIndex">
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

                <div class="vx-row w-full mb-3" v-for="(acc, aIndex) in order.accessories" :key="aIndex">
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
                    <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-save" @click="updateLeadInfo(order)">Save</vs-button>
                    <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="updateLeadInfoAndCreateDelivery()" :disabled="!canCreateDelivery(order)">Create Delivery</vs-button>
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
        <vs-popup title="Add products" :active.sync="showPopup['add_product']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3 w-full font-bold">
                    <div class="vx-col md:w-1/6 w-full">
                    </div>
                    <div class="vx-col md:w-1/3 w-full">
                        Name
                    </div>
                    <div class="vx-col md:w-1/6 w-full">
                        Q.ty
                    </div>
                    <div class="vx-col md:w-1/3 w-full">
                        SKU
                    </div>
                </div>
                <div class="vx-row mb-3 w-full text-sm" v-for="(product, pIndex) in products" :key="pIndex">
                    <div class="vx-col md:w-1/6 w-full">
                        <vs-checkbox :value="hasProduct(product)" @change="toggleProduct(product)" />
                    </div>
                    <div class="vx-col md:w-1/3 w-full">
                        {{product.name}}
                    </div>
                    <div class="vx-col md:w-1/6 w-full">
                        {{product.quantity_stock}}
                    </div>
                    <div class="vx-col md:w-1/3 w-full">
                        {{product.sku}}
                    </div>
                </div>
            </div>
        </vs-popup>

        <!-- Lead info popup -->
        <vs-popup title="Lead info" :active.sync="showPopup['lead_info']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3" v-for="(value, key, index) in lead.delivery" :key="index">
                    <div class="vx-col md:w-1/2 w-full">
                        <b>{{key}}</b>
                    </div>
                    <div class="vx-col md:w-1/2 w-full">
                        {{value}}
                    </div>
                </div>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'

import Warehouse from 'warehouse'

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
    StatisticsCardLine,
  },
  data () {
    return {
      warehouse: null,

      maxItems: 150,
      view: 'leads',

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        status: '',
        product: '',
      },

      showPopup: {
        'notes': false,
        'add_product': false,
        'lead_info': false,
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
      },

      products: [],

      leadStatuses: [
        'confirmed',
        'no answer',
        'cancelled',
        'fake',
        'duplicate',
        'to recall',
        'to do',
        'waiting payment',
      ],
      leadStatusColor: {
        'confirmed'      : 'success',
        'no answer'      : '#555555',
        'cancelled'      : 'danger',
        'fake'           : '#cc8877',
        'duplicate'      : 'warning',
        'to recall'      : 'primary',
        'to do'          : '',
        'waiting payment': 'primary',
      },

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
    // TODO: Old code
    /*
    orderReviewProducts() {
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

      return toReturn
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
        this.leads = await this.warehouse.fetchLeads(this.filters)
        this.calculateStats()
        this.view = view
        this.$vs.loading.close()
      } else if (view == 'order_builder') {
        let lead = item
        this.loadOrderInfo(lead)
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
        status: '',
        product: '',
      }
      await this.filterDatesShortcut('this_month')
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
      let hh = String(dateObj.getHours() - 1).padStart(2, '0') // TODO: Why -1?
      let ii = String(dateObj.getMinutes()).padStart(2, '0')
      
      let formattedDate = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + ii
      return formattedDate
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
        let phone = lead.phone
        if (phone[0] != '+') {
          phone = '+39' + phone
        }
        let url = 'https://api.whatsapp.com/send?phone=' + phone
        this.openNewTab(url)
      }
    },

    async updateStatus(lead) {
      this.$vs.loading()

      let params = {
        status: this.newStatus,
      }
      await this.warehouse.updateLead(lead.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead\'s status updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()

      this.applyFilters()
    },
    async updateStatusOrderBuilder() {
      this.$vs.loading()

      this.order.status = this.newStatus
      let params = {
        status: this.newStatus,
      }
      await this.warehouse.updateLead(this.order.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead\'s status updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()
    },
    showNotes(lead) {
      this.lead = lead
      this.showPopup['notes'] = true
    },
    async updateNotes(lead) {
      this.$vs.loading()

      let params = {
        notes: lead.notes,
      }
      await this.warehouse.updateLead(lead.id, params)

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
      info.contact = lead.contact
      let params = {
        info: JSON.stringify(info),
      }
      await this.warehouse.updateLead(lead.id, params)

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
        street_address = loc.street_number + ' ' + loc.street
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
    mapProduct(p) {
      return {
        id: p.id,
        name: p.name,  
        sku: p.sku,
        quantity_stock: parseInt(p.quantity_stock),
        price: parseFloat(p.min_price),
      }
    },
    async fetchProducts() {
      let products = await this.warehouse.fetchProducts()

      this.products = []
      for (let i = 0; i < products.length; i++) {
        let p = products[i]
        this.products.push(this.mapProduct(p))
      }
    },
    removeProduct(index) {
      this.order.products.splice(index, 1)
      this.refreshOrderReviewTotal()
    },
    getProductById(id) {
      let p = null
      for (let i = 0; i < this.products.length; i++) {
        if (this.products[i].id == id) {
          p = this.products[i]
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
      await this.warehouse.updateLead(lead.id, params)
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

      let shippingPhone = ''
      let shippingEmail = ''
      if (o.shipping.hasOwnProperty('phone') && o.shipping.phone !== null) {
        shippingPhone = o.shipping.phone
      }
      if (o.shipping.hasOwnProperty('email') && o.shipping.email !== null) {
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
        customer_first_name:    o.first_name,
        customer_last_name:     o.last_name,
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
        created_at: this.formatDateTime(new Date()),
        products: products,
      }

      await this.warehouse.updateDelivery(o.lead_id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Lead updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()
    },
    async createDelivery() {
      this.$vs.loading()

      let o = this.order

      let params = {
        completed_at: this.formatDateTime(new Date()),
      }
      await this.warehouse.updateDelivery(o.lead_id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Delivery created',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()

      this.changeView('leads')
    },
    async updateLeadInfoAndCreateDelivery() {
      await this.updateLeadInfo(this.order)
      await this.createDelivery()
    },
    async fastCreateDelivery(lead) {
      //if (lead.delivery_status === 'sent') return
      if (!this.canCreateDelivery(lead.info)) return
      this.loadOrderInfo(lead)
      await this.createDelivery()
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
      }

      if (lead.info !== null && lead.info.hasOwnProperty('total') &&
          parseFloat(lead.info.total) > 0) {
        this.order.total = lead.info.total
      } else {
        this.refreshOrderReviewTotal()
      }
    },

    async updatePrice(lead) {
      this.$vs.loading()

      let info = lead.info
      info.price = this.newValue
      let params = {
        info: JSON.stringify(info),
      }
      await this.warehouse.updateLead(lead.id, params)

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
      await this.warehouse.updateLead(lead.id, params)

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
      await this.warehouse.updateLead(lead.id, params)

      this.$vs.loading.close()

      this.changeView('leads')
    },
    async deleteLead(lead) {
      this.$vs.loading()
      await this.warehouse.deleteLead(lead.id)
      this.$vs.loading.close()
      this.changeView('leads')
    },
    showAddProductPopup() {
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
    async showLeadInfo(lead) {
      this.$vs.loading()

      let delivery = await this.warehouse.getDelivery(lead.id)
      if (delivery !== null) {
        this.lead = lead
        this.lead.delivery = delivery
        this.showPopup['lead_info'] = true
      }

      this.$vs.loading.close()
    },
    canCreateDelivery(leadInfo) {
      let canCreate = true

      // Check if street address has number
      if (leadInfo !== null &&
          leadInfo.hasOwnProperty('shipping') && leadInfo.shipping !== null &&
          leadInfo.shipping.hasOwnProperty('street_address') && leadInfo.shipping.street_address !== null &&
          leadInfo.shipping.street_address !== '') {
        canCreate = canCreate && stringContainsNumber(leadInfo.shipping.street_address)
      } else {
        canCreate = false
      }

      return canCreate
    },
  },
  async created() {
    this.warehouse = new Warehouse(localStorage.getItem('accessToken'))

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

.items_popover {
  z-index: 999999 !important;
  position: fixed !important;
}

.carrier-icon {
  width: 24px !important;
}

</style>
