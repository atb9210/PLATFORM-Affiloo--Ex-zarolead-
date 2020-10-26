function CustomPlugin(data) {
    Plugin.call(this, data);

    this.code = this.data.afterSubmit;

    // Load external scripts
    var els = this.htmlToElements(this.code);
    for (var i = 0; i < els.length; i++) {
        if ((typeof els[i] !== 'undefined') && (els[i] instanceof HTMLElement) && (els[i] !== null)) {
            if (els[i].tagName.toLowerCase() === 'script') {
                if (els[i].getAttribute('src') != '' && els[i].getAttribute('src') != null) {
                    this.loadExternalScript(els[i]);
                }
            }
        }
    }
    this.codeElements = els;
}

CustomPlugin.prototype.onVisit = function() {
    console.log("CustomPlugin onVisit");
}

CustomPlugin.prototype.onSubmit = function() {
}

CustomPlugin.prototype.afterSubmit = function(response) {
    console.log("CustomPlugin afterSubmit");
    if (response.api_call.status === 'success') {
        for (var i = 0; i < this.codeElements.length; i++) {
            if ((typeof this.codeElements[i] !== 'undefined') && (this.codeElements[i] instanceof HTMLElement) && (this.codeElements[i] !== null)) {
                if (this.codeElements[i].tagName.toLowerCase() === 'script') {
                    eval(this.codeElements[i].innerHTML);
                }
                document.body.append(this.codeElements[i]);
            }
        }
    }
}

CustomPlugin.prototype.onRedirect = function() {
}

CustomPlugin.prototype.loadExternalScript = function(el) {
    var extScript = document.createElement('script');
    var el1 = $(el);
    var el2 = $(extScript);
    var attrbts = el1.prop("attributes");
    $.each(attrbts, function() {
        el2.attr(this.name, this.value);
    });
    document.body.append(el2[0]);
}

CustomPlugin.prototype.htmlToElement = function(html) {
    var template = document.createElement('template');
    html = html.trim(); // Never return a text node of whitespace as the result
    template.innerHTML = html;
    return template.content.firstChild;
}

CustomPlugin.prototype.htmlToElements = function(html) {
    var template = document.createElement('template');
    template.innerHTML = html;
    return template.content.childNodes;
}
