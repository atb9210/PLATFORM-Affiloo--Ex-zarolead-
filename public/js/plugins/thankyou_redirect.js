function ThankyouRedirectPlugin(data) {
    Plugin.call(this, data);

    this.redirect_url = this.data.url;
}

ThankyouRedirectPlugin.prototype.onVisit = function() {
}

ThankyouRedirectPlugin.prototype.onSubmit = function() {
}

ThankyouRedirectPlugin.prototype.afterSubmit = function() {
    window.location.replace(this.redirect_url);
}

ThankyouRedirectPlugin.prototype.onRedirect = function() {
}
