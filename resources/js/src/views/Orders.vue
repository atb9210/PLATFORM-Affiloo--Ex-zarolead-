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
        <orders-table></orders-table>
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
  }
}
</script>
