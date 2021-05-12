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
                <div class="vx-col w-full">
                    <div id="css-plugin-preview-form-container" :style="cssPluginContainerStyle">
                        <div class="css-plugin-preview-countdown" :style="cssPluginCountdownStyle">
                            <div class="css-plugin-preview-countdown-text-container">
                                <span class="css-plugin-preview-countdown-text" :style="cssPluginCountdownTextStyle">L'OFFERTA SCADE TRA:</span>
                            </div>
                            <div><p class="css-plugin-preview-countdown-time" :style="cssPluginCountdownTimeStyle">{{cssPluginCountdownTime}}</p></div>
                        </div>
                        <div id="css-plugin-preview-main-form" style="display: block;">
                            <div>
                                <div style="margin-botton: 1rem; display: block;">
                                    <label style="display: inline-block;margin-bottom: .5rem;">Nome e cognome</label>
                                    <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                                </div>
                                <div style="margin-botton: 1rem; display: block;">
                                    <label style="display: inline-block;margin-bottom: .5rem;">Telefono (meglio cellulare)</label>
                                    <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                                </div>
                                <div style="margin-botton: 1rem; display: block;" :style="cssPluginInputAddressStyle">
                                    <label style="display: inline-block;margin-bottom: .5rem;">Città e indirizzo di consegna</label>
                                    <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                                </div>
                            </div>
                            <div class="css-plugin-preview-trust-badge" :style="cssPluginTrustBadgeStyle">
                                <div style="text-align: right;">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div style="padding: 0.5rem 0.5rem; font-size: 0.7rem; display: flex; align-items: center;">
                                    <p>
                                        I tuoi dati sono al sicuro.<br>
                                        Un nostro consulente ti contatterà per aiutarti a completare il tuo ordine.
                                    </p>
                                </div>
                            </div>
                            <button class="css-plugin-preview-form-button" :style="cssPluginButtonStyle">{{ cssPluginButtonText }}</button>
                        </div>

                    </div>
                </div>
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
                <div class="vx-col w-full">
                    <div id="css-plugin-preview-form-container" :style="cssPluginContainerStyle">
                        <div class="css-plugin-preview-countdown" :style="cssPluginCountdownStyle">
                            <div class="css-plugin-preview-countdown-text-container">
                                <span class="css-plugin-preview-countdown-text" :style="cssPluginCountdownTextStyle">L'OFFERTA SCADE TRA:</span>
                            </div>
                            <div><p class="css-plugin-preview-countdown-time" :style="cssPluginCountdownTimeStyle">{{cssPluginCountdownTime}}</p></div>
                        </div>
                        <div id="css-plugin-preview-main-form" style="display: block;">
                            <div>
                                <div style="margin-botton: 1rem; display: block;">
                                    <label style="display: inline-block;margin-bottom: .5rem;">Nome e cognome</label>
                                    <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                                </div>
                                <div style="margin-botton: 1rem; display: block;">
                                    <label style="display: inline-block;margin-bottom: .5rem;">Telefono (meglio cellulare)</label>
                                    <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                                </div>
                                <div style="margin-botton: 1rem; display: block;" :style="cssPluginInputAddressStyle">
                                    <label style="display: inline-block;margin-bottom: .5rem;">Città e indirizzo di consegna</label>
                                    <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                                </div>
                            </div>
                            <div class="css-plugin-preview-trust-badge" :style="cssPluginTrustBadgeStyle">
                                <div style="text-align: right;">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div style="padding: 0.5rem 0.5rem; font-size: 0.7rem; display: flex; align-items: center;">
                                    <p>
                                        I tuoi dati sono al sicuro.<br>
                                        Un nostro consulente ti contatterà per aiutarti a completare il tuo ordine.
                                    </p>
                                </div>
                            </div>
                            <button class="css-plugin-preview-form-button" :style="cssPluginButtonStyle">{{ cssPluginButtonText }}</button>
                        </div>

                    </div>
                </div>
            </div>

        </vs-popup>

        <!-- Preview popup -->
        <vs-popup title="Style preview" :active.sync="showPopup['preview']">

            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                    <center><h1>{{ newObject['preview'].name }}</h1></center>
                </div>
                <div class="vx-col w-full">
                    <div id="css-plugin-preview-form-container" :style="cssPluginContainerStyle">
                        <div id="css-plugin-preview-main-form" style="display: block;">
                            <div>
                                <div style="margin-botton: 1rem; display: block;">
                                    <label style="display: inline-block;margin-bottom: .5rem;">Nome e cognome</label>
                                    <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                                </div>
                                <div style="margin-botton: 1rem; display: block;">
                                    <label style="display: inline-block;margin-bottom: .5rem;">Telefono (meglio cellulare)</label>
                                    <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                                </div>
                                <div style="margin-botton: 1rem; display: block;">
                                    <label style="display: inline-block;margin-bottom: .5rem;">Città e indirizzo di consegna</label>
                                    <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                                </div>
                            </div>
                            <button class="css-plugin-preview-form-button" :style="cssPluginButtonStyle">{{ cssPluginButtonText }}</button>
                        </div>

                    </div>
                </div>
            </div>

        </vs-popup>

    </vx-card>
</template>

