<!-- =========================================================================================
  File Name: DashboardAnalytics.vue
  Description: Dashboard Analytics
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>
    <mediabuyer-dashboard v-if="dashboardView == 'admin'" />
    <mediabuyer-dashboard v-else-if="dashboardView == 'mediabuyer'" />
</template>

<script>
import AdminDashboard from './ui-elements/dashboard/AdminDashboard.vue'
import MediabuyerDashboard from './ui-elements/dashboard/MediabuyerDashboard.vue'

export default {
  components: {
    AdminDashboard,
    MediabuyerDashboard,
  },
  computed: {
    dashboardView() {
      if (this.$store.getters.userHasRole('developer')) {
        return 'admin'
      } else if (this.$store.getters.userHasRole('admin')) {
        return 'admin'
      } else if (this.$store.getters.userHasRole('mediabuyer')) {
        return 'mediabuyer'
      } else if (this.$store.getters.userHasRole('buyer')) {
        return 'mediabuyer'
      } else {
        // TODO: Handle this (idea: blank dashboard? redirect?)
        return ''
      }
    }
  },
  data () {
    return {}
  },
}
</script>
