<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Styles" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="showForm('create')"></vs-button>
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="confirmDeletePopup()"></vs-button>
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
              <a href="#" @click="$refs.context_menu.close();showPreview(data.id);" class="flex items-center text-sm">
                <feather-icon icon="EyeIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Preview</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close();editObject(data.id);" class="flex items-center text-sm">
                <feather-icon icon="EditIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Edit</span>
              </a>
            </li>
            <li>
              <a href="#" @click="$refs.context_menu.close();deleteObject(data.id);" class="flex items-center text-sm">
                <feather-icon icon="TrashIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Trash</span>
              </a>
            </li>
            <!--
            <li v-if="canCreateAndEdit">
              <a href="#" @click="$refs.context_menu.close();cloneForm(data.id);" class="flex items-center text-sm">
                <feather-icon icon="CopyIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Clone</span>
              </a>
            </li>
            -->
            <!--
            <li v-if="data.archived == 1">
              <a href="#" @click="$refs.context_menu.close();restoreForm(data.id);" class="flex items-center text-sm">
                <feather-icon icon="UpdateIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Restore</span>
              </a>
            </li>
            -->
            <li>
              <a href="#" @click="$refs.context_menu.close()" class="flex items-center text-sm">
                <feather-icon icon="XIcon" svgClasses="w-5 h-5" />
                <span class="ml-2">Close</span>
              </a>
            </li>
        </vue-context>

        <vs-table stripe multiple v-model="selected" :max-items="maxItems" pagination search :data="objects" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="created_at">Creation date</vs-th>
                <vs-th sort-key="name">Name</vs-th>
                <vs-th sort-key="default_style"></vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].created_at">
                        {{formatDate(new Date(data[indextr].created_at))}}
                    </vs-td>

                    <vs-td :data="data[indextr].name">
                        <div @contextmenu.prevent="$refs.context_menu.open($event, data[indextr])">
                            <vs-chip>
                                {{data[indextr].name}}
                            </vs-chip>
                        </div>
                    </vs-td>

                    <vs-td :data="data[indextr].default_style">
                        <vs-checkbox v-model="data[indextr].default_style" @change="changeDefaultStyle(data[indextr])">{{(data[indextr].default_style) ? 'Default' : 'Not default'}}</vs-checkbox>
                    </vs-td>

                </vs-tr>

            </template>
        </vs-table>

        <!-- Create style popup -->
        <vs-popup title="Create style" :active.sync="showPopup['create']">

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create'].name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Container:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/2" v-model="newObject['create'].background_color" />
                    <input type="color" class="w-1/2" v-model="newObject['create'].text_color" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Input:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/4" v-model="newObject['create'].input_background_color" />
                    <input type="color" class="w-1/4" v-model="newObject['create'].input_text_color" />
                    <input type="color" class="w-1/4" v-model="newObject['create'].input_border_color" />
                    <vs-checkbox class="w-1/4" v-model="newObject['create'].input_address_visible">Address</vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Button:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/4" v-model="newObject['create'].button_background_color" />
                    <input type="color" class="w-1/4" v-model="newObject['create'].button_text_color" />
                    <vs-input class="w-1/2" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['create'].button_text" />
                </div>
            </div>

            <hr class="mb-4">

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Countdown:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/6" v-model="newObject['create'].countdown_text_background_color" />
                    <input type="color" class="w-1/6" v-model="newObject['create'].countdown_text_color" />
                    <input type="color" class="w-1/6" v-model="newObject['create'].countdown_time_color" />
                    <vs-input type="number" class="w-1/3"  icon-pack="feather" icon="icon-clock" icon-no-border v-model="newObject['create'].countdown_time" />
                    <vs-checkbox class="w-1/6" v-model="newObject['create'].countdown_visible"></vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>People viewing:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/4" v-model="newObject['create'].people_viewing_text_color" />
                    <input type="color" class="w-1/4" v-model="newObject['create'].people_viewing_counter_color" />
                    <input type="color" class="w-1/4" v-model="newObject['create'].people_viewing_counter_background_color" />
                    <vs-checkbox class="w-1/4" v-model="newObject['create'].people_viewing_visible">Show</vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Available quantity:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/4" v-model="newObject['create'].available_quantity_bar_background_color" />
                    <input type="color" class="w-1/4" v-model="newObject['create'].available_quantity_bar_color" />
                    <input type="color" class="w-1/4" v-model="newObject['create'].available_quantity_text_color" />
                    <vs-checkbox class="w-1/4" v-model="newObject['create'].available_quantity_visible">Show</vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Trust badge:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-2/3" v-model="newObject['create'].trust_badge_color" />
                    <vs-checkbox class="w-1/3" v-model="newObject['create'].trust_badge_visible">Show</vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button
                    class="mt-12 mr-auto ml-auto"
                    @click="createObject"
                    color="primary" type="filled"
                    :disabled="!isValidForm('create')">
                    Create
                </vs-button>
            </div>

            <!-- CSS Template preview -->
            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                    <center><h1>Preview</h1></center>
                </div>
                <style-preview class="vx-col w-full" :styleObject="newObject['create']" />
            </div>

        </vs-popup>

        <!-- Edit style popup -->
        <vs-popup title="Edit style" :active.sync="showPopup['edit']">

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Name</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                    <vs-input class="w-full" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['edit'].name" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Container:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/2" v-model="newObject['edit'].background_color" />
                    <input type="color" class="w-1/2" v-model="newObject['edit'].text_color" />
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Input:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/4" v-model="newObject['edit'].input_background_color" />
                    <input type="color" class="w-1/4" v-model="newObject['edit'].input_text_color" />
                    <input type="color" class="w-1/4" v-model="newObject['edit'].input_border_color" />
                    <vs-checkbox class="w-1/4" v-model="newObject['edit'].input_address_visible">Address</vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Button:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/4" v-model="newObject['edit'].button_background_color" />
                    <input type="color" class="w-1/4" v-model="newObject['edit'].button_text_color" />
                    <vs-input class="w-1/2" icon-pack="feather" icon="icon-hash" icon-no-border v-model="newObject['edit'].button_text" />
                </div>
            </div>

            <hr class="mb-4">

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Countdown:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/6" v-model="newObject['edit'].countdown_text_background_color" />
                    <input type="color" class="w-1/6" v-model="newObject['edit'].countdown_text_color" />
                    <input type="color" class="w-1/6" v-model="newObject['edit'].countdown_time_color" />
                    <vs-input type="number" class="w-1/3"  icon-pack="feather" icon="icon-clock" icon-no-border v-model="newObject['edit'].countdown_time" />
                    <vs-checkbox class="w-1/6" v-model="newObject['edit'].countdown_visible"></vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>People viewing:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/4" v-model="newObject['edit'].people_viewing_text_color" />
                    <input type="color" class="w-1/4" v-model="newObject['edit'].people_viewing_counter_color" />
                    <input type="color" class="w-1/4" v-model="newObject['edit'].people_viewing_counter_background_color" />
                    <vs-checkbox class="w-1/4" v-model="newObject['edit'].people_viewing_visible">Show</vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Available quantity:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-1/4" v-model="newObject['edit'].available_quantity_bar_background_color" />
                    <input type="color" class="w-1/4" v-model="newObject['edit'].available_quantity_bar_color" />
                    <input type="color" class="w-1/4" v-model="newObject['edit'].available_quantity_text_color" />
                    <vs-checkbox class="w-1/4" v-model="newObject['edit'].available_quantity_visible">Show</vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                    <span>Trust badge:</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full flex items-center">
                    <input type="color" class="w-2/3" v-model="newObject['edit'].trust_badge_color" />
                    <vs-checkbox class="w-1/3" v-model="newObject['edit'].trust_badge_visible">Show</vs-checkbox>
                </div>
            </div>

            <div class="vx-row mb-6">
                <vs-button
                    class="mt-12 mr-auto ml-auto"
                    @click="updateObject"
                    color="primary" type="filled"
                    :disabled="!isValidForm('edit')">
                    Update
                </vs-button>
            </div>

            <!-- CSS Template preview -->
            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                    <center><h1>Preview</h1></center>
                </div>
                <style-preview class="vx-col w-full" :styleObject="newObject['edit']" />
            </div>

        </vs-popup>

        <!-- Preview popup -->
        <vs-popup title="Style preview" :active.sync="showPopup['preview']">

            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                    <center><h1>Style name: {{ newObject['preview'].name }}</h1></center>
                </div>
                <style-preview class="vx-col w-full" :styleObject="newObject['preview']" />
            </div>

        </vs-popup>

    </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import { VueContext } from 'vue-context'
