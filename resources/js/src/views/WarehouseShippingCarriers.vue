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
        <WarehouseShippingCarriersTable />
    </div>
</template>

<script>
import WarehouseShippingCarriersTable from './ui-elements/table/WarehouseShippingCarriersTable.vue'

export default{
  components: {
    WarehouseShippingCarriersTable
  },
}
</script>
