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
        <logistics-orders-table></logistics-orders-table>
    </div>
</template>

<script>
import LogisticsOrdersTable from './ui-elements/table/LogisticsOrdersTable.vue'

export default{
  components: {
    LogisticsOrdersTable
  },
  data() {
      return {
      }
  },
  methods: {
  },
  created() {
  }
}
</script>
