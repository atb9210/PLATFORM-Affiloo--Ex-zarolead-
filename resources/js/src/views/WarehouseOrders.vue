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
        <WarehouseOrdersTable />
    </div>
</template>

<script>
import WarehouseOrdersTable from './ui-elements/table/WarehouseOrdersTable.vue'

export default{
  components: {
    WarehouseOrdersTable
  },
}
</script>
