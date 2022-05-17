<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Templates" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="goToTemplateBuilder"></vs-button>
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

        <vue-context ref="context_menu" v-slot="{ data }">
            <li>
              <a href="#" @click="$refs.context_menu.close();editTemplate(data);" class="flex items-center text-sm">
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close();showPopupInstall(data);" class="flex items-center text-sm">
                <feather-icon icon="UploadIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Install</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close();confirmSingleDeletePopup(data);" class="flex items-center text-sm">
                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Delete</span>
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
                <vs-th sort-key="name">Name</vs-th>
                <vs-th>Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                    <vs-td :data="data[indextr].name">
                        <div @contextmenu.prevent="$refs.context_menu.open($event, data[indextr])">
                            <vs-chip>
                                {{data[indextr].name}}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center">
                            <!--
                            <vs-button @click="editForm(data[indextr].id)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-edit"></vs-button>
                            <vs-button @click="deleteObject(data[indextr].id)" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash"></vs-button>
                            -->
                        </div>
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

        <!-- Install template popup -->
        <vs-popup title="Install template on a domain" :active.sync="showPopup['install']">
            <div class="w-full pr-2 pl-2">
                <div class="vx-row mb-3">

                    <div class="vx-col md:w-1/4 w-full mb-3">
                        Domain
                    </div>
                    <div class="vx-col md:w-3/4 w-full mb-3">
                        <vs-select v-model="domain" class="w-full select-large" autocomplete="true">
                            <vs-select-item :key="index" :value="item" :text="item.domain" v-for="(item, index) in domains" class="w-full" />
                        </vs-select>
                    </div>

                    <div class="vx-col md:w-1/4 w-full mb-3">
                        Path
                    </div>
                    <div class="vx-col md:w-3/4 w-full mb-3">
                        <vs-input type="text" v-model="path" class="w-full select-large" />
                        Final URL: {{ installationUrl }}
                    </div>

                    <div class="vx-col w-full mb-3">
                        <vs-button color="success" type="filled" icon-pack="feather" icon="icon-upload" @click="installTemplate(template, domain, path)" :disabled="!isValidForm('install')">Install</vs-button>
                    </div>
                </div>
            </div>
        </vs-popup>

    </vx-card>
</template>

<script>
import { VueContext } from 'vue-context'
import * as utils from 'utils'

export default {
  components: {
    VueContext,
  },
  data () {
    return {
      objects: [],
      selected: [],

      maxItems: 20,

      showPopup: {
        'install': false,
      },

      template: {},
      domains: [],
      domain: '',
      path: '',
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
    installationUrl() {
      let result = ''

      if (this.domain != '') {
        if (this.domain.hasOwnProperty('ssl')) {
          if (this.domain.ssl == 'SSL') {
            result += 'https://'
          } else {
            result += 'http://'
          }
        }
        if (this.domain.hasOwnProperty('domain')) {
          result += this.domain.domain + '/'
        }

        let path = this.path
        if (path != '') {
          if (path[0] == '/') {
            path = path.slice(1)
          }
          result += path
        }
      }

      return result
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
    getObjectById(id) {
      let index = this.getObjectIndexById(id)
      if (index < 0) {
        return null
      } else {
        return this.objects[index]
      }
    },
    retrieveObjects() {
      this.selected = []
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      // Retrieve templates
      this.$http.get('/api/v1/templates', { headers })
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
    editTemplate(template) {
      // TODO: HERE
    },
    showPopupInstall(template) {
      this.template = utils.deepCloneObject(template)
      this.showPopup['install'] = true
    },
    isValidForm(type) {
      let result = false

      if (type == 'install') {
        result = (this.domain != '' && this.path != '')
      } else {
        result = false
      }

      return result
    },
    async installTemplate(template, domain, path) {
      this.$vs.loading()

      let result = false

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let params = {
        domain: parseInt(domain.id),
        path: path,
      }

      try {
        const response = await this.$http.post('/api/v1/templates/' + template.id + '/install', params, { headers })
        if (response !== null && response.hasOwnProperty('data') && response.data !== null &&
            response.data.hasOwnProperty('data') && response.data.data !== null) {
          result = true
        }
      } catch (error) {
        // TODO: Log error
        result = false
      }

      if (result) {
        this.$vs.notify({
          title: 'Success',
          text: 'Template installed',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Error: Cannot install the template',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    },
    confirmSingleDeletePopup(template) {
      if (template == null) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Delete confirmation',
        text: 'Are you sure you want to delete this object?',
        accept: this.deleteTemplate(template)
      })
    },
    async deleteTemplate(template) {
      this.$vs.loading()

      this.deleted = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      await this.$http.delete('/api/v1/templates/' + template.id, { headers })

      this.selected = []
      this.$vs.notify({
        title: 'Success',
        text: 'Template deleted',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
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
    async deleteSelected() {
      let numSelected = this.selected.length
      if (numSelected == 0) return

      this.$vs.loading()

      this.deleted = []

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      for (let i = 0; i < numSelected; i++) {
        await this.$http.delete('/api/v1/templates/' + this.selected[i].id, { headers })
      }

      this.selected = []
      this.$vs.notify({
        title: 'Success',
        text: numSelected + ' Template(s) deleted',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
    },
    goToPage(page) {
      this.$router.push(page)
        .catch((error) => {
          this.$router.push(page)
        })
    },
    goToTemplateBuilder() {
      this.goToPage('/dashboard/templates/builder')
    },
  },
  async created() {
    await this.retrieveObjects()

    this.$vs.loading()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    this.domains = []
    try {
      const response = await this.$http.get('/api/v1/domains', { headers })
      this.domains = response.data.data
    } catch (error) {
      // TODO: Log error
    }

    this.$vs.loading.close()
  },
  mounted () {
  }
}

</script>
