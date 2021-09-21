// TODO: This is a clone of getUrlParameter, this types of functions should be in a utils.js file
function FacebookPlugin_getUrlParams(param) {
    var url_string = window.location.href
    var url = new URL(url_string);
    return url.searchParams.get(param);
}

function FacebookPlugin_getCookie(key) {
    var cookiesMatch = document.cookie.match(new RegExp('(^| )' + key + '=([^;]+)'));
    if (cookiesMatch) return cookiesMatch[2];
    else return null;
}

function FacebookPlugin_getParentHostnameAndScheme() {
    var url = (window.location != window.parent.location)
        ? document.referrer
        : document.location.href;
    url = new URL(url);
    return url.protocol + '//' + url.hostname;
}

function FacebookPlugin(data) {
    Plugin.call(this, data);

    this.pixel = this.data.pixel;
    //this.pixel = FacebookPlugin_getCookie('_fbp');

    fbq('init', this.pixel);

    if (FacebookPlugin_getUrlParams('pi') != '' && FacebookPlugin_getUrlParams('pi') != null) {
        this.pixel = FacebookPlugin_getUrlParams('pi');
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

    // Fetch click id
    this.fbc = FacebookPlugin_getCookie('_fbc');
    if (FacebookPlugin_getUrlParams('fbclid') != '' && FacebookPlugin_getUrlParams('fbclid') != null) {
        this.fbc = FacebookPlugin_getUrlParams('fbclid');
    }

    // NOTE: This is a custom code
    $(".addtocart-button").each(function() {
        this.onAddToCart();
    }.bind(this));
}

FacebookPlugin.prototype.onVisit = function() {
    // TODO: to delete (old code)
    /*
    fbq('init', this.pixel);
    fbq('track', 'ViewContent');
    */

    this.fbTriggerEvent('ViewContent');

    console.log("FacebookPlugin onVisit");
}

FacebookPlugin.prototype.onSubmit = function() {
    console.log("FacebookPlugin onSubmit");
}

FacebookPlugin.prototype.afterSubmit = function(response) {
    console.log("FacebookPlugin afterSubmit");
    if (response.api_call.status === 'success') {
        // Default event infos
        var eventName = 'Purchase';
        var eventValue = 0.0;
        var eventCurrency = 'EUR';

        // Fetch event name
        if (FacebookPlugin_getUrlParams('ev') != '' && FacebookPlugin_getUrlParams('ev') != null) {
            eventName = FacebookPlugin_getUrlParams('ev');
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

FacebookPlugin.prototype.onRedirect = function() {
}

FacebookPlugin.prototype.onAddToCart = function() {
    // TODO: to delete (old code)
    /*
    // Grab those link clickers!!
    fbq('track', 'AddToCart');
    */
}

FacebookPlugin.prototype.fbTriggerEvent = function(eventName, customData = {}) {
    // TODO: to delete (old code)
    /*
    fbq('track', eventName, customData);
    */

    var data = {
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
