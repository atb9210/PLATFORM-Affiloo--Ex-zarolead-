(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[90],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/FormBuilder.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/FormBuilder.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _forms_FormBuilder_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./forms/FormBuilder.vue */ "./resources/js/src/views/forms/FormBuilder.vue");
//
//
//
//
//
//
//
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
    FormBuilder: _forms_FormBuilder_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  data: function data() {
    return {};
  },
  methods: {},
  created: function created() {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/forms/FormBuilder.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/forms/FormBuilder.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue_form_wizard__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-form-wizard */ "./node_modules/vue-form-wizard/dist/vue-form-wizard.js");
/* harmony import */ var vue_form_wizard__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue_form_wizard__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vue_form_wizard_dist_vue_form_wizard_min_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-form-wizard/dist/vue-form-wizard.min.css */ "./node_modules/vue-form-wizard/dist/vue-form-wizard.min.css");
/* harmony import */ var vue_form_wizard_dist_vue_form_wizard_min_css__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(vue_form_wizard_dist_vue_form_wizard_min_css__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var vee_validate__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vee-validate */ "./node_modules/vee-validate/dist/vee-validate.esm.js");
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return; var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

 // For custom error message


var dict = {
  custom: {
    name: {
      required: 'Name is required'
    },
    campaign: {
      required: 'Campaign is required'
    },
    network: {
      required: 'Network is required'
    },
    product: {
      required: 'Product is required'
    },
    traffic_source: {
      required: 'Traffic Source is required'
    }
  }
}; // register custom messages

vee_validate__WEBPACK_IMPORTED_MODULE_2__["Validator"].localize('en', dict); // custom rules
// ...

