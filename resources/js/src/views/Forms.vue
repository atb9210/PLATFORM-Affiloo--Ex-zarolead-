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
        <forms-table></forms-table>
    </div>
</template>

<script>
import FormsTable from './ui-elements/table/FormsTable.vue'

export default{
  components: {
    FormsTable
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
