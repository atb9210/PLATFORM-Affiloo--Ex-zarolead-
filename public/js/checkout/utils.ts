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

export { utils };