/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      campaigns: [],
      networks: [],
      products: [],
      traffic_sources: [],
      networksOptions: [],
      network_params: [],
      network_params_values: [],
      name: '',
      campaign: '',
      network: '',
      product: '',
      traffic_source: '',
      quantityOptions: [{
        'quantity': '1',
        'price': 99.99,
        'commission': 99.99,
        'sub_commission': 99.99,
        'default': true
      }],
      quantityDefaultIndex: 0,
      upsell: false,
      upsellQuantity: '1',
      plugins: {
        'facebook': {
          enabled: false,
          pixel: ''
        },
        'zeustrack': {
          enabled: false,
          sub_id: ''
        }
      },
      showPopup: {
        'create': false,
        'edit': false
      },
      newObject: {
        'create': {
          quantity: '',
          price: '',
          commission: '',
          sub_commission: ''
        },
        'edit': {
          quantity: '',
          price: '',
          commission: '',
          sub_commission: ''
        }
      },
      editingQuantityOptionIndex: -1,
      firstName: '',
      lastName: '',
      email: '',
      city: 'new-york',
      proposalTitle: '',
      jobTitle: '',
      textarea: '',
      eventName: '',
      eventLocation: 'san-francisco',
      status: 'plannning',
      cityOptions: [{
        text: 'New York',
        value: 'new-york'
      }, {
        text: 'Chicago',
        value: 'chicago'
      }, {
        text: 'San Francisco',
        value: 'san-francisco'
      }, {
        text: 'Boston',
        value: 'boston'
      }],
      statusOptions: [{
        text: 'Plannning',
        value: 'plannning'
      }, {
        text: 'In Progress',
        value: 'in progress'
      }, {
        text: 'Finished',
        value: 'finished'
      }],
      LocationOptions: [{
        text: 'New York',
        value: 'new-york'
      }, {
        text: 'Chicago',
        value: 'chicago'
      }, {
        text: 'San Francisco',
        value: 'san-francisco'
      }, {
        text: 'Boston',
        value: 'boston'
      }]
    };
  },
  methods: {
    getObjectIndexById: function getObjectIndexById(id, arr) {
      var index = -1;
      arr.forEach(function (obj, i) {
        if (obj.id == id) {
          index = i;
          return;
        }
      });
      return index;
    },
    changeNetwork: function changeNetwork() {
      this.network_params = [];
      this.network_params_values = [];

      if (this.network != '' && this.network > 0) {
        var index = this.getObjectIndexById(this.network, this.networks);
        console.log("INDEX: " + index);

        if (index != -1 && this.networks[index].api.hasOwnProperty('form') && this.networks[index].api.form.hasOwnProperty('params')) {
          for (var _i = 0, _Object$entries = Object.entries(this.networks[index].api.form.params); _i < _Object$entries.length; _i++) {
            var _Object$entries$_i = _slicedToArray(_Object$entries[_i], 2),
                key = _Object$entries$_i[0],
                value = _Object$entries$_i[1];

            this.network_params.push(value);
            this.network_params[this.network_params.length - 1].name = key;
            this.network_params[this.network_params.length - 1].validation = this.networks[index].api.form.validation.params[key];
            this.network_params_values.push('');
            this.network_params[this.network_params.length - 1].value = '';
            console.log(this.network_params[this.network_params.length - 1]);
          }
        }
      }
    },
    changeNetworkParam: function changeNetworkParam(index, newVal) {
      console.log(this.network_params);
      console.log("CALL INDEX: " + index + " val: " + newVal);
      console.log(this.$refs['network_param_api_key']);
      this.network_params[index].value = newVal;
    },
    showForm: function showForm(type) {
      this.showPopup[type] = true;
    },
    clearForm: function clearForm(type) {
      this.newObject[type] = {
        quantity: '',
        price: '',
        commission: '',
        sub_commission: ''
      };
    },
    isValidForm: function isValidForm(type) {
      var obj = this.newObject[type];
      return obj.quantity != '' && obj.price != '' && obj.price >= 0 && obj.commission != '' && obj.commission >= 0 && obj.sub_commission != '' && obj.sub_commission >= 0;
    },
    showEditQuantityOptionForm: function showEditQuantityOptionForm(index) {
      var obj = this.quantityOptions[index];
      this.newObject['edit'] = {
        quantity: obj.quantity,
        price: obj.price,
        commission: obj.commission,
        sub_commission: obj.sub_commission
      };
      this.editingQuantityOptionIndex = index;
      this.showForm('edit');
    },
    editQuantityOption: function editQuantityOption() {
      this.quantityOptions[this.editingQuantityOptionIndex] = this.newObject['edit'];
      this.clearForm('edit');
      this.editingQuantityOptionIndex = -1;
      this.showPopup['edit'] = false;
    },
    removeQuantityOption: function removeQuantityOption(index) {
      if (index >= this.quantityOptions.length) return;
      var defaultRemoved = this.quantityOptions[index]["default"];
      this.quantityOptions = this.quantityOptions.splice(index, 1);

      if (defaultRemoved && this.quantityOptions.length > 0) {
        this.quantityOptions[0]["default"] = true;
      }
    },
    createQuantityOption: function createQuantityOption() {
      this.quantityOptions.push({
        quantity: this.newObject['create'].quantity,
        price: this.newObject['create'].price,
        commission: this.newObject['create'].commission,
        sub_commission: this.newObject['create'].sub_commission
      });
      this.showPopup['create'] = false;
      this.clearForm('create');
    },
    changeQuantityDefault: function changeQuantityDefault() {
      console.log(this.quantityDefaultIndex);

      for (var i = 0; i < this.quantityOptions.length; i++) {
        if (i == this.quantityDefaultIndex) {
          this.quantityOptions[i]["default"] = true;
        } else {
          this.quantityOptions[i]["default"] = false;
        }
      }
    },
    validateStep1: function validateStep1() {
      var _this = this;

      return new Promise(function (resolve, reject) {
        _this.$validator.validateAll('step-1').then(function (result) {
          var selectionResult = true;

          if (_this.campaign === '' || _this.campaign <= 0) {
            selectionResult = false;

            _this.errors.add({
              scope: 'step-1',
              field: 'campaign',
              msg: 'Campaign is required (if it doesn\'t exist just create one)'
            });
          }

          if (_this.product === '' || _this.product <= 0) {
            selectionResult = false;

            _this.errors.add({
              scope: 'step-1',
              field: 'product',
              msg: 'Product is required (if it doesn\'t exist just create one)'
            });
          }

          if (_this.network === '' || _this.network <= 0) {
            selectionResult = false;

            _this.errors.add({
              scope: 'step-1',
              field: 'network',
              msg: 'Network is required (if it doesn\'t exist just create one)'
            });
          }

          if (_this.traffic_source === '' || _this.traffic_source <= 0) {
            selectionResult = false;

            _this.errors.add({
              scope: 'step-1',
              field: 'traffic_source',
              msg: 'Traffic Source is required (if it doesn\'t exist just create one)'
            });
          }

          if (result && selectionResult) {
            resolve(true);
          } else {
            reject('correct all values');
          }
        });
      });
    },
    validateStep2: function validateStep2() {
      var _this2 = this;

      return new Promise(function (resolve, reject) {
        _this2.$validator.validateAll('step-2').then(function (result) {
          if (result) {
            resolve(true);
          } else {
            reject('correct all values');
          }
        });
      });
    },
    validateStep3: function validateStep3() {
      var _this3 = this;

      return new Promise(function (resolve, reject) {
        _this3.$vs.loading();

        var headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        };
        var data = {
          network_params: {}
        };

        for (var i = 0; i < _this3.network_params.length; i++) {
          data.network_params[_this3.network_params[i].name] = _this3.network_params_values[i];
        }

        data.network_params = JSON.stringify(data.network_params); // Validate network params

        _this3.$http.post('/api/v1/networks/' + _this3.network + '/params/validate', data, {
          headers: headers
        }).then(function (response) {
          _this3.$vs.loading.close();

          resolve(true);
        })["catch"](function (error) {
          _this3.$vs.loading.close();

          _this3.errors.clear();

          for (var _i2 = 0, _Object$entries2 = Object.entries(error.response.data.errors); _i2 < _Object$entries2.length; _i2++) {
            var _Object$entries2$_i = _slicedToArray(_Object$entries2[_i2], 2),
                key = _Object$entries2$_i[0],
                value = _Object$entries2$_i[1];

            for (var _i3 = 0; _i3 < value.length; _i3++) {
              _this3.errors.add({
                scope: 'step-3',
                field: key,
                msg: value[_i3]
              });
            }
          }

          reject('correct all values');
        });
        /*
        this.$validator.validateAll('step-3').then(result => {
          if (result) {
            this.createForm()
            resolve(true)
          } else {
            reject('correct all values')
          }
        })
        */

      });
    },
    validateStep4: function validateStep4() {
      var _this4 = this;

      return new Promise(function (resolve, reject) {
        _this4.$vs.loading();

        var headers = {
          Authorization: 'Bearer ' + localStorage.getItem('accessToken')
        };
        var data = {
          name: _this4.name,
          campaign_id: _this4.campaign,
          network_id: _this4.network,
          product_id: _this4.product,
          traffic_source_id: _this4.traffic_source,
          quantity: {
            options: {},
            "default": ''
          },
          upsell: {
            enabled: _this4.upsell,
            quantity: _this4.upsellQuantity
          },
          network_params: {},
          plugins: {}
        };

        _this4.quantityOptions.forEach(function (opt) {
          data.quantity['options'][opt.quantity] = {
            price: opt.price,
            commission: opt.commission,
            sub_commission: opt.sub_commission
          };
        });

        data.quantity['default'] = _this4.quantityOptions[_this4.quantityDefaultIndex].quantity;

        for (var i = 0; i < _this4.network_params.length; i++) {
          data.network_params[_this4.network_params[i].name] = _this4.network_params_values[i];
        }

        data.plugins = _this4.plugins;
        data.quantity = JSON.stringify(data.quantity);
        data.upsell = JSON.stringify(data.upsell);
        data.network_params = JSON.stringify(data.network_params);
        data.plugins = JSON.stringify(data.plugins);
        console.log(data); // Create form

        _this4.$http.post('/api/v1/forms', data, {
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

          _this4.$router.push('/dashboard/forms');

          resolve(true);
        })["catch"](function (error) {
          _this4.$vs.loading.close();

          _this4.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          });

          reject('correct all values');
        });
      });
    }
  },
  components: {
    FormWizard: vue_form_wizard__WEBPACK_IMPORTED_MODULE_0__["FormWizard"],
    TabContent: vue_form_wizard__WEBPACK_IMPORTED_MODULE_0__["TabContent"]
  },
  created: function created() {
    var _this5 = this;

    var headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }; // Retrieve traffic sources

    this.$vs.loading();
    this.$http.get('/api/v1/traffic-sources/', {
      headers: headers
    }).then(function (response) {
      _this5.traffic_sources = [];
      response.data.data.forEach(function (traffic_source) {
        _this5.traffic_sources.push({
          id: traffic_source.id,
          label: traffic_source.name
        });

        _this5.traffic_source = traffic_source.id;
      });

      _this5.$vs.loading.close();
    })["catch"](function (error) {
      _this5.$vs.loading.close();
    }); // Retrieve products

    this.$vs.loading();
    this.$http.get('/api/v1/products/', {
      headers: headers
    }).then(function (response) {
      _this5.products = [];
      response.data.data.forEach(function (product) {
        _this5.products.push({
          id: product.id,
          label: product.name
        });

        _this5.product = product.id;
      });

      _this5.$vs.loading.close();
    })["catch"](function (error) {
      _this5.$vs.loading.close();
    }); // Retrieve networks

    this.$vs.loading();
    this.$http.get('/api/v1/networks/', {
      headers: headers
    }).then(function (response) {
      _this5.networks = response.data.data;

      _this5.networks.forEach(function (network) {
        _this5.networksOptions.push({
          id: network.id,
          label: network.name
        });

        _this5.network = network.id;
      });

      _this5.$vs.loading.close();
    })["catch"](function (error) {
      _this5.$vs.loading.close();
    }); // Retrieve campaigns

    this.$vs.loading();
    this.$http.get('/api/v1/campaigns/', {
      headers: headers
    }).then(function (response) {
      _this5.campaigns = [];
      response.data.data.forEach(function (campaign) {
        _this5.campaigns.push({
          id: campaign.id,
          label: campaign.name
        });

        _this5.campaign = campaign.id;
      });

      _this5.$vs.loading.close();
    })["catch"](function (error) {
      _this5.$vs.loading.close();
    });
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/FormBuilder.vue?vue&type=template&id=112d7906&":
/*!*************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/FormBuilder.vue?vue&type=template&id=112d7906& ***!
  \*************************************************************************************************************************************************************************************************************/
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
  return _c("div", { attrs: { id: "table-demo" } }, [_c("form-builder")], 1)
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/forms/FormBuilder.vue?vue&type=template&id=3a8fdb1d&":
/*!*******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/forms/FormBuilder.vue?vue&type=template&id=3a8fdb1d& ***!
  \*******************************************************************************************************************************************************************************************************************/
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
                              attrs: { label: "Network" },
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

