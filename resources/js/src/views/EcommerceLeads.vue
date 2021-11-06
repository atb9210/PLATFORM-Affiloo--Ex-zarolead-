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
        <EcommerceLeadsTable />
    </div>
</template>

<script>
import EcommerceLeadsTable from './ui-elements/table/EcommerceLeadsTable.vue'

export default{
  components: {
    EcommerceLeadsTable
  },
}
</script>
