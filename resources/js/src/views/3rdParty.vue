<!-- =========================================================================================
    File Name: Table.vue
    Description: Table demo - Imports all table demos
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
  <vs-row vs-justify="center">

    <vs-col type="flex" vs-justify="center" vs-align="center" vs-w="4" v-for="(item, index) in apps" :key="index">
      <vs-card class="cardx" fixedHeight>
        <div slot="header">
          <h3>
            {{item.name}}
          </h3>
        </div>
        <div slot="media">
          <img :src="item.image">
        </div>
        <div>
          <span>{{item.description}}</span>
        </div>
        <div slot="footer">
          <vs-row vs-justify="flex-end">
            <vs-button type="gradient" color="danger" icon="favorite"></vs-button>
            <vs-button color="primary" icon="turned_in_not"></vs-button>
            <vs-button color="rgb(230,230,230)" color-text="rgb(50,50,50)" icon="settings"></vs-button>
          </vs-row>
        </div>
      </vs-card>
    </vs-col>

  </vs-row>
</template>

<script>
export default{
  data() {
    return {
      apps: [
        {
          name: 'Worldfilia',
          image: '/images/3rd_party/worldfilia.png',
          description: 'Network',
        },
        {
          name: 'Worldfilia',
          image: '/images/3rd_party/worldfilia.png',
          description: 'Network',
        },
        {
          name: 'Worldfilia',
          image: '/images/3rd_party/worldfilia.png',
          description: 'Network',
        },
        {
          name: 'Worldfilia',
          image: '/images/3rd_party/worldfilia.png',
          description: 'Network',
        },
        {
          name: 'Worldfilia',
          image: '/images/3rd_party/worldfilia.png',
          description: 'Network',
        },
        {
          name: 'Worldfilia',
          image: '/images/3rd_party/worldfilia.png',
          description: 'Network',
        },
      ],
    }
  },
  methods: {
  },
  created() {
  }
}
</script>

<style>
.cardx {
    margin: 15px;
}
</style>
