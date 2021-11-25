class Http
{
    build_query(obj: object|null): string
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
        var result: string|null = null;

        try {
            var req: XMLHttpRequest = new XMLHttpRequest();

            // Headers
            for (var k in headers) {
                req.setRequestHeader(k, <string>headers[k]);
            }

            // Params query
            var query: string = this.build_query(params);

            req.open(method, url, false);
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
        return this.request('get', url, params, headers);
    }

    post(url: string, params: object = {}, headers: object = {}): string|null
    {
        return this.request('post', url, params, headers);
    }
}

declare var google: any;
declare var autocomplete: any;

interface location {
    street_number: string,
    route: string,
    area3: string,   // comune
    area2: string,   // provincia
    area1: string,   // regione
    country: string,
    postcode: string,
}

var currentLocation: location = {
    street_number: '',
    route: '',
    area3: '',
    area2: '',
    area1: '',
    country: '',
    postcode: '',
}

const $http = new Http();

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
        console.log('fullName.length < 5');
        return false;
    }

    var tmp: Array<string> = fullName.split(' ');
    if (tmp.length < 2) {
        console.log('There\'s no first or second name');
        return false
    }

    var firstName: string = tmp[0];
    var i: number = 1;
    while (tmp[i] === '') {
        i++;
        if (i >= tmp.length) {
            console.log('Invalid second name', tmp[i - 1]);
            return false;
        }
    }
    var secondName: string = tmp[i];

    if (firstName.length === 0 || secondName.length === 0) {
        console.log('Empty first/second name');
        return false;
    }

    return true;
}

function checkPhone(phone: string): boolean
{
    phone = phone.split(' ').join('');

    if (phone.length < 8)
    {
        console.log("phone.length < 8");
        return false;
    }

    for (var i: number = 0; i < phone.length; i++) {
        if (!(phone[i] >= '0' && phone[i] <= '9') && phone[i] !== '+') {
            console.log("phone[i] is not a char nor a +", phone[i]);
            return false;
        }
    }

    return true;
}

function checkLocation(loc: location): boolean
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

function setError(form: HTMLObjectElement, inputId: string, error: string)
{
    var e: HTMLObjectElement = <HTMLObjectElement>document.getElementById(inputId);
    if (e !== null) {
        e.setCustomValidity(error);
        e.reportValidity();
        form.reportValidity();
    }
}

function submit(form: HTMLObjectElement): void
{
    var fullName: string = getInputValue('fullName');
    var phone: string = getInputValue('phone');
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
        return;
    }


    //const response: string|null = $http.get('http://zarolead.localhost/test');
    //console.log('resp', response);
}

function fillInAddress(): void
{
    resetCurrentLocation()

    const place = autocomplete.getPlace();
    for (const component of place.address_components) {
        const componentType = component.types[0];

        console.log(componentType, ' // ', component.short_name, ' // ', component.long_name);
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
}

function onLoad(): void
{
    var form: HTMLObjectElement = <HTMLObjectElement>document.getElementById('mainForm');
    if (form === null) {
        return;
    }

    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
        submit(form);
    }, false);

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

    console.log('google', google);
}
