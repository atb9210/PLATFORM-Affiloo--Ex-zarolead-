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
        <CustomersTable></CustomersTable>
    </div>
</template>

<script>
import CustomersTable from './ui-elements/table/CustomersTable.vue'

export default{
  components: {
    CustomersTable
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
