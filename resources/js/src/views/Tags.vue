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
        <tags-table></tags-table>
    </div>
</template>

<script>
import TagsTable from './ui-elements/table/TagsTable.vue'

export default{
  components: {
    TagsTable
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
