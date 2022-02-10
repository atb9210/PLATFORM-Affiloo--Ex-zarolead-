<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Facebook Monitors" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>
        </p>

        <div v-if="showFilters" class="mb-12">
            <div class="vx-row mb-2">
                <div class="vx-col md:w-1/2 w-full">
                    <label>Advertiser</label>
                    <my-select v-model="filters.advertiser" :options="advertisers" :autocomplete="true" class="w-full select-large" />
                </div>
                <div class="vx-col md:w-1/2 w-full">
                    <label>Form</label>
                    <my-select v-model="filters.form" :options="forms" :autocomplete="true" class="w-full select-large" />
                </div>
            </div>
            <div class="vx-row mt-6">
                <div class="vx-col w-full">
                    <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
                    <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
                </div>
            </div>
        </div>

        <div>
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
                    <vs-th sort-key="id">Id</vs-th>
                    <vs-th sort-key="advertiser.name">Advertiser</vs-th>
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="views">Views</vs-th>
                    <vs-th sort-key="spent">Spent</vs-th>
                    <vs-th sort-key="conversions">Conversions</vs-th>
                    <vs-th sort-key="conversions_value">Commissions</vs-th>
                    <vs-th sort-key="profit">Profit</vs-th>
                    <vs-th sort-key="roi">ROI</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td :data="data[indextr].id">
                            {{data[indextr].id}}
                        </vs-td>

                        <vs-td :data="data[indextr].advertiser.name">
                            {{data[indextr].advertiser.name}}
                        </vs-td>

                        <vs-td :data="data[indextr].name">
                            {{data[indextr].name}}
                        </vs-td>

                        <vs-td :data="data[indextr].views">
                            {{data[indextr].views}}
                        </vs-td>

                        <vs-td :data="data[indextr].spent">
                            € {{data[indextr].spent.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].conversions">
                            {{data[indextr].conversions}}
                        </vs-td>

                        <vs-td :data="data[indextr].conversions_value">
                            € {{data[indextr].conversions_value.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].profit">
                            € {{data[indextr].profit.toFixed(2)}}
                        </vs-td>

                        <vs-td :data="data[indextr].roi">
                            {{data[indextr].roi.toFixed(2)}}%
                        </vs-td>

                    </vs-tr>

                    <!-- TOTAL STATS -->
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding"></vs-td>
                    <vs-td class="column-no-padding">{{data.length}}</vs-td>
                    <vs-td class="column-no-padding">
                        {{totalStats.views}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{totalStats.spent.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{totalStats.conversions}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{totalStats.conversions_value.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        € {{totalStats.profit.toFixed(2)}}
                    </vs-td>
                    <vs-td class="column-no-padding">
                        {{totalStats.roi.toFixed(2)}}%
                    </vs-td>
                    <!-- END TOTAL STATS-->

                </template>
            </vs-table>
        </div>

    </vx-card>
</template>

<script>
import Facebook from 'facebook'

export default {
  data () {
    return {
      maxItems: 20,

      objects: [],
      selected: [],

      showFilters: false,
      filters: {
        advertiser: '',
        form: '',
      },

      totalStats: {
        views: 0,
        spent: 0.0,
        conversions: 0,
        conversions_value: 0.0,
        profit: 0.0,
        roi: 0.0,
      },

      advertisers: [],
      forms: [],
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
    onPageChanged(val) {
      this.currentPage = val
    },
    async applyFilters() {
      this.$vs.loading()

      this.selected = []
      this.objects = []

      let filters = {}
      if (this.filters.advertiser != '') {
        filters.filter_advertiser = this.filters.advertiser.id
      }
      if (this.filters.form != '') {
        filters.filter_form = this.filters.form.id
      }
      filters.reports = 1

      this.objects = await this.facebook.fetchMonitors(filters)

      this.calculateStats()

      this.$vs.loading.close()
    },
    async resetFilters() {
      this.filters = {
        advertiser: '',
        form: '',
      }
      await this.applyFilters()
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
    async deleteSelected() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      for (let i = 0; i < this.selected.length; i++) {
        await this.facebook.deleteMonitor(this.selected[i].id)
      }

      this.$vs.loading.close()
    },
    async fetchForms() {
      let objects = []

      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        const response = await this.$http.get('/api/v1/forms', { headers })
        objects = response.data.data
      } catch (error) {
        // Nothing
      }

      return objects
    },
    calculateStats() {
      let views = 0
      let spent = 0
      let conversions = 0
      let conversions_value = 0
      let profit = 0.0
      let roi = 0.0

      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        views += obj.views
        spent += obj.spent
        conversions += obj.conversions
        conversions_value += obj.conversions_value
        profit += obj.profit
      }

      roi = (spent > 0) ? (((profit/spent))*100) : 0

      this.totalStats = {
        views: views,
        spent: spent,
        conversions: conversions,
        conversions_value: conversions_value,
        profit: profit,
        roi: roi,
      }
    },
  },
  async created() {
    this.facebook = new Facebook(localStorage.getItem('accessToken'))
    await this.resetFilters()

    this.$vs.loading()

    this.advertisers = await this.facebook.fetchAdvertisers()

    // TODO: Fetch this now or when trying to create a new monitor?
    this.forms = await this.fetchForms()

    this.$vs.loading.close()
  },
}

</script>

<style scoped>
.column-no-padding {
  padding-top: 0px !important;
  padding-bottom: 0px !important;
  white-space: nowrap;
}
.disabled-object {
  color: #363636 !important;
}
</style>
