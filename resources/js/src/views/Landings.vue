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
        <landings-table></landings-table>
    </div>
</template>

<script>
import LandingsTable from './ui-elements/table/LandingsTable.vue'

export default{
  components: {
    LandingsTable
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
