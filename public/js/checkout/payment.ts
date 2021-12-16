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

    constructor()
    {
        let hostname = utils.getHostname();
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
            const response = await this.$http.get(this.API_URL_3RD + 'warehouse/products/payment-intent', { params: params, headers: httpHeaders });
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

declare var Stripe: any;
var stripeKey: string|null = null;
var stripe: any;
var card: any;
var stripeClientSecret: string = '';

var paymentIntent: object|null = null;

var total = 0.0;

// Billing info
let firstName = '';
let lastName = '';
let fullName = '';
let phone = '';
let country = '';
let city = '';
let address = '';
let postcode = '';
let state = '';

/******************************************************************************/
/**                                  ONLOAD                                  **/
/******************************************************************************/

function setStripeError(stripeErrorId: string, error: string) {
    let displayError = document.getElementById(stripeErrorId);
    displayError.textContent = error;
    if (error !== '') {
        displayError.style.display = 'block';
    } else {
        displayError.style.display = 'none';
    }
}

async function init()
{
    let pIntent = await zarolead.getPaymentIntent(total);
    if (pIntent !== null && pIntent.hasOwnProperty('client_secret')) {
        paymentIntent = pIntent;
        stripeClientSecret = pIntent['client_secret'];
    }

    utils.showElement('step1');
}

async function next()
{
    let isValid = false;

    const stripeResult = await stripe.confirmCardPayment(stripeClientSecret, {
        payment_method: {
            card: card,
            billing_details: {
                name: fullName,
                address: {
                    country: country,
                    city: city,
                    line1: address,
                    line2: '',
                    postal_code: postcode,
                    state: state,
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
        let leadUpdated = await zarolead.completeLead(lead['hash'], 'confirmed', total, 'online');
        isValid = leadUpdated && isValid;
    }

    // DEBUG TODO: to delete
    console.log("next", isValid);

    if (isValid) {
        utils.hideElement('step1');
        utils.showElement('step2');
    }

    return isValid;
}

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

async function onLoad()
{
    utils.loadingStart();

    // Form initialization
    form = <HTMLFormElement>document.getElementById('mainForm');
    if (form === null) {
        return;
    }
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        event.stopPropagation();
        form.classList.add('was-validated');
        await next();
    }, false);


    // Get checkout info
    let thisScript = document.getElementById('checkout-js');
    if (thisScript !== null) {
        leadId = utils.getAttribute(thisScript, 'data-lead-id');

        if (leadId !== null) {
            lead = await zarolead.getLeadInfo(utils.strval(leadId));
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

    // TODO: Check correctness of lead['shipping'] otherwise he cannot pay.
    // Well, technically he can pay but we need to make invoice so we need his
    // billing info.

    // Fetch billing ingo
    if (lead !== null && lead.hasOwnProperty('shipping') && lead['shipping'] !== null) {
        // Name and phone
        if (lead['shipping'].hasOwnProperty('first_name') && lead['shipping']['first_name'] !== null) {
            firstName = lead['shipping']['first_name'];
        }
        if (lead['shipping'].hasOwnProperty('last_name') && lead['shipping']['last_name'] !== null) {
            lastName = lead['shipping']['last_name'];
        }
        fullName = firstName + ' ' + lastName;
        if (lead['shipping'].hasOwnProperty('phone') && lead['shipping']['phone'] !== null) {
            phone = lead['shipping']['phone'];
        }

        // Address
        if (lead['shipping'].hasOwnProperty('country') && lead['shipping']['country'] !== null) {
            country = lead['shipping']['country'];
        }
        if (lead['shipping'].hasOwnProperty('city') && lead['shipping']['city'] !== null) {
            city = lead['shipping']['city'];
        }
        if (lead['shipping'].hasOwnProperty('street_address') && lead['shipping']['street_address'] !== null) {
            address = lead['shipping']['street_address'];
        }
        if (lead['shipping'].hasOwnProperty('postcode') && lead['shipping']['postcode'] !== null) {
            postcode = lead['shipping']['postcode'];
        }
        if (lead['shipping'].hasOwnProperty('state') && lead['shipping']['state'] !== null) {
            state = lead['shipping']['state'];
        }
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

    init();

    utils.loadingEnd();
}
