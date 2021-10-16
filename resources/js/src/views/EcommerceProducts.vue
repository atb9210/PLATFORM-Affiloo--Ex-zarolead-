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
        <EcommerceProductsTable />
    </div>
</template>

<script>
import EcommerceProductsTable from './ui-elements/table/EcommerceProductsTable.vue'

export default{
  components: {
    EcommerceProductsTable
  },
}
</script>
