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
        <DropinReportsTable />
    </div>
</template>

<script>
import DropinReportsTable from './ui-elements/table/DropinReportsTable.vue'

export default{
  components: {
    DropinReportsTable
  },
}
</script>
