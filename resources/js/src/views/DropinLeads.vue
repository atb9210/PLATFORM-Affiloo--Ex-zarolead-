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
        <DropinLeadsTable />
    </div>
</template>

<script>
import DropinLeadsTable from './ui-elements/table/DropinLeadsTable.vue'

export default{
  components: {
    DropinLeadsTable
  },
}
</script>
