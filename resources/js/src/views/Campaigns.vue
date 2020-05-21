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
        <campaigns-table></campaigns-table>
    </div>
</template>

<script>
import CampaignsTable from './ui-elements/table/CampaignsTable.vue'

export default{
  components: {
    CampaignsTable
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
