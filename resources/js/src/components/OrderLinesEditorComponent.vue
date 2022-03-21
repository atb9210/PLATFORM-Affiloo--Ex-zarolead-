<template>
    <div>

    <div class="w-full pr-2 pl-2">
        <div class="vx-row w-full mb-3 font-bold">
            <div class="vx-col w-full md:w-1/3">
                Product
            </div>
            <div class="vx-col w-full md:w-1/6">
                Quantity
            </div>
            <div class="vx-col w-full md:w-1/6">
                Unit Price
            </div>
            <div class="vx-col w-full md:w-1/6">
                Total Price
            </div>
            <div class="vx-col w-full md:w-1/6">
                Upsell
            </div>
        </div>

        <div class="vx-row w-full mb-3" v-for="(line, lineIndex) in order.lines" :key="'reviewProductOption' + lineIndex">
            <div class="vx-col w-full md:w-1/3">
                <span @click="removeLine(lineIndex)"><vs-icon class="icon-button" icon-pack="feather" icon="icon-x"></vs-icon></span>
                {{ line.product.name }}
            </div>
            <div class="vx-col w-full md:w-1/6">
                <fast-editable-value v-model="line.quantity" @onUpdate="refreshOrderTotal()">
                    {{line.quantity}}
                </fast-editable-value>
            </div>
            <div class="vx-col w-full md:w-1/6">
                <fast-editable-value v-model="line.unit_price" @onUpdate="refreshOrderTotal()">
                    € {{line.unit_price}}
                </fast-editable-value>
            </div>
            <div class="vx-col w-full md:w-1/6">
                € {{(parseFloat(line.unit_price)*parseInt(line.quantity)).toFixed(2)}}
            </div>
            <div class="vx-col w-full md:w-1/6">
                TODO:
            </div>
        </div>

        <div class="vx-row w-full mb-3" >
            <div class="vx-col w-full md:w-1/3">
                <span @click="showAddProductPopup"><vs-icon class="icon-button" icon-pack="feather" icon="icon-plus"></vs-icon></span>
                <span @click="showAddProductPopup" class="icon-button">Add Product</span>
            </div>
            <div class="vx-col w-full md:w-1/6">
            </div>
            <div class="vx-col w-full md:w-1/6">
            </div>
            <div class="vx-col w-full md:w-1/6">
            </div>
            <div class="vx-col w-full md:w-1/6">
            </div>
        </div>

        <div class="vx-row w-full mt-3 mb-3 font-bold">
            <div class="vx-col w-full md:w-1/3">
            </div>
            <div class="vx-col w-full md:w-1/6">
            </div>
            <div class="vx-col w-full md:w-1/6">
            </div>
            <div class="vx-col w-full md:w-1/6">
                _____________________<br>
                € {{order.total.toFixed(2)}}
            </div>
            <div class="vx-col w-full md:w-1/6">
            </div>
        </div>

    </div>

    <vs-popup fullscreen title="Add products" :active.sync="showPopup['add_product']">

        <div class="w-full pr-2 pl-2">

            <vs-table stripe search :data="products">

                <template slot="thead">
                    <vs-th></vs-th>
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="sku">SKU</vs-th>
                    <vs-th sort-key="price">Price</vs-th>
                    <vs-th sort-key="stock_quantity">Stock Qty</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="item" :key="'addProductsRow' + indextr" v-for="(item, indextr) in data">

                        <vs-td class="column-no-padding">
                            <vs-checkbox :value="hasProduct(item)" @change="toggleProduct(item)" />
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.name">
                            {{item.name}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.sku">
                            {{item.sku}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.price">
                            € {{ item.price.toFixed(2) }}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="item.stock_quantity">
                            {{item.stock_quantity}}
                        </vs-td>

                    </vs-tr>

                </template>
            </vs-table>

        </div>

    </vs-popup>

    </div>

</template>

<script>
import { Order } from 'zarolead'

export default {
  name: 'OrderLinesEditorComponent',
  props: {
    order: { type: Object, required: true },
    products: { type: Array, required: true },
  },
  data () {
    return {
      showPopup: {
        'add_product': false,
      },
    }
  },
  methods: {
    refreshOrderTotal() {
      let total = 0.0

      for (let i = 0; i < this.order.lines.length; i++) {
        let line = this.order.lines[i]
        let quantity = parseInt(line.quantity)
        let unitPrice = parseFloat(line.unit_price)
        let price = quantity*unitPrice
        this.order.lines[i].price = price.toFixed(2)
        total += price
      }

      this.order.total = total
    },
    removeLine(lineIndex) {
      this.order.lines.splice(lineIndex, 1)
      this.refreshOrderTotal()
    },
    showAddProductPopup() {
      this.showPopup['add_product'] = true
    },
    toggleProduct(product) {
      for (let i = 0; i < this.order.lines.length; i++) {
        let line = this.order.lines[i]
        if (product.id === line.product.id) {
          this.removeLine(i)
          return
        }
      }

      let quantity = 1
      let unitPrice = parseFloat(product.price)
      let price = unitPrice*quantity
      this.order.lines.push({
        product_id: product.id,
        product: product,
        unit_price: unitPrice,
        price: price,
        quantity: 1,
      })
      this.refreshOrderTotal()
    },
    getProductById(productId) {
      for (let i = 0; i < this.products.length; i++) {
        if (this.products[i].id == productId) {
          return this.products[i]
        }
      }
      return null
    },
    hasProduct(product) {
      for (let i = 0; i < this.order.lines.length; i++) {
        let line = this.order.lines[i]
        if (product.id == line.product.id) {
          return true
        }
      }
      return false
    },
  },
  created () {
    for (let i = 0; i < this.order.lines.length; i++) {
      let line = this.order.lines[i]

      let product = this.getProductById(line.product_id)

      let quantity = parseInt(line.quantity)
      let unitPrice = parseFloat(product.price)
      let price = unitPrice*quantity

      line.product = product
      line.unit_price = unitPrice
      line.price = price
      line.quantity = quantity

      this.order.lines[i] = line
    }
  },
  updated () {
  },
  mounted () {
  }
}
</script>

<style lang="scss" scoped>
.icon-button {
  cursor: pointer;
}
</style>
