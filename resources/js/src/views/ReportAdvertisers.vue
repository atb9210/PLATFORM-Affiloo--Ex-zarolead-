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
        <report-advertisers-table></report-advertisers-table>
    </div>
</template>

<script>
import ReportAdvertisersTable from './ui-elements/table/ReportAdvertisersTable.vue'

export default{
  components: {
    ReportAdvertisersTable
  },
  data() {
      return {
      }
  },
  methods: {
  },
  created() {
  }
}
</script>
