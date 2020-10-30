<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Ads" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <RouterLink to="/dashboard/ads/builder" v-if="canCreateAndEdit">
                <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus"></vs-button>
            </RouterLink>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" :icon="(showImagesGrid) ? 'icon-list' : 'icon-grid'" @click="showImagesGrid = !showImagesGrid"></vs-button>
            <vs-button v-if="canCreateAndEdit" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="deleteSelected"></vs-button>
        </p>

        <div v-if="showFilters" class="mb-12">
            <div class="vx-row mb-2">
                <div class="vx-col">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('today')" icon-pack="feather" icon="icon-calendar">Today</vs-button>
                </div>
                <div class="vx-col">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('yesterday')" icon-pack="feather" icon="icon-calendar">Yesterday</vs-button>
                </div>
                <div class="vx-col">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('this_month')" icon-pack="feather" icon="icon-calendar">This Month</vs-button>
                </div>
                <div class="vx-col">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('last_month')" icon-pack="feather" icon="icon-calendar">Last Month</vs-button>
                </div>
            </div>

            <div class="vx-row mb-2">
                <div class="vx-col w-1/2">
                    <label>From</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_from" />
                </div>
                <div class="vx-col w-1/2">
                    <label>To</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_to" />
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col w-1/2">
                    <label>Form</label>
                    <v-select :options="productOptions" v-model="filters.product" />
                </div>
                <div class="vx-col w-1/2">
                    <label>Traffic Source</label>
                    <v-select :options="trafficSourceOptions" v-model="filters.traffic_source" />
                </div>
            </div>
            <div class="vx-row mt-6">
                <div class="vx-col w-full">
                    <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
                    <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
                </div>
            </div>
        </div>

        <div v-if="showImagesGrid" class="images-grid w-full">
          <vs-images>
            <vs-image :src="object.file_url_preview" :key="index" v-for="(object, index) in objects" @click.native="showAdPopup(object.id)" />
          </vs-images>
        </div>

        <vs-dropdown v-if="!showImagesGrid" vs-trigger-click class="cursor-pointer">
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

        <vs-table v-if="!showImagesGrid" stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="product.name">Product</vs-th>
                <vs-th sort-key="traffic_source.name">Traffic Source</vs-th>
                <vs-th sort-key="ctr">CTR</vs-th>
                <vs-th sort-key="cpc">CPC</vs-th>
                <vs-th sort-key="cpa">CPA</vs-th>
                <vs-th>Preview</vs-th>
                <vs-th>Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].product.name">
                        {{data[indextr].product.name}}
                    </vs-td>

                    <vs-td :data="data[indextr].traffic_source.name">
                        {{data[indextr].traffic_source.name}}
                    </vs-td>

                    <vs-td :data="data[indextr].ctr">
                        {{data[indextr].ctr}} %
                    </vs-td>

                    <vs-td :data="data[indextr].cpc">
                        € {{data[indextr].cpc}}
                    </vs-td>

                    <vs-td :data="data[indextr].cpa">
                        € {{data[indextr].cpa}}
                    </vs-td>

                    <vs-td>
                        <a :href="data[indextr].product_media.url" target="_blank">
                            <img :src="(data[indextr].product_media.type == 'image') ? data[indextr].product_media.url_preview : ((data[indextr].hasPreview) ? data[indextr].product_media_preview.url_preview : dropboxThumbnail)" style="border: 1px solid gray; width: 80px;">
                        </a>
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center">
                            <vs-button @click="showAdPopup(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-eye"></vs-button>
                            <vs-button v-if="canCreateAndEdit" @click="goToAdEditor(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-edit"></vs-button>
                            <a :href="data[indextr].product_media.url_preview"><vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-download"></vs-button></a>
                            <vs-button v-if="canCreateAndEdit" @click="deleteObject(data[indextr].id)" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash"></vs-button>
                        </div>
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td>
                    {{total_stats.ctr}} %
                </vs-td>
                <vs-td>
                    € {{total_stats.cpc}}
                </vs-td>
                <vs-td>
                    € {{total_stats.cpa}}
                </vs-td>
                <vs-td>
                </vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>

        <!-- Ad popup -->
        <vs-popup title="Ad details" :active.sync="showPopup">
          <div class="vx-row mb-2">
            <div class="vx-col w-1/5">
            </div>
            <div class="vx-col w-3/5">
              <div class="w-full" v-if="canCreateAndEdit" >
                <RouterLink class="mr-auto ml-auto mb-2" :to="'/dashboard/ads/editor/' + editingObject.id" v-if="canCreateAndEdit" @click="showPopup = false">
                  <vs-button class="mr-auto ml-auto mb-2" color="primary" type="filled" icon-pack="feather" icon="icon-edit" @click="showPopup = false"></vs-button>
                </RouterLink>
              </div>
              <vs-textarea :label="item" disabled="true" :data="adModel" :key="index" v-for="(item, index) in adModel" v-model="adModelValues[item]"/>
              <div>
                  Type: <span>{{editingObject.product_media.type}}</span>
              </div>
              <div>
                Main: <a :href="editingObject.product_media.url_preview">Download</a>
              </div>
              <div v-if="editingObject.hasPreview">
                Preview: <a :href="editingObject.product_media_preview.url_preview">Download</a>
              </div>
              <div class="w-full" v-if="editingObject.hasPreview">
                <img class="w-full" :src="editingObject.product_media_preview.url_preview" style="border: 1px solid gray" />
              </div>
            </div>
            <div class="vx-col w-1/5">
            </div>
          </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import VueApexCharts from 'vue-apexcharts'

