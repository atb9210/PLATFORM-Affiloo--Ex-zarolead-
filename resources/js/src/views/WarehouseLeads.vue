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
        <WarehouseLeadsTable />
    </div>
</template>

<script>
import WarehouseLeadsTable from './ui-elements/table/WarehouseLeadsTable.vue'

export default{
  components: {
    WarehouseLeadsTable
  },
}
</script>
