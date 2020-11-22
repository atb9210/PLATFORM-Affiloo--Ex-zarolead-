function CssStylePlugin(data) {
    Plugin.call(this, data);

    this.stylesheet_url = this.data.stylesheet_url;
    this.loadCssStyle()
}

CssStylePlugin.prototype.onVisit = function() {
    console.log("CssStylePlugin onVisit");
}

CssStylePlugin.prototype.onSubmit = function() {
    console.log("CssStylePlugin onSubmit");
}

CssStylePlugin.prototype.afterSubmit = function(response) {
    console.log("CssStylePlugin afterSubmit");
}

CssStylePlugin.prototype.onRedirect = function() {
    console.log("CssStylePlugin onRedirect");
}

CssStylePlugin.prototype.loadCssStyle = function() {
    var head = document.getElementsByTagName('HEAD')[0];
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = this.stylesheet_url;
    head.appendChild(link);
    /*
    // NOTE: This code loads custom css code instead of a file
    cssCode = "#formContainer { background-color: blue; }";
    var style = document.createElement('style');
    style.type = 'text/css';
    if (style.styleSheet) {
        // IE
        style.styleSheet.cssText = cssCode;
    } else {
        // Other browsers
        style.innerHTML = cssCode;
    }
    document.getElementsByTagName("head")[0].appendChild(style);
    */
}
