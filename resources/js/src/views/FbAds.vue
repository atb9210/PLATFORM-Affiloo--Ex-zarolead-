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
        <fb-ads-table :fb_account_id="fb_account_id"></fb-ads-table>
    </div>
</template>

<script>
import FbAdsTable from './ui-elements/table/FbAdsTable.vue'

export default{
  components: {
    FbAdsTable
  },
  data() {
    return {
      fb_account_id: 0,
    }
  },
  methods: {
  },
  created() {
    this.fb_account_id = parseInt(this.$route.params.fb_account_id)
  },
}
</script>
