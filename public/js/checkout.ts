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
            req.open(method, url, false);

            // Headers
            let fmtHeaders = {};
            for (var k in headers) {
                let fmtKey = k.toLowerCase();
                fmtHeaders[fmtKey] = <string>headers[k];
                req.setRequestHeader(fmtKey, <string>fmtHeaders[fmtKey]);
            }
            headers = fmtHeaders;

            // Params query
            let contentType = '';
            if (headers.hasOwnProperty('content-type')) {
                contentType = headers['content-type'];
            }
            var query: string = this.buildQuery(params);
            if (contentType === 'application/json') {
                query = <string>jsonEncode(params);
            }

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

    $http: Http;
    httpHeaders: object;

    constructor()
    {
        let tmpUrl = new URL(window.location.href);
        this.API_URL = tmpUrl.origin + '/api/v1/'
        this.API_URL_3RD = tmpUrl.origin + '/api/3rd/'

        this.$http = new Http();
        this.httpHeaders = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };
    }

    createOrder(params: object): object|null
    {
        // DEBUG TODO:
        console.log('ZaroLead.createOrder()', params);

        var order: object = null;
        const response = this.$http.post(this.API_URL + 'orders', params, this.httpHeaders);

        // DEBUG TODO:
        console.log('response', response);

        if (response !== null && response !== '') {
            try {
                order = jsonDecode(response);
                if (order !== null && order.hasOwnProperty('error') || order.hasOwnProperty('errors')) {
                    order = null;
                } else if (order.hasOwnProperty('data')) {
                    order = order['data'];
                }
            } catch (error) {
                order = null;
            }
        }
        return order;
    }

    createLead(params: object): object|null
    {
        // DEBUG TODO:
        console.log('ZaroLead.createLead()', params);

        var lead: object = null;
        const response = this.$http.post(this.API_URL_3RD + 'warehouse/leads', params, this.httpHeaders);

        // DEBUG TODO:
        console.log('response', response);

        if (response !== null && response !== '') {
            try {
                lead = jsonDecode(response);
                if (lead !== null && lead.hasOwnProperty('error') || lead.hasOwnProperty('errors')) {
                    lead = null;
                } else if (lead.hasOwnProperty('data')) {
                    lead = lead['data'];
                }
            } catch (error) {
                lead = null;
            }
        }
        return lead;
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
let $http = new Http();
let zarolead = new ZaroLead();
var lead: object|null = null;

declare var google: any;
declare var autocomplete: any;

interface GmapLocation {
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
var formId: string = '';
var pluginsList: Array<object> = [];

var fullName: string = '';
var phone: string = '';

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

async function submit(form: HTMLFormElement)
{
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
    } else {
        setError(form, 'fullName', '');
    }

    // Check phone
    console.log('checkPhone', phone, checkPhone(phone));
    if (!checkPhone(phone)) {
        setError(form, 'phone', 'Numero di telefono non valido');
    } else {
        setError(form, 'phone', '');
    }

    // Check address
    console.log('checkLocation', currentLocation, checkLocation(currentLocation));
    if (!checkLocation(currentLocation)) {
        setError(form, 'address', 'Indirizzo non valido');
    } else {
        setError(form, 'address', '');
    }

    if (!form.checkValidity()) {
        loadingEnd();
        return;
    }

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
    });
    console.log('stripeResult', stripeResult);

    if (stripeResult.error) {
        setStripeError('cardErrors', stripeResult.error.message);
    } else {
        setStripeError('cardErrors', '');
        if (stripeResult.paymentIntent.status === 'succeeded') {
            // TODO: HERE
            console.log('COOL DONE FUCK YEAH!');
        }
    }

    loadingEnd();
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

async function validateStep1()
{
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

        // TODO:
        let sku = 'sku123';
        let productName = 'my product';
        let price = 19.99;
        let total = 19.99;
        let quantity = 1;

        lead = zarolead.createLead({
            first_name: firstName,
            last_name: lastName,
            phone: phone,
            sku: sku,
            product_name: productName,
            price: price,
            total: total,
            quantity: quantity,
        });

        // DEBUG TODO: to delete
        console.log('lead', lead);
    }

    // DEBUG TODO: to delete
    console.log('validateStep1', isValid);

    return isValid;
}

async function validateStep2()
{
    // TODO
    return false;
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

    // DEBUG TODO: to delete
    console.log('validateStep1', isValid);

    return isValid;
}

async function validateStep4()
{
    // DEBUT TODO: to delete
    console.log("validateStep4()");

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

    // DEBUG TODO: to delete
    console.log("validateStep4 => ", isValid);

    return isValid;
}

interface Step {
    id: string,
    enabled: boolean,
    validate: () => any,
}

var steps: Array<Step> = [
    {
        id: 'step1',
        enabled: true,
        validate: validateStep1,
    },
    {
        id: 'step2',
        enabled: false,
        validate: validateStep2,
    },
    {
        id: 'step3',
        enabled: true,
        validate: validateStep3,
    },
    {
        id: 'step4',
        enabled: true,
        validate: validateStep4,
    },
    {
        id: 'step5',
        enabled: true,
        validate: null,
    },
];
var currentStep = -1;

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
            if (!steps[i].enabled) {
                continue;
            }
            showStep(steps[i].id);
            if (steps[i].validate === null) {
                hideElement('nextStepButton');
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

async function onLoad()
{
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
            base: 'form-control',
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
    thisScript = document.getElementById('form-js');
    if (thisScript !== null) {
        formId = getAttribute(thisScript, 'data-form-id');
        var pluginListStr: string|null = getAttribute(thisScript, 'data-plugins');
        if (pluginListStr !== null && pluginListStr !== '') {
            pluginsList = < Array<object> > jsonDecode(pluginListStr);
        }

        // DEBUG TODO: to delete
        console.log('formId', formId);
        console.log('pluginsList', pluginsList);
    } else {
        // TODO: Abort??
    }
    // TODO:
    const response: string|null = $http.get('/test');
    let obj = null;
    if (response !== null && response !== '') {
        obj = jsonDecode(response);
    }
    if (obj !== null && obj.hasOwnProperty('data') && obj.data !== null &&
        obj.data.hasOwnProperty('client_secret')) {
        stripeClientSecret = obj.data.client_secret;
    }

    // Start with first step
    await nextStep();

    loadingEnd();
}
