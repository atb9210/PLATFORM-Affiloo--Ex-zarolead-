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
        <form-stats-table :form_id="form_id"></form-stats-table>
    </div>
</template>

<script>
import FormStatsTable from './ui-elements/table/FormStatsTable.vue'

export default{
  components: {
    FormStatsTable
  },
  data() {
    return {
      form_id: 0,
    }
  },
  methods: {
    switchForm(value) {
      if (value == '' || value <= 0 || value == this.form_id) {
        return
      }

      this.$router.push({name: 'form-stats', params: {form_id: value}})
      this.form_id = parseInt(this.$route.params.form_id)
    }
  },
  created() {
    this.form_id = parseInt(this.$route.params.form_id)

    // TODO: to delete
    console.log("AO")
  },
}
</script>
