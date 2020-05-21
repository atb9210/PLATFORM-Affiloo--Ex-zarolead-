<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Orders" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters">Filters</vs-button> 
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="deleteOrders">Delete</vs-button> 
        </p>

        <div v-if="showFilters" class="mb-12">
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
            <div class="vx-row mb-2">
                <div class="vx-col w-full">
                    <label>Status</label>
                    <v-select :options="orderStatuses" v-model="filters.status" />
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col w-full">
                    <label>Product</label>
                    <v-select :options="orderProducts" v-model="filters.product" />
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
                <vs-th sort-key="email">Id</vs-th>
                <vs-th sort-key="created_at">Date</vs-th>
                <vs-th sort-key="product">Product</vs-th>
                <vs-th sort-key="username">Name</vs-th>
                <vs-th sort-key="roles">Phone</vs-th>
                <vs-th sort-key="quantity">Q.ty</vs-th>
                <vs-th sort-key="commission">Commission</vs-th>
                <vs-th sort-key="status">Status</vs-th>
                <vs-th sort-key="network">Network</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td :data="data[indextr].created_at">
                        {{data[indextr].created_at}}
                    </vs-td>

                    <vs-td :data="data[indextr].product">
                        {{data[indextr].product}}
                    </vs-td>

                    <vs-td :data="data[indextr].name">
                        {{data[indextr].name}}
                    </vs-td>

                    <vs-td :data="data[indextr].phone">
                        {{data[indextr].phone}}
                    </vs-td>

                    <vs-td :data="data[indextr].quantity">
                        {{data[indextr].quantity}}
                    </vs-td>

                    <vs-td :data="data[indextr].commission">
                        {{data[indextr].commission}}
                    </vs-td>

                    <vs-td :data="data[indextr].status">
                        <span class="flex items-center px-2 py-1 rounded">
                            <!--<div class="h-3 w-3 rounded-full mr-2" :class="'bg-' + statusColors[data[indextr].status]"></div>{{data[indextr].status}}-->
                            <vs-chip :color="statusColors[data[indextr].status]">{{data[indextr].status}}</vs-chip>
                        </span>
                    </vs-td>

                    <vs-td :data="data[indextr].network">
                        {{data[indextr].network}}
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

    </vx-card>
</template>

<script>
import vSelect from 'vue-select'

import '@sass/vuexy/extraComponents/agGridStyleOverride.scss'

export default {
  components: {
    'v-select': vSelect,
  },
  props: {
  },
  data () {
    return {
      objects: [],
      selected: [],
      deleted: [],

      maxItems: 20,

      statusColors: {
          'waiting': 'primary',
          'confirmed': 'success',
          'not responding': 'warning',
          'refused': 'danger',
          'not sent': 'danger',
          'double': 'warning',
      },

      dateFormat: 'yyyy-MM-dd',
      filters: {
        date_from: '',
        date_to: '',
        status: '',
        product: '',
      },
      showFilters: false,
      orderStatuses: [
        { id: 1, label: 'waiting' },
        { id: 2, label: 'confirmed' },
        { id: 3, label: 'not responding' },
        { id: 4, label: 'refused' },
        { id: 5, label: 'not sent' },
        { id: 6, label: 'double' },
      ],
      orderProducts: [],
    }
  },
  watch: {
    '$store.state.windowWidth' (val) {
      if (val <= 576) {
        this.maxPageNumbers = 4
        this.gridOptions.columnApi.setColumnPinned('email', null)
      } else this.gridOptions.columnApi.setColumnPinned('email', 'left')
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
    }
  },
  methods: {
    retrieveObjects() {
      this.selected = []
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Retrieve orders
      this.$http.get('/api/v1/orders', { headers })
        .then((response) => {
          this.objects = response.data.data
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
    onPageChanged(val) {
      this.currentPage = val
    },
    updateSearchQuery (val) {
      this.gridApi.setQuickFilter(val)
    },
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        status: '',
        product: '',
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
      if (this.filters.status != '') {
        data['filter_status'] = this.filters.status.id
      }
      if (this.filters.product != '') {
        data['filter_product'] = this.filters.product.id
      }

      // Retrieve orders
      this.$http.get('/api/v1/orders', { headers, params: data })
        .then((response) => {
            console.log(response)
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
    deleteOrders() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      this.deleted = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.selected.forEach((order) => {
        this.$http.delete('/api/v1/orders/' + order.id, { headers })
          .then((response) => {
            this.deleted.push(order.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Order(s) deleted',
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
  },
  created() {
    this.$vs.loading()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve products list
    this.$http.get('/api/v1/products', { headers })
      .then((response) => {
        this.orderProducts = []
        response.data.data.forEach((product) => {
          this.orderProducts.push({
            id: product.id,
            label: product.name,
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

    // Retrieve orders
    this.retrieveObjects()
  },
}

</script>