/***/ "./resources/js/src/views/FormBuilder.vue":
/*!************************************************!*\
  !*** ./resources/js/src/views/FormBuilder.vue ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _FormBuilder_vue_vue_type_template_id_112d7906___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FormBuilder.vue?vue&type=template&id=112d7906& */ "./resources/js/src/views/FormBuilder.vue?vue&type=template&id=112d7906&");
/* harmony import */ var _FormBuilder_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FormBuilder.vue?vue&type=script&lang=js& */ "./resources/js/src/views/FormBuilder.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _FormBuilder_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _FormBuilder_vue_vue_type_template_id_112d7906___WEBPACK_IMPORTED_MODULE_0__["render"],
  _FormBuilder_vue_vue_type_template_id_112d7906___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/FormBuilder.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/FormBuilder.vue?vue&type=script&lang=js&":
/*!*************************************************************************!*\
  !*** ./resources/js/src/views/FormBuilder.vue?vue&type=script&lang=js& ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FormBuilder_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./FormBuilder.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/FormBuilder.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FormBuilder_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/FormBuilder.vue?vue&type=template&id=112d7906&":
/*!*******************************************************************************!*\
  !*** ./resources/js/src/views/FormBuilder.vue?vue&type=template&id=112d7906& ***!
  \*******************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormBuilder_vue_vue_type_template_id_112d7906___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./FormBuilder.vue?vue&type=template&id=112d7906& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/FormBuilder.vue?vue&type=template&id=112d7906&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormBuilder_vue_vue_type_template_id_112d7906___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormBuilder_vue_vue_type_template_id_112d7906___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/forms/FormBuilder.vue":
/*!******************************************************!*\
  !*** ./resources/js/src/views/forms/FormBuilder.vue ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _FormBuilder_vue_vue_type_template_id_3a8fdb1d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FormBuilder.vue?vue&type=template&id=3a8fdb1d& */ "./resources/js/src/views/forms/FormBuilder.vue?vue&type=template&id=3a8fdb1d&");
