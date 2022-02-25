/******************************************************************************/
/**                                   LIBS                                   **/
/******************************************************************************/

/*
import { utils } from "./utils";
import { ZaroLead } from "./zarolead";
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

    floatval(o: any): number
    {
        return parseFloat(o);
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



class ZaroLead
{
    API_URL: string;
    API_URL_3RD: string;

    $http: any;
    httpHeaders: object;

    warehouseNetwork: string;

    constructor(warehouseNetwork: string = 'warehouse')
    {
        // TODO: Change this based on which server is hosted
        //let hostname = utils.getHostname();

        let hostname = 'https://beta2.zarolead.com';
        this.API_URL = hostname + '/api/v1/'
        this.API_URL_3RD = hostname + '/api/3rd/'

        this.warehouseNetwork = warehouseNetwork;

        /*
        this.$http = new Http();
        this.httpHeaders = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };
        */

        this.$http = axios;
    }

    setWarehouseNetwork(warehouseNetwork)
    {
        this.warehouseNetwork = warehouseNetwork;
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
            const response = await this.$http.post(this.API_URL_3RD + this.warehouseNetwork + '/leads', params, { headers: this.httpHeaders });
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
            const response = await this.$http.put(this.API_URL_3RD + this.warehouseNetwork + '/leads/' + leadId + '/update', params, { headers: this.httpHeaders });
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
            const response = await this.$http.get(this.API_URL_3RD + this.warehouseNetwork + '/leads/' + leadId + '/info', { headers: this.httpHeaders });
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
            const response = await this.$http.post(this.API_URL_3RD + this.warehouseNetwork + '/leads/' + leadId + '/address', params, { headers: this.httpHeaders });
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

    async completeLead(leadId: string, status: string, total: number, paymentMethod: string, push: string)
    {
        try {
            let params = {
                status: status,
                total: total,
                payment_method: paymentMethod,
                push: push,
            };
            const response = await this.$http.post(this.API_URL_3RD + this.warehouseNetwork + '/leads/' + leadId + '/complete', params, { headers: this.httpHeaders });
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
            const response = await this.$http.delete(this.API_URL_3RD + this.warehouseNetwork + '/leads/' + leadId + '/delete', { headers: this.httpHeaders });
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
            const response = await this.$http.get(this.API_URL_3RD + this.warehouseNetwork + '/products/' + productId + '/payment-intent', { params: params, headers: httpHeaders });
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

    async getPaymentIntent(amount: number)
    {
        var paymentIntent: object = null;
        let params = {
            amount: amount,
        };
        let httpHeaders = {
            'Accept': 'application/json',
        };

        try {
            const response = await this.$http.get(this.API_URL_3RD + this.warehouseNetwork + '/products/payment-intent', { params: params, headers: httpHeaders });
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
/**                             GLOBAL VARIABLES                             **/
/******************************************************************************/

var axios: any;
var $http: any;

let zarolead = new ZaroLead();

var thisScript: HTMLElement = null;
var leadId: string;
var lead: object|null = null;
var form: HTMLFormElement|null = null;
var products: Array<object> = [];
var accessories: Array<object> = [];

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
var stripeKey: string|null = null;
var stripe: any;
var card: any;
var stripeClientSecret: string = '';

var paymentIntent: object|null = null;

var fullName: string = '';
var phone: string = '';

var total = 0.0;

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
/**                                   STEPS                                  **/
/******************************************************************************/

async function initStep1()
{
    // TODO: HERE
}

async function initStep2()
{
    let pIntent = await zarolead.getPaymentIntent(total);
    if (pIntent !== null && pIntent.hasOwnProperty('client_secret')) {
        paymentIntent = pIntent;
        stripeClientSecret = pIntent['client_secret'];
    }
}

async function validateStep1()
{
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

        if (lead !== null) {
            const updateLeadResult = await zarolead.updateLead(utils.strval(lead['hash']), {
                first_name: firstName,
                last_name: lastName,
                phone: phone,
            });

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
    console.log('validateStep1', isValid);

    return isValid;
}

async function validateStep2()
{
    let isValid = false;

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

    // Update total and status
    if (isValid) {
        let leadUpdated = await zarolead.completeLead(lead['hash'], 'confirmed', total, 'online', 'auto');
        isValid = leadUpdated && isValid;
    }

    // DEBUG TODO: to delete
    console.log("validateStep2", isValid);

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
        init: initStep1,
        validate: validateStep1,
        buttonText: 'CONTINUA',
    },
    {
        id: 'step2',
        enabled: true,
        init: initStep2,
        validate: validateStep2,
        buttonText: 'PAGA ORA',
    },
    {
        id: 'step3',
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
    // Nothing
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
/**                                  REVIEW                                  **/
/******************************************************************************/

function addProduct(name: string, quantity: string, total: number, bold: boolean = false)
{
    var reviewDiv: HTMLElement|null = null;

    reviewDiv = document.getElementById('review');
    if (reviewDiv !== null) {
        let row = document.createElement('div');
        row.classList.add('row');
        if (bold) {
            row.classList.add('fw-bold');
        }

        let leftCol = document.createElement('div');
        leftCol.classList.add('col-9');
        leftCol.classList.add('text-start');
        let s = name
        if (quantity !== '') {
            s += ' x' + quantity;
        }
        leftCol.innerText = s;

        let rightCol = document.createElement('div');
        rightCol.classList.add('col-3');
        rightCol.classList.add('text-end');
        rightCol.innerText = total.toFixed(2) + ' €';

        row.appendChild(leftCol);
        row.appendChild(rightCol);

        reviewDiv.appendChild(row);
    }

    reviewDiv = document.getElementById('completeReview');
    if (reviewDiv !== null) {
        let row = document.createElement('div');
        row.classList.add('row');
        if (bold) {
            row.classList.add('fw-bold');
        }

        let leftCol = document.createElement('div');
        leftCol.classList.add('col-9');
        leftCol.classList.add('text-start');
        let s = name
        if (quantity !== '') {
            s += ' x' + quantity;
        }
        leftCol.innerText = s;

        let rightCol = document.createElement('div');
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


async function onLoad()
{
    utils.loadingStart();

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

    // Get checkout info
    let thisScript = document.getElementById('checkout-js');
    if (thisScript !== null) {
        leadId = utils.getAttribute(thisScript, 'data-lead-id');

        if (leadId !== null) {
            lead = await zarolead.getLeadInfo(utils.strval(leadId));
            if (lead === null) {
                zarolead.setWarehouseNetwork('dropin');
                lead = await zarolead.getLeadInfo(utils.strval(leadId));
            }
        }

        if (lead !== null && lead.hasOwnProperty('products') && lead['products'] !== null) {
            products = lead['products'] as Array<object>;
        }
        if (lead !== null && lead.hasOwnProperty('accessories') && lead['accessories'] !== null) {
            accessories = lead['accessories'] as Array<object>;
        }

        stripeKey = utils.getAttribute(thisScript, 'data-stripe-key');
    } else {
        // TODO: Abort??
    }

    // TODO: Better error handling (show message?)
    if (lead === null) {
        return;
    }

    // TODO: Better error handling (show message?)
    if (products.length == 0) {
        return;
    }

    // TODO: Check correctness of products items (they must at least have certain fields)
    // TODO: Check correctness of accessories items (they must at least have certain fields)

    for (let i = 0; i < products.length; i++) {
        let product = products[i];
        let pTotal = utils.getCurrencyValue(utils.strval(product['total']));
        addProduct(product['name'], utils.strval(product['quantity']), pTotal);
        total += pTotal;
    }

    for (let i = 0; i < accessories.length; i++) {
        let accessory = accessories[i];
        if (accessory['enabled'] !== true) {
            continue;
        }
        let aTotal = utils.getCurrencyValue(utils.strval(accessory['total']));
        addProduct(accessory['name'], '', aTotal);
        total += aTotal;
    }
    addProduct('TOTAL', '', total, true);

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

    if (lead !== null) {
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
        e.innerText = total.toFixed(2);
    }
    e = document.getElementById('orderPaymentMethod');
    if (e !== null) {
        e.innerText = 'Carta di credito'; // TODO: What about other methods?
    }

    // Initialize steps and start with first step
    initSteps();
    await nextStep();

    utils.loadingEnd();
}
