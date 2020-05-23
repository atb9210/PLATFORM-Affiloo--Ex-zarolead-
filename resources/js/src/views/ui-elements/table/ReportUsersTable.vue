<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Users report" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters">Filters</vs-button> 
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
                    <label>User</label>
                    <v-select :options="users" v-model="filters.user" />
                </div>
            </div>

            <div class="vx-row mb-2">
                <div class="vx-col w-full">
                    <label>Role</label>
                    <v-select :options="roles" v-model="filters.role" />
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
                <vs-th sort-key="user.id">User Id</vs-th>
                <vs-th sort-key="user.email">User Email</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="commission">Commission</vs-th>
                <vs-th sort-key="sub_commission">Sub commission</vs-th>
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

                    <vs-td :data="data[indextr].commissions">
                        {{data[indextr].commissions}} €
                    </vs-td>

                    <vs-td :data="data[indextr].sub_commissions">
                        {{data[indextr].sub_commissions}} €
                    </vs-td>

                </vs-tr>
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
      selected: [],
      deleted: [],

      maxItems: 20,

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
      if (this.filters.role != '') {
        data['filter_role'] = this.filters.role.id
      }

      // Retrieve report
      this.$http.get('/api/v1/reports/users', { headers, params: data })
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
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        user: '',
        role: '',
      }
      this.applyFilters()
    },
    applyFilters() {
      this.retrieveObjects()
    },
  },
  created() {
    this.retrieveObjects()

    this.$vs.loading()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    this.$http.get('/api/v1/users', { headers })
      .then((response) => {
        this.users = []
        response.data.data.forEach((user) => {
          this.users.push({
            id: user.id,
            label: user.email,
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
