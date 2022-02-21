<!-- =========================================================================================
    File Name: TableMiscellaneous.vue
    Description: Combine filter,sorter, pagination etc.
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Users" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button v-if="canCreateAndEdit" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create')"></vs-button>
        </p>

        <div v-if="showFilters" class="mb-12">
            <div class="vx-row mb-2">
                <div class="vx-col w-full">
                    <label>Role</label>
                    <vs-select class="w-full" autocomplete v-model="filters.role"">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in roles" />
                    </vs-select> 
                </div>
            </div>
            <div class="vx-row mt-6">
                <div class="vx-col w-full">
                    <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
                    <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
                </div>
            </div>
        </div>

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
              <a href="#" @click="$refs.context_menu.close();toggleStatus(data.id);" class="flex items-center text-sm">
                <feather-icon :icon="(data != null && data.hasOwnProperty('status') && data.status != 'blocked') ? 'LockIcon' : 'UnlockIcon'" svgClasses="w-5 h-5" />
                <span class="ml-2">{{(data != null && data.hasOwnProperty('status') && data.status != 'blocked') ? 'Lock' : 'Unlock'}}</span>
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
                <vs-th sort-key="id">ID</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="role">Role</vs-th>
                <vs-th sort-key="email">Email</vs-th>
                <vs-th sort-key="website">Registration date</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td class="column-no-padding" :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].name">
                        <div @contextmenu.prevent="$refs.context_menu.open($event, data[indextr])">
                            <vs-chip :color="(data[indextr].status == 'active') ? 'success' : 'danger'">
                              {{data[indextr].name}}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].role">
                        <vs-select v-model="data[indextr].role" @input="onRoleChange(data[indextr])">
                            <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in roles" />
                        </vs-select> 
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].email">
                        {{data[indextr].email}}
                    </vs-td>

                    <vs-td class="column-no-padding" :data="data[indextr].created_at">
                        {{data[indextr].created_at}}
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Create popup -->
        <vs-popup title="Create User" :active.sync="showPopup['create']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" color="primary">{{newObject['create'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['create'].name" />
                    <span class="text-danger">{{ errors.first('name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Email*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-at-sign" icon-no-border v-model="newObject['create'].email" />
                    <span class="text-danger">{{ errors.first('email') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Password*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="password" class="w-full" icon-pack="feather" icon="icon-lock" icon-no-border v-model="newObject['create'].password" />
                    <span class="text-danger">{{ errors.first('password') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Repeat password*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="password" class="w-full" icon-pack="feather" icon="icon-lock" icon-no-border v-model="newObject['create'].password_repeat" />
                </div>
            </div>
            
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Role*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select v-model="newObject['create'].role">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in roles" />
                    </vs-select> 
                    <span class="text-danger">{{ errors.first('role') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Edit User" :active.sync="showPopup['edit']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto" color="primary">{{newObject['edit'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['edit'].name" />
                    <span class="text-danger">{{ errors.first('name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Email*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-at-sign" icon-no-border v-model="newObject['edit'].email" />
                    <span class="text-danger">{{ errors.first('email') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Password</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="password" class="w-full" icon-pack="feather" icon="icon-lock" icon-no-border v-model="newObject['edit'].password" />
                    <span class="text-danger">{{ errors.first('password') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Repeat password</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input type="password" class="w-full" icon-pack="feather" icon="icon-lock" icon-no-border v-model="newObject['edit'].password_repeat" />
                </div>
            </div>
            
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Role*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select v-model="newObject['edit'].role">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in roles" />
                    </vs-select> 
                    <span class="text-danger">{{ errors.first('role') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateObject" color="primary" type="filled" :disabled="!isValidForm('edit')">Update</vs-button>
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
      selected: [],
      maxItems: 20,
      currentPage: 1,

      objects: [],

      showFilters: false,
      filters: {
        role: '',
      },

      roles: [
        'admin',
        'mediabuyer',
        'affiliate',
        'advertiser',
        'accountant',
        'buyer',
        'account_manager',
        'callcenter',
      ],

      showPopup: {
        'create': false,
        'edit': false,
      },

      newObject: {
        'create': {
          name: '',
          email: '',
          password: '',
          password_repeat: '',
          role: '',
        },
        'edit': {
          name: '',
          email: '',
          password: '',
          password_repeat: '',
          role: '',
        }
      }
    }
  },
  computed: {
    canCreateAndEdit() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin'))
    },
  },
  methods: {
    getObjectIndexById(id) {
      let index = -1;
      this.objects.forEach((user, i) => {
        if (user.id == id) {
          index = i
          return
        }
      })
      return index
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    fetchErrors(errorsMap) {
      this.errors.clear()
      for (const [key, value] of Object.entries(errorsMap)) {
        for (let i = 0; i < value.length; i++) {
          this.errors.add({
            field: key,
            msg: value[i]
          })
        }
      }
    },
    resetFilters() {
      this.filters = {
        role: '',
      }
      this.retrieveObjects()
    },
    applyFilters() {
      this.retrieveObjects()
    },
    async retrieveObjects() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.objects = []
      const response = await this.$http.get('/api/v1/users', { headers })
      let roles = this.roles
      for (let i = 0; i < response.data.data.length; i++) {
        let user = response.data.data[i]

        let u = {}
        for (var attr in user) {
          if (user.hasOwnProperty(attr)) u[attr] = user[attr]
        }
        u.roles = {}
        for (let roleIndex = 0; roleIndex < roles.length; roleIndex++) {
          let role = roles[roleIndex]
          if (user.roles.includes(role)) {
            u.roles[role] = true
            u.role = role
          } else {
            u.roles[role] = false
          }
        }
        u.created_at = user.created_at
        u.updated_at = user.updated_at

        if (this.filters.role == '') {
          this.objects.push(u)
        } else {
          if (this.filters.role == u.role) {
            this.objects.push(u)
          }
        }
      }

      this.$vs.loading.close()
    },
    clearForm(type) {
      this.newObject[type] = {
        name: '',
        email: '',
        password: '',
        password_repeat: '',
        role: '',
      }
    },
    showForm(type) {
      if (type == 'create') {
        this.clearForm(type)
      }
      this.showPopup[type] = true
    },
    editForm(id) {
      let objIndex = this.getObjectIndexById(id)
      let obj = this.objects[objIndex]
      this.editingObject = obj

      this.newObject['edit'] = {
        name: obj.name,
        email: obj.email,
        role: obj.role,
      }

      this.showForm('edit')
    },
    isValidForm(type) {
      let obj = this.newObject[type]

      if (type == 'create') {
        return (obj.name != '' && obj.email != '' &&
          obj.password != '' && obj.password_repeat == obj.password &&
          obj.role != '')
      } else if (type == 'edit') {
        return (obj.name != '' && obj.email != '' &&
          (obj.password == '' || obj.password_repeat == obj.password) &&
          obj.role != '')
      }

      return false
    },
    async toggleRole(id, role) {
      this.$vs.loading()

      let index = this.getObjectIndexById(id)
      if (index == -1) {
        return
      }

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let user = this.objects[index]
      let data = {}
      data[role] = user.roles[role]

      const response = await this.$http.post('/api/v1/users/' + user.id + '/change-roles', data, { headers })
      this.$vs.notify({
        title: 'Success',
        text: 'Roles changed',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()
    },
    async changeRole(id, role) {
      this.$vs.loading()

      let index = this.getObjectIndexById(id)
      let user = this.objects[index]

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {}
      for (let i = 0; i < this.roles.length; i++) {
        data[this.roles[i]] = false
      }
      data[role] = true

      const response = await this.$http.post('/api/v1/users/' + user.id + '/change-roles', data, { headers })
      this.$vs.notify({
        title: 'Success',
        text: 'Roles changed',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.$vs.loading.close()
    },
    onRoleChange(user) {
      this.changeRole(user.id, user.role)
    },
    async createObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.newObject['create'].name,
        email: this.newObject['create'].email,
        password: this.newObject['create'].password,
        role: this.newObject['create'].role,
      }

      try {
        const response = await this.$http.post('/api/v1/users', data, { headers })
        this.showPopup['create'] = false
        this.$vs.notify({
          title: 'Success',
          text: 'User created',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
        this.retrieveObjects()
      } catch (error) {
        this.fetchErrors(error.response.data.errors)
      }

      this.$vs.loading.close()
    },
    async updateObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.newObject['edit'].name,
        email: this.newObject['edit'].email,
        role: this.newObject['edit'].role,
      }

      try {
        const response = await this.$http.put('/api/v1/users/' + this.editingObject.id, data, { headers })
        this.showPopup['edit'] = false
        this.$vs.notify({
          title: 'Success',
          text: 'User updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
        this.retrieveObjects()
      } catch (error) {
        this.fetchErrors(error.response.data.errors)
      }

      this.$vs.loading.close()
    },
    async toggleStatus(id) {
      this.$vs.loading()

      let objIndex = this.getObjectIndexById(id)
      let obj = this.objects[objIndex]

      let newStatus = 'blocked'
      if (obj.status == 'blocked') {
        newStatus = 'active'
      }

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        status: newStatus,
      }

      const response = await this.$http.put('/api/v1/users/' + id, data, { headers })
      this.$vs.notify({
        title: 'Success',
        text: 'User ' + ((newStatus != 'blocked') ? 'un' : '') + 'blocked',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.retrieveObjects()

      this.$vs.loading.close()
    },
  },
  created() {
    this.retrieveObjects()
  }
}
</script>

<style scoped>
.column-no-padding {
  padding-top: 0px !important;
  padding-bottom: 0px !important;
  white-space: nowrap;
}
</style>
