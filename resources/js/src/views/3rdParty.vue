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

    <vs-col type="flex" class="mb-6" vs-justify="center" vs-align="center" vs-w="3" v-for="(item, index) in apps" :key="index">
      <vs-card class="cardx" fixedHeight>
        <div slot="header">
          <h3 class="text-center">
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
          name: 'AMSped',
          image: '/images/3rd_party/amsped.png',
          description: 'Network',
        },
        {
          name: 'AffiliationPark',
          image: '/images/3rd_party/affiliation-park.png',
          description: 'Network',
        },
        {
          name: 'Facebook Business',
          image: '/images/3rd_party/facebook-business.png',
          description: 'Advertising Platform',
        },
        {
          name: 'TikTok',
          image: '/images/3rd_party/tiktok.png',
          description: 'Advertising Platform',
        },
        {
          name: 'Google Ads',
          image: '/images/3rd_party/google-ads.png',
          description: 'Advertising Platform',
        },
        {
          name: 'ServerPilot',
          image: '/images/3rd_party/serverpilot.png',
          description: 'Server Management',
        },
        {
          name: 'Cloudflare',
          image: '/images/3rd_party/cloudflare.png',
          description: 'DNS & Security',
        },
        {
          name: 'DigitalOcean',
          image: '/images/3rd_party/digitalocean.png',
          description: 'Cloud Service Provider',
        },
        {
          name: 'Dropbox',
          image: '/images/3rd_party/dropbox.png',
          description: 'File Hosting Service',
        },
        {
          name: 'Namecheap',
          image: '/images/3rd_party/namecheap.png',
          description: 'Web Hosting & Domain Registration Service',
        },
        {
          name: 'ZaroCloner',
          image: '/images/3rd_party/zarocloner.png',
          description: 'Webpages Cloner',
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
    width: calc(100% - 10px);
    margin: 5px;
}
</style>
