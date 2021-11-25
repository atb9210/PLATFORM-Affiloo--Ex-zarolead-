var Http = /** @class */ (function () {
    function Http() {
    }
    Http.prototype.build_query = function (obj) {
        var result = '';
        if (obj !== null) {
            for (var _i = 0, _a = Object.entries(obj); _i < _a.length; _i++) {
                var _b = _a[_i], key = _b[0], val = _b[1];
                result += encodeURIComponent(key) + '=';
                result += encodeURIComponent(val) + '&';
            }
        }
        if (result.length > 0) {
            result = result.substring(0, result.length - 1);
        }
        return result;
    };
    Http.prototype.request = function (method, url, params, headers) {
        if (params === void 0) { params = {}; }
        if (headers === void 0) { headers = {}; }
        var result = null;
        try {
            var req = new XMLHttpRequest();
            // Headers
            for (var k in headers) {
                req.setRequestHeader(k, headers[k]);
            }
            // Params query
            var query = this.build_query(params);
            req.open(method, url, false);
            req.send(query);
            if (req.readyState === 4) {
                result = req.response;
            }
        }
        catch (e) {
            // Nothing
        }
        return result;
    };
    Http.prototype.get = function (url, params, headers) {
        if (params === void 0) { params = {}; }
        if (headers === void 0) { headers = {}; }
        return this.request('get', url, params, headers);
    };
    Http.prototype.post = function (url, params, headers) {
        if (params === void 0) { params = {}; }
        if (headers === void 0) { headers = {}; }
        return this.request('post', url, params, headers);
    };
    return Http;
}());
var currentLocation = {
    street_number: '',
    route: '',
    area3: '',
    area2: '',
    area1: '',
    country: '',
    postcode: ''
};
var $http = new Http();
function resetCurrentLocation() {
    currentLocation = {
        street_number: '',
        route: '',
        area3: '',
        area2: '',
        area1: '',
        country: '',
        postcode: ''
    };
}
function getInputValue(inputId) {
    var result = '';
    var e = document.getElementById(inputId);
    if (e !== null) {
        result = e.value;
    }
    return result;
}
function checkFullName(fullName) {
    fullName = fullName.trim();
    if (fullName.length < 5) {
        console.log('fullName.length < 5');
        return false;
    }
    var tmp = fullName.split(' ');
    if (tmp.length < 2) {
        console.log('There\'s no first or second name');
        return false;
    }
    var firstName = tmp[0];
    var i = 1;
    while (tmp[i] === '') {
        i++;
        if (i >= tmp.length) {
            console.log('Invalid second name', tmp[i - 1]);
            return false;
        }
    }
    var secondName = tmp[i];
    if (firstName.length === 0 || secondName.length === 0) {
        console.log('Empty first/second name');
        return false;
    }
    return true;
}
function checkPhone(phone) {
    phone = phone.split(' ').join('');
    if (phone.length < 8) {
        console.log("phone.length < 8");
        return false;
    }
    for (var i = 0; i < phone.length; i++) {
        if (!(phone[i] >= '0' && phone[i] <= '9') && phone[i] !== '+') {
            console.log("phone[i] is not a char nor a +", phone[i]);
            return false;
        }
    }
    return true;
}
function checkLocation(loc) {
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
function setError(form, inputId, error) {
    var e = document.getElementById(inputId);
    if (e !== null) {
        e.setCustomValidity(error);
        e.reportValidity();
        form.reportValidity();
    }
}
function submit(form) {
    var fullName = getInputValue('fullName');
    var phone = getInputValue('phone');
    phone = phone.split(' ').join('');
    console.log('fullName', fullName);
    console.log('phone', phone);
    console.log('currentLocation', currentLocation);
    // Check full name
    console.log('checkFullName', fullName, checkFullName(fullName));
    if (!checkFullName(fullName)) {
        setError(form, 'fullName', 'Nome e Cognome non validi');
    }
    else {
        setError(form, 'fullName', '');
    }
    // Check phone
    console.log('checkPhone', phone, checkPhone(phone));
    if (!checkPhone(phone)) {
        setError(form, 'phone', 'Numero di telefono non valido');
    }
    else {
        setError(form, 'phone', '');
    }
    // Check address
    console.log('checkLocation', currentLocation, checkLocation(currentLocation));
    if (!checkLocation(currentLocation)) {
        setError(form, 'address', 'Indirizzo non valido');
    }
    else {
        setError(form, 'address', '');
    }
    if (!form.checkValidity()) {
        return;
    }
    //const response: string|null = $http.get('http://zarolead.localhost/test');
    //console.log('resp', response);
}
function fillInAddress() {
    resetCurrentLocation();
    var place = autocomplete.getPlace();
    for (var _i = 0, _a = place.address_components; _i < _a.length; _i++) {
        var component = _a[_i];
        var componentType = component.types[0];
        console.log(componentType, ' // ', component.short_name, ' // ', component.long_name);
        if (componentType === 'street_number') {
            currentLocation.street_number = component.long_name;
        }
        else if (componentType === 'route') {
            currentLocation.route = component.long_name;
        }
        else if (componentType === 'administrative_area_level_3') {
            currentLocation.area3 = component.long_name;
        }
        else if (componentType === 'administrative_area_level_2') {
            currentLocation.area2 = component.short_name;
        }
        else if (componentType === 'administrative_area_level_1') {
            currentLocation.area1 = component.short_name;
        }
        else if (componentType === 'country') {
            currentLocation.country = component.short_name;
        }
        else if (componentType === 'postal_code') {
            currentLocation.postcode = component.long_name;
        }
    }
    var e = null;
    e = document.getElementById('streetAddress');
    if (e !== null) {
        e.value = currentLocation.route;
    }
    e = document.getElementById('streetNumber');
    if (e !== null) {
        e.value = currentLocation.street_number;
    }
    e = document.getElementById('cap');
    if (e !== null) {
        e.value = currentLocation.postcode;
    }
    e = document.getElementById('city');
    if (e !== null) {
        e.value = currentLocation.area3;
    }
    e = document.getElementById('province');
    if (e !== null) {
        e.value = currentLocation.area2;
    }
}
function onLoad() {
    var form = document.getElementById('mainForm');
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
    var addressInput = document.getElementById('address');
    addressInput.addEventListener('change', function (event) {
        resetCurrentLocation();
    });
    if (addressInput !== null) {
        autocomplete = new google.maps.places.Autocomplete(addressInput, {
            componentRestrictions: { country: ['it'] },
            fields: ['address_components'],
            types: ['address']
        });
        autocomplete.addListener('place_changed', fillInAddress);
    }
    console.log('google', google);
}
