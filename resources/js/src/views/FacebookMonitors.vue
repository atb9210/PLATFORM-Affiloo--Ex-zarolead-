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
        <FacebookMonitorsTable />
    </div>
</template>

<script>
import FacebookMonitorsTable from './ui-elements/table/FacebookMonitorsTable.vue'

export default{
  components: {
    FacebookMonitorsTable
  },
}
</script>
