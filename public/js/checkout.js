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
function jsonEncode(o) {
    var result = null;
    try {
        result = JSON.stringify(o);
    }
    catch (error) {
        result = null;
    }
    return result;
}
function jsonDecode(s) {
    var result = null;
    try {
        result = JSON.parse(s);
    }
    catch (error) {
        result = null;
    }
    return result;
}
var Http = /** @class */ (function () {
    function Http() {
    }
    Http.prototype.buildQuery = function (obj) {
        var result = '';
        if (obj !== null) {
            for (var _i = 0, _a = Object.entries(obj); _i < _a.length; _i++) {
                var _b = _a[_i], key = _b[0], val = _b[1];
                result += encodeURIComponent(key) + '=';
                result += encodeURIComponent(val) + '&';
            }
        }
        if (result.length > 0) {
            result = result.substring(0, result.length - 1);
        }
        return result;
    };
    Http.prototype.request = function (method, url, params, headers) {
        if (params === void 0) { params = {}; }
        if (headers === void 0) { headers = {}; }
        // DEBUG TODO:
        console.log('ZaroLead.request()', method, url, params, headers);
        var result = null;
        try {
            var req = new XMLHttpRequest();
            if (method.toLowerCase() === 'get') {
                url += '?' + this.buildQuery(params);
            }
            req.open(method, url, false);
            // Headers
            var fmtHeaders = {};
            for (var k in headers) {
                // TODO: Lower case is different header???
                //let fmtKey = k.toLowerCase();
                var fmtKey = k;
                fmtHeaders[fmtKey] = headers[k];
                req.setRequestHeader(fmtKey, fmtHeaders[fmtKey]);
            }
            headers = fmtHeaders;
            // Params query
            var query = null;
            if (method.toLowerCase() !== 'get') {
                var contentType = '';
                if (headers.hasOwnProperty('Content-Type')) {
                    contentType = headers['Content-Type'];
                }
                if (contentType === 'application/json') {
                    query = jsonEncode(params);
                }
                else {
                    query = this.buildQuery(params);
                }
            }
            // DEBUG TODO: to delete
            console.log('query', query);
            req.send(query);
            if (req.readyState === 4) {
                result = req.response;
            }
        }
        catch (e) {
            // Nothing
        }
        return result;
    };
    Http.prototype.get = function (url, params, headers) {
        if (params === void 0) { params = {}; }
        if (headers === void 0) { headers = {}; }
        return this.request('GET', url, params, headers);
    };
    Http.prototype.post = function (url, params, headers) {
        if (params === void 0) { params = {}; }
        if (headers === void 0) { headers = {}; }
        // DEBUG TODO: to delete
        console.log('Http.post()');
        return this.request('POST', url, params, headers);
    };
    return Http;
}());
var ZaroLead = /** @class */ (function () {
    function ZaroLead() {
        var tmpUrl = new URL(window.location.href);
        this.API_URL = tmpUrl.origin + '/api/v1/';
        this.API_URL_3RD = tmpUrl.origin + '/api/3rd/';
        this.$http = new Http();
        this.httpHeaders = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
    }
    ZaroLead.prototype.createOrder = function (params) {
        // DEBUG TODO:
        console.log('ZaroLead.createOrder()', params);
        var order = null;
        var response = this.$http.post(this.API_URL + 'orders', params, this.httpHeaders);
        if (response !== null && response !== '') {
            try {
                order = jsonDecode(response);
                if (order !== null && order.hasOwnProperty('error') || order.hasOwnProperty('errors')) {
                    order = null;
                }
                else if (order.hasOwnProperty('data')) {
                    order = order['data'];
                }
            }
            catch (error) {
                order = null;
            }
        }
        return order;
    };
    ZaroLead.prototype.createLead = function (params) {
        // DEBUG TODO:
        console.log('ZaroLead.createLead()', params);
        var lead = null;
        var response = this.$http.post(this.API_URL_3RD + 'warehouse/leads', params, this.httpHeaders);
        if (response !== null && response !== '') {
            try {
                lead = jsonDecode(response);
                if (lead !== null && lead.hasOwnProperty('error') || lead.hasOwnProperty('errors')) {
                    lead = null;
                }
                else if (lead.hasOwnProperty('data')) {
                    lead = lead['data'];
                }
            }
            catch (error) {
                lead = null;
            }
        }
        return lead;
    };
    ZaroLead.prototype.getProduct = function (productId) {
        // DEBUG TODO:
        console.log('ZaroLead.getProduct()', productId);
        var product = null;
        var response = this.$http.get(this.API_URL + 'products/' + productId + '/info', {}, this.httpHeaders);
        if (response !== null && response !== '') {
            try {
                product = jsonDecode(response);
                if (product !== null && product.hasOwnProperty('error') || product.hasOwnProperty('errors')) {
                    product = null;
                }
                else if (product.hasOwnProperty('data')) {
                    product = product['data'];
                }
            }
            catch (error) {
                product = null;
            }
        }
        return product;
    };
    ZaroLead.prototype.getPaymentIntent = function (productId, quantity) {
        // DEBUG TODO:
        console.log('ZaroLead.getPaymentIntent()', productId);
        var paymentIntent = null;
        var params = {
            quantity: quantity
        };
        var httpHeaders = {
            'Accept': 'application/json'
        };
        var response = this.$http.get(this.API_URL_3RD + 'warehouse/products/' + productId + '/payment-intent', params, httpHeaders);
        if (response !== null && response !== '') {
            try {
                paymentIntent = jsonDecode(response);
                if (paymentIntent !== null && paymentIntent.hasOwnProperty('error') || paymentIntent.hasOwnProperty('errors')) {
                    paymentIntent = null;
                }
                else if (paymentIntent.hasOwnProperty('data')) {
                    paymentIntent = paymentIntent['data'];
                }
            }
            catch (error) {
                paymentIntent = null;
            }
        }
        return paymentIntent;
    };
    return ZaroLead;
}());
/*

Example:

let params = {
    name: 'John Doe',
    quantity: 1,
}

let rules = {
    name: 'required|string|min:4',
    quantity: 'required|number|min:1',
}

*/
function validate(params, rules) {
    var result = {};
    for (var k in rules) {
        var rule = '';
        if (typeof (rules[k]) === 'string') {
            rule = rules[k];
        }
        var r = rule.split('|');
        var type = 'string';
        if (r.includes('number')) {
            type = 'number';
        }
        var required = false;
        if (r.includes('required')) {
            required = true;
        }
        if (required && !params.hasOwnProperty(k)) {
            return null;
        }
        var value = '';
        if (type === 'string') {
            value = params[k];
        }
        else if (type === 'number') {
            if (isNaN(params[k])) {
                return null;
            }
            value = Number(params[k]);
        }
        for (var i = 0; i < r.length; i++) {
            var currentRule = r[i];
            var param = '';
            if (currentRule === '' ||
                currentRule === 'required' ||
                currentRule === 'string' ||
                currentRule === 'number') {
                continue;
            }
            var tmp = currentRule.split(':');
            if (tmp.length === 0) {
                return null;
            }
            currentRule = tmp[0];
            if (tmp.length > 1) {
                param = tmp[1];
            }
            if (currentRule === 'min') {
                if (isNaN(param)) {
                    return null;
                }
                var m = Number(param);
                if (type === 'string') {
                    if (value.length < m) {
                        return null;
                    }
                }
                else if (type === 'number') {
                    if (value < m) {
                        return null;
                    }
                }
            }
            else if (currentRule === 'max') {
                if (isNaN(param)) {
                    return null;
                }
                var m = Number(param);
                if (type === 'string') {
                    if (value.length > m) {
                        return null;
                    }
                }
                else if (type === 'number') {
                    if (value > m) {
                        return null;
                    }
                }
            }
            else {
                return null;
            }
            result[k] = value;
        }
    }
    return result;
}
var form = null;
var $http = new Http();
var zarolead = new ZaroLead();
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
var stripe;
var card;
var stripeClientSecret = '';
var thisScript = null;
var productId = '';
var product = null;
var pluginsList = [];
var fullName = '';
var phone = '';
var quantity = '';
var price = 0.0;
var total = 0.0;
function loadingStart() {
    var loading = document.getElementById('loading');
    if (loading !== null) {
        loading.style.display = 'block';
    }
}
function loadingEnd() {
    var loading = document.getElementById('loading');
    if (loading !== null) {
        loading.style.display = 'none';
    }
}
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
function getAttribute(e, name) {
    var result = null;
    if (e !== null && e.attributes.hasOwnProperty(name)) {
        result = e.attributes[name].value;
    }
    return result;
}
function getInputValue(inputId) {
    var result = '';
    var e = document.getElementById(inputId);
    if (e !== null) {
        result = e.value;
    }
    return result;
}
function getCurrencyValue(s) {
    var value = 0.0;
    s = s.replace(',', '.');
    if (!isNaN(Number(s))) {
        value = parseFloat(s);
    }
    return value;
}
function formatCurrency(n) {
    return n.toFixed(2).replace('.', ',');
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
function submit(form) {
    return __awaiter(this, void 0, void 0, function () {
        var stripeResult;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    loadingStart();
                    fullName = getInputValue('fullName');
                    phone = forcePhoneFormat(getInputValue('phone'));
                    phone = phone.split(' ').join('');
                    console.log('fullName', fullName);
                    console.log('phone', phone);
                    console.log('currentLocation', currentLocation);
                    // Check full name
                    console.log('checkFullName', fullName, checkFullName(fullName));
                    if (!checkFullName(fullName)) {
                        setError(form, 'fullName', 'Nome e Cognome non validi');
                    }
                    else {
                        setError(form, 'fullName', '');
                    }
                    // Check phone
                    console.log('checkPhone', phone, checkPhone(phone));
                    if (!checkPhone(phone)) {
                        setError(form, 'phone', 'Numero di telefono non valido');
                    }
                    else {
                        setError(form, 'phone', '');
                    }
                    // Check address
                    console.log('checkLocation', currentLocation, checkLocation(currentLocation));
                    if (!checkLocation(currentLocation)) {
                        setError(form, 'address', 'Indirizzo non valido');
                    }
                    else {
                        setError(form, 'address', '');
                    }
                    if (!form.checkValidity()) {
                        loadingEnd();
                        return [2 /*return*/];
                    }
                    // DEBUG TODO: to delete
                    console.log('currentLocation', currentLocation);
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
                    console.log('stripeResult', stripeResult);
                    if (stripeResult.error) {
                        setStripeError('cardErrors', stripeResult.error.message);
                    }
                    else {
                        setStripeError('cardErrors', '');
                        if (stripeResult.paymentIntent.status === 'succeeded') {
                            // TODO: HERE
                            console.log('COOL DONE FUCK YEAH!');
                        }
                    }
                    loadingEnd();
                    return [2 /*return*/];
            }
        });
    });
}
function fillInAddress() {
    resetCurrentLocation();
    var place = autocomplete.getPlace();
    for (var _i = 0, _a = place.address_components; _i < _a.length; _i++) {
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
function initStep4() {
    return __awaiter(this, void 0, void 0, function () {
        var paymentIntent;
        return __generator(this, function (_a) {
            paymentIntent = zarolead.getPaymentIntent(productId, quantity);
            if (paymentIntent !== null && paymentIntent.hasOwnProperty('client_secret')) {
                stripeClientSecret = paymentIntent['client_secret'];
            }
            return [2 /*return*/];
        });
    });
}
function validateStep1() {
    return __awaiter(this, void 0, void 0, function () {
        var isValid, firstName, lastName, tmp, i;
        return __generator(this, function (_a) {
            isValid = true;
            fullName = getInputValue('fullName');
            phone = forcePhoneFormat(getInputValue('phone'));
            phone = phone.split(' ').join('');
            console.log('fullName', fullName);
            console.log('phone', phone);
            // Check full name
            console.log('checkFullName', fullName, checkFullName(fullName));
            if (checkFullName(fullName)) {
                setError(form, 'fullName', '');
            }
            else {
                setError(form, 'fullName', 'Nome e Cognome non validi');
                isValid = false;
            }
            // Check phone
            console.log('checkPhone', phone, checkPhone(phone));
            if (checkPhone(phone)) {
                setError(form, 'phone', '');
            }
            else {
                setError(form, 'phone', 'Numero di telefono non valido');
                isValid = false;
            }
            isValid = isValid && (product !== null);
            if (isValid) {
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
                lead = zarolead.createLead({
                    first_name: firstName,
                    last_name: lastName,
                    phone: phone,
                    sku: product['sku'],
                    product_name: product['name'],
                    price: price.toFixed(2),
                    total: total.toFixed(2),
                    quantity: quantity
                });
                // DEBUG TODO: to delete
                console.log('lead', lead);
            }
            // DEBUG TODO: to delete
            console.log('validateStep1', isValid);
            return [2 /*return*/, isValid];
        });
    });
}
function validateStep2() {
    return __awaiter(this, void 0, void 0, function () {
        var isValid;
        return __generator(this, function (_a) {
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
            // DEBUG TODO: to delete
            console.log("validateStep2", isValid);
            return [2 /*return*/, isValid];
        });
    });
}
function validateStep3() {
    return __awaiter(this, void 0, void 0, function () {
        var isValid, streetAddress, streetNumber, cap, city, province;
        return __generator(this, function (_a) {
            isValid = true;
            streetAddress = getInputValue('streetAddress');
            streetNumber = getInputValue('streetNumber');
            cap = getInputValue('cap');
            city = getInputValue('city');
            province = getInputValue('province');
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
            // DEBUG TODO: to delete
            console.log('validateStep1', isValid);
            return [2 /*return*/, isValid];
        });
    });
}
function validateStep4() {
    return __awaiter(this, void 0, void 0, function () {
        var isValid, stripeResult;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    // DEBUT TODO: to delete
                    console.log("validateStep4()");
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
                        })
                        // DEBUG TODO: to delete
                    ];
                case 1:
                    stripeResult = _a.sent();
                    // DEBUG TODO: to delete
                    console.log('stripeResult', stripeResult);
                    if (stripeResult.error) {
                        // DEBUG TODO: to delete
                        console.log("stripeResult.error", stripeResult.error);
                        setStripeError('cardErrors', stripeResult.error.message);
                    }
                    else {
                        setStripeError('cardErrors', '');
                        if (stripeResult.paymentIntent.status === 'succeeded') {
                            isValid = true;
                        }
                    }
                    // DEBUG TODO: to delete
                    console.log("validateStep4 => ", isValid);
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
        buttonText: 'Continua'
    },
    {
        id: 'step2',
        enabled: true,
        init: null,
        validate: validateStep2,
        buttonText: 'Continua'
    },
    {
        id: 'step3',
        enabled: true,
        init: null,
        validate: validateStep3,
        buttonText: 'Continua'
    },
    {
        id: 'step4',
        enabled: true,
        init: initStep4,
        validate: validateStep4,
        buttonText: 'Continua'
    },
    {
        id: 'step5',
        enabled: true,
        init: null,
        validate: null,
        buttonText: 'Continua'
    },
];
var currentStep = -1;
function hideElement(elementId) {
    var e = document.getElementById(elementId);
    if (e !== null) {
        e.style.display = 'none';
    }
}
function showElement(elementId) {
    var e = document.getElementById(elementId);
    if (e !== null) {
        e.style.display = 'block';
    }
}
function hideSteps() {
    for (var i = 0; i < steps.length; i++) {
        hideElement(steps[i].id);
    }
}
function showStep(stepId) {
    showElement(stepId);
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
            steps[stepIndex].enabled = true;
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
                    loadingStart();
                    return [4 /*yield*/, steps[currentStep].validate()];
                case 2:
                    res = _a.sent();
                    goToNextStep = (res === true);
                    loadingEnd();
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
                    return [4 /*yield*/, step.init()];
                case 5:
                    _a.sent();
                    _a.label = 6;
                case 6:
                    btn = document.getElementById('nextStepButton');
                    if (btn !== null) {
                        btn.innerText = step.buttonText;
                    }
                    if (step.validate === null) {
                        hideElement('nextStepButton');
                    }
                    else {
                        showElement('nextStepButton');
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
function addUpSellOption(qty, discount, price, bestSeller) {
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
    eContentSale.classList.add('col-4');
    eContentSale.classList.add('pt-2');
    eContentSale.classList.add('pe-0');
    eContentSale.classList.add('ps-0');
    var eContentSaleSpan = document.createElement('span');
    eContentSaleSpan.classList.add('upSellOptionBodyDiscount');
    eContentSaleSpan.innerText = 'RISPARMIA ' + String(discount) + '%';
    eContentSale.appendChild(eContentSaleSpan);
    var eContentPrice = document.createElement('div');
    eContentPrice.classList.add('col-3');
    var eContentPrice1 = document.createElement('div');
    eContentPrice1.classList.add('upSellOptionBodyPriceAmount');
    eContentPrice1.classList.add('pt-2');
    eContentPrice1.innerText = '€' + formatCurrency(price);
    eContentPrice.appendChild(eContentPrice1);
    var eContentPrice2 = document.createElement('div');
    eContentPrice2.classList.add('upSellOptionBodyPriceLabel');
    eContentPrice2.classList.add('pb-1');
    eContentPrice2.innerText = 'al pezzo';
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
        price = getCurrencyValue(product['quantity']['options'][qty]['price']);
        quantity = qty;
    }
    total = price;
    price = price;
    total = total;
    var e = document.getElementById('upSellTotalAmount');
    if (e !== null) {
        e.innerText = total.toFixed(2);
    }
    for (var i = 0; i < upSellOptions.length; i++) {
        var opt = upSellOptions[i];
        opt.element.classList.remove('upSellOptionActive');
        if (opt.quantity === qty) {
            opt.element.classList.add('upSellOptionActive');
        }
    }
}
function onLoad() {
    return __awaiter(this, void 0, void 0, function () {
        var phoneInput, addressInput, elements, pluginListStr, counter, q, qty, discount, price_1, _loop_1, i, response, obj;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    loadingStart();
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
                            base: 'form-control'
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
                    // Get form's info
                    thisScript = document.getElementById('checkout-js');
                    if (thisScript !== null) {
                        productId = getAttribute(thisScript, 'data-product-id');
                        pluginListStr = getAttribute(thisScript, 'data-plugins');
                        if (pluginListStr !== null && pluginListStr !== '') {
                            pluginsList = jsonDecode(pluginListStr);
                        }
                        product = zarolead.getProduct(productId);
                        counter = 0;
                        for (q in product['quantity']['options']) {
                            console.log('q', q);
                            qty = q;
                            discount = 0;
                            if (typeof (product['quantity']['options'][q]['discount']) === 'string') {
                                discount = getCurrencyValue(product['quantity']['options'][q]['discount']);
                            }
                            else {
                                discount = product['quantity']['options'][q]['discount'];
                            }
                            price_1 = 0;
                            if (typeof (product['quantity']['options'][q]['price']) === 'string') {
                                price_1 = getCurrencyValue(product['quantity']['options'][q]['price']);
                            }
                            else {
                                price_1 = product['quantity']['options'][q]['price'];
                            }
                            addUpSellOption(qty, discount, price_1, (counter == 1));
                            counter++;
                        }
                        changeQuantity(product['quantity']['default']);
                        _loop_1 = function (i) {
                            var opt = upSellOptions[i];
                            var q_1 = opt.quantity;
                            opt.element.addEventListener('click', function (event) {
                                changeQuantity(q_1);
                            });
                        };
                        for (i = 0; i < upSellOptions.length; i++) {
                            _loop_1(i);
                        }
                    }
                    else {
                        // TODO: Abort??
                    }
                    response = $http.get('/test');
                    obj = null;
                    if (response !== null && response !== '') {
                        obj = jsonDecode(response);
                    }
                    if (obj !== null && obj.hasOwnProperty('data') && obj.data !== null &&
                        obj.data.hasOwnProperty('client_secret')) {
                        stripeClientSecret = obj.data.client_secret;
                    }
                    // Initialize steps and start with first step
                    initSteps();
                    return [4 /*yield*/, nextStep()];
                case 1:
                    _a.sent();
                    loadingEnd();
                    return [2 /*return*/];
            }
        });
    });
}
