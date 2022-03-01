<template>
    <vx-card title="Dropin Shippings" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
        </p>

        <div v-if="showFilters" class="mb-12">
            <div class="vx-row mb-2">
                <div class="vx-col w-1/2">
                    <!--
                    <label>Categories</label>
                    <my-select v-model="filters.categories" :options="categories" :autocomplete="true" multiple class="w-full select-large" />
                    -->
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

        <vue-context ref="shipping_context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.shipping_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <!-- Shippings table -->
        <vs-table v-if="view == 'shippings'" stripe multiple v-model="selectedProducts" :max-items="maxItems" pagination search :data="shippings" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Order #</vs-th>
                <vs-th sort-key="created_at">Shipping Date</vs-th>
                <vs-th>Channel</vs-th>
                <vs-th>Customer</vs-th>
                <vs-th>Phone</vs-th>
                <vs-th>Payment</vs-th>
                <vs-th>Carrier</vs-th>
                <vs-th>Status</vs-th>
                <vs-th>Notes</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                    <vs-td class="column-no-padding" :data="item.id">
                        <div @contextmenu.prevent="$refs.shipping_context_menu.open($event, item)">
                            <vs-chip>
                                Order #{{item.id}}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.created_at">
                        {{item.created_at}}
                    </vs-td>

                    <vs-td class="column-no-padding">
                        Channel
                    </vs-td>

                    <vs-td class="column-no-padding">
                        Customer
                    </vs-td>

                    <vs-td class="column-no-padding">
                        Phone
                    </vs-td>

                    <vs-td class="column-no-padding">
                        Payment
                    </vs-td>

                    <vs-td class="column-no-padding">
                        Carrier
                    </vs-td>

                    <vs-td class="column-no-padding">
                        Status
                    </vs-td>

                    <vs-td class="column-no-padding">
                        Notes
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'

import Dropin from 'dropin'

function formatMoney(num) {
  let s = ''
  if (typeof(num) === 'number') {
    s = num.toFixed(2)
  } else {
    s = parseFloat(num.replace(',', '.')).toFixed(2)
  }
  return s.replace(',', '.')
}

export default {
  components: {
    VueContext,
  },
  data () {
    return {
      dropin: null,

      maxItems: 20,
      view: 'shippings',

      showFilters: false,
      filters: {
        categories: [],
      },

      shippings: [],
      selectedShippings: [],

      showPopup: {
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
      return this.shippings
    },
  },
  methods: {
    // Frontend functions
    onPageChanged(val) {
      this.currentPage = val
    },
    async changeView(view, parent = null) {
      this.$vs.loading()

      // TODO: HERE

      this.$vs.loading.close()
    },
    getObjectById(id) {
      for (let i = 0; i < this.orders.length; i++) {
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
        categories: [],
      }
      await this.applyFilters()
    },
  },
  async created() {
    this.dropin = new Dropin(localStorage.getItem('accessToken'))
  },
}

</script>

<style scoped>
.nextPrevProductImage {
  cursor: pointer;
}
.nextPrevProductImage:hover {
  filter: brightness(0.9);
}
</style>
