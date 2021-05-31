<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Domains" code-toggler>

        <vue-context ref="upload_context_menu" lazy>
            <li>
              <a href="#" @click="$refs.upload_context_menu.close();showFilePicker('files');" class="flex items-center text-sm">
                <feather-icon icon="FileIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Files</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.upload_context_menu.close();showFilePicker('directories');" class="flex items-center text-sm">
                <feather-icon icon="FolderIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Directories</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.upload_context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <p class="flex items-center mb-4">
            <vs-button v-if="view == 'domains'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create')"></vs-button>
            <vs-button v-if="view == 'domains'" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup"></vs-button>

            <vs-button v-if="view == 'file_manager'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="view = 'domains'"></vs-button>
            <vs-button v-if="view == 'file_manager'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="startCreatingFileOrDir"></vs-button>
            <vs-button v-if="view == 'file_manager'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-upload" @click.prevent="$refs.upload_context_menu.open($event)" @contextmenu.prevent="$refs.upload_context_menu.open($event)"></vs-button>
            <vs-button v-if="view == 'file_manager'" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeleteFilesPopup"></vs-button>
        </p>

        <div v-if="view == 'domains'">

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
                  <a href="#" @click="$refs.context_menu.close();editForm(data.id);" class="flex items-center text-sm">
                    <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Edit</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.context_menu.close();showFileManager(data.id);" class="flex items-center text-sm">
                    <feather-icon icon="FolderIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">File Manager</span>
                  </a>
                </li>
                <li>
                  <a href="#" @click="$refs.context_menu.close()" class="flex items-center text-sm">
                    <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                    <span class="ml-2">Close</span>
                  </a>
                </li>
            </vue-context>

            <vs-table stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

                <template slot="thead">
                    <vs-th sort-key="id">Id</vs-th>
                    <vs-th sort-key="domain">Domain</vs-th>
                    <vs-th sort-key="path">Path</vs-th>
                </template>

                <template slot-scope="{data}">
                    <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                        <vs-td :data="data[indextr].id">
                            {{data[indextr].id}}
                        </vs-td>

                        <vs-td :data="data[indextr].domain">
                            <div @contextmenu.prevent="$refs.context_menu.open($event, data[indextr])">
                                <vs-chip>
                                    {{data[indextr].domain}}
                                </vs-chip>
                            </div>
                        </vs-td>

                        <vs-td :data="data[indextr].path">
                            {{data[indextr].path}}
                        </vs-td>

                    </vs-tr>
                </template>
            </vs-table>

        </div>

        <div class="vx-row" v-else-if="view == 'file_manager'">
            <!-- File explorer -->
            <input type="file" ref="filesUpload" class="hidden" multiple @change="onFilesUploadChange">
            <input type="file" ref="directoriesUpload" class="hidden" webkitdirectory directory multiple @change="onFilesUploadChange">
            <div class="vx-col sm:w-1/2 w-full">
                <vs-table stripe multiple v-model="selectedFilesAndDirectories" search :data="currentDirectory">

                    <template slot="header">
                        <span>
                            <a :key="index" v-for="(item, index) in fileManager.dirLevel" @click="goToDirLevel(index)" class="dir-level">{{item}}/</a>
                        </span>
                    </template>
                    <template slot="thead">
                        <vs-th sort-key="name">Name</vs-th>
                        <vs-th></vs-th>
                        <vs-th></vs-th>
                    </template>

                    <template slot-scope="{data}">

                        <vs-td v-if="isCreatingFileOrDir">
                        </vs-td>
                        <vs-td v-if="isCreatingFileOrDir">
                            <vs-input ref="createFileOrDirInput" v-model="createFileOrDirValue" @keyup.enter="createFileOrDir()" @keyup.escape="dontCreateFileOrDir()" icon-pack="feather" icon="icon-file" placeholder="folder/ or file.ext" class="is-label-placeholder"></vs-input>
                        </vs-td>
                        <vs-td v-if="isCreatingFileOrDir">
                        </vs-td>
                        <vs-td v-if="isCreatingFileOrDir">
                        </vs-td>

                        <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                            <vs-td :data="data[indextr].name">
                                <vs-chip transparent :color="fileManagerObjectColor[tr.type]" @dblclick="(tr.type == 'directory') ? openDir(tr.name) : openFile(tr.pathname)">
                                    <vs-avatar icon-pack="feather" :icon="(tr.type == 'directory') ? 'icon-folder' : 'icon-file'"  @dblclick="(tr.type == 'directory') ? openDir(tr.name) : openFile(tr.pathname)" />
                                    {{data[indextr].name}}
                                </vs-chip>
                            </vs-td>

                            <vs-td></vs-td>
                            <vs-td></vs-td>

                        </vs-tr>
                    </template>
                </vs-table>
            </div>

            <!-- File editor -->
            <div class="vx-col sm:w-1/2 w-full">
                <p class="flex items-center mb-4">
                    <vs-button v-if="view == 'file_manager'" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-save" @click="saveFile"></vs-button>
                    <vs-button v-if="view == 'file_manager'" class="mr-2" color="warning" type="filled" icon-pack="feather" icon="icon-x" @click="cancelFileEdit"></vs-button>
                </p>
                <vs-textarea class="file-editor" :label="fileManager.currentFile + ((fileManager.fileContent != fileManager.fileNewContent) ? ' (* edited)' : '')" v-model="fileManager.fileNewContent" height="50rem" @keydown.tab.prevent="tabber($event)" />
            </div>
        </div>

        <!-- Create popup -->
        <vs-popup title="Create Domain" :active.sync="showPopup['create']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" color="primary">{{newObject['create'].domain}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Domain</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].domain" />
                </div>
            </div>
            
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Path</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].path" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Edit Domain" :active.sync="showPopup['edit']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" color="primary">{{newObject['edit'].domain}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Domain</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].domain" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Path</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input disabled class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].path" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateObject" color="primary" type="filled" :disabled="!isValidForm('edit')">Update</vs-button>
            </div>
        </vs-popup>

        <!-- Update name servers popup -->
        <vs-popup title="Update name servers" :active.sync="showPopup['update_name_servers']">
            <div class="vx-row mb-6">
                <div class="vx-col w-full mb-6">
                    You need to pdate name servers
                </div>
                <div class="vx-col w-full mb-3">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-server" icon-no-border value="marek.ns.cloudflare.com" disabled />
                </div>
                <div class="vx-col w-full" mb-3>
                    <vs-input class="w-full" icon-pack="feather" icon="icon-server" icon-no-border value="naomi.ns.cloudflare.com" disabled />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="showPopup['update_name_servers'] = false" color="primary" type="filled">OK</vs-button>
            </div>
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
      view: 'domains',

      objects: [],
      selected: [],
      deleted: [],

      maxItems: 20,

      editingObject: -1,

      showPopup: {
        'create': false,
        'edit': false,
        'update_name_servers': false,
      },
      newObject: {
        'create': {
          domain: '',
          path: '',
        },
        'edit': {
          domain: '',
          path: '',
        },
      },

      selectedFilesAndDirectories: [],
      fileManager: {
        dirLevel: [],
        content: [],
        fileContent: '',
        fileNewContent: '',
        currentFile: '',
      },
      currentDirectory: [],
      isCreatingFileOrDir: false,
      createFileOrDirValue: '',

      filesToUpload: [],

      fileManagerObjectColor: {
        'directory': '#197da8',
        'file': '',
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

      // Retrieve domains
      this.$http.get('/api/v1/domains', { headers })
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

      this.selected.forEach((obj) => {
        this.$http.delete('/api/v1/domains/' + obj.id, { headers })
          .then((response) => {
            this.deleted.push(obj.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Domain(s) deleted',
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
        domain: '',
        path: '',
      }
    },
    isValidForm(type) {
      let obj = this.newObject[type]
      return (obj.domain.length > 0 && obj.path.length > 0)
    },
    createObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        domain: this.newObject['create'].domain,
        path: this.newObject['create'].path,
      }

      // Create domain
      this.$http.post('/api/v1/domains', data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Domain created',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.clearForm('create')
          this.showPopup['create'] = false
          this.retrieveObjects()
          this.showEditNameServersDialog()
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

      this.newObject['edit'] = {
        domain: obj.domain,
        path: obj.path,
      }
      this.editingObject = obj.id
      this.showForm('edit')
    },
    updateObject() {
      if (this.editingObject <= 0) return

      let id = this.editingObject

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        domain: this.newObject['edit'].domain,
      }

      this.$http.put('/api/v1/domains/' + id, data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Domain updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.retrieveObjects()
          this.showEditNameServersDialog()
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

      this.$http.delete('/api/v1/domains/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Domain deleted',
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
    showEditNameServersDialog() {
      this.showPopup['update_name_servers'] = true
    },
    async showFileManager(id) {
      await this.refreshFileManager(id)
    },
    async refreshFileManager(id) {
      this.$vs.loading()

      this.currentObject = this.objects[this.getObjectIndexById(id)]

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Retrieve directory content
      const response = await this.$http.get('/api/v1/domains/' + id + '/ls', { headers })
      this.fileManager.content = response.data.data
      this.resetDirLevel()

      this.view = 'file_manager'

      this.$vs.loading.close()
    },
    fileManagerGetCurrentDirLevel() {
      let path = this.fileManager.dirLevel[0]
      for (let i = 1; i < this.fileManager.dirLevel.length; i++) {
        path += '/' + this.fileManager.dirLevel[i]
      }
      return path
    },
    resetDirLevel() {
      this.fileManager.dirLevel = []
      this.fileManager.dirLevel.push(this.currentObject.path)
      this.currentDirectory = this.fileManager.content
      this.selectedFilesAndDirectories = []
    },
    goToDirLevel(index) {
      if (index < 0 && index >= this.fileManager.dirLevel.length) return

      this.currentDirectory = this.fileManager.content

      let newDirLevel = []
      newDirLevel.push(this.fileManager.dirLevel[0])

      for (let i = 1; i <= index; i++) {
        for (let j = 0; j < this.currentDirectory.length; j++) {
          if (this.currentDirectory[j].name == this.fileManager.dirLevel[i]) {
            this.currentDirectory = this.currentDirectory[j].data
            break
          }
        }
        newDirLevel.push(this.fileManager.dirLevel[i])
      }

      this.fileManager.dirLevel = newDirLevel
      this.selectedFilesAndDirectories = []
    },
    openDir(name) {
      let newDirLevel = this.fileManager.dirLevel

      for (let j = 0; j < this.currentDirectory.length; j++) {
        if (this.currentDirectory[j].name == name) {
          this.currentDirectory = this.currentDirectory[j].data
          newDirLevel.push(name)
          break
        }
      }

      this.fileManager.dirLevel = newDirLevel
      this.selectedFilesAndDirectories = []
    },
    async openFile(pathname) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        pathname: pathname,
      }

      const response = await this.$http.get('/api/v1/domains/' + this.currentObject.id + '/cat', { headers, params: data })

      this.fileManager.currentFile = pathname
      this.fileManager.fileContent = response.data.data
      this.fileManager.fileNewContent = this.fileManager.fileContent

      this.$vs.loading.close()
    },
    async saveFile() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken'),
        'Content-Type': 'multipart/form-data',
      }

      let path = ''
      let fileName = ''
      let fileContent = this.fileManager.fileNewContent

      let pathname = this.fileManager.currentFile
      let lastIndex = pathname.lastIndexOf('/') + 1
      fileName = pathname.substring(lastIndex)
      path = pathname.substring(0, lastIndex)

      let formData = new FormData()
      formData.append('path', path)
      let blob = new Blob([fileContent])
      formData.append('file', blob, fileName)

      const response = await this.$http.post('/api/v1/domains/' + this.currentObject.id + '/upload', formData, { headers })

      this.fileManager.fileContent = this.fileManager.fileNewContent

      this.$vs.notify({
        title: 'Success',
        text: 'File saved successfully',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()
    },
    cancelFileEdit() {
      this.fileManager.fileNewContent = this.fileManager.fileContent
    },
    startCreatingFileOrDir() {
      this.createFileOrDirValue = ''
      this.isCreatingFileOrDir = true

      this.$nextTick(() => {
        let inputEl = this.$refs.createFileOrDirInput.$el.querySelector('input')
        inputEl.focus()
      });
    },
    async createFileOrDir() {
      this.$vs.loading()

      let pathname = this.createFileOrDirValue
      let type = 'file'
      if (pathname.charAt(pathname.length - 1) == '/') {
        type = 'folder'
      }

      for (let i = this.fileManager.dirLevel.length - 1; i >= 0; i--) {
        pathname = this.fileManager.dirLevel[i] + '/' + pathname
      }

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        pathname: pathname,
      }

      if (type == 'file') {
        const response = await this.$http.post('/api/v1/domains/' + this.currentObject.id + '/touch', data, { headers })
      } else if (type == 'folder') {
        const response = await this.$http.post('/api/v1/domains/' + this.currentObject.id + '/mkdir', data, { headers })
      }

      this.isCreatingFileOrDir = false
      this.$vs.notify({
        title: 'Success',
        text: 'File/folder created successfully',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()

      // Refresh without losing current dir level
      let oldDirLevel = this.fileManager.dirLevel
      await this.refreshFileManager(this.currentObject.id)
      for (let i = 1; i < oldDirLevel.length; i++) {
        this.openDir(oldDirLevel[i])
      }
    },
    dontCreateFileOrDir() {
      this.isCreatingFileOrDir = false
    },
    showFilePicker(type) {
      if (type == 'directories') {
        this.$refs.directoriesUpload.click()
      } else {
        this.$refs.filesUpload.click()
      }
    },
    onFilesUploadChange(event) {
      this.filesToUpload = event.target.files
      this.confirmUploadPopup()
    },
    isValidUpload() {
      return this.filesToUpload.length > 0
    },
    confirmUploadPopup() {
      if (!this.isValidUpload()) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'primary',
        title: 'Upload confirmation',
        text: 'Are you sure you want to upload ' + this.filesToUpload.length + ' file(s)?',
        accept: this.uploadFiles
      })
    },
    async uploadFiles() {
      this.$vs.loading()

      let headers = {
        'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
        'Content-Type': 'multipart/form-data',
      }

      for (let i = 0; i < this.filesToUpload.length; i++) {
        let f = this.filesToUpload[i]
        let path = f.webkitRelativePath.substring(0,f.webkitRelativePath.lastIndexOf('/') + 1);
        path = this.fileManagerGetCurrentDirLevel() + '/' + path

        let formData = new FormData()
        formData.append('path', path)
        formData.append('file', f)

        await this.$http.post('/api/v1/domains/' + this.currentObject.id + '/upload', formData, { headers })
      }

      this.$vs.notify({
        title: 'Success',
        text: 'File saved successfully',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()

      // Refresh without losing current dir level
      let oldDirLevel = this.fileManager.dirLevel
      await this.refreshFileManager(this.currentObject.id)
      for (let i = 1; i < oldDirLevel.length; i++) {
        this.openDir(oldDirLevel[i])
      }
    },
    async deleteFilesAndDirectories() {
      this.$vs.loading()

      let headers = {
        'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
      }

      for (let i = 0; i < this.selectedFilesAndDirectories.length; i++) {
        let data = {
          type: this.selectedFilesAndDirectories[i].type,
          pathname: this.selectedFilesAndDirectories[i].pathname,
        }
        await this.$http.post('/api/v1/domains/' + this.currentObject.id + '/rm', data, { headers })
      }
      this.selectedFilesAndDirectories = []

      this.$vs.notify({
        title: 'Success',
        text: 'File(s) deleted successfully',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()

      // Refresh without losing current dir level
      let oldDirLevel = this.fileManager.dirLevel
      await this.refreshFileManager(this.currentObject.id)
      for (let i = 1; i < oldDirLevel.length; i++) {
        this.openDir(oldDirLevel[i])
      }
    },
    confirmDeleteFilesPopup() {
      if (this.selectedFilesAndDirectories.length == 0) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Delete confirmation',
        text: 'Are you sure you want to delete ' + this.selectedFilesAndDirectories.length + ' object(s)?',
        accept: this.deleteFilesAndDirectories
      })
    },
    tabber(event) {
      if (event) {
        let text = this.fileManager.fileNewContent
        let originalSelectionStart = event.target.selectionStart
        let textStart = text.slice(0, originalSelectionStart)
        let textEnd =  text.slice(originalSelectionStart)
        this.fileManager.fileNewContent = `${textStart}\t${textEnd}`
        event.target.value = this.fileManager.fileNewContent
        event.target.selectionEnd = event.target.selectionStart = originalSelectionStart + 1
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

<style>
.dir-level {
    text-decoration: underline;
    margin-right: 0.4em;
    cursor: pointer;
}

.file-editor > h4 {
    background-color: #bababa;
    border: 1px solid white;
}
</style>
