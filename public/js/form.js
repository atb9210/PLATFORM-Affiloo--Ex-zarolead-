class Utils {
    static getUrlParams(param) {
        var url_string = window.location.href
        var url = new URL(url_string);
        return url.searchParams.get(param);
    }
}

class Form {
    DEBUG = true;
    BETA = false;

    FORM_ID = 0;
    API_URL = '';

    input = [];
    quantity = {};
    currentQuantity = '';
    upsell = {};
    upsellEnabled = false;

    errors = [];

    networkData = {};

    pluginManager = null;

    landing_id = 0;

    constructor(id, debug = false, beta = false) {
        console.log("[i] FORM " + id + " CONSTRUCTED");

        this.FORM_ID = id;
        this.DEBUG = debug;
        this.BETA = beta;

        this.initialize();
    }

    initialize() {
        // Variables initialization
        var api_url = 'https://';
        if (this.BETA) {
            api_url += 'beta2.';
        }
        if (this.DEBUG) {
            api_url = 'http://';
            api_url += '127.0.0.1:8000/';
        } else {
            api_url += 'zarolead.com/';
        }
        api_url += 'api/v1/';
        this.API_URL = api_url;

        let landing_id = Utils.getUrlParams('landing'); 
        if (landing_id !== null) {
            this.landing_id = parseInt(landing_id);
        }

        // Initialize inputs
        this.initializeInput();

        // Function bindings
        $('#mainForm').submit(false);

        $("#submit-button").on('click', function() {
            console.log("submit button clicked");
            this.onSubmit(1);
        }.bind(this));

        for (var buttonId in this.UPSELL) {
            $('#' + buttonId).on('click', function(event) {
                var quantity = this.UPSELL[event.target.id]['quantity'];
                var price = this.UPSELL[event.target.id]['price'];
                var order_commission = this.UPSELL[event.target.id]['order_commission'];
                $('#quantity').val(quantity);
                $('#price').val(price);
                $('#order_commission').val(order_commission);
                this.onSubmit(2);
            }.bind(this));
        }

        // This is now into FacebookPlugin
        //$(".addtocart-button").each(function() {
        //    this.fbAddToCart();
        //}.bind(this));

        // TODO: We cannot do this anymore
        // Default pixel is in fb_pixel input
        //var pixel = this.getUrlParameter('pi');
        //if (pixel == '') {
        //    pixel = $('#fb_pixel').val();
        //}
        //$('#fb_pixel').val(pixel);

        // Input values
        $('#form_id').val(this.FORM_ID);
    }

    formVisited() {
        let data = {};
        if (this.landing_id > 0) {
            console.log("Landing id: " + this.landing_id);
            data.landing_id = this.landing_id;
        }
        console.log("DATA");
        console.log(data);

        // Submit
        $.ajax({
            url: this.API_URL + 'forms/' + this.FORM_ID + '/visited',
            method: 'GET',
            data: data,
            success: function(data) {
                console.log('[i] FORM VISITED');
            }.bind(this),
            error: function(data) {
                console.log('[!] FORM NOT VISITED');
            }.bind(this)
        });

        this.pluginManager.onVisit();
    }

    initializeInput() {
        $.ajax({
            url: this.API_URL + 'forms/' + this.FORM_ID + '/initialize',
            method: 'GET',
            success: function(data) {
                this.input = data.data.input;
                this.quantity = data.data.quantity;
                this.upsell = data.data.upsell;
                for (var i in this.input) {
                    this.createInput(i, this.input[i]);
                }

                this.currentQuantity = this.quantity.default;

                $('#quantity').val(this.currentQuantity);
                $('#price').val(this.quantity.options[this.currentQuantity].price);

                // Set upsell
                this.upsellEnabled = this.upsell.enabled;

                console.log('[i] FORM INPUT INITIALIZED');
            }.bind(this),
            error: function(data) {
                console.log('[!] FORM INPUT NOT INITIALIZED');
            }.bind(this)
        });
    }

    createInput(name, params) {
        console.log("Creating input " + name);
        console.log(params);

        // Create group
        var d = $('<div>', {
            class: 'form-group',
        }).appendTo($('#form-inputs'));

        // Create label
        var l = $('<label>' + params['label'] + '</label>', {
            class: "font-form",
            style: "line-height: 1.3em; width: 100%; font-size: 18px; font-weight: 600;",
        }).appendTo(d);

        // Create input
        var inputData = {
            id: name,
            name: name,
            type: params['type'],
            style: 'margin-bottom: 0.5em; font-weight: 400; width: 100%; border-radius: 3px; padding: 15px; font-size: 22px; border-width: 1px; border-style: double; border-color: #f2e832; box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5); font-family: "Open Sans",Sans-serif; font-weight: 400;',
        };
        if ('required' in params) {
            inputData.required = params['required'];
        }
        $('<input>', inputData).appendTo(d);

        // Create error span
        $('<span>', {
            id: name + 'Error',
            class: 'span-error',
        }).appendTo(d);
    }

    validateInput() {
        // Form validation
        $("#submit-button").attr("disabled", true);

        var input = {};
        for (var i in this.input) {
            input[i] = $('#' + i).val();
        }
        var data = {
            input: JSON.stringify(input)
        };
        $.ajax({
            url: this.API_URL + 'forms/' + this.FORM_ID + '/input/validate',
            method: 'POST',
            data: data,
            success: function(data) {
                console.log('[i] INPUT VALIDATED');

                this.onSubmit(2);
                $("#submit-button").attr("disabled", false);
            }.bind(this),
            error: function(response) {
                console.log('[!] INPUT NOT VALIDATED');

                this.errors = response.responseJSON.errors;
                this.inputSetErrors();
                $("#submit-button").attr("disabled", false);
            }.bind(this)
        });
    }

    inputClearErrors() {
        this.errors = [];
        for (var i in this.input) {
            this.inputClearError(i);
        }
    }

    inputClearError(input) {
        $("#" + input + "Error").html("");
        $("#" + input + "Error").css("display", "none");
    }

    inputSetErrors() {
        for (var error in this.errors) {
            this.inputSetError(error, this.errors[error][0]);
        }
    }

    inputSetError(input, message) {
        $("#" + input + "Error").css("display", "block");
        $("#" + input + "Error").html(message);
        $("#" + input).css("border-color", "red");
        $("#" + input).focus();
    }

    resetForm() {
        $("#submit-button").attr("disabled", false);
        $('#mainForm').css('display', 'block');
        $('#mainForm2').css('display', 'none');
    }

    /*
     * Step 1 => form validation
     * Step 2 => upsell & submission
     */
    onSubmit(step) {
        // Form validation
        $("#submit-button").attr("disabled", true);

        this.inputClearErrors();

        if (step == 1) {
            this.validateInput();
            return;

            // @TODO: Upsell system
            if (this.UPSELL_ENABLED) {
                $('#mainForm').css('display', 'none');
                $('#mainForm2').css('display', 'block');
            } else {
                this.onSubmit(2);
            }
        } else if (step == 2) {
            // Upsell & submission

            for (var i in this.input) {
                this.networkData[i] = $('#' + i).val();
            }
            this.networkData["quantity"] = $("#quantity").val();

            // Submit
            $('#network_data').val(JSON.stringify(this.networkData));
            console.log($('#network_data').val());

            var data = {};
            $.each($('#mainForm').serializeArray(), function(_, kv) {
                data[kv.name] = kv.value;
            });
            if ($("#surname").length) {
                data["name"] += " " + data["surname"];
                delete data["surname"];
            }
            if (this.landing_id > 0) {
                data["landing_id"] = this.landing_id;
            }
            console.log(data);

            $.ajax({
                url: this.API_URL + 'orders',
                method: "POST",
                dataType: "json",
                data: data,
                success: function(data) {
                    console.log("[+] STORED IN TRACKER DATABASE");

                    console.log(data);

                    this.pluginManager.onSubmit();

                    // Show thank you page
                    //var thankyou_page = $('#thankyou_page').val();
                    //window.location.replace(thankyou_page);

                    if (data.hasOwnProperty('redirect') && data.redirect != '') {
                        this.pluginManager.onRedirect();
                        window.location.replace(data.redirect);
                    } else {
                        $('#mainForm').css('display', 'none');
                        $('#mainForm2').css('display', 'none');
                        $('#thankyou-page').css('display', 'block');
                        
                    }
                }.bind(this),
                error: function(data) {
                    console.log("[!] ERROR: NOT STORED IN TRACKED DATABASE");
                    console.log(data);
                    alert("Errore ordine!");
                    this.resetForm();
                }.bind(this)
            });
        }
    }

    getUrlParameter(sParam) {
       var sPageURL = decodeURIComponent(window.location.search.substring(1)),
           sURLVariables = sPageURL.split('&'),
           sParameterName,
           i;
       for (i = 0; i < sURLVariables.length; i++) {
           sParameterName = sURLVariables[i].split('=');
           if (sParameterName[0] === sParam) {
               return sParameterName[1] === undefined ? '' : sParameterName[1];
           }
       }
       return '';
    } 
     
    attachPluginManager(pluginManager) {
        this.pluginManager = pluginManager;
        console.log("[i] PLUGIN MANAGER ATTACHED");
    }
}

var this_js_script = $('#form-js');
var form_id = this_js_script.attr('data-form-id'); 
var debug = (this_js_script.attr('data-debug') == 'true');
var beta = (this_js_script.attr('data-beta') == 'true'); 
var pluginsList = JSON.parse(this_js_script.attr('data-plugins'));

// Create plugin manager
let pluginManager = new PluginManager();

// Add plugins
for (const [key, value] of Object.entries(pluginsList)) {
    pluginManager.add(key, value);
}

// Create form
var form = new Form(form_id, debug, beta);

// Attach the plugin manager
form.attachPluginManager(pluginManager);

// Form visited
form.formVisited();
