// TODO: This is a clone of getUrlParameter, this types of functions should be in a utils.js file
function DoubleRecoveryPlugin_getUrlParams(param) {
    var url_string = window.location.href
    var url = new URL(url_string);
    return url.searchParams.get(param);
}

function DoubleRecoveryPlugin(data) {
    Plugin.call(this, data);

    this.form_id = this.data.form_id;
}

DoubleRecoveryPlugin.prototype.onVisit = function() {
    console.log("DoubleRecoveryPlugin onVisit");
}

DoubleRecoveryPlugin.prototype.onSubmit = function() {
    console.log("DoubleRecoveryPlugin onSubmit");
}

DoubleRecoveryPlugin.prototype.afterSubmit = function(response) {
    console.log("DoubleRecoveryPlugin afterSubmit");

    if (response.api_call.status === 'double') {
        var networkData = form.networkData;
        var data = {
            form_id: this.form_id,
            network_data: JSON.stringify(networkData)
        };

        $.ajax({
            url: form.API_URL + 'orders/double-recovery',
            method: "POST",
            dataType: "json",
            data: data,
            success: function(response) {
                console.log("[+] DOUBLE ORDER RECOVERED");
                console.log(response);
            }.bind(this),
            error: function(data) {
            }.bind(this)
        });
    }
}

DoubleRecoveryPlugin.prototype.onRedirect = function() {
}
