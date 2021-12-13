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
/******************************************************************************/
/**                                   LIBS                                   **/
/******************************************************************************/
/*
import { utils } from "./utils";
import { ZaroLead } from "./zarolead";
import { Facebook } from "./facebook";
import { TikTok } from "./tiktok";
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
/******************************************************************************/
/**                                 ZAROLEAD                                 **/
/******************************************************************************/
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
    ZaroLead.prototype.getPaymentIntent = function (productId, leadId, quantity) {
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
    ZaroLead.prototype.facebookEvent = function (params) {
        return __awaiter(this, void 0, void 0, function () {
            var response, error_10;
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
                        error_10 = _a.sent();
                        return [2 /*return*/, false];
                    case 3: return [2 /*return*/, false];
                }
            });
        });
    };
    return ZaroLead;
}());
/******************************************************************************/
/**                                 FACEBOOK                                 **/
/******************************************************************************/
var Facebook = /** @class */ (function () {
    function Facebook(pixel, accessToken, eventValue, eventCurrency, testCode) {
        if (testCode === void 0) { testCode = null; }
        this.pixel = pixel;
        this.accessToken = accessToken;
        this.eventValue = eventValue;
        this.eventCurrency = eventCurrency;
        this.testCode = testCode;
        this.zarolead = new ZaroLead();
    }
    Facebook.prototype.getClickId = function () {
        var clickId = '';
        clickId = utils.getCookie('_fbc');
        var urlParamClickId = utils.getUrlParam('fbclid');
        if (urlParamClickId !== null && urlParamClickId !== '') {
            clickId = utils.getUrlParam('fbclid');
        }
        if (clickId === null) {
            clickId = '';
        }
        return clickId;
    };
    Facebook.prototype.triggerEvent = function (eventName, customData, testCode) {
        if (customData === void 0) { customData = {}; }
        if (testCode === void 0) { testCode = null; }
        return __awaiter(this, void 0, void 0, function () {
            var params, result;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        params = {
                            'access_token': this.accessToken,
                            'pixel': this.pixel,
                            'event': eventName,
                            'fbc': this.getClickId(),
                            'custom_data': JSON.stringify(customData),
                            'test_code': testCode
                        };
                        return [4 /*yield*/, this.zarolead.facebookEvent(params)];
                    case 1:
                        result = _a.sent();
                        return [2 /*return*/, result];
                }
            });
        });
    };
    Facebook.prototype.finalize = function () {
        return __awaiter(this, void 0, void 0, function () {
            var result;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0: return [4 /*yield*/, this.triggerEvent('Purchase', {
                            value: this.eventValue,
                            currency: this.eventCurrency
                        }, this.testCode)];
                    case 1:
                        result = _a.sent();
                        return [2 /*return*/, result];
                }
            });
        });
    };
    return Facebook;
}());
/******************************************************************************/
/**                                  TIKTOK                                  **/
/******************************************************************************/
var TikTok = /** @class */ (function () {
    function TikTok() {
    }
    return TikTok;
}());
/******************************************************************************/
/**                             GLOBAL VARIABLES                             **/
/******************************************************************************/
var axios;
var $http;
var zarolead = new ZaroLead();
var currentLocation = {
    street_number: '',
    route: '',
    area3: '',
    area2: '',
    area1: '',
    country: '',
    postcode: ''
};
var stripe;
var card;
var stripeClientSecret = '';
var thisScript = null;
var form = null;
var lead = null;
var productId = '';
var product = null;
var paymentIntent = null;
var pluginsList = [];
var fullName = '';
var phone = '';
var quantity = '';
var price = 0.0;
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
var paymentMethods = [
    {
        id: 'paymentMethodCod',
        fee: 0.0,
        name: 'Alla consegna',
        enabled: false
    },
    {
        id: 'paymentMethodOnline',
        fee: 0.0,
        name: 'Online',
        enabled: false
    },
];
var paymentMethod = '';
function getPaymentMethod(pmId) {
    for (var i = 0; i < paymentMethods.length; i++) {
        var pm = paymentMethods[i];
        if (pm.id === pmId) {
            return pm;
        }
    }
    return null;
}
function getPaymentMethodIndex(pmId) {
    for (var i = 0; i < paymentMethods.length; i++) {
        var pm = paymentMethods[i];
        if (pm.id === pmId) {
            return i;
        }
    }
    return -1;
}
function initPaymentMethods() {
    return __awaiter(this, void 0, void 0, function () {
        var e, i, checkout, pms, pm, pmIndex, i, pm, e_1;
        return __generator(this, function (_a) {
            e = document.getElementsByClassName('paymentMethodInputRadio');
            if (e !== null) {
                for (i = 0; i < e.length; i++) {
                    e[i].addEventListener('change', function (event) {
                        return __awaiter(this, void 0, void 0, function () {
                            return __generator(this, function (_a) {
                                if (event !== null) {
                                    changePaymentMethod(event.target['value']);
                                }
                                return [2 /*return*/];
                            });
                        });
                    }, false);
                }
            }
            if (product.hasOwnProperty('checkout') && product['checkout'] !== null) {
                checkout = product['checkout'];
                if (checkout.hasOwnProperty('payment_methods') && checkout['payment_methods'] !== null) {
                    pms = checkout['payment_methods'];
                    for (pm in pms) {
                        pmIndex = getPaymentMethodIndex(pm);
                        if (pmIndex < 0) {
                            continue;
                        }
                        if (pms[pm].hasOwnProperty('enabled') && pms[pm]['enabled'] === true) {
                            paymentMethods[pmIndex].enabled = true;
                            if (pms[pm].hasOwnProperty('fee') && pms[pm]['fee'] !== null) {
                                paymentMethods[pmIndex].fee = utils.getCurrencyValue(pms[pm]['fee']);
                            }
                            if (pms[pm].hasOwnProperty('name') && pms[pm]['name'] !== null) {
                                paymentMethods[pmIndex].name = utils.strval(pms[pm]['name']);
                            }
                        }
                        else {
                            paymentMethods[pmIndex].enabled = false;
                            utils.hideElement(pm + 'Div');
                        }
                    }
                }
            }
            for (i = 0; i < paymentMethods.length; i++) {
                pm = paymentMethods[i];
                e_1 = document.getElementById(pm.id + 'Fee');
                if (e_1 !== null) {
                    e_1.innerText = pm.fee.toFixed(2);
                }
            }
            return [2 /*return*/];
        });
    });
}
function changePaymentMethod(pmId) {
    for (var i = 0; i < paymentMethods.length; i++) {
        var pm = paymentMethods[i];
        utils.hideElement(pm.id);
        if (pm.id === pmId) {
            var e = document.getElementById('reviewTotalAmount');
            if (e !== null) {
                e.innerText = (total + pm.fee).toFixed(2);
            }
        }
    }
    utils.showElement(pmId);
    paymentMethod = pmId;
}
/******************************************************************************/
/**                                   STEPS                                  **/
/******************************************************************************/
function initStep4() {
    return __awaiter(this, void 0, void 0, function () {
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0: return [4 /*yield*/, initPaymentMethods()];
                case 1:
                    _a.sent();
                    if (paymentMethods.length > 0) {
                        changePaymentMethod(paymentMethods[0].id);
                    }
                    return [2 /*return*/];
            }
        });
    });
}
function initStep5() {
    return __awaiter(this, void 0, void 0, function () {
        var fee, pm, e, dateOptions, s;
        return __generator(this, function (_a) {
            fee = 0.0;
            pm = getPaymentMethod(paymentMethod);
            if (pm !== null) {
                fee = pm.fee;
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
                    e.innerText = (total + fee).toFixed(2);
                }
                e = document.getElementById('orderPaymentMethod');
                if (e !== null) {
                    s = pm.name;
                    if (fee > 0) {
                        s += ' (+' + fee.toFixed(2) + '€)';
                    }
                    e.innerText = s;
                }
                e = document.getElementById('orderDetailsPaymentMethod');
                if (e !== null) {
                    e.innerText = pm.name;
                }
                e = document.getElementById('orderDetailsPaymentMethodFee');
                if (e !== null) {
                    e.innerText = fee.toFixed(2);
                }
                e = document.getElementById('orderDetailsTotal');
                if (e !== null) {
                    e.innerText = (total + fee).toFixed(2);
                }
            }
            return [2 /*return*/];
        });
    });
}
function validateStep1() {
    return __awaiter(this, void 0, void 0, function () {
        var isValid, termsAndConditions, termsAndConditionsInput, firstName, lastName, tmp, i, updateLeadResult, fbResult;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    utils.loadingStart();
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
                    isValid = isValid && (product !== null);
                    if (!isValid) return [3 /*break*/, 6];
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
                    if (!(lead === null)) return [3 /*break*/, 2];
                    return [4 /*yield*/, zarolead.createLead({
                            first_name: firstName,
                            last_name: lastName,
                            phone: phone,
                            sku: product['sku'],
                            product_name: product['name'],
                            product_id: utils.intval(productId),
                            price: price.toFixed(2),
                            total: total.toFixed(2),
                            quantity: quantity,
                            status: 'abandoned'
                        })];
                case 1:
                    lead = _a.sent();
                    return [3 /*break*/, 4];
                case 2: return [4 /*yield*/, zarolead.updateLead(utils.strval(lead['hash']), {
                        first_name: firstName,
                        last_name: lastName,
                        phone: phone
                    })];
                case 3:
                    updateLeadResult = _a.sent();
                    _a.label = 4;
                case 4:
                    fbResult = false;
                    if (!(facebook !== null)) return [3 /*break*/, 6];
                    return [4 /*yield*/, facebook.finalize()];
                case 5:
                    fbResult = _a.sent();
                    _a.label = 6;
                case 6:
                    // DEBUG TODO: to delete
                    console.log('validateStep1', isValid);
                    utils.loadingEnd();
                    return [2 /*return*/, isValid];
            }
        });
    });
}
function validateStep2() {
    return __awaiter(this, void 0, void 0, function () {
        var isValid, pIntent;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    isValid = true;
                    if (quantity === '') {
                        isValid = false;
                    }
                    if (price === 0.0) {
                        isValid = false;
                    }
                    if (total === 0.0) {
                        isValid = false;
                    }
                    if (!isValid) return [3 /*break*/, 3];
                    if (!(lead !== null && lead.hasOwnProperty('id'))) return [3 /*break*/, 2];
                    return [4 /*yield*/, zarolead.getPaymentIntent(utils.strval(productId), utils.strval(lead['hash']), quantity)];
                case 1:
                    pIntent = _a.sent();
                    if (pIntent !== null && pIntent.hasOwnProperty('client_secret')) {
                        paymentIntent = pIntent;
                        stripeClientSecret = pIntent['client_secret'];
                    }
                    else {
                        isValid = false;
                    }
                    return [3 /*break*/, 3];
                case 2:
                    isValid = false;
                    _a.label = 3;
                case 3:
                    // DEBUG TODO: to delete
                    console.log("validateStep2", isValid);
                    return [2 /*return*/, isValid];
            }
        });
    });
}
function validateStep3() {
    return __awaiter(this, void 0, void 0, function () {
        var isValid, streetAddress, streetNumber, cap, city, province, addressUpdated;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    isValid = true;
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
                    if (!isValid) return [3 /*break*/, 2];
                    if (!(lead !== null)) return [3 /*break*/, 2];
                    return [4 /*yield*/, zarolead.updateLeadAddress(lead['hash'], {
                            street_address: streetAddress + ', ' + streetNumber,
                            city: city,
                            province: province,
                            state: currentLocation.area1,
                            country: 'IT',
                            postcode: cap
                        })];
                case 1:
                    addressUpdated = _a.sent();
                    _a.label = 2;
                case 2:
                    // DEBUG TODO: to delete
                    console.log('validateStep3', isValid);
                    return [2 /*return*/, isValid];
            }
        });
    });
}
function validateStep4() {
    return __awaiter(this, void 0, void 0, function () {
        var isValid, pMethod, stripeResult, fee, pm, leadUpdated;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    isValid = false;
                    pMethod = '';
                    if (!(paymentMethod === 'paymentMethodCod')) return [3 /*break*/, 1];
                    pMethod = 'cashondelivery';
                    isValid = true;
                    return [3 /*break*/, 3];
                case 1:
                    if (!(paymentMethod === 'paymentMethodOnline')) return [3 /*break*/, 3];
                    pMethod = 'online';
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
                case 2:
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
                    _a.label = 3;
                case 3:
                    if (!isValid) return [3 /*break*/, 5];
                    fee = 0.0;
                    pm = getPaymentMethod(paymentMethod);
                    if (pm !== null) {
                        fee = pm.fee;
                    }
                    return [4 /*yield*/, zarolead.completeLead(lead['hash'], 'confirmed', total + fee, pMethod)];
                case 4:
                    leadUpdated = _a.sent();
                    isValid = leadUpdated && isValid;
                    _a.label = 5;
                case 5:
                    // DEBUG TODO: to delete
                    console.log("validateStep4", isValid);
                    return [2 /*return*/, isValid];
            }
        });
    });
}
var steps = [
    {
        id: 'step1',
        enabled: true,
        init: null,
        validate: validateStep1,
        buttonText: 'CONTINUA'
    },
    {
        id: 'step2',
        enabled: false,
        init: null,
        validate: validateStep2,
        buttonText: 'CONTINUA'
    },
    {
        id: 'step3',
        enabled: true,
        init: null,
        validate: validateStep3,
        buttonText: 'CONTINUA'
    },
    {
        id: 'step4',
        enabled: true,
        init: initStep4,
        validate: validateStep4,
        buttonText: 'CONTINUA'
    },
    {
        id: 'step5',
        enabled: true,
        init: initStep5,
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
    // TODO: searching for step4 (payment step) is not general
    var stepIndex = getStepIndexById('step4');
    if (stepIndex >= 0) {
        if (product.hasOwnProperty('quantity') && product['quantity'] !== null &&
            product['quantity'].hasOwnProperty('options') && product['quantity']['options'] !== null &&
            Object.entries(product['quantity']['options']).length > 1) {
            steps[stepIndex].enabled = true && steps[stepIndex].enabled;
        }
        else {
            steps[stepIndex].enabled = false;
        }
    }
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
var upSellOptions = [];
function addUpSellOption(qty, discount, price, unitPrice, bestSeller) {
    var eOptions = document.getElementById('upSellOptions');
    if (eOptions === null) {
        return;
    }
    var eOption = document.createElement('div');
    eOption.classList.add('upSellOption');
    if (bestSeller) {
        var eBestSeller = document.createElement('div');
        eBestSeller.classList.add('upSellOptionHeader');
        eBestSeller.classList.add('w-100');
        var eBestSellerContent = document.createElement('span');
        eBestSellerContent.classList.add('upSellOptionHeaderBestSeller');
        eBestSellerContent.innerText = 'il più venduto';
        eBestSeller.appendChild(eBestSellerContent);
        eOption.appendChild(eBestSeller);
    }
    var eContent = document.createElement('div');
    eContent.classList.add('upSellOptionContent');
    eContent.classList.add('row');
    eContent.classList.add('w-100');
    var eContentQty = document.createElement('div');
    eContentQty.classList.add('col-5');
    eContentQty.classList.add('ps-5');
    eContentQty.classList.add('pt-2');
    if (parseInt(qty) === 1) {
        eContentQty.innerText = qty + ' pezzo';
    }
    else {
        eContentQty.innerText = qty + ' pezzi';
    }
    var eContentSale = document.createElement('div');
    eContentSale.classList.add('col-2');
    eContentSale.classList.add('pt-2');
    eContentSale.classList.add('pe-0');
    eContentSale.classList.add('ps-0');
    if (discount > 0) {
        var eContentSaleSpan = document.createElement('span');
        eContentSaleSpan.classList.add('upSellOptionBodyDiscount');
        eContentSaleSpan.innerText = '-' + String(discount) + '%';
        eContentSale.appendChild(eContentSaleSpan);
    }
    var eContentPrice = document.createElement('div');
    eContentPrice.classList.add('col-5');
    var eContentPrice1 = document.createElement('div');
    eContentPrice1.classList.add('upSellOptionBodyPriceAmount');
    eContentPrice1.classList.add('pt-2');
    eContentPrice1.innerText = '€' + utils.formatCurrency(price);
    eContentPrice.appendChild(eContentPrice1);
    var eContentPrice2 = document.createElement('div');
    eContentPrice2.classList.add('upSellOptionBodyPriceLabel');
    eContentPrice2.classList.add('pb-1');
    eContentPrice2.innerText = '€' + utils.formatCurrency(unitPrice) + ' al pezzo';
    eContentPrice.appendChild(eContentPrice2);
    eContent.appendChild(eContentQty);
    eContent.appendChild(eContentSale);
    eContent.appendChild(eContentPrice);
    eOption.appendChild(eContent);
    eOptions.appendChild(eOption);
    var result = {
        quantity: qty,
        element: eOption
    };
    upSellOptions.push(result);
    return result;
}
function changeQuantity(qty) {
    if (product['quantity']['options'].hasOwnProperty(qty)) {
        price = utils.getCurrencyValue(product['quantity']['options'][qty]['price']);
        quantity = qty;
    }
    total = price;
    price = price;
    total = total;
    var e = null;
    e = document.getElementById('upSellTotalAmount');
    if (e !== null) {
        e.innerText = total.toFixed(2);
    }
    e = document.getElementById('reviewTotalAmount');
    if (e !== null) {
        e.innerText = total.toFixed(2);
    }
    e = document.getElementById('reviewProductName');
    if (e !== null) {
        e.innerText = product['name'];
    }
    e = document.getElementById('reviewQuantity');
    if (e !== null) {
        e.innerText = quantity;
    }
    for (var i = 0; i < upSellOptions.length; i++) {
        var opt = upSellOptions[i];
        opt.element.classList.remove('upSellOptionActive');
        if (opt.quantity === qty) {
            opt.element.classList.add('upSellOptionActive');
        }
    }
    e = document.getElementById('orderDetailsProduct');
    if (e !== null) {
        e.innerText = product['name'];
    }
    e = document.getElementById('orderDetailsQuantity');
    if (e !== null) {
        e.innerText = quantity;
    }
    e = document.getElementById('orderDetailsProductSubtotal');
    if (e !== null) {
        e.innerText = total.toFixed(2);
    }
}
/******************************************************************************/
/**                                  PLUGINS                                 **/
/******************************************************************************/
var plugins = null;
var facebook = null;
var tiktok = null;
function initPlugins() {
    // Privacy Policy (Terms and conditions)
    utils.hideElement('termsAndConditionsDiv');
    var tcInput = document.getElementById('termsAndConditions');
    if (tcInput !== null) {
        tcInput.checked = true;
    }
    if (plugins !== null && plugins.hasOwnProperty('privacy_policy') && plugins['privacy_policy'] !== null) {
        var p = plugins['privacy_policy'];
        if (p.hasOwnProperty('enabled') && p['enabled'] === true) {
            utils.showElement('termsAndConditionsDiv');
            if (tcInput !== null) {
                tcInput.checked = false;
            }
        }
        if (p.hasOwnProperty('url') && p['url']) {
            var tcUrl = document.getElementById('termsAndConditionsUrl');
            if (tcUrl !== null) {
                tcUrl.href = p['url'];
            }
        }
    }
    // Upsell
    // TODO: id step2 is not general??
    var upsellStepIndex = getStepIndexById('step2');
    if (upsellStepIndex >= 0 && plugins !== null && plugins.hasOwnProperty('upsell') && plugins['upsell'] !== null) {
        var p = plugins['upsell'];
        if (p.hasOwnProperty('enabled') && p['enabled'] === true) {
            steps[upsellStepIndex].enabled = true;
            if (product.hasOwnProperty('quantity') && product['quantity'] !== null &&
                product['quantity'].hasOwnProperty('options') && product['quantity']['options'] !== null) {
                var counter = 0;
                for (var q in product['quantity']['options']) {
                    var qty = q;
                    var discount = 0;
                    if (typeof (product['quantity']['options'][q]['discount']) === 'string') {
                        discount = utils.getCurrencyValue(product['quantity']['options'][q]['discount']);
                    }
                    else if (product['quantity']['options'][q]['discount'] !== '') {
                        discount = product['quantity']['options'][q]['discount'];
                    }
                    var price_1 = 0;
                    if (typeof (product['quantity']['options'][q]['price']) === 'string') {
                        price_1 = utils.getCurrencyValue(product['quantity']['options'][q]['price']);
                    }
                    else {
                        price_1 = product['quantity']['options'][q]['price'];
                    }
                    var bestSeller = false;
                    if (p.hasOwnProperty('best_seller') && p['best_seller'] !== null &&
                        product['quantity']['options'].hasOwnProperty(p['best_seller'])) {
                        bestSeller = (q === p['best_seller']);
                    }
                    else {
                        bestSeller = (counter === 1);
                    }
                    var unitPrice = price_1 / utils.intval(qty);
                    addUpSellOption(qty, discount, price_1, unitPrice, bestSeller);
                    counter++;
                }
            }
        }
        else {
            steps[upsellStepIndex].enabled = false;
        }
    }
    // Facebook
    if (plugins !== null && plugins.hasOwnProperty('facebook') && plugins['facebook'] !== null) {
        var p = plugins['facebook'];
        if (p.hasOwnProperty('enabled') && p['enabled'] === true) {
            if (p.hasOwnProperty('pixel') && p['pixel'] !== null && p['pixel'] !== '' &&
                p.hasOwnProperty('access_token') && p['access_token'] !== null && p['access_token'] !== '' &&
                p.hasOwnProperty('eventValue') && p['eventValue'] !== null && p['eventValue'] !== '' &&
                p.hasOwnProperty('eventCurrency') && p['eventCurrency'] !== null && p['eventCurrency'] !== '') {
                var testCode = null;
                if (p.hasOwnProperty('test_code')) {
                    testCode = p['test_code'];
                }
                facebook = new Facebook(p['pixel'], p['access_token'], p['eventValue'], p['eventCurrency'], testCode);
            }
        }
    }
    // TikTok
    // TODO: HERE
}
/******************************************************************************/
/**                                  ONLOAD                                  **/
/******************************************************************************/
function onLoad() {
    return __awaiter(this, void 0, void 0, function () {
        var phoneInput, addressInput, elements, pluginListStr, checkout, _loop_1, i, leadId, leadInfo, fullName_1, phone_1, shipping, address, acService, res, placeId, map, pService;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    utils.loadingStart();
                    $http = axios;
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
                    // Stripe initialization
                    stripe = Stripe('pk_test_51HZhdsKZ0GYSGPZVGo4ggAfbpvnXbooyq1kATn5fn5HUcZmm15W63iUzTLztDS9iUebQr3VjkHUHJkUrYDgPMj1x00c5Xc1pyG');
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
                    // TODO:
                    // Get checkout info
                    thisScript = document.getElementById('checkout-js');
                    if (!(thisScript !== null)) return [3 /*break*/, 2];
                    productId = utils.getAttribute(thisScript, 'data-product-id');
                    pluginListStr = utils.getAttribute(thisScript, 'data-plugins');
                    if (pluginListStr !== null && pluginListStr !== '') {
                        pluginsList = utils.jsonDecode(pluginListStr);
                    }
                    return [4 /*yield*/, zarolead.getProduct(productId)];
                case 1:
                    product = _a.sent();
                    if (product.hasOwnProperty('checkout') && product['checkout'] !== null) {
                        checkout = product['checkout'];
                        if (checkout.hasOwnProperty('plugins') && checkout['plugins'] !== null) {
                            plugins = checkout['plugins'];
                        }
                    }
                    initPlugins();
                    changeQuantity(product['quantity']['default']);
                    _loop_1 = function (i) {
                        var opt = upSellOptions[i];
                        var q = opt.quantity;
                        opt.element.addEventListener('click', function (event) {
                            changeQuantity(q);
                        });
                    };
                    for (i = 0; i < upSellOptions.length; i++) {
                        _loop_1(i);
                    }
                    return [3 /*break*/, 2];
                case 2:
                    lead = null;
                    leadId = utils.getUrlParam('lid');
                    if (!(leadId !== null)) return [3 /*break*/, 6];
                    return [4 /*yield*/, zarolead.getLeadInfo(utils.strval(leadId))];
                case 3:
                    leadInfo = _a.sent();
                    if (!(leadInfo !== null)) return [3 /*break*/, 6];
                    lead = leadInfo;
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
                    // Prefill quantity
                    if (lead.hasOwnProperty('quantity') && lead['quantity'] !== null) {
                        changeQuantity(lead['quantity']);
                    }
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
