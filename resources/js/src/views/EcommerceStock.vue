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
        <EcommerceStockTable />
    </div>
</template>

<script>
import EcommerceStockTable from './ui-elements/table/EcommerceStockTable.vue'

export default{
  components: {
    EcommerceStockTable
  },
}
</script>
