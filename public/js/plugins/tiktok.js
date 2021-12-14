// TODO: This is a clone of getUrlParameter, this types of functions should be in a utils.js file
function TikTokPlugin_getUrlParams(param) {
    var url_string = window.location.href
    var url = new URL(url_string);
    return url.searchParams.get(param);
}

function TikTokPlugin(data) {
    Plugin.call(this, data);

    this.pixel = this.data.pixel;
    this.token = this.data.token;

    if (TikTokPlugin_getUrlParams('pi') !== null && TikTokPlugin_getUrlParams('pi') !== '') {
        this.pixel = TikTokPlugin_getUrlParams('pi');
    }

    // Fetch event info
    this.eventName = 'SubmitForm';
    if (this.data.hasOwnProperty('eventName')) {
        this.eventName = this.data.eventName;
    }
    this.eventCurrency = 'EUR';
    if (this.data.hasOwnProperty('eventCurrency')) {
        this.eventCurrency = this.data.eventCurrency;
    }
    this.eventContentType = 'product';
    this.eventContentName = 'product_name';

    // Fetch click id (ttclid)
    this.ttclid = TikTokPlugin_getUrlParams('ttclid');
    if (this.ttclid === null) {
      this.ttclid = ''
    }
}

TikTokPlugin.prototype.onVisit = function() {
    /*
    //TODO: Old code
    ttq.load(this.pixel);
    ttq.track('ViewForm');
    ttq.page();
    */

    this.ttTriggerEvent('ViewContent');

    console.log("TikTokPlugin onVisit");
}

TikTokPlugin.prototype.onSubmit = function() {
    console.log("TikTokPlugin onSubmit");
    this.ttTriggerEvent('ClickButton');
}

TikTokPlugin.prototype.afterSubmit = function(response) {
    console.log("TikTokPlugin afterSubmit");
    if (response.api_call.status === 'success') {

        var formId = String($('#form_id').val());

        var eventValue = 0.0;
        var eventPrice = 0.0;
        var quantity = $('#quantity').val();
        if (form.quantity.options.hasOwnProperty(quantity)) {
            eventValue = parseFloat(form.quantity.options[quantity].commission);
            eventPrice = parseFloat(form.quantity.options[quantity].commission);
        }

        this.ttTriggerEvent(this.eventName, {
          content_id: formId,
          content_type: this.eventContentType,
          content_name: this.eventContentName,
          quantity: parseInt(quantity),
          value: eventValue,
          price: eventPrice,
          currency: this.eventCurrency,
        });
    }
}

TikTokPlugin.prototype.onRedirect = function() {
}

TikTokPlugin.prototype.ttTriggerEvent = function(eventName, params = {}) {
    /*
    // TODO: Old code
    if (Object.entries(params).length > 0) {
        ttq.track(eventName, params);
    } else {
        ttq.track(eventName);
    }
    */

    // Trigger tiktok event through S2S API
    var data = {
      token: this.token,
      pixel: this.pixel,
      event: eventName,
      ttclid: this.ttclid,
    };
    if (Object.entries(params).length > 0) {
      data.properties = {};
      if (params.hasOwnProperty('currency')) {
        data.properties.currency = params.currency;
      }
      if (params.hasOwnProperty('value')) {
        data.properties.value = params.value;
      }
      data.properties.contents = params;
      data.properties = JSON.stringify(data.properties);
    }
    $.ajax({
        url: form.API_URL_3RD + 'tiktok/events/trigger',
        method: 'POST',
        data: data,
        success: function(data) {
            console.log('[i] TIKTOK EVENT ' + data.event + ' TRIGGERED');
        }.bind(this),
        error: function(data) {
            console.log('[!] TIKTOK EVENT ' + data.event + ' NOT TRIGGERED');
        }.bind(this)
    });
}

/*
// TODO: Old code
// TikTok stuff
!function (w, d, t) {
w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
}(window, document, "ttq");

!function (w, d, t) {
w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
}(window, document, 'ttq');
*/
