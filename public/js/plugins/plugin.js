function Plugin(data) {
    this.data = {};
    this.data = data;
}

Plugin.prototype.onVisit = function() {
    console.log("Plugin onVisit");
}

Plugin.prototype.onSubmit = function() {
    console.log("Plugin onSubmit");
}

Plugin.prototype.onRedirect = function() {
    console.log("Plugin onRedirect");
}

function PluginManager() {
    this.plugins = [];
}

PluginManager.prototype.add = function(plugin, data) {
    if(plugin == "thankyou_redirect") {
        this.plugins.push(new ThankyouRedirectPlugin(data));
    } else if(plugin == "facebook") {
        this.plugins.push(new FacebookPlugin(data));
    } else if(plugin == "zeustrack") {
        this.plugins.push(new ZeusTrackPlugin(data));
    } else if(plugin == "double_recovery") {
        this.plugins.push(new DoubleRecoveryPlugin(data));
    } else if(plugin == "custom") {
        this.plugins.push(new CustomPlugin(data));
    } else if(plugin == "css_style") {
        this.plugins.push(new CssStylePlugin(data));
    }
}

PluginManager.prototype.onVisit = function() {
    for (var i = 0; i < this.plugins.length; i++) {
        this.plugins[i].onVisit();
    }
}

PluginManager.prototype.onSubmit = function() {
    for (var i = 0; i < this.plugins.length; i++) {
        this.plugins[i].onSubmit();
    }
}

PluginManager.prototype.afterSubmit = function(response) {
    for (var i = 0; i < this.plugins.length; i++) {
        this.plugins[i].afterSubmit(response);
    }
}

PluginManager.prototype.onRedirect = function() {
    for (var i = 0; i < this.plugins.length; i++) {
        this.plugins[i].onRedirect();
    }
}
