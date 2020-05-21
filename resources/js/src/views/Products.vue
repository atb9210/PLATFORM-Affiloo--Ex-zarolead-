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
        <products-table></products-table>
    </div>
</template>

<script>
import ProductsTable from './ui-elements/table/ProductsTable.vue'

export default{
  components: {
    ProductsTable
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