/* harmony import */ var _FormBuilder_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FormBuilder.vue?vue&type=script&lang=js& */ "./resources/js/src/views/forms/FormBuilder.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _FormBuilder_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _FormBuilder_vue_vue_type_template_id_3a8fdb1d___WEBPACK_IMPORTED_MODULE_0__["render"],
  _FormBuilder_vue_vue_type_template_id_3a8fdb1d___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/forms/FormBuilder.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/forms/FormBuilder.vue?vue&type=script&lang=js&":
/*!*******************************************************************************!*\
  !*** ./resources/js/src/views/forms/FormBuilder.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FormBuilder_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./FormBuilder.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/forms/FormBuilder.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_FormBuilder_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/forms/FormBuilder.vue?vue&type=template&id=3a8fdb1d&":
/*!*************************************************************************************!*\
  !*** ./resources/js/src/views/forms/FormBuilder.vue?vue&type=template&id=3a8fdb1d& ***!
  \*************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormBuilder_vue_vue_type_template_id_3a8fdb1d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./FormBuilder.vue?vue&type=template&id=3a8fdb1d& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/forms/FormBuilder.vue?vue&type=template&id=3a8fdb1d&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormBuilder_vue_vue_type_template_id_3a8fdb1d___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_FormBuilder_vue_vue_type_template_id_3a8fdb1d___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);