function strval(o: any): string
{
    return <string>String(o);
}

function jsonEncode(o: object): string|null
{
    var result: string|null = null;
    try {
        result = JSON.stringify(o);
    } catch (error) {
        result = null;
    }
    return result;
}

function jsonDecode(s: string): object|Array<any>|null
{
    var result: object|Array<any>|null = null;
    try {
        result = JSON.parse(s);
    } catch (error) {
        result = null;
    }
    return result;
}

function hideElement(elementId: string): void
{
    var e: HTMLElement = document.getElementById(elementId);
    if (e !== null) {
        e.style.display = 'none';
    }
}

function showElement(elementId: string): void
{
    var e: HTMLElement = document.getElementById(elementId);
    if (e !== null) {
        e.style.display = 'block';
    }
}

/*
 * TODO: Use axios as http library because this library is not async this means
 * that loading does not work properly.
 * ALTERNATIVE: Make this library asynchronous
 */

var axios: any;
var $http: any;

class Http
{
    buildQuery(obj: object|null): string
    {
        var result: string = '';

        if (obj !== null) {
            for (const [key, val] of Object.entries(obj)) {
                result += encodeURIComponent(<string>key) + '='
                result += encodeURIComponent(<string>val) + '&'
            }
        }
        if (result.length > 0) {
            result = result.substring(0, result.length - 1);
        }

        return result;
    }

    request(method: string, url: string, params: object = {}, headers: object = {}): string|null
    {
        // DEBUG TODO:
        console.log(
            'ZaroLead.request()',
            method,
            url,
            params,
            headers
        );

        var result: string|null = null;

        try {
            var req: XMLHttpRequest = new XMLHttpRequest();

            if (method.toLowerCase() === 'get') {
                url += '?' + this.buildQuery(params);
            }
            req.open(method, url, false);

            // Headers
            let fmtHeaders = {};
            for (var k in headers) {
                // TODO: Lower case is different header???
                //let fmtKey = k.toLowerCase();
                let fmtKey = k;
                fmtHeaders[fmtKey] = <string>headers[k];
                req.setRequestHeader(fmtKey, <string>fmtHeaders[fmtKey]);
            }
            headers = fmtHeaders;

            // Params query
            var query: string|null = null;
            if (method.toLowerCase() !== 'get') {
                let contentType = '';
                if (headers.hasOwnProperty('Content-Type')) {
                    contentType = headers['Content-Type'];
                }
                if (contentType === 'application/json') {
                    query = <string>jsonEncode(params);
                } else {
                    query = this.buildQuery(params);
                }
            }

            // DEBUG TODO: to delete
            console.log('query', query);

            req.send(query);
            if (req.readyState === 4) {
                result = req.response;
            }
        } catch (e) {
            // Nothing
        }
        return result;
    }

    get(url: string, params: object = {}, headers: object = {}): string|null
    {
        return this.request('GET', url, params, headers);
    }

    post(url: string, params: object = {}, headers: object = {}): string|null
    {
        // DEBUG TODO: to delete
        console.log('Http.post()');

        return this.request('POST', url, params, headers);
    }
}

class ZaroLead
{
    API_URL: string;
    API_URL_3RD: string;

    $http: any;
    httpHeaders: object;

    constructor()
    {
        let tmpUrl = new URL(window.location.href);
        this.API_URL = tmpUrl.origin + '/api/v1/'
        this.API_URL_3RD = tmpUrl.origin + '/api/3rd/'

        /*
        this.$http = new Http();
        this.httpHeaders = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };
        */

        this.$http = axios;
    }