import StylePreview from '../../components/StylePreview.vue'

let cssStyleBoilerPlate = `\
#formContainer {
    background-color: --background_color-- !important;
    color: --text_color-- !important;
}

.form-input {
    background-color: --input_background_color-- !important;
    color: --input_text_color-- !important;
    border-color: --input_border_color-- !important;
}

.form-button {
    background-color: --button_background_color-- !important;
    color: --button_text_color-- !important;
}

.countdown {
    display: --countdown_display-- !important;
}

.countdown-text {
    background-color: --countdown_text_background_color-- !important;
    color: --countdown_text_color-- !important;
}

.countdown-time {
    color: --countdown_time_color-- !important;
}

.people-viewing {
    display: --people_viewing_display-- !important;
    color: --people_viewing_text_color-- !important;
}

.people-viewing-count {
    background-color: --people_viewing_counter_background_color-- !important;
    color: --people_viewing_counter_color-- !important;
}

.available-quantity {
    display: --available_quantity_display-- !important;
}

.available-quantity-bar {
    --barColor: --available_quantity_bar_color-- !important;
    background-color: --available_quantity_bar_background_color-- !important;
    color: --available_quantity_text_color-- !important;
}

.trust-badge {
    display: --trust_badge_display-- !important;
    color: --trust_badge_color-- !important;
}
`

