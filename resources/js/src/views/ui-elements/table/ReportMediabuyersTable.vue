<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Mediabuyers report" code-toggler>

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
            <div class="vx-row mb-2">
                <div class="vx-col w-full">
                    <label>User</label>
                    <v-select :options="users" v-model="filters.user" />
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
                <vs-th sort-key="user.id">User Id</vs-th>
                <vs-th sort-key="user.email">User Email</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="commission">Commission</vs-th>
                <vs-th sort-key="cost">Cost</vs-th>
                <vs-th sort-key="extra_cost">Extra Cost</vs-th>
                <vs-th sort-key="profit">Profit</vs-th>
                <vs-th sort-key="roi">ROI</vs-th>
                <vs-th>Approved</vs-th>
                <vs-th>Net Profit</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].user.id">
                        {{data[indextr].user.id}}
                    </vs-td>

                    <vs-td :data="data[indextr].user.email">
                        {{data[indextr].user.email}}
                    </vs-td>

                    <vs-td :data="data[indextr].leads">
                        {{data[indextr].leads}}
                    </vs-td>

                    <vs-td :data="data[indextr].commission">
                        {{data[indextr].commission}} €
                    </vs-td>

                    <vs-td :data="data[indextr].cost">
                        {{data[indextr].cost}} €
                    </vs-td>

                    <vs-td :data="data[indextr].extra_cost">
                        {{data[indextr].extra_cost}} €
                    </vs-td>

                    <vs-td :data="data[indextr].profit">
                        <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">€ {{data[indextr].profit}}</vs-chip>
                    </vs-td>

                    <vs-td :data="data[indextr].roi">
                        <vs-chip transparent :color="(data[indextr].roi > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">{{data[indextr].roi}} %</vs-chip>
                    </vs-td>

                    <vs-td>
                        0,00 €
                    </vs-td>

                    <vs-td>
                        0,00 €
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td>
                    {{total_stats.leads}}
                </vs-td>
                <vs-td>
                    {{total_stats.commission}} €
                </vs-td>
                <vs-td>
                    {{total_stats.cost}} €
                </vs-td>
                <vs-td>
                    {{total_stats.extra_cost}} €
                </vs-td>
                <vs-td>
                    <vs-chip transparent :color="(total_stats.profit > 0) ? 'success' : 'danger'">€ {{total_stats.profit}}</vs-chip>
                </vs-td>
                <vs-td>
                    <vs-chip transparent :color="(total_stats.roi > 0) ? 'success' : 'danger'">{{total_stats.roi}} %</vs-chip>
                </vs-td>
                <vs-td>
                    0,00 €
                </vs-td>
                <vs-td>
                    0,00 €
                </vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>
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
      objectsIds: [],

      selected: [],
      deleted: [],

      maxItems: 20,

      total_stats: {
        leads: 0,
        commission: 0.0,
        cost: 0.0,
        extra_cost: 0.0,
        profit: 0.0,
        roi: 0.0,
      },

      filters: {
        date_from: '',
        date_to: '',
        user: '',
        role: '',
      },
      showFilters: false,

      users: [],
      roles: [
        { id: 1, label: 'developer' },
        { id: 2, label: 'admin' },
        { id: 3, label: 'mediabuyer' },
        { id: 4, label: 'affiliate' },
        { id: 5, label: 'advertiser' },
        { id: 6, label: 'accountant' },
      ],
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
        if (obj.user.id == id) {
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
      let data = {}
      if (this.filters.date_from != '') {
        data['filter_date_from'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        data['filter_date_to'] = this.filters.date_to
      }
      if (this.filters.user != '') {
        data['filter_user'] = this.filters.user.id
      }
      data['filter_roles'] = [3, 7]

      // Retrieve mediabuyers' report
      this.$http.get('/api/v1/reports/users', { headers, params: data })
        .then((response) => {
          this.objects = response.data.data
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
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        user: '',
        role: '',
      }
      this.filterDatesShortcut('this_month')
    },
    applyFilters() {
      this.retrieveObjects()
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
        leads: 0,
        cost: 0,
        extra_cost: 0,
        commission: 0,
        profit: 0,
        roi: 0,
      }

      for (var i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        let roi = parseFloat((obj.commission > 0 && obj.cost == 0 && obj.extra_cost == 0) ? 9999 : (obj.profit/(obj.cost + obj.extra_cost))*100)

        this.total_stats.leads += parseInt(obj.leads)
        this.total_stats.cost += parseFloat(obj.cost),
        this.total_stats.extra_cost += parseFloat(obj.extra_cost),
        this.total_stats.commission += parseFloat(obj.commission),
        this.total_stats.profit += parseFloat(obj.profit),

        // Format calculated stats
        roi = roi.toFixed(2)

        this.objects[i].roi = roi
      }

      // Total stats
      let obj = this.total_stats

      let roi = parseFloat((obj.commission > 0 && obj.cost == 0 && obj.extra_cost == 0) ? 9999 : (obj.profit/(obj.cost + obj.extra_cost))*100)

      // Format calculated stats
      roi = roi.toFixed(2)

      this.total_stats.roi = roi

      this.total_stats.cost = this.total_stats.cost.toFixed(2)
      this.total_stats.extra_cost = this.total_stats.extra_cost.toFixed(2)
      this.total_stats.commission = this.total_stats.commission.toFixed(2)
      this.total_stats.profit = this.total_stats.profit.toFixed(2)
    },
  },
  created() {
    this.resetFilters()

    this.$vs.loading()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve users
    this.$http.get('/api/v1/users', { headers })
      .then((response) => {
        this.users = []
        response.data.data.forEach((user) => {
          if (user.roles.includes("mediabuyer") || user.roles.includes("buyer")) {
            this.users.push({
              id: user.id,
              label: user.email,
            })
          }
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
