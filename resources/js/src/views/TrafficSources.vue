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
        <traffic-sources-table></traffic-sources-table>
    </div>
</template>

<script>
import TrafficSourcesTable from './ui-elements/table/TrafficSourcesTable.vue'

export default{
  components: {
    TrafficSourcesTable
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
