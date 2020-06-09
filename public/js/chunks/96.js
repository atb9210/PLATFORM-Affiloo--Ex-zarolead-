(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[96],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/Products.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/Products.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ui_elements_table_ProductsTable_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ui-elements/table/ProductsTable.vue */ "./resources/js/src/views/ui-elements/table/ProductsTable.vue");
//
//
//
//
//
//
//
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
    ProductsTable: _ui_elements_table_ProductsTable_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  data: function data() {
    return {};
  },
  methods: {},
  created: function created() {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/ProductsTable.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ui-elements/table/ProductsTable.vue?vue&type=script&lang=js& ***!
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
          tag: '',
          advertiser: '',
          country: ''
        },
        'edit': {
          name: '',
          tag: '',
          advertiser: '',
          country: ''
        }
      },
      tags: [],
      advertisers: []
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
      }; // Retrieve products

      this.$http.get('/api/v1/products', {
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
      this.selected.forEach(function (product) {
        _this2.$http["delete"]('/api/v1/products/' + product.id, {
          headers: headers
        }).then(function (response) {
          _this2.deleted.push(product.id);

          if (_this2.deleted.length == _this2.selected.length) {
            _this2.$vs.loading.close();

            _this2.selected = [];

            _this2.$vs.notify({
              title: 'Success',
              text: _this2.deleted.length + ' Product(s) deleted',
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
        tag: '',
        advertiser: '',
        country: ''
      };
    },
    isValidForm: function isValidForm(type) {
      var obj = this.newObject[type];
      return obj.name.length > 0 && obj.tag != '' && obj.tag.id >= 1 && obj.advertiser != '' && obj.advertiser.id >= 1;
    },
    createObject: function createObject() {
      var _this3 = this;

      this.$vs.loading();
      var headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      };
      var data = {
        name: this.newObject['create'].name,
        tag_id: this.newObject['create'].tag.id,
        advertiser_id: this.newObject['create'].advertiser.id,
        country: this.newObject['create'].country
      }; // Retrieve products

      this.$http.post('/api/v1/products', data, {
        headers: headers
      }).then(function (response) {
        _this3.$vs.loading.close();

        _this3.$vs.notify({
          title: 'Success',
          text: 'Product created',
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
      this.newObject['edit'] = {
        name: obj.name,
        tag: {
          id: obj.tag.id,
          label: obj.tag.name
        },
        advertiser: {
          id: obj.advertiser.id,
          label: obj.advertiser.name
        },
        country: obj.country
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
      var data = {
        name: this.newObject['edit'].name,
        tag_id: this.newObject['edit'].tag.id,
        advertiser_id: this.newObject['edit'].advertiser.id,
        country: this.newObject['edit'].country
      };
      this.$http.put('/api/v1/products/' + id, data, {
        headers: headers
      }).then(function (response) {
        _this4.$vs.loading.close();

        _this4.$vs.notify({
          title: 'Success',
          text: 'Product updated',
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
      this.$http["delete"]('/api/v1/products/' + id, {
        headers: headers
      }).then(function (response) {
        _this5.$vs.loading.close();

        _this5.$vs.notify({
          title: 'Success',
          text: 'Product deleted',
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
    }
  },
  created: function created() {
    var _this6 = this;

    this.retrieveObjects();
    var headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }; // Retrieve tags

    this.$vs.loading();
    this.$http.get('/api/v1/tags/', {
      headers: headers
    }).then(function (response) {
      _this6.tags = [];
      response.data.data.forEach(function (tag) {
        _this6.tags.push({
          id: tag.id,
          label: tag.name
        });
      });

      _this6.$vs.loading.close();
    })["catch"](function (error) {
      _this6.$vs.loading.close();
    }); // Retrieve advertisers

    this.$vs.loading();
    this.$http.get('/api/v1/advertisers/', {
      headers: headers
    }).then(function (response) {
      _this6.advertisers = [];
      response.data.data.forEach(function (advertiser) {
        _this6.advertisers.push({
          id: advertiser.id,
          label: advertiser.name
        });
      });

      _this6.$vs.loading.close();
    })["catch"](function (error) {
      _this6.$vs.loading.close();
    });
  },
  mounted: function mounted() {}
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/Products.vue?vue&type=template&id=540e628e&":
/*!**********************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/Products.vue?vue&type=template&id=540e628e& ***!
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
  return _c("div", { attrs: { id: "table-demo" } }, [_c("products-table")], 1)
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/ProductsTable.vue?vue&type=template&id=aa6aeee0&":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/ui-elements/table/ProductsTable.vue?vue&type=template&id=aa6aeee0& ***!
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
    { attrs: { title: "Products", "code-toggler": "" } },
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
                      _c(
                        "vs-td",
                        { attrs: { data: data[indextr].advertiser.name } },
                        [
                          _vm._v(
                            "\n                    " +
                              _vm._s(data[indextr].advertiser.name) +
                              "\n                "
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c("vs-td", { attrs: { data: data[indextr].tag.name } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].tag.name) +
                            "\n                "
                        )
                      ]),
                      _vm._v(" "),
                      _c("vs-td", { attrs: { data: data[indextr].country } }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(data[indextr].country) +
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
              _c("vs-th", { attrs: { "sort-key": "advertiser.name" } }, [
                _vm._v("Advertiser")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "tag.name" } }, [
                _vm._v("Tag")
              ]),
              _vm._v(" "),
              _c("vs-th", { attrs: { "sort-key": "country" } }, [
                _vm._v("Country")
              ]),
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
          attrs: { title: "Create Product", active: _vm.showPopup["create"] },
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
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Tag")])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full" },
              [
                _c("v-select", {
                  attrs: { options: _vm.tags },
                  model: {
                    value: _vm.newObject["create"].tag,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["create"], "tag", $$v)
                    },
                    expression: "newObject['create'].tag"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Advertiser")])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full" },
              [
                _c("v-select", {
                  attrs: { options: _vm.advertisers },
                  model: {
                    value: _vm.newObject["create"].advertiser,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["create"], "advertiser", $$v)
                    },
                    expression: "newObject['create'].advertiser"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Country")])
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
                    icon: "icon-globe",
                    "icon-no-border": ""
                  },
                  model: {
                    value: _vm.newObject["create"].country,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["create"], "country", $$v)
                    },
                    expression: "newObject['create'].country"
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
                    disabled: !_vm.isValidForm("create")
                  },
                  on: { click: _vm.createObject }
                },
                [_vm._v("Create")]
              )
            ],
            1
          )
        ]
      ),
      _vm._v(" "),
      _c(
        "vs-popup",
        {
          attrs: { title: "Edit Product", active: _vm.showPopup["edit"] },
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
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Tag")])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full" },
              [
                _c("v-select", {
                  attrs: { options: _vm.tags },
                  model: {
                    value: _vm.newObject["edit"].tag,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["edit"], "tag", $$v)
                    },
                    expression: "newObject['edit'].tag"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Advertiser")])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full" },
              [
                _c("v-select", {
                  attrs: { options: _vm.advertisers },
                  model: {
                    value: _vm.newObject["edit"].advertiser,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["edit"], "advertiser", $$v)
                    },
                    expression: "newObject['edit'].advertiser"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Country")])
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
                    icon: "icon-globe",
                    "icon-no-border": ""
                  },
                  model: {
                    value: _vm.newObject["edit"].country,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["edit"], "country", $$v)
                    },
                    expression: "newObject['edit'].country"
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

/***/ "./resources/js/src/views/Products.vue":
/*!*********************************************!*\
  !*** ./resources/js/src/views/Products.vue ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Products_vue_vue_type_template_id_540e628e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Products.vue?vue&type=template&id=540e628e& */ "./resources/js/src/views/Products.vue?vue&type=template&id=540e628e&");
/* harmony import */ var _Products_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Products.vue?vue&type=script&lang=js& */ "./resources/js/src/views/Products.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Products_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Products_vue_vue_type_template_id_540e628e___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Products_vue_vue_type_template_id_540e628e___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/Products.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/Products.vue?vue&type=script&lang=js&":
/*!**********************************************************************!*\
  !*** ./resources/js/src/views/Products.vue?vue&type=script&lang=js& ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Products_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./Products.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/Products.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Products_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/Products.vue?vue&type=template&id=540e628e&":
/*!****************************************************************************!*\
  !*** ./resources/js/src/views/Products.vue?vue&type=template&id=540e628e& ***!
  \****************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Products_vue_vue_type_template_id_540e628e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./Products.vue?vue&type=template&id=540e628e& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/Products.vue?vue&type=template&id=540e628e&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Products_vue_vue_type_template_id_540e628e___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Products_vue_vue_type_template_id_540e628e___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/ProductsTable.vue":
/*!********************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/ProductsTable.vue ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ProductsTable_vue_vue_type_template_id_aa6aeee0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ProductsTable.vue?vue&type=template&id=aa6aeee0& */ "./resources/js/src/views/ui-elements/table/ProductsTable.vue?vue&type=template&id=aa6aeee0&");
/* harmony import */ var _ProductsTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ProductsTable.vue?vue&type=script&lang=js& */ "./resources/js/src/views/ui-elements/table/ProductsTable.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _ProductsTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _ProductsTable_vue_vue_type_template_id_aa6aeee0___WEBPACK_IMPORTED_MODULE_0__["render"],
  _ProductsTable_vue_vue_type_template_id_aa6aeee0___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/ui-elements/table/ProductsTable.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/ProductsTable.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/ProductsTable.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ProductsTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ProductsTable.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/ProductsTable.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ProductsTable_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/ui-elements/table/ProductsTable.vue?vue&type=template&id=aa6aeee0&":
/*!***************************************************************************************************!*\
  !*** ./resources/js/src/views/ui-elements/table/ProductsTable.vue?vue&type=template&id=aa6aeee0& ***!
  \***************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ProductsTable_vue_vue_type_template_id_aa6aeee0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ProductsTable.vue?vue&type=template&id=aa6aeee0& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/ui-elements/table/ProductsTable.vue?vue&type=template&id=aa6aeee0&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ProductsTable_vue_vue_type_template_id_aa6aeee0___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ProductsTable_vue_vue_type_template_id_aa6aeee0___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);