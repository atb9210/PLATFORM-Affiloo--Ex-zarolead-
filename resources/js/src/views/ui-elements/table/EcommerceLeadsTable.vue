<template>
    <vx-card title="Leads" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
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
            
            <div class="vx-row mt-6">
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

        <vue-context ref="lead_context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.lead_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <!-- Customers table -->
        <vs-table v-if="view == 'leads'" stripe multiple v-model="selectedLeads" :max-items="maxItems" pagination search :data="leads" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="full_name">Name</vs-th>
                <vs-th sort-key="phone">Phone</vs-th>
                <vs-th sort-key="sku">SKU</vs-th>
                <vs-th sort-key="price">Price</vs-th>
                <vs-th sort-key="quantity">Quantity</vs-th>
                <vs-th sort-key="total">Total</vs-th>
                <vs-th sort-key="contact">Contact</vs-th>
                <vs-th sort-key="status">Status</vs-th>
                <vs-th sort-key="notes">Notes</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                    <vs-td class="column-no-padding" :data="item.name">
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

                    <vs-td class="column-no-padding" :data="item.sku">
                        {{item.sku}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.price">
                        € {{item.price.toFixed(2)}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.quantity">
                        {{item.quantity}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.total">
                        € {{item.total.toFixed(2)}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.contact">
                        <vs-checkbox disabled="true" v-model="data[indextr].contact" />
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.status">
                        <fast-editable-value-select
                            :options="leadStatuses"
                            v-model="newStatus"
                            @onEdit="newStatus = data[indextr].status"
                            @onUpdate="updateStatus(data[indextr])">
                              <vs-chip :color="leadStatusColor[item.status]">
                                  {{item.status}}
                              </vs-chip>
                        </fast-editable-value-select>
                    </vs-td>

                    <vs-td class="column-no-padding">
                        <div class="flex items-center">
                            <vx-tooltip :text="item.notes" position="top">
                                <vs-button class="mr-2" :color="item.notes == '' ? 'primary' : 'warning'" @click="showNotes(item)" icon-pack="feather" icon="icon-flag" />
                            </vx-tooltip>
                            <vx-tooltip text="Create order" position="top">
                                <vs-button class="mr-2" color="primary" icon-pack="feather" icon="icon-package" />
                            </vx-tooltip>
                            <vx-tooltip text="Send Whatsapp message" position="top">
                                <vs-button class="mr-2" color="primary" @click="whatsappMessage(item)" icon-pack="feather" icon="icon-message-circle" />
                            </vx-tooltip>
                        </div>
                    </vs-td>
                    
                </vs-tr>
            </template>
        </vs-table>

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

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'

import Ecommerce from 'ecommerce'

export default {
  components: {
    VueContext,
    StatisticsCardLine,
  },
  data () {
    return {
      ecommerce: null,

      maxItems: 20,
      view: 'leads',

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
      },

      showPopup: {
        'notes': false,
      },

      leads: [],
      selectedLeads: [],
      lead: {},

      leadStatuses: [
        'confirmed',
        'no answer',
        'cancelled',
      ],
      leadStatusColor: {
        'confirmed': 'success',
        'no answer': 'warning',
        'cancelled': 'danger',
      },

      newStatus: '',
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
  },
  methods: {
    // Frontend functions
    onPageChanged(val) {
      this.currentPage = val
    },
    async changeView(view) {
      this.$vs.loading()

      if (view == 'leads') {
        this.leads = await this.ecommerce.fetchLeads()
        this.view = view
      }

      this.$vs.loading.close()
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
      /* TODO */
      return
    },
    showNotes(lead) {
      this.lead = lead
      this.showPopup['notes'] = true
    },
    async updateNotes(lead) {
      /* TODO */
      return
    }
  },
  async created() {
    this.ecommerce = new Ecommerce(localStorage.getItem('accessToken'))

    this.$vs.loading()

    await this.resetFilters()

    this.$vs.loading.close()
  },
}

</script>
