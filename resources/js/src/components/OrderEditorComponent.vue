<template>
    <div>

    <div class="w-full">
        <h2 class="mb-3">Order Lines</h2>
        <OrderLinesEditorComponent :order="order" :products="products" />
    </div>

    <div class="w-full pr-2 pl-2">
        <div class="vx-row w-full mt-3 mb-3 font-bold items-center flex">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-package" @click="updateOrder" :disabled="!isValidForm()">Update Order</vs-button>
        </div>
    </div>

    </div>

</template>

<script>
import { WarehouseOrder } from 'zarolead'
import * as utils from 'utils'

export default {
  name: 'OrderEditorComponent',
  components: {
  },
  props: {
    order: { type: Object, required: true },
    products: { type: Array, required: true },
  },
  data () {
    return {
      warehouseOrderClient: null,

      orderCopy: {},
    }
  },
  methods: {
    isValidForm() {
      let isValidOrder = (this.order.lines.length > 0)
      return isValidOrder
    },
    async updateOrder() {
      this.$vs.loading()

      // Update the order
      let lines = []
      let amount = 0.0
      for (let i = 0; i < this.order.lines.length; i++) {
        let line = this.order.lines[i]
        let unitPrice = parseFloat(line.unit_price)
        let quantity = parseInt(line.quantity)
        let totalPrice = unitPrice*quantity
        let discount = 0.0
        let finalPrice = totalPrice - discount
        amount += finalPrice
        lines.push({
          id: line.id,
          warehouse_product_variation_id: line.product.id,
          quantity: quantity,
          unit_price: unitPrice.toFixed(2),
          total_price: totalPrice.toFixed(2),
          discount: discount.toFixed(2),
          final_price: finalPrice.toFixed(2),
        })
      }
      let order = await this.warehouseOrderClient.update(this.order.id, {
        amount: amount.toFixed(2),
      })

      // Delete the old order lines
      for (let i = 0; i < this.orderCopy.lines.length; i++) {
        await this.warehouseOrderClient.deleteLine(this.orderCopy.lines[i].id)
      }

      // Create the new order lines
      for (let i = 0; i < lines.length; i++) {
        await this.warehouseOrderClient.createLine(order.id, lines[i])
      }

      let result = (order !== null)
      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Warehouse order updated',
          iconPack: 'feather',
          icon: 'icon-package',
          color: 'success'
        })
        this.$emit('onOrderUpdated', order)
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the warehouse order',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
  },
  created() {
    this.warehouseOrderClient = new WarehouseOrder(this.$zarolead)
    this.orderCopy = utils.deepCloneObject(this.order)
  },
}
</script>
