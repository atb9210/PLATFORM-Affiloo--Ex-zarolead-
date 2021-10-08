<template>
    <vx-card title="Products" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-plus"></vs-button>
        </p>

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

        <!--
        <vue-context ref="context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.context_menu.close();editForm(data.id);" class="flex items-center text-sm">
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close();deleteObject(data.id);" class="flex items-center text-sm">
                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Delete</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>
        -->

        <vs-table v-if="view == 'orders'" stripe multiple v-model="selectedOrders" :max-items="maxItems" pagination search :data="orders" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Id</vs-th>
                <vs-th sort-key="total">Amount</vs-th>
                <vs-th sort-key="status">Status</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                    <vs-td class="column-no-padding" :data="item.id">
                        {{item.id}}
                    </vs-td>

                    <!--
                    <vs-td class="column-no-padding" :data="item.name">
                        <div @contextmenu.prevent="$refs.context_menu.open($event, item)">
                            <vs-chip>
                                {{item.name}}
                            </vs-chip>
                        </div>
                    </vs-td>
                    -->
                    
                    <vs-td class="column-no-padding" :data="item.id">
                        {{item.currency_symbol}} {{item.total}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.status">
                        <vs-chip :color="orderStatusColor[item.status]">
                            {{item.status}}
                        </vs-chip>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'

export default {
  components: {
    VueContext,
  },
  data () {
    return {
      maxItems: 20,
      view: 'orders',

      orders: [],
      selectedOrders: [],

      customers: [],
      selectedCustomers: [],

      products: [],
      selectedProducts: [],

      orderStatuses: [
        'pending',
        'processing',
        'on-hold',
        'completed',
        'cancelled',
        'refunded',
        'failed',
      ],
      orderStatusColor: {
        'pending':    'primary',
        'processing': 'primary',
        'on-hold':    'warning',
        'completed':  'success',
        'cancelled':  'danger',
        'refunded':   'success',
        'failed':     'danger',
      },
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
      if (this.view == 'orders') {
        return this.orders
      } else if (this.view == 'customers') {
        return this.customers
      }
      return []
    },
  },
  methods: {
    onPageChanged(val) {
      this.currentPage = val
    },

    // Backend functions
    async fetchOrders() {
      let objects = []

      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        const response = await this.$http.get('/api/3rd/ecommerce/orders', { headers })
        if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
          objects = response.data.data
        }
        for (let i = 0; i < objects.length; i++) {
          let obj = objects[i]
          if (obj.hasOwnProperty('total')) {
            obj.total = parseFloat(obj.total)
          }
        }

        /*objects = objects.map((obj) => {
          if (obj.hasOwnProperty('total')) {
            obj.total = parseFloat(obj.total)
          }
          return obj
        })*/
      } catch (error) {
        // Nothing
      }

      return objects
    },
    async fetchCustomers() {
      let objects = []

      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        const response = await this.$http.get('/api/3rd/ecommerce/customers', { headers })
        if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
          objects = response.data.data
        }
      } catch (error) {
        // Nothing
      }

      return objects
    },
    async fetchProducts() {
      let objects = []

      try {
        let headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        }
        const response = await this.$http.get('/api/3rd/ecommerce/products', { headers })
        if (response.hasOwnProperty('data') && response.data.hasOwnProperty('data')) {
          objects = response.data.data
        }
      } catch (error) {
        // Nothing
      }

      return objects
    },
  },
  async created() {
    this.$vs.loading()

    this.orders = await this.fetchOrders()

    this.$vs.loading.close()
  },
}

</script>