<script>
import vSelect from 'vue-select'
import { VueContext } from 'vue-context'

let cssStyleBoilerPlate = `\
#formContainer {
    background-color: --background_color-- !important;
    color: --text_color-- !important;
}

.form-input {
    background-color: --input_background_color-- !important;
    color: --input_text_color-- !important;
    border-color: --input_border_color-- !important;
}'

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
          trust_badge_visible: false,
          trust_badge_color: '#000000',
          countdown_visible: false,
          countdown_text_background_color: '#ff2233',
          countdown_text_color: '#ffffff',
          countdown_time_color: '#000000',
          countdown_time: 5,
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
          trust_badge_visible: false,
          trust_badge_color: '#000000',
          countdown_visible: false,
          countdown_text_background_color: '#ff2233',
          countdown_text_color: '#ffffff',
          countdown_time_color: '#000000',
          countdown_time: 5,
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
          trust_badge_visible: false,
          trust_badge_color: '#000000',
          countdown_visible: false,
          countdown_text_background_color: '#ff2233',
          countdown_text_color: '#ffffff',
          countdown_time_color: '#000000',
          countdown_time: 5,
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
    cssPluginContainerStyle() {
      let type = this.popupType
      return {
        backgroundColor: this.newObject[type].background_color,
        color: this.newObject[type].text_color,
      }
    },
    cssPluginInputStyle() {
      let type = this.popupType
      return {
        backgroundColor: this.newObject[type].input_background_color,
        color: this.newObject[type].input_text_color,
        borderColor: this.newObject[type].input_border_color,
      }
    },
    cssPluginButtonStyle() {
      let type = this.popupType
      return {
        backgroundColor: this.newObject[type].button_background_color,
        color: this.newObject[type].button_text_color,
      }
    },
    cssPluginButtonText() {
      let type = this.popupType
      return this.newObject[type].button_text
    },
    cssPluginInputAddressStyle() {
      let type = this.popupType
      return {
        display: (this.newObject[type].input_address_visible) ? 'block' : 'none',
      }
    },
    cssPluginCountdownStyle() {
      let type = this.popupType
      return {
        display: (this.newObject[type].countdown_visible) ? 'grid' : 'none',
      }
    },
    cssPluginCountdownTextStyle() {
      let type = this.popupType
      return {
        backgroundColor: this.newObject[type].countdown_text_background_color,
        color: this.newObject[type].countdown_text_color,
      }
    },
    cssPluginCountdownTimeStyle() {
      let type = this.popupType
      return {
        color: this.newObject[type].countdown_time_color,
      }
    },
    cssPluginCountdownTime() {
      let type = this.popupType
      return this.newObject[type].countdown_time + ':00'
    },
    cssPluginTrustBadgeStyle() {
      let type = this.popupType
      return {
        display: (this.newObject[type].trust_badge_visible) ? 'grid' : 'none',
        color: this.newObject[type].trust_badge_color,
      }
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
        trust_badge_visible: false,
        trust_badge_color: '#000000',
        countdown_visible: false,
        countdown_text_background_color: '#ff2233',
        countdown_text_color: '#ffffff',
        countdown_time_color: '#000000',
        countdown_time: 5,
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
  },
  created() {
    this.retrieveObjects()
  },
  mounted () {
  }
}

</script>

<style>
#css-plugin-preview-form-container {
    width: 100%;
    /*color: black;*/
    /*background-color: white;*/
}

#css-plugin-preview-main-form {
   padding: 15px;
   height: 100%;
}

.css-plugin-preview-form-input {
	margin-bottom: 0.5em;
	font-weight: 400;
	width: 100%;
	border-radius: 3px;
	padding: 15px;
	font-size: 22px;
	border-width: 1px;
	border-style: double;
	/*border-color: #f2e832;*/
	box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5);
	font-family: "Open Sans",Sans-serif;
	font-weight: 400;
}

.css-plugin-preview-form-button {
    margin-bottom: 3em;
    border: 1px solid #e0e0e0;
    font-size: 25px;
    padding: 10px;
    width: 90%;
    margin-left: auto !important;
    margin-right: auto !important;
    display: block;
    float: none;
    font-family: "Arial",Sans-serif;
    font-weight: 600;
    text-transform: none;
    /*color: #fff;*/
    /*background-color: #eaac00;*/
    border-radius: 8px;
    box-shadow: 0px 0px 10px 0px rgba(0,0,0,.5);
}

.css-plugin-preview-countdown {
    width: 100%;
    padding: 0.2rem;
    font-size: 3.5rem;
    display: grid;
    grid-template-columns: auto auto auto;
    font-weight: bold;
}

.css-plugin-preview-countdown-text-container {
    padding: 0.5rem 0.5rem;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.css-plugin-preview-countdown-text {
    background-color: #ff2233;
    color: #ffffff;
    border-radius: 0.4rem;
    padding: 0rem 0.4rem;
}

.css-plugin-preview-countdown-time {
    font-size: 3rem;
}

.css-plugin-preview-trust-badge {
    width: 100%;
    padding: 0.2rem;
    font-size: 3.5rem;
    margin-bottom: 1rem;
    display: grid;
    grid-template-columns: auto auto auto;
}
</style>
