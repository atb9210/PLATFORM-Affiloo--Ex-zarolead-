(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[92],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/FormStats.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/FormStats.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ui_elements_table_FormStatsTable_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ui-elements/table/FormStatsTable.vue */ "./resources/js/src/views/ui-elements/table/FormStatsTable.vue");
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
    FormStatsTable: _ui_elements_table_FormStatsTable_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  data: function data() {
    return {
      form_id: 0
    };
  },
  methods: {},
  created: function created() {
    this.form_id = parseInt(this.$route.params.form_id);
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/FormStatsTable.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ui-elements/table/FormStatsTable.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************/
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

/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    'v-select': vue_select__WEBPACK_IMPORTED_MODULE_0___default.a
  },
  props: {
    form_id: Number
  },
  data: function data() {
    return {
      objects: [],
      selected: [],
      deleted: [],
      maxItems: 20,
      isEditingCost: false,
      editingCostValue: 0,
      editingObject: -1,
      showPopup: {
        'create': false,
        'edit': false
      },
      newObject: {
        'create': {
          name: '',
          campaign: '',
          network: '',
          product: '',
          quantity: '',
          price: 0,
          commission: 0,
          sub_commission: 0,
          traffic_source: '',
          fb_pixel: '',
          sub_id: '',
          api_call_url: '',
          // Worldfilia
          source_id: '',
          // Amsped
          product_reference: '',
          // Affiliation Park
          offer_id: ''
        },
        'edit': {
          name: '',
          campaign: '',
          network: '',
          product: '',
          quantity: '',
          price: 0,
          commission: 0,
          sub_commission: 0,
          traffic_source: '',
          fb_pixel: '',
          sub_id: '',
          api_call_url: '',
          // Worldfilia
          source_id: '',
          // Amsped
          product_reference: '',
          // Affiliation Park
          offer_id: ''
        }
      },
      campaigns: [],
      networks: [],
      products: [],
      traffic_sources: []
    };
  },
  watch: {
    form_id: function form_id(newVal, oldVal) {
      this.retrieveObjects();
    }
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
      }; // Retrieve forms

      this.$http.get('/api/v1/forms/' + this.form_id + '/stats', {
        headers: headers
      }).then(function (response) {
        _this.objects = response.data.data;

        _this.calculateStats();

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
    calculateStats: function calculateStats() {
      for (var i = 0; i < this.objects.length; i++) {
        var obj = this.objects[i];
        var profit = obj.total_commission - obj.total_cost;
        var roi = parseFloat(obj.total_commission > 0 && obj.total_cost == 0 ? 9999 : profit / obj.total_cost * 100);
        var cr = 0.0;
        if (obj.leads == 0) cr = 0.0;else if (obj.visits == 0) cr = 100.0;else cr = parseFloat(obj.leads / obj.visits * 100.0);
        var cpv = parseFloat(obj.visits == 0 ? obj.total_cost : obj.total_cost / obj.visits);
        var epv = parseFloat(obj.visits == 0 ? obj.total_commission : obj.total_commission / obj.visits);
        var cpl = parseFloat(obj.leads == 0 ? obj.total_cost : obj.total_cost / obj.leads); // Format calculated stats

        profit = profit.toFixed(2);
        roi = roi.toFixed(2);
        cr = cr.toFixed(2);
        cpv = cpv.toFixed(2);
        epv = epv.toFixed(2);
        cpl = cpl.toFixed(2);
        this.objects[i].profit = profit;
        this.objects[i].roi = roi;
        this.objects[i].cr = cr;
        this.objects[i].cpv = cpv;
        this.objects[i].epv = epv;
        this.objects[i].cpl = cpl;
      }
    },
    onPageChanged: function onPageChanged(val) {
      this.currentPage = val;
    },
    showEditCostInput: function showEditCostInput(id) {
      var index = this.getObjectIndexById(id);
      this.isEditingCost = true;
      this.editingCostValue = this.objects[index].total_cost;
      this.editingObject = id;
    },
    editCost: function editCost() {
      var _this2 = this;

      this.$vs.loading();
      var index = this.getObjectIndexById(this.editingObject);
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      var data = {
        form_stats_id: this.editingObject,
        total_cost: this.editingCostValue
      };
      this.$http.put('/api/v1/forms/' + this.objects[index].form.id + '/stats', data, {
        headers: headers
      }).then(function (response) {
        _this2.objects[index].total_cost = _this2.editingCostValue;
        _this2.isEditingCost = false;

        _this2.calculateStats();

        _this2.$vs.loading.close();

        _this2.$vs.notify({
          title: 'Success',
          text: 'Total cost updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        });
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
    },
    deleteSelected: function deleteSelected() {
      var _this3 = this;

      if (this.selected.length == 0) return;
      this.$vs.loading();
      this.deleted = [];
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      this.selected.forEach(function (form) {
        _this3.$http["delete"]('/api/v1/forms/' + form.id, {
          headers: headers
        }).then(function (response) {
          _this3.deleted.push(form.id);

          if (_this3.deleted.length == _this3.selected.length) {
            _this3.$vs.loading.close();

            _this3.selected = [];

            _this3.$vs.notify({
              title: 'Success',
              text: _this3.deleted.length + ' Form(s) deleted',
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'success'
            });

            _this3.selected = [];
            _this3.deleted = [];

            _this3.retrieveObjects();
          }
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
      });
    },
    showForm: function showForm(type) {
      this.showPopup[type] = true;
    },
    clearForm: function clearForm(type) {
      this.newObject[type] = {
        name: '',
        campaign: '',
        network: '',
        product: '',
        quantity: '',
        price: 0,
        commission: 0,
        sub_commission: 0,
        traffic_source: '',
        fb_pixel: '',
        sub_id: '',
        api_call_url: '',
        // Worldfilia
        source_id: '',
        // Amsped
        product_reference: '',
        // Affiliation Park
        offer_id: ''
      };
    },
    isValidForm: function isValidForm(type) {
      var obj = this.newObject[type];
      return obj.name.length > 0 && obj.campaign != '' && obj.campaign.id >= 1 && obj.network != '' && obj.network.id >= 1 && obj.product != '' && obj.product.id >= 1 && obj.quantity.length > 0 && obj.traffic_source != '' && obj.traffic_source.id >= 1;
    },
    createObject: function createObject() {
      var _this4 = this;

      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      var data = {
        name: this.newObject['create'].name,
        campaign_id: this.newObject['create'].campaign.id,
        network_id: this.newObject['create'].network.id,
        product_id: this.newObject['create'].product.id,
        quantity: this.newObject['create'].quantity,
        price: this.newObject['create'].price,
        commission: this.newObject['create'].commission,
        sub_commission: this.newObject['create'].sub_commission,
        traffic_source_id: this.newObject['create'].traffic_source.id,
        network_params: {}
      };

      if (this.newObject['create'].network.label == 'Worldfilia') {
        data.network_params = {
          fb_pixel: this.newObject['create'].fb_pixel,
          api_call_url: this.newObject['create'].api_call_url,
          source_id: this.newObject['create'].source_id
        };
      } else if (this.newObject['create'].network.label == 'Amsped') {
        data.network_params = {
          fb_pixel: this.newObject['create'].fb_pixel,
          api_call_url: this.newObject['create'].api_call_url,
          sub_id: this.newObject['create'].sub_id,
          product_reference: this.newObject['create'].product_reference
        };
      } else if (this.newObject['create'].network.label == 'Affiliation Park') {
        data.network_params = {
          fb_pixel: this.newObject['create'].fb_pixel,
          api_call_url: this.newObject['create'].api_call_url,
          sub_id: this.newObject['create'].sub_id,
          offer_id: this.newObject['create'].offer_id
        };
      }

      data.network_params = JSON.stringify(data.network_params); // Retrieve forms

      this.$http.post('/api/v1/forms', data, {
        headers: headers
      }).then(function (response) {
        _this4.$vs.loading.close();

        _this4.$vs.notify({
          title: 'Success',
          text: 'Form created',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        });

        _this4.clearForm('create');

        _this4.showPopup['create'] = false;

        _this4.retrieveObjects();
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
    },
    editForm: function editForm(id) {
      var obj = this.objects[this.getObjectIndexById(id)];
      this.newObject['edit'] = {
        name: obj.name,
        campaign: {
          id: obj.campaign.id,
          label: obj.campaign.name
        },
        network: {
          id: obj.network.id,
          label: obj.network.name
        },
        product: {
          id: obj.product.id,
          label: obj.product.name
        },
        quantity: obj.quantity,
        price: obj.price,
        commission: obj.commission,
        sub_commission: obj.sub_commission,
        traffic_source: {
          id: obj.traffic_source.id,
          label: obj.traffic_source.name
        },
        fb_pixel: '',
        api_call_url: '',
        sub_id: '',
        // Worldfilia
        source_id: '',
        // Amsped
        product_reference: '',
        // Affiliation Park
        offer_id: ''
      };

      if (obj.hasOwnProperty('network_params')) {
        if (obj.network_params.hasOwnProperty('fb_pixel')) {
          this.newObject['edit'].fb_pixel = obj.network_params.fb_pixel;
        }

        if (obj.network_params.hasOwnProperty('api_call_url')) {
          this.newObject['edit'].api_call_url = obj.network_params.api_call_url;
        }

        if (obj.network.name == 'Worldfilia') {
          if (obj.network_params.hasOwnProperty('source_id')) {
            this.newObject['edit'].source_id = obj.network_params.source_id;
          }
        } else if (obj.network.name == 'Amsped') {
          if (obj.network_params.hasOwnProperty('sub_id')) {
            this.newObject['edit'].sub_id = obj.network_params.sub_id;
          }

          if (obj.network_params.hasOwnProperty('product_reference')) {
            this.newObject['edit'].product_reference = obj.network_params.product_reference;
          }
        } else if (obj.network.name == 'Affiliation Park') {
          if (obj.network_params.hasOwnProperty('sub_id')) {
            this.newObject['edit'].sub_id = obj.network_params.sub_id;
          }

          if (obj.network_params.hasOwnProperty('offer_id')) {
            this.newObject['edit'].offer_id = obj.network_params.offer_id;
          }
        }
      }

      this.editingObject = obj.id;
      this.showForm('edit');
    },
    updateObject: function updateObject() {
      var _this5 = this;

      if (this.editingObject <= 0) return;
      var id = this.editingObject;
      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      var data = {
        name: this.newObject['edit'].name,
        campaign_id: this.newObject['edit'].campaign.id,
        network_id: this.newObject['edit'].network.id,
        product_id: this.newObject['edit'].product.id,
        quantity: this.newObject['edit'].quantity,
        price: this.newObject['edit'].price,
        commission: this.newObject['edit'].commission,
        sub_commission: this.newObject['edit'].sub_commission,
        traffic_source_id: this.newObject['edit'].traffic_source.id
      };

      if (this.newObject['edit'].network.label == 'Worldfilia') {
        data.network_params = {
          fb_pixel: this.newObject['edit'].fb_pixel,
          api_call_url: this.newObject['edit'].api_call_url,
          source_id: this.newObject['edit'].source_id
        };
      } else if (this.newObject['edit'].network.label == 'Amsped') {
        data.network_params = {
          fb_pixel: this.newObject['edit'].fb_pixel,
          api_call_url: this.newObject['edit'].api_call_url,
          sub_id: this.newObject['edit'].sub_id,
          product_reference: this.newObject['edit'].product_reference
        };
      } else if (this.newObject['edit'].network.label == 'Affiliation Park') {
        data.network_params = {
          fb_pixel: this.newObject['edit'].fb_pixel,
          api_call_url: this.newObject['edit'].api_call_url,
          sub_id: this.newObject['edit'].sub_id,
          offer_id: this.newObject['edit'].offer_id
        };
      }

      data.network_params = JSON.stringify(data.network_params);
      this.$http.put('/api/v1/forms/' + id, data, {
        headers: headers
      }).then(function (response) {
        _this5.$vs.loading.close();

        _this5.$vs.notify({
          title: 'Success',
          text: 'Form updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        });

        _this5.retrieveObjects();
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
      this.showPopup['edit'] = false;
      this.editingObject = -1;
    },
    deleteObject: function deleteObject(id) {
      var _this6 = this;

      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      this.$http["delete"]('/api/v1/forms/' + id, {
        headers: headers
      }).then(function (response) {
        _this6.$vs.loading.close();

        _this6.$vs.notify({
          title: 'Success',
          text: 'Form deleted',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        });

        _this6.retrieveObjects();
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
    }
  },
  created: function created() {
    this.retrieveObjects();
  },
  mounted: function mounted() {}
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/FormStats.vue?vue&type=template&id=67f52101&":
/*!***********************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/FormStats.vue?vue&type=template&id=67f52101& ***!
  \***********************************************************************************************************************************************************************************************************/
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
    "div",
    { attrs: { id: "table-demo" } },
    [_c("form-stats-table", { attrs: { form_id: _vm.form_id } })],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/FormStatsTable.vue?vue&type=template&id=599a5afd&":
/*!**********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ui-elements/table/FormStatsTable.vue?vue&type=template&id=599a5afd& ***!
  \**********************************************************************************************************************************************************************************************************************************/
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
    { attrs: { title: "Form Stats", "code-toggler": "" } },
    [
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
              _vm._v(" "),
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
                      _c("vs-td", { attrs: { data: data[indextr].day } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].day) +
                            "\n                "
                        )
                      ]),
                      _vm._v(" "),
                      _c("vs-td", { attrs: { data: data[indextr].visits } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].visits) +
                            "\n                "
                        )
                      ]),
                      _vm._v(" "),
                      _c("vs-td", { attrs: { data: data[indextr].leads } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].leads) +
                            "\n                "
                        )
                      ]),
                      _vm._v(" "),
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].total_cost } },
                        [
                          _vm.isEditingCost &&
                          _vm.editingObject == data[indextr].id
                            ? _c("vs-input", {
                                staticClass: "is-label-placeholder",
                                attrs: {
                                  type: "number",
                                  step: "0.01",
                                  min: "0",
                                  "icon-pack": "feather",
                                  icon: "icon-dollar-sign",
                                  placeholder: "Amount"
                                },
                                on: {
                                  keyup: function($event) {
                                    if (
                                      !$event.type.indexOf("key") &&
                                      _vm._k(
                                        $event.keyCode,
                                        "enter",
                                        13,
                                        $event.key,
                                        "Enter"
                                      )
                                    ) {
                                      return null
                                    }
                                    return _vm.editCost($event)
                                  }
                                },
                                model: {
                                  value: _vm.editingCostValue,
                                  callback: function($$v) {
                                    _vm.editingCostValue = $$v
                                  },
                                  expression: "editingCostValue"
                                }
                              })
                            : _c("span", [
                                _vm._v(
                                  "€ " + _vm._s(data[indextr].total_cost) + " "
                                ),
                                _c(
                                  "span",
                                  {
                                    staticClass: "underline",
                                    on: {
                                      click: function($event) {
                                        return _vm.showEditCostInput(
                                          data[indextr].id
                                        )
                                      }
                                    }
                                  },
                                  [_vm._v("(edit)")]
                                )
                              ])
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].total_commission } },
                        [
                          _vm._v(
                            "\n                    € " +
                              _vm._s(data[indextr].total_commission) +
                              "\n                "
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].profit } },
                        [
                          _c(
                            "vs-chip",
                            {
                              attrs: {
                                transparent: "",
                                color:
                                  data[indextr].profit > 0
                                    ? "success"
                                    : "danger"
                              }
                            },
                            [_vm._v("€ " + _vm._s(data[indextr].profit))]
                          )
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].roi } },
                        [
                          _c(
                            "vs-chip",
                            {
                              attrs: {
                                transparent: "",
                                color:
                                  data[indextr].roi > 0 ? "success" : "danger"
                              }
                            },
                            [_vm._v(_vm._s(data[indextr].roi) + " %")]
                          )
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c("vs-td", { attrs: { data: data[indextr].cr } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].cr) +
                            " %\n                "
                        )
                      ]),
                      _vm._v(" "),
                      _c("vs-td", { attrs: { data: data[indextr].cpv } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].cpv) +
                            " %\n                "
                        )
                      ]),
                      _vm._v(" "),
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].epv } },
                        [
                          _c(
                            "vs-chip",
                            {
                              attrs: {
                                transparent: "",
                                color:
                                  data[indextr].epv > 0 ? "success" : "danger"
                              }
                            },
                            [_vm._v(_vm._s(data[indextr].epv) + " %")]
                          )
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c("vs-td", { attrs: { data: data[indextr].cpl } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].cpl) +
                            " %\n                "
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
              _c("vs-th", { attrs: { "sort-key": "day" } }, [_vm._v("Day")]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "visits" } }, [
                _vm._v("Visits")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "leads" } }, [
                _vm._v("Leads")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "total_cost" } }, [
                _vm._v("Cost")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "total_commission" } }, [
                _vm._v("Commission")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "profit" } }, [
                _vm._v("Profit")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "roi" } }, [_vm._v("ROI")]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "cr" } }, [_vm._v("CR")]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "cpv" } }, [_vm._v("CPV")]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "epv" } }, [_vm._v("EPV")]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "cpl" } }, [_vm._v("CPL")])
            ],
            1
          )
        ],
        2
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/views/FormStats.vue":
/*!**********************************************!*\
  !*** ./resources/js/src/views/FormStats.vue ***!
  \**********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _FormStats_vue_vue_type_template_id_67f52101___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FormStats.vue?vue&type=template&id=67f52101& */ "./resources/js/src/views/FormStats.vue?vue&type=template&id=67f52101&");
