import { utils } from "./utils";

var axios: any;

export class ZaroLead
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
            const response = await this.$http.get(this.API_URL_3RD + 'warehouse/leads/' + leadId + '/delete', { headers: this.httpHeaders });
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

    async getPaymentIntent(productId: string, leadId: string, quantity: string)
    {
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
