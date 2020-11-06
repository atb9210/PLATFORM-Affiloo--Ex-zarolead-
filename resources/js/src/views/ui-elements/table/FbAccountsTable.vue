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
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create')"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-database" @click="/*showForm('multi_create')*/"></vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-upload" @click="showForm('import')"></vs-button>
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="deleteSelected"></vs-button>
        </p>

        <div v-if="showFilters" class="mb-12">
            <div class="vx-row mb-2">
                <div class="vx-col md:w-1/3 w-full" v-if="canCreateAsAdmin">
                    <label>User</label>
                    <vs-select autocomplete v-model="filters.user" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in userOptions" class="w-full" />
                    </vs-select>
                </div>
                <div class="vx-col md:w-1/3 w-full">
                    <label>Status</label>
                    <vs-select autocomplete v-model="filters.status" class="w-full select-large">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in statusOptionsForFilters" class="w-full" />
                    </vs-select>
                </div>
                <div class="vx-col md:w-1/3 w-full">
                    <label>Product</label>
                    <vs-select autocomplete v-model="filters.product" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in productOptions" class="w-full" />
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

        <vs-table stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="user.email">User</vs-th>
                <vs-th sort-key="status">Status</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="product.name">Product</vs-th>
                <vs-th sort-key="cards">Cards</vs-th>
                <vs-th sort-key="token_status">Token Status</vs-th>
                <vs-th sort-key="">BM</vs-th>
                <vs-th sort-key="">Spent</vs-th>
                <vs-th>Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].user.email">
                        {{data[indextr].user.email}}
                    </vs-td>

                    <vs-td :data="data[indextr].status">
                        <vs-chip transparent :color="statusColors[data[indextr].status]">{{data[indextr].status}}</vs-chip>
                    </vs-td>

                    <vs-td :data="data[indextr].name">
                        {{data[indextr].name}}
                    </vs-td>

                    <vs-td :data="data[indextr].product.name">
                        {{data[indextr].product.name}}
                    </vs-td>

                    <vs-td :data="data[indextr].cards">
                        <vs-textarea v-if="((fastEdit['cards'].enabled) && (fastEdit['index'] == indextr))" v-model="data[indextr].cards" @keyup.enter="if (fastEdit['cards'].enabled) { doFastEdit($event, 'cards'); }" @keyup.escape="fastEdit['cards'].enabled = false"></vs-textarea>
                        <vs-textarea v-else v-model="data[indextr].cards" @dblclick="enableFastEdit('cards', indextr)" readonly></vs-textarea>
                        <span v-if="((fastEdit['cards'].enabled) && (fastEdit['index'] == indextr))">Ctrl + Enter to update</span>
                    </vs-td>

                    <vs-td :data="data[indextr].token_status">
                        <vs-chip v-if="data[indextr].token_status != '' && data[indextr].token_status != null" transparent :color="(data[indextr].token_status == 'active') ? 'success' : 'danger'">
                            {{data[indextr].token_status}}
                        </vs-chip>
                    </vs-td>

                    <vs-td>
                        <div v-if="data[indextr].info != null && data[indextr].info.hasOwnProperty('bm')">
                            <vs-avatar v-for="(bm, index) in data[indextr].info['bm']" :key="index" icon-pack="feather" icon="icon-briefcase" :color="(bm.active > 0) ? 'success' : 'danger'" />
                        </div>
                    </vs-td>

                    <vs-td>
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center">
                            <vs-button @click="checkStatus(indextr)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-eye"></vs-button>
                            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-activity"></vs-button>
                            <vs-button @click="editForm(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-edit"></vs-button>
                            <vs-button @click="deleteObject(data[indextr].id)" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash"></vs-button>
                        </div>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Create popup -->
        <vs-popup title="Create FB account" :active.sync="showPopup['create']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{newObject['create'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['create'].name" />
                    <span class="text-danger">{{ errors.first('create.name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Status*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['create'].status" class="w-full select-large">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in statusOptions" class="w-full" />
                    </vs-select>
                    <span class="text-danger">{{ errors.first('create.status') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Product</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['create'].product" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in productOptions" class="w-full" />
                    </vs-select>
                    <span class="text-danger">{{ errors.first('create.product_id') }}</span>
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
                <div class="vx-col sm:w-1/3 w-full">
                    <span>User Agent</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['create'].userAgent" />
                    <span class="text-danger">{{ errors.first('create.user_agent') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Proxy</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                        <template slot="prepend">
                            <div class="prepend-text">
                                <vs-checkbox v-model="newObject['create'].proxyEnabled"></vs-checkbox>
                            </div>
                        </template>
                        <vs-input class="w-full" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['create'].proxy" placeholder="host::port::user::password" />
                    </vx-input-group>
                    <span class="text-danger">{{ errors.first('create.proxy') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Multi create popup -->
        <vs-popup title="Create FB accounts" :active.sync="showPopup['multi_create']">
            <div class="vx-row mb-3 mr-2 ml-2" v-for="(obj, index) in multiCreate.objects">
                <div :class="'vx-col w-full' + (this.canCreateAsAdmin) ? ' md:w-1/3' : ' md:w-1/2'">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="multiCreate.objects[index].name" placeholder="name" />
                </div>
                <div :class="'vx-col w-full' + (this.canCreateAsAdmin) ? ' md:w-1/3' : ' md:w-1/2'">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="multiCreate.objects[index].user" />
                </div>
                <div :class="'vx-col w-full' + (this.canCreateAsAdmin) ? ' md:w-1/3' : ' md:w-1/2'">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="multiCreate.objects[index].notes" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObjects" color="primary" type="filled" :disabled="!isValidForm('multi_create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Edit FB account" :active.sync="showPopup['edit']">
            <div class="vx-row mb-6 flex items-center">
                <vs-button class="mr-auto ml-auto">{{newObject['edit'].name}}</vs-button>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-tag" icon-no-border v-model="newObject['edit'].name" />
                    <span class="text-danger">{{ errors.first('edit.name') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Status*</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['edit'].status" class="w-full select-large">
                        <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in statusOptions" class="w-full" />
                    </vs-select>
                    <span class="text-danger">{{ errors.first('edit.status') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Product</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-select autocomplete v-model="newObject['edit'].product" class="w-full select-large">
                        <vs-select-item :key="index" :value="item.id" :text="item.label" v-for="(item, index) in productOptions" class="w-full" />
                    </vs-select>
                    <span class="text-danger">{{ errors.first('edit.product_id') }}</span>
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
                <div class="vx-col sm:w-1/3 w-full">
                    <span>User Agent</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-user" icon-no-border v-model="newObject['edit'].userAgent" />
                    <span class="text-danger">{{ errors.first('edit.user_agent') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Proxy</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vx-input-group prependClasses="border border-solid border-grey border-r-0">
                        <template slot="prepend">
                            <div class="prepend-text">
                                <vs-checkbox v-model="newObject['edit'].proxyEnabled"></vs-checkbox>
                            </div>
                        </template>
                        <vs-input class="w-full" icon-pack="feather" icon="icon-globe" icon-no-border v-model="newObject['edit'].proxy" placeholder="host::port::user::password" />
                    </vx-input-group>
                    <span class="text-danger">{{ errors.first('edit.proxy') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="updateObject" color="primary" type="filled" :disabled="!isValidForm('edit')">Update</vs-button>
            </div>
        </vs-popup>

        <!-- Import popup -->
        <vs-popup title="Import FB accounts from gologin" :active.sync="showPopup['import']">
            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Profiles' IDs</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-textarea v-model="profileIds"></vs-textarea>
                    <span class="text-danger">{{ errors.first('import.profile_ids') }}</span>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="importProfiles" color="primary" type="filled" :disabled="profileIds == ''">Import</vs-button>
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

      showFilters: false,
      filters: {
        user: '',
        status: 'run',
        product: '',
      },

      statusOptionsForFilters: ['all', 'setup', 'warm', 'ready', 'run', 'ban'],
      statusOptions: ['setup', 'warm', 'ready', 'run', 'ban'],
      userOptions: [],
      productOptions: [],
      myId: 0,

      editingObject: -1,

      showPopup: {
        'create': false,
        'multi_create': false,
        'edit': false,
        'import': false,
      },
      newObject: {
        'create': {
          user: '',
          name: '',
          product: '',
          status: 'setup',
          notes: '',
          token: '',
          userAgent: '',
          proxyEnabled: false,
          proxy: '',
        },
        'edit': {
          user: '',
          name: '',
          product: '',
          status: '',
          notes: '',
          token: '',
          userAgent: '',
          proxyEnabled: false,
          proxy: '',
        },
      },
      multiCreate: {
        num: 0,
        objects: [],
      },

      fastEdit: {
        'index': -1,
        'cards': {
          enabled: false,
          value: '',
        },
      },

      profileIds: '',

      statusColors: {
        'setup': 'primary',
        'warm': 'dark',
        'ready': 'warning',
        'run': 'success',
        'ban': 'danger',
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
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin') ||
              this.$store.getters.userHasRole('account_manager'))
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
    resetFilters() {
      this.filters = {
        user: '',
        status: 'run',
        product: '',
      }
      this.applyFilters()
    },
    applyFilters() {
      this.selected = []
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {}
      if (this.filters.user != '') {
        data['filter_user'] = this.filters.user
      }
      if (this.filters.status != '' && this.filters.status != 'all') {
        data['filter_status'] = this.filters.status
      }
      if (this.filters.product != '') {
        data['filter_product'] = this.filters.product
      }

      // Retrieve fb accounts
      this.$http.get('/api/3rd/fb-accounts', { headers, params: data })
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
    showForm(type) {
      this.showPopup[type] = true
      if (type == 'multi_create') {
        for (let i = 0; i < 3; i++) {
          this.multiCreate.objects.push({
            name: '',
            user: '',
            token: '',
          })
          this.multiCreate.num++
        }
      }
    },
    clearForm(type) {
      if (type == 'multi_create') {
        this.multiCreate = {
          num: 0,
          objects: [],
        }
      } else {
        this.newObject[type] = {
          name: '',
          product: '',
          status: 'setup',
          user: '',
          notes: '',
          token: '',
        }
        if (this.canCreateAsAdmin) {
          this.newObject[type].user = this.myId
        }
      }
    },
    isValidForm(type) {
      let isValid = false
      if (type == 'multi_create') {
        // TODO:
        return false
      } else {
        isValid = (this.newObject[type].name.length > 0)
        if (this.canCreateAsAdmin) {
          isValid = isValid && (this.newObject[type].user != '')
        }
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
        status: this.newObject['create'].status,
        user_id: this.newObject['create'].user,
        notes: this.newObject['create'].notes,
        proxy_enabled: this.newObject['create'].proxyEnabled,
        proxy: this.newObject['create'].proxy,
      }
      if (this.newObject['create'].product != '' && this.newObject['create'].product > 0) {
        data.product_id = this.newObject['create'].product
      }
      if (this.newObject['create'].token != '') {
        data.token = this.newObject['create'].token
      }
      if (this.newObject['create'].userAgent != '') {
        data.user_agent = this.newObject['create'].userAgent
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
          this.applyFilters()
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
        product: obj.product.id,
        status: obj.status,
        user: obj.user.id,
        notes: obj.notes,
        token: obj.token,
        userAgent: '',
        proxyEnabled: false,
        proxy: '',
      }
      if (obj.profile.hasOwnProperty('user_agent')) {
        this.newObject['edit'].userAgent = obj.profile['user_agent']
      }
      if (obj.profile.hasOwnProperty('proxy_enabled')) {
        this.newObject['edit'].proxyEnabled = obj.profile['proxy_enabled']
      }
      if (obj.profile.hasOwnProperty('proxy')) {
        this.newObject['edit'].proxy = obj.profile['proxy']
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
        status: this.newObject['edit'].status,
        user_id: this.newObject['edit'].user,
        notes: this.newObject['edit'].notes,
        token: this.newObject['edit'].token,
        user_agent: this.newObject['edit'].userAgent,
        proxy_enabled: this.newObject['edit'].proxyEnabled,
        proxy: this.newObject['edit'].proxy,
      }
      if (this.newObject['edit'].product != '' && this.newObject['edit'].product > 0) {
        data.product_id = this.newObject['edit'].product
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
      if (what == 'cards' && !event.ctrlKey) {
        return
      }
      this.$vs.loading()
      this.errors.clear()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let index = this.fastEdit['index']
      let data = {
        cards: this.objects[index].cards,
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

      // TODO: to delete
      /*
      let config = {
      }
      if (obj.profile.hasOwnProperty('proxy_enabled') && obj.profile.proxy_enabled &&
          obj.profile.hasOwnProperty('proxy') && isValidProxy(obj.profile.proxy)) {
        config.proxy = getProxyFromString(obj.profile.proxy)
      }
      */

      // Check FB API token status and get business accounts
      this.$vs.loading()
      this.$http.get('https://graph.facebook.com/me/businesses?access_token=' + obj.token)
        .then((response) => {
          let businesses = {}
          response.data.data.forEach((bm) => {
            businesses[bm.id] = {
              active: 0,
              total: 0,
            }
          })

          // Get FB ad accounts
          this.$http.get('https://graph.facebook.com/v5.0/me/adaccounts?access_token=' + obj.token + '&fields=business,account_status')
            .then((response) => {
              let mainAdAccountActive = true
              response.data.data.forEach((adAccount) => {
                if (adAccount.hasOwnProperty('business') && adAccount.business.hasOwnProperty('id') &&
                    (adAccount.business.id in businesses)) {
                  businesses[adAccount.business.id].total += 1
                  if (adAccount.account_status == 1) {
                    businesses[adAccount.business.id].active += 1
                  }
                }
              })

              let info = obj.info
              if (info == null) {
                info = {}
              }
              info["bm"] = []
              for (const [key, value] of Object.entries(businesses)) {
                info["bm"].push({
                  "active": businesses[key].active,
                  "total": businesses[key].total,
                })
              }

              // Update fb account object (token status and businesses status)
              let headers = {
                Authorization: 'Bearer ' + localStorage.getItem('accessToken')
              }
              let data = {
                token_status: 'active',
                info: JSON.stringify(info),
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

          let info = obj.info
          if (info == null) {
            info = {}
          }
          info["bm"] = []
          let data = {
            token_status: 'disabled',
            info: JSON.stringify(info),
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
        })
    },
    importProfiles() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let pIds = []
      let lines = this.profileIds.split('\n')
      for (let i = 0; i < lines.length; i++) {
        lines[i] = lines[i].replace('\r', '')
        if (lines[i] != '') {
          pIds.push(lines[i])
        }
      }

      let data = {
        gologin_ids: pIds,
      }

      this.$http.post('/api/3rd/fb-accounts/import', data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'FB account imported',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.profileIds = ''
          this.showPopup['import'] = false
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
    // TODO: fix this function
    isValidProxy(proxy) {
      // TODO: to delete
      return true

      let regex = "/^[a-zA-Z0-9-.]+::[0-9]+::[a-zA-Z0-9-_.]+::.+$/"
      console.log(proxy.match(regex))
      return (proxy.match(regex) != null)
    },
    getProxyFromString(proxy) {
      let toReturn = {
        host: '',
        port: 0,
        auth: {
          username: '',
          password: '',
        }
      }

      let splits = proxy.split('::')
      toReturn.host = splits[0]
      toReturn.port = parseInt(splits[1])
      toReturn.auth.username = splits[2]
      toReturn.auth.password = splits[3]

      return toReturn
    },
    createObjects() {
    },
  },
  created() {
    // TODO: to delete
    // This is only for test purposes
    /*
    let p = '196.16.229.179::25707::asrafatb92::asrafatb92'
    console.log(this.isValidProxy(p))
    let proxy = this.getProxyFromString(p)
    console.log(proxy)

    let config = { proxy: proxy }
    this.$http.get('http://api.ipify.org/?format=json', config)
      .then((response) => {
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
    */

    let userInfo = this.$store.getters.getUserInfo()
    this.myId = userInfo.id
    this.newObject['create'].user = this.myId

    this.resetFilters()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve products
    this.$http.get('/api/v1/products', { headers })
      .then((response) => {
        this.productOptions = []
        response.data.data.forEach((product) => {
          this.productOptions.push({
            id: product.id,
            label: product.name,
          })
        })

        if (this.canCreateAsAdmin) {
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
  },
  mounted () {
  }
}

</script>
