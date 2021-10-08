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
        <EcommerceTable />
    </div>
</template>

<script>
import EcommerceTable from './ui-elements/table/EcommerceTable.vue'

export default{
  components: {
    EcommerceTable
  },
}
</script>
