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
        <finapsy-orders-table></finapsy-orders-table>
    </div>
</template>

<script>
import FinapsyOrdersTable from './ui-elements/table/FinapsyOrdersTable.vue'

export default{
  components: {
    FinapsyOrdersTable
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
