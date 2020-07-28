// TODO: This is a clone of getUrlParameter, this types of functions should be in a utils.js file
function ThankyouRedirectPlugin_getUrlParams(param) {
    var url_string = window.location.href
    var url = new URL(url_string);
    return url.searchParams.get(param);
}

function ThankyouRedirectPlugin(data) {
    Plugin.call(this, data);

    this.redirect_url = this.data.url;
}

ThankyouRedirectPlugin.prototype.onVisit = function() {
}

ThankyouRedirectPlugin.prototype.onSubmit = function() {
}

ThankyouRedirectPlugin.prototype.afterSubmit = function() {
    // TODO: This is custom code, this should not be here because this is
    // a general thankyou redirect plugin
    if (this.redirect_url.includes("?")) {
        this.redirect_url += "&pi=" + ThankyouRedirectPlugin_getUrlParams("pi");
    } else {
        this.redirect_url += "?pi=" + ThankyouRedirectPlugin_getUrlParams("pi");
    }

    // Redirect
    window.location.replace(this.redirect_url);
}

ThankyouRedirectPlugin.prototype.onRedirect = function() {
}
