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
        <TikTokAdsTable />
    </div>
</template>

<script>
import TikTokAdsTable from './ui-elements/table/TikTokAdsTable.vue'

export default{
  components: {
    TikTokAdsTable
  },
}
</script>
