<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="FB Accounts" code-toggler>

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
                <vs-th sort-key="user.email">User</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="notes">Notes</vs-th>
                <vs-th sort-key="token">Token</vs-th>
                <vs-th sort-key="status">Status</vs-th>
                <vs-th>Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td :data="data[indextr].user.email">
                        {{data[indextr].user.email}}
                    </vs-td>

                    <vs-td :data="data[indextr].name">
                        {{data[indextr].name}}
                    </vs-td>

                    <vs-td :data="data[indextr].notes">
                        <vs-textarea v-if="((fastEdit['notes'].enabled) && (fastEdit['index'] == indextr))" v-model="data[indextr].notes" @keyup.enter="if (fastEdit['notes'].enabled) { doFastEdit($event, 'notes'); }" @keyup.escape="fastEdit['notes'].enabled = false"></vs-textarea>
                        <vs-textarea v-else v-model="data[indextr].notes" @dblclick="enableFastEdit('notes', indextr)" readonly></vs-textarea>
                        <span v-if="((fastEdit['notes'].enabled) && (fastEdit['index'] == indextr))">Ctrl + Edit to update</span>
                    </vs-td>

                    <vs-td :data="data[indextr].token">
                        <span v-if="!((fastEdit['token'].enabled) && (fastEdit['index'] == indextr))" v-model="data[indextr].token" @click="enableFastEdit('token', indextr)">{{firstCharacters(data[indextr].token, 12)}}</span>
                        <vs-input type="text" v-if="((fastEdit['token'].enabled) && (fastEdit['index'] == indextr))" v-model="data[indextr].token" @keyup.enter="if (fastEdit['token'].enabled) { doFastEdit($event, 'token'); }" @keyup.escape="fastEdit['token'].enabled = false" />
                    </vs-td>

                    <vs-td :data="data[indextr].status">
                        <vs-chip v-if="data[indextr].status != '' && data[indextr].status != null" transparent :color="(data[indextr].status == 'active') ? 'success' : 'danger'">{{data[indextr].status}}</vs-chip>
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center">
                            <vs-button @click="checkStatus(indextr)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-eye"></vs-button>
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
                <vs-button class="mr-auto ml-auto">{{newObject['create'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].name" />
                    <span class="text-danger">{{ errors.first('create.name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6" v-if="canCreateAsAdmin">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>User</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['create'].user" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in userOptions" class="w-full" />
                    </vs-select>
                    <span class="text-danger">{{ errors.first('create.user_id') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Notes</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-textarea v-model="newObject['create'].notes"></vs-textarea>
                    <span class="text-danger">{{ errors.first('create.notes') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Token</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create'].token" />
                    <span class="text-danger">{{ errors.first('create.token') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Edit Tag" :active.sync="showPopup['edit']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{newObject['edit'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].name" />
                    <span class="text-danger">{{ errors.first('edit.name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6" v-if="canCreateAsAdmin">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>User</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['edit'].user" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in userOptions" class="w-full" />
                    </vs-select>
                    <span class="text-danger">{{ errors.first('edit.user_id') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Notes</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-textarea v-model="newObject['edit'].notes"></vs-textarea>
                    <span class="text-danger">{{ errors.first('edit.notes') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Token</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['edit'].token" />
                    <span class="text-danger">{{ errors.first('edit.token') }}</span>
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

      userOptions: [],
      myId: 0,

      editingObject: -1,

      showPopup: {
        'create': false,
        'edit': false,
      },
      newObject: {
        'create': {
          user: '',
          name: '',
          notes: '',
          token: '',
        },
        'edit': {
          user: '',
          name: '',
          notes: '',
          token: '',
        },
      },

      fastEdit: {
        'index': -1,
        'notes': {
          enabled: false,
          value: '',
        },
        'token': {
          enabled: false,
          value: '',
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
    canCreateAsAdmin() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin'))
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

      // Retrieve fb accounts
      this.$http.get('/api/3rd/fb-accounts', { headers })
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

      this.selected.forEach((tag) => {
        this.$http.delete('/api/3rd/fb-accounts/' + tag.id, { headers })
          .then((response) => {
            this.deleted.push(tag.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' FB account(s) deleted',
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
        user: '',
        notes: '',
        token: '',
      }
      if (this.canCreateAsAdmin) {
        this.newObject[type].user = this.myId
      }
    },
    isValidForm(type) {
      let isValid = (this.newObject[type].name.length > 0)
      if (this.canCreateAsAdmin) {
        isValid = isValid && (this.newObject[type].user != '')
      }
      return isValid
    },
    createObject() {
      this.$vs.loading()
      this.errors.clear()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.newObject['create'].name,
        user_id: this.newObject['create'].user,
        notes: this.newObject['create'].notes,
        token: this.newObject['create'].token,
      }

      // Create fb account
      this.$http.post('/api/3rd/fb-accounts', data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'FB account created',
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
          for (const [key, value] of Object.entries(error.response.data.errors)) {
            for (let i = 0; i < value.length; i++) {
              this.errors.add({
                scope: 'create',
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
    editForm(id) {
      let obj = this.objects[this.getObjectIndexById(id)]
      this.newObject['edit'] = {
        name: obj.name,
        user: obj.user.id,
        notes: obj.notes,
        token: obj.token,
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
        name: this.newObject['edit'].name,
        user_id: this.newObject['edit'].user,
        notes: this.newObject['edit'].notes,
        token: this.newObject['edit'].token,
      }

      this.$http.put('/api/3rd/fb-accounts/' + id, data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'FB account updated',
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

      this.$http.delete('/api/3rd/fb-accounts/' + id, { headers })
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
    firstCharacters(str, numOfCharacters) {
      if (typeof str != 'string') {
        return ''
      }
      if (numOfCharacters <= 0) {
        return str
      }
      if (str.length <= numOfCharacters) {
        return str
      } else {
        return str.substr(0, numOfCharacters) + '...'
      }
    },
    enableFastEdit(what, index) {
      this.fastEdit['index'] = index
      this.fastEdit[what].enabled = true
    },
    doFastEdit(event, what) {
      if (what == 'notes' && !event.ctrlKey) {
        return
      }
      this.$vs.loading()
      this.errors.clear()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let index = this.fastEdit['index']
      let data = {
        notes: this.objects[index].notes,
        token: this.objects[index].token
      }

      // Update notes and token
      this.$http.put('/api/3rd/fb-accounts/' + this.objects[index].id, data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'FB account updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.fastEdit[what].enabled = false
          this.objects[index] = response.data.data
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
    checkStatus(objIndex) {
      let obj = this.objects[objIndex]
      if (obj.token == null || obj.token == '') {
        return
      }

      // Check FB API token status
      this.$http.get('https://graph.facebook.com/me?access_token=' + obj.token)
        .then((response) => {
          // FB API token is active
          let headers = {
            Authorization: 'Bearer ' + localStorage.getItem('accessToken')
          }
          let data = {
            status: 'active',
          }
          this.$http.put('/api/3rd/fb-accounts/' + obj.id, data, { headers })
            .then((response) => {
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Success',
                text: 'Token is active',
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
        })
        .catch((error) => {
          // FB API token is disabled
          let headers = {
            Authorization: 'Bearer ' + localStorage.getItem('accessToken')
          }
          let data = {
            status: 'disabled',
          }
          this.$http.put('/api/3rd/fb-accounts/' + obj.id, data, { headers })
            .then((response) => {
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Error',
                text: 'Token is disabled',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'danger'
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
        })
    },
  },
  created() {
    let userInfo = this.$store.getters.getUserInfo()
    this.myId = userInfo.id
    this.newObject['create'].user = this.myId

    this.retrieveObjects()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve users
    this.$http.get('/api/v1/users', { headers })
      .then((response) => {
        response.data.data.forEach((user) => {
          this.userOptions.push({
            id: user.id,
            label: user.email,
          })
        })
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
  mounted () {
  }
}

</script>
