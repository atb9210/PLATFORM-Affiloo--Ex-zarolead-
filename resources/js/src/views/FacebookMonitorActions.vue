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
        <FacebookMonitorActionsTable :monitor_id="monitor_id" />
    </div>
</template>

<script>
import FacebookMonitorActionsTable from './ui-elements/table/FacebookMonitorActionsTable.vue'

export default{
  components: {
    FacebookMonitorActionsTable
  },
  data () {
    return {
      monitor_id: 0,
    }
  },
  methods: {
    switchMonitor(value) {
      if (value == '' || value <= 0 || value == this.monitor_id) {
        return
      }

      this.$router.push({name: 'facebook-monitor-actions', params: {facebook_monitor_id: value}})
      this.monitor_id = parseInt(this.$route.params.facebook_monitor_id)
    }
  },
  created() {
    this.monitor_id = parseInt(this.$route.params.facebook_monitor_id)
  },
}
</script>