export default {
  components: {
    'v-select': vSelect,
    VueApexCharts,
  },
  data () {
    return {
      objects: [],
      selected: [],
      deleted: [],

      imagesLoaded: 0,
      images: [],

      maxItems: 20,

      total_stats: {
        ctr: 0.0,
        cpc: 0.0,
        cpa: 0.0,
      },

      dateFormat: 'yyyy-MM-dd',
      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        traffic_source: '',
        product: '',
      },

      trafficSourceOptions: [],
      productOptions: [],

      showImagesGrid: false,

      showPopup: false,
      adModel: [],
      adModelValues: {},
      editingObject: {
        id: 0,
        product_media: {},
      },

      dropboxThumbnail: 'https://www.shareicon.net/data/512x512/2016/07/10/119920_dropbox_512x512.png',
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
      },
    },
    canCreateAndEdit() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin') ||
        this.$store.getters.userHasRole('mediabuyer') || this.$store.getters.userHasRole('buyer'))
    }
  },
  methods: {
    getObjectIndexById(id) {
      var index = -1;
      this.objects.forEach((obj, i) => {
        if (obj.id == id) {
          index = i
          return
        }
      })
      return index
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    deleteSelected() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      this.deleted = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.selected.forEach((ad) => {
        this.$http.delete('/api/v1/ads/' + ad.id, { headers })
          .then((response) => {
            this.deleted.push(ad.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Ad(s) deleted',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.selected = []
              this.deleted = []
              this.applyFilters()
            }
          })
          .catch((error) => {
            this.$vs.loading.close()
            this.$vs.notify({
              title: 'Error',
              text: error.message,
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'danger'
            })
          })
      })
    },
    deleteObject(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$http.delete('/api/v1/ads/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Ad deleted',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.applyFilters()
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    },
    goToAdEditor(id) {
      this.$router.push({name: 'ad-editor', params: {ad_id: id}})
    },
    onCodeCopied() {
      this.$vs.notify({
        title: 'Success',
        text: 'Form code copied',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    calculateStats() {
      this.total_stats = {
        ctr: 0.0,
        cpc: 0.0,
        cpa: 0.0,
      }

      for (var i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        this.total_stats.ctr += parseFloat(obj.ctr)
        this.total_stats.cpc += parseFloat(obj.cpc)
        this.total_stats.cpa += parseFloat(obj.cpa)
      }

      this.total_stats.ctr /= ((this.objects.length > 0) ? this.objects.length : 1)
      this.total_stats.cpc /= ((this.objects.length > 0) ? this.objects.length : 1)
      this.total_stats.cpa /= ((this.objects.length > 0) ? this.objects.length : 1)

      this.total_stats.ctr = this.total_stats.ctr.toFixed(2)
      this.total_stats.cpc = this.total_stats.cpc.toFixed(2)
      this.total_stats.cpa = this.total_stats.cpa.toFixed(2)
    },
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        traffic_source: '',
        product: '',
      }
      this.filterDatesShortcut('this_month')
    },
    applyFilters() {
      this.$vs.loading()

      this.selected = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {}
      if (this.filters.date_from != '') {
        data['filter_date_from'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        data['filter_date_to'] = this.filters.date_to
      }
      if (this.filters.traffic_source != '') {
        data['filter_traffic_source'] = this.filters.traffic_source.id
      }
      if (this.filters.product != '') {
        data['filter_product'] = this.filters.product.id
      }

      // Retrieve ads
      this.$http.get('/api/v1/ads', { headers, params: data })
        .then((response) => {
          this.objects = response.data.data

          this.calculateStats()
          this.$vs.loading.close()
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    },
    filterDatesShortcut(period) {
      let invalidPeriod = false;

      switch (period) {
        case 'today': {
          let today = new Date();
          this.filters.date_from = this.formatDate(today);
          this.filters.date_to = this.formatDate(today);
        } break;
        case 'yesterday': {
          let dateObj = new Date();
          dateObj.setDate(dateObj.getDate() - 1);
          this.filters.date_from = this.formatDate(dateObj);
          this.filters.date_to = this.formatDate(dateObj);
        } break;
        case 'this_month': {
          let dateObj = new Date();
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1);
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0);
          this.filters.date_from = this.formatDate(fromObj);
          this.filters.date_to = this.formatDate(toObj);
        } break;
        case 'last_month': {
          let dateObj = new Date();
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth() - 1, 1);
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 0);
          this.filters.date_from = this.formatDate(fromObj);
          this.filters.date_to = this.formatDate(toObj);
        } break;
        default: {
          invalidPeriod = true;
        } break;
      }

      if(!invalidPeriod) {
        this.applyFilters();
      }
    },
    formatDate(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0');
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0'); //January is 0!
      let yyyy = dateObj.getFullYear();
      
      let formattedDate = yyyy + '-' + mm + '-' + dd;
      return formattedDate;
    },
    showAdPopup(id) {
      this.showAdId = id
      let objIndex = this.getObjectIndexById(id)
      if (objIndex < 0) {
        return
      }
      let obj = this.objects[objIndex]

      this.editingObject = obj

      this.adModel = []
      this.adModelValues = {}
      for (const [key, value] of Object.entries(obj.info)) {
        this.adModel.push(key)
        this.adModelValues[key] = value
      }

      this.showPopup = true
    },
  },
  created() {
    this.resetFilters()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve traffic sources
    this.$vs.loading()
    this.$http.get('/api/v1/traffic-sources/', { headers })
      .then((response) => {
        this.trafficSourceOptions = []
        response.data.data.forEach((traffic_source) => {
          this.trafficSourceOptions.push({
            id: traffic_source.id,
            label: traffic_source.name,
          })
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

    // Retrieve products
    this.$vs.loading()
    this.$http.get('/api/v1/products', { headers })
      .then((response) => {
        this.productOptions = []
        response.data.data.forEach((products) => {
          this.productOptions.push({
            id: product.id,
            label: product.name,
          })
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })
  },
  mounted () {
  }
}

</script>

<style>
.images-grid {
    max-height: 500px;
    overflow: auto;
}
</style>
