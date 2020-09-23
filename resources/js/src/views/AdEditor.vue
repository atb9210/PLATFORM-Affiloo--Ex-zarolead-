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
        <ad-editor :ad_id="ad_id"></ad-editor>
    </div>
</template>

<script>
import AdEditor from './forms/AdEditor.vue'

export default{
  components: {
    AdEditor
  },
  data() {
    return {
      ad_id: 0,
    }
  },
  methods: {
  },
  created() { 
    this.ad_id = parseInt(this.$route.params.ad_id)
  }
}
</script>