/* harmony import */ var _FormStats_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FormStats.vue?vue&type=script&lang=js& */ "./resources/js/src/views/FormStats.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _FormStats_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _FormStats_vue_vue_type_template_id_67f52101___WEBPACK_IMPORTED_MODULE_0__["render"],
  _FormStats_vue_vue_type_template_id_67f52101___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/FormStats.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/FormStats.vue?vue&type=script&lang=js&":
/*!***********************************************************************!*\
  !*** ./resources/js/src/views/FormStats.vue?vue&type=script&lang=js& ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FormStats_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./FormStats.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/FormStats.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FormStats_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/FormStats.vue?vue&type=template&id=67f52101&":
/*!*****************************************************************************!*\
  !*** ./resources/js/src/views/FormStats.vue?vue&type=template&id=67f52101& ***!
  \*****************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormStats_vue_vue_type_template_id_67f52101___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./FormStats.vue?vue&type=template&id=67f52101& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/FormStats.vue?vue&type=template&id=67f52101&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormStats_vue_vue_type_template_id_67f52101___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormStats_vue_vue_type_template_id_67f52101___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/FormStatsTable.vue":
/*!*********************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/FormStatsTable.vue ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _FormStatsTable_vue_vue_type_template_id_599a5afd___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FormStatsTable.vue?vue&type=template&id=599a5afd& */ "./resources/js/src/views/ui-elements/table/FormStatsTable.vue?vue&type=template&id=599a5afd&");
/* harmony import */ var _FormStatsTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FormStatsTable.vue?vue&type=script&lang=js& */ "./resources/js/src/views/ui-elements/table/FormStatsTable.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _FormStatsTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _FormStatsTable_vue_vue_type_template_id_599a5afd___WEBPACK_IMPORTED_MODULE_0__["render"],
  _FormStatsTable_vue_vue_type_template_id_599a5afd___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/ui-elements/table/FormStatsTable.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/FormStatsTable.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/FormStatsTable.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FormStatsTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./FormStatsTable.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/FormStatsTable.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FormStatsTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/FormStatsTable.vue?vue&type=template&id=599a5afd&":
/*!****************************************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/FormStatsTable.vue?vue&type=template&id=599a5afd& ***!
  \****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormStatsTable_vue_vue_type_template_id_599a5afd___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./FormStatsTable.vue?vue&type=template&id=599a5afd& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/FormStatsTable.vue?vue&type=template&id=599a5afd&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormStatsTable_vue_vue_type_template_id_599a5afd___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormStatsTable_vue_vue_type_template_id_599a5afd___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);