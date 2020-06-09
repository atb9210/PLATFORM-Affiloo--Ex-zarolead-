(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[100],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/TrafficSources.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/TrafficSources.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ui_elements_table_TrafficSourcesTable_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ui-elements/table/TrafficSourcesTable.vue */ "./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue");
//
//
//
//
//
//
//
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
    TrafficSourcesTable: _ui_elements_table_TrafficSourcesTable_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  data: function data() {
    return {};
  },
  methods: {},
  created: function created() {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue?vue&type=script&lang=js& ***!
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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
      editingObject: -1,
      showPopup: {
        'create': false,
        'edit': false
      },
      newObject: {
        'create': {
          name: '',
          custom_params: [{
            query_param: '',
            token: '',
            name: ''
          }]
        },
        'edit': {
          name: '',
          custom_params: [{
            query_param: '',
            token: '',
            name: ''
          }]
        }
      }
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
      }; // Retrieve traffic sources

      this.$http.get('/api/v1/traffic-sources', {
        headers: headers
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
    deleteSelected: function deleteSelected() {
      var _this2 = this;

      if (this.selected.length == 0) return;
      this.$vs.loading();
      this.deleted = [];
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      this.selected.forEach(function (trafficSource) {
        _this2.$http["delete"]('/api/v1/traffic-sources/' + trafficSource.id, {
          headers: headers
        }).then(function (response) {
          _this2.deleted.push(trafficSource.id);

          if (_this2.deleted.length == _this2.selected.length) {
            _this2.$vs.loading.close();

            _this2.selected = [];

            _this2.$vs.notify({
              title: 'Success',
              text: _this2.deleted.length + ' Traffic source(s) deleted',
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'success'
            });

            _this2.selected = [];
            _this2.deleted = [];

            _this2.retrieveObjects();
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
        custom_params: [{
          query_param: '',
          token: '',
          name: ''
        }]
      };
    },
    isValidForm: function isValidForm(type) {
      var obj = this.newObject[type];
      return obj.name.length > 0;
    },
    createObject: function createObject() {
      var _this3 = this;

      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      var custom_params = [];
      this.newObject['create'].custom_params.forEach(function (custom_param) {
        if (custom_param.query_param == '' && custom_param.token == '') return;
        custom_params.push(custom_param);
      });
      var data = {
        name: this.newObject['create'].name,
        custom_params: JSON.stringify(custom_params)
      }; // Retrieve traffic sources

      this.$http.post('/api/v1/traffic-sources', data, {
        headers: headers
      }).then(function (response) {
        _this3.$vs.loading.close();

        _this3.$vs.notify({
          title: 'Success',
          text: 'Traffic source created',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        });

        _this3.clearForm('create');

        _this3.showPopup['create'] = false;

        _this3.retrieveObjects();
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
      var custom_params = [{
        query_param: '',
        token: '',
        name: ''
      }];

      if (obj.custom_params.length > 0) {
        custom_params = obj.custom_params;
      }

      this.newObject['edit'] = {
        name: obj.name,
        custom_params: custom_params
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
      var custom_params = [];
      this.newObject['edit'].custom_params.forEach(function (custom_param) {
        if (custom_param.query_param == '' && custom_param.token == '') return;
        custom_params.push(custom_param);
      });
      var data = {
        name: this.newObject['edit'].name,
        custom_params: JSON.stringify(custom_params)
      };
      this.$http.put('/api/v1/traffic-sources/' + id, data, {
        headers: headers
      }).then(function (response) {
        _this4.$vs.loading.close();

        _this4.$vs.notify({
          title: 'Success',
          text: 'Traffic source updated',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        });

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
      this.showPopup['edit'] = false;
      this.editingObject = -1;
    },
    deleteObject: function deleteObject(id) {
      var _this5 = this;

      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      this.$http["delete"]('/api/v1/traffic-sources/' + id, {
        headers: headers
      }).then(function (response) {
        _this5.$vs.loading.close();

        _this5.$vs.notify({
          title: 'Success',
          text: 'Traffic source deleted',
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
    },
    addNewCustomParam: function addNewCustomParam(type) {
      this.newObject[type].custom_params.push({
        query_param: '',
        token: '',
        name: ''
      });
    }
  },
  created: function created() {
    this.retrieveObjects();
  },
  mounted: function mounted() {}
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/TrafficSources.vue?vue&type=template&id=3b5d0325&":
/*!****************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/TrafficSources.vue?vue&type=template&id=3b5d0325& ***!
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
    [_c("traffic-sources-table")],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue?vue&type=template&id=2fa40e59&":
/*!***************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue?vue&type=template&id=2fa40e59& ***!
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
    { attrs: { title: "Traffic sources", "code-toggler": "" } },
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
                icon: "icon-plus"
              },
              on: {
                click: function($event) {
                  return _vm.showForm("create")
                }
              }
            },
            [_vm._v("Create new")]
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
              on: { click: _vm.deleteSelected }
            },
            [_vm._v("Delete")]
          )
        ],
        1
      ),
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
                      _c("vs-td", { attrs: { data: data[indextr].id } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].id) +
                            "\n                "
                        )
                      ]),
                      _vm._v(" "),
                      _c("vs-td", { attrs: { data: data[indextr].name } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].name) +
                            "\n                "
                        )
                      ]),
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
                                icon: "icon-edit"
                              },
                              on: {
                                click: function($event) {
                                  return _vm.editForm(data[indextr].id)
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
              _c("vs-th", { attrs: { "sort-key": "id" } }, [_vm._v("Id")]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "name" } }, [_vm._v("Name")]),
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
          attrs: {
            title: "Create Traffic source",
            active: _vm.showPopup["create"]
          },
          on: {
            "update:active": function($event) {
              return _vm.$set(_vm.showPopup, "create", $event)
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
                [_vm._v(_vm._s(_vm.newObject["create"].name))]
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
                    value: _vm.newObject["create"].name,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["create"], "name", $$v)
                    },
                    expression: "newObject['create'].name"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _vm._l(_vm.newObject["create"].custom_params, function(
            custom_param,
            indexparam
          ) {
            return _c("div", { staticClass: "vx-row mb-6" }, [
              _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
                indexparam == 0
                  ? _c("span", [_vm._v("Custom Params")])
                  : _vm._e()
              ]),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "vx-col sm:w-2/3 w-full flex items-center" },
                [
                  _c("vs-input", {
                    staticClass: "w-1/3",
                    attrs: {
                      "icon-pack": "feather",
                      icon: "icon-code",
                      placeholder: "Query Param",
                      "icon-no-border": ""
                    },
                    model: {
                      value:
                        _vm.newObject["create"].custom_params[indexparam]
                          .query_param,
                      callback: function($$v) {
                        _vm.$set(
                          _vm.newObject["create"].custom_params[indexparam],
                          "query_param",
                          $$v
                        )
                      },
                      expression:
                        "newObject['create'].custom_params[indexparam].query_param"
                    }
                  }),
                  _vm._v(" "),
                  _c("vs-input", {
                    staticClass: "w-1/3",
                    attrs: {
                      "icon-pack": "feather",
                      icon: "icon-code",
                      placeholder: "Token",
                      "icon-no-border": ""
                    },
                    model: {
                      value:
                        _vm.newObject["create"].custom_params[indexparam].token,
                      callback: function($$v) {
                        _vm.$set(
                          _vm.newObject["create"].custom_params[indexparam],
                          "token",
                          $$v
                        )
                      },
                      expression:
                        "newObject['create'].custom_params[indexparam].token"
                    }
                  }),
                  _vm._v(" "),
                  _c("vs-input", {
                    staticClass: "w-1/3",
                    attrs: {
                      "icon-pack": "feather",
                      icon: "icon-code",
                      placeholder: "Name",
                      "icon-no-border": ""
                    },
                    model: {
                      value:
                        _vm.newObject["create"].custom_params[indexparam].name,
                      callback: function($$v) {
                        _vm.$set(
                          _vm.newObject["create"].custom_params[indexparam],
                          "name",
                          $$v
                        )
                      },
                      expression:
                        "newObject['create'].custom_params[indexparam].name"
                    }
                  })
                ],
                1
              )
            ])
          }),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full flex items-center" },
              [
                _c("div", { staticClass: "w-1/3" }),
                _vm._v(" "),
                _c("div", { staticClass: "w-1/3" }),
                _vm._v(" "),
                _c(
                  "vs-button",
                  {
                    staticClass: "w-1/3",
                    attrs: { "icon-pack": "feather", icon: "icon-plus" },
                    on: {
                      click: function($event) {
                        return _vm.addNewCustomParam("create")
                      }
                    }
                  },
                  [_vm._v("Add")]
                )
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
                    disabled: !_vm.isValidForm("create")
                  },
                  on: { click: _vm.createObject }
                },
                [_vm._v("Create")]
              )
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
          attrs: {
            title: "Edit Traffic source",
            active: _vm.showPopup["edit"]
          },
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
          _vm._l(_vm.newObject["edit"].custom_params, function(
            custom_param,
            indexparam
          ) {
            return _c("div", { staticClass: "vx-row mb-6" }, [
              _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
                indexparam == 0
                  ? _c("span", [_vm._v("Custom Params")])
                  : _vm._e()
              ]),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "vx-col sm:w-2/3 w-full flex items-center" },
                [
                  _c("vs-input", {
                    staticClass: "w-1/3",
                    attrs: {
                      "icon-pack": "feather",
                      icon: "icon-code",
                      placeholder: "Query Param",
                      "icon-no-border": ""
                    },
                    model: {
                      value:
                        _vm.newObject["edit"].custom_params[indexparam]
                          .query_param,
                      callback: function($$v) {
                        _vm.$set(
                          _vm.newObject["edit"].custom_params[indexparam],
                          "query_param",
                          $$v
                        )
                      },
                      expression:
                        "newObject['edit'].custom_params[indexparam].query_param"
                    }
                  }),
                  _vm._v(" "),
                  _c("vs-input", {
                    staticClass: "w-1/3",
                    attrs: {
                      "icon-pack": "feather",
                      icon: "icon-code",
                      placeholder: "Token",
                      "icon-no-border": ""
                    },
                    model: {
                      value:
                        _vm.newObject["edit"].custom_params[indexparam].token,
                      callback: function($$v) {
                        _vm.$set(
                          _vm.newObject["edit"].custom_params[indexparam],
                          "token",
                          $$v
                        )
                      },
                      expression:
                        "newObject['edit'].custom_params[indexparam].token"
                    }
                  }),
                  _vm._v(" "),
                  _c("vs-input", {
                    staticClass: "w-1/3",
                    attrs: {
                      "icon-pack": "feather",
                      icon: "icon-code",
                      placeholder: "Name",
                      "icon-no-border": ""
                    },
                    model: {
                      value:
                        _vm.newObject["edit"].custom_params[indexparam].name,
                      callback: function($$v) {
                        _vm.$set(
                          _vm.newObject["edit"].custom_params[indexparam],
                          "name",
                          $$v
                        )
                      },
                      expression:
                        "newObject['edit'].custom_params[indexparam].name"
                    }
                  })
                ],
                1
              )
            ])
          }),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full flex items-center" },
              [
                _c("div", { staticClass: "w-1/3" }),
                _vm._v(" "),
                _c("div", { staticClass: "w-1/3" }),
                _vm._v(" "),
                _c(
                  "vs-button",
                  {
                    staticClass: "w-1/3",
                    attrs: { "icon-pack": "feather", icon: "icon-plus" },
                    on: {
                      click: function($event) {
                        return _vm.addNewCustomParam("edit")
                      }
                    }
                  },
                  [_vm._v("Add")]
                )
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

/***/ "./resources/js/src/views/TrafficSources.vue":
/*!***************************************************!*\
  !*** ./resources/js/src/views/TrafficSources.vue ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _TrafficSources_vue_vue_type_template_id_3b5d0325___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TrafficSources.vue?vue&type=template&id=3b5d0325& */ "./resources/js/src/views/TrafficSources.vue?vue&type=template&id=3b5d0325&");
/* harmony import */ var _TrafficSources_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./TrafficSources.vue?vue&type=script&lang=js& */ "./resources/js/src/views/TrafficSources.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _TrafficSources_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _TrafficSources_vue_vue_type_template_id_3b5d0325___WEBPACK_IMPORTED_MODULE_0__["render"],
  _TrafficSources_vue_vue_type_template_id_3b5d0325___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/TrafficSources.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/TrafficSources.vue?vue&type=script&lang=js&":
/*!****************************************************************************!*\
  !*** ./resources/js/src/views/TrafficSources.vue?vue&type=script&lang=js& ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TrafficSources_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./TrafficSources.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/TrafficSources.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TrafficSources_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/TrafficSources.vue?vue&type=template&id=3b5d0325&":
/*!**********************************************************************************!*\
  !*** ./resources/js/src/views/TrafficSources.vue?vue&type=template&id=3b5d0325& ***!
  \**********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_TrafficSources_vue_vue_type_template_id_3b5d0325___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./TrafficSources.vue?vue&type=template&id=3b5d0325& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/TrafficSources.vue?vue&type=template&id=3b5d0325&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_TrafficSources_vue_vue_type_template_id_3b5d0325___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_TrafficSources_vue_vue_type_template_id_3b5d0325___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue":
/*!**************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _TrafficSourcesTable_vue_vue_type_template_id_2fa40e59___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TrafficSourcesTable.vue?vue&type=template&id=2fa40e59& */ "./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue?vue&type=template&id=2fa40e59&");
/* harmony import */ var _TrafficSourcesTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./TrafficSourcesTable.vue?vue&type=script&lang=js& */ "./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _TrafficSourcesTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _TrafficSourcesTable_vue_vue_type_template_id_2fa40e59___WEBPACK_IMPORTED_MODULE_0__["render"],
  _TrafficSourcesTable_vue_vue_type_template_id_2fa40e59___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TrafficSourcesTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./TrafficSourcesTable.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TrafficSourcesTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue?vue&type=template&id=2fa40e59&":
/*!*********************************************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue?vue&type=template&id=2fa40e59& ***!
  \*********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_TrafficSourcesTable_vue_vue_type_template_id_2fa40e59___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./TrafficSourcesTable.vue?vue&type=template&id=2fa40e59& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/TrafficSourcesTable.vue?vue&type=template&id=2fa40e59&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_TrafficSourcesTable_vue_vue_type_template_id_2fa40e59___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_TrafficSourcesTable_vue_vue_type_template_id_2fa40e59___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);