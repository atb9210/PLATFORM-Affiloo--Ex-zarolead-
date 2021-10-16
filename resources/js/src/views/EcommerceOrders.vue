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
        <EcommerceOrdersTable />
    </div>
</template>

<script>
import EcommerceOrdersTable from './ui-elements/table/EcommerceOrdersTable.vue'

export default{
  components: {
    EcommerceOrdersTable
  },
}
</script>
