<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Warehouse shippings" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button v-if="view == 'shippings'" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>
        </p>

        <div v-if="view == 'shippings'">

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

            <vs-table stripe multiple v-model="selectedShippings" :max-items="maxItems" pagination search :data="shippings" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="id">Id</vs-th>
                    <vs-th sort-key="carrier_name">Carrier</vs-th>
                    <vs-th sort-key="tracking_number">Tracking</vs-th>
                    <vs-th sort-key="status">Status</vs-th>
                    <vs-th sort-key="carrier_status">Carrier Status</vs-th>
                    <vs-th sort-key="created_at">Start Date</vs-th>
                    <vs-th sort-key="delivered_at">Delivery Date</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                        <vs-td :data="item.id">
                            {{item.id}}
                        </vs-td>

                        <vs-td :data="item.carrier_name">
                            {{item.carrier_name}}
                        </vs-td>

                        <vs-td :data="item.status">
                            <span v-if="item.tracking_url !== null && item.tracking_url != ''">
                                <a :href="item.tracking_url">
                                    {{item.tracking_number}}
                                </a>
                            </span>
                            <span v-else>
                                {{item.tracking_number}}
                            </span>
                        </vs-td>

                        <vs-td :data="item.status">
                            {{item.status}}
                        </vs-td>

                        <vs-td :data="item.carrier_status">
                            {{item.carrier_status}}
                        </vs-td>

                        <vs-td :data="item.created_at">
                            {{item.created_at}}
                        </vs-td>

                        <vs-td :data="item.delivered_at">
                            {{item.delivered_at}}
                        </vs-td>

                    </vs-tr>
                </template>
            </vs-table>

        </div>

    </vx-card>
</template>

<script>
import { WarehouseShipping } from 'zarolead'

export default {
  data () {
    return {
      warehouseShippingClient: null,

      maxItems: 20,
      view: 'shippings',

      shippings: [],
      selectedShippings: [],

      filters: {
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
    objects() {
      if (this.view == 'shippings') {
        return this.shippings
      }
      return []
    },
    selected() {
      if (this.view == 'shippings') {
        return this.selectedShippings
      }
      return []
    },
  },
  methods: {
    onPageChanged(val) {
      this.currentPage = val
    },
    getObjectById(id, objects = null) {
      if (objects === null) {
        objects = this.objects
      }
      for (let i = 0; i < objects.length; i++) {
        if (objects[i].hasOwnProperty('id') && objects[i].id == id) {
          return objects[i]
        }
      }
      return null
    },
    async changeView(view, value = null) {
      if (view == 'shippings') {
        this.$vs.loading()
        this.selectedShippings = []
        this.shippings = await this.warehouseShippingClient.fetch(this.filters)
        for (let i = 0; i < this.shippings.length; i++) {
          let obj = this.shippings[i]

          obj.carrier_name = obj.carrier.name
          obj.carrier_status = ''

          this.shippings[i] = obj
        }
        this.$vs.loading.close()
      }

      this.view = view
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
      let numSelected = this.selected.length
      if (numSelected == 0) return

      this.$vs.loading()

      for (let i = 0; i < numSelected; i++) {
        let obj = this.selected[i]
        await this.warehouseShippingClient.delete(obj.id)
      }

      this.$vs.notify({
        title: 'Success',
        text: numSelected + ' Warehouse shipping(s) deleted',
        iconPack: 'feather',
        icon: 'icon-user-x',
        color: 'success'
      })
      this.$vs.loading.close()
      await this.changeView('orders')
    },
  },
  async created() {
    this.warehouseShippingClient = new WarehouseShipping(this.$zarolead)

    await this.changeView('shippings')
  },
}

</script>
