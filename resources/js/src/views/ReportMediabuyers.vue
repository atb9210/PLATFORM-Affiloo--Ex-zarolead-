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
        <report-mediabuyers-table></report-mediabuyers-table>
    </div>
</template>

<script>
import ReportMediabuyersTable from './ui-elements/table/ReportMediabuyersTable.vue'

export default{
  components: {
    ReportMediabuyersTable
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
