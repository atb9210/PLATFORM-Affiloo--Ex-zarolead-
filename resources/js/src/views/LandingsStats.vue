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
        <landings-stats-table :landing_id="landing_id"></landings-stats-table>
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
        landing_id: 0,
      }
  },
  methods: {
  },
  created() {
    this.landing_id = parseInt(this.$route.params.landing_id)
  }
}
</script>