    async createOrder(params: object)
    {
        // DEBUG TODO:
        console.log('ZaroLead.createOrder()', params);

        var order: object = null;
        try {
            const response = await this.$http.post(this.API_URL + 'orders', params, { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //order = jsonDecode(response);
                if (response.hasOwnProperty('data')) {
                    order = response['data'];
                }
                if (order === null || order.hasOwnProperty('error') || order.hasOwnProperty('errors')) {
                    order = null;
                } else if (order.hasOwnProperty('data')) {
                    order = order['data'];
                }
            }
        } catch (error) {
            order = null;
        }
        return order;
    }

    async createLead(params: object)
    {
        // DEBUG TODO:
        console.log('ZaroLead.createLead()', params);

        var lead: object = null;
        try {
            const response = await this.$http.post(this.API_URL_3RD + 'warehouse/leads', params, { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //lead = jsonDecode(response);
                if (response.hasOwnProperty('data')) {
                    lead = response['data'];
                }

                // DEBUG TODO: to delete
                console.log('response', response);
                console.log('response.hasOwnProperty(\'data\')', response.hasOwnProperty('data'));
                console.log('response.[\'data\']', response['data']);

                if (lead === null || lead.hasOwnProperty('error') || lead.hasOwnProperty('errors')) {
                    // DEBUG TODO: to delete
                    console.log('has not data', lead['data']);

                    lead = null;
                } else if (lead.hasOwnProperty('data')) {
                    // DEBUG TODO: to delete
                    console.log('has data', lead['data']);

                    lead = lead['data'];
                }
            }
        } catch (error) {
            lead = null;
        }
        return lead;
    }

    async updateLeadAddress(leadId: string, params: object)
    {
        // DEBUG TODO:
        console.log('ZaroLead.updateLeadAddress()', leadId, params);

        try {
            const response = await this.$http.post(this.API_URL_3RD + 'warehouse/leads/' + leadId + '/address', params, { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //let respJson = jsonDecode(response);
                let respJson = null;
                if (response.hasOwnProperty('data')) {
                    let respJson = response['data'];
                }
                if (respJson !== null && respJson.hasOwnProperty('status') && respJson['status'] === 'success') {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (error) {
            return false;
        }
        return false;
    }

    async completeLead(leadId: string, status: string, total: number)
    {
        // DEBUG TODO:
        console.log('ZaroLead.completeLead()', leadId, status, total);

        try {
            let params = {
                status: status,
                total: total,
            };
            const response = await this.$http.post(this.API_URL_3RD + 'warehouse/leads/' + leadId + '/complete', params, { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //let respJson = jsonDecode(response);
                let respJson = null;
                if (response.hasOwnProperty('data')) {
                    respJson = response['data'];
                }
                if (respJson !== null && respJson.hasOwnProperty('status') && respJson['status'] === 'success') {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (error) {
            return false;
        }
        return false;
    }

    async getProduct(productId: string)
    {
        // DEBUG TODO:
        console.log('ZaroLead.getProduct()', productId);

        var product: object = null;
        try {
            const response = await this.$http.get(this.API_URL + 'products/' + productId + '/info', { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //product = jsonDecode(response);
                if (response.hasOwnProperty('data')) {
                    product = response['data'];
                }
                if (product === null || product.hasOwnProperty('error') || product.hasOwnProperty('errors')) {
                    product = null;
                } else if (product.hasOwnProperty('data')) {
                    product = product['data'];
                }
            }
        } catch (error) {
            product = null;
        }
        return product;
    }

    async getPaymentIntent(productId: string, leadId: string, quantity: string)
    {
        // DEBUG TODO:
        console.log('ZaroLead.getPaymentIntent()', productId, leadId, quantity);

        var paymentIntent: object = null;
        let params = {
            lead_id: leadId,
            quantity: quantity,
        };
        let httpHeaders = {
            'Accept': 'application/json',
        };

        try {
            const response = await this.$http.get(this.API_URL_3RD + 'warehouse/products/' + productId + '/payment-intent', { params: params, headers: httpHeaders });
            if (response !== null && response !== '') {
                //paymentIntent = jsonDecode(response);
                if (response.hasOwnProperty('data')) {
                    paymentIntent = response['data'];
                }
                if (paymentIntent === null || paymentIntent.hasOwnProperty('error') || paymentIntent.hasOwnProperty('errors')) {
                    paymentIntent = null;
                } else if (paymentIntent.hasOwnProperty('data')) {
                    paymentIntent = paymentIntent['data'];
                }
            }
        } catch (error) {
            paymentIntent = null;
        }
        return paymentIntent;
    }
}

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

function validate(params: object, rules: object): object|null
{
    var result: object|null = {};

    for (var k in rules) {
        var rule: string = '';
        if (typeof(rules[k]) === 'string') {
            rule = rules[k];
        }

        var r: Array<string> = rule.split('|');
        let type = 'string';
        if (r.includes('number')) {
            type = 'number';
        }

        let required = false;
        if (r.includes('required')) {
            required = true;
        }

        if (required && !params.hasOwnProperty(k)) {
            return null;
        }

        let value: string|number = '';
        if (type === 'string') {
            value = <string>params[k];
        } else if (type === 'number') {
            if (isNaN(params[k])) {
                return null;
            }
            value = <number>Number(params[k]);
        }

        for (let i = 0; i < r.length; i++) {
            let currentRule = r[i];
            var param: any = '';

            if (currentRule === '' ||
                currentRule === 'required' ||
                currentRule === 'string' ||
                currentRule === 'number')
            {
                continue;
            }

            let tmp = currentRule.split(':');
            if (tmp.length === 0) {
                return null;
            }

            currentRule = tmp[0];
            if (tmp.length > 1) {
                param = tmp[1];
            }

            if (currentRule === 'min') {
                if (isNaN(param)) { return null; }
                let m = <number>Number(param);
                if (type === 'string') {
                    if ((<string>value).length < m) {
                        return null;
                    }
                } else if (type === 'number') {
                    if (<number>value < m) {
                        return null;
                    }
                }
            } else if (currentRule === 'max') {
                if (isNaN(param)) { return null; }
                let m = <number>Number(param);
                if (type === 'string') {
                    if ((<string>value).length > m) {
                        return null;
                    }
                } else if (type === 'number') {
                    if (<number>value > m) {
                        return null;
                    }
                }
            } else {
                return null;
            }

            result[k] = value;
        }
    }

    return result;
}

var form: HTMLFormElement = null;
let zarolead = new ZaroLead();
var lead: object|null = null;

declare var google: any;
declare var autocomplete: any;

interface GmapLocation
{
    street_number: string,
    route: string,
    area3: string,   // comune
    area2: string,   // provincia
    area1: string,   // regione
    country: string,
    postcode: string,
}

var currentLocation: GmapLocation = {
    street_number: '', // numero civico
    route: '',    // Nome via
    area3: '',    // comune
    area2: '',    // provincia
    area1: '',    // regione
    country: '',  // paese (IT)
    postcode: '', // CAP
}

declare var Stripe: any;
var stripe: any;
var card: any;
var stripeClientSecret: string = '';

var thisScript: HTMLElement = null;
var productId: string = '';
var product: object|null = null;
var pluginsList: Array<object> = [];

var fullName: string = '';
var phone: string = '';

let quantity = '';
let price = 0.0;
let total = 0.0;

function loadingStart()
{
    var loading: HTMLElement = document.getElementById('loading');
    if (loading !== null) {
        loading.style.display = 'block';
    }
}

function loadingEnd()
{
    var loading: HTMLElement = document.getElementById('loading');
    if (loading !== null) {
        loading.style.display = 'none';
    }
}

function resetCurrentLocation(): void
{
    currentLocation = {
        street_number: '',
        route: '',
        area3: '',
        area2: '',
        area1: '',
        country: '',
        postcode: '',
    }
}

function getAttribute(e: HTMLElement, name: string): string|null
{
    var result: string|null = null;
    if (e !== null && e.attributes.hasOwnProperty(name)) {
        result = <string>e.attributes[name].value;
    }
    return result;
}

function getInputValue(inputId: string): string
{
    var result: string = '';
    const e: HTMLInputElement = <HTMLInputElement>document.getElementById(inputId);
    if (e !== null) {
        result = <string>e.value;
    }
    return result;
}

function getCurrencyValue(s: string): number
{
    let value = 0.0;
    s = s.replace(',', '.');
    if (!isNaN(Number(s))) {
        value = <number>parseFloat(s);
    }
    return value;
}

function formatCurrency(n: number): string
{
    return n.toFixed(2).replace('.', ',');
}

function checkFullName(fullName: string): boolean
{
    fullName = fullName.trim();

    if (fullName.length < 5) {
        return false;
    }

    var tmp: Array<string> = fullName.split(' ');
    if (tmp.length < 2) {
        return false
    }

    var firstName: string = tmp[0];
    var i: number = 1;
    while (tmp[i] === '') {
        i++;
        if (i >= tmp.length) {
            return false;
        }
    }
    var secondName: string = tmp[i];

    if (firstName.length === 0 || secondName.length === 0) {
        return false;
    }

    return true;
}

function checkPhone(phone: string): boolean
{
    phone = phone.split(' ').join('');

    if (phone.length < 8)
    {
        return false;
    }

    for (var i: number = 0; i < phone.length; i++) {
        if (!(phone[i] >= '0' && phone[i] <= '9') &&
            phone[i] !== '+' && phone[i] !== '(' && phone[i] !== ')') {
            return false;
        }
    }

    return true;
}

function checkLocation(loc: GmapLocation): boolean
{
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

function setError(form: HTMLFormElement, inputId: string, error: string)
{
    var e: HTMLObjectElement = <HTMLObjectElement>document.getElementById(inputId);
    if (e !== null) {
        e.setCustomValidity(error);
        e.reportValidity();
        form.reportValidity();
    }
}

function clearErrors(form: HTMLFormElement)
{
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

function setStripeError(stripeErrorId: string, error: string) {
    let displayError = document.getElementById(stripeErrorId);
    displayError.textContent = error;
    if (error !== '') {
        displayError.style.display = 'block';
    } else {
        displayError.style.display = 'none';
    }
}

function fillInAddress(): void
{
    resetCurrentLocation()

    const place = autocomplete.getPlace();
    for (const component of place.address_components) {
        const componentType = component.types[0];

        if (componentType === 'street_number') {
            currentLocation.street_number = component.long_name
        } else if (componentType === 'route') {
            currentLocation.route = component.long_name
        } else if (componentType === 'administrative_area_level_3') {
            currentLocation.area3 = component.long_name
        } else if (componentType === 'administrative_area_level_2') {
            currentLocation.area2 = component.short_name
        } else if (componentType === 'administrative_area_level_1') {
            currentLocation.area1 = component.short_name
        } else if (componentType === 'country') {
            currentLocation.country = component.short_name
        } else if (componentType === 'postal_code') {
            currentLocation.postcode = component.long_name
        }
    }

    var e: HTMLInputElement = null;
    
    e = <HTMLInputElement>document.getElementById('streetAddress');
    if (e !== null) {
        e.value = currentLocation.route;
    }
    e = <HTMLInputElement>document.getElementById('streetNumber');
    if (e !== null) {
        e.value = currentLocation.street_number;
    }
    e = <HTMLInputElement>document.getElementById('cap');
    if (e !== null) {
        e.value = currentLocation.postcode;
    }
    e = <HTMLInputElement>document.getElementById('city');
    if (e !== null) {
        e.value = currentLocation.area3;
    }
    e = <HTMLInputElement>document.getElementById('province');
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

interface PaymentMethod
{
    id: string,
    fee: number,
}

var paymentMethods: Array<PaymentMethod> = [
    {
        id: 'paymentMethodCod',
        fee: 1.95,
    },
    {
        id: 'paymentMethodOnline',
        fee: 0.0,
    },
];
var paymentMethod: string = '';

async function initPaymentMethods()
{
    let e = document.getElementsByClassName('paymentMethodInputRadio');
    if (e !== null) {
        for (let i = 0; i < e.length; i++) {
            e[i].addEventListener('change', async function (event) {
                if (event !== null) {
                    changePaymentMethod(event.target['value']);
                }
            }, false);
        }
    }

    if (lead !== null && lead.hasOwnProperty('id')) {
        let paymentIntent = await zarolead.getPaymentIntent(strval(productId), strval(lead['id']), quantity);
        if (paymentIntent !== null && paymentIntent.hasOwnProperty('client_secret')) {
            stripeClientSecret = paymentIntent['client_secret'];
        }
    }
}

function changePaymentMethod(pmId: string): void
{
    // DEBUG TODO: to delete
    console.log('changePaymentMethod', pmId);

    for (let i = 0; i < paymentMethods.length; i++) {
        let pm = paymentMethods[i];
        hideElement(pm.id);

        if (pm.id === pmId) {
            let e = document.getElementById('reviewTotalAmount');
            if (e !== null) {
                e.innerText = (total + pm.fee).toFixed(2);
            }
        }
    }
    showElement(pmId);
    paymentMethod = pmId;
}

function getPaymentMethod(pmId: string): PaymentMethod|null
{
    for (let i = 0; i < paymentMethods.length; i++) {
        let pm = paymentMethods[i];
        if (pm.id === pmId) {
            return pm;
        }
    }
    return null;
}

async function initStep4()
{
    await initPaymentMethods();
    if (paymentMethods.length > 0) {
        changePaymentMethod(paymentMethods[0].id);
    }
}

async function validateStep1()
{
    loadingStart();

    let isValid = true;

    fullName = getInputValue('fullName');
    phone = forcePhoneFormat(getInputValue('phone'));
    phone = phone.split(' ').join('');

    console.log('fullName', fullName);
    console.log('phone', phone);

    // Check full name
    console.log('checkFullName', fullName, checkFullName(fullName));
    if (checkFullName(fullName)) {
        setError(form, 'fullName', '');
    } else {
        setError(form, 'fullName', 'Nome e Cognome non validi');
        isValid = false;
    }

    // Check phone
    console.log('checkPhone', phone, checkPhone(phone));
    if (checkPhone(phone)) {
        setError(form, 'phone', '');
    } else {
        setError(form, 'phone', 'Numero di telefono non valido');
        isValid = false;
    }

    isValid = isValid && (product !== null);

    if (isValid) {
        let firstName = '';
        let lastName = '';

        var tmp: Array<string> = fullName.split(' ');
        if (tmp.length > 0) {
            firstName = tmp[0];
        }
        for (let i = 1; i < tmp.length; i++) {
            lastName += tmp[i]
            if (i + 1 < tmp.length) {
                lastName += ' '
            }
        }

        lead = await zarolead.createLead({
            first_name: firstName,
            last_name: lastName,
            phone: phone,
            sku: product['sku'],
            product_name: product['name'],
            price: price.toFixed(2),
            total: total.toFixed(2),
            quantity: quantity,
        });

        // DEBUG TODO: to delete
        console.log('lead', lead);
    }

    // DEBUG TODO: to delete
    console.log('validateStep1', isValid);

    loadingEnd();

    return isValid;
}

async function validateStep2()
{
    let isValid = true;

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

    return isValid;
}

async function validateStep3()
{
    let isValid = true;

    /*
    console.log('checkLocation', currentLocation, checkLocation(currentLocation));
    if (checkLocation(currentLocation)) {
        setError(form, 'address', '');
    } else {
        setError(form, 'address', 'Indirizzo non valido');
        isValid = false;
    }
    */

    // Check address fields
    var streetAddress: string = getInputValue('streetAddress');
    var streetNumber: string = getInputValue('streetNumber');
    var cap: string = getInputValue('cap');
    var city: string = getInputValue('city');
    var province: string = getInputValue('province');
    if (streetAddress == '') {
        setError(form, 'streetAddress', 'Indirizzo non valido');
        isValid = false;
    } else {
        setError(form, 'streetAddress', '');
    }
    if (streetNumber == '') {
        setError(form, 'streetNumber', 'Indirizzo non valido');
        isValid = false;
    } else {
        setError(form, 'streetNumber', '');
    }
    if (cap == '') {
        setError(form, 'cap', 'Indirizzo non valido');
        isValid = false;
    } else {
        setError(form, 'cap', '');
    }
    if (city == '') {
        setError(form, 'city', 'Indirizzo non valido');
        isValid = false;
    } else {
        setError(form, 'city', '');
    }
    if (province == '') {
        setError(form, 'province', 'Indirizzo non valido');
        isValid = false;
    } else {
        setError(form, 'province', '');
    }

    if (isValid) {
        if (lead !== null) {
            let addressUpdated = await zarolead.updateLeadAddress(lead['id'], {
                street_address: streetAddress + ', ' + streetNumber,
                city          : city,
                province      : province,
                state         : currentLocation.area1,
                country       : 'IT',
                postcode      : cap,
            });

            // DEBUG TODO: to delete
            console.log('addressUpdated', addressUpdated);
        }
    }

    // DEBUG TODO: to delete
    console.log('validateStep1', isValid);

    return isValid;
}

async function validateStep4()
{
    // DEBUT TODO: to delete
    console.log("validateStep4()");

    let isValid = false;

    if (paymentMethod === 'paymentMethodCod') {
        isValid = true;
    } else if (paymentMethod === 'paymentMethodOnline') {
        const stripeResult = await stripe.confirmCardPayment(stripeClientSecret, {
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
                        state: currentLocation.area2,
                    },
                    phone: phone,
                },
            },
        })

        // DEBUG TODO: to delete
        console.log('stripeResult', stripeResult);

        if (stripeResult.error) {
            // DEBUG TODO: to delete
            console.log("stripeResult.error", stripeResult.error);

            setStripeError('cardErrors', stripeResult.error.message);
        } else {
            setStripeError('cardErrors', '');
            if (stripeResult.paymentIntent.status === 'succeeded') {
                isValid = true;
            }
        }
    }

    if (isValid) {
        // Update total and status
        let fee = 0.0;
        let pm = getPaymentMethod(paymentMethod);
        if (pm !== null) {
            fee = pm.fee;
        }
        for (let i = 0; i < paymentMethods.length; i++) {
            let pm = paymentMethods[i];
            if (pm.id === paymentMethod) {
                fee = pm.fee;
            }
        }
        let leadUpdated = await zarolead.completeLead(lead['id'], 'confirmed', total + fee);
        isValid = leadUpdated && isValid;
    }

    // DEBUG TODO: to delete
    console.log("validateStep4 => ", isValid);

    return isValid;
}

interface Step
{
    id: string,
    enabled: boolean,
    init: () => any,
    validate: () => any,
    buttonText: string,
}

var steps: Array<Step> = [
    {
        id: 'step1',
        enabled: true,
        init: null,
        validate: validateStep1,
        buttonText: 'CONTINUA',
    },
    {
        id: 'step2',
        enabled: true,
        init: null,
        validate: validateStep2,
        buttonText: 'CONTINUA',
    },
    {
        id: 'step3',
        enabled: true,
        init: null,
        validate: validateStep3,
        buttonText: 'CONTINUA',
    },
    {
        id: 'step4',
        enabled: true,
        init: initStep4,
        validate: validateStep4,
        buttonText: 'CONTINUA',
    },
    {
        id: 'step5',
        enabled: true,
        init: null,
        validate: null,
        buttonText: 'CONTINUA',
    },
];
var currentStep = -1;

function hideSteps(): void
{
    for (let i = 0; i < steps.length; i++) {
        hideElement(steps[i].id);
    }
}

function showStep(stepId: string): void
{
    showElement(stepId);
}

function getStepIndexById(stepId: string): number
{
    for (let i = 0; i < steps.length; i++) {
        if (steps[i].id === stepId) {
            return i;
        }
    }
    return -1;
}

function getStepById(stepId: string): Step|null
{
    for (let i = 0; i < steps.length; i++) {
        if (steps[i].id === stepId) {
            return steps[i];
        }
    }
    return null;
}

function initSteps(): void
{
    // TODO: searching for step4 (payment step) is not general
    let stepIndex = getStepIndexById('step4');

    if (stepIndex >= 0) {
        if (product.hasOwnProperty('quantity') && product['quantity'] !== null &&
            product['quantity'].hasOwnProperty('options') && product['quantity']['options'] !== null &&
            Object.entries(product['quantity']['options']).length > 1) {
            steps[stepIndex].enabled = true;
        } else {
            steps[stepIndex].enabled = false;
        }
    }
}

async function nextStep()
{
    let goToNextStep = false;
    if (currentStep < 0) {
        currentStep = -1;
        goToNextStep = true;
    } else {
        if (steps[currentStep].validate !== null) {
            loadingStart();
            const res = await steps[currentStep].validate();
            goToNextStep = (res === true);
            loadingEnd();
        }
    }

    if (goToNextStep) {
        hideSteps();
        clearErrors(form);
        currentStep += 1;
        for (let i = currentStep; i < steps.length; i++) {
            let step = steps[i];

            if (!step.enabled) {
                continue;
            }
            showStep(step.id);
            if (step.init !== null) {
                loadingStart();
                await step.init();
                loadingEnd();
            }

            let btn = document.getElementById('nextStepButton');
            if (btn !== null) {
                btn.innerText = step.buttonText;
            }
            if (step.validate === null) {
                hideElement('nextStepButton');
            } else {
                showElement('nextStepButton');
            }
            currentStep = i;
            break;
        }
    }
}

function forcePhoneFormat(val: string): string
{
    let phonePrefix = '+(39)';
    let pos = val.indexOf(phonePrefix);
    if (pos < 0) {
        val = phonePrefix + val;
    }

    let newVal = phonePrefix;
    for (let i = phonePrefix.length; i < val.length; i++) {
        if ((val[i] >= '0' && val[i] <= '9') || val[i] === ' ') {
            newVal += val[i];
        }
    }

    return newVal;
}

interface UpSellOption
{
    quantity: string;
    element: HTMLElement;
}

var upSellOptions: Array<UpSellOption> = [];

function addUpSellOption(
    qty: string,
    discount: number,
    price: number,
    bestSeller: boolean): UpSellOption
{
    let eOptions = document.getElementById('upSellOptions');
    if (eOptions === null) {
        return;
    }

    let eOption = document.createElement('div');
    eOption.classList.add('upSellOption');

    if (bestSeller) {
        let eBestSeller = document.createElement('div');
        eBestSeller.classList.add('upSellOptionHeader');
        eBestSeller.classList.add('w-100');
        let eBestSellerContent = document.createElement('span');
        eBestSellerContent.classList.add('upSellOptionHeaderBestSeller');
        eBestSellerContent.innerText = 'il più venduto';
        eBestSeller.appendChild(eBestSellerContent);
        eOption.appendChild(eBestSeller);
    }

    let eContent = document.createElement('div');
    eContent.classList.add('upSellOptionContent');
    eContent.classList.add('row');
    eContent.classList.add('w-100');

    let eContentQty = document.createElement('div');
    eContentQty.classList.add('col-5');
    eContentQty.classList.add('ps-5');
    eContentQty.classList.add('pt-2');
    if (parseInt(qty) === 1) {
        eContentQty.innerText = qty + ' pezzo';
    } else {
        eContentQty.innerText = qty + ' pezzi';
    }

    let eContentSale = document.createElement('div');
    eContentSale.classList.add('col-4');
    eContentSale.classList.add('pt-2');
    eContentSale.classList.add('pe-0');
    eContentSale.classList.add('ps-0');
    let eContentSaleSpan = document.createElement('span');
    eContentSaleSpan.classList.add('upSellOptionBodyDiscount');
    eContentSaleSpan.innerText = 'RISPARMIA ' + <string>String(discount) + '%';
    eContentSale.appendChild(eContentSaleSpan);

    let eContentPrice = document.createElement('div');
    eContentPrice.classList.add('col-3');
    let eContentPrice1 = document.createElement('div');
    eContentPrice1.classList.add('upSellOptionBodyPriceAmount');
    eContentPrice1.classList.add('pt-2');
    eContentPrice1.innerText = '€' + formatCurrency(price);
    eContentPrice.appendChild(eContentPrice1);
    let eContentPrice2 = document.createElement('div');
    eContentPrice2.classList.add('upSellOptionBodyPriceLabel');
    eContentPrice2.classList.add('pb-1');
    eContentPrice2.innerText = 'al pezzo';
    eContentPrice.appendChild(eContentPrice2);

    eContent.appendChild(eContentQty);
    eContent.appendChild(eContentSale);
    eContent.appendChild(eContentPrice);

    eOption.appendChild(eContent);
    eOptions.appendChild(eOption);

    var result: UpSellOption = {
        quantity: qty,
        element: <HTMLElement>eOption,
    }

    upSellOptions.push(result);

    return result;
}

function changeQuantity(qty: string): void
{
    if (product['quantity']['options'].hasOwnProperty(qty)) {
        price = getCurrencyValue(product['quantity']['options'][qty]['price']);
        quantity = qty;
    }
    total = price;
    price = price;
    total = total;

    var e: HTMLElement|null = null;

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

    for (let i = 0; i < upSellOptions.length; i++) {
        let opt = upSellOptions[i];
        opt.element.classList.remove('upSellOptionActive');
        if (opt.quantity === qty) {
            opt.element.classList.add('upSellOptionActive');
        }
    }
}

async function onLoad()
{
    // DEBUG TODO: to delete
    console.log('axios', axios);
    console.log('axios.get', axios.get);

    loadingStart();

    // Steps initialization
    hideSteps();

    // Form initialization
    form = <HTMLFormElement>document.getElementById('mainForm');
    if (form === null) {
        return;
    }
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        event.stopPropagation();
        form.classList.add('was-validated');
        await nextStep();
    }, false);

    // Force phone format
    let phoneInput = document.getElementById('phone');
    if (phoneInput !== null) {
        phoneInput.addEventListener('keyup', function (event) {
            let inputTarget = (<HTMLInputElement>(event.target));
            var value: string = inputTarget.value;
            value = forcePhoneFormat(value);
            inputTarget.value = value;
        });
    }

    // Google maps (address field) initialization
    var addressInput: HTMLInputElement = <HTMLInputElement>document.getElementById('address');
    addressInput.addEventListener('change', function (event) {
        resetCurrentLocation();
    });
    if (addressInput !== null) {
        autocomplete = new google.maps.places.Autocomplete(addressInput, {
            componentRestrictions: { country: ['it'] },
            fields: ['address_components'],
            types: ['address'],
        });
        autocomplete.addListener('place_changed', fillInAddress);
    }

    // Stripe initialization
    stripe = Stripe('pk_test_51HZhdsKZ0GYSGPZVGo4ggAfbpvnXbooyq1kATn5fn5HUcZmm15W63iUzTLztDS9iUebQr3VjkHUHJkUrYDgPMj1x00c5Xc1pyG');
    let elements = stripe.elements();
    card = elements.create('card', {
        classes: {
            base: 'form-control py-2',
        },
        hidePostalCode: true,
    });
    if (card !== null) {
        card.mount('#cardElement');
    }
    card.on('change', function ({error}) {
        let displayError = document.getElementById('cardErrors');
        if (error) {
            displayError.textContent = error.message;
            displayError.style.display = 'block';
        } else {
            displayError.textContent = '';
            displayError.style.display = 'none';
        }
    });

    // TODO:
    // Get form's info
    thisScript = document.getElementById('checkout-js');
    if (thisScript !== null) {
        productId = getAttribute(thisScript, 'data-product-id');
        var pluginListStr: string|null = getAttribute(thisScript, 'data-plugins');
        if (pluginListStr !== null && pluginListStr !== '') {
            pluginsList = < Array<object> > jsonDecode(pluginListStr);
        }

        product = await zarolead.getProduct(productId);

        // DEBUG TODO: to delete
        console.log('product', product);

        let counter = 0;
        for (var q in product['quantity']['options']) {
            console.log('q', q);

            let qty = q;
            let discount = 0;
            if (typeof(product['quantity']['options'][q]['discount']) === 'string') {
                discount = getCurrencyValue(product['quantity']['options'][q]['discount']);
            } else {
                discount = product['quantity']['options'][q]['discount'];
            }
            let price = 0;
            if (typeof(product['quantity']['options'][q]['price']) === 'string') {
                price = getCurrencyValue(product['quantity']['options'][q]['price']);
            } else {
                price = product['quantity']['options'][q]['price'];
            }
            addUpSellOption(qty, discount, price, (counter == 1));
            counter++;
        }

        changeQuantity(product['quantity']['default']);

        for (let i = 0; i < upSellOptions.length; i++) {
            let opt = upSellOptions[i];
            let q = opt.quantity;
            opt.element.addEventListener('click', function (event) {
                changeQuantity(q);
            });
        }

    } else {
        // TODO: Abort??
    }

    // Initialize steps and start with first step
    initSteps();
    await nextStep();

    loadingEnd();
}
