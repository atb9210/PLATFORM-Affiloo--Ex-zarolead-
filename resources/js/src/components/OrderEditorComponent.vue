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
import { Order } from 'zarolead'

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
      for (let i = 0; i < this.order.lines.length; i++) {
        let line = this.order.lines[i]
        let quantity = parseInt(line.quantity)
        let fullPrice = parseFloat(line.unit_price)*quantity
        let price = fullPrice
        let discount = 0.0
        lines.push({
          product_id: line.product.id,
          quantity: quantity,
          full_price: fullPrice,
          discount: discount,
          price: price,
        })
      }
      let orderClient = new Order(localStorage.getItem('accessToken'))
      let order = await orderClient.update(this.order.id, {
        lines: JSON.stringify(lines),
      })

      let result = (order !== null)
      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Order updated',
          iconPack: 'feather',
          icon: 'icon-package',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the order',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
  },
  updated () {
  },
  mounted () {
  }
}
</script>
