<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Tags" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create')">Create new</vs-button> 
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="deleteSelected">Delete</vs-button> 
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

        <vs-table multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="email">Id</vs-th>
                <vs-th sort-key="username">Name</vs-th>
                <vs-th sort-key="bg_color">Back color</vs-th>
                <vs-th sort-key="fb_color">Text color</vs-th>
                <vs-th>Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td :data="data[indextr].name">
                        <vs-button :color="data[indextr].bg_color" :text-color="data[indextr].fg_color">{{data[indextr].name}}</vs-button>
                    </vs-td>

                    <vs-td :data="data[indextr].bg_color">
                        <input class="mr-2" type="color" v-model="data[indextr].bg_color" @input="changeColor(data[indextr].id, 'bg_color', data[indextr].bg_color)">
                    </vs-td>

                    <vs-td :data="data[indextr].bg_color">
                        <input class="mr-2" type="color" v-model="data[indextr].fg_color" @input="changeColor(data[indextr].id, 'fg_color', data[indextr].fg_color)">
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
        <vs-popup title="Create Tag" :active.sync="showPopup['create']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" :color="newObject['create'].bg_color" :text-color="newObject['create'].fg_color">{{newObject['create'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].name" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Colors:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/2" v-model="newObject['create'].bg_color" />
                    <input type="color" class="w-1/2" v-model="newObject['create'].fg_color" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Edit Tag" :active.sync="showPopup['edit']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" :color="newObject['edit'].bg_color" :text-color="newObject['edit'].fg_color">{{newObject['edit'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].name" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Colors:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/2" v-model="newObject['edit'].bg_color" />
                    <input type="color" class="w-1/2" v-model="newObject['edit'].fg_color" />
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
          bg_color: '#000000',
          fg_color: '#ffffff',
        },
        'edit': {
          name: '',
          bg_color: '#000000',
          fg_color: '#ffffff',
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

      // Retrieve tags
      this.$http.get('/api/v1/tags', { headers })
        .then((response) => {
          this.objects = response.data.data
          this.$vs.loading.close()
          console.log(this.objects)
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
    deleteSelected() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      this.deleted = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.selected.forEach((tag) => {
        this.$http.delete('/api/v1/tags/' + tag.id, { headers })
          .then((response) => {
            this.deleted.push(tag.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Tag(s) deleted',
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
        bg_color: '#000000',
        fg_color: '#ffffff',
      }
    },
    isValidForm(type) {
      return (this.newObject[type].name.length > 0)
    },
    createObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.newObject['create'].name,
        bg_color: this.newObject['create'].bg_color,
        fg_color: this.newObject['create'].fg_color,
      }

      // Retrieve tags
      this.$http.post('/api/v1/tags', data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Tag created',
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
      this.newObject['edit'] = {
        name: obj.name,
        bg_color: obj.bg_color,
        fg_color: obj.fg_color,
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
      let data = this.newObject['edit']

      this.$http.put('/api/v1/tags/' + id, data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Tag updated',
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

      this.$http.delete('/api/v1/tags/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Tag deleted',
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
    changeColor(tagid, type, value) {
      this.$vs.loading()

      var data = {}
      data[type] = value

      this.$http.put('/api/v1/tags/' + tagid, data, {
          headers: { Authorization: 'Bearer ' + localStorage.getItem('accessToken') },
        })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Color changed',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
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
  },
  created() {
    this.retrieveObjects()
  },
  mounted () {
  }
}

</script>
