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
        <advertisers-table></advertisers-table>
    </div>
</template>

<script>
import AdvertisersTable from './ui-elements/table/AdvertisersTable.vue'

export default{
  components: {
    AdvertisersTable
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
