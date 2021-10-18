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
        <EcommerceCartsTable />
    </div>
</template>

<script>
import EcommerceCartsTable from './ui-elements/table/EcommerceCartsTable.vue'

export default{
  components: {
    EcommerceCartsTable
  },
}
</script>
