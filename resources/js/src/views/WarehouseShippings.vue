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
        <WarehouseShippingsTable />
    </div>
</template>

<script>
import WarehouseShippingsTable from './ui-elements/table/WarehouseShippingsTable.vue'

export default{
  components: {
    WarehouseShippingsTable
  },
}
</script>
