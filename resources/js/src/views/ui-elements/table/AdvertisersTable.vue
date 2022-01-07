<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Advertisers" code-toggler>

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
                <vs-th sort-key="email">Email</vs-th>
                <vs-th>Network</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td :data="data[indextr].name">
                        {{data[indextr].name}}
                    </vs-td>

                    <vs-td :data="data[indextr].email">
                        {{data[indextr].email}}
                    </vs-td>

                    <vs-td class="column-no-padding">
                        <fast-editable-value-select
                            class="font-bold"
                            :options="networks"
                            v-model="newNetwork"
                            @onEdit="if (data[indextr].network !== null) { newNetwork = data[indextr].network.id }"
                            @onUpdate="updateNetwork(data[indextr], newNetwork)">
                            <span v-if="data[indextr].network !== null">{{data[indextr].network.name}}</span>
                            <span v-else>No network</span>
                        </fast-editable-value-select>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Create popup -->
        <vs-popup title="Create Advertiser" :active.sync="showPopup['create']">
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

            <div class="vx-row mb-6">
                <vs-button class="mt-12 mr-auto ml-auto" @click="createObject" color="primary" type="filled" :disabled="!isValidForm('create')">Create</vs-button>
            </div>
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Edit Advertiser" :active.sync="showPopup['edit']">
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

      networks: [],

      maxItems: 20,

      editingObject: -1,

      showPopup: {
        'create': false,
        'edit': false,
      },
      newObject: {
        'create': {
          name: '',
        },
        'edit': {
          name: '',
        },
      },

      newNetwork: '',
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
    async retrieveObjects() {
      this.selected = []
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {}
      data['filter_roles'] = [5]

      // Retrieve advertisers
      const response = await this.$http.get('/api/v1/users', { headers, params: data })
      this.objects = response.data.data

      for (let i = 0; i < this.objects.length; i++) {
        let obj = this.objects[i]

        let network = null
        if (obj.hasOwnProperty('info') && obj.info !== null &&
            obj.info.hasOwnProperty('network_id') && obj.info.network_id !== null) {
          for (let j = 0; j < this.networks.length; j++) {
            if (this.networks[j].id == obj.info.network_id) {
              network = this.networks[j]
              break
            }
          }
        }
        obj.network = network

        this.objects[i] = obj
      }

      this.$vs.loading.close()
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
    // TODO: Confirmation before deletion
    async deleteSelected() {
      let numSelected = this.selected.length

      if (numSelected == 0) return

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      for (let i = 0; i < numSelected; i++) {
        let advertiser = this.selected[i]
        await this.$http.delete('/api/v1/users/' + advertiser.id, { headers })
      }

      this.$vs.notify({
        title: 'Success',
        text: numSelected + ' Advertiser(s) deleted',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.selected = []

      this.$vs.loading.close()
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
    async createObject() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.newObject['create'].name,
        bg_color: this.newObject['create'].bg_color,
        fg_color: this.newObject['create'].fg_color,
      }

      // Create advertiser
      try {
        const response = await this.$http.post('/api/v1/users', data, { headers: headers })

        this.$vs.notify({
          title: 'Success',
          text: 'Advertiser created',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })

        this.$vs.loading.close()

        this.clearForm('create')
        this.showPopup['create'] = false
        this.retrieveObjects()
      } catch (error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot create the advertiser',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
      }
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
    async updateObject() {
      if (this.editingObject <= 0) return

      let id = this.editingObject

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = this.newObject['edit']

      try {
        const response = await this.$http.put('/api/v1/users/' + id, data, { headers })

        this.$vs.notify({
          title: 'Success',
          text: 'Advertiser updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })

        this.showPopup['edit'] = false
        this.editingObject = -1
        this.$vs.loading.close()

        this.retrieveObjects()
      } catch (error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the advertiser',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
      }
    },
    // TODO: Confirmation before deletion
    async deleteObject(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      try {
        const response = await this.$http.delete('/api/v1/users/' + id, { headers })

        this.$vs.notify({
          title: 'Success',
          text: 'Advertiser deleted',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
        this.$vs.loading.close()

        this.retrieveObjects()
      } catch (error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot delete the advertiser',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
      }
    },
    async updateNetwork(advertiser, newNetwork) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let info = {}
      if (advertiser.info !== null) {
        for (const [key, value] of Object.entries(advertiser.info)) {
          info[key] = value
        }
      }
      info['network_id'] = newNetwork

      let data = {
        info: JSON.stringify(info),
      }
      try {
        const response = await this.$http.put('/api/v1/users/' + advertiser.id, data, { headers })

        this.$vs.notify({
          title: 'Success',
          text: 'Advertiser updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })

        this.showPopup['edit'] = false
        this.editingObject = -1
        this.$vs.loading.close()

        this.retrieveObjects()
      } catch (error) {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot update the advertiser',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
        this.$vs.loading.close()
      }
    },
  },
  async created() {
    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    try {
      const networksResponse = await this.$http.get('/api/v1/networks', { headers })
      this.networks = networksResponse.data.data
      for (let i = 0; i < this.networks.length; i++) {
        this.networks[i].label = this.networks[i].name
      }
    } catch (error) {
      // Nothing
    }

    await this.retrieveObjects()
  },
  mounted () {
  }
}

</script>
