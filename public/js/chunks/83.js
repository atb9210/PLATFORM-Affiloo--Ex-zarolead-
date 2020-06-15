(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[83],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/Invoices.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/Invoices.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ui_elements_table_InvoicesTable_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ui-elements/table/InvoicesTable.vue */ "./resources/js/src/views/ui-elements/table/InvoicesTable.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    InvoicesTable: _ui_elements_table_InvoicesTable_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  data: function data() {
    return {};
  },
  methods: {},
  created: function created() {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-select */ "./node_modules/vue-select/dist/vue-select.js");
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue_select__WEBPACK_IMPORTED_MODULE_0__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    'v-select': vue_select__WEBPACK_IMPORTED_MODULE_0___default.a
  },
  data: function data() {
    return {
      objects: [],
      selected: [],
      deleted: [],
      maxItems: 15,
      editingObject: -1,
      showPopup: {
        'create': false,
        'edit': false
      },
      newObject: {
        'create': {
          name: ''
        },
        'edit': {
          name: ''
        }
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
        transaction: ''
      },
      invoiceStatusOptions: [{
        value: '',
        label: 'All'
      }, {
        value: 'paid',
        label: 'Paid'
      }, {
        value: 'failed',
        label: 'Failed'
      }],
      invoiceTypeOptions: [{
        value: '',
        label: 'All'
      }, {
        value: 'normal',
        label: 'Normal'
      }, {
        value: 'auto',
        label: 'Auto'
      }],
      yesNoOptions: [{
        value: -1,
        label: 'All'
      }, {
        value: 1,
        label: 'Yes'
      }, {
        value: 0,
        label: 'No'
      }],
      filesToUpload: [],
      isUploading: false,
      uploadProgress: 0,
      statusColor: {
        'paid': 'success',
        'failed': 'danger'
      },
      invoiceTypeColor: {
        'normal': 'primary',
        'auto': 'warning'
      },
      total_amount: 0.0
    };
  },
  computed: {
    paginationPageSize: function paginationPageSize() {
      if (this.gridApi) return this.gridApi.paginationGetPageSize();else return 50;
    },
    totalPages: function totalPages() {
      if (this.gridApi) return this.gridApi.paginationGetTotalPages();else return 0;
    },
    currentPage: {
      get: function get() {
        if (this.gridApi) return this.gridApi.paginationGetCurrentPage() + 1;else return 1;
      },
      set: function set(val) {
        this.gridApi.paginationGoToPage(val - 1);
      }
    }
  },
  methods: {
    getObjectIndexById: function getObjectIndexById(id) {
      var index = -1;
      this.objects.forEach(function (obj, i) {
        if (obj.id == id) {
          index = i;
          return;
        }
      });
      return index;
    },
    retrieveObjects: function retrieveObjects() {
      var _this = this;

      this.selected = [];
      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }; // Retrieve invoices

      this.$http.get('/api/v1/invoices', {
        headers: headers
      }).then(function (response) {
        _this.objects = response.data.data;
        total_amount = 0.0;

        _this.objects.forEach(function (obj) {
          total_amount += obj.amount;
        });

        _this.$vs.loading.close();

        console.log(_this.objects);
      })["catch"](function (error) {
        _this.$vs.loading.close();

        _this.$vs.notify({
          title: 'Error',
          text: error.message,
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        });
      });
    },
    onPageChanged: function onPageChanged(val) {
      this.currentPage = val;
    },
    deleteSelected: function deleteSelected() {
      var _this2 = this;

      if (this.selected.length == 0) return;
      this.$vs.loading();
      this.deleted = [];
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      this.selected.forEach(function (invoice) {
        _this2.$http["delete"]('/api/v1/invoices/' + invoice.id, {
          headers: headers
        }).then(function (response) {
          _this2.deleted.push(invoice.id);

          if (_this2.deleted.length == _this2.selected.length) {
            _this2.$vs.loading.close();

            _this2.selected = [];

            _this2.$vs.notify({
              title: 'Success',
              text: _this2.deleted.length + ' Invoice(s) deleted',
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'success'
            });

            _this2.selected = [];
            _this2.deleted = [];

            _this2.applyFilters();
          }
        })["catch"](function (error) {
          _this2.$vs.loading.close();

          _this2.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          });
        });
      });
    },
    showForm: function showForm(type) {
      this.showPopup[type] = true;
    },
    clearForm: function clearForm(type) {
      this.newObject[type] = {
        name: '',
        bg_color: '#000000',
        fg_color: '#ffffff'
      };
    },
    isValidForm: function isValidForm(type) {
      return this.newObject[type].name.length > 0;
    },
    createObject: function createObject() {
      var _this3 = this;

      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      var data = {
        name: this.newObject['create'].name,
        bg_color: this.newObject['create'].bg_color,
        fg_color: this.newObject['create'].fg_color
      }; // Retrieve invoices

      this.$http.post('/api/v1/invoices', data, {
        headers: headers
      }).then(function (response) {
        _this3.$vs.loading.close();

        _this3.$vs.notify({
          title: 'Success',
          text: 'Invoice created',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        });

        _this3.clearForm('create');

        _this3.showPopup['create'] = false;

        _this3.applyFilters();
      })["catch"](function (error) {
        _this3.$vs.loading.close();

        _this3.$vs.notify({
          title: 'Error',
          text: error.message,
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        });
      });
    },
    editForm: function editForm(id) {
      var obj = this.objects[this.getObjectIndexById(id)];
      this.newObject['edit'] = {
        name: obj.name,
        bg_color: obj.bg_color,
        fg_color: obj.fg_color
      };
      this.editingObject = obj.id;
      this.showForm('edit');
    },
    updateObject: function updateObject() {
      var _this4 = this;

      if (this.editingObject <= 0) return;
      var id = this.editingObject;
      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      var data = this.newObject['edit'];
      this.$http.put('/api/v1/invoices/' + id, data, {
        headers: headers
      }).then(function (response) {
        _this4.$vs.loading.close();

        _this4.$vs.notify({
          title: 'Success',
          text: 'Invoice updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        });

        _this4.applyFilters();
      })["catch"](function (error) {
        _this4.$vs.loading.close();

        _this4.$vs.notify({
          title: 'Error',
          text: error.message,
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        });
      });
      this.showPopup['edit'] = false;
      this.editingObject = -1;
    },
    deleteObject: function deleteObject(id) {
      var _this5 = this;

      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      this.$http["delete"]('/api/v1/invoices/' + id, {
        headers: headers
      }).then(function (response) {
        _this5.$vs.loading.close();

        _this5.$vs.notify({
          title: 'Success',
          text: 'Invoice deleted',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        });

        _this5.applyFilters();
      })["catch"](function (error) {
        _this5.$vs.loading.close();

        _this5.$vs.notify({
          title: 'Error',
          text: error.message,
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        });
      });
    },
    applyFilters: function applyFilters() {
      var _this6 = this;

      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      var data = {};

      if (this.filters.date_from != '') {
        data['filter_date_from'] = this.filters.date_from;
      }

      if (this.filters.date_to != '') {
        data['filter_date_to'] = this.filters.date_to;
      }

      if (this.filters.min_amount != '') {
        data['filter_min_amount'] = this.filters.min_amount;
      }

      if (this.filters.max_amount != '') {
        data['filter_max_amount'] = this.filters.max_amount;
      }

      if (this.filters.status.value != '') {
        data['filter_status'] = this.filters.status.value;
      }

      if (this.filters.type.value != '') {
        data['filter_type'] = this.filters.type.value;
      }

      if (this.filters.uploaded.value != -1) {
        data['filter_uploaded'] = this.filters.uploaded.value;
      }

      if (this.filters.transaction.value != -1) {
        data['filter_transaction'] = this.filters.transaction.value;
      } // Retrieve invoices


      this.$http.get('/api/v1/invoices', {
        headers: headers,
        params: data
      }).then(function (response) {
        console.log(response);
        _this6.objects = response.data.data;

        _this6.$vs.loading.close();
      })["catch"](function (error) {
        _this6.$vs.loading.close();

        _this6.$vs.notify({
          title: 'Error',
          text: error.message,
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        });
      });
    },
    resetFilters: function resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        min_amount: '',
        max_amount: '',
        status: '',
        type: '',
        uploaded: '',
        transaction: ''
      };
      this.applyFilters();
    },
    showFilePicker: function showFilePicker() {
      this.$refs.filesUpload.click();
    },
    onFilesUploadChange: function onFilesUploadChange(event) {
      this.filesToUpload = event.target.files;
    },
    isValidUpload: function isValidUpload() {
      return this.filesToUpload.length > 0;
    },
    clearFilesToUpload: function clearFilesToUpload() {
      this.filesToUpload = [];
    },
    uploadFiles: function uploadFiles() {
      var _this7 = this;

      if (this.isUploading) return;
      this.isUploading = true;
      this.uploadProgress = 0;
      this.$vs.loading();
      var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
        'Content-Type': 'multipart/form-data'
      };
      var config = {
        headers: headers,
        onUploadProgress: function onUploadProgress(e) {
          if (e.lengthComputable) {
            _this7.uploadProgress = e.loaded / e.total * 100;
          }
        }
      };
      var formData = new FormData();

      for (var i = 0; i < this.filesToUpload.length; i++) {
        formData.append('files[' + i + ']', this.filesToUpload[i]);
      } // Retrieve invoices


      this.$http.post('/api/v1/invoices', formData, config).then(function (response) {
        _this7.showPopup['create'] = false;

        _this7.$vs.loading.close();

        _this7.$vs.notify({
          title: 'Success',
          text: _this7.filesToUpload.length + ' Invoice(s) uploaded',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        });

        _this7.clearFilesToUpload();

        _this7.applyFilters();

        _this7.isUploading = false;
      })["catch"](function (error) {
        _this7.$vs.loading.close();

        _this7.$vs.notify({
          title: 'Error',
          text: error.message,
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        });

        _this7.isUploading = false;
      });
    },
    setAutoInvoice: function setAutoInvoice(id) {
      var _this8 = this;

      this.$vs.loading();
      var obj = this.objects[this.getObjectIndexById(id)];
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      var type = 'auto';
      if (obj.type == 'auto') type = 'normal';
      var data = {
        type: type
      }; // Update invoice

      this.$http.put('/api/v1/invoices/' + id, data, {
        headers: headers
      }).then(function (response) {
        _this8.$vs.loading.close();

        _this8.applyFilters();
      })["catch"](function (error) {
        _this8.$vs.loading.close();

        _this8.$vs.notify({
          title: 'Error',
          text: error.message,
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        });
      });
    },
    downloadInvoice: function downloadInvoice(id) {
      var _this9 = this;

      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }; // Download invoice

      this.$http.get('/api/v1/invoices/' + id + '/download', {
        headers: headers,
        responseType: 'blob'
      }).then(function (response) {
        _this9.$vs.loading.close();

        var url = window.URL.createObjectURL(new Blob([response.data]));
        var headerLine = response.headers['content-disposition'];
        var filenameTag = 'filename=';
        var startFileNameIndex = headerLine.indexOf(filenameTag) + filenameTag.length;
        var endFileNameIndex = headerLine.length;
        var filename = headerLine.substring(startFileNameIndex, endFileNameIndex);
        filename = filename.replace(/['"]+/g, '');
        var link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
      })["catch"](function (error) {
        _this9.$vs.loading.close();

        _this9.$vs.notify({
          title: 'Error',
          text: error.message,
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        });
      });
    },
    downloadInvoicesZip: function downloadInvoicesZip() {
      var _this10 = this;

      if (this.selected.length == 0) return;
      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      var data = {
        ids: []
      };
      this.selected.forEach(function (obj) {
        data.ids.push(obj.id);
      }); // Download invoices in zip archive

      this.$http.post('/api/v1/invoices/download-zip', data, {
        headers: headers,
        responseType: 'blob'
      }).then(function (response) {
        _this10.$vs.loading.close();

        var url = window.URL.createObjectURL(new Blob([response.data]));
        var headerLine = response.headers['content-disposition'];
        var filenameTag = 'filename=';
        var startFileNameIndex = headerLine.indexOf(filenameTag) + filenameTag.length;
        var endFileNameIndex = headerLine.length;
        var filename = headerLine.substring(startFileNameIndex, endFileNameIndex);
        filename = filename.replace(/['"]+/g, '');
        var link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
      })["catch"](function (error) {
        _this10.$vs.loading.close();

        _this10.$vs.notify({
          title: 'Error',
          text: error.message,
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        });
      });
    },
    downloadSummary: function downloadSummary(type) {
      var _this11 = this;

      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      var data = {};

      if (this.filters.date_from != '') {
        data['filter_date_from'] = this.filters.date_from;
      }

      if (this.filters.date_to != '') {
        data['filter_date_to'] = this.filters.date_to;
      }

      if (this.filters.min_amount != '') {
        data['filter_min_amount'] = this.filters.min_amount;
      }

      if (this.filters.max_amount != '') {
        data['filter_max_amount'] = this.filters.max_amount;
      }

      if (this.filters.status.value != '') {
        data['filter_status'] = this.filters.status.value;
      }

      data['filter_type'] = type;

      if (this.filters.uploaded.value != -1) {
        data['filter_uploaded'] = this.filters.uploaded.value;
      }

      if (this.filters.transaction.value != -1) {
        data['filter_transaction'] = this.filters.transaction.value;
      } // Download invoices in zip archive


      this.$http.post('/api/v1/invoices/download-summary', data, {
        headers: headers,
        responseType: 'blob'
      }).then(function (response) {
        _this11.$vs.loading.close();

        var url = window.URL.createObjectURL(new Blob([response.data]));
        var headerLine = response.headers['content-disposition'];
        var filenameTag = 'filename=';
        var startFileNameIndex = headerLine.indexOf(filenameTag) + filenameTag.length;
        var endFileNameIndex = headerLine.length;
        var filename = headerLine.substring(startFileNameIndex, endFileNameIndex);
        filename = filename.replace(/['"]+/g, '');
        var link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
      })["catch"](function (error) {
        _this11.$vs.loading.close();

        _this11.$vs.notify({
          title: 'Error',
          text: error.message,
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        });
      });
    }
  },
  created: function created() {
    this.retrieveObjects();
  },
  mounted: function mounted() {}
});

/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=style&index=0&lang=css&":
/*!************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--7-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=style&index=0&lang=css& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "/*\n.con-input-upload {\n    margin-right: auto !important;\n    margin-left: auto !important;\n    float: none !important;\n}\n*/\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=style&index=0&lang=css&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--7-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=style&index=0&lang=css& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader??ref--7-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./InvoicesTable.vue?vue&type=style&index=0&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=style&index=0&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/Invoices.vue?vue&type=template&id=4127b9e0&":
/*!**********************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/Invoices.vue?vue&type=template&id=4127b9e0& ***!
  \**********************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { attrs: { id: "table-demo" } }, [_c("invoices-table")], 1)
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=template&id=31dac94e&":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=template&id=31dac94e& ***!
  \*********************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "vx-card",
    { attrs: { title: "Invoices", "code-toggler": "" } },
    [
      _c(
        "p",
        { staticClass: "flex items-center mb-4" },
        [
          _c(
            "vs-button",
            {
              staticClass: "mr-2",
              attrs: {
                color: "primary",
                type: "filled",
                "icon-pack": "feather",
                icon: "icon-filter"
              },
              on: {
                click: function($event) {
                  _vm.showFilters = !_vm.showFilters
                }
              }
            },
            [_vm._v("Filters")]
          ),
          _vm._v(" "),
          _c(
            "vs-button",
            {
              staticClass: "mr-2",
              attrs: {
                color: "success",
                type: "filled",
                "icon-pack": "feather",
                icon: "icon-upload-cloud"
              },
              on: {
                click: function($event) {
                  return _vm.showForm("create")
                }
              }
            },
            [_vm._v("Upload invoices")]
          ),
          _vm._v(" "),
          _c(
            "vs-button",
            {
              staticClass: "mr-2",
              attrs: {
                color: "primary",
                type: "filled",
                "icon-pack": "feather",
                icon: "icon-download-cloud",
                disabled: _vm.selected.length == 0
              },
              on: { click: _vm.downloadInvoicesZip }
            },
            [_vm._v("Download invoices (ZIP)")]
          ),
          _vm._v(" "),
          _c(
            "vs-button",
            {
              staticClass: "mr-2",
              attrs: {
                color: "danger",
                type: "filled",
                "icon-pack": "feather",
                icon: "icon-trash"
              },
              on: { click: function($event) {} }
            },
            [_vm._v("Delete")]
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "p",
        { staticClass: "flex items-center mb-4" },
        [
          _c(
            "vs-button",
            {
              staticClass: "mr-2",
              attrs: {
                color: "primary",
                type: "filled",
                "icon-pack": "feather",
                icon: "icon-filter"
              },
              on: {
                click: function($event) {
                  return _vm.downloadSummary("normal")
                }
              }
            },
            [_vm._v("Download invoices summary")]
          ),
          _vm._v(" "),
          _c(
            "vs-button",
            {
              staticClass: "mr-2",
              attrs: {
                color: "warning",
                type: "filled",
                "icon-pack": "feather",
                icon: "icon-download-cloud"
              },
              on: {
                click: function($event) {
                  return _vm.downloadSummary("auto")
                }
              }
            },
            [_vm._v("Download auto invoices summary")]
          )
        ],
        1
      ),
      _vm._v(" "),
      _vm.showFilters
        ? _c("div", { staticClass: "mb-12" }, [
            _c("div", { staticClass: "vx-row mb-2" }, [
              _c(
                "div",
                { staticClass: "vx-col w-1/2" },
                [
                  _c("label", [_vm._v("From")]),
                  _vm._v(" "),
                  _c("vs-input", {
                    staticClass: "w-full",
                    attrs: {
                      type: "date",
                      "icon-pack": "feather",
                      icon: "icon-calendar",
                      "icon-no-border": ""
                    },
                    model: {
                      value: _vm.filters.date_from,
                      callback: function($$v) {
                        _vm.$set(_vm.filters, "date_from", $$v)
                      },
                      expression: "filters.date_from"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "vx-col w-1/2" },
                [
                  _c("label", [_vm._v("To")]),
                  _vm._v(" "),
                  _c("vs-input", {
                    staticClass: "w-full",
                    attrs: {
                      type: "date",
                      "icon-pack": "feather",
                      icon: "icon-calendar",
                      "icon-no-border": ""
                    },
                    model: {
                      value: _vm.filters.date_to,
                      callback: function($$v) {
                        _vm.$set(_vm.filters, "date_to", $$v)
                      },
                      expression: "filters.date_to"
                    }
                  })
                ],
                1
              )
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "vx-row mb-2" }, [
              _c(
                "div",
                { staticClass: "vx-col w-1/2" },
                [
                  _c("label", [_vm._v("Min")]),
                  _vm._v(" "),
                  _c("vs-input", {
                    staticClass: "w-full",
                    attrs: {
                      type: "number",
                      min: "0",
                      step: "0.01",
                      "icon-pack": "feather",
                      icon: "icon-dollar-sign",
                      "icon-no-border": ""
                    },
                    model: {
                      value: _vm.filters.min_amount,
                      callback: function($$v) {
                        _vm.$set(_vm.filters, "min_amount", $$v)
                      },
                      expression: "filters.min_amount"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "vx-col w-1/2" },
                [
                  _c("label", [_vm._v("Max")]),
                  _vm._v(" "),
                  _c("vs-input", {
                    staticClass: "w-full",
                    attrs: {
                      type: "number",
                      min: "0",
                      step: "0.01",
                      "icon-pack": "feather",
                      icon: "icon-dollar-sign",
                      "icon-no-border": ""
                    },
                    model: {
                      value: _vm.filters.max_amount,
                      callback: function($$v) {
                        _vm.$set(_vm.filters, "max_amount", $$v)
                      },
                      expression: "filters.max_amount"
                    }
                  })
                ],
                1
              )
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "vx-row mb-2" }, [
              _c(
                "div",
                { staticClass: "vx-col w-1/2" },
                [
                  _c("label", [_vm._v("Status")]),
                  _vm._v(" "),
                  _c("v-select", {
                    attrs: { options: _vm.invoiceStatusOptions },
                    model: {
                      value: _vm.filters.status,
                      callback: function($$v) {
                        _vm.$set(_vm.filters, "status", $$v)
                      },
                      expression: "filters.status"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "vx-col w-1/2" },
                [
                  _c("label", [_vm._v("Type")]),
                  _vm._v(" "),
                  _c("v-select", {
                    attrs: { options: _vm.invoiceTypeOptions },
                    model: {
                      value: _vm.filters.type,
                      callback: function($$v) {
                        _vm.$set(_vm.filters, "type", $$v)
                      },
                      expression: "filters.type"
                    }
                  })
                ],
                1
              )
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "vx-row mb-2" }, [
              _c(
                "div",
                { staticClass: "vx-col w-1/2" },
                [
                  _c("label", [_vm._v("Uploaded")]),
                  _vm._v(" "),
                  _c("v-select", {
                    attrs: { options: _vm.yesNoOptions },
                    model: {
                      value: _vm.filters.uploaded,
                      callback: function($$v) {
                        _vm.$set(_vm.filters, "uploaded", $$v)
                      },
                      expression: "filters.uploaded"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "vx-col w-1/2" },
                [
                  _c("label", [_vm._v("Transaction")]),
                  _vm._v(" "),
                  _c("v-select", {
                    attrs: { options: _vm.yesNoOptions },
                    model: {
                      value: _vm.filters.transaction,
                      callback: function($$v) {
                        _vm.$set(_vm.filters, "transaction", $$v)
                      },
                      expression: "filters.transaction"
                    }
                  })
                ],
                1
              )
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "vx-row mb-6" }, [
              _c("div", { staticClass: "vx-col w-full" })
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "vx-row" }, [
              _c(
                "div",
                { staticClass: "vx-col w-full" },
                [
                  _c(
                    "vs-button",
                    {
                      staticClass: "mr-3 mb-2",
                      on: { click: _vm.applyFilters }
                    },
                    [_vm._v("Apply")]
                  ),
                  _vm._v(" "),
                  _c(
                    "vs-button",
                    {
                      staticClass: "mb-2",
                      attrs: { color: "warning", type: "border" },
                      on: { click: _vm.resetFilters }
                    },
                    [_vm._v("Reset")]
                  )
                ],
                1
              )
            ])
          ])
        : _vm._e(),
      _vm._v(" "),
      _c(
        "vs-dropdown",
        { staticClass: "cursor-pointer", attrs: { "vs-trigger-click": "" } },
        [
          _c(
            "div",
            {
              staticClass:
                "p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium"
            },
            [
              _c("span", { staticClass: "mr-2" }, [
                _vm._v(
                  _vm._s(_vm.currentPage * _vm.maxItems - (_vm.maxItems - 1)) +
                    " - " +
                    _vm._s(
                      _vm.objects.length - _vm.currentPage * _vm.maxItems > 0
                        ? _vm.currentPage * _vm.maxItems
                        : _vm.objects.length
                    ) +
                    " of " +
                    _vm._s(_vm.objects.length)
                )
              ]),
              _vm._v(
                "\n            (total: € " +
                  _vm._s(_vm.total_amount) +
                  ") \n            "
              ),
              _c("feather-icon", {
                attrs: { icon: "ChevronDownIcon", svgClasses: "h-4 w-4" }
              })
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "vs-dropdown-menu",
            [
              _c(
                "vs-dropdown-item",
                {
                  on: {
                    click: function($event) {
                      _vm.maxItems = 20
                    }
                  }
                },
                [_c("span", [_vm._v("20")])]
              ),
              _vm._v(" "),
              _c(
                "vs-dropdown-item",
                {
                  on: {
                    click: function($event) {
                      _vm.maxItems = 50
                    }
                  }
                },
                [_c("span", [_vm._v("50")])]
              ),
              _vm._v(" "),
              _c(
                "vs-dropdown-item",
                {
                  on: {
                    click: function($event) {
                      _vm.maxItems = 100
                    }
                  }
                },
                [_c("span", [_vm._v("100")])]
              ),
              _vm._v(" "),
              _c(
                "vs-dropdown-item",
                {
                  on: {
                    click: function($event) {
                      _vm.maxItems = 150
                    }
                  }
                },
                [_c("span", [_vm._v("150")])]
              )
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "vs-table",
        {
          attrs: {
            multiple: "",
            "max-items": _vm.maxItems,
            pagination: "",
            search: "",
            data: _vm.objects,
            currentPage: _vm.currentPage
          },
          on: { "change-page": _vm.onPageChanged },
          scopedSlots: _vm._u([
            {
              key: "default",
              fn: function(ref) {
                var data = ref.data
                return _vm._l(data, function(tr, indextr) {
                  return _c(
                    "vs-tr",
                    { key: indextr, attrs: { data: tr } },
                    [
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].invoice_date } },
                        [
                          _vm._v(
                            "\n                    " +
                              _vm._s(data[indextr].invoice_date) +
                              "\n                "
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c("vs-td", { attrs: { data: data[indextr].uploaded } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].uploaded ? "Yes" : "No") +
                            "\n                "
                        )
                      ]),
                      _vm._v(" "),
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].transaction } },
                        [
                          _vm._v(
                            "\n                    " +
                              _vm._s(data[indextr].transaction ? "Yes" : "No") +
                              "\n                "
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].transaction_id } },
                        [
                          _vm._v(
                            "\n                    " +
                              _vm._s(data[indextr].transaction_id) +
                              "\n                "
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c("vs-td", { attrs: { data: data[indextr].number } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].number) +
                            "\n                "
                        )
                      ]),
                      _vm._v(" "),
                      _c("vs-td", { attrs: { data: data[indextr].amount } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].amount) +
                            "\n                "
                        )
                      ]),
                      _vm._v(" "),
                      _c("vs-td", { attrs: { data: data[indextr].currency } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].currency) +
                            "\n                "
                        )
                      ]),
                      _vm._v(" "),
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].status } },
                        [
                          _c(
                            "vs-chip",
                            {
                              attrs: {
                                color: _vm.statusColor[data[indextr].status]
                              }
                            },
                            [_vm._v(_vm._s(data[indextr].status))]
                          )
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "vs-td",
                        [
                          _c(
                            "vs-chip",
                            {
                              attrs: {
                                color: _vm.invoiceTypeColor[data[indextr].type]
                              }
                            },
                            [_vm._v(_vm._s(data[indextr].type))]
                          )
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c("vs-td", [
                        _c(
                          "div",
                          { staticClass: "flex items-center" },
                          [
                            _c("vs-button", {
                              staticClass: "mr-2",
                              attrs: {
                                color: "primary",
                                type: "filled",
                                "icon-pack": "feather",
                                icon: "icon-download"
                              },
                              on: {
                                click: function($event) {
                                  return _vm.downloadInvoice(data[indextr].id)
                                }
                              }
                            }),
                            _vm._v(" "),
                            _c("vs-button", {
                              staticClass: "mr-2",
                              attrs: {
                                color: "warning",
                                type: "filled",
                                "icon-pack": "feather",
                                icon: "icon-bookmark"
                              },
                              on: {
                                click: function($event) {
                                  return _vm.setAutoInvoice(data[indextr].id)
                                }
                              }
                            }),
                            _vm._v(" "),
                            _c("vs-button", {
                              staticClass: "mr-2",
                              attrs: {
                                color: "danger",
                                type: "filled",
                                "icon-pack": "feather",
                                icon: "icon-trash"
                              },
                              on: {
                                click: function($event) {
                                  return _vm.deleteObject(data[indextr].id)
                                }
                              }
                            })
                          ],
                          1
                        )
                      ])
                    ],
                    1
                  )
                })
              }
            }
          ]),
          model: {
            value: _vm.selected,
            callback: function($$v) {
              _vm.selected = $$v
            },
            expression: "selected"
          }
        },
        [
          _c(
            "template",
            { slot: "thead" },
            [
              _c("vs-th", { attrs: { "sort-key": "invoice_date" } }, [
                _vm._v("Date")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "uploaded" } }, [
                _vm._v("Uploaded")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "transaction" } }, [
                _vm._v("Transaction")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "transaction_id" } }, [
                _vm._v("Transaction ID")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "number" } }, [
                _vm._v("Number")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "amount" } }, [
                _vm._v("Amount")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "currency" } }, [
                _vm._v("Currency")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "status" } }, [
                _vm._v("Status")
              ]),
              _vm._v(" "),
              _c("vs-th", [_vm._v("Type")]),
              _vm._v(" "),
              _c("vs-th", [_vm._v("Actions")])
            ],
            1
          )
        ],
        2
      ),
      _vm._v(" "),
      _c(
        "vs-popup",
        {
          attrs: { title: "Upload Invoices", active: _vm.showPopup["create"] },
          on: {
            "update:active": function($event) {
              return _vm.$set(_vm.showPopup, "create", $event)
            }
          }
        },
        [
          _c(
            "div",
            {
              staticClass: "vx-row mb-6 flex items-center",
              attrs: { "vs-justify": "center" }
            },
            [
              _c(
                "vs-row",
                { attrs: { "vs-justify": "center" } },
                [
                  _c(
                    "vs-col",
                    {
                      attrs: {
                        type: "flex",
                        "vs-justify": "center",
                        "vs-align": "center",
                        "vs-w": "6"
                      }
                    },
                    [
                      _c("vs-card", { staticClass: "cardx" }, [
                        _c(
                          "div",
                          { attrs: { slot: "header" }, slot: "header" },
                          [
                            _c(
                              "h3",
                              { staticClass: "mr-auto ml-auto text-center" },
                              [
                                _c("span", { staticClass: "mr-auto ml-auto" }, [
                                  _vm._v(
                                    _vm._s(_vm.filesToUpload.length) +
                                      " files selected"
                                  )
                                ])
                              ]
                            )
                          ]
                        ),
                        _vm._v(" "),
                        _c(
                          "div",
                          {
                            staticClass: "mb-3",
                            attrs: { slot: "media" },
                            slot: "media"
                          },
                          [
                            _c("img", {
                              attrs: {
                                src:
                                  "https://image.flaticon.com/icons/png/512/337/337946.png"
                              }
                            })
                          ]
                        ),
                        _vm._v(" "),
                        _c(
                          "div",
                          { attrs: { slot: "footer" }, slot: "footer" },
                          [
                            _c(
                              "vs-row",
                              { attrs: { "vs-justify": "center" } },
                              [
                                _c("vs-button", {
                                  staticClass: "mr-2",
                                  attrs: {
                                    color: "primary",
                                    "icon-pack": "feather",
                                    icon: "icon-file-plus"
                                  },
                                  on: { click: _vm.showFilePicker }
                                }),
                                _vm._v(" "),
                                _c("vs-button", {
                                  staticClass: "mr-2",
                                  attrs: {
                                    color: "success",
                                    "icon-pack": "feather",
                                    icon: "icon-upload-cloud",
                                    disabled: !_vm.isValidUpload()
                                  },
                                  on: { click: _vm.uploadFiles }
                                }),
                                _vm._v(" "),
                                _c("vs-button", {
                                  staticClass: "mr-2",
                                  attrs: {
                                    color: "danger",
                                    "icon-pack": "feather",
                                    icon: "icon-trash"
                                  },
                                  on: { click: _vm.clearFilesToUpload }
                                })
                              ],
                              1
                            )
                          ],
                          1
                        )
                      ])
                    ],
                    1
                  )
                ],
                1
              )
            ],
            1
          ),
          _vm._v(" "),
          _c("vs-row", { staticClass: "vx-row mb-6 flex items-center" }, [
            _c("div", { staticClass: "vx-col w-full flex items-center" }, [
              _c("span", { staticClass: "ml-auto mr-auto" }, [
                _vm._v("Upload: " + _vm._s(_vm.uploadProgress) + "%")
              ])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col w-full" },
              [
                _c("vs-progress", {
                  attrs: {
                    height: 12,
                    percent: _vm.uploadProgress,
                    color: "success"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("input", {
            ref: "filesUpload",
            staticClass: "hidden",
            attrs: { type: "file", multiple: "" },
            on: { change: _vm.onFilesUploadChange }
          })
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "vs-popup",
        {
          attrs: { title: "Edit Invoice", active: _vm.showPopup["edit"] },
          on: {
            "update:active": function($event) {
              return _vm.$set(_vm.showPopup, "edit", $event)
            }
          }
        },
        [
          _c(
            "div",
            { staticClass: "vx-row mb-6 flex items-center" },
            [
              _c(
                "vs-button",
                { staticClass: "mr-auto ml-auto", attrs: { color: "primary" } },
                [_vm._v(_vm._s(_vm.newObject["edit"].name))]
              )
            ],
            1
          ),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Name")])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full" },
              [
                _c("vs-input", {
                  staticClass: "w-full",
                  attrs: {
                    "icon-pack": "feather",
                    icon: "icon-tag",
                    "icon-no-border": ""
                  },
                  model: {
                    value: _vm.newObject["edit"].name,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["edit"], "name", $$v)
                    },
                    expression: "newObject['edit'].name"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "vx-row mb-6" },
            [
              _c(
                "vs-button",
                {
                  staticClass: "mt-12 mr-auto ml-auto",
                  attrs: {
                    color: "primary",
                    type: "filled",
                    disabled: !_vm.isValidForm("edit")
                  },
                  on: { click: _vm.updateObject }
                },
                [_vm._v("Update")]
              )
            ],
            1
          )
        ]
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/views/Invoices.vue":
/*!*********************************************!*\
  !*** ./resources/js/src/views/Invoices.vue ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Invoices_vue_vue_type_template_id_4127b9e0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Invoices.vue?vue&type=template&id=4127b9e0& */ "./resources/js/src/views/Invoices.vue?vue&type=template&id=4127b9e0&");
/* harmony import */ var _Invoices_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Invoices.vue?vue&type=script&lang=js& */ "./resources/js/src/views/Invoices.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Invoices_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Invoices_vue_vue_type_template_id_4127b9e0___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Invoices_vue_vue_type_template_id_4127b9e0___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/Invoices.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/Invoices.vue?vue&type=script&lang=js&":
/*!**********************************************************************!*\
  !*** ./resources/js/src/views/Invoices.vue?vue&type=script&lang=js& ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Invoices_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./Invoices.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/Invoices.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Invoices_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/Invoices.vue?vue&type=template&id=4127b9e0&":
/*!****************************************************************************!*\
  !*** ./resources/js/src/views/Invoices.vue?vue&type=template&id=4127b9e0& ***!
  \****************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Invoices_vue_vue_type_template_id_4127b9e0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./Invoices.vue?vue&type=template&id=4127b9e0& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/Invoices.vue?vue&type=template&id=4127b9e0&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Invoices_vue_vue_type_template_id_4127b9e0___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Invoices_vue_vue_type_template_id_4127b9e0___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/InvoicesTable.vue":
/*!********************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/InvoicesTable.vue ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _InvoicesTable_vue_vue_type_template_id_31dac94e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./InvoicesTable.vue?vue&type=template&id=31dac94e& */ "./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=template&id=31dac94e&");
/* harmony import */ var _InvoicesTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./InvoicesTable.vue?vue&type=script&lang=js& */ "./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _InvoicesTable_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./InvoicesTable.vue?vue&type=style&index=0&lang=css& */ "./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _InvoicesTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _InvoicesTable_vue_vue_type_template_id_31dac94e___WEBPACK_IMPORTED_MODULE_0__["render"],
  _InvoicesTable_vue_vue_type_template_id_31dac94e___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/ui-elements/table/InvoicesTable.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_InvoicesTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./InvoicesTable.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_InvoicesTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=style&index=0&lang=css&":
/*!*****************************************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=style&index=0&lang=css& ***!
  \*****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_InvoicesTable_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader??ref--7-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./InvoicesTable.vue?vue&type=style&index=0&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_InvoicesTable_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_InvoicesTable_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_InvoicesTable_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_InvoicesTable_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_InvoicesTable_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=template&id=31dac94e&":
/*!***************************************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=template&id=31dac94e& ***!
  \***************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_InvoicesTable_vue_vue_type_template_id_31dac94e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./InvoicesTable.vue?vue&type=template&id=31dac94e& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/InvoicesTable.vue?vue&type=template&id=31dac94e&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_InvoicesTable_vue_vue_type_template_id_31dac94e___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_InvoicesTable_vue_vue_type_template_id_31dac94e___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);