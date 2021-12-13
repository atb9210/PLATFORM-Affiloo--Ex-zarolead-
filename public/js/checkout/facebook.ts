import { utils } from "./utils";
import { ZaroLead } from "./zarolead";

export class Facebook
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
