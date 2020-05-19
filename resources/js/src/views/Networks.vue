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
        <networks-table></networks-table>
    </div>
</template>

<script>
import NetworksTable from './ui-elements/table/NetworksTable.vue'

export default{
  components: {
    NetworksTable
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
