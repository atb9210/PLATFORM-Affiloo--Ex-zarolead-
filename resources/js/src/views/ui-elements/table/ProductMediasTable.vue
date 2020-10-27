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
                    <v-select :options="productOptions" v-model="filters.product" />
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
            <vs-image :src="object.file_url_preview" :key="index" v-for="(object, index) in objects" @click.native="showProductMedia(object.id)" />
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
                <vs-th sort-key="pathname">Pathname</vs-th>
                <vs-th>Preview</vs-th>
                <vs-th>Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].product.name">
                        {{data[indextr].product.name}}
                    </vs-td>

                    <vs-td :data="data[indextr].pathname">
                        {{data[indextr].pathname}}
                    </vs-td>

                    <vs-td>
                        <img :src="data[indextr].file_url_preview" style="border: 1px solid gray; width: 80px;">
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center">
                            <vs-button @click="showProductMedia(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-eye"></vs-button>
                            <a :href="data[indextr].file_url"><vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-download"></vs-button></a>
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
                        <vs-select v-model="product" class="w-full select-large" label="Product" v-validate="'required'">
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
                            <span class="ml-auto mr-auto">{{ (fileToUpload == '') ? 'No file selected' : fileToUpload.name }}</span>
                        </div>
                        <div class="vx-col w-full flex items-center">
                            <span class="text-danger">{{ errors.first('file') }}</span>
                        </div>
                    </vs-col>
                    <input type="file" ref="fileUpload" class="hidden" @change="onFileUploadChange" :accept="type + '/*'">
                </vs-row>

                <vs-row vs-justify="center" class="mb-3 mt-3" v-if="type == 'video'">
                    <vs-col type="flex" vs-justify="center" vs-align="center" vs-w="6">
                        <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-image" class="w-full" @click="showFilePicker('preview')">Upload preview</vs-button>
                        <div class="vx-col w-full">
                            <vs-progress :height="12" :percent="uploadPreviewProgress" color="success"></vs-progress>
                        </div>
                        <div class="vx-col w-full flex items-center">
                            <span class="ml-auto mr-auto">{{ (filePreviewToUpload == '') ? 'No preview selected' : filePreviewToUpload.name }}</span>
                        </div>
                        <div class="vx-col w-full flex items-center">
                            <span class="text-danger">{{ errors.first('file_preview') }}</span>
                        </div>
                    </vs-col>
                    <input type="file" ref="filePreviewUpload" class="hidden" @change="onFilePreviewUploadChange" accept="image/*">
                </vs-row>
                <vs-row vs-justify="center" class="mb-3 mt-3">
                    <vs-col type="flex" vs-justify="center" vs-align="center" vs-w="6">
                        <img class="w-full" :src="imagePreviewUrl" style="border: 1px solid gray">
                    </vs-col>
                </vs-row>
                <vs-row vs-justify="center" class="mb-3 mt-3">
                    <vs-col type="flex" vs-justify="center" vs-align="center" vs-w="6">
                        <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-plus" class="w-full" @click="createObject()">Create product media</vs-button>
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
                Main: <a :href="editingObject.file_url">Download</a>
              </div>
              <div>
                Preview: <a :href="editingObject.file_url_preview">Download</a>
              </div>
              <div class="w-full">
                <img class="w-full" :src="editingObject.file_url_preview" style="border: 1px solid gray" />
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
        file_url: '',
        file_url_preview: '',
      },
      
      fileToUpload: '',
      isUploading: false,
      uploadProgress: 0,

      filePreviewToUpload: '',
      isUploadingPreview: false,
      uploadPreviewProgress: 0,

      images: {
        'image': 'https://www.iconfinder.com/data/icons/set-app-incredibles/24/Image-01-512.png',
        'video': 'https://icon-library.com/images/media-icon-png/media-icon-png-0.jpg',
      },
      imagePreviewUrl: '',
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
      this.clearFileToUpload('preview')
    },
    showFilePicker(what = 'main') {
      if (what == 'preview') {
        this.$refs.filePreviewUpload.click()
      } else {
        this.$refs.fileUpload.click()
      }
    },
    onFileUploadChange(event) {
      this.fileToUpload = event.target.files[0]
      if (this.type == 'image') {
        this.imagePreviewUrl = URL.createObjectURL(this.fileToUpload)
      }
    },
    onFilePreviewUploadChange(event) {
      this.filePreviewToUpload = event.target.files[0]
      this.imagePreviewUrl = URL.createObjectURL(this.filePreviewToUpload)
    },
    clearFileToUpload(what = 'main') {
      if (what == 'preview') {
        this.filePreviewToUpload = ''
        this.imagePreviewUrl = ''
      } else {
        this.fileToUpload = ''
        this.filePreviewToUpload = ''
        this.imagePreviewUrl = ''
      }
    },
    createObject() {
      // Create Ad
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
            this.uploadPreviewProgress = (e.loaded / e.total) * 100
          }
        }
      }
      let formData = new FormData()
      formData.append('product_id', this.product)
      formData.append('type', this.type)
      formData.append('file', this.fileToUpload)
      if (this.filePreviewToUpload != '') {
        formData.append('file_preview', this.filePreviewToUpload)
      }
      this.$http.post('/api/v1/product-medias', formData, config)
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Product media created',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.showPopup['create'] = false
          this.applyFilters()
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
        this.$vs.loading.close()
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
