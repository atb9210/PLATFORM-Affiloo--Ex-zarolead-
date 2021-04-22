<!-- =========================================================================================
    File Name: AgGridTable.vue
    Description: Ag Grid table
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Invoices" code-toggler>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters">Filters</vs-button>
            <!--<vs-button class="mr-2" color="success" type="filled" icon-pack="feather" icon="icon-upload-cloud" @click="showForm('create')">Upload invoices</vs-button>-->
            <!--<vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-download-cloud" :disabled="selected.length == 0" @click="downloadInvoicesZip">Download invoices (ZIP)</vs-button>-->
            <vs-button class="mr-2" color="success" type="filled" icon-pack="feather" icon="icon-download" :disabled="selected.length == 0" @click="downloadInvoices">Download invoices (ZIP)</vs-button>
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-file" :disabled="selected.length == 0" @click="downloadInvoicesXML">Download invoices (XML)</vs-button>
            <vs-button class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash" @click="">Delete</vs-button>
        </p>

        <p class="flex items-center mb-4">
            <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="downloadSummary('normal')">Download invoices summary</vs-button> 
            <vs-button class="mr-2" color="warning" type="filled" icon-pack="feather" icon="icon-download-cloud" @click="downloadSummary('auto')">Download auto invoices summary</vs-button> 
        </p>

        <div v-if="showFilters" class="mb-12">
            <div class="vx-row mb-2">
                <div class="vx-col w-3-12">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('today')" icon-pack="feather" icon="icon-calendar">Today</vs-button>
                </div>
                <div class="vx-col w-3-12">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('yesterday')" icon-pack="feather" icon="icon-calendar">Yesterday</vs-button>
                </div>
                <div class="vx-col w-3-12">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('this_month')" icon-pack="feather" icon="icon-calendar">This Month</vs-button>
                </div>
                <div class="vx-col w-3-12">
                    <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('last_month')" icon-pack="feather" icon="icon-calendar">Last Month</vs-button>
                </div>
            </div>

            <div class="vx-row mb-2">
                <div class="vx-col w-1/2">
                    <label>From</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_from" />
                </div>
                <div class="vx-col w-1/2">
                    <label>To</label>
                    <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_to" />
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col w-1/2">
                    <label>Min</label>
                    <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="filters.min_amount" />
                </div>
                <div class="vx-col w-1/2">
                    <label>Max</label>
                    <vs-input type="number" min="0" step="0.01" class="w-full" icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="filters.max_amount" />
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col w-1/2">
                    <label>Status</label>
                    <v-select :options="invoiceStatusOptions" v-model="filters.status" />
                </div>
                <div class="vx-col w-1/2">
                    <label>Type</label>
                    <v-select :options="invoiceTypeOptions" v-model="filters.type" />
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col w-1/2">
                    <label>Uploaded</label>
                    <v-select :options="yesNoOptions" v-model="filters.uploaded" />
                </div>
                <div class="vx-col w-1/2">
                    <label>Transaction</label>
                    <v-select :options="yesNoOptions" v-model="filters.transaction" />
                </div>
            </div>
            <div class="vx-row mb-6">
                <div class="vx-col w-full">
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col w-full">
                    <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
                    <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
                </div>
            </div>
        </div>

        <vs-dropdown vs-trigger-click class="cursor-pointer">
            <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
                <span class="mr-2">{{ currentPage * maxItems - (maxItems - 1) }} - {{ objects.length - currentPage * maxItems > 0 ? currentPage * maxItems : objects.length }} of {{ objects.length }}</span>
                (total: € {{ total_amount.toFixed(2) }})&nbsp;
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
                <vs-th sort-key="invoice_date">Date</vs-th>
                <vs-th sort-key="uploaded">Uploaded</vs-th>
                <vs-th sort-key="transaction">Transaction</vs-th>
                <vs-th sort-key="transaction_id">Transaction ID</vs-th>
                <vs-th sort-key="number">Number</vs-th>
                <vs-th sort-key="amount">Amount</vs-th>
                <vs-th sort-key="currency">Currency</vs-th>
                <vs-th sort-key="status">Status</vs-th>
                <vs-th>Type</vs-th>
                <vs-th>Actions</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].invoice_date">
                        {{data[indextr].invoice_date}}
                    </vs-td>

                    <vs-td :data="data[indextr].uploaded">
                        {{(data[indextr].uploaded) ? 'Yes' : 'No'}}
                    </vs-td>

                    <vs-td :data="data[indextr].transaction">
                        {{(data[indextr].transaction) ? 'Yes' : 'No'}}
                    </vs-td>

                    <vs-td :data="data[indextr].transaction_id">
                        {{data[indextr].transaction_id}}
                    </vs-td>

                    <vs-td :data="data[indextr].number">
                        {{data[indextr].number}}
                    </vs-td>

                    <vs-td :data="data[indextr].amount">
                        {{data[indextr].amount}}
                    </vs-td>

                    <vs-td :data="data[indextr].currency">
                        {{data[indextr].currency}}
                    </vs-td>

                    <vs-td :data="data[indextr].status">
                        <vs-chip :color="statusColor[data[indextr].status]">{{data[indextr].status}}</vs-chip>
                    </vs-td>

                    <vs-td>
                        <vs-chip :color="invoiceTypeColor[data[indextr].type]">{{data[indextr].type}}</vs-chip>
                    </vs-td>

                    <vs-td>
                        <div class="flex items-center">
                            <vs-button @click="downloadInvoice(indextr)" class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-download"></vs-button>
                            <vs-button @click="setAutoInvoice(data[indextr].id)" class="mr-2" color="warning" type="filled" icon-pack="feather" icon="icon-bookmark"></vs-button>
                            <vs-button @click="deleteObject(data[indextr].id)" class="mr-2" color="danger" type="filled" icon-pack="feather" icon="icon-trash"></vs-button>
                        </div>
                    </vs-td>

                </vs-tr>

                <!-- TOTAL STATS -->
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td>{{total_amount.toFixed(2)}}</vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <vs-td></vs-td>
                <!-- END TOTAL STATS-->
            </template>
        </vs-table>

        <!-- Create popup -->
        <vs-popup title="Upload Invoices" :active.sync="showPopup['create']">
            <div class="vx-row mb-6 flex items-center" vs-justify="center">
                <vs-row vs-justify="center">
                    <vs-col type="flex" vs-justify="center" vs-align="center" vs-w="6">
                        <vs-card class="cardx">
                            <div slot="header">
                                <h3 class="mr-auto ml-auto text-center">
                                    <span class="mr-auto ml-auto">{{ filesToUpload.length }} files selected</span>
                                </h3>
                            </div>
                            <div slot="media" class="mb-3">
                                <img src="https://image.flaticon.com/icons/png/512/337/337946.png">
                            </div>
                            <div slot="footer">
                                <vs-row vs-justify="center">
                                    <vs-button class="mr-2" color="primary" icon-pack="feather" icon="icon-file-plus" @click="showFilePicker"></vs-button>
                                    <vs-button class="mr-2" color="success" icon-pack="feather" icon="icon-upload-cloud" :disabled="!isValidUpload()" @click="uploadFiles"></vs-button>
                                    <vs-button class="mr-2" color="danger" icon-pack="feather" icon="icon-trash" @click="clearFilesToUpload"></vs-button>
                                </vs-row>
                            </div>
                        </vs-card>
                    </vs-col>
                </vs-row>
            </div>
            <vs-row class="vx-row mb-6 flex items-center">
                <div class="vx-col w-full flex items-center">
                    <span class="ml-auto mr-auto">Upload: {{uploadProgress}}%</span>
                </div>
                <div class="vx-col w-full">
                    <vs-progress :height="12" :percent="uploadProgress" color="success"></vs-progress>
                </div>
            </vs-row>
            <input type="file" ref="filesUpload" class="hidden" multiple @change="onFilesUploadChange">
        </vs-popup>

        <!-- Edit popup -->
        <vs-popup title="Edit Invoice" :active.sync="showPopup['edit']">
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
import vSelect from 'vue-select'
import JSZip from 'jszip'
import { generateInvoicePDF } from '@assets/utils/fb-invoice'
import { invoiceTemplateXML } from '@assets/utils/invoice'

function generateInvoices(invoices) {
  // Generate PDFs
  return new Promise((resolve, reject) => {
    let pdfDocs = []
    for (let i = 0; i < invoices.length; i++) {
      pdfDocs.push(generateInvoicePDF(invoices[i]))
    }
    resolve(pdfDocs)
  })
}

export default {
  components: {
    'v-select': vSelect,
  },
  data () {
    return {
      objects: [],
      selected: [],
      deleted: [],

      maxItems: 15,

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

      dateFormat: 'yyyy-MM-dd',
      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        min_amount: '',
        max_amount: '',
        status: '',
        type: '',
        uploaded: '',
        transaction: '',
      },
      invoiceStatusOptions: [
        { value: '', label: 'All' },
        { value: 'paid', label: 'Paid' },
        { value: 'failed', label: 'Failed' },
      ],
      invoiceTypeOptions: [
        { value: '', label: 'All' },
        { value: 'normal', label: 'Normal' },
        { value: 'auto', label: 'Auto' },
      ],
      yesNoOptions: [
        { value: -1, label: 'All' },
        { value: 1, label: 'Yes' },
        { value: 0, label: 'No' },
      ],

      filesToUpload: [],
      isUploading: false,
      uploadProgress: 0,

      statusColor: {
        'paid': 'success',
        'failed': 'danger',
      },
      invoiceTypeColor: {
        'normal': 'primary',
        'auto': 'warning',
      },

      total_amount: 0.0,

      invoices: [],
      pdfDocs: [],
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

      // Retrieve invoices
      this.$http.get('/api/v1/invoices', { headers })
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

      this.selected.forEach((invoice) => {
        this.$http.delete('/api/v1/invoices/' + invoice.id, { headers })
          .then((response) => {
            this.deleted.push(invoice.id)
            if (this.deleted.length == this.selected.length) {
              this.$vs.loading.close()
              this.selected = []
              this.$vs.notify({
                title: 'Success',
                text: this.deleted.length + ' Invoice(s) deleted',
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

      // Retrieve invoices
      this.$http.post('/api/v1/invoices', data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Invoice created',
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

      this.$http.put('/api/v1/invoices/' + id, data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Invoice updated',
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

      this.$http.delete('/api/v1/invoices/' + id, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'Invoice deleted',
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
    applyFilters() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {}
      if (this.filters.date_from != '') {
        data['filter_date_from'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        data['filter_date_to'] = this.filters.date_to
      }
      if (this.filters.min_amount != '') {
        data['filter_min_amount'] = this.filters.min_amount
      }
      if (this.filters.max_amount != '') {
        data['filter_max_amount'] = this.filters.max_amount
      }
      if (this.filters.status.value != '') {
        data['filter_status'] = this.filters.status.value
      }
      if (this.filters.type.value != '') {
        data['filter_type'] = this.filters.type.value
      }
      if (this.filters.uploaded.value != -1) {
        data['filter_uploaded'] = this.filters.uploaded.value
      }
      if (this.filters.transaction.value != -1) {
        data['filter_transaction'] = this.filters.transaction.value
      }

      // Retrieve invoices
      this.$http.get('/api/v1/invoices', { headers, params: data })
        .then((response) => {
          this.objects = response.data.data
          this.calculateStats()
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
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        min_amount: '',
        max_amount: '',
        status: '',
        type: '',
        uploaded: '',
        transaction: '',
      }
      this.applyFilters()
    },
    showFilePicker() {
      this.$refs.filesUpload.click()
    },
    onFilesUploadChange(event) {
      this.filesToUpload = event.target.files
    },
    isValidUpload() {
      return this.filesToUpload.length > 0
    },
    clearFilesToUpload() {
      this.filesToUpload = []
    },
    uploadFiles() {
      if (this.isUploading) return
      this.isUploading = true
      this.uploadProgress = 0

      this.$vs.loading()

      let headers = {
        'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
        'Content-Type': 'multipart/form-data',
      }
      let config = {
        headers: headers,
        onUploadProgress: e => {
          if (e.lengthComputable) {
            this.uploadProgress = (e.loaded / e.total) * 100
          }
        }
      }
      let formData = new FormData()
      for (var i = 0; i < this.filesToUpload.length; i++)
        formData.append('files[' + i + ']', this.filesToUpload[i])

      // Retrieve invoices
      this.$http.post('/api/v1/invoices', formData, config)
        .then((response) => {
          this.showPopup['create'] = false
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: this.filesToUpload.length + ' Invoice(s) uploaded',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.clearFilesToUpload()
          this.applyFilters()
          this.isUploading = false
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
          this.isUploading = false
        })
    },
    setAutoInvoice(id) {
      this.$vs.loading()

      let obj = this.objects[this.getObjectIndexById(id)]
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken'),
      }
      let type = 'auto'
      if (obj.type == 'auto') type = 'normal'
      let data = {
        type: type,
      }

      // Update invoice
      this.$http.put('/api/v1/invoices/' + id, data, { headers })
        .then((response) => {
          this.$vs.loading.close()
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

    // TODO: to delete
    _downloadInvoice(id) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken'),
      }

      // Download invoice
      this.$http.get('/api/v1/invoices/' + id + '/download', { headers, responseType: 'blob' })
        .then((response) => {
          this.$vs.loading.close()
          const url = window.URL.createObjectURL(new Blob([response.data]))
          let headerLine = response.headers['content-disposition']
          let filenameTag = 'filename='
          let startFileNameIndex = headerLine.indexOf(filenameTag) + filenameTag.length
          let endFileNameIndex = headerLine.length
          let filename = headerLine.substring(startFileNameIndex, endFileNameIndex)
          filename = filename.replace(/['"]+/g, '')
          const link = document.createElement('a')
          link.href = url
          link.setAttribute('download', filename)
          document.body.appendChild(link)
          link.click()
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

    // TODO: to delete
    // This will not download from the cloud anymore
    downloadInvoicesZip() {
      if (this.selected.length == 0)
        return

      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken'),
      }
      let data = {
        ids: []
      }
      this.selected.forEach((obj) => {
        data.ids.push(obj.id)
      })

      // Download invoices in zip archive
      this.$http.post('/api/v1/invoices/download-zip', data, { headers, responseType: 'blob' })
        .then((response) => {
          this.$vs.loading.close()
          const url = window.URL.createObjectURL(new Blob([response.data]))
          let headerLine = response.headers['content-disposition']
          let filenameTag = 'filename='
          let startFileNameIndex = headerLine.indexOf(filenameTag) + filenameTag.length
          let endFileNameIndex = headerLine.length
          let filename = headerLine.substring(startFileNameIndex, endFileNameIndex)
          filename = filename.replace(/['"]+/g, '')
          const link = document.createElement('a')
          link.href = url
          link.setAttribute('download', filename)
          document.body.appendChild(link)
          link.click()
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
    downloadSummary(type) {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken'),
      }
      let data = {}
      if (this.filters.date_from != '') {
        data['filter_date_from'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        data['filter_date_to'] = this.filters.date_to
      }
      if (this.filters.min_amount != '') {
        data['filter_min_amount'] = this.filters.min_amount
      }
      if (this.filters.max_amount != '') {
        data['filter_max_amount'] = this.filters.max_amount
      }
      if (this.filters.status.value != '') {
        data['filter_status'] = this.filters.status.value
      }
      data['filter_type'] = type
      if (this.filters.uploaded.value != -1) {
        data['filter_uploaded'] = this.filters.uploaded.value
      }
      if (this.filters.transaction.value != -1) {
        data['filter_transaction'] = this.filters.transaction.value
      }

      // Download invoices in zip archive
      this.$http.post('/api/v1/invoices/download-summary', data, { headers, responseType: 'blob' })
        .then((response) => {
          this.$vs.loading.close()
          const url = window.URL.createObjectURL(new Blob([response.data]))
          let headerLine = response.headers['content-disposition']
          let filenameTag = 'filename='
          let startFileNameIndex = headerLine.indexOf(filenameTag) + filenameTag.length
          let endFileNameIndex = headerLine.length
          let filename = headerLine.substring(startFileNameIndex, endFileNameIndex)
          filename = filename.replace(/['"]+/g, '')
          const link = document.createElement('a')
          link.href = url
          link.setAttribute('download', filename)
          document.body.appendChild(link)
          link.click()
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
    filterDatesShortcut(period) {
      let invalidPeriod = false;

      switch (period) {
        case 'today': {
          let today = new Date();
          this.filters.date_from = this.formatDate(today);
          this.filters.date_to = this.formatDate(today);
        } break;
        case 'yesterday': {
          let dateObj = new Date();
          dateObj.setDate(dateObj.getDate() - 1);
          this.filters.date_from = this.formatDate(dateObj);
          this.filters.date_to = this.formatDate(dateObj);
        } break;
        case 'this_month': {
          let dateObj = new Date();
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1);
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0);
          this.filters.date_from = this.formatDate(fromObj);
          this.filters.date_to = this.formatDate(toObj);
        } break;
        case 'last_month': {
          let dateObj = new Date();
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth() - 1, 1);
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 0);
          this.filters.date_from = this.formatDate(fromObj);
          this.filters.date_to = this.formatDate(toObj);
        } break;
        default: {
          invalidPeriod = true;
        } break;
      }

      if(!invalidPeriod) {
        this.applyFilters();
      }
    },
    formatDate(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0');
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0'); //January is 0!
      let yyyy = dateObj.getFullYear();
      
      let formattedDate = yyyy + '-' + mm + '-' + dd;
      return formattedDate;
    },
    downloadInvoice(i) {
      this.$vs.loading()
      this.$nextTick(() => {
        setTimeout(() => {
          let invoiceD = new Date(this.objects[i].invoice_date)
          let invoice = {
            'id': this.objects[i].transaction_id,
            'ad_account_name': this.objects[i].info['ad_account_name'],
            'ad_account_id': this.objects[i].info['ad_account_id'],
            'time': parseInt(invoiceD.getTime() / 1000),
            'funding_source': this.objects[i].info['funding_source'],
            'reference_number': this.objects[i].info['reference_number'],
            'amount': this.objects[i].amount,
            'currency': this.objects[i].currency,
            'billing_reason': this.objects[i].info['billing_reason'],
            'billing_start_time': this.objects[i].info['billing_start_time'],
            'billing_end_time': this.objects[i].info['billing_end_time'],
            'vat_invoice_id': this.objects[i].number,
          }
          let doc = generateInvoicePDF(invoice)
          let d = new Date(invoice.time * 1000)
          let filename = ''
          filename += this.formatDate(d) + 'T' + d.getHours() + '-' + d.getMinutes()
          filename += ' Transaction #' + invoice.id
          filename += '.pdf'
          this.$vs.loading.close()
          doc.save(filename)
        }, 100)
      })
    },
    downloadInvoices() {
      this.$vs.loading()
      this.$nextTick(() => {
        setTimeout(() => {
          this.invoices = []
          for (let i = 0; i < this.selected.length; i++) {
            let invoiceD = new Date(this.selected[i].invoice_date)
            let invoice = {
              'id': this.selected[i].transaction_id,
              'ad_account_name': this.selected[i].info['ad_account_name'],
              'ad_account_id': this.selected[i].info['ad_account_id'],
              'time': parseInt(invoiceD.getTime() / 1000),
              'funding_source': this.selected[i].info['funding_source'],
              'reference_number': this.selected[i].info['reference_number'],
              'amount': this.selected[i].amount,
              'currency': this.selected[i].currency,
              'billing_reason': this.selected[i].info['billing_reason'],
              'billing_start_time': this.selected[i].info['billing_start_time'],
              'billing_end_time': this.selected[i].info['billing_end_time'],
              'vat_invoice_id': this.selected[i].number,
            }
            this.invoices.push(invoice)
          }

          generateInvoices(this.invoices)
            .then((pdfDocs) => {
              this.pdfDocs = pdfDocs

              // Create zip file and download
              let zip = new JSZip()
              for (let i = 0; i < this.invoices.length; i++) {
                let invoice = this.invoices[i]
                let d = new Date(invoice.time * 1000)
                let filename = ''
                filename += this.formatDate(d) + 'T' + d.getHours() + '-' + d.getMinutes()
                filename += ' Transaction #' + invoice.id
                filename += '.pdf'
                zip.folder(d.getFullYear().toString())
                  .folder((d.getMonth() + 1).toString().padStart(2, '0'))
                  .file(filename, this.pdfDocs[i].output('arraybuffer'))
              }
              zip.generateAsync({type:"blob"}).then(function (blob) {
                let url = window.URL.createObjectURL(blob)
                let a = document.createElement("a")
                document.body.appendChild(a)
                a.style = "display: none"
                a.href = url
                a.download = "invoices.zip"
                a.click()
                window.URL.revokeObjectURL(url)
                this.$vs.loading.close()
                this.$vs.notify({
                  title: 'Success',
                  text: this.invoices.length + ' invoices downloaded',
                  iconPack: 'feather',
                  icon: 'icon-alert-circle',
                  color: 'success'
                })
              }.bind(this), function (err) {
                // TODO: to delete
                // This should be here only for debug purposes
                console.log(err)

                this.$vs.loading.close()
                this.$vs.notify({
                  title: 'Error',
                  text: 'Error while downloading PDFs',
                  iconPack: 'feather',
                  icon: 'icon-alert-circle',
                  color: 'danger'
                })
              }.bind(this));
            })
            .catch((log) => {
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Error',
                text: 'Error while generating PDFs',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'danger'
              })
            })
        }, 100)
      })
    },
    downloadInvoicesXML() {
      this.$vs.loading()
      this.$nextTick(() => {
        setTimeout(() => {
          this.invoices = []
          for (let i = 0; i < this.selected.length; i++) {
            let invoiceD = new Date(this.selected[i].invoice_date)
            let invoice = {
              'id': this.selected[i].transaction_id,
              'ad_account_name': this.selected[i].info['ad_account_name'],
              'ad_account_id': this.selected[i].info['ad_account_id'],
              'time': parseInt(invoiceD.getTime() / 1000),
              'date': this.selected[i].invoice_date,
              'funding_source': this.selected[i].info['funding_source'],
              'reference_number': this.selected[i].info['reference_number'],
              'amount': this.selected[i].amount,
              'currency': this.selected[i].currency,
              'billing_reason': this.selected[i].info['billing_reason'],
              'billing_start_time': this.selected[i].info['billing_start_time'],
              'billing_end_time': this.selected[i].info['billing_end_time'],
              'vat_invoice_id': this.selected[i].number,
            }
            this.invoices.push(invoice)
          }


          let zip = new JSZip()
          for (let i = 0; i < this.invoices.length; i++) {
            let invoice = this.invoices[i]

            let dateNoTime = (invoice.date.split(" "))[0]

            let fContent = invoiceTemplateXML
            fContent = fContent.replace(/--CURRENCY--/g, invoice.currency)
            fContent = fContent.replace(/--NUMBER--/g, invoice.reference_number)
            fContent = fContent.replace(/--AMOUNT--/g, invoice.amount)
            fContent = fContent.replace(/--DATE--/g, dateNoTime)

            let d = new Date(invoice.time * 1000)
            let filename = ''
            filename += this.formatDate(d) + 'T' + d.getHours() + '-' + d.getMinutes()
            filename += ' Transaction #' + invoice.id
            filename += '.xml'

            zip.folder(d.getFullYear().toString())
              .folder((d.getMonth() + 1).toString().padStart(2, '0'))
              .file(filename, fContent)
          }

          // Create zip file and download
          zip.generateAsync({type:"blob"}).then(function (blob) {
            let url = window.URL.createObjectURL(blob)
            let a = document.createElement("a")
            document.body.appendChild(a)
            a.style = "display: none"
            a.href = url
            a.download = "invoices.zip"
            a.click()
            window.URL.revokeObjectURL(url)
            this.$vs.loading.close()
            this.$vs.notify({
              title: 'Success',
              text: this.invoices.length + ' invoices downloaded',
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'success'
            })
          }.bind(this))

        }, 100)
      })
    },
    calculateStats() {
      this.total_amount = 0.0
      this.objects.forEach((obj) => {
        this.total_amount += parseFloat(obj.amount)
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
