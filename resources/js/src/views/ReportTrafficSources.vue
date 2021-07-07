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
        <report-traffic-sources-table></report-traffic-sources-table>
    </div>
</template>

<script>
import ReportTrafficSourcesTable from './ui-elements/table/ReportTrafficSourcesTable.vue'

export default{
  components: {
    ReportTrafficSourcesTable
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
