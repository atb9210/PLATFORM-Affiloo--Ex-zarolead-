function TikTokPlugin(data) {
    Plugin.call(this, data);

    this.pixel = this.data.pixel;

    // Fetch event info
    this.eventName = 'SubmitForm';
    if (this.data.hasOwnProperty('eventName')) {
        this.eventName = this.data.eventName;
    }

    if (this.data.hasOwnProperty('eventCurrency')) {
        this.eventCurrency = this.data.eventCurrency;
    }
}

TikTokPlugin.prototype.onVisit = function() {
    ttq.load(this.pixel);
    ttq.page();
    ttq.track('ViewContent');

    console.log("TikTokPlugin onVisit");
}

TikTokPlugin.prototype.onSubmit = function() {
    console.log("TikTokPlugin onSubmit");
}

TikTokPlugin.prototype.afterSubmit = function(response) {
    console.log("TikTokPlugin afterSubmit");
    if (response.api_call.status === 'success') {
        this.ttTriggerEvent(this.eventName);
    }
}

TikTokPlugin.prototype.onRedirect = function() {
}

TikTokPlugin.prototype.ttTriggerEvent = function(eventName) {
    ttq(eventName);
}

// TikTok stuff
!function (w, d, t) {
w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
}(window, document, "ttq");
