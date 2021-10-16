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
        <EcommerceCustomersTable />
    </div>
</template>

<script>
import EcommerceCustomersTable from './ui-elements/table/EcommerceCustomersTable.vue'

export default{
  components: {
    EcommerceCustomersTable
  },
}
</script>
