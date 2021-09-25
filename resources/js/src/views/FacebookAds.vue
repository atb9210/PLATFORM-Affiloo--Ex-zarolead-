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
        <FacebookAdsTable />
    </div>
</template>

<script>
import FacebookAdsTable from './ui-elements/table/FacebookAdsTable.vue'

export default{
  components: {
    FacebookAdsTable
  },
}
</script>