function getCssCode(obj) {
  let code = cssStyleBoilerPlate

  code = code.replace('--background_color--', obj.background_color)
  code = code.replace('--text_color--', obj.text_color)

  code = code.replace('--input_background_color--', obj.input_background_color)
  code = code.replace('--input_text_color--', obj.input_text_color)
  code = code.replace('--input_border_color--', obj.input_border_color)

  code = code.replace('--button_background_color--', obj.button_background_color)
  code = code.replace('--button_text_color--', obj.button_text_color)

  code = code.replace('--countdown_display--', (obj.countdown_visible) ? 'grid' : 'none')
  code = code.replace('--countdown_text_background_color--', obj.countdown_text_background_color)
  code = code.replace('--countdown_text_color--', obj.countdown_text_color)
  code = code.replace('--countdown_time_color--', obj.countdown_time_color)

  code = code.replace('--people_viewing_display--', (obj.people_viewing_visible) ? 'block' : 'none')
  code = code.replace('--people_viewing_text_color--', obj.people_viewing_text_color)
  code = code.replace('--people_viewing_counter_background_color--', obj.people_viewing_counter_background_color)
  code = code.replace('--people_viewing_counter_color--', obj.people_viewing_counter_color)

  code = code.replace('--available_quantity_display--', (obj.available_quantity_visible) ? 'block' : 'none')
  code = code.replace('--available_quantity_bar_color--', obj.available_quantity_bar_color)
  code = code.replace('--available_quantity_bar_background_color--', obj.available_quantity_bar_background_color)
  code = code.replace('--available_quantity_text_color--', obj.available_quantity_text_color)

  code = code.replace('--trust_badge_display--', (obj.trust_badge_visible) ? 'grid' : 'none')
  code = code.replace('--trust_badge_color--', obj.trust_badge_color)

  if (!obj.input_address_visible) {
      code += '#address { display: none; }'
  }

  return code
}

