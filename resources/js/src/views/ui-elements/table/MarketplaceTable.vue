<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Marketplace" code-toggler>

        <p class="flex items-center mb-4">
        </p>

        <vs-dropdown vs-trigger-click class="cursor-pointer">
            <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
                <span class="mr-2">{{ currentPage * maxItems - (maxItems - 1) }} - {{ objects.length - currentPage * maxItems > 0 ? currentPage * maxItems : objects.length }} of {{ objects.length }}</span>
                <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
            </div>
            <vs-dropdown-menu>
                <vs-dropdown-item @click="maxItems = 20">
                    <span>20</span>
                </vs-dropdown-item>
                <vs-dropdown-item @click="maxItems = 50">
                    <span>50</span>
                </vs-dropdown-item>
                <vs-dropdown-item @click="maxItems = 100">
                    <span>100</span>
                </vs-dropdown-item>
                <vs-dropdown-item @click="maxItems = 150">
                    <span>150</span>
                </vs-dropdown-item>
            </vs-dropdown-menu>
        </vs-dropdown>

        <vue-context ref="context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.context_menu.close();showLandings(data.id);" class="flex items-center text-sm">
                <feather-icon icon="LayoutIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Show landings</span>
              </a>
            </li>
            <!--
            <li>
              <a href="#" @click="$refs.context_menu.close();deleteObject(data.id);" class="flex items-center text-sm">
                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Delete</span>
              </a>
            </li>
            -->
            <li>
              <a href="#" @click="$refs.context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vs-table stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="status">Status</vs-th>
                <vs-th sort-key="commission">Commission</vs-th>
                <vs-th sort-key="ranking">Ranking</vs-th>
                <vs-th sort-key="created">Created</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td class="column-no-padding" :data="data[indextr].name">
                        <div @contextmenu.prevent="$refs.context_menu.open($event, data[indextr])">
                            <vs-chip>
                                {{data[indextr].name}}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].status">
                        {{data[indextr].status}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].commission">
                        {{data[indextr].commission.toFixed(2)}} €
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].ranking">
                        {{data[indextr].ranking.toFixed(2)}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].ranking">
                        {{data[indextr].created}}
                    </vs-td>
                </vs-tr>
            </template>
        </vs-table>

        <!-- Landings popup -->
        <vs-popup title="Landings" :active.sync="showPopup['landings']">
            <div class="w-full vx-row">
                <div class="vx-col w-full">
                    <h2>Product JSON</h2>
                    <vx-input-group class="w-full mb-base">
                        <vs-input v-model="productJson" disabled="disabled" />
                        
                        <template slot="append">
                            <div class="append-text btn-addon">
                                <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-copy" v-clipboard="productJson" v-clipboard:success="onProductJsonCopied"></vs-button>
                            </div>
                        </template>
                    </vx-input-group>
                </div>
            </div>

            <vs-table v-model="selectedLandings" stripe multiple search :data="landings">

                <template slot="thead">
                    <vs-th sort-key="name">Name</vs-th>
                    <vs-th sort-key="epc">EPC</vs-th>
                    <vs-th sort-key="">Preview</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td class="column-no-padding" :data="data[indextr].name">
                            {{data[indextr].name}}
                        </vs-td>

                        <vs-td class="column-no-padding" :data="data[indextr].epc">
                            {{data[indextr].epc}}
                        </vs-td>

                        <vs-td class="column-no-padding">
                            <a :href="data[indextr].landing_preview_url" target="_blank">
                                <img :src="data[indextr].landing_preview_image" class="landing-preview" />
                            </a>
                        </vs-td>
                    </vs-tr>
                </template>
            </vs-table>
        </vs-popup>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'

export default {
  components: {
    VueContext,
  },
  data () {
    return {
      userInfo: {},

      objects: [],
      selected: [],
      deleted: [],

      maxItems: 20,

      showPopup: {
        'landings': false,
      },

      landings: [],
      selectedLandings: [],

      productJson: '',
    }
  },
  computed: {
    paginationPageSize () {
      if (this.gridApi) return this.gridApi.paginationGetPageSize()
      else return 50
    },
    totalPages () {
      if (this.gridApi) return this.gridApi.paginationGetTotalPages()
      else return 0
    },
    currentPage: {
      get () {
        if (this.gridApi) return this.gridApi.paginationGetCurrentPage() + 1
        else return 1
      },
      set (val) {
        this.gridApi.paginationGoToPage(val - 1)
      }
    },
  },
  methods: {
    getObjectIndexById(id) {
      let index = -1
      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]
        if (obj.id == id) {
          index = i
          break
        }
      }
      return index
    },
    async retrieveObjects() {
      this.$vs.loading()

      this.selected = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Retrieve products
      const response = await this.$http.get('/api/3rd/marketplace/campaigns', { headers })
      this.objects = response.data.data
      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        // Get commission as float
        let commission = (obj.default_commission.split(' '))[0]
        commission = commission.replace(',', '.')
        commission = parseFloat(commission)
        this.objects[i].commission = commission

        // Get ranking as float
        let ranking = 0
        if (obj.ranking_value != null) {
          ranking = parseFloat(obj.ranking_value)
        }
        this.objects[i].ranking = ranking
      }

      // Sort by created
      this.objects.sort(function (a, b) {
        if (a > b) {
          return 1
        } else if (a < b) {
          return -1
        } else {
          return 0
        }
      })

      this.$vs.loading.close()
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    async showLandings(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      const response = await this.$http.get('/api/3rd/marketplace/campaigns/' + id + '/landings', { headers })
      this.landings = response.data.data

      // Adjust landing preview URLs by inserting pub_id (which is userInfo.source_id)
      for (let i = 0; i < this.landings.length; i++) {
        let landing = this.landings[i]
        if (landing.hasOwnProperty('landing_preview_url')) {
          let effectiveUrl = landing.landing_preview_url
          let u = new URL(effectiveUrl)
          if (u.searchParams.has('pub_id')) {
            u.searchParams.set('pub_id', this.userInfo.source_id)
          }
          if (u.searchParams.has('ref')) {
            u.searchParams.set('ref', this.userInfo.source_id)
          }
          effectiveUrl = u.href
          this.landings[i].landing_preview_url = effectiveUrl
        }
      }

      this.productJson = await this.fetchProductJson(id)

      this.showPopup['landings'] = true

      this.$vs.loading.close()
    },
    async fetchProductJson(campaignId) {
      // Get product JSON of the specified campaign
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      const response = await this.$http.get('/api/3rd/marketplace/campaigns/' + campaignId + '/product-json', { headers })
      return response.data.data
    },
    onProductJsonCopied() {
      this.$vs.notify({
        title: 'Success',
        text: 'Product JSON copied',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
  },
  async created() {
    // Get user info
    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }
    const response = await this.$http.get('/api/3rd/marketplace/me', { headers })
    this.userInfo = response.data.data

    // Get marketplace campaigns
    await this.retrieveObjects()
  },
}

</script>

<style scoped>
.column-no-padding {
  padding-top: 0px !important;
  padding-bottom: 0px !important;
  white-space: nowrap;
}
.landing-preview {
  max-height: 50px;
}
</style>
