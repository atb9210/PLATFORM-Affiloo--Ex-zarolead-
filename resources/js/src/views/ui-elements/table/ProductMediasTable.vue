<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Product Medias" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showPopup['create'] = true"></vs-button>
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
                    <label>Product</label>
                    <v-select autocomplete :options="productOptions" v-model="filters.product" />
                </div>
            </div>
            <div class="vx-row mt-6">
                <div class="vx-col w-full">
                    <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
                    <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
                </div>
            </div>
        </div>

        <div class="vx-row mb-2">
            <div class="vx-col md:w-1/3 w-full">
                <div>
                    Free space: {{storageFreeSpace}} MB / {{storageTotalSpace}} MB ({{storageFreeSpacePercentage.toFixed(1)}}%)
                </div>
                <vs-progress height="12" :percent="storageFreeSpacePercentage" :color="(storageFreeSpacePercentage >= 60) ? 'success' : ((storageFreeSpacePercentage >= 30) ? 'warning' : 'danger')"></vs-progress>
            </div>
        </div>

        <div v-if="showImagesGrid" class="images-grid w-full">
          <vs-images>
            <vs-image :src="(object.type == 'video') ? videoThumbnail : object.url_preview" :key="index" v-for="(object, index) in objects" @click.native="showProductMedia(object.id)" />
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
                <vs-th sort-key="pathname">Pathname</vs-th>
                <vs-th sort-key="product.name">Product</vs-th>
                <vs-th>Preview</vs-th>
                <vs-th>Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].pathname">
                        {{data[indextr].pathname}}
                    </vs-td>

                    <vs-td :data="data[indextr].product.name">
                        {{data[indextr].product.name}}
                    </vs-td>

                    <vs-td>
                        <a :href="data[indextr].url" target="_blank">
                            <img :src="(data[indextr].type == 'video') ? videoThumbnail : data[indextr].url_preview" style="border: 1px solid gray; width: 80px;">
                        </a>
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center">
                            <vs-button @click="showProductMedia(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-eye"></vs-button>
                            <a :href="data[indextr].url_preview"><vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-download"></vs-button></a>
                            <vs-button v-if="canCreateAndEdit" @click="deleteObject(data[indextr].id)" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash"></vs-button>
                        </div>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Create popup -->
        <vs-popup title="Add product media" :active.sync="showPopup['create']">
            <div class="vx-row mb-6 flex items-center" vs-justify="center">
                <vs-row vs-justify="center" class="mb-3">
                    <vs-col type="flex" vs-justify="center" vs-align="center" vs-w="6">
                        <vs-select v-model="product" class="w-full select-large" label="Product" v-validate="'required'" autocomplete>
                            <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in productOptions" class="w-full" />
                        </vs-select>
                        <span class="text-danger">{{ errors.first('product_id') }}</span>
                    </vs-col>
                </vs-row>

                <vs-row vs-justify="center" class="mb-3">
                    <vs-col type="flex" vs-justify="center" vs-align="center" vs-w="6">
                        <vs-select v-model="type" class="w-full select-large" label="Type" v-validate="'required'" @change="changeType">
                            <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in typesOptions" class="w-full" />
                        </vs-select>
                        <span class="text-danger">{{ errors.first('type') }}</span>
                    </vs-col>
                </vs-row>

                <vs-row vs-justify="center" class="mb-3 mt-3">
                    <vs-col type="flex" vs-justify="center" vs-align="center" vs-w="6">
                        <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-image" class="w-full" @click="showFilePicker('main')">Upload media</vs-button>
                        <div class="vx-col w-full">
                            <vs-progress :height="12" :percent="uploadProgress" color="success"></vs-progress>
                        </div>
                        <div class="vx-col w-full flex items-center">
                            <span class="ml-auto mr-auto">{{ (filesToUpload.length == 0) ? 'No file selected' : ((filesToUpload.length == 1) ? filesToUpload[0].name : filesToUpload.length + ' ' + type + 's') }}</span>
                        </div>
                        <div class="vx-col w-full flex items-center">
                            <span class="text-danger">{{ errors.first('file') }}</span>
                        </div>
                    </vs-col>
                    <input type="file" ref="fileUpload" class="hidden" :accept="type + '/*'" multiple @change="onFilesUploadChange">
                </vs-row>

                <vs-row vs-justify="center" class="mb-3 mt-3">
                    <vs-col type="flex" vs-justify="center" vs-align="center" vs-w="6">
                        <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-plus" class="w-full" @click="createObjects()">Create product media</vs-button>
                    </vs-col>
                </vs-row>
            </div>
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Ad details" :active.sync="showPopup['edit']">
          <div class="vx-row mb-2">
            <div class="vx-col w-1/5">
            </div>
            <div class="vx-col w-3/5">
              <div>
                  Type: <span>{{editingObject.type}}</span>
              </div>
              <div>
                Main: <a :href="editingObject.url">Download</a>
              </div>
              <div>
                Preview: <a :href="editingObject.url_preview">Download</a>
              </div>
              <div class="w-full">
                <img class="w-full" :src="editingObject.url_preview" style="border: 1px solid gray" />
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
        product: '',
      },

      showImagesGrid: false,

      productOptions: [],
      typesOptions: ['image', 'video'],

      product: '',
      type: 'image',

      showPopup: {
        'create': false,
        'edit': false,
      },
      adModel: [],
      adModelValues: {},
      editingObject: {
        id: -1,
        type: '',
        url: '',
        url_preview: '',
      },
      
      filesToUpload: [],
      isUploading: false,
      uploadProgress: 0,
      numOfFilesToUpload: 0,

      images: {
        'image': 'https://www.iconfinder.com/data/icons/set-app-incredibles/24/Image-01-512.png',
        'video': 'https://icon-library.com/images/media-icon-png/media-icon-png-0.jpg',
      },

      videoThumbnail: 'data:image/png;base64,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',

      storageFreeSpace: 0,
      storageTotalSpace: 0,
      storageFreeSpacePercentage: 0,
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
        this.$http.delete('/api/v1/product-medias/' + ad.id, { headers })
          .then((response) => {
            this.deleted.push(ad.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Product media(s) deleted',
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

      this.$http.delete('/api/v1/product-medias/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Product media deleted',
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
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
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
      if (this.filters.product != '') {
        data['filter_product'] = this.filters.product.id
      }

      // Retrieve product medias
      this.$http.get('/api/v1/product-medias', { headers, params: data })
        .then((response) => {
          this.objects = response.data.data

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
    showProductMedia(id) {
      let objIndex = this.getObjectIndexById(id)
      if (objIndex < 0) {
        return
      }
      let obj = this.objects[objIndex]
      this.editingObject = obj
      this.showPopup['edit'] = true
    },
    changeType() {
      this.clearFileToUpload()
    },
    showFilePicker(what = 'main') {
      if (what == 'preview') {
        this.$refs.filePreviewUpload.click()
      } else {
        this.$refs.fileUpload.click()
      }
    },
    clearFileToUpload() {
      this.filesToUpload = []
    },
    createObjects() {
      if (this.filesToUpload.length == 0) return;

      // Create product medias
      this.$vs.loading()
      let headers = {
        'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
        'Content-Type': 'multipart/form-data',
      }
      let config = {
        headers: headers,
        onUploadProgress: e => {
          if (e.lengthComputable) {
            this.uploadProgress = (e.loaded / e.total) * 100
          }
        }
      }
      this.numOfFilesToUpload = this.filesToUpload.length
      let filesUploaded = 0

      for (let i = 0; i < this.filesToUpload.length; i++) {
        let fileToUpload = this.filesToUpload[i]
        let formData = new FormData()
        formData.append('product_id', this.product)
        formData.append('type', this.type)
        formData.append('file', fileToUpload)
        this.$http.post('/api/v1/product-medias', formData, config)
          .then((response) => {
            filesUploaded += 1
            if (filesUploaded == this.numOfFilesToUpload) {
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Success',
                text: 'Product media(s) created',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.showPopup['create'] = false
              this.clearForm()
              this.applyFilters()
            }
          })
          .catch((error) => {
            this.$vs.loading.close()
            this.errors.clear()
            for (const [key, value] of Object.entries(error.response.data.errors)) {
              for (let i = 0; i < value.length; i++) {
                this.errors.add({
                  field: key,
                  msg: value[i]
                })
              }
            }
            this.$vs.notify({
              title: 'Error',
              text: error.message,
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'danger'
            })
          })
      }
    },
    onFilesUploadChange(event) {
      this.filesToUpload = event.target.files
    },
    clearForm() {
      this.product = ''
      this.type = 'image'
      this.filesToUpload = []
      this.uploadProgress = 0
    },
  },
  created() {
    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve products
    this.$vs.loading()
    this.$http.get('/api/v1/products', { headers })
      .then((response) => {
        this.productOptions = []
        response.data.data.forEach((product) => {
          this.productOptions.push({
            id: product.id,
            label: product.name,
          })
        })

        // Retrieve storage info
        this.$http.get('/api/v1/storage', { headers })
          .then((response) => {
            this.storageFreeSpace = response.data.data.free_space
            this.storageTotalSpace = response.data.data.total_space
            this.storageFreeSpacePercentage = (this.storageFreeSpace / this.storageTotalSpace)*100
            this.$vs.loading.close()
          })
          .catch((error) => {
            this.$vs.loading.close()
          })
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

    this.resetFilters()
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
