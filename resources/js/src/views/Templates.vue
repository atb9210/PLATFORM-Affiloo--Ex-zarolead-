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
        <templates-table></templates-table>
    </div>
</template>

<script>
import TemplatesTable from './ui-elements/table/TemplatesTable.vue'

export default{
  components: {
    TemplatesTable
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
