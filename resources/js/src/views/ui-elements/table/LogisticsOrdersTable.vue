<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Logistics Orders" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="globalUpdateStatus"></vs-button>
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
                <vs-th sort-key="order_id">Order Id</vs-th>
                <vs-th sort-key="created_at">Date</vs-th>
                <vs-th sort-key="system.name">System</vs-th>
                <vs-th sort-key="info.product">Product</vs-th>
                <vs-th sort-key="info.quantity">Quantity</vs-th>
                <vs-th sort-key="info.amount">Price</vs-th>
                <vs-th sort-key="info.name">Customer</vs-th>
                <vs-th sort-key="info.fullname">Phone</vs-th>
                <vs-th sort-key="status">Status</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].order_id">
                        {{data[indextr].order_id}}
                    </vs-td>

                    <vs-td :data="data[indextr].created_at">
                        {{formatDateTime(new Date(data[indextr].created_at))}}
                    </vs-td>

                    <vs-td :data="data[indextr].system.name">
                        {{data[indextr].system.name}}
                    </vs-td>

                    <vs-td :data="data[indextr].info.product">
                        {{data[indextr].info.product}}
                    </vs-td>

                    <vs-td :data="data[indextr].info.quantity">
                        {{data[indextr].info.quantity}}
                    </vs-td>

                    <vs-td :data="data[indextr].info.amount">
                        € {{parseFloat(data[indextr].info.amount).toFixed(2)}}
                    </vs-td>

                    <vs-td :data="data[indextr].info.fullname">
                        {{data[indextr].info.fullname}}
                    </vs-td>

                    <vs-td :data="data[indextr].info.phone">
                        {{data[indextr].info.phone}}
                    </vs-td>

                    <vs-td :data="data[indextr].status">
                        {{data[indextr].status}}
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

    </vx-card>
</template>

<script>
export default {
  data () {
    return {
      objects: [],
      selected: [],
      deleted: [],
      updated: [],

      maxItems: 20,

      dateFormat: 'yyyy-MM-dd',
      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
      },
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
    onPageChanged(val) {
      this.currentPage = val
    },
    applyFilters() {
      this.$vs.loading()

      this.selected = []

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

      // Retrieve logistics orders
      this.$http.get('/api/3rd/network', { headers, params: data })
        .then((response) => {
          this.objects = response.data.data
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
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
      }
      this.filterDatesShortcut('this_month')
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
      let dd = String(dateObj.getDate()).padStart(2, '0');
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0'); //January is 0!
      let yyyy = dateObj.getFullYear();

      let hh = String(dateObj.getHours());
      let ii = String(dateObj.getMinutes());
      let ss = String(dateObj.getSeconds());
      
      let formattedDate = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + ii + ':' + ss;
      return formattedDate;
    },
    globalUpdateStatus() {
      if (this.objects.length == 0) return

      this.updated = []

      this.$vs.loading()

      this.objects.forEach((obj) => {
        this.$http.get('/api/3rd/network/orders/' + obj.id + '/update-status')
          .then((response) => {
            this.updated.push(obj.id)
            if (this.updated.length == this.objects.length) {
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Success',
                text: this.updated.length + ' Order(s) updated',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.selected = []
              this.applyFilters()
            }
          })
          .catch((error) => {
            // TODO: This is only for debug
            console.log(error)

            this.updated.push(obj.id)
            if (this.updated.length == this.objects.length) {
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Success',
                text: this.updated.length + ' Order(s) updated',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.selected = []
              this.applyFilters()
            }
          })
      })
    },
  },
  created() {
    this.resetFilters()
  },
  mounted () {
  }
}

</script>
