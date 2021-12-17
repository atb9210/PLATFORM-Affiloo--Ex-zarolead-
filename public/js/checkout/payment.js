/******************************************************************************/
/**                                   LIBS                                   **/
/******************************************************************************/
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
/*
import { utils } from "./utils";
import { ZaroLead } from "./zarolead";
*/
/******************************************************************************/
/**                                   UTILS                                  **/
/******************************************************************************/
var Utils = /** @class */ (function () {
    function Utils() {
    }
    Utils.prototype.strval = function (o) {
        return String(o);
    };
    Utils.prototype.intval = function (o) {
        return parseInt(o);
    };
    Utils.prototype.floatval = function (o) {
        return parseFloat(o);
    };
    Utils.prototype.jsonEncode = function (o) {
        var result = null;
        try {
            result = JSON.stringify(o);
        }
        catch (error) {
            result = null;
        }
        return result;
    };
    Utils.prototype.jsonDecode = function (s) {
        var result = null;
        try {
            result = JSON.parse(s);
        }
        catch (error) {
            result = null;
        }
        return result;
    };
    Utils.prototype.getUrlParam = function (param) {
        var url = new URL(window.location.href);
        return url.searchParams.get(param);
    };
    Utils.prototype.getHostname = function () {
        var tmpUrl = new URL(window.location.href);
        return tmpUrl.origin;
    };
    Utils.prototype.getCookie = function (key) {
        var cookiesMatch = document.cookie.match(new RegExp('(^| )' + key + '=([^;]+)'));
        if (cookiesMatch)
            return cookiesMatch[2];
        else
            return null;
    };
    Utils.prototype.hideElement = function (elementId) {
        var e = document.getElementById(elementId);
        if (e !== null) {
            e.style.display = 'none';
        }
    };
    Utils.prototype.showElement = function (elementId) {
        var e = document.getElementById(elementId);
        if (e !== null) {
            e.style.display = 'block';
        }
    };
    Utils.prototype.getAttribute = function (e, name) {
        var result = null;
        if (e !== null && e.attributes.hasOwnProperty(name)) {
            result = e.attributes[name].value;
        }
        return result;
    };
    Utils.prototype.getInputValue = function (inputId) {
        var result = '';
        var e = document.getElementById(inputId);
        if (e !== null) {
            result = e.value;
        }
        return result;
    };
    Utils.prototype.setInputValue = function (inputId, value) {
        var e = document.getElementById(inputId);
        if (e !== null) {
            e.value = value;
        }
    };
    Utils.prototype.getCurrencyValue = function (s) {
        var value = 0.0;
        s = s.replace(',', '.');
        if (s === '') {
            value = 0.0;
        }
        else if (!isNaN(Number(s))) {
            value = parseFloat(s);
        }
        return value;
    };
    Utils.prototype.formatCurrency = function (n) {
        return n.toFixed(2).replace('.', ',');
    };
    Utils.prototype.loadingStart = function () {
        var loading = document.getElementById('loading');
        if (loading !== null) {
            loading.style.display = 'block';
        }
    };
    Utils.prototype.loadingEnd = function () {
        var loading = document.getElementById('loading');
        if (loading !== null) {
            loading.style.display = 'none';
        }
    };
    return Utils;
}());
var utils = new Utils();
var ZaroLead = /** @class */ (function () {
    function ZaroLead() {
        var hostname = utils.getHostname();
        this.API_URL = hostname + '/api/v1/';
        this.API_URL_3RD = hostname + '/api/3rd/';
        /*
        this.$http = new Http();
        this.httpHeaders = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };
        */
        this.$http = axios;
    }
    ZaroLead.prototype.createOrder = function (params) {
        return __awaiter(this, void 0, void 0, function () {
            var order, response, error_1;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        order = null;
                        _a.label = 1;
                    case 1:
                        _a.trys.push([1, 3, , 4]);
                        return [4 /*yield*/, this.$http.post(this.API_URL + 'orders', params, { headers: this.httpHeaders })];
                    case 2:
                        response = _a.sent();
                        if (response !== null && response !== '') {
                            //order = utils.jsonDecode(response);
                            if (response.hasOwnProperty('data')) {
                                order = response['data'];
                            }
                            if (order === null || order.hasOwnProperty('error') || order.hasOwnProperty('errors')) {
                                order = null;
                            }
                            else if (order.hasOwnProperty('data')) {
                                order = order['data'];
                            }
                        }
                        return [3 /*break*/, 4];
                    case 3:
                        error_1 = _a.sent();
                        order = null;
                        return [3 /*break*/, 4];
                    case 4: return [2 /*return*/, order];
                }
            });
        });
    };
    ZaroLead.prototype.createLead = function (params) {
        return __awaiter(this, void 0, void 0, function () {
            var lead, response, error_2;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        lead = null;
                        _a.label = 1;
                    case 1:
                        _a.trys.push([1, 3, , 4]);
                        return [4 /*yield*/, this.$http.post(this.API_URL_3RD + 'warehouse/leads', params, { headers: this.httpHeaders })];
                    case 2:
                        response = _a.sent();
                        if (response !== null && response !== '') {
                            //lead = utils.jsonDecode(response);
                            if (response.hasOwnProperty('data')) {
                                lead = response['data'];
                            }
                            if (lead === null || lead.hasOwnProperty('error') || lead.hasOwnProperty('errors')) {
                                lead = null;
                            }
                            else if (lead.hasOwnProperty('data')) {
                                lead = lead['data'];
                            }
                        }
                        return [3 /*break*/, 4];
                    case 3:
                        error_2 = _a.sent();
                        lead = null;
                        return [3 /*break*/, 4];
                    case 4: return [2 /*return*/, lead];
                }
            });
        });
    };
    ZaroLead.prototype.updateLead = function (leadId, params) {
        return __awaiter(this, void 0, void 0, function () {
            var response, respJson, error_3;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 2, , 3]);
                        return [4 /*yield*/, this.$http.put(this.API_URL_3RD + 'warehouse/leads/' + leadId + '/update', params, { headers: this.httpHeaders })];
                    case 1:
                        response = _a.sent();
                        if (response !== null && response !== '') {
                            respJson = null;
                            if (response.hasOwnProperty('data')) {
                                respJson = response['data'];
                            }
                            if (respJson !== null && respJson.hasOwnProperty('status') && respJson['status'] === 'success') {
                                return [2 /*return*/, true];
                            }
                            else {
                                return [2 /*return*/, false];
                            }
                        }
                        return [3 /*break*/, 3];
                    case 2:
                        error_3 = _a.sent();
                        return [2 /*return*/, false];
                    case 3: return [2 /*return*/, false];
                }
            });
        });
    };
    ZaroLead.prototype.getLeadInfo = function (leadId) {
        return __awaiter(this, void 0, void 0, function () {
            var lead, response, error_4;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        lead = null;
                        _a.label = 1;
                    case 1:
                        _a.trys.push([1, 3, , 4]);
                        return [4 /*yield*/, this.$http.get(this.API_URL_3RD + 'warehouse/leads/' + leadId + '/info', { headers: this.httpHeaders })];
                    case 2:
                        response = _a.sent();
                        if (response !== null && response !== '') {
                            //lead = utils.jsonDecode(response);
                            if (response.hasOwnProperty('data')) {
                                lead = response['data'];
                            }
                            if (lead === null || lead.hasOwnProperty('error') || lead.hasOwnProperty('errors')) {
                                lead = null;
                            }
                            else if (lead.hasOwnProperty('data')) {
                                lead = lead['data'];
                            }
                        }
                        return [3 /*break*/, 4];
                    case 3:
                        error_4 = _a.sent();
                        lead = null;
                        return [3 /*break*/, 4];
                    case 4: return [2 /*return*/, lead];
                }
            });
        });
    };
    ZaroLead.prototype.updateLeadAddress = function (leadId, params) {
        return __awaiter(this, void 0, void 0, function () {
            var response, respJson, respJson_1, error_5;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 2, , 3]);
                        return [4 /*yield*/, this.$http.post(this.API_URL_3RD + 'warehouse/leads/' + leadId + '/address', params, { headers: this.httpHeaders })];
                    case 1:
                        response = _a.sent();
                        if (response !== null && response !== '') {
                            respJson = null;
                            if (response.hasOwnProperty('data')) {
                                respJson_1 = response['data'];
                            }
                            if (respJson !== null && respJson.hasOwnProperty('status') && respJson['status'] === 'success') {
                                return [2 /*return*/, true];
                            }
                            else {
                                return [2 /*return*/, false];
                            }
                        }
                        return [3 /*break*/, 3];
                    case 2:
                        error_5 = _a.sent();
                        return [2 /*return*/, false];
                    case 3: return [2 /*return*/, false];
                }
            });
        });
    };
    ZaroLead.prototype.completeLead = function (leadId, status, total, paymentMethod) {
        return __awaiter(this, void 0, void 0, function () {
            var params, response, respJson, error_6;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 2, , 3]);
                        params = {
                            status: status,
                            total: total,
                            payment_method: paymentMethod
                        };
                        return [4 /*yield*/, this.$http.post(this.API_URL_3RD + 'warehouse/leads/' + leadId + '/complete', params, { headers: this.httpHeaders })];
                    case 1:
                        response = _a.sent();
                        if (response !== null && response !== '') {
                            respJson = null;
                            if (response.hasOwnProperty('data')) {
                                respJson = response['data'];
                            }
                            if (respJson !== null && respJson.hasOwnProperty('status') && respJson['status'] === 'success') {
                                return [2 /*return*/, true];
                            }
                            else {
                                return [2 /*return*/, false];
                            }
                        }
                        return [3 /*break*/, 3];
                    case 2:
                        error_6 = _a.sent();
                        return [2 /*return*/, false];
                    case 3: return [2 /*return*/, false];
                }
            });
        });
    };
    ZaroLead.prototype.deleteLead = function (leadId) {
        return __awaiter(this, void 0, void 0, function () {
            var response, respJson, error_7;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 2, , 3]);
                        return [4 /*yield*/, this.$http["delete"](this.API_URL_3RD + 'warehouse/leads/' + leadId + '/delete', { headers: this.httpHeaders })];
                    case 1:
                        response = _a.sent();
                        if (response !== null && response !== '') {
                            respJson = null;
                            if (response.hasOwnProperty('data')) {
                                respJson = response['data'];
                            }
                            if (respJson !== null && respJson.hasOwnProperty('status') && respJson['status'] === 'success') {
                                return [2 /*return*/, true];
                            }
                            else {
                                return [2 /*return*/, false];
                            }
                        }
                        return [3 /*break*/, 3];
                    case 2:
                        error_7 = _a.sent();
                        return [2 /*return*/, false];
                    case 3: return [2 /*return*/, false];
                }
            });
        });
    };
    ZaroLead.prototype.getProduct = function (productId) {
        return __awaiter(this, void 0, void 0, function () {
            var product, response, error_8;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        product = null;
                        _a.label = 1;
                    case 1:
                        _a.trys.push([1, 3, , 4]);
                        return [4 /*yield*/, this.$http.get(this.API_URL + 'products/' + productId + '/info', { headers: this.httpHeaders })];
                    case 2:
                        response = _a.sent();
                        if (response !== null && response !== '') {
                            //product = utils.jsonDecode(response);
                            if (response.hasOwnProperty('data')) {
                                product = response['data'];
                            }
                            if (product === null || product.hasOwnProperty('error') || product.hasOwnProperty('errors')) {
                                product = null;
                            }
                            else if (product.hasOwnProperty('data')) {
                                product = product['data'];
                            }
                        }
                        return [3 /*break*/, 4];
                    case 3:
                        error_8 = _a.sent();
                        product = null;
                        return [3 /*break*/, 4];
                    case 4: return [2 /*return*/, product];
                }
            });
        });
    };
    ZaroLead.prototype.getPaymentIntentProduct = function (productId, leadId, quantity) {
        return __awaiter(this, void 0, void 0, function () {
            var paymentIntent, params, httpHeaders, response, error_9;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        paymentIntent = null;
                        params = {
                            lead: leadId,
                            quantity: quantity
                        };
                        httpHeaders = {
                            'Accept': 'application/json'
                        };
                        _a.label = 1;
                    case 1:
                        _a.trys.push([1, 3, , 4]);
                        return [4 /*yield*/, this.$http.get(this.API_URL_3RD + 'warehouse/products/' + productId + '/payment-intent', { params: params, headers: httpHeaders })];
                    case 2:
                        response = _a.sent();
                        if (response !== null && response !== '') {
                            //paymentIntent = utils.jsonDecode(response);
                            if (response.hasOwnProperty('data')) {
                                paymentIntent = response['data'];
                            }
                            if (paymentIntent === null || paymentIntent.hasOwnProperty('error') || paymentIntent.hasOwnProperty('errors')) {
                                paymentIntent = null;
                            }
                            else if (paymentIntent.hasOwnProperty('data')) {
                                paymentIntent = paymentIntent['data'];
                            }
                        }
                        return [3 /*break*/, 4];
                    case 3:
                        error_9 = _a.sent();
                        paymentIntent = null;
                        return [3 /*break*/, 4];
                    case 4: return [2 /*return*/, paymentIntent];
                }
            });
        });
    };
    ZaroLead.prototype.getPaymentIntent = function (amount) {
        return __awaiter(this, void 0, void 0, function () {
            var paymentIntent, params, httpHeaders, response, error_10;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        paymentIntent = null;
                        params = {
                            amount: amount
                        };
                        httpHeaders = {
                            'Accept': 'application/json'
                        };
                        _a.label = 1;
                    case 1:
                        _a.trys.push([1, 3, , 4]);
                        return [4 /*yield*/, this.$http.get(this.API_URL_3RD + 'warehouse/products/payment-intent', { params: params, headers: httpHeaders })];
                    case 2:
                        response = _a.sent();
                        if (response !== null && response !== '') {
                            //paymentIntent = utils.jsonDecode(response);
                            if (response.hasOwnProperty('data')) {
                                paymentIntent = response['data'];
                            }
                            if (paymentIntent === null || paymentIntent.hasOwnProperty('error') || paymentIntent.hasOwnProperty('errors')) {
                                paymentIntent = null;
                            }
                            else if (paymentIntent.hasOwnProperty('data')) {
                                paymentIntent = paymentIntent['data'];
                            }
                        }
                        return [3 /*break*/, 4];
                    case 3:
                        error_10 = _a.sent();
                        paymentIntent = null;
                        return [3 /*break*/, 4];
                    case 4: return [2 /*return*/, paymentIntent];
                }
            });
        });
    };
    ZaroLead.prototype.facebookEvent = function (params) {
        return __awaiter(this, void 0, void 0, function () {
            var response, error_11;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        _a.trys.push([0, 2, , 3]);
                        return [4 /*yield*/, this.$http.post(this.API_URL_3RD + 'facebook/events', params, { headers: this.httpHeaders })];
                    case 1:
                        response = _a.sent();
                        if (response !== null && response !== '' &&
                            response.hasOwnProperty('status') && response['status'] === 'success') {
                            return [2 /*return*/, true];
                        }
                        else {
                            return [2 /*return*/, false];
                        }
                        return [3 /*break*/, 3];
                    case 2:
                        error_11 = _a.sent();
                        return [2 /*return*/, false];
                    case 3: return [2 /*return*/, false];
                }
            });
        });
    };
    return ZaroLead;
}());
/******************************************************************************/
/**                             GLOBAL VARIABLES                             **/
/******************************************************************************/
var axios;
var $http;
var zarolead = new ZaroLead();
var thisScript = null;
var leadId;
var lead = null;
var form = null;
var products = [];
var accessories = [];
var lead = null;
var currentLocation = {
    street_number: '',
    route: '',
    area3: '',
    area2: '',
    area1: '',
    country: '',
    postcode: ''
};
var stripeKey = null;
var stripe;
var card;
var stripeClientSecret = '';
var paymentIntent = null;
var fullName = '';
var phone = '';
var total = 0.0;
/******************************************************************************/
/**                              STEP VALIDATION                             **/
/******************************************************************************/
function resetCurrentLocation() {
    currentLocation = {
        street_number: '',
        route: '',
        area3: '',
        area2: '',
        area1: '',
        country: '',
        postcode: ''
    };
}
function checkFullName(fullName) {
    fullName = fullName.trim();
    if (fullName.length < 5) {
        return false;
    }
    var tmp = fullName.split(' ');
    if (tmp.length < 2) {
        return false;
    }
    var firstName = tmp[0];
    var i = 1;
    while (tmp[i] === '') {
        i++;
        if (i >= tmp.length) {
            return false;
        }
    }
    var secondName = tmp[i];
    if (firstName.length === 0 || secondName.length === 0) {
        return false;
    }
    return true;
}
function checkPhone(phone) {
    phone = phone.split(' ').join('');
    if (phone.length < 8) {
        return false;
    }
    for (var i = 0; i < phone.length; i++) {
        if (!(phone[i] >= '0' && phone[i] <= '9') &&
            phone[i] !== '+' && phone[i] !== '(' && phone[i] !== ')') {
            return false;
        }
    }
    return true;
}
function checkLocation(loc) {
    if (loc.street_number === '') {
        return false;
    }
    if (loc.route === '') {
        return false;
    }
    if (loc.area3 === '') {
        return false;
    }
    if (loc.area2 === '') {
        return false;
    }
    if (loc.area1 === '') {
        return false;
    }
    if (loc.country === '') {
        return false;
    }
    if (loc.postcode === '') {
        return false;
    }
    return true;
}
function setError(form, inputId, error) {
    var e = document.getElementById(inputId);
    if (e !== null) {
        e.setCustomValidity(error);
        e.reportValidity();
        form.reportValidity();
    }
}
function clearErrors(form) {
    setError(form, 'fullName', '');
    setError(form, 'phone', '');
    setError(form, 'address', '');
    setError(form, 'streetAddress', '');
    setError(form, 'streetNumber', '');
    setError(form, 'cap', '');
    setError(form, 'city', '');
    setError(form, 'province', '');
    form.reportValidity();
}
function setStripeError(stripeErrorId, error) {
    var displayError = document.getElementById(stripeErrorId);
    displayError.textContent = error;
    if (error !== '') {
        displayError.style.display = 'block';
    }
    else {
        displayError.style.display = 'none';
    }
}
function changeLocation(place) {
    if (place === null) {
        return;
    }
    if (place.hasOwnProperty('address_components') && place['address_components'] !== null) {
        for (var _i = 0, _a = place['address_components']; _i < _a.length; _i++) {
            var component = _a[_i];
            var componentType = component.types[0];
            if (componentType === 'street_number') {
                currentLocation.street_number = component.long_name;
            }
            else if (componentType === 'route') {
                currentLocation.route = component.long_name;
            }
            else if (componentType === 'administrative_area_level_3') {
                currentLocation.area3 = component.long_name;
            }
            else if (componentType === 'administrative_area_level_2') {
                currentLocation.area2 = component.short_name;
            }
            else if (componentType === 'administrative_area_level_1') {
                currentLocation.area1 = component.short_name;
            }
            else if (componentType === 'country') {
                currentLocation.country = component.short_name;
            }
            else if (componentType === 'postal_code') {
                currentLocation.postcode = component.long_name;
            }
        }
        var e = null;
        e = document.getElementById('streetAddress');
        if (e !== null) {
            e.value = currentLocation.route;
        }
        e = document.getElementById('streetNumber');
        if (e !== null) {
            e.value = currentLocation.street_number;
        }
        e = document.getElementById('cap');
        if (e !== null) {
            e.value = currentLocation.postcode;
        }
        e = document.getElementById('city');
        if (e !== null) {
            e.value = currentLocation.area3;
        }
        e = document.getElementById('province');
        if (e !== null) {
            e.value = currentLocation.area2;
        }
        setError(form, 'address', '');
        setError(form, 'streetAddress', '');
        setError(form, 'streetNumber', '');
        setError(form, 'cap', '');
        setError(form, 'city', '');
        setError(form, 'province', '');
    }
}
function fillInAddress() {
    resetCurrentLocation();
    var place = autocomplete.getPlace();
    changeLocation(place);
}
/******************************************************************************/
/**                                   STEPS                                  **/
/******************************************************************************/
function initStep1() {
    return __awaiter(this, void 0, void 0, function () {
        return __generator(this, function (_a) {
            return [2 /*return*/];
        });
    });
}
function initStep2() {
    return __awaiter(this, void 0, void 0, function () {
        var pIntent;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0: return [4 /*yield*/, zarolead.getPaymentIntent(total)];
                case 1:
                    pIntent = _a.sent();
                    if (pIntent !== null && pIntent.hasOwnProperty('client_secret')) {
                        paymentIntent = pIntent;
                        stripeClientSecret = pIntent['client_secret'];
                    }
                    return [2 /*return*/];
            }
        });
    });
}
function validateStep1() {
    return __awaiter(this, void 0, void 0, function () {
        var isValid, termsAndConditions, termsAndConditionsInput, streetAddress, streetNumber, cap, city, province, firstName, lastName, tmp, i, updateLeadResult, addressUpdated;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    isValid = true;
                    fullName = utils.getInputValue('fullName');
                    phone = forcePhoneFormat(utils.getInputValue('phone'));
                    phone = phone.split(' ').join('');
                    // Check full name
                    if (checkFullName(fullName)) {
                        setError(form, 'fullName', '');
                    }
                    else {
                        setError(form, 'fullName', 'Nome e Cognome non validi');
                        isValid = false;
                    }
                    // Check phone
                    if (checkPhone(phone)) {
                        setError(form, 'phone', '');
                    }
                    else {
                        setError(form, 'phone', 'Numero di telefono non valido');
                        isValid = false;
                    }
                    termsAndConditions = false;
                    termsAndConditionsInput = document.getElementById('termsAndConditions');
                    if (termsAndConditionsInput !== null) {
                        termsAndConditions = (termsAndConditionsInput.checked === true);
                    }
                    if (termsAndConditions) {
                        setError(form, 'termsAndConditions', '');
                    }
                    else {
                        setError(form, 'termsAndConditions', 'Devi accettare i termini e condizioni');
                        isValid = false;
                    }
                    streetAddress = utils.getInputValue('streetAddress');
                    streetNumber = utils.getInputValue('streetNumber');
                    cap = utils.getInputValue('cap');
                    city = utils.getInputValue('city');
                    province = utils.getInputValue('province');
                    if (streetAddress == '') {
                        setError(form, 'streetAddress', 'Indirizzo non valido');
                        isValid = false;
                    }
                    else {
                        setError(form, 'streetAddress', '');
                    }
                    if (streetNumber == '') {
                        setError(form, 'streetNumber', 'Indirizzo non valido');
                        isValid = false;
                    }
                    else {
                        setError(form, 'streetNumber', '');
                    }
                    if (cap == '') {
                        setError(form, 'cap', 'Indirizzo non valido');
                        isValid = false;
                    }
                    else {
                        setError(form, 'cap', '');
                    }
                    if (city == '') {
                        setError(form, 'city', 'Indirizzo non valido');
                        isValid = false;
                    }
                    else {
                        setError(form, 'city', '');
                    }
                    if (province == '') {
                        setError(form, 'province', 'Indirizzo non valido');
                        isValid = false;
                    }
                    else {
                        setError(form, 'province', '');
                    }
                    if (!isValid) return [3 /*break*/, 3];
                    firstName = '';
                    lastName = '';
                    tmp = fullName.split(' ');
                    if (tmp.length > 0) {
                        firstName = tmp[0];
                    }
                    for (i = 1; i < tmp.length; i++) {
                        lastName += tmp[i];
                        if (i + 1 < tmp.length) {
                            lastName += ' ';
                        }
                    }
                    if (!(lead !== null)) return [3 /*break*/, 3];
                    return [4 /*yield*/, zarolead.updateLead(utils.strval(lead['hash']), {
                            first_name: firstName,
                            last_name: lastName,
                            phone: phone
                        })];
                case 1:
                    updateLeadResult = _a.sent();
                    return [4 /*yield*/, zarolead.updateLeadAddress(lead['hash'], {
                            street_address: streetAddress + ', ' + streetNumber,
                            city: city,
                            province: province,
                            state: currentLocation.area1,
                            country: 'IT',
                            postcode: cap
                        })];
                case 2:
                    addressUpdated = _a.sent();
                    _a.label = 3;
                case 3:
                    // DEBUG TODO: to delete
                    console.log('validateStep1', isValid);
                    return [2 /*return*/, isValid];
            }
        });
    });
}
function validateStep2() {
    return __awaiter(this, void 0, void 0, function () {
        var isValid, stripeResult, leadUpdated;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    isValid = false;
                    return [4 /*yield*/, stripe.confirmCardPayment(stripeClientSecret, {
                            payment_method: {
                                card: card,
                                billing_details: {
                                    name: fullName,
                                    address: {
                                        country: currentLocation.country,
                                        city: currentLocation.area3,
                                        line1: currentLocation.route + ' ' + currentLocation.street_number,
                                        line2: '',
                                        postal_code: currentLocation.postcode,
                                        state: currentLocation.area2
                                    },
                                    phone: phone
                                }
                            }
                        })];
                case 1:
                    stripeResult = _a.sent();
                    if (stripeResult.error) {
                        setStripeError('cardErrors', stripeResult.error.message);
                    }
                    else {
                        setStripeError('cardErrors', '');
                        if (stripeResult.paymentIntent.status === 'succeeded') {
                            isValid = true;
                        }
                    }
                    if (!isValid) return [3 /*break*/, 3];
                    return [4 /*yield*/, zarolead.completeLead(lead['hash'], 'confirmed', total, 'online')];
                case 2:
                    leadUpdated = _a.sent();
                    isValid = leadUpdated && isValid;
                    _a.label = 3;
                case 3:
                    // DEBUG TODO: to delete
                    console.log("validateStep2", isValid);
                    return [2 /*return*/, isValid];
            }
        });
    });
}
var steps = [
    {
        id: 'step1',
        enabled: true,
        init: initStep1,
        validate: validateStep1,
        buttonText: 'CONTINUA'
    },
    {
        id: 'step2',
        enabled: true,
        init: initStep2,
        validate: validateStep2,
        buttonText: 'PAGA ORA'
    },
    {
        id: 'step3',
        enabled: true,
        init: null,
        validate: null,
        buttonText: 'CONTINUA'
    },
];
var currentStep = -1;
function hideSteps() {
    for (var i = 0; i < steps.length; i++) {
        utils.hideElement(steps[i].id);
    }
}
function showStep(stepId) {
    utils.showElement(stepId);
}
function getStepIndexById(stepId) {
    for (var i = 0; i < steps.length; i++) {
        if (steps[i].id === stepId) {
            return i;
        }
    }
    return -1;
}
function getStepById(stepId) {
    for (var i = 0; i < steps.length; i++) {
        if (steps[i].id === stepId) {
            return steps[i];
        }
    }
    return null;
}
function initSteps() {
    // Nothing
}
function nextStep() {
    return __awaiter(this, void 0, void 0, function () {
        var goToNextStep, res, i, step, btn;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    goToNextStep = false;
                    if (!(currentStep < 0)) return [3 /*break*/, 1];
                    currentStep = -1;
                    goToNextStep = true;
                    return [3 /*break*/, 3];
                case 1:
                    if (!(steps[currentStep].validate !== null)) return [3 /*break*/, 3];
                    utils.loadingStart();
                    return [4 /*yield*/, steps[currentStep].validate()];
                case 2:
                    res = _a.sent();
                    goToNextStep = (res === true);
                    utils.loadingEnd();
                    _a.label = 3;
                case 3:
                    if (!goToNextStep) return [3 /*break*/, 8];
                    hideSteps();
                    clearErrors(form);
                    currentStep += 1;
                    i = currentStep;
                    _a.label = 4;
                case 4:
                    if (!(i < steps.length)) return [3 /*break*/, 8];
                    step = steps[i];
                    if (!step.enabled) {
                        return [3 /*break*/, 7];
                    }
                    showStep(step.id);
                    if (!(step.init !== null)) return [3 /*break*/, 6];
                    utils.loadingStart();
                    return [4 /*yield*/, step.init()];
                case 5:
                    _a.sent();
                    utils.loadingEnd();
                    _a.label = 6;
                case 6:
                    btn = document.getElementById('nextStepButton');
                    if (btn !== null) {
                        btn.innerText = step.buttonText;
                    }
                    if (step.validate === null) {
                        utils.hideElement('nextStepButton');
                    }
                    else {
                        utils.showElement('nextStepButton');
                    }
                    currentStep = i;
                    return [3 /*break*/, 8];
                case 7:
                    i++;
                    return [3 /*break*/, 4];
                case 8: return [2 /*return*/];
            }
        });
    });
}
function forcePhoneFormat(val) {
    var phonePrefix = '+(39)';
    var pos = val.indexOf(phonePrefix);
    if (pos < 0) {
        val = phonePrefix + val;
    }
    var newVal = phonePrefix;
    for (var i = phonePrefix.length; i < val.length; i++) {
        if ((val[i] >= '0' && val[i] <= '9') || val[i] === ' ') {
            newVal += val[i];
        }
    }
    return newVal;
}
/******************************************************************************/
/**                                  REVIEW                                  **/
/******************************************************************************/
function addProduct(name, quantity, total, bold) {
    if (bold === void 0) { bold = false; }
    var reviewDiv = null;
    reviewDiv = document.getElementById('review');
    if (reviewDiv !== null) {
        var row = document.createElement('div');
        row.classList.add('row');
        if (bold) {
            row.classList.add('fw-bold');
        }
        var leftCol = document.createElement('div');
        leftCol.classList.add('col-9');
        leftCol.classList.add('text-start');
        var s = name;
        if (quantity !== '') {
            s += ' x' + quantity;
        }
        leftCol.innerText = s;
        var rightCol = document.createElement('div');
        rightCol.classList.add('col-3');
        rightCol.classList.add('text-end');
        rightCol.innerText = total.toFixed(2) + ' €';
        row.appendChild(leftCol);
        row.appendChild(rightCol);
        reviewDiv.appendChild(row);
    }
    reviewDiv = document.getElementById('completeReview');
    if (reviewDiv !== null) {
        var row = document.createElement('div');
        row.classList.add('row');
        if (bold) {
            row.classList.add('fw-bold');
        }
        var leftCol = document.createElement('div');
        leftCol.classList.add('col-9');
        leftCol.classList.add('text-start');
        var s = name;
        if (quantity !== '') {
            s += ' x' + quantity;
        }
        leftCol.innerText = s;
        var rightCol = document.createElement('div');
        rightCol.classList.add('col-3');
        rightCol.classList.add('text-end');
        rightCol.innerText = total.toFixed(2) + ' €';
        row.appendChild(leftCol);
        row.appendChild(rightCol);
        reviewDiv.appendChild(row);
    }
}
/******************************************************************************/
/**                                  ONLOAD                                  **/
/******************************************************************************/
function onLoad() {
    return __awaiter(this, void 0, void 0, function () {
        var phoneInput, addressInput, thisScript, i, product, pTotal, i, accessory, aTotal, elements, fullName_1, phone_1, shipping, address, acService, res, placeId, map, pService, e, dateOptions;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    utils.loadingStart();
                    // Steps initialization
                    hideSteps();
                    // Form initialization
                    form = document.getElementById('mainForm');
                    if (form === null) {
                        return [2 /*return*/];
                    }
                    form.addEventListener('submit', function (event) {
                        return __awaiter(this, void 0, void 0, function () {
                            return __generator(this, function (_a) {
                                switch (_a.label) {
                                    case 0:
                                        event.preventDefault();
                                        event.stopPropagation();
                                        form.classList.add('was-validated');
                                        return [4 /*yield*/, nextStep()];
                                    case 1:
                                        _a.sent();
                                        return [2 /*return*/];
                                }
                            });
                        });
                    }, false);
                    phoneInput = document.getElementById('phone');
                    if (phoneInput !== null) {
                        phoneInput.addEventListener('keyup', function (event) {
                            var inputTarget = (event.target);
                            var value = inputTarget.value;
                            value = forcePhoneFormat(value);
                            inputTarget.value = value;
                        });
                    }
                    addressInput = document.getElementById('address');
                    addressInput.addEventListener('change', function (event) {
                        resetCurrentLocation();
                    });
                    if (addressInput !== null) {
                        autocomplete = new google.maps.places.Autocomplete(addressInput, {
                            componentRestrictions: { country: ['it'] },
                            fields: ['address_components'],
                            types: ['address']
                        });
                        autocomplete.addListener('place_changed', fillInAddress);
                    }
                    thisScript = document.getElementById('checkout-js');
                    if (!(thisScript !== null)) return [3 /*break*/, 3];
                    leadId = utils.getAttribute(thisScript, 'data-lead-id');
                    if (!(leadId !== null)) return [3 /*break*/, 2];
                    return [4 /*yield*/, zarolead.getLeadInfo(utils.strval(leadId))];
                case 1:
                    lead = _a.sent();
                    _a.label = 2;
                case 2:
                    if (lead !== null && lead.hasOwnProperty('products') && lead['products'] !== null) {
                        products = lead['products'];
                    }
                    if (lead !== null && lead.hasOwnProperty('accessories') && lead['accessories'] !== null) {
                        accessories = lead['accessories'];
                    }
                    stripeKey = utils.getAttribute(thisScript, 'data-stripe-key');
                    return [3 /*break*/, 3];
                case 3:
                    // TODO: Better error handling (show message?)
                    if (lead === null) {
                        return [2 /*return*/];
                    }
                    // TODO: Better error handling (show message?)
                    if (products.length == 0) {
                        return [2 /*return*/];
                    }
                    // TODO: Check correctness of products items (they must at least have certain fields)
                    // TODO: Check correctness of accessories items (they must at least have certain fields)
                    for (i = 0; i < products.length; i++) {
                        product = products[i];
                        pTotal = utils.getCurrencyValue(utils.strval(product['total']));
                        addProduct(product['name'], utils.strval(product['quantity']), pTotal);
                        total += pTotal;
                    }
                    for (i = 0; i < accessories.length; i++) {
                        accessory = accessories[i];
                        if (accessory['enabled'] !== true) {
                            continue;
                        }
                        aTotal = utils.getCurrencyValue(utils.strval(accessory['total']));
                        addProduct(accessory['name'], '', aTotal);
                        total += aTotal;
                    }
                    addProduct('TOTAL', '', total, true);
                    // Stripe initialization
                    if (stripeKey !== null) {
                        stripe = Stripe(stripeKey);
                        elements = stripe.elements();
                        card = elements.create('card', {
                            classes: {
                                base: 'form-control py-2'
                            },
                            hidePostalCode: true
                        });
                        if (card !== null) {
                            card.mount('#cardElement');
                        }
                        card.on('change', function (_a) {
                            var error = _a.error;
                            var displayError = document.getElementById('cardErrors');
                            if (error) {
                                displayError.textContent = error.message;
                                displayError.style.display = 'block';
                            }
                            else {
                                displayError.textContent = '';
                                displayError.style.display = 'none';
                            }
                        });
                    }
                    else {
                        // TODO: Error handling
                    }
                    if (!(lead !== null)) return [3 /*break*/, 6];
                    fullName_1 = '';
                    if (lead.hasOwnProperty('first_name') && lead['first_name'] !== null) {
                        fullName_1 += lead['first_name'];
                    }
                    if (lead.hasOwnProperty('last_name') && lead['last_name'] !== null) {
                        fullName_1 += ' ' + lead['last_name'];
                    }
                    utils.setInputValue('fullName', fullName_1);
                    phone_1 = '';
                    if (lead.hasOwnProperty('phone') && lead['phone'] !== null) {
                        phone_1 = lead['phone'];
                    }
                    utils.setInputValue('phone', phone_1);
                    if (!(lead.hasOwnProperty('shipping') && lead['shipping'] !== null)) return [3 /*break*/, 6];
                    shipping = lead['shipping'];
                    if (!(shipping.hasOwnProperty('street_address') && shipping['street_address'] !== null &&
                        shipping.hasOwnProperty('city') && shipping['city'] !== null &&
                        shipping.hasOwnProperty('province') && shipping['province'] !== null &&
                        shipping.hasOwnProperty('state') && shipping['state'] !== null)) return [3 /*break*/, 6];
                    address = shipping['street_address'] + ', ' + shipping['city'] + ', ' + shipping['province'] + ', ' + shipping['state'];
                    utils.setInputValue('address', address);
                    if (!(autocomplete !== null)) return [3 /*break*/, 6];
                    autocomplete.set(address);
                    acService = new google.maps.places.AutocompleteService();
                    return [4 /*yield*/, acService.getPlacePredictions({ input: address })];
                case 4:
                    res = _a.sent();
                    placeId = res['predictions'][0]['place_id'];
                    map = new google.maps.Map(document.getElementById('map'), {
                        center: new google.maps.LatLng(-33.8665433, 151.1956316),
                        zoom: 15
                    });
                    pService = new google.maps.places.PlacesService(map);
                    return [4 /*yield*/, pService.getDetails({
                            placeId: placeId,
                            componentRestrictions: { country: ['it'] },
                            fields: ['address_components'],
                            types: ['address']
                        }, function (place, status) {
                            if (status == google.maps.places.PlacesServiceStatus.OK) {
                                changeLocation(place);
                            }
                        })];
                case 5:
                    _a.sent();
                    _a.label = 6;
                case 6:
                    e = null;
                    e = document.getElementById('orderNumber');
                    if (e !== null) {
                        e.innerText = lead['number'];
                    }
                    e = document.getElementById('orderDate');
                    if (e !== null) {
                        dateOptions = {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        };
                        e.innerText = (new Date()).toLocaleDateString('it-IT', dateOptions);
                    }
                    e = document.getElementById('orderTotal');
                    if (e !== null) {
                        e.innerText = total.toFixed(2);
                    }
                    e = document.getElementById('orderPaymentMethod');
                    if (e !== null) {
                        e.innerText = 'Carta di credito'; // TODO: What about other methods?
                    }
                    // Initialize steps and start with first step
                    initSteps();
                    return [4 /*yield*/, nextStep()];
                case 7:
                    _a.sent();
                    utils.loadingEnd();
                    return [2 /*return*/];
            }
        });
    });
}
