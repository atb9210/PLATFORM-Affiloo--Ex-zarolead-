<!-- =========================================================================================
    File Name: Table.vue
    Description: Table demo - Imports all table demos
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <div id="table-demo">
        <orders-table :orders="orders"></orders-table>
    </div>
</template>

<script>
import OrdersTable from './ui-elements/table/OrdersTable.vue'

export default{
  components: {
    OrdersTable
  },
  data() {
      return {
          orders: [],
      }
  },
  methods: {
  },
  created() {
      this.$http.get('/api/v1/orders', { headers: { Authorization: 'Bearer ' + localStorage.getItem('accessToken') } })
        .then((response) => {
          this.orders = response.data.data
        })
        .catch()
  }
}
</script>
