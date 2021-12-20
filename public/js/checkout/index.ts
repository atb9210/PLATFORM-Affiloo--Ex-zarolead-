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

class Utils
{
    strval(o: any): string
    {
        return <string>String(o);
    }

    intval(o: any): number
    {
        return parseInt(o);
    }

    boolval(o: any): boolean
    {
        if (typeof(o) === 'string') {
            let s = <string>o;
            if (s !== '' && s !== '0' && s !== 'false') {
                return true;
            }
        } else if (typeof(o) === 'number') {
            let n = <number>o;
            if (n != 0) {
                return true;
            }
        } else if (typeof(o) === 'boolean') {
            let b = <boolean>o;
            if (b === true) {
                return true;
            }
        }
        return false;
    }

    jsonEncode(o: object): string|null
    {
        var result: string|null = null;
        try {
            result = JSON.stringify(o);
        } catch (error) {
            result = null;
        }
        return result;
    }

    jsonDecode(s: string): object|Array<any>|null
    {
        var result: object|Array<any>|null = null;
        try {
            result = JSON.parse(s);
        } catch (error) {
            result = null;
        }
        return result;
    }

    getUrlParam(param: string): string|null
    {
        let url = new URL(window.location.href);
        return url.searchParams.get(param);
    }

    getHostname(): string
    {
        let tmpUrl = new URL(window.location.href);
        return tmpUrl.origin
    }

    getCookie(key: string): string|null
    {
        let cookiesMatch = document.cookie.match(new RegExp('(^| )' + key + '=([^;]+)'));
        if (cookiesMatch) return cookiesMatch[2];
        else return null;
    }

    hideElement(elementId: string): void
    {
        var e: HTMLElement = document.getElementById(elementId);
        if (e !== null) {
            e.style.display = 'none';
        }
    }

    showElement(elementId: string): void
    {
        var e: HTMLElement = document.getElementById(elementId);
        if (e !== null) {
            e.style.display = 'block';
        }
    }

    getAttribute(e: HTMLElement, name: string): string|null
    {
        var result: string|null = null;
        if (e !== null && e.attributes.hasOwnProperty(name)) {
            result = <string>e.attributes[name].value;
        }
        return result;
    }

    getInputValue(inputId: string): string
    {
        var result: string = '';
        const e: HTMLInputElement = <HTMLInputElement>document.getElementById(inputId);
        if (e !== null) {
            result = <string>e.value;
        }
        return result;
    }

    setInputValue(inputId: string, value: string): void
    {
        const e: HTMLInputElement = <HTMLInputElement>document.getElementById(inputId);
        if (e !== null) {
            e.value = value;
        }
    }

    getCurrencyValue(s: string): number
    {
        let value = 0.0;
        s = s.replace(',', '.');
        if (s === '') {
            value = 0.0;
        } else if (!isNaN(Number(s))) {
            value = <number>parseFloat(s);
        }
        return value;
    }

    formatCurrency(n: number): string
    {
        return n.toFixed(2).replace('.', ',');
    }

    loadingStart()
    {
        var loading: HTMLElement = document.getElementById('loading');
        if (loading !== null) {
            loading.style.display = 'block';
        }
    }

    loadingEnd()
    {
        var loading: HTMLElement = document.getElementById('loading');
        if (loading !== null) {
            loading.style.display = 'none';
        }
    }
}

let utils = new Utils();

/******************************************************************************/
/**                                 ZAROLEAD                                 **/
/******************************************************************************/

class ZaroLead
{
    API_URL: string;
    API_URL_3RD: string;

    $http: any;
    httpHeaders: object;

