class Plugin {
    data = {};

    constructor(data) {
        this.data = data;
    }

    onVisit() {
        console.log("Plugin onVisit");
    }

    onSubmit() {
        console.log("Plugin onSubmit");
    }

    onRedirect() {
        console.log("Plugin onRedirect");
    }
}

class PluginManager {
    plugins = [];

    add(plugin, data) {
        if(plugin == "facebook") {
            this.plugins.push(new FacebookPlugin(data));
        } else if(plugin == "zeustrack") {
            this.plugins.push(new ZeusTrackPlugin(data));
        }
    }

    onVisit() {
        for (var i = 0; i < this.plugins.length; i++) {
            this.plugins[i].onVisit();
        }
    }

    onSubmit() {
        for (var i = 0; i < this.plugins.length; i++) {
            this.plugins[i].onSubmit();
        }
    }

    onRedirect() {
        for (var i = 0; i < this.plugins.length; i++) {
            this.plugins[i].onRedirect();
        }
    }
}
