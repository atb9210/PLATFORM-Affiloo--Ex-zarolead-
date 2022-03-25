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
        <WarehouseProductsTable />
    </div>
</template>

<script>
import WarehouseProductsTable from './ui-elements/table/WarehouseProductsTable.vue'

export default{
  components: {
    WarehouseProductsTable
  },
}
</script>
