(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[98],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ReportProducts.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ReportProducts.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ui_elements_table_ReportProductsTable_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ui-elements/table/ReportProductsTable.vue */ "./resources/js/src/views/ui-elements/table/ReportProductsTable.vue");
//
//
//
//
//
//
//
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
    ReportProductsTable: _ui_elements_table_ReportProductsTable_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  data: function data() {
    return {};
  },
  methods: {},
  created: function created() {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/ReportProductsTable.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ui-elements/table/ReportProductsTable.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************************/
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

/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    'v-select': vue_select__WEBPACK_IMPORTED_MODULE_0___default.a
  },
  data: function data() {
    return {
      objects: [],
      selected: [],
      deleted: [],
      maxItems: 20,
      filters: {
        date_from: '',
        date_to: '',
        product: ''
      },
      showFilters: false,
      products: []
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
        if (obj.product.id == id) {
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
      };
      var data = {};

      if (this.filters.date_from != '') {
        data['filter_date_from'] = this.filters.date_from;
      }

      if (this.filters.date_to != '') {
        data['filter_date_to'] = this.filters.date_to;
      }

      if (this.filters.product != '') {
        data['filter_product'] = this.filters.product.id;
      } // Retrieve report


      this.$http.get('/api/v1/reports/products', {
        headers: headers,
        params: data
      }).then(function (response) {
        _this.objects = response.data.data;

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
    resetFilters: function resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        product: ''
      };
      this.applyFilters();
    },
    applyFilters: function applyFilters() {
      this.retrieveObjects();
    }
  },
  created: function created() {
    var _this2 = this;

    this.retrieveObjects();
    this.$vs.loading();
    var headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    };
    this.$http.get('/api/v1/products', {
      headers: headers
    }).then(function (response) {
      _this2.products = [];
      response.data.data.forEach(function (product) {
        _this2.products.push({
          id: product.id,
          label: product.name
        });
      });

      _this2.$vs.loading.close();
    })["catch"](function (error) {
      _this2.$vs.loading.close();
    });
  },
  mounted: function mounted() {}
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ReportProducts.vue?vue&type=template&id=83d4af3c&":
/*!****************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ReportProducts.vue?vue&type=template&id=83d4af3c& ***!
  \****************************************************************************************************************************************************************************************************************/
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
    [_c("report-products-table")],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/ReportProductsTable.vue?vue&type=template&id=d0bbb188&":
/*!***************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ui-elements/table/ReportProductsTable.vue?vue&type=template&id=d0bbb188& ***!
  \***************************************************************************************************************************************************************************************************************************************/
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
    { attrs: { title: "Users report", "code-toggler": "" } },
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
                { staticClass: "vx-col w-full" },
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
              )
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "vx-row mb-2" }, [
              _c(
                "div",
                { staticClass: "vx-col w-full" },
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
                { staticClass: "vx-col w-full" },
                [
                  _c("label", [_vm._v("Product")]),
                  _vm._v(" "),
                  _c("v-select", {
                    attrs: { options: _vm.products },
                    model: {
                      value: _vm.filters.product,
                      callback: function($$v) {
                        _vm.$set(_vm.filters, "product", $$v)
                      },
                      expression: "filters.product"
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
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].product.id } },
                        [
                          _vm._v(
                            "\n                    " +
                              _vm._s(data[indextr].product.id) +
                              "\n                "
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].product.name } },
                        [
                          _vm._v(
                            "\n                    " +
                              _vm._s(data[indextr].product.name) +
                              "\n                "
                          )
                        ]
                      ),
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
                        { attrs: { data: data[indextr].commissions } },
                        [
                          _vm._v(
                            "\n                    " +
                              _vm._s(data[indextr].commissions) +
                              " €\n                "
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].sub_commissions } },
                        [
                          _vm._v(
                            "\n                    " +
                              _vm._s(data[indextr].sub_commissions) +
                              " €\n                "
                          )
                        ]
                      )
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
              _c("vs-th", { attrs: { "sort-key": "product.id" } }, [
                _vm._v("Product Id")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "product.name" } }, [
                _vm._v("Product Name")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "leads" } }, [
                _vm._v("Leads")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "commission" } }, [
                _vm._v("Commission")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "sub_commission" } }, [
                _vm._v("Sub commission")
              ])
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

/***/ "./resources/js/src/views/ReportProducts.vue":
/*!***************************************************!*\
  !*** ./resources/js/src/views/ReportProducts.vue ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ReportProducts_vue_vue_type_template_id_83d4af3c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ReportProducts.vue?vue&type=template&id=83d4af3c& */ "./resources/js/src/views/ReportProducts.vue?vue&type=template&id=83d4af3c&");
/* harmony import */ var _ReportProducts_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ReportProducts.vue?vue&type=script&lang=js& */ "./resources/js/src/views/ReportProducts.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _ReportProducts_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _ReportProducts_vue_vue_type_template_id_83d4af3c___WEBPACK_IMPORTED_MODULE_0__["render"],
  _ReportProducts_vue_vue_type_template_id_83d4af3c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/ReportProducts.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/ReportProducts.vue?vue&type=script&lang=js&":
/*!****************************************************************************!*\
  !*** ./resources/js/src/views/ReportProducts.vue?vue&type=script&lang=js& ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ReportProducts_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./ReportProducts.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ReportProducts.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ReportProducts_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/ReportProducts.vue?vue&type=template&id=83d4af3c&":
/*!**********************************************************************************!*\
  !*** ./resources/js/src/views/ReportProducts.vue?vue&type=template&id=83d4af3c& ***!
  \**********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ReportProducts_vue_vue_type_template_id_83d4af3c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./ReportProducts.vue?vue&type=template&id=83d4af3c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ReportProducts.vue?vue&type=template&id=83d4af3c&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ReportProducts_vue_vue_type_template_id_83d4af3c___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ReportProducts_vue_vue_type_template_id_83d4af3c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/ReportProductsTable.vue":
/*!**************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/ReportProductsTable.vue ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ReportProductsTable_vue_vue_type_template_id_d0bbb188___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ReportProductsTable.vue?vue&type=template&id=d0bbb188& */ "./resources/js/src/views/ui-elements/table/ReportProductsTable.vue?vue&type=template&id=d0bbb188&");
/* harmony import */ var _ReportProductsTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ReportProductsTable.vue?vue&type=script&lang=js& */ "./resources/js/src/views/ui-elements/table/ReportProductsTable.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _ReportProductsTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _ReportProductsTable_vue_vue_type_template_id_d0bbb188___WEBPACK_IMPORTED_MODULE_0__["render"],
  _ReportProductsTable_vue_vue_type_template_id_d0bbb188___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/ui-elements/table/ReportProductsTable.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/ReportProductsTable.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/ReportProductsTable.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ReportProductsTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ReportProductsTable.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/ReportProductsTable.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ReportProductsTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/ReportProductsTable.vue?vue&type=template&id=d0bbb188&":
/*!*********************************************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/ReportProductsTable.vue?vue&type=template&id=d0bbb188& ***!
  \*********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ReportProductsTable_vue_vue_type_template_id_d0bbb188___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ReportProductsTable.vue?vue&type=template&id=d0bbb188& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/ReportProductsTable.vue?vue&type=template&id=d0bbb188&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ReportProductsTable_vue_vue_type_template_id_d0bbb188___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ReportProductsTable_vue_vue_type_template_id_d0bbb188___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);