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
        <report-users-table></report-users-table>
    </div>
</template>

<script>
import ReportUsersTable from './ui-elements/table/ReportUsersTable.vue'

export default{
  components: {
    ReportUsersTable
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
