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
        <landings-stats-table :form_id="form_id"></landings-stats-table>
    </div>
</template>

<script>
import LandingsStatsTable from './ui-elements/table/LandingsStatsTable.vue'

export default{
  components: {
    LandingsStatsTable
  },
  data() {
      return {
        form_id: 0,
      }
  },
  methods: {
  },
  created() {
    this.form_id = parseInt(this.$route.params.form_id)
  }
}
</script>
