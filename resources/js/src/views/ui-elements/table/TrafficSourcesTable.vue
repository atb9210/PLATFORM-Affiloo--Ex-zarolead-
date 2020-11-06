<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Traffic sources" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create')"></vs-button>
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>
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

        <vs-table stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="id">Id</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th>Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td :data="data[indextr].name">
                        <vs-chip>
                            <vs-avatar :src="data[indextr].icon"/>
                            {{data[indextr].name}}
                        </vs-chip>
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center">
                            <vs-button @click="editForm(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-edit"></vs-button>
                            <vs-button @click="deleteObject(data[indextr].id)" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash"></vs-button>
                        </div>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Create popup -->
        <vs-popup title="Create Traffic source" :active.sync="showPopup['create']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" color="primary">{{newObject['create'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col md:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col md:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col md:w-1/3 w-full">
                </div>
                <div class="vx-col md:w-1/3" vs-justify="center" vs-align="center">
                    <vs-card class="cardx">
                        <div slot="header">
                            <h3 class="mr-auto ml-auto text-center">
                                <span class="mr-auto ml-auto">{{ (newObject['create'].fileToUpload == '') ? 'No icon selected' : 'Icon selected' }}</span>
                            </h3>
                        </div>
                        <div slot="media" class="mb-3">
                            <img :src="uploadCardImage('create')">
                        </div>
                        <div slot="footer">
                            <vs-row vs-justify="center">
                                <vs-button class="mr-2" color="primary" icon-pack="feather" icon="icon-file-plus" :disabled="newObject['create'].fileToUpload != ''" @click="showFilePicker('create')"></vs-button>
                                <vs-button class="mr-2" color="danger" icon-pack="feather" icon="icon-trash" :disabled="newObject['create'].fileToUpload == ''" @click="clearFileToUpload('create')"></vs-button>
                            </vs-row>
                        </div>
                    </vs-card>
                    <div class="vx-row mb-6 flex items-center">
                        <!--
                        <div class="vx-col w-full flex items-center">
                            <span class="ml-auto mr-auto text-danger">{{ errors.first('icon') }}</span>
                        </div>
                        -->
                        <div class="vx-col w-full flex items-center">
                            <span class="ml-auto mr-auto">Upload: {{newObject['create'].uploadProgress}}%</span>
                        </div>
                        <div class="vx-col w-full">
                            <vs-progress :height="12" :percent="newObject['create'].uploadProgress" color="success"></vs-progress>
                        </div>
                    </div>
                    <input type="file" ref="fileUploadCreate" class="hidden" @change="onFileUploadChangeCreate" accept="image/*">
                </div>
                <div class="vx-col md:w-1/3 w-full">
                </div>
            </div>

            <div class="vx-row mb-6" v-for="(custom_param, indexparam) in newObject['create'].custom_params">
                <div class="vx-col md:w-1/3 w-full">
                    <span v-if="indexparam == 0">Custom Params</span>
                </div>
                <div class="vx-col md:w-2/3 w-full flex items-center">
                    <vs-input class="w-1/3" icon-pack="feather" icon="icon-code" placeholder="Query Param" icon-no-border v-model="newObject['create'].custom_params[indexparam].query_param" />
                    <vs-input class="w-1/3" icon-pack="feather" icon="icon-code" placeholder="Token" icon-no-border v-model="newObject['create'].custom_params[indexparam].token" />
                    <vs-input class="w-1/3" icon-pack="feather" icon="icon-code" placeholder="Name" icon-no-border v-model="newObject['create'].custom_params[indexparam].name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col md:w-1/3 w-full">
                </div>
                <div class="vx-col md:w-2/3 w-full flex items-center">
                    <div class="md:w-1/3"></div>
                    <div class="md:w-1/3"></div>
                    <vs-button class="md:w-1/3" icon-pack="feather" icon="icon-plus" @click="addNewCustomParam('create')">Add</vs-button>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Edit Traffic source" :active.sync="showPopup['edit']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" color="primary">{{newObject['edit'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col md:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col md:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col md:w-1/3 w-full">
                </div>
                <div class="vx-col md:w-1/3" vs-justify="center" vs-align="center">
                    <vs-card class="cardx">
                        <div slot="header">
                            <h3 class="mr-auto ml-auto text-center">
                                <span class="mr-auto ml-auto">{{ (newObject['edit'].fileToUpload == '') ? 'No icon selected' : 'Icon selected' }}</span>
                            </h3>
                        </div>
                        <div slot="media" class="mb-3">
                            <img :src="uploadCardImage('edit')">
                        </div>
                        <div slot="footer">
                            <vs-row vs-justify="center">
                                <vs-button class="mr-2" color="primary" icon-pack="feather" icon="icon-file-plus" :disabled="newObject['edit'].fileToUpload != ''" @click="showFilePicker('edit')"></vs-button>
                                <vs-button class="mr-2" color="danger" icon-pack="feather" icon="icon-trash" :disabled="newObject['edit'].fileToUpload == ''" @click="clearFileToUpload('edit')"></vs-button>
                            </vs-row>
                        </div>
                    </vs-card>
                    <div class="vx-row mb-6 flex items-center">
                        <!--
                        <div class="vx-col w-full flex items-center">
                            <span class="ml-auto mr-auto text-danger">{{ errors.first('icon') }}</span>
                        </div>
                        -->
                        <div class="vx-col w-full flex items-center">
                            <span class="ml-auto mr-auto">Upload: {{newObject['edit'].uploadProgress}}%</span>
                        </div>
                        <div class="vx-col w-full">
                            <vs-progress :height="12" :percent="newObject['edit'].uploadProgress" color="success"></vs-progress>
                        </div>
                    </div>
                    <input type="file" ref="fileUploadEdit" class="hidden" @change="onFileUploadChangeEdit" accept="image/*">
                </div>
                <div class="vx-col md:w-1/3 w-full">
                </div>
            </div>

            <div class="vx-row mb-6" v-for="(custom_param, indexparam) in newObject['edit'].custom_params">
                <div class="vx-col md:w-1/3 w-full">
                    <span v-if="indexparam == 0">Custom Params</span>
                </div>
                <div class="vx-col md:w-2/3 w-full flex items-center">
                    <vs-input class="w-1/3" icon-pack="feather" icon="icon-code" placeholder="Query Param" icon-no-border v-model="newObject['edit'].custom_params[indexparam].query_param" />
                    <vs-input class="w-1/3" icon-pack="feather" icon="icon-code" placeholder="Token" icon-no-border v-model="newObject['edit'].custom_params[indexparam].token" />
                    <vs-input class="w-1/3" icon-pack="feather" icon="icon-code" placeholder="Name" icon-no-border v-model="newObject['edit'].custom_params[indexparam].name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col md:w-1/3 w-full">
                </div>
                <div class="vx-col md:w-2/3 w-full flex items-center">
                    <div class="w-1/3"></div>
                    <div class="w-1/3"></div>
                    <vs-button class="w-1/3" icon-pack="feather" icon="icon-plus" @click="addNewCustomParam('edit')">Add</vs-button>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateObject" color="primary" type="filled" :disabled="!isValidForm('edit')">Update</vs-button>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
export default {
  data () {
    return {
      objects: [],
      selected: [],
      deleted: [],

      maxItems: 20,

      editingObject: -1,

      showPopup: {
        'create': false,
        'edit': false,
      },
      newObject: {
        'create': {
          name: '',
          fileToUpload: '',
          imagePreviewUrl: '',
          uploadProgress: 0,
          custom_params: [
            {
              query_param: '',
              token: '',
              name: '',
            }
          ],
        },
        'edit': {
          name: '',
          fileToUpload: '',
          imagePreviewUrl: '',
          uploadProgress: 0,
          custom_params: [
            {
              query_param: '',
              token: '',
              name: '',
            }
          ],
        },
      },
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
      var index = -1;
      this.objects.forEach((obj, i) => {
        if (obj.id == id) {
          index = i
          return
        }
      })
      return index
    },
    retrieveObjects() {
      this.selected = []
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Retrieve traffic sources
      this.$http.get('/api/v1/traffic-sources', { headers })
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
    onPageChanged(val) {
      this.currentPage = val
    },
    confirmDeletePopup() {
      if (this.selected.length == 0) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Delete confirmation',
        text: 'Are you sure you want to delete ' + this.selected.length + ' object(s)?',
        accept: this.deleteSelected
      })
    },
    deleteSelected() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      this.deleted = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.selected.forEach((trafficSource) => {
        this.$http.delete('/api/v1/traffic-sources/' + trafficSource.id, { headers })
          .then((response) => {
            this.deleted.push(trafficSource.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Traffic source(s) deleted',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.selected = []
              this.deleted = []
              this.retrieveObjects()
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
    showForm(type) {
      this.showPopup[type] = true
    },
    clearForm(type) {
      this.newObject[type] = {
        name: '',
        fileToUpload: '',
        imagePreviewUrl: '',
        uploadProgress: 0,
        custom_params: [
          {
            query_param: '',
            token: '',
            name: '',
          }
        ],
      }
    },
    isValidForm(type) {
      let obj = this.newObject[type]
      return (obj.name.length > 0)
    },
    createObject() {
      this.$vs.loading()

      let headers = {
        'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
        'Content-Type': 'multipart/form-data',
      }

      let config = {
        headers: headers,
          onUploadProgress: e => {
            if (e.lengthComputable) {
              this.newObject['create'].uploadProgress = (e.loaded / e.total) * 100
            }
          }
      }

      let custom_params = []
      this.newObject['create'].custom_params.forEach((custom_param) => {
        if (custom_param.query_param == '' && custom_param.token == '') return
        custom_params.push(custom_param)
      })

      let formData = new FormData()
      formData.append('name', this.newObject['create'].name)
      formData.append('custom_params', JSON.stringify(custom_params))
      formData.append('icon', this.newObject['create'].fileToUpload)

      // Create traffic source
      this.$http.post('/api/v1/traffic-sources', formData, config)
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Traffic source created',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.clearForm('create')
          this.showPopup['create'] = false
          this.retrieveObjects()
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
    editForm(id) {
      let obj = this.objects[this.getObjectIndexById(id)]

      let custom_params = [
        {
          query_param: '',
          token: '',
          name: '',
        }
      ]
      if (obj.custom_params.length > 0) {
        custom_params = obj.custom_params
      }
      this.newObject['edit'] = {
        name: obj.name,
        custom_params: custom_params,
        fileToUpload: '',
        imagePreviewUrl: obj.icon,
        uploadProgress: 0,
      }
      this.editingObject = obj.id
      this.showForm('edit')
    },
    updateObject() {
      if (this.editingObject <= 0) return

      let id = this.editingObject

      this.$vs.loading()

      let headers = {
        'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
        'Content-Type': 'multipart/form-data',
      }

      let config = {
        headers: headers,
          onUploadProgress: e => {
            if (e.lengthComputable) {
              this.newObject['edit'].uploadProgress = (e.loaded / e.total) * 100
            }
          }
      }

      let custom_params = []
      this.newObject['edit'].custom_params.forEach((custom_param) => {
        if (custom_param.query_param == '' && custom_param.token == '') return
        custom_params.push(custom_param)
      })

      let formData = new FormData()
      formData.append('name', this.newObject['edit'].name)
      formData.append('custom_params', JSON.stringify(custom_params))
      if (this.newObject['edit'].fileToUpload != '') {
        formData.append('icon', this.newObject['edit'].fileToUpload)
      }

      this.$http.post('/api/v1/traffic-sources/' + id + '/update', formData, config)
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Traffic source updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.retrieveObjects()
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
      this.showPopup['edit'] = false
      this.editingObject = -1
    },
    deleteObject(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$http.delete('/api/v1/traffic-sources/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Traffic source deleted',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.retrieveObjects()
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
    addNewCustomParam(type) {
      this.newObject[type].custom_params.push({
        query_param: '',
        token: '',
        name: '',
      })
    },
    showFilePicker(type) {
      if (type == 'create') {
        this.$refs.fileUploadCreate.click()
      } else if (type == 'edit') {
        this.$refs.fileUploadEdit.click()
      }
    },
    onFileUploadChangeCreate(event) {
      let type = 'create'
      this.newObject[type].fileToUpload = event.target.files[0]
      this.newObject[type].imagePreviewUrl = URL.createObjectURL(this.newObject[type].fileToUpload)
    },
    onFileUploadChangeEdit(event) {
      let type = 'edit'
      this.newObject[type].fileToUpload = event.target.files[0]
      this.newObject[type].imagePreviewUrl = URL.createObjectURL(this.newObject[type].fileToUpload)
    },
    isValidUpload(type) {
      return (this.newObject[type].fileToUpload != '')
    },
    clearFileToUpload(type) {
      this.newObject[type].fileToUpload = ''
      if (type == 'create') {
        this.newObject[type].imagePreviewUrl = ''
      } else if (type == 'edit') {
        let obj = this.objects[this.getObjectIndexById(this.editingObject)]
        this.newObject[type].imagePreviewUrl = obj.icon
      }
    },
    uploadCardImage(type) {
      if (this.newObject[type].imagePreviewUrl == '') {
        return 'https://www.iconfinder.com/data/icons/set-app-incredibles/24/Image-01-512.png'
      } else {
        return this.newObject[type].imagePreviewUrl
      }
    },
  },
  created() {
    this.retrieveObjects()
  },
  mounted () {
  }
}

</script>
