<template>
    <vx-card title="Warehouse Products" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button v-if="view == 'products'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button v-if="view == 'products'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="changeView('create')"></vs-button>
            <vs-button v-if="view == 'create' || view == 'edit'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="changeView('products')"></vs-button>
        </p>

        <div v-if="showFilters" class="mb-12">
            <div class="vx-row mt-6">
                <div class="vx-col w-full">
                    <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
                    <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
                </div>
            </div>
        </div>

        <div v-if="view == 'products'">

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
                  <a href="#" @click="$refs.product_context_menu.close();changeView('edit', data)" class="flex items-center text-sm">
                    <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Edit</span>
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
            <vs-table stripe multiple v-model="selectedProducts" :max-items="maxItems" pagination search :data="products" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="sku">SKU</vs-th>
                    <vs-th sort-key="variations">Variations</vs-th>
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
                            <vx-tooltip :title="item.name + ' variations'" :text="getVariationsNames(item)" position="left">
                                <vs-chip>
                                    {{item.variations.length}} variations
                                </vs-chip>
                            </vx-tooltip>
                        </vs-td>

                    </vs-tr>
                </template>

            </vs-table>

        </div>

        <div v-if="view == 'create' || view == 'edit'">

            <div class="vx-row w-full">
                <div class="vx-col mb-3 sm:w-1/2 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="product.name" placeholder="Name*" />
                </div>
                <div class="vx-col mb-3 sm:w-1/2 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="product.sku" placeholder="SKU" />
                </div>
            </div>

            <div class="vx-row w-full" v-for="(variation, vIndex) in product.variations" :key="'prodCreateVariation' + vIndex">
                <div class="vx-col mb-3 sm:w-1/5 w-full">
                    <span @click="removeVariation(vIndex)"><vs-icon class="icon-button" icon-pack="feather" icon="icon-x"></vs-icon></span>
                    {{ variation.name }}
                </div>
                <div class="vx-col mb-3 sm:w-1/5 w-full">
                    {{ variation.sku }}
                </div>
                <div class="vx-col mb-3 sm:w-1/5 w-full">
                    {{ variation.price }}
                </div>
                <div class="vx-col mb-3 sm:w-1/5 w-full">
                    {{ variation.quantity }}
                </div>
                <div class="vx-col mb-3 sm:w-1/5 w-full">
                    <span @click="removeVariation(vIndex)"><vs-icon class="icon-button" icon-pack="feather" icon="icon-x"></vs-icon></span>
                </div>
            </div>

            <div class="vx-row w-full">
                <div class="vx-col mb-3 sm:w-1/5 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="variation.name" placeholder="Name*" />
                </div>
                <div class="vx-col mb-3 sm:w-1/5 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="variation.sku" placeholder="SKU*" />
                </div>
                <div class="vx-col mb-3 sm:w-1/5 w-full">
                    <vs-input type="number" step="0.01" min="0" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="variation.price" placeholder="Price*" />
                </div>
                <div class="vx-col mb-3 sm:w-1/5 w-full">
                    <vs-input type="number" step="1" min="0" class="w-full" icon-pack="feather" icon="icon-package" icon-no-border v-model="variation.quantity" placeholder="Quantity*" />
                </div>
                <div class="vx-col mb-3 sm:w-1/5 w-full">
                    <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="addVariation(variation)" :disabled="!isValidVariation(variation)"></vs-button>
                </div>
            </div>

            <div class="vx-row w-full">
                <div class="vx-col mb-3 w-full">
                    <vs-button v-if="view == 'create'" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="createProduct(product)" :disabled="!isValidProduct(product)">Create Product</vs-button>
                    <vs-button v-if="view == 'edit'" color="primary" type="filled" icon-pack="feather" icon="icon-save" @click="updateProduct(product)" :disabled="!isValidProduct(product)">Update Product</vs-button>
                </div>
            </div>

        </div>

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
                        <strong>{{ product.name }} ({{ (product.sku !== null) ? product.sku : 'sku undefined'}})</strong>
                    </div>
                </div>

                <!--
                <div class="vx-row mb-6" v-if="product.description != ''">
                    <div class="vx-col w-full">
                        <span>{{ product.description }}</span>
                    </div>
                </div>
                -->

                <div class="vx-row w-full mb-6 font-bold" v-if="product !== null && product.hasOwnProperty('variations') && product.variations.length > 0">
                    <div class="vx-col mb-3 sm:w-1/4 w-full">
                        Name
                    </div>
                    <div class="vx-col mb-3 sm:w-1/4 w-full">
                        SKU
                    </div>
                    <div class="vx-col mb-3 sm:w-1/4 w-full">
                        Price
                    </div>
                    <div class="vx-col mb-3 sm:w-1/4 w-full">
                        Stock Quantity
                    </div>
                </div>
                <div class="vx-row w-full" v-if="product !== null && product.hasOwnProperty('variations') && product.variations.length > 0" v-for="(variation, vIndex) in product.variations" :key="'showInfoVariation' + variation.id">
                    <div class="vx-col mb-3 sm:w-1/4 w-full">
                        {{ variation.name }}
                    </div>
                    <div class="vx-col mb-3 sm:w-1/4 w-full">
                        {{ variation.sku }}
                    </div>
                    <div class="vx-col mb-3 sm:w-1/4 w-full">
                        € {{ variation.price }}
                    </div>
                    <div class="vx-col mb-3 sm:w-1/4 w-full">
                        {{ variation.quantity }}
                    </div>
                </div>
                <div class="vx-row w-full" v-else>
                    This product has no variations
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
import { WarehouseProduct } from 'zarolead'
import * as utils from 'utils'

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
      warehouseProductClient: null,

      maxItems: 20,
      view: 'products',

      showFilters: false,
      filters: {
      },

      products: [],
      selectedProducts: [],

      showPopup: {
        'product_info': false,
        'add_product': false,
      },

      product: {},
      productImageIndex: 0,

      variation: {},

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
      if (this.view == 'products') {
        return this.products
      }
      return []
    },
    selected() {
      if (this.view == 'products') {
        return this.selectedProducts
      }
      return []
    },
  },
  methods: {
    // Frontend functions
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
      if (view == 'products') {
        this.$vs.loading()
        this.selectedProducts = []
        this.products = await this.warehouseProductClient.fetch(this.filters)
        this.$vs.loading.close()
      } else if (view == 'create') {
        this.product = {
          name: '',
          sku: '',
          variations: [],
        }
        this.variation = {
          name: '',
          sku: '',
          price: '',
          quantity: '',
        }
      } else if (view == 'edit') {
        this.product = utils.deepCloneObject(value)
      }

      this.view = view
    },
    async applyFilters() {
      await this.changeView(this.view)
    },
    async resetFilters() {
      this.filters = {
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
    removeVariation(index) {
      this.product.variations.splice(index, 1)
    },
    addVariation(variation) {
      this.product.variations.push(this.variation)
      this.variation = {
        name: '',
        sku: '',
        price: '',
        quantity: '',
      }
    },
    isValidVariation(variation) {
      try {
        if (variation.name.length > 0 && variation.sku.length > 0 &&
            parseFloat(variation.price) >= 0 && parseInt(variation.quantity) >= 0) {
          return true
        }
      } catch (error) {
        return false
      }
      return false
    },
    isValidProduct(product) {
      try {
        if (product.name.length > 0) {
          return true
        }
      } catch (error) {
        return false
      }
      return false
    },
    async createProduct(product) {
      this.$vs.loading()

      // Create product
      let obj = await this.warehouseProductClient.create(product)
      if (obj === null) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot create the product',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      } else {
        // Create product variations
        for (let i = 0; i < product.variations.length; i++) {
          let variation = product.variations[i]
          await this.warehouseProductClient.createVariation(obj.id, variation)
          // TODO: Check for errors when creating a variation
        }
        this.$vs.notify({
          title: 'Success',
          text: 'Product created',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      }

      this.$vs.loading.close()

      this.changeView('products')
    },
    async updateProduct(product) {
      this.$vs.loading()

      // Update product
      let obj = await this.warehouseProductClient.update(product.id)
      if (obj === null) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the product',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      } else {
        let originalProduct = this.getObjectById(obj.id, this.products)

        // Delete missing variations
        for (let i = 0; i < originalProduct.variations.length; i++) {
          let variation = originalProduct.variations[i]
          let found = (this.getObjectById(variation.id, product.variations) !== null)
          if (!found) {
            await this.warehouseProductClient.deleteVariation(variation.id)
          }
        }

        // Create new variations
        for (let i = 0; i < product.variations.length; i++) {
          let variation = product.variations[i]
          if (!variation.hasOwnProperty('id')) {
            await this.warehouseProductClient.createVariation(obj.id, variation)
          }
        }

        // TODO: Check for errors when creating/deleting variations

        this.$vs.notify({
          title: 'Success',
          text: 'Product updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      }

      this.$vs.loading.close()

      this.changeView('products')
    },
    getVariationsNames(product) {
      let s = ''
      for (let i = 0; i < product.variations.length; i++) {
        if (i > 0) {
          s += ', '
        }
        s += product.variations[i].name
      }
      return s
    },
  },
  async created() {
    this.warehouseProductClient = new WarehouseProduct(this.$zarolead)

    await this.changeView('products')
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
.icon-button {
  cursor: pointer;
}
</style>
