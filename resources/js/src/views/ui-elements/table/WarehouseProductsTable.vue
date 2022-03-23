<template>
    <vx-card title="Warehouse Products" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
        </p>

        <div v-if="showFilters" class="mb-12">
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
              <a href="#" @click="$refs.product_context_menu.close();showProductInfo(data)" class="flex items-center text-sm">
                <feather-icon icon="InfoIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show Info</span>
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
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="sku">SKU</vs-th>
                <vs-th sort-key="variations">Variations</vs-th>
                <vs-th sort-key="sale_price">Price</vs-th>
                <vs-th sort-key="quantity">Quantity</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="item" :key="indextr" v-for="(item, indextr) in data">

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

                    <vs-td class="column-no-padding" :data="item.variations">
                        {{item.variations}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="item.sale_price">
                        € {{ item.price.toFixed(2) }}
                    </vs-td>

                    <vs-td class="column-no-padding">
                        {{item.quantity}}
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Product info popup -->
        <vs-popup title="Product info" :active.sync="showPopup['product_info']">

            <div>
                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                        <img v-if="productImageIndex > 0" src="/images/arrow-left.png" class="w-full nextPrevProductImage" @click="previousProductImage()">
                    </div>
                    <div class="vx-col sm:w-1/2 w-full mt-2">
                        <img v-if="product.hasOwnProperty('images') && product.images !== null && product.images.length > 0" :src="product.images[productImageIndex]" class="w-full">
                        <img v-else src="https://www.tucanomacchine.it/wp-content/uploads/2019/07/480px-No_image_available.svg.png" class="w-full">
                    </div>
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                        <img v-if="product.hasOwnProperty('images') && product.images !== null && (productImageIndex + 1 < product.images.length)" src="/images/arrow-right.png" class="w-full nextPrevProductImage" @click="nextProductImage()">
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <strong>{{ product.name }} ({{ product.sku }})</strong>
                    </div>
                </div>

                <div class="vx-row mb-6" v-if="product.description != ''">
                    <div class="vx-col w-full">
                        <span>{{ product.description }}</span>
                    </div>
                </div>

                <div class="vx-row mb-6" v-if="product.variations != ''">
                    <div class="vx-col w-full">
                        <span>Variations: {{ product.variations }}</span>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col md:1/2 w-full">
                        <span>Sugg. Price: € {{ (product.hasOwnProperty('sale_price') ? product.sale_price.toFixed(2) : 0.0) }}</span>
                    </div>
                    <div class="vx-col md:1/2 w-full">
                        <span>Drop. Price: € {{ (product.hasOwnProperty('dropshipping_price') ? product.dropshipping_price.toFixed(2) : 0.0) }}</span>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <span>Stock Quantity: {{ product.quantity }}</span>
                    </div>
                </div>
                
            </div>

        </vs-popup>

        <!-- Add product popup -->
        <vs-popup title="Add products" :active.sync="showPopup['add_product']">

            <div v-if="productView == 'normal'" class="w-full pr-2 pl-2">

                <div class="vx-row mb-3 w-full font-bold">
                    <div class="vx-col md:w-1/6 w-full">
                    </div>
                    <div class="vx-col md:w-1/4 w-full">
                        Name
                    </div>
                    <div class="vx-col md:w-1/6 w-full">
                        Q.ty
                    </div>
                    <div class="vx-col md:w-1/4 w-full">
                        SKU
                    </div>
                    <div class="vx-col md:w-1/6 w-full">
                    </div>
                </div>
                <div class="vx-row mb-3 w-full text-sm" v-for="(product, pIndex) in dropinProducts" :key="'addProductOption' + pIndex">
                    <div class="vx-col md:w-1/6 w-full">
                        <vs-checkbox :value="hasProduct(product)" @change="toggleProduct(product)" />
                    </div>
                    <div class="vx-col md:w-1/4 w-full">
                        {{product.name}} {{product.variations}}
                    </div>
                    <div class="vx-col md:w-1/6 w-full">
                        {{product.quantity}}
                    </div>
                    <div class="vx-col md:w-1/4 w-full">
                        {{product.sku}}
                    </div>
                    <div class="vx-col md:w-1/6 w-full">
                        <vs-avatar color="primary" size="24px" @click="showProductInfo(product, true)" icon-pack="feather" icon="icon-info" />
                    </div>
                </div>

            </div>

            <div v-else-if="productView == 'product_info'">
                <div class="vx-row mb-6">
                    <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="productView = 'normal'"></vs-button>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                        <img v-if="productImageIndex > 0" src="/images/arrow-left.png" class="w-full nextPrevProductImage" @click="previousProductImage()">
                    </div>
                    <div class="vx-col sm:w-1/2 w-full mt-2">
                        <img v-if="product.hasOwnProperty('images') && product.images.length > 0" :src="product.images[productImageIndex]" class="w-full">
                        <img v-else src="https://www.tucanomacchine.it/wp-content/uploads/2019/07/480px-No_image_available.svg.png" class="w-full">
                    </div>
                    <div class="vx-col sm:w-1/4 w-full mt-2">
                        <img v-if="product.hasOwnProperty('images') && (productImageIndex + 1 < product.images.length)" src="/images/arrow-right.png" class="w-full nextPrevProductImage" @click="nextProductImage()">
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <strong>{{ product.name }} ({{ product.sku }})</strong>
                    </div>
                </div>

                <div class="vx-row mb-6" v-if="product.description != ''">
                    <div class="vx-col w-full">
                        <span>{{ product.description }}</span>
                    </div>
                </div>

                <div class="vx-row mb-6" v-if="product.variations != ''">
                    <div class="vx-col w-full">
                        <span>Variations: {{ product.variations }}</span>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col md:1/2 w-full">
                        <span>Sugg. Price: € {{ (product.hasOwnProperty('sale_price') ? product.sale_price.toFixed(2) : 0.0) }}</span>
                    </div>
                    <div class="vx-col md:1/2 w-full">
                        <span>Drop. Price: € {{ (product.hasOwnProperty('dropshipping_price') ? product.dropshipping_price.toFixed(2) : 0.0) }}</span>
                    </div>
                </div>

                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <span>Stock Quantity: {{ product.quantity }}</span>
                    </div>
                </div>
                
            </div>

        </vs-popup>

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
      view: 'products',

      showFilters: false,
      filters: {
        categories: [],
      },

      products: [],
      selectedProducts: [],

      showPopup: {
        'product_info': false,
        'add_product': false,
      },

      product: {},
      productImageIndex: 0,

      categories: [],

      dropinProducts: [],
      productView: 'normal',
      productImageIndex: 0,
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
        this.products = await this.dropin.fetchProducts(this.filters)
        this.view = view
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
        categories: [],
      }
      await this.applyFilters()
    },
    showProductInfo(product, inAddProductPopup = false) {
      this.product = product
      this.productImageIndex = 0
      if (inAddProductPopup) {
        this.productView = 'product_info'
      } else {
        this.showPopup['product_info'] = true
      }
    },
    previousProductImage() {
      this.productImageIndex -= 1
      if (this.productImageIndex < 0) {
        this.productImageIndex = 0
      }
    },
    nextProductImage() {
      this.productImageIndex += 1
      if (this.productImageIndex >= this.product.images.length) {
        this.productImageIndex = this.product.images.length - 1
      }
    },
    async showAddProductPopup() {
      if (this.dropinProducts.length == 0) {
        this.$vs.loading()
        this.dropinProducts = await this.dropin.fetchDropinProducts()
        this.$vs.loading.close()
      }

      this.productView = 'normal'
      this.showPopup['add_product'] = true
    },
    async toggleProduct(product) {
      this.$vs.loading()

      let hasBeenDeleted = false
      for (let i = 0; i < this.products.length; i++) {
        let p = this.products[i]
        if (product.sku == p.sku) {
          await this.dropin.deleteProduct(p.sku)
          hasBeenDeleted = true
          break
        }
      }

      if (hasBeenDeleted) {
        this.$vs.notify({
          title: 'Success',
          text: 'Product removed',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } else {
        let newProduct = await this.dropin.createProduct({
          sku: product.sku,
          name: product.name,
          variations: product.variations,
          description: product.description,
          category: product.category,
          images: JSON.stringify(product.images),
          dropshipping_price: product.dropshipping_price,
          sale_price: product.sale_price,
          cost: product.dropshipping_price,
          quantity: product.quantity,
        })

        if (product !== null) {
          this.$vs.notify({
            title: 'Success',
            text: 'Product added',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
        } else {
          this.$vs.notify({
            title: 'Error',
            text: 'Cannot add the product',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        }
      }

      this.$vs.loading.close()
    },
    hasProduct(product) {
      for (let i = 0; i < this.products.length; i++) {
        let p = this.products[i]
        if (product.sku == p.sku) {
          return true
        }
      }
      return false
    },
  },
  async created() {
    this.dropin = new Dropin(localStorage.getItem('accessToken'))

    this.$vs.loading()

    await this.resetFilters()

    // Get categories list from products
    this.categories = []
    for (let i = 0; i < this.products.length; i++) {
      let category = this.products[i].category
      if (!this.categories.includes(category)) {
        this.categories.push(category)
      }
    }

    this.$vs.loading.close()
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