export default {
  components: {
    'v-select': vSelect,
    VueContext,
    StylePreview,
  },
  data () {
    return {
      allObjects: [],
      objects: [],
      selected: [],

      maxItems: 20,

      editingObject: null,

      showPopup: {
        'create': false,
        'edit': false,
        'preview': false,
      },
      newObject:  {
        'create': {
          name: '',
          type: 'css',
          code: '',
          background_color: '#ffffff',
          text_color: '#000000',
          input_background_color: '#ffffff',
          input_text_color: '#000000',
          input_border_color: '#f2e832',
          input_address_visible: true,
          button_background_color: '#eaac00',
          button_text_color: '#ffffff',
          button_text: 'Conferma l\'ordine',
          countdown_visible: false,
          countdown_text_background_color: '#ff2233',
          countdown_text_color: '#ffffff',
          countdown_time_color: '#000000',
          countdown_time: 5,
          people_viewing_visible: false,
          people_viewing_text_color: '#000000',
          people_viewing_counter_color: '#ffffff',
          people_viewing_counter_background_color: '#ff2233',
          available_quantity_visible: false,
          available_quantity_text_color: '#ffffff',
          available_quantity_bar_background_color: '#919191',
          available_quantity_bar_color: '#ff330f',
          trust_badge_visible: false,
          trust_badge_color: '#000000',
        },
        'edit': {
          name: '',
          type: 'css',
          code: '',
          background_color: '#ffffff',
          text_color: '#000000',
          input_background_color: '#ffffff',
          input_text_color: '#000000',
          input_border_color: '#f2e832',
          input_address_visible: true,
          button_background_color: '#eaac00',
          button_text_color: '#ffffff',
          button_text: 'Conferma l\'ordine',
          countdown_visible: false,
          countdown_text_background_color: '#ff2233',
          countdown_text_color: '#ffffff',
          countdown_time_color: '#000000',
          countdown_time: 5,
          people_viewing_visible: false,
          people_viewing_text_color: '#000000',
          people_viewing_counter_color: '#ffffff',
          people_viewing_counter_background_color: '#ff2233',
          available_quantity_visible: false,
          available_quantity_text_color: '#ffffff',
          available_quantity_bar_background_color: '#919191',
          available_quantity_bar_color: '#ff330f',
          trust_badge_visible: false,
          trust_badge_color: '#000000',
        },
        'preview': {
          name: '',
          type: 'css',
          code: '',
          background_color: '#ffffff',
          text_color: '#000000',
          input_background_color: '#ffffff',
          input_text_color: '#000000',
          input_border_color: '#f2e832',
          input_address_visible: true,
          button_background_color: '#eaac00',
          button_text_color: '#ffffff',
          button_text: 'Conferma l\'ordine',
          countdown_visible: false,
          countdown_text_background_color: '#ff2233',
          countdown_text_color: '#ffffff',
          countdown_time_color: '#000000',
          countdown_time: 5,
          people_viewing_visible: false,
          people_viewing_text_color: '#000000',
          people_viewing_counter_color: '#ffffff',
          people_viewing_counter_background_color: '#ff2233',
          available_quantity_visible: false,
          available_quantity_text_color: '#ffffff',
          available_quantity_bar_background_color: '#919191',
          available_quantity_bar_color: '#ff330f',
          trust_badge_visible: false,
          trust_badge_color: '#000000',
        },
      },

      previewObject: null,
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
    popupType() {
      if (this.showPopup['create']) {
        return 'create'
      } else if (this.showPopup['edit']) {
        return 'edit'
      } else if (this.showPopup['preview']) {
        return 'preview'
      }
      return 'create'
    },
  },
  methods: {
    getObjectIndexById(id) {
      var index = -1;
      for (let i = 0; i < this.allObjects.length; i++) {
        if (this.allObjects[i].id == id) {
          index = i
          break
        }
      }
      return index
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    confirmDeletePopup() {
      if (this.selected.length == 0) return
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Archive confirmation',
        text: 'Are you sure you want to archive ' + this.selected.length + ' object(s)?',
        accept: this.deleteSelected
      })
    },
    async restoreObject(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$http.get('/api/v1/forms-styles/' + id + '/restore', { headers })
      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Style restored',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.applyFilters()
    },
    async deleteSelected() {
      if (this.selected.length == 0) return

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      for (let i = 0; i < this.selected.length; i++) {
        let obj = this.selected[i]
        await this.$http.delete('/api/v1/forms-styles/' + obj.id, { headers })
      }

      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: this.selected.length + ' Style(s) deleted',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.selected = []
      this.retrieveObjects()
    },
    showForm(type) {
      if (type == 'create') {
        this.clearForm(type)
      }
      this.showPopup[type] = true
    },
    clearForm(type) {
      this.newObject[type] = {
        name: '',
        type: 'css',
        code: '',
        background_color: '#ffffff',
        text_color: '#000000',
        input_background_color: '#ffffff',
        input_text_color: '#000000',
        input_border_color: '#f2e832',
        input_address_visible: true,
        button_background_color: '#eaac00',
        button_text_color: '#ffffff',
        button_text: 'Conferma l\'ordine',
        countdown_visible: false,
        countdown_text_background_color: '#ff2233',
        countdown_text_color: '#ffffff',
        countdown_time_color: '#000000',
        countdown_time: 5,
        people_viewing_visible: false,
        people_viewing_text_color: '#000000',
        people_viewing_counter_color: '#ffffff',
        people_viewing_counter_background_color: '#ff2233',
        available_quantity_visible: false,
        available_quantity_text_color: '#ffffff',
        available_quantity_bar_background_color: '#919191',
        available_quantity_bar_color: '#ff330f',
        trust_badge_visible: false,
        trust_badge_color: '#000000',
      }
    },
    isValidForm(type) {
      let obj = this.newObject[type]
      return (obj.name != '')
    },
    formatDate(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0');
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0'); //January is 0!
      let yyyy = dateObj.getFullYear();
      
      let formattedDate = yyyy + '-' + mm + '-' + dd;
      return formattedDate;
    },
    async retrieveObjects() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      const response = await this.$http.get('/api/v1/forms-styles', { headers })
      this.allObjects = response.data.data
      this.objects = this.allObjects

      this.$vs.loading.close()
    },
    editObject(id) {
      let obj = this.allObjects[this.getObjectIndexById(id)]
      this.editingObject = obj

      this.clearForm('edit')
      for (const [key, value] of Object.entries(obj.settings)) {
        this.newObject['edit'][key] = value
      }

      this.showForm('edit')
    },
    async createObject() {
      this.$vs.loading()
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let code = getCssCode(this.newObject['create'])
      let settings = this.newObject['create']

      let data = {
        name: this.newObject['create'].name,
        type: this.newObject['create'].type,
        code: code,
        settings: JSON.stringify(settings),
      }

      // Create forms style
      const response = await this.$http.post('/api/v1/forms-styles', data, { headers: headers })
      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Style created',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.showPopup['create'] = false

      this.retrieveObjects()
    },
    async updateObject() {
      this.$vs.loading()
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let code = getCssCode(this.newObject['edit'])
      let settings = this.newObject['edit']

      let data = {
        name: this.newObject['edit'].name,
        type: this.newObject['edit'].type,
        code: code,
        settings: JSON.stringify(settings),
      }

      // Update forms style
      const response = await this.$http.put('/api/v1/forms-styles/' + this.editingObject.id, data, { headers: headers })
      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Style updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })
      this.showPopup['edit'] = false

      this.retrieveObjects()
    },
    async deleteObject(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      await this.$http.delete('/api/v1/forms-styles/' + id, { headers: headers })
      this.$vs.loading.close()

      this.$vs.notify({
        title: 'Success',
        text: 'Style deleted',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.retrieveObjects()
    },
    showPreview(id) {
      this.previewObject = this.allObjects[this.getObjectIndexById(id)]
      this.newObject['preview'] = this.previewObject.settings
      this.showPopup['preview'] = true
    },
    async changeDefaultStyle(style) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        default_style: style.default_style,
      }

      // Update forms style
      const response = await this.$http.put('/api/v1/forms-styles/' + style.id, data, { headers: headers })
      this.$vs.loading.close()
      this.$vs.notify({
        title: 'Success',
        text: 'Style updated',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'success'
      })

      this.retrieveObjects()
    },
  },
  created() {
    this.retrieveObjects()
  },
  mounted () {
  }
}

</script>
