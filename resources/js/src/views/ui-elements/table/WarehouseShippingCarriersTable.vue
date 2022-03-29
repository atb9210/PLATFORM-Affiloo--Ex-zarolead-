<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Warehouse shipping carriers" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button v-if="view == 'carriers'" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>
        </p>

        <div v-if="view == 'carriers'">

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

            <vs-table stripe multiple v-model="selectedCarriers" :max-items="maxItems" pagination search :data="carriers" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="id">Id</vs-th>
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="slug">Slug</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                        <vs-td :data="item.id">
                            {{item.id}}
                        </vs-td>

                        <vs-td :data="item.name">
                            {{item.name}}
                        </vs-td>

                        <vs-td :data="item.slug">
                            {{item.slug}}
                        </vs-td>

                    </vs-tr>
                </template>
            </vs-table>

        </div>

    </vx-card>
</template>

<script>
import { WarehouseShippingCarrier } from 'zarolead'

export default {
  data () {
    return {
      warehouseShippingCarrierClient: null,

      maxItems: 20,
      view: 'carriers',

      carriers: [],
      selectedCarriers: [],

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
      if (this.view == 'carriers') {
        return this.carriers
      }
      return []
    },
    selected() {
      if (this.view == 'carriers') {
        return this.selectedCarriers
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
      if (view == 'carriers') {
        this.$vs.loading()
        this.selectedCarriers = []
        this.carriers = await this.warehouseShippingCarrierClient.fetch(this.filters)
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
        await this.warehouseShippingCarrierClient.delete(obj.id)
      }

      this.$vs.notify({
        title: 'Success',
        text: numSelected + ' Warehouse shipping carrier(s) deleted',
        iconPack: 'feather',
        icon: 'icon-user-x',
        color: 'success'
      })
      this.$vs.loading.close()
      await this.changeView('orders')
    },
  },
  async created() {
    this.warehouseShippingCarrierClient = new WarehouseShippingCarrier(this.$zarolead)

    await this.changeView('carriers')
  },
}

</script>
