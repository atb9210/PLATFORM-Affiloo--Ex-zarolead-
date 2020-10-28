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
        <user-reports-table v-if="dashboardView == 'normal'"></user-reports-table>
        <advertiser-reports-table v-if="dashboardView == 'advertiser'"></advertiser-reports-table>
    </div>
</template>

<script>
import UserReportsTable from './ui-elements/table/UserReportsTable.vue'
import AdvertiserReportsTable from './ui-elements/table/AdvertiserReportsTable.vue'

export default{
  components: {
    UserReportsTable,
    AdvertiserReportsTable
  },
  computed: {
    dashboardView() {
      if (this.$store.getters.userHasRole('developer')) {
        return 'normal'
      } else if (this.$store.getters.userHasRole('admin')) {
        return 'normal'
      } else if (this.$store.getters.userHasRole('mediabuyer')) {
        return 'normal'
      } else if (this.$store.getters.userHasRole('buyer')) {
        return 'normal'
      } else if (this.$store.getters.userHasRole('advertiser')) {
        return 'advertiser'
      }
    },
  },
  data() {
      return {
      }
  },
  methods: {
  },
  created() {
  },
}
</script>
