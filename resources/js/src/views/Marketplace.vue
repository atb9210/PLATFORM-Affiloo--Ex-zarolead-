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
        <marketplace-table></marketplace-table>
    </div>
</template>

<script>
import MarketplaceTable from './ui-elements/table/MarketplaceTable.vue'

export default{
  components: {
    MarketplaceTable
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
