// TODO: This is a clone of getUrlParameter, this types of functions should be in a utils.js file
function FacebookConversionsAPIPlugin_getUrlParams(param) {
    var url_string = window.location.href
    var url = new URL(url_string);
    return url.searchParams.get(param);
}

function FacebookConversionsAPIPlugin_getCookie(key) {
    var cookiesMatch = document.cookie.match(new RegExp('(^| )' + key + '=([^;]+)'));
    if (cookiesMatch) return cookiesMatch[2];
    else return null;
}

function FacebookConversionsAPIPlugin_getParentHostnameAndScheme() {
    var url = (window.location != window.parent.location)
        ? document.referrer
        : document.location.href;
    url = new URL(url);
    return url.protocol + '//' + url.hostname;
}

function FacebookConversionsAPIPlugin(data) {
    Plugin.call(this, data);

    this.pixel = this.data.pixel;

    fbq('init', this.pixel);

    if (FacebookConversionsAPIPlugin_getUrlParams('pi') != '' && FacebookConversionsAPIPlugin_getUrlParams('pi') != null) {
        this.pixel = FacebookConversionsAPIPlugin_getUrlParams('pi');
    }

    // Fetch event info
    this.eventName = '';
    this.eventValue = '';
    this.eventCurrency = '';
    if (this.data.hasOwnProperty('eventName')) {
        this.eventName = this.data.eventName;
    }
    if (this.data.hasOwnProperty('eventValue')) {
        this.eventValue = this.data.eventValue;
    }
    if (this.data.hasOwnProperty('eventCurrency')) {
        this.eventCurrency = this.data.eventCurrency;
    }

    // TEST DEBUG
    this.accessToken = '';
    if (this.data.hasOwnProperty('access_token')) {
        this.accessToken = this.data.access_token;
    }
    this.testCode = '';
    if (this.data.hasOwnProperty('test_code')) {
        this.testCode = this.data.test_code;
    }

    // Fetch click id
    this.fbc = FacebookConversionsAPIPlugin_getCookie('_fbc');
    if (FacebookConversionsAPIPlugin_getUrlParams('fbclid') != '' && FacebookConversionsAPIPlugin_getUrlParams('fbclid') != null) {
        this.fbc = FacebookConversionsAPIPlugin_getUrlParams('fbclid');
    }

    // NOTE: This is a custom code
    $(".addtocart-button").each(function() {
        this.onAddToCart();
    }.bind(this));
}

FacebookConversionsAPIPlugin.prototype.onVisit = function() {
    this.fbTriggerEvent('ViewContent');

    console.log("FacebookConversionsAPIPlugin onVisit");
}

FacebookConversionsAPIPlugin.prototype.onSubmit = function() {
    console.log("FacebookConversionsAPIPlugin onSubmit");
}

FacebookConversionsAPIPlugin.prototype.afterSubmit = function(response) {
    console.log("FacebookConversionsAPIPlugin afterSubmit");
    if (response.api_call.status === 'success') {
        // Default event infos
        var eventName = 'Purchase';
        var eventValue = 0.0;
        var eventCurrency = 'EUR';

        // Fetch event name
        if (FacebookConversionsAPIPlugin_getUrlParams('ev') != '' && FacebookConversionsAPIPlugin_getUrlParams('ev') != null) {
            eventName = FacebookConversionsAPIPlugin_getUrlParams('ev');
        } else if (this.eventName != '') {
            eventName = this.eventName;
        }

        // Fetch event value
        if (this.eventValue != '') {
            eventValue = this.eventValue;
        } else {
            var quantity = $('#quantity').val();
            if (form.quantity.options.hasOwnProperty(quantity)) {
                eventValue = form.quantity.options[quantity].commission;
            }
        }

        // Fetch event currency
        if (this.eventCurrency != '') {
            eventCurrency = this.eventCurrency;
        }

        this.fbTriggerEvent(eventName, { value: eventValue, currency: eventCurrency });
    }
}

FacebookConversionsAPIPlugin.prototype.onRedirect = function() {
}

FacebookConversionsAPIPlugin.prototype.onAddToCart = function() {
    this.fbTriggerEvent('AddToCart');
}

FacebookConversionsAPIPlugin.prototype.fbTriggerEvent = function(eventName, customData = {}) {
    console.log('[i] TRIGGERING FACEBOOK EVENT: ' + eventName);

    var data = {
        access_token: accessToken,
        test_code: testCode,
        pixel: this.pixel,
        event: eventName,
        fbc: this.fbc,
        custom_data: JSON.stringify(customData),
    };
    $.ajax({
        url: form.API_URL_3RD + 'facebook/events',
        method: 'POST',
        data: data,
        success: function(data) {
            console.log('[i] FACEBOOK EVENT ' + eventName + ' TRIGGERED');
        }.bind(this),
        error: function(data) {
            console.log('[!] FACEBOOK EVENT ' + eventName + ' NOT TRIGGERED');
        }.bind(this)
    });
}

// Facebook stuff
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
