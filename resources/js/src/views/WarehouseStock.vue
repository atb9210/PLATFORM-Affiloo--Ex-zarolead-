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
        <WarehouseStockTable />
    </div>
</template>

<script>
import WarehouseStockTable from './ui-elements/table/WarehouseStockTable.vue'

export default{
  components: {
    WarehouseStockTable
  },
}
</script>
