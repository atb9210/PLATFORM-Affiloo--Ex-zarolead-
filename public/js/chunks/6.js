(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[6],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/FormEditor.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/FormEditor.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _forms_FormEditor_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./forms/FormEditor.vue */ "./resources/js/src/views/forms/FormEditor.vue");
//
//
//
//
//
//
//
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
    FormEditor: _forms_FormEditor_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/forms/FormEditor.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/forms/FormEditor.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, exports) {

throw new Error("Module build failed (from ./node_modules/babel-loader/lib/index.js):\nSyntaxError: C:\\dev\\zarolead\\resources\\js\\src\\views\\forms\\FormEditor.vue: Unexpected token, expected \";\" (669:12)\n\n\u001b[0m \u001b[90m 667 | \u001b[39m            commission\u001b[33m:\u001b[39m value\u001b[33m.\u001b[39mcommission\u001b[33m,\u001b[39m\u001b[0m\n\u001b[0m \u001b[90m 668 | \u001b[39m            price\u001b[33m:\u001b[39m value\u001b[33m.\u001b[39msub_commission\u001b[33m,\u001b[39m\u001b[0m\n\u001b[0m\u001b[31m\u001b[1m>\u001b[22m\u001b[39m\u001b[90m 669 | \u001b[39m          })price\u001b[33m;\u001b[39m\u001b[0m\n\u001b[0m \u001b[90m     | \u001b[39m            \u001b[31m\u001b[1m^\u001b[22m\u001b[39m\u001b[0m\n\u001b[0m \u001b[90m 670 | \u001b[39m        }\u001b[0m\n\u001b[0m \u001b[90m 671 | \u001b[39m\u001b[0m\n\u001b[0m \u001b[90m 672 | \u001b[39m        \u001b[36mthis\u001b[39m\u001b[33m.\u001b[39m$vs\u001b[33m.\u001b[39mloading\u001b[33m.\u001b[39mclose()\u001b[0m\n    at Parser._raise (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:742:17)\n    at Parser.raiseWithData (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:735:17)\n    at Parser.raise (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:729:17)\n    at Parser.unexpected (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:8779:16)\n    at Parser.semicolon (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:8761:40)\n    at Parser.parseExpressionStatement (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11637:10)\n    at Parser.parseStatementContent (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11238:19)\n    at Parser.parseStatement (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11104:17)\n    at Parser.parseBlockOrModuleBlockBody (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11679:25)\n    at Parser.parseBlockBody (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11665:10)\n    at Parser.parseBlock (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11649:10)\n    at Parser.parseStatementContent (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11180:21)\n    at Parser.parseStatement (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11104:17)\n    at C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11747:60\n    at Parser.withTopicForbiddingContext (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:10979:14)\n    at Parser.parseForIn (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11747:22)\n    at Parser.parseForStatement (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11419:21)\n    at Parser.parseStatementContent (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11129:21)\n    at Parser.parseStatement (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11104:17)\n    at Parser.parseBlockOrModuleBlockBody (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11679:25)\n    at Parser.parseBlockBody (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11665:10)\n    at Parser.parseBlock (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:11649:10)\n    at Parser.parseFunctionBody (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:10656:24)\n    at Parser.parseArrowExpression (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:10625:10)\n    at Parser.parseParenAndDistinguishExpression (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:10243:12)\n    at Parser.parseExprAtom (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:9969:21)\n    at Parser.parseExprSubscripts (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:9624:23)\n    at Parser.parseMaybeUnary (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:9604:21)\n    at Parser.parseExprOps (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:9474:23)\n    at Parser.parseMaybeConditional (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:9447:23)\n    at Parser.parseMaybeAssign (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:9402:21)\n    at Parser.parseExprListItem (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:10740:18)\n    at Parser.parseCallExpressionArguments (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:9812:22)\n    at Parser.parseSubscript (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:9718:31)\n    at Parser.parseSubscripts (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:9647:19)\n    at Parser.parseExprSubscripts (C:\\dev\\zarolead\\node_modules\\@babel\\parser\\lib\\index.js:9630:17)");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/FormEditor.vue?vue&type=template&id=32376d7b&":
/*!************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/FormEditor.vue?vue&type=template&id=32376d7b& ***!
  \************************************************************************************************************************************************************************************************************/
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
    [_c("form-editor", { attrs: { form_id: _vm.form_id } })],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/forms/FormEditor.vue?vue&type=template&id=2be13c4a&":
/*!******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/forms/FormEditor.vue?vue&type=template&id=2be13c4a& ***!
  \******************************************************************************************************************************************************************************************************************/
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
    { attrs: { title: "Form wizard with validation", "code-toggler": "" } },
    [
      _c("p", [
        _vm._v(
          "Build a form the easiest way. You can see all your forms in the Forms page"
        )
      ]),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "mt-5" },
        [
          _c(
            "form-wizard",
            {
              attrs: {
                color: "rgba(var(--vs-primary), 1)",
                errorColor: "rgba(var(--vs-danger), 1)",
                title: null,
                subtitle: null,
                finishButtonText: "Create Form"
              }
            },
            [
              _c(
                "tab-content",
                {
                  staticClass: "mb-5",
                  attrs: {
                    title: "Form Informations",
                    icon: "feather icon-info",
                    "before-change": _vm.validateStep1
                  }
                },
                [
                  _c("form", { attrs: { "data-vv-scope": "step-1" } }, [
                    _c("div", { staticClass: "vx-row" }, [
                      _c(
                        "div",
                        { staticClass: "vx-col md:w-full w-full mt-5" },
                        [
                          _c("vs-input", {
                            directives: [
                              {
                                name: "validate",
                                rawName: "v-validate",
                                value: "required",
                                expression: "'required'"
                              }
                            ],
                            staticClass: "w-full",
                            attrs: {
                              "icon-pack": "feather",
                              icon: "icon-tag",
                              "icon-no-border": "",
                              label: "Name",
                              name: "name"
                            },
                            model: {
                              value: _vm.name,
                              callback: function($$v) {
                                _vm.name = $$v
                              },
                              expression: "name"
                            }
                          }),
                          _vm._v(" "),
                          _c("span", { staticClass: "text-danger" }, [
                            _vm._v(_vm._s(_vm.errors.first("step-1.name")))
                          ])
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "div",
                        { staticClass: "vx-col md:w-1/2 w-full mt-5" },
                        [
                          _c(
                            "vs-select",
                            {
                              directives: [
                                {
                                  name: "validate",
                                  rawName: "v-validate",
                                  value: "required",
                                  expression: "'required'"
                                }
                              ],
                              staticClass: "w-full select-large",
                              attrs: { label: "Campaign" },
                              model: {
                                value: _vm.campaign,
                                callback: function($$v) {
                                  _vm.campaign = $$v
                                },
                                expression: "campaign"
                              }
                            },
                            _vm._l(_vm.campaigns, function(item, index) {
                              return _c("vs-select-item", {
                                key: index,
                                staticClass: "w-full",
                                attrs: { value: item.id, text: item.label }
                              })
                            }),
                            1
                          ),
                          _vm._v(" "),
                          _c("span", { staticClass: "text-danger" }, [
                            _vm._v(_vm._s(_vm.errors.first("step-1.campaign")))
                          ])
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "div",
                        { staticClass: "vx-col md:w-1/2 w-full mt-5" },
                        [
                          _c(
                            "vs-select",
                            {
                              directives: [
                                {
                                  name: "validate",
                                  rawName: "v-validate",
                                  value: "required",
                                  expression: "'required'"
                                }
                              ],
                              staticClass: "w-full select-large",
                              attrs: { label: "Product" },
                              model: {
                                value: _vm.product,
                                callback: function($$v) {
                                  _vm.product = $$v
                                },
                                expression: "product"
                              }
                            },
                            _vm._l(_vm.products, function(item, index) {
                              return _c("vs-select-item", {
                                key: index,
                                staticClass: "w-full",
                                attrs: { value: item.id, text: item.label }
                              })
                            }),
                            1
                          ),
                          _vm._v(" "),
                          _c("span", { staticClass: "text-danger" }, [
                            _vm._v(_vm._s(_vm.errors.first("step-1.product")))
                          ])
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "div",
                        { staticClass: "vx-col md:w-1/2 w-full mt-5" },
                        [
                          _c(
                            "vs-select",
                            {
                              directives: [
                                {
                                  name: "validate",
                                  rawName: "v-validate",
                                  value: "required",
                                  expression: "'required'"
                                }
                              ],
                              staticClass: "w-full select-large",
                              attrs: { label: "Network", disabled: "true" },
                              on: { change: _vm.changeNetwork },
                              model: {
                                value: _vm.network,
                                callback: function($$v) {
                                  _vm.network = $$v
                                },
                                expression: "network"
                              }
                            },
                            _vm._l(_vm.networksOptions, function(item, index) {
                              return _c("vs-select-item", {
                                key: index,
                                staticClass: "w-full",
                                attrs: { value: item.id, text: item.label }
                              })
                            }),
                            1
                          ),
                          _vm._v(" "),
                          _c("span", { staticClass: "text-danger" }, [
                            _vm._v(_vm._s(_vm.errors.first("step-1.network")))
                          ])
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "div",
                        { staticClass: "vx-col md:w-1/2 w-full mt-5" },
                        [
                          _c(
                            "vs-select",
                            {
                              directives: [
                                {
                                  name: "validate",
                                  rawName: "v-validate",
                                  value: "required",
                                  expression: "'required'"
                                }
                              ],
                              staticClass: "w-full select-large",
                              attrs: { label: "Traffic Source" },
                              model: {
                                value: _vm.traffic_source,
                                callback: function($$v) {
                                  _vm.traffic_source = $$v
                                },
                                expression: "traffic_source"
                              }
                            },
                            _vm._l(_vm.traffic_sources, function(item, index) {
                              return _c("vs-select-item", {
                                key: index,
                                staticClass: "w-full",
                                attrs: { value: item.id, text: item.label }
                              })
                            }),
                            1
                          ),
                          _vm._v(" "),
                          _c("span", { staticClass: "text-danger" }, [
                            _vm._v(
                              _vm._s(_vm.errors.first("step-1.traffic_source"))
                            )
                          ])
                        ],
                        1
                      )
                    ])
                  ])
                ]
              ),
              _vm._v(" "),
              _c(
                "tab-content",
                {
                  staticClass: "mb-5",
                  attrs: {
                    title: "Quantity & Upsell",
                    icon: "feather icon-dollar-sign",
                    "before-change": _vm.validateStep2
                  }
                },
                [
                  _c("form", { attrs: { "data-vv-scope": "step-2" } }, [
                    _c(
                      "div",
                      { staticClass: "vx-row" },
                      [
                        _c(
                          "vs-list",
                          { staticClass: "vx-col w-full mt-5" },
                          [
                            _c("vs-list-header", {
                              attrs: {
                                "icon-pack": "feather",
                                icon: "icon-hash",
                                title: "Quantity Options"
                              }
                            }),
                            _vm._v(" "),
                            _vm._l(_vm.quantityOptions, function(
                              quantityOption,
                              index
                            ) {
                              return _c(
                                "vs-list-item",
                                {
                                  key: index,
                                  attrs: {
                                    "icon-pack": "feather",
                                    icon: "icon-check",
                                    title:
                                      "Quantity: " + quantityOption.quantity,
                                    subtitle:
                                      "Price: €" +
                                      quantityOption.price +
                                      ", Commission: €" +
                                      quantityOption.commission +
                                      ", Sub Commission: €" +
                                      quantityOption.sub_commission
                                  }
                                },
                                [
                                  _c(
                                    "div",
                                    { staticClass: "flex items-center" },
                                    [
                                      _c(
                                        "vs-radio",
                                        {
                                          staticClass: "mr-4",
                                          attrs: { "vs-value": index },
                                          on: {
                                            change: _vm.changeQuantityDefault
                                          },
                                          model: {
                                            value: _vm.quantityDefaultIndex,
                                            callback: function($$v) {
                                              _vm.quantityDefaultIndex = $$v
                                            },
                                            expression: "quantityDefaultIndex"
                                          }
                                        },
                                        [_vm._v("Default")]
                                      ),
                                      _vm._v(" "),
                                      _c("vs-button", {
                                        staticClass: "mr-2",
                                        attrs: {
                                          "icon-pack": "feather",
                                          icon: "icon-edit",
                                          type: "filled",
                                          color: "primary"
                                        },
                                        on: {
                                          click: function($event) {
                                            return _vm.showEditQuantityOptionForm(
                                              index
                                            )
                                          }
                                        }
                                      }),
                                      _vm._v(" "),
                                      _c("vs-button", {
                                        staticClass: "mr-2",
                                        attrs: {
                                          "icon-pack": "feather",
                                          icon: "icon-trash",
                                          type: "filled",
                                          color: "danger"
                                        },
                                        on: {
                                          click: function($event) {
                                            return _vm.removeQuantityOption(
                                              index
                                            )
                                          }
                                        }
                                      })
                                    ],
                                    1
                                  )
                                ]
                              )
                            })
                          ],
                          2
                        ),
                        _vm._v(" "),
                        _c(
                          "div",
                          { staticClass: "vx-col w-full mt-2" },
                          [
                            _c(
                              "vs-button",
                              {
                                staticClass: "mt-3",
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
                              [_vm._v("Add option")]
                            )
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c(
                          "div",
                          { staticClass: "vx-col w-full mt-5" },
                          [
                            _c("div", { staticClass: "mb-2" }, [
                              _vm._v("Upsell")
                            ]),
                            _vm._v(" "),
                            _c(
                              "vx-input-group",
                              {
                                attrs: {
                                  prependClasses:
                                    "border border-solid border-grey border-r-0"
                                }
                              },
                              [
                                _c("template", { slot: "prepend" }, [
                                  _c(
                                    "div",
                                    { staticClass: "prepend-text" },
                                    [
                                      _c("vs-checkbox", {
                                        model: {
                                          value: _vm.upsell,
                                          callback: function($$v) {
                                            _vm.upsell = $$v
                                          },
                                          expression: "upsell"
                                        }
                                      })
                                    ],
                                    1
                                  )
                                ]),
                                _vm._v(" "),
                                _c(
                                  "vs-select",
                                  {
                                    staticClass: "w-full",
                                    model: {
                                      value: _vm.upsellQuantity,
                                      callback: function($$v) {
                                        _vm.upsellQuantity = $$v
                                      },
                                      expression: "upsellQuantity"
                                    }
                                  },
                                  _vm._l(_vm.quantityOptions, function(
                                    item,
                                    index
                                  ) {
                                    return _c("vs-select-item", {
                                      key: index,
                                      staticClass: "w-full",
                                      attrs: {
                                        value: item.quantity,
                                        text: item.quantity
                                      }
                                    })
                                  }),
                                  1
                                )
                              ],
                              2
                            )
                          ],
                          1
                        )
                      ],
                      1
                    )
                  ])
                ]
              ),
              _vm._v(" "),
              _c(
                "tab-content",
                {
                  staticClass: "mb-5",
                  attrs: {
                    title: "Network API",
                    icon: "feather icon-settings",
                    "before-change": _vm.validateStep3
                  }
                },
                [
                  _c("form", { attrs: { "data-vv-scope": "step-3" } }, [
                    _c(
                      "div",
                      { staticClass: "vx-row" },
                      [
                        _vm._l(_vm.network_params, function(param, index) {
                          return _c(
                            "div",
                            { key: index, staticClass: "vx-col w-full" },
                            [
                              _c("vs-input", {
                                staticClass: "w-full",
                                attrs: {
                                  "icon-pack": "feather",
                                  icon: "icon-tag",
                                  "icon-no-border": "",
                                  type: param.type,
                                  label:
                                    param.name +
                                    (param.hasOwnProperty("required")
                                      ? "*"
                                      : ""),
                                  name: param.name
                                },
                                model: {
                                  value: _vm.network_params_values[index],
                                  callback: function($$v) {
                                    _vm.$set(
                                      _vm.network_params_values,
                                      index,
                                      $$v
                                    )
                                  },
                                  expression: "network_params_values[index]"
                                }
                              }),
                              _vm._v(" "),
                              _c("span", { staticClass: "text-danger" }, [
                                _vm._v(
                                  _vm._s(
                                    _vm.errors.first("step-3." + param.name)
                                  )
                                )
                              ])
                            ],
                            1
                          )
                        }),
                        _vm._v(" "),
                        _c("div", { staticClass: "vx-col w-full mt-4" }, [
                          _c("span", [_vm._v("* Mandatory fields")])
                        ])
                      ],
                      2
                    )
                  ])
                ]
              ),
              _vm._v(" "),
              _c(
                "tab-content",
                {
                  staticClass: "mb-5",
                  attrs: {
                    title: "Plugins",
                    icon: "feather icon-plus-square",
                    "before-change": _vm.validateStep4
                  }
                },
                [
                  _c("form", { attrs: { "data-vv-scope": "step-4" } }, [
                    _c("div", { staticClass: "vx-row" }, [
                      _c(
                        "div",
                        { staticClass: "vx-col w-full mt-5" },
                        [
                          _c("div", { staticClass: "mb-2" }, [
                            _vm._v("Facebook Pixel")
                          ]),
                          _vm._v(" "),
                          _c(
                            "vx-input-group",
                            {
                              attrs: {
                                prependClasses:
                                  "border border-solid border-grey border-r-0"
                              }
                            },
                            [
                              _c("template", { slot: "prepend" }, [
                                _c(
                                  "div",
                                  { staticClass: "prepend-text" },
                                  [
                                    _c("vs-checkbox", {
                                      model: {
                                        value: _vm.plugins["facebook"].enabled,
                                        callback: function($$v) {
                                          _vm.$set(
                                            _vm.plugins["facebook"],
                                            "enabled",
                                            $$v
                                          )
                                        },
                                        expression:
                                          "plugins['facebook'].enabled"
                                      }
                                    })
                                  ],
                                  1
                                )
                              ]),
                              _vm._v(" "),
                              _c("vs-input", {
                                staticClass: "w-full",
                                attrs: {
                                  "icon-pack": "feather",
                                  icon: "icon-facebook",
                                  "icon-no-border": "",
                                  name: "fb_pixel",
                                  placeholder: "Facebook pixel"
                                },
                                model: {
                                  value: _vm.plugins["facebook"].pixel,
                                  callback: function($$v) {
                                    _vm.$set(
                                      _vm.plugins["facebook"],
                                      "pixel",
                                      $$v
                                    )
                                  },
                                  expression: "plugins['facebook'].pixel"
                                }
                              })
                            ],
                            2
                          )
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "div",
                        { staticClass: "vx-col w-full mt-5" },
                        [
                          _c("div", { staticClass: "mb-2" }, [
                            _vm._v("Zeus Track")
                          ]),
                          _vm._v(" "),
                          _c(
                            "vx-input-group",
                            {
                              attrs: {
                                prependClasses:
                                  "border border-solid border-grey border-r-0"
                              }
                            },
                            [
                              _c("template", { slot: "prepend" }, [
                                _c(
                                  "div",
                                  { staticClass: "prepend-text" },
                                  [
                                    _c("vs-checkbox", {
                                      model: {
                                        value: _vm.plugins["zeustrack"].enabled,
                                        callback: function($$v) {
                                          _vm.$set(
                                            _vm.plugins["zeustrack"],
                                            "enabled",
                                            $$v
                                          )
                                        },
                                        expression:
                                          "plugins['zeustrack'].enabled"
                                      }
                                    })
                                  ],
                                  1
                                )
                              ]),
                              _vm._v(" "),
                              _c("vs-input", {
                                staticClass: "w-full",
                                attrs: {
                                  "icon-pack": "feather",
                                  icon: "icon-at-sign",
                                  "icon-no-border": "",
                                  placeholder: "Sub ID"
                                },
                                model: {
                                  value: _vm.plugins["zeustrack"].sub_id,
                                  callback: function($$v) {
                                    _vm.$set(
                                      _vm.plugins["zeustrack"],
                                      "sub_id",
                                      $$v
                                    )
                                  },
                                  expression: "plugins['zeustrack'].sub_id"
                                }
                              })
                            ],
                            2
                          )
                        ],
                        1
                      )
                    ])
                  ])
                ]
              )
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "vs-popup",
        {
          attrs: {
            title: "Create Quantity Option",
            active: _vm.showPopup["create"]
          },
          on: {
            "update:active": function($event) {
              return _vm.$set(_vm.showPopup, "create", $event)
            }
          }
        },
        [
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Quantity")])
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
                    icon: "icon-hash",
                    "icon-no-border": ""
                  },
                  model: {
                    value: _vm.newObject["create"].quantity,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["create"], "quantity", $$v)
                    },
                    expression: "newObject['create'].quantity"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Price")])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full" },
              [
                _c("vs-input", {
                  staticClass: "w-full",
                  attrs: {
                    type: "number",
                    step: "0.01",
                    min: "0",
                    "icon-pack": "feather",
                    icon: "icon-dollar-sign",
                    "icon-no-border": ""
                  },
                  model: {
                    value: _vm.newObject["create"].price,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["create"], "price", $$v)
                    },
                    expression: "newObject['create'].price"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Commission")])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full" },
              [
                _c("vs-input", {
                  staticClass: "w-full",
                  attrs: {
                    type: "number",
                    step: "0.01",
                    min: "0",
                    "icon-pack": "feather",
                    icon: "icon-dollar-sign",
                    "icon-no-border": ""
                  },
                  model: {
                    value: _vm.newObject["create"].commission,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["create"], "commission", $$v)
                    },
                    expression: "newObject['create'].commission"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Sub Commission")])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full" },
              [
                _c("vs-input", {
                  staticClass: "w-full",
                  attrs: {
                    type: "number",
                    step: "0.01",
                    min: "0",
                    "icon-pack": "feather",
                    icon: "icon-dollar-sign",
                    "icon-no-border": ""
                  },
                  model: {
                    value: _vm.newObject["create"].sub_commission,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["create"], "sub_commission", $$v)
                    },
                    expression: "newObject['create'].sub_commission"
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
                  on: { click: _vm.createQuantityOption }
                },
                [_vm._v("Add")]
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
          attrs: {
            title: "Edit Quantity Option",
            active: _vm.showPopup["edit"]
          },
          on: {
            "update:active": function($event) {
              return _vm.$set(_vm.showPopup, "edit", $event)
            }
          }
        },
        [
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Quantity")])
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
                    icon: "icon-hash",
                    "icon-no-border": ""
                  },
                  model: {
                    value: _vm.newObject["edit"].quantity,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["edit"], "quantity", $$v)
                    },
                    expression: "newObject['edit'].quantity"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Price")])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full" },
              [
                _c("vs-input", {
                  staticClass: "w-full",
                  attrs: {
                    type: "number",
                    step: "0.01",
                    min: "0",
                    "icon-pack": "feather",
                    icon: "icon-dollar-sign",
                    "icon-no-border": ""
                  },
                  model: {
                    value: _vm.newObject["edit"].price,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["edit"], "price", $$v)
                    },
                    expression: "newObject['edit'].price"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Commission")])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full" },
              [
                _c("vs-input", {
                  staticClass: "w-full",
                  attrs: {
                    type: "number",
                    step: "0.01",
                    min: "0",
                    "icon-pack": "feather",
                    icon: "icon-dollar-sign",
                    "icon-no-border": ""
                  },
                  model: {
                    value: _vm.newObject["edit"].commission,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["edit"], "commission", $$v)
                    },
                    expression: "newObject['edit'].commission"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "vx-row mb-6" }, [
            _c("div", { staticClass: "vx-col sm:w-1/3 w-full" }, [
              _c("span", [_vm._v("Sub Commission")])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "vx-col sm:w-2/3 w-full" },
              [
                _c("vs-input", {
                  staticClass: "w-full",
                  attrs: {
                    type: "number",
                    step: "0.01",
                    min: "0",
                    "icon-pack": "feather",
                    icon: "icon-dollar-sign",
                    "icon-no-border": ""
                  },
                  model: {
                    value: _vm.newObject["edit"].sub_commission,
                    callback: function($$v) {
                      _vm.$set(_vm.newObject["edit"], "sub_commission", $$v)
                    },
                    expression: "newObject['edit'].sub_commission"
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
                  on: { click: _vm.editQuantityOption }
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

/***/ "./resources/js/src/views/FormEditor.vue":
/*!***********************************************!*\
  !*** ./resources/js/src/views/FormEditor.vue ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _FormEditor_vue_vue_type_template_id_32376d7b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FormEditor.vue?vue&type=template&id=32376d7b& */ "./resources/js/src/views/FormEditor.vue?vue&type=template&id=32376d7b&");
/* harmony import */ var _FormEditor_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FormEditor.vue?vue&type=script&lang=js& */ "./resources/js/src/views/FormEditor.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _FormEditor_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _FormEditor_vue_vue_type_template_id_32376d7b___WEBPACK_IMPORTED_MODULE_0__["render"],
  _FormEditor_vue_vue_type_template_id_32376d7b___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/FormEditor.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/FormEditor.vue?vue&type=script&lang=js&":
/*!************************************************************************!*\
  !*** ./resources/js/src/views/FormEditor.vue?vue&type=script&lang=js& ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FormEditor_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./FormEditor.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/FormEditor.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FormEditor_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/FormEditor.vue?vue&type=template&id=32376d7b&":
/*!******************************************************************************!*\
  !*** ./resources/js/src/views/FormEditor.vue?vue&type=template&id=32376d7b& ***!
  \******************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormEditor_vue_vue_type_template_id_32376d7b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./FormEditor.vue?vue&type=template&id=32376d7b& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/FormEditor.vue?vue&type=template&id=32376d7b&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormEditor_vue_vue_type_template_id_32376d7b___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormEditor_vue_vue_type_template_id_32376d7b___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/forms/FormEditor.vue":
/*!*****************************************************!*\
  !*** ./resources/js/src/views/forms/FormEditor.vue ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _FormEditor_vue_vue_type_template_id_2be13c4a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FormEditor.vue?vue&type=template&id=2be13c4a& */ "./resources/js/src/views/forms/FormEditor.vue?vue&type=template&id=2be13c4a&");
/* harmony import */ var _FormEditor_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FormEditor.vue?vue&type=script&lang=js& */ "./resources/js/src/views/forms/FormEditor.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _FormEditor_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _FormEditor_vue_vue_type_template_id_2be13c4a___WEBPACK_IMPORTED_MODULE_0__["render"],
  _FormEditor_vue_vue_type_template_id_2be13c4a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/forms/FormEditor.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/forms/FormEditor.vue?vue&type=script&lang=js&":
/*!******************************************************************************!*\
  !*** ./resources/js/src/views/forms/FormEditor.vue?vue&type=script&lang=js& ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FormEditor_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./FormEditor.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/forms/FormEditor.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FormEditor_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/forms/FormEditor.vue?vue&type=template&id=2be13c4a&":
/*!************************************************************************************!*\
  !*** ./resources/js/src/views/forms/FormEditor.vue?vue&type=template&id=2be13c4a& ***!
  \************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormEditor_vue_vue_type_template_id_2be13c4a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./FormEditor.vue?vue&type=template&id=2be13c4a& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/forms/FormEditor.vue?vue&type=template&id=2be13c4a&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormEditor_vue_vue_type_template_id_2be13c4a___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormEditor_vue_vue_type_template_id_2be13c4a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);