<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="My reports" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="generateReports"></vs-button>
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

        <vs-table stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th></vs-th>
                <vs-th sort-key="day">Date</vs-th>
                <vs-th sort-key="open_orders">Open Orders</vs-th>
                <vs-th sort-key="closed_orders">Closed Orders</vs-th>
                <vs-th sort-key="marketing">Marketing</vs-th>
                <vs-th sort-key="shipping">Shipping</vs-th>
                <vs-th sort-key="cogs">COGS</vs-th>
                <vs-th sort-key="order_value">Order Value</vs-th>
                <vs-th sort-key="revenue">Revenue</vs-th>
                <vs-th sort-key="net_revenue">Net Revenue</vs-th>
                <vs-th sort-key="vat">VAT</vs-th>
                <vs-th sort-key="profit">Profit</vs-th>
                <vs-th sort-key="cdv">COD Delivery Value</vs-th>
                <vs-th>NOTES</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                    <vs-td class="column-no-padding">
                        <vs-button size="small" radius :color="((item.profit > 0) ? 'success' : ((item.profit < 0) ? 'danger' : '#d9d9d9'))" type="filled" icon-pack="feather" :icon="((item.profit > 0) ? 'icon-plus' : ((item.profit < 0) ? 'icon-minus' : 'icon-refresh-cw'))"></vs-button>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].day">
                        {{data[indextr].day}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].open_orders">
                        <fast-editable-value
                            v-model="newValue"
                            @onEdit="newValue = data[indextr].open_orders"
                            @onUpdate="updateReport(item, 'open_orders')">
                                {{data[indextr].open_orders}}
                        </fast-editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].closed_orders">
                        <fast-editable-value
                            v-model="newValue"
                            @onEdit="newValue = data[indextr].closed_orders"
                            @onUpdate="updateReport(item, 'closed_orders')">
                                {{data[indextr].closed_orders}}
                        </fast-editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].marketing">
                        <fast-editable-value
                            v-model="newValue"
                            @onEdit="newValue = data[indextr].marketing"
                            @onUpdate="updateReport(item, 'marketing')">
                                {{data[indextr].marketing}}
                        </fast-editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].shipping">
                        <fast-editable-value
                            v-model="newValue"
                            @onEdit="newValue = data[indextr].shipping"
                            @onUpdate="updateReport(item, 'shipping')">
                                {{data[indextr].shipping}}
                        </fast-editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].cogs">
                        <fast-editable-value
                            v-model="newValue"
                            @onEdit="newValue = data[indextr].cogs"
                            @onUpdate="updateReport(item, 'cogs')">
                                {{data[indextr].cogs}}
                        </fast-editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].order_value">
                        <fast-editable-value
                            v-model="newValue"
                            @onEdit="newValue = data[indextr].order_value"
                            @onUpdate="updateReport(item, 'order_value')">
                                {{data[indextr].order_value}}
                        </fast-editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].revenue">
                        <fast-editable-value
                            v-model="newValue"
                            @onEdit="newValue = data[indextr].revenue"
                            @onUpdate="updateReport(item, 'revenue')">
                                {{data[indextr].revenue}}
                        </fast-editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].net_revenue">
                        <fast-editable-value
                            v-model="newValue"
                            @onEdit="newValue = data[indextr].net_revenue"
                            @onUpdate="updateReport(item, 'net_revenue')">
                                {{data[indextr].net_revenue}}
                        </fast-editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].vat">
                        <fast-editable-value
                            v-model="newValue"
                            @onEdit="newValue = data[indextr].vat"
                            @onUpdate="updateReport(item, 'vat')">
                                {{data[indextr].vat}}
                        </fast-editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].profit">
                        <fast-editable-value
                            v-model="newValue"
                            @onEdit="newValue = data[indextr].profit"
                            @onUpdate="updateReport(item, 'profit')">
                                {{data[indextr].profit}}
                        </fast-editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].cdv">
                        <fast-editable-value
                            v-model="newValue"
                            @onEdit="newValue = data[indextr].cdv"
                            @onUpdate="updateReport(item, 'cdv')">
                                {{data[indextr].cdv}}
                        </fast-editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding">
                        <vs-button style="width: 30px !important; height: 30px !important;" :color="(data[indextr].notes == '') ? 'primary' : 'warning'" :type="(data[indextr].notes == '') ? 'border' : 'filled'" icon-pack="feather" icon="icon-message-square"></vs-button>
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <vs-td class="column-no-padding">{{objects.length}}</vs-td>
                <vs-td class="column-no-padding">{{total_stats.open_orders}}</vs-td>
                <vs-td class="column-no-padding">{{total_stats.closed_orders}}</vs-td>
                <vs-td class="column-no-padding">€ {{total_stats.marketing.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{total_stats.shipping.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{total_stats.cogs.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{total_stats.order_value.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{total_stats.revenue.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{total_stats.net_revenue.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{total_stats.vat.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{total_stats.profit.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding">€ {{total_stats.cdv.toFixed(2)}}</vs-td>
                <vs-td class="column-no-padding"></vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>
    </vx-card>
</template>

<script>
import Dropin from 'dropin'

export default {
  components: {
  },
  data () {
    return {
      dropin: null,

      objects: [],
      selected: [],

      maxItems: 150,

      filters: {
        date_from: '',
        date_to: '',
      },
      showFilters: false,

      total_stats: {
        open_orders: 0,
        closed_orders: 0,
        marketing: 0.0,
        shipping: 0.0,
        cogs: 0.0,
        order_value: 0.0,
        revenue: 0.0,
        net_revenue: 0.0,
        vat: 0.0,
        profit: 0.0,
        cdv: 0.0,
      },

      newValue: '',
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
      for (let i = 0; i < this.objects.length; i++) {
        if (this.objects[i].id == id) {
          index = i
          break
        }
      }
      return index
    },
    getObjectById(id) {
      let index = this.getObjectIndexById(id)
      if (index >= 0) {
        return this.objects[index]
      } else {
        return null
      }
    },
    async retrieveObjects() {
      this.selected = []
      this.$vs.loading()

      this.objects = await this.dropin.fetchReports(this.filters.date_from, this.filters.date_to)
      this.calculateStats()

      this.$vs.loading.close()
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
      }
      this.filterDatesShortcut('this_month')
    },
    async applyFilters() {
      await this.retrieveObjects()
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
    calculateStats() {
      this.total_stats = {
        open_orders: 0,
        closed_orders: 0,
        marketing: 0.0,
        shipping: 0.0,
        cogs: 0.0,
        order_value: 0.0,
        revenue: 0.0,
        net_revenue: 0.0,
        vat: 0.0,
        profit: 0.0,
        cdv: 0.0,
      }

      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        this.total_stats.open_orders += parseInt(obj.open_orders)
        this.total_stats.closed_orders += parseInt(obj.closed_orders)
        this.total_stats.marketing += parseFloat(obj.marketing)
        this.total_stats.shipping += parseFloat(obj.shipping)
        this.total_stats.cogs += parseFloat(obj.cogs)
        this.total_stats.order_value += parseFloat(obj.order_value)
        this.total_stats.revenue += parseFloat(obj.revenue)
        this.total_stats.net_revenue += parseFloat(obj.net_revenue)
        this.total_stats.vat += parseFloat(obj.vat)
        this.total_stats.profit += parseFloat(obj.profit)
        this.total_stats.cdv += parseFloat(obj.cdv)
      }
    },
    async updateReport(report, field) {
      this.$vs.loading()

      let value = this.newValue
      if (field == 'open_orders' || field == 'closed_orders') {
        value = parseInt(this.newValue)
      } else {
        value = parseFloat(this.newValue)
      }

      let params = {}
      params[field] = value
      const result = await this.dropin.updateReport(report.id, params)

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Report updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
        report[field] = value
        this.calculateStats()
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the report',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    async generateReports() {
      this.$vs.loading()

      let days = []

      let d = new Date(this.filters.date_from)
      let dEnd = new Date(this.filters.date_to)
      while (d <= dEnd) {
        days.push(this.formatDate(d))
        d.setDate(d.getDate() + 1)
      }

      await this.dropin.generateReports(days)
      this.$vs.loading.close()

      await this.applyFilters()
    },
  },
  created() {
    this.dropin = new Dropin(localStorage.getItem('accessToken'))

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
}
</style>
