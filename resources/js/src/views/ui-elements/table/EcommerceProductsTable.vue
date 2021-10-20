<template>
    <vx-card title="Products" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button v-if="view == 'variations'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="changeView('products')"></vs-button>
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

        <vue-context ref="product_context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.product_context_menu.close();showVariations(data);" class="flex items-center text-sm">
                <feather-icon icon="GitBranchIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Variations</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.product_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <!-- Products table -->
        <vs-table v-if="view == 'products'" stripe multiple v-model="selectedProducts" :max-items="maxItems" pagination search :data="products" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Id</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="sku">SKU</vs-th>
                <vs-th sort-key="shipping_class">Shipping Class</vs-th>
                <vs-th sort-key="regular_price">Regular Price</vs-th>
                <vs-th sort-key="sale_price">Sale Price</vs-th>
                <vs-th sort-key="cog_cost">Cost</vs-th>
                <vs-th sort-key="status">Status</vs-th>
                <vs-th sort-key="stock_status">Stock Status</vs-th>
                <vs-th sort-key="">Info</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                    <vs-td class="column-no-padding" :data="item.id">
                        {{item.id}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.name">
                        <div @contextmenu.prevent="$refs.product_context_menu.open($event, item)">
                            <vs-chip>
                                {{item.name}}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.sku">
                        {{item.sku}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.shipping_class">
                        {{item.shipping_class}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.regular_price">
                        <editable-value
                            v-model="newPrice"
                            @onEdit="newPrice = data[indextr].regular_price"
                            @onUpdate="updateRegularPrice(data[indextr])">
                              € {{ item.regular_price.toFixed(2) }}
                        </editable-value>
                    </vs-td>
                    
                    <vs-td class="column-no-padding" :data="item.sale_price">
                        <editable-value
                            v-model="newPrice"
                            @onEdit="newPrice = data[indextr].sale_price"
                            @onUpdate="updatePrice(data[indextr])">
                              € {{ item.sale_price.toFixed(2) }}
                        </editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.cog_cost">
                        <editable-value
                            v-model="newPrice"
                            @onEdit="newPrice = data[indextr].cog_cost"
                            @onUpdate="updateCost(data[indextr])">
                              € {{ item.cog_cost.toFixed(2) }}
                        </editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.status">
                        <vs-chip :color="productStatusColor[item.status]">
                            {{item.status}}
                        </vs-chip>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.stock_status">
                        <vs-chip :color="productStockStatusColor[item.stock_status]">
                            {{item.stock_status}}
                        </vs-chip>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.stock_status">
                        <div class="flex items-center">
                            <vs-avatar color="primary" size="24px" @click="showInfo(item)" icon-pack="feather" icon="icon-help-circle" />
                        </div>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Variations table -->
        <vs-table v-if="view == 'variations'" stripe multiple v-model="selectedVariations" :max-items="maxItems" pagination search :data="variations" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Id</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="regular_price">Regular Price</vs-th>
                <vs-th sort-key="sale_price">Sale Price</vs-th>
                <vs-th sort-key="cog_cost">Cost</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

                    <vs-td class="column-no-padding" :data="item.id">
                        {{item.id}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.name">
                        {{item.name}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.regular_price">
                        <editable-value
                            v-model="newPrice"
                            @onEdit="newPrice = data[indextr].regular_price"
                            @onUpdate="updateVariationRegularPrice(data[indextr])">
                              € {{ item.regular_price.toFixed(2) }}
                        </editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.sale_price">
                        <editable-value
                            v-model="newPrice"
                            @onEdit="newPrice = data[indextr].sale_price"
                            @onUpdate="updateVariationPrice(data[indextr])">
                              € {{ item.sale_price.toFixed(2) }}
                        </editable-value>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.cog_cost">
                        <editable-value
                            v-model="newPrice"
                            @onEdit="newPrice = data[indextr].cog_cost"
                            @onUpdate="updateVariationCost(data[indextr])">
                              € {{ item.cog_cost.toFixed(2) }}
                        </editable-value>
                    </vs-td>
                    
                </vs-tr>
            </template>
        </vs-table>

        <!-- Product info popup -->
        <vs-popup title="Product info" :active.sync="showPopup['info']">

            <div v-if="product !== null">
                <div class="vx-row mb-6 flex items-center" v-if="product.hasOwnProperty('name')">
                    <vs-button class="mr-auto ml-auto">{{ product.name }}</vs-button>
                </div>

                <div class="vx-row mb-6">
                    <div v-if="product.hasOwnProperty('images') && product.images.length > 0" class="vx-col sm:w-1/4 w-full mt-2" :key="imgIndex" v-for="(image, imgIndex) in product.images">
                        <img :src="image.src" class="w-full">
                    </div>
                    <div v-else class="w-full mt-2">
                        <span>This product has no images.</span>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        Length: <span>{{ product.dimensions.length }}</span>
                    </div>
                    <div class="vx-col w-full">
                        Width: <span>{{ product.dimensions.width }}</span>
                    </div>
                    <div class="vx-col w-full">
                        Height: <span>{{ product.dimensions.height }}</span>
                    </div>
                    <div class="vx-col w-full">
                        Weight: <span>{{ product.weight }}</span>
                    </div>
                </div>

            </div>

        </vs-popup>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'

import Ecommerce from 'ecommerce'

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
    StatisticsCardLine,
  },
  data () {
    return {
      ecommerce: null,

      maxItems: 20,
      view: 'products',

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
      },

      showPopup: {
        'info': false,
      },

      products: [],
      selectedProducts: [],

      product: null,

      variations: [],
      selectedVariations: [],

      productStatuses: [
        'publish',
        'pending',
        'draft',
      ],
      productStatusColor: {
        'publish': 'success',
        'pending': 'primary',
        'draft':   'warning',
      },

      productStockStatuses: [
        'instock',
        'outofstock',
      ],
      productStockStatusColor: {
        'instock':    'success',
        'outofstock': 'danger',
      },

      newPrice: 0.0,
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
      return this.products
    },
  },
  methods: {
    // Frontend functions
    onPageChanged(val) {
      this.currentPage = val
    },
    async changeView(view, parent = null) {
      this.$vs.loading()

      if (view == 'products') {
        this.products = await this.ecommerce.fetchProducts()
        this.view = view
      } else if (view == 'variations') {
        this.variations = await this.ecommerce.fetchProductVariations(parent.id)
        this.product = parent
      }

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
        date_from: '',
        date_to: '',
      }
      await this.filterDatesShortcut('this_month')
    },
    async filterDatesShortcut(period) {
      let invalidPeriod = false

      switch (period) {
        case 'today': {
          let today = new Date()
          this.filters.date_from = this.formatDate(today)
          this.filters.date_to = this.formatDate(today)
        } break
        case 'yesterday': {
          let dateObj = new Date()
          dateObj.setDate(dateObj.getDate() - 1)
          this.filters.date_from = this.formatDate(dateObj)
          this.filters.date_to = this.formatDate(dateObj)
        } break
        case 'this_month': {
          let dateObj = new Date()
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1)
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0)
          this.filters.date_from = this.formatDate(fromObj)
          this.filters.date_to = this.formatDate(toObj)
        } break
        case 'last_month': {
          let dateObj = new Date()
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth() - 1, 1)
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 0)
          this.filters.date_from = this.formatDate(fromObj)
          this.filters.date_to = this.formatDate(toObj)
        } break
        default: {
          invalidPeriod = true
        } break
      }

      if(!invalidPeriod) {
        await this.applyFilters()
      }
    },
    formatDate(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0')
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0')
      let yyyy = dateObj.getFullYear()
      
      let formattedDate = yyyy + '-' + mm + '-' + dd
      return formattedDate
    },
    async showInfo(product) {
      this.product = product
      this.showPopup['info'] = true
    },

    async updateRegularPrice(product) {
      this.$vs.loading()

      let priceParam = formatMoney(this.newPrice)
      let params = {
        regular_price: priceParam,
      }

      if (product.type === 'variable') {
        await this.ecommerce.updateProductVariations(product.id, params)
      }
      await this.ecommerce.updateProduct(product.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Product regular price updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()

      this.applyFilters()
    },
    async updatePrice(product) {
      this.$vs.loading()

      let priceParam = formatMoney(this.newPrice)
      let params = {
        sale_price: product.sale_price,
      }

      if (product.type === 'variable') {
        await this.ecommerce.updateProductVariations(product.id, params)
      }
      await this.ecommerce.updateProduct(product.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Product sale price updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()

      this.applyFilters()
    },
    async updateCost(product) {
      this.$vs.loading()

      let priceParam = formatMoney(this.newPrice)

      let meta_data = []
      if (product.hasOwnProperty('meta_data')) {
        meta_data = product.meta_data
      }

      let cog_cost_index = -1
      for (let mIndex = 0; mIndex < meta_data.length; mIndex++) {
        let md = meta_data[mIndex]
        if (md.key === '_wc_cog_cost') {
          cog_cost_index = mIndex
        }
      }
      if (cog_cost_index === -1) {
        this.$vs.notify({
          title: 'Error',
          text: 'Product has no "_wc_cog_cost" meta data',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
        return
      }
      meta_data[cog_cost_index].value = priceParam

      let params = {
        meta_data: meta_data,
      }

      if (product.type === 'variable') {
        await this.ecommerce.updateProductVariations(product.id, params)
      }
      await this.ecommerce.updateProduct(product.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Product cost updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()

      this.applyFilters()
    },
    async showVariations(product) {
      if (product.type !== 'variable') return

      this.$vs.loading()

      this.product = product
      this.variations = await this.ecommerce.fetchProductVariations(product.id)
      for (let i = 0; i < this.variations.length; i++) {
        let obj = this.variations[i]

        obj.name = ''
        for (let j = 0; j < obj.attributes.length; j++) {
          let attr = obj.attributes[j]
          obj.name += attr.option + ' - '
        }
        obj.name += this.product.name

        this.variations[i] = obj
      }
      this.view = 'variations'

      this.$vs.loading.close()
    },
    async updateVariationPrice(variation) {
      this.$vs.loading()

      let priceParam = formatMoney(this.newPrice)
      let params = {
        sale_price: priceParam,
      }

      await this.ecommerce.updateProductVariation(this.product.id, variation.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Variation sale price updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()

      this.showVariations(this.product)
    },
    async updateVariationRegularPrice(variation) {
      this.$vs.loading()

      let priceParam = formatMoney(this.newPrice)
      let params = {
        regular_price: priceParam,
      }

      await this.ecommerce.updateProductVariation(this.product.id, variation.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Variation regular price updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()

      this.showVariations(this.product)
    },
    async updateVariationCost(variation) {
      this.$vs.loading()

      let priceParam = formatMoney(this.newPrice)

      let meta_data = []
      if (variation.hasOwnProperty('meta_data')) {
        meta_data = variation.meta_data
      }

      let cog_cost_index = -1
      for (let mIndex = 0; mIndex < meta_data.length; mIndex++) {
        let md = meta_data[mIndex]
        if (md.key === '_wc_cog_cost') {
          cog_cost_index = mIndex
        }
      }
      if (cog_cost_index === -1) {
        this.$vs.notify({
          title: 'Error',
          text: 'Product has no "_wc_cog_cost" meta data',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
        return
      }
      meta_data[cog_cost_index].value = priceParam

      let params = {
        meta_data: meta_data,
      }

      await this.ecommerce.updateProductVariation(this.product.id, variation.id, params)

      this.$vs.notify({
        title: 'Success',
        text: 'Variation regular price updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()

      this.showVariations(this.variation)
    },
  },
  async created() {
    this.ecommerce = new Ecommerce(localStorage.getItem('accessToken'))

    this.$vs.loading()

    await this.resetFilters()

    this.$vs.loading.close()
  },
}

</script>
