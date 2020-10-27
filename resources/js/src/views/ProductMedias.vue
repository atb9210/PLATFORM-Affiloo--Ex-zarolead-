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
        <product-medias-table></product-medias-table>
    </div>
</template>

<script>
import ProductMediasTable from './ui-elements/table/ProductMediasTable.vue'

export default{
  components: {
    ProductMediasTable
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
