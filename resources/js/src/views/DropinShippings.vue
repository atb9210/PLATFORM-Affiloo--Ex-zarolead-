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
        <DropinShippingsTable />
    </div>
</template>

<script>
import DropinShippingsTable from './ui-elements/table/DropinShippingsTable.vue'

export default{
  components: {
    DropinShippingsTable
  },
}
</script>