    constructor()
    {
        // TODO: Change this based on which server is hosted
        //let hostname = utils.getHostname();
        let hostname = 'https://beta2.zarolead.com';
        this.API_URL = hostname + '/api/v1/'
        this.API_URL_3RD = hostname + '/api/3rd/'

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
        var order: object = null;
        try {
            const response = await this.$http.post(this.API_URL + 'orders', params, { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //order = utils.jsonDecode(response);
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
        var lead: object = null;
        try {
            const response = await this.$http.post(this.API_URL_3RD + 'warehouse/leads', params, { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //lead = utils.jsonDecode(response);
                if (response.hasOwnProperty('data')) {
                    lead = response['data'];
                }

                if (lead === null || lead.hasOwnProperty('error') || lead.hasOwnProperty('errors')) {
                    lead = null;
                } else if (lead.hasOwnProperty('data')) {
                    lead = lead['data'];
                }
            }
        } catch (error) {
            lead = null;
        }
        return lead;
    }

    async updateLead(leadId: string, params: object)
    {
        try {
            const response = await this.$http.put(this.API_URL_3RD + 'warehouse/leads/' + leadId + '/update', params, { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //let respJson = utils.jsonDecode(response);
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

    async getLeadInfo(leadId: string)
    {
        var lead: object = null;
        try {
            const response = await this.$http.get(this.API_URL_3RD + 'warehouse/leads/' + leadId + '/info', { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //lead = utils.jsonDecode(response);
                if (response.hasOwnProperty('data')) {
                    lead = response['data'];
                }

                if (lead === null || lead.hasOwnProperty('error') || lead.hasOwnProperty('errors')) {
                    lead = null;
                } else if (lead.hasOwnProperty('data')) {
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
        try {
            const response = await this.$http.post(this.API_URL_3RD + 'warehouse/leads/' + leadId + '/address', params, { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //let respJson = utils.jsonDecode(response);
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

    async completeLead(leadId: string, status: string, total: number, paymentMethod: string)
    {
        try {
            let params = {
                status: status,
                total: total,
                payment_method: paymentMethod,
            };
            const response = await this.$http.post(this.API_URL_3RD + 'warehouse/leads/' + leadId + '/complete', params, { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //let respJson = utils.jsonDecode(response);
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

    async deleteLead(leadId: string)
    {
        try {
            const response = await this.$http.delete(this.API_URL_3RD + 'warehouse/leads/' + leadId + '/delete', { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //let respJson = utils.jsonDecode(response);
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
        var product: object|null = null;
        try {
            const response = await this.$http.get(this.API_URL + 'products/' + productId + '/info', { headers: this.httpHeaders });
            if (response !== null && response !== '') {
                //product = utils.jsonDecode(response);
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

    async getPaymentIntentProduct(productId: string, leadId: string, quantity: string)
    {
        var paymentIntent: object = null;
        let params = {
            lead: leadId,
            quantity: quantity,
        };
        let httpHeaders = {
            'Accept': 'application/json',
        };

        try {
            const response = await this.$http.get(this.API_URL_3RD + 'warehouse/products/' + productId + '/payment-intent', { params: params, headers: httpHeaders });
            if (response !== null && response !== '') {
                //paymentIntent = utils.jsonDecode(response);
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

    async facebookEvent(params: object)
    {
        try {
            const response = await this.$http.post(this.API_URL_3RD + 'facebook/events', params, { headers: this.httpHeaders });
            if (response !== null && response !== '' &&
                response.hasOwnProperty('status') && response['status'] === 'success') {
                return true;
            } else {
                return false;
            }
        } catch (error) {
            return false;
        }
        return false;
    }
}

/******************************************************************************/
/**                                 FACEBOOK                                 **/
/******************************************************************************/

class Facebook
{
    pixel: string;
    accessToken: string;
    eventValue: string;
    eventCurrency: string;
    testCode: string|null;

    zarolead: ZaroLead;

    constructor(pixel: string, accessToken: string,
                eventValue: string, eventCurrency: string,
                testCode: string|null = null)
    {
        this.pixel = pixel;
        this.accessToken = accessToken;
        this.eventValue = eventValue;
        this.eventCurrency = eventCurrency;
        this.testCode = testCode;

        this.zarolead = new ZaroLead();
    }

    getClickId(): string
    {
        let clickId = '';
        clickId = utils.getCookie('_fbc');
        let urlParamClickId = utils.getUrlParam('fbclid');
        if (urlParamClickId !== null && urlParamClickId !== '') {
            clickId = utils.getUrlParam('fbclid');
        }
        if (clickId === null) {
            clickId = '';
        }
        return clickId;
    }

    async triggerEvent(eventName: string, customData: object = {}, testCode: string|null = null)
    {
        let params = {
            'access_token': this.accessToken,
            'pixel': this.pixel,
            'event': eventName,
            'fbc': this.getClickId(),
            'custom_data': JSON.stringify(customData),
            'test_code': testCode,
        };
        // BUG TODO: It does not work with this code (commented). Why?
        /*
        if (testCode !== null) {
            params['test_code'] = testCode;
        }
        */

        const result = await this.zarolead.facebookEvent(params);
        return result;
    }

    async finalize()
    {
        const result = await this.triggerEvent('Purchase', {
            value: this.eventValue,
            currency: this.eventCurrency
        }, this.testCode);
        return result;
    }
}

/******************************************************************************/
/**                                  TIKTOK                                  **/
/******************************************************************************/

class TikTok
{
    // TODO
}

/******************************************************************************/
/**                             GLOBAL VARIABLES                             **/
/******************************************************************************/

var axios: any;
var $http: any;

let zarolead = new ZaroLead();

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
var stripeKey: string|null = null;
var stripe: any;
var card: any;
var stripeClientSecret: string = '';

var thisScript: HTMLElement = null;
var form: HTMLFormElement = null;
var lead: object|null = null;
var productId: string = '';
var product: object|null = null;
var paymentIntent: object|null = null;

var pluginsList: Array<object> = [];

var fullName: string = '';
var phone: string = '';

let quantity = '';
let price = 0.0;
let total = 0.0;

/******************************************************************************/
/**                              STEP VALIDATION                             **/
/******************************************************************************/

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

function changeLocation(place: object|null): void
{
    if (place === null) {
        return
    }

    if (place.hasOwnProperty('address_components') && place['address_components'] !== null) {
        for (const component of place['address_components']) {
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
}

function fillInAddress(): void
{
    resetCurrentLocation()

    const place = autocomplete.getPlace();
    changeLocation(place);
}

/******************************************************************************/
/**                              PAYMENT METHODS                             **/
/******************************************************************************/

interface PaymentMethod
{
    id: string,
    fee: number,
    name: string,
    enabled: boolean,
}

var paymentMethods: Array<PaymentMethod> = [
    {
        id: 'paymentMethodCod',
        fee: 0.0,
        name: 'Alla consegna',
        enabled: false,
    },
    {
        id: 'paymentMethodOnline',
        fee: 0.0,
        name: 'Online',
        enabled: false,
    },
];
var paymentMethod: string = '';

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

function getPaymentMethodIndex(pmId: string): number
{
    for (let i = 0; i < paymentMethods.length; i++) {
        let pm = paymentMethods[i];
        if (pm.id === pmId) {
            return i;
        }
    }
    return -1;
}

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

    if (product.hasOwnProperty('checkout') && product['checkout'] !== null) {
        let checkout = product['checkout'];
        if (checkout.hasOwnProperty('payment_methods') && checkout['payment_methods'] !== null) {
            let pms = checkout['payment_methods'];
            for (let pm in pms) {
                let pmIndex = getPaymentMethodIndex(pm);
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
                } else {
                    paymentMethods[pmIndex].enabled = false;
                    utils.hideElement(pm + 'Div');
                }
            }
        }
    }

    for (let i = 0; i < paymentMethods.length; i++) {
        let pm = paymentMethods[i];
        let e = document.getElementById(pm.id + 'Fee');
        if (e !== null) {
            e.innerText = pm.fee.toFixed(2);
        }
    }
}

function changePaymentMethod(pmId: string): void
{
    for (let i = 0; i < paymentMethods.length; i++) {
        let pm = paymentMethods[i];
        utils.hideElement(pm.id);

        if (pm.id === pmId) {
            let e = document.getElementById('reviewTotalAmount');
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

async function initStep4()
{
    await initPaymentMethods();
    if (paymentMethods.length > 0) {
        changePaymentMethod(paymentMethods[0].id);
    }
}

async function initStep5()
{
    let fee = 0.0;
    let pm = getPaymentMethod(paymentMethod);
    if (pm !== null) {
        fee = pm.fee;

        var e: HTMLElement|null = null;

        e = document.getElementById('orderNumber');
        if (e !== null) {
            e.innerText = lead['number'];
        }
        e = document.getElementById('orderDate');
        if (e !== null) {
            var dateOptions = {
                year: <"numeric"|"2-digit">'numeric',
                month: <"long"|"short"|"narrow"|"numeric"|"2-digit">'long',
                day: <"numeric"|"2-digit">'numeric',
            }
            e.innerText = (new Date()).toLocaleDateString('it-IT', dateOptions);
        }
        e = document.getElementById('orderTotal');
        if (e !== null) {
            e.innerText = (total + fee).toFixed(2);
        }
        e = document.getElementById('orderPaymentMethod');
        if (e !== null) {
            let s = pm.name;
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
}

async function validateStep1()
{
    utils.loadingStart();

    let isValid = true;

    fullName = utils.getInputValue('fullName');
    phone = forcePhoneFormat(utils.getInputValue('phone'));
    phone = phone.split(' ').join('');

    // Check full name
    if (checkFullName(fullName)) {
        setError(form, 'fullName', '');
    } else {
        setError(form, 'fullName', 'Nome e Cognome non validi');
        isValid = false;
    }

    // Check phone
    if (checkPhone(phone)) {
        setError(form, 'phone', '');
    } else {
        setError(form, 'phone', 'Numero di telefono non valido');
        isValid = false;
    }

    // Check terms and conditions
    let termsAndConditions = false;
    let termsAndConditionsInput = <HTMLInputElement>document.getElementById('termsAndConditions');
    if (termsAndConditionsInput !== null) {
        termsAndConditions = (termsAndConditionsInput.checked === true);
    }
    if (termsAndConditions) {
        setError(form, 'termsAndConditions', '');
    } else {
        setError(form, 'termsAndConditions', 'Devi accettare i termini e condizioni');
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

        let info = {
            products: [
                {
                    price: price,
                    quantity: utils.intval(quantity),
                    total: total,
                    // TODO: IMPORTANT
                    /*
                    id: elogy id,
                    name: product name,
                    sku: product sku,
                    upsell: true/false,
                    */
                }
            ],
        };

        if (lead === null) {
            lead = await zarolead.createLead({
                first_name: firstName,
                last_name: lastName,
                phone: phone,
                sku: product['sku'],
                product_name: product['name'],
                product_id: utils.intval(productId),
                price: price.toFixed(2),
                total: total.toFixed(2),
                quantity: quantity,
                status: 'abandoned',
                info: info,
            });
        } else {
            const updateLeadResult = await zarolead.updateLead(utils.strval(lead['hash']), {
                first_name: firstName,
                last_name: lastName,
                phone: phone,
            });
        }

        let fbResult = false;
        if (facebook !== null) {
            fbResult = await facebook.finalize();
        }
    }

    // DEBUG TODO: to delete
    console.log('validateStep1', isValid);

    utils.loadingEnd();

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

    if (isValid) {
        if (lead !== null && lead.hasOwnProperty('id')) {
            let pIntent = await zarolead.getPaymentIntentProduct(utils.strval(productId), utils.strval(lead['hash']), quantity);
            if (pIntent !== null && pIntent.hasOwnProperty('client_secret')) {
                paymentIntent = pIntent;
                stripeClientSecret = pIntent['client_secret'];
            } else {
                isValid = false;
            }
        } else {
            isValid = false;
        }
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
    var streetAddress: string = utils.getInputValue('streetAddress');
    var streetNumber: string = utils.getInputValue('streetNumber');
    var cap: string = utils.getInputValue('cap');
    var city: string = utils.getInputValue('city');
    var province: string = utils.getInputValue('province');
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
            let addressUpdated = await zarolead.updateLeadAddress(lead['hash'], {
                street_address: streetAddress + ', ' + streetNumber,
                city          : city,
                province      : province,
                state         : currentLocation.area1,
                country       : 'IT',
                postcode      : cap,
            });
        }
    }

    // DEBUG TODO: to delete
    console.log('validateStep3', isValid);

    return isValid;
}

async function validateStep4()
{
    let isValid = false;

    let pMethod = '';
    if (paymentMethod === 'paymentMethodCod') {
        pMethod = 'cashondelivery';
        isValid = true;
    } else if (paymentMethod === 'paymentMethodOnline') {
        pMethod = 'online';
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

        if (stripeResult.error) {
            setStripeError('cardErrors', stripeResult.error.message);
        } else {
            setStripeError('cardErrors', '');
            if (stripeResult.paymentIntent.status === 'succeeded') {
                isValid = true;
            }
        }
    }

    // Update total and status
    if (isValid) {
        let fee = 0.0;
        let pm = getPaymentMethod(paymentMethod);
        if (pm !== null) {
            fee = pm.fee;
        }
        let leadUpdated = await zarolead.completeLead(lead['hash'], 'confirmed', total + fee, pMethod);
        isValid = leadUpdated && isValid;
    }

    // DEBUG TODO: to delete
    console.log("validateStep4", isValid);

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
        enabled: false,
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
        init: initStep5,
        validate: null,
        buttonText: 'CONTINUA',
    },
];
var currentStep = -1;

function hideSteps(): void
{
    for (let i = 0; i < steps.length; i++) {
        utils.hideElement(steps[i].id);
    }
}

function showStep(stepId: string): void
{
    utils.showElement(stepId);
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
            steps[stepIndex].enabled = true && steps[stepIndex].enabled;
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
            utils.loadingStart();
            const res = await steps[currentStep].validate();
            goToNextStep = (res === true);
            utils.loadingEnd();
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
                utils.loadingStart();
                await step.init();
                utils.loadingEnd();
            }

            let btn = document.getElementById('nextStepButton');
            if (btn !== null) {
                btn.innerText = step.buttonText;
            }
            if (step.validate === null) {
                utils.hideElement('nextStepButton');
            } else {
                utils.showElement('nextStepButton');
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

/******************************************************************************/
/**                                  UPSELL                                  **/
/******************************************************************************/

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
    unitPrice: number,
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
    eContentSale.classList.add('col-2');
    eContentSale.classList.add('pt-2');
    eContentSale.classList.add('pe-0');
    eContentSale.classList.add('ps-0');
    if (discount > 0) {
        let eContentSaleSpan = document.createElement('span');
        eContentSaleSpan.classList.add('upSellOptionBodyDiscount');
        eContentSaleSpan.innerText = '-' + <string>String(discount) + '%';
        eContentSale.appendChild(eContentSaleSpan);
    }

    let eContentPrice = document.createElement('div');
    eContentPrice.classList.add('col-5');
    let eContentPrice1 = document.createElement('div');
    eContentPrice1.classList.add('upSellOptionBodyPriceAmount');
    eContentPrice1.classList.add('pt-2');
    eContentPrice1.innerText = '€' + utils.formatCurrency(price);
    eContentPrice.appendChild(eContentPrice1);
    let eContentPrice2 = document.createElement('div');
    eContentPrice2.classList.add('upSellOptionBodyPriceLabel');
    eContentPrice2.classList.add('pb-1');
    eContentPrice2.innerText = '€' + utils.formatCurrency(unitPrice) + ' al pezzo';
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
        price = utils.getCurrencyValue(product['quantity']['options'][qty]['price']);
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

var plugins: object|null = null;
var facebook: Facebook|null = null;
var tiktok: TikTok|null = null;

function initPlugins()
{
    // Privacy Policy (Terms and conditions)
    utils.hideElement('termsAndConditionsDiv');
    let tcInput = <HTMLInputElement>document.getElementById('termsAndConditions');
    if (tcInput !== null) {
        tcInput.checked = true;
    }
    if (plugins !== null && plugins.hasOwnProperty('privacy_policy') && plugins['privacy_policy'] !== null) {
        let p = plugins['privacy_policy'];
        if (p.hasOwnProperty('enabled') && p['enabled'] === true) {
            utils.showElement('termsAndConditionsDiv');
            if (tcInput !== null) {
                tcInput.checked = false;
            }
        }
        if (p.hasOwnProperty('url') && p['url']) {
            let tcUrl = <HTMLAnchorElement>document.getElementById('termsAndConditionsUrl');
            if (tcUrl !== null) {
                tcUrl.href = p['url'];
            }
        }
    }

    // Upsell
    // TODO: id step2 is not general??
    let upsellStepIndex = getStepIndexById('step2');
    if (upsellStepIndex >= 0 && plugins !== null && plugins.hasOwnProperty('upsell') && plugins['upsell'] !== null) {
        let p = plugins['upsell'];
        if (p.hasOwnProperty('enabled') && p['enabled'] === true) {
            steps[upsellStepIndex].enabled = true;

            if (product.hasOwnProperty('quantity') && product['quantity'] !== null &&
                product['quantity'].hasOwnProperty('options') && product['quantity']['options'] !== null) {
                let counter = 0;
                for (var q in product['quantity']['options']) {
                    let qty = q;
                    let discount = 0;
                    if (typeof(product['quantity']['options'][q]['discount']) === 'string') {
                        discount = utils.getCurrencyValue(product['quantity']['options'][q]['discount']);
                    } else if (product['quantity']['options'][q]['discount'] !== '') {
                        discount = product['quantity']['options'][q]['discount'];
                    }
                    let price = 0;
                    if (typeof(product['quantity']['options'][q]['price']) === 'string') {
                        price = utils.getCurrencyValue(product['quantity']['options'][q]['price']);
                    } else {
                        price = product['quantity']['options'][q]['price'];
                    }
                    let bestSeller = false;
                    if (p.hasOwnProperty('best_seller') && p['best_seller'] !== null &&
                        product['quantity']['options'].hasOwnProperty(p['best_seller'])) {
                        bestSeller = (q === p['best_seller']);
                    } else {
                        bestSeller = (counter === 1);
                    }
                    let unitPrice = price/utils.intval(qty);
                    addUpSellOption(qty, discount, price, unitPrice, bestSeller);
                    counter++;
                }
            }
        } else {
            steps[upsellStepIndex].enabled = false;
        }
    }

    // Facebook
    if (plugins !== null && plugins.hasOwnProperty('facebook') && plugins['facebook'] !== null) {
        let p = plugins['facebook'];
        if (p.hasOwnProperty('enabled') && p['enabled'] === true) {
            if (p.hasOwnProperty('pixel') && p['pixel'] !== null && p['pixel'] !== '' &&
                p.hasOwnProperty('access_token') && p['access_token'] !== null && p['access_token'] !== '' &&
                p.hasOwnProperty('eventValue') && p['eventValue'] !== null && p['eventValue'] !== '' &&
                p.hasOwnProperty('eventCurrency') && p['eventCurrency'] !== null && p['eventCurrency'] !== '') {
                var testCode: string|null = null;
                if (p.hasOwnProperty('test_code')) {
                    testCode = p['test_code'];
                }

                facebook = new Facebook(p['pixel'], p['access_token'],
                                        p['eventValue'], p['eventCurrency'],
                                        testCode);
            }
        }
    }

    // TikTok
    // TODO: HERE
}

/******************************************************************************/
/**                                  ONLOAD                                  **/
/******************************************************************************/

async function onLoad()
{
    utils.loadingStart();

    $http = axios;

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

    // TODO:
    // Get checkout info
    thisScript = document.getElementById('checkout-js');
    if (thisScript !== null) {
        productId = utils.getAttribute(thisScript, 'data-product-id');
        var pluginListStr: string|null = utils.getAttribute(thisScript, 'data-plugins');
        if (pluginListStr !== null && pluginListStr !== '') {
            pluginsList = < Array<object> > utils.jsonDecode(pluginListStr);
        }

        product = await zarolead.getProduct(productId);

        if (product.hasOwnProperty('checkout') && product['checkout'] !== null) {
            let checkout = product['checkout'];
            if (checkout.hasOwnProperty('plugins') && checkout['plugins'] !== null) {
                plugins = <object>checkout['plugins'];
            }
        }

        stripeKey = utils.getAttribute(thisScript, 'data-stripe-key');

        initPlugins();

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

    // Stripe initialization
    if (stripeKey !== null) {
        stripe = Stripe(stripeKey);
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
    } else {
        // TODO: Error handling
    }

    lead = null;
    let leadId = utils.getUrlParam('lid');
    if (leadId !== null) {
        let leadInfo = await zarolead.getLeadInfo(utils.strval(leadId));
        if (leadInfo !== null) {
            lead = leadInfo;

            // Prefill name and phone
            let fullName = '';
            if (lead.hasOwnProperty('first_name') && lead['first_name'] !== null) {
                fullName += lead['first_name'];
            }
            if (lead.hasOwnProperty('last_name') && lead['last_name'] !== null) {
                fullName += ' ' + lead['last_name'];
            }
            utils.setInputValue('fullName', fullName);

            let phone = '';
            if (lead.hasOwnProperty('phone') && lead['phone'] !== null) {
                phone = lead['phone'];
            }
            utils.setInputValue('phone', phone);

            // Prefill quantity
            if (lead.hasOwnProperty('quantity') && lead['quantity'] !== null) {
                changeQuantity(lead['quantity']);
            }

            // Prefill address
            if (lead.hasOwnProperty('shipping') && lead['shipping'] !== null) {
                let shipping = lead['shipping'];
                if (shipping.hasOwnProperty('street_address') && shipping['street_address'] !== null &&
                    shipping.hasOwnProperty('city') && shipping['city'] !== null &&
                    shipping.hasOwnProperty('province') && shipping['province'] !== null &&
                    shipping.hasOwnProperty('state') && shipping['state'] !== null) {

                    let address = shipping['street_address'] + ', ' + shipping['city'] + ', ' + shipping['province'] + ', ' + shipping['state'];

                    utils.setInputValue('address', address);
                    if (autocomplete !== null) {
                        autocomplete.set(address);
                        //google.maps.event.trigger(autocomplete, 'place_changed');
                        //const res = await google.maps.places.AutocompletionRequest.getPlacePredictions({ input: address});
                        //const res = await autocomplete.getPlacePredictions({ input: address});

                        let acService = new google.maps.places.AutocompleteService();
                        const res = await acService.getPlacePredictions({ input: address});

                        let placeId = res['predictions'][0]['place_id'];

                        const map = new google.maps.Map(document.getElementById('map'), {
                            center: new google.maps.LatLng(-33.8665433,151.1956316),
                            zoom: 15,
                        });
                        let pService = new google.maps.places.PlacesService(map);
                        await pService.getDetails({
                            placeId: placeId,
                            componentRestrictions: { country: ['it'] },
                            fields: ['address_components'],
                            types: ['address'],
                        }, function (place, status) {
                            if (status == google.maps.places.PlacesServiceStatus.OK) {
                                changeLocation(place);
                            }
                        });
                    }
                }
            }
        }
    }

    // Initialize steps and start with first step
    initSteps();
    await nextStep();

    utils.loadingEnd();
}
