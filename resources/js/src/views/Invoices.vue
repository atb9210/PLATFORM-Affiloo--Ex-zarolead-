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
        <invoices-table></invoices-table>
    </div>
</template>

<script>
import InvoicesTable from './ui-elements/table/InvoicesTable.vue'

export default{
  components: {
    InvoicesTable
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
