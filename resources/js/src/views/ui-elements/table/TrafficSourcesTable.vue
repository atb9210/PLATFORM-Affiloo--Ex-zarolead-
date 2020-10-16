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
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="deleteSelected"></vs-button>
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
                        {{data[indextr].name}}
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
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].name" />
                </div>
            </div>

            <div class="vx-row mb-6" v-for="(custom_param, indexparam) in newObject['create'].custom_params">
                <div class="vx-col sm:w-1/3 w-full">
                    <span v-if="indexparam == 0">Custom Params</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <vs-input class="w-1/3" icon-pack="feather" icon="icon-code" placeholder="Query Param" icon-no-border v-model="newObject['create'].custom_params[indexparam].query_param" />
                    <vs-input class="w-1/3" icon-pack="feather" icon="icon-code" placeholder="Token" icon-no-border v-model="newObject['create'].custom_params[indexparam].token" />
                    <vs-input class="w-1/3" icon-pack="feather" icon="icon-code" placeholder="Name" icon-no-border v-model="newObject['create'].custom_params[indexparam].name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <div class="w-1/3"></div>
                    <div class="w-1/3"></div>
                    <vs-button class="w-1/3" icon-pack="feather" icon="icon-plus" @click="addNewCustomParam('create')">Add</vs-button>
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
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].name" />
                </div>
            </div>

            <div class="vx-row mb-6" v-for="(custom_param, indexparam) in newObject['edit'].custom_params">
                <div class="vx-col sm:w-1/3 w-full">
                    <span v-if="indexparam == 0">Custom Params</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <vs-input class="w-1/3" icon-pack="feather" icon="icon-code" placeholder="Query Param" icon-no-border v-model="newObject['edit'].custom_params[indexparam].query_param" />
                    <vs-input class="w-1/3" icon-pack="feather" icon="icon-code" placeholder="Token" icon-no-border v-model="newObject['edit'].custom_params[indexparam].token" />
                    <vs-input class="w-1/3" icon-pack="feather" icon="icon-code" placeholder="Name" icon-no-border v-model="newObject['edit'].custom_params[indexparam].name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
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
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let custom_params = []
      this.newObject['create'].custom_params.forEach((custom_param) => {
        if (custom_param.query_param == '' && custom_param.token == '') return
        custom_params.push(custom_param)
      })

      let data = {
        name: this.newObject['create'].name,
        custom_params: JSON.stringify(custom_params),
      }

      // Create traffic source
      this.$http.post('/api/v1/traffic-sources', data, { headers: headers })
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

      let custom_params = []
      this.newObject['edit'].custom_params.forEach((custom_param) => {
        if (custom_param.query_param == '' && custom_param.token == '') return
        custom_params.push(custom_param)
      })

      let data = {
        name: this.newObject['edit'].name,
        custom_params: JSON.stringify(custom_params),
      }

      this.$http.put('/api/v1/traffic-sources/' + id, data, { headers })
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
  },
  created() {
    this.retrieveObjects()
  },
  mounted () {
  }
}

</script>
