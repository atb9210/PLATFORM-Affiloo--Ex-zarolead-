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
        <DropinStockTable />
    </div>
</template>

<script>
import DropinStockTable from './ui-elements/table/DropinStockTable.vue'

export default{
  components: {
    DropinStockTable
  },
}
</script>
