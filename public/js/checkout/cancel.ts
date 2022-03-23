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

/******************************************************************************/
/**                                 ZAROLEAD                                 **/
/******************************************************************************/

class ZaroLead
{
    API_URL: string;
    API_URL_3RD: string;

    $http: any;
    httpHeaders: object;

    warehouseNetwork: string;

    constructor(warehouseNetwork: string = 'dropin')
    {
        // TODO: Change this based on which server is hosted
        //let hostname = utils.getHostname();

        let hostname = 'https://beta2.zarolead.com';
        this.API_URL = hostname + '/api/v1/';
        this.API_URL_3RD = hostname + '/api/3rd/';

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

var leadId: string;
var form: HTMLFormElement|null = null;

var lead: object|null = null;

/******************************************************************************/
/**                                  ONLOAD                                  **/
/******************************************************************************/

async function init()
{
    if (leadId !== null) {
        lead = await zarolead.getLeadInfo(utils.strval(leadId));
    }
    if (lead !== null) {
        var e: HTMLElement|null = null;

        e = document.getElementById('orderNumber');
        if (e !== null) {
            e.innerText = utils.strval(lead['number']);
        }
        e = document.getElementById('orderDate');
        if (e !== null) {
            var dateOptions = {
                year: <"numeric"|"2-digit">'numeric',
                month: <"long"|"short"|"narrow"|"numeric"|"2-digit">'long',
                day: <"numeric"|"2-digit">'numeric',
            }
            e.innerText = (new Date(lead['created_at'])).toLocaleDateString('it-IT', dateOptions);
        }
        e = document.getElementById('orderProductName');
        if (e !== null) {
            e.innerText = lead['product_name'];
        }
        e = document.getElementById('orderQuantity');
        if (e !== null) {
            e.innerText = lead['quantity'];
        }
        e = document.getElementById('orderTotal');
        if (e !== null) {
            e.innerText = utils.floatval(lead['total']).toFixed(2);
        }
    }

    utils.showElement('step1');
}

async function next()
{
    utils.hideElement('step1');
    utils.showElement('step2');
    utils.hideElement('step3');
}

async function back()
{
    utils.showElement('step1');
    utils.hideElement('step2');
    utils.hideElement('step3');
}

async function finish()
{
    utils.loadingStart();

    if (lead !== null) {
        const result = await zarolead.deleteLead(lead['id']);
        if (result === true) {
            utils.hideElement('step1');
            utils.hideElement('step2');
            utils.showElement('step3');
        }
    }

    utils.loadingEnd();
}

async function onLoad()
{
    utils.loadingStart();

    // Get checkout info
    let thisScript = document.getElementById('checkout-js');
    if (thisScript !== null) {
        leadId = utils.getAttribute(thisScript, 'data-lead-id');
    } else {
        // TODO: Abort??
    }

    // Set event listeners
    var e: HTMLElement|null = null;
    e = document.getElementById('nextStepButton');
    if (e !== null) {
        e.addEventListener('click', async function (event) {
            await next();
        });
    }
    e = document.getElementById('confirmDeleteButton');
    if (e !== null) {
        e.addEventListener('click', async function (event) {
            await finish();
        });
    }
    e = document.getElementById('cancelDeleteButton');
    if (e !== null) {
        e.addEventListener('click', async function (event) {
            await back();
        });
    }

    init();

    utils.loadingEnd();
}
