function getUrlParam(param) {
    var url_string = window.location.href
    var url = new URL(url_string);
    return url.searchParams.get(param);
}

function getUrlParams() {
    var toReturn = {};
    var url_string = window.location.href
    var url = new URL(url_string);
    for (const entry of url.searchParams.entries()) {
        toReturn[entry[0]] = entry[1];
    }
    return toReturn;
}

function formatCurrency(curr) {
    return parseFloat(curr).toFixed(2).replace('.', ',');
}

function formatDeliveryDay(d) {
    var months = [
        'Gennaio', 'Febbraio', 'Marzo', 'Aprile',
        'Maggio', 'Giugno', 'Luglio', 'Agosto',
        'Settembre', 'Ottobre', 'Novembre', 'Dicembre'
    ];
    return d.getDate() + ' ' + months[d.getMonth()];
}

function formatPhone(val) {
    var prefix = '+(39)';
    var regex = /^\+(39) /g;

    var alternative_prefix = '+39';

    val = val.replace(prefix, '');
    val = val.replace(alternative_prefix, '');

    // Remove all characters other than digits
    var tmp = val;
    for (var i = 0; i < tmp.length; i++) {
        if (tmp[i] < '0' || tmp[i] > '9') {
            val = val.replace(tmp[i], '');
        }
    }

    // Add prefix
    if (!val.match(regex)) {
        val = prefix + val;
    }

    return val;
}

function forcePhoneFormat(evt) {
    var prefix = '+(39) ';
    var regex = /^\+(39) /g;

    var val = (evt.target.value);
    var newVal = val;

    newVal = newVal.replace(prefix, '');
    var tmp = newVal;
    for (var i = 0; i < tmp.length; i++) {
        if ((tmp[i] != ' ') && (tmp[i] < '0' || tmp[i] > '9')) {
            newVal = newVal.replace(tmp[i], '');
        }
    }

    // Add prefix
    if (!newVal.match(regex)) {
        newVal = prefix + newVal;
    }

    // Update input value
    evt.target.value = newVal;
}

function Form(id, debug = false, alpha = false, beta = false) {
    this.DEBUG = true;
    this.ALPHA = false;
    this.BETA = false;

    this.FORM_ID = 0;
    this.API_URL = '';

    this.order = {};

    this.sendingForm = false;
    this.input = [];
    this.inputSteps = [];
    this.currentInputStep = -1;
    this.quantity = {};
    this.currentQuantity = '';
    this.upSell = {};
    this.upSellEnabled = false;
    this.crossSell = {};
    this.crossSellEnabled = false;
    this.crossSellAddedToCart = false;
    this.crossSellValue = {
        enabled: false,
        price: 0,
        commission: 0,
        sub_commission: 0,
    };
    this.deliveryEnabled = false;
    this.delivery = [];
    this.deliveryOption = 0;

    this.currentStep = '';

    this.errors = [];
    this.errorsInStep = 0;

    this.networkData = {};

    this.pluginManager = null;

    this.landing_id = 0;

    this.notValidatedInput = {};
    this.validatedInput = {};

    this.forcePhoneFormatIds = [];

    console.log("[i] FORM " + id + " CONSTRUCTED");

    this.FORM_ID = id;
    this.DEBUG = debug;
    this.ALPHA = alpha;
    this.BETA = beta;

    this.initialize();
}

Form.prototype.initialize = function() {
    // Variables initialization
    var api_url = 'https://';
    if (this.BETA) {
        api_url += 'beta2.';
    } else if(this.ALPHA) {
        api_url += 'alpha.';
    }
    if (this.DEBUG) {
        api_url = 'http://';
        //api_url += '127.0.0.1:8000/';
        api_url += 'zarolead.localhost/';
    } else {
        api_url += 'zarolead.com/';
    }
    api_url += 'api/v1/';
    this.API_URL = api_url;

    var landing_id = getUrlParam('landing'); 
    if (landing_id !== null) {
        this.landing_id = parseInt(landing_id);
    }

    // Initialize inputs
    this.initializeInput();

    // Function bindings
    $('#mainForm').submit(false);

    $("#submit-button").on('click', function() {
        this.goToNextStep();
    }.bind(this));

    $("#submit-button3").on('click', function() {
        this.signupNewsletter();
    }.bind(this));

    // Input values
    $('#form_id').val(this.FORM_ID);
}

Form.prototype.formVisited = function() {
    var data = {};
    if (this.landing_id > 0) {
        data.landing_id = this.landing_id;
    }
    var query_params = getUrlParams();
    data["query_params"] = JSON.stringify(query_params);

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

Form.prototype.initializeFirstStep = function() {
    $('#upSellStep').css('display', 'none');
    $('#crossSellStep').css('display', 'none');
    $('#mainForm').css('display', 'none');
    if (this.upSellEnabled) {
        this.currentStep = 'upSell';
        $('#upSellStep').css('display', 'block');
    } else if (this.crossSellEnabled) {
        this.currentStep = 'crossSell';
        $('#crossSellStep').css('display', 'block');
    } else {
        this.currentStep = 'input';
        $('#mainForm').css('display', 'block');
    }
}

Form.prototype.initializeInput = function() {
    $.ajax({
        url: this.API_URL + 'forms/' + this.FORM_ID + '/initialize',
        method: 'GET',
        success: function(data) {
            this.currentInputStep = -1;
            this.inputSteps = data.data.input;
            this.input = [];
            this.quantity = data.data.quantity;
            this.upSell = data.data.up_sell;
            this.crossSell = data.data.cross_sell;
            this.delivery = data.data.delivery;

            this.upSellEnabled = false;
            if (this.upSell.hasOwnProperty('enabled')) {
                this.upSellEnabled = (this.upSell.enabled == true && Object.keys(this.quantity.options).length > 1);
            }
            if (this.upSellEnabled) {
                this.initializeUpSellStep();
            }

            this.crossSellEnabled = false;
            if (this.crossSell != null && this.crossSell.hasOwnProperty('enabled')) {
                this.crossSellEnabled = (this.crossSell.enabled == true);
            }
            if (this.crossSellEnabled) {
                this.initializeCrossSellStep();
            }

            this.deliveryEnabled = false;
            if (this.delivery != null && this.delivery.hasOwnProperty('enabled')) {
                this.deliveryEnabled = ((this.delivery.enabled == true) && this.delivery.hasOwnProperty('options') &&
                    Array.isArray(this.delivery.options) && this.delivery.options.length > 0);
            }
            if (this.deliveryEnabled) {
                this.initializeDeliveryStep();
            }

            var inputStepCounter = 0;
            for (var inputIndex = 0; inputIndex < this.inputSteps.length; inputIndex++) {
                var inp = this.inputSteps[inputIndex];
                for (var i in inp) {
                    this.input[i] = inp[i];
                    this.createInput(i, inp[i], inputStepCounter, inputIndex);
                }
                inputStepCounter++;
            }

            this.currentQuantity = this.quantity.default;

            $('#quantity').val(this.currentQuantity);
            $('#price').val(this.quantity.options[this.currentQuantity].price);

            // Initialize first input step
            this.initializeFirstStep();
            this.goToNextStep();

            console.log('[i] FORM INPUT INITIALIZED');
        }.bind(this),
        error: function(data) {
            console.log('[!] FORM INPUT NOT INITIALIZED');
        }.bind(this)
    });
}

Form.prototype.initializeUpSellStep = function() {
    // Reset up sell options
    $('#upSellOptions').html('');

    // Create up sell options
    var activeSetFlag = false;
    var defaultPrice = 0;
    for (const [key, value] of Object.entries(this.quantity.options)) {
        var bestSeller = (this.upSell.hasOwnProperty('best_seller') && this.upSell.best_seller == key);
        var activeOption = false;
        if (!activeSetFlag) {
            if (this.quantity.hasOwnProperty('default') && (this.quantity.default in this.quantity.options)) {
                if (key == this.quantity.default) {
                    defaultPrice = parseFloat(value.price);
                    activeOption = true;
                    activeSetFlag = true;
                }
            } else {
                activeOption = true;
                activeSetFlag = true;
            }
        }
        this.createUpSellOption(key, value.discount, value.price, activeOption, bestSeller);
    }

    // Update total amount
    $('#upSellTotalAmount').html(formatCurrency(defaultPrice));

    // Finish up sell button click function binding
    $('#up-sell-done').on('click', function() {
        this.finishUpSell();
    }.bind(this));
}

Form.prototype.initializeCrossSellStep = function() {
    var price = 0.0;
    if (this.crossSell.hasOwnProperty('price')) {
        price = this.crossSell.price;
    }
    var commission = 0.0;
    if (this.crossSell.hasOwnProperty('commission')) {
        commission = this.crossSell.commission;
    }
    var sub_commission = 0.0;
    if (this.crossSell.hasOwnProperty('sub_commission')) {
        sub_commission = this.crossSell.sub_commission;
    }
    var quantity = 1;
    if (this.crossSell.hasOwnProperty('quantity')) {
        quantity = parseInt(this.crossSell.quantity);
    }
    this.updateCrossSellValue({
        enabled: false,
        price: price,
        commission: commission,
        sub_commission: sub_commission,
        quantity: quantity.toString(),
    });

    this.crossSellAddedToCart = false;

    $('#crossSellPrice').html(formatCurrency(this.crossSell.price));
    if (this.crossSell.hasOwnProperty('title')) {
        $('#crossSellTitle').html(this.crossSell.title);
    }
    if (this.crossSell.hasOwnProperty('description')) {
        $('#crossSellDescription').html(this.crossSell.description);
    }
    if (this.crossSell.hasOwnProperty('img')) {
        $('#crossSellImg').attr('src', this.crossSell.img);
    }

    // Finish up sell button click function binding
    $('#cross-sell-done').on('click', function() {
        this.finishCrossSell();
    }.bind(this));

    // Add to cart (cross sell) function binding
    $('#crossSellAddButton').on('click', function() {
        this.crossSellAddToCart();
    }.bind(this));
}

Form.prototype.initializeDeliveryStep = function() {
    $('#deliveryOptions').html('');
    for (var i = 0; i < this.delivery.options.length; i++) {
        var opt = this.delivery.options[i];
        var deliveryOption = document.createElement('div');
        deliveryOption.className = 'col-12';
        var inputRadio = document.createElement('input');
        inputRadio.type = 'radio';
        inputRadio.id = 'delivery-' + i;
        inputRadio.name = 'deliveryRadio';
        inputRadio.value = i;
        if (i == 0) {
            inputRadio.setAttribute('checked', 'true');
        }
        var label = document.createElement('label');
        label.setAttribute('for', inputRadio.id);
        label.innerHTML = opt.name + ' ' + opt.days + ' giorni +' + formatCurrency(opt.price) + '€';
        deliveryOption.appendChild(inputRadio);
        deliveryOption.appendChild(label);
        $('#deliveryOptions')[0].appendChild(deliveryOption);
    }

    // Delivery selection function bindings
    $('input[type=radio][name=deliveryRadio]').change(function(e) {
        this.setDeliveryOption(parseInt(e.target.value));
    }.bind(this));

    // Finish delivery button click function binding
    $('#delivery-done').on('click', function() {
        this.finishDelivery();
    }.bind(this));
}

Form.prototype.updateCrossSellValue = function(val) {
    this.crossSellValue = val;
    $('#cross_sell').val(JSON.stringify(this.crossSellValue));
}

Form.prototype.createInput = function(name, params, stepNum = 0) {
    var type = params['type'];
    var isPhone = false;

    if (type == 'phone') {
        type = 'text';
        isPhone = true;
    }

    // Create group
    var d = $('<div>', {
        class: 'form-group input-step' + stepNum,
        style: "display: none;",
    }).appendTo($('#form-inputs'));

    // Create label
    if (type != 'hidden') {
        var l = $('<label>' + params['label'] + '</label>', {
            class: "font-form",
            style: "line-height: 1.3em; width: 100%; font-size: 18px; font-weight: 600;",
        }).appendTo(d);
    }

    // Create input
    var inputData = {
        id: name,
        name: name,
        type: type,
        style: 'margin-bottom: 0.5em; font-weight: 400; width: 100%; border-radius: 3px; padding: 15px; font-size: 22px; border-width: 1px; border-style: double; border-color: #f2e832; box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5); font-family: "Open Sans",Sans-serif; font-weight: 400;',
    };
    if ('required' in params) {
        inputData.required = params['required'];
    }
    if ('value' in params) {
        inputData.value = params['value'];
    }
    $('<input>', inputData).appendTo(d);

    // Create error span
    if (params['type'] != 'hidden') {
        $('<span>', {
            id: name + 'Error',
            class: 'span-error',
        }).appendTo(d);
    }

    if(isPhone) {
        this.forcePhoneFormatIds.push(name);
        $('#' + name).keyup(function(evt) {
            forcePhoneFormat(evt);
        });
    }
}

Form.prototype.createUpSellOption = function(quantity, discount, price, active = false, bestSeller = false) {
    var unitPrice = formatCurrency(parseFloat(price) / parseInt(quantity));

    var upSellOptions = $('#upSellOptions')[0];

    var upSellOption = document.createElement('div');
    upSellOption.setAttribute('id', 'up_sell_option_' + quantity);
    upSellOption.className = 'upSellOption';
    if (active) {
        upSellOption.className += ' upSellOptionActive';
    }

    if (bestSeller) {
        upSellOption.className += ' upSellOptionBestSeller';
        var upSellOptionHeader = document.createElement('div');
        upSellOptionHeader.className = 'upSellOptionHeader w-100';
        var upSellOptionHeaderBestSeller = document.createElement('span');
        upSellOptionHeaderBestSeller.className = 'upSellOptionHeaderBestSeller';
        upSellOptionHeaderBestSeller.innerHTML = 'il più venduto ';
        upSellOptionHeader.appendChild(upSellOptionHeaderBestSeller);
        upSellOption.appendChild(upSellOptionHeader);
    }

    var upSellOptionContent = document.createElement('div');
    upSellOptionContent.className = 'upSellOptionContent row w-100';

    var divPieces = document.createElement('div');
    divPieces.className = 'col-5 pl-5 pt-2';
    divPieces.innerHTML = quantity + ' ' + ((quantity == 1) ? 'pezzo' : 'pezzi');

    var divDiscount = document.createElement('div');
    divDiscount.className = 'col-4 pt-2 pr-0 pl-0';
    if (typeof discount != 'undefined') {
        divDiscountLabel = document.createElement('span');
        divDiscountLabel.className = 'upSellOptionBodyDiscount';
        divDiscountLabel.innerHTML = 'risparmia ' + discount + '%';
        divDiscount.appendChild(divDiscountLabel);
    }

    var divUnitPrice = document.createElement('div');
    divUnitPrice.className = 'col-3';
    var divUnitPriceAmount = document.createElement('div');
    divUnitPriceAmount.className = 'upSellOptionBodyPriceAmount pt-2';
    divUnitPriceAmount.innerHTML = '€' + unitPrice;
    var divUnitPriceLabel = document.createElement('div');
    divUnitPriceLabel.className = 'upSellOptionBodyPriceLabel pb-1';
    divUnitPriceLabel.innerHTML = 'al pezzo';
    divUnitPrice.appendChild(divUnitPriceAmount);
    divUnitPrice.appendChild(divUnitPriceLabel);

    upSellOptionContent.appendChild(divPieces);
    upSellOptionContent.appendChild(divDiscount);
    upSellOptionContent.appendChild(divUnitPrice);
    upSellOption.appendChild(upSellOptionContent);
    upSellOptions.appendChild(upSellOption);

    // Click on up sell option binding
    upSellOption.onclick = function () {
        // Update quantity and price
        var key = quantity;
        this.currentQuantity = key;
        $('#quantity').val(key);
        var price = parseFloat(this.quantity.options[key].price);
        $('#price').val(price.toFixed(2));

        // Update active
        var upSellOptions = $('.upSellOption');
        $('.upSellOption').each(function(i, obj) {
            obj.className = 'upSellOption';
        });
        $('#up_sell_option_' + quantity)[0].className += ' upSellOptionActive';

        // Update up sell total
        $('#upSellTotalAmount').html(formatCurrency(price));
    }.bind(this);
}

Form.prototype.goToInputStep = function(stepNum) {
    if (stepNum < 0 && stepNum >= this.inputSteps.length) return;

    // Hide all inputs
    for (var inputStep = 0; inputStep < this.inputSteps.length; inputStep++) {
        var inputsToHide = $('#form-inputs').find('.input-step' + inputStep);
        for (var i = 0; i < inputsToHide.length; i++) {
            $(inputsToHide[i]).css('display', 'none');
        }
    }

    // Show current step inputs
    var inputsToShow = $('#form-inputs').find('.input-step' + stepNum);
    for (var i = 0; i < inputsToShow.length; i++) {
        $(inputsToShow[i]).css('display', 'block');
    }

    // Change step
    this.currentInputStep = stepNum;
    if (this.currentInputStep + 1 < this.inputSteps.length) {
        $("#submit-button").html("Continua");
    } else {
        if (!this.deliveryEnabled) {
            $("#submit-button").html("Conferma l'ordine");
        } else {
            $("#submit-button").html("Continua");
        }
    }
}

Form.prototype.goToNextStep = function() {
    if (this.currentInputStep + 1 < this.inputSteps.length) {
        $("#submit-button").html("Continua");
        this.goToInputStep(this.currentInputStep + 1);
    } else {
        if (!this.deliveryEnabled) {
            $("#submit-button").html("Conferma l'ordine");
        } else {
            $("#submit-button").html("Continua");
        }
        this.finishInput();
    }
}

Form.prototype.setDeliveryOption = function(index) {
    if (index < 0 || index >= this.delivery.options.length) return;

    this.deliveryOption = index;

    // Calulate total price (including delivery)
    var price = parseFloat($('#price').val());
    price += parseFloat(this.delivery.options[index].price);
    $('#cartTotalAmount').html(formatCurrency(price));

    // Calulate delivery days (excluding Saturday and Sunday)
    var deliveryDay = new Date();
    var daysCounter = 0;
    var tempDate = new Date();
    while (daysCounter < parseInt(this.delivery.options[index].days)) {
        tempDate.setDate(tempDate.getDate() + 1);
        if (tempDate.getDay() != 0 && tempDate.getDay() != 6) {
            daysCounter++;
        }
    }
    deliveryDay.setDate(deliveryDay.getDate() + daysCounter);
    $('#cartDeliveryDay').html(formatDeliveryDay(deliveryDay));
}

Form.prototype.finishUpSell = function() {
    if (this.crossSellEnabled) {
        this.currentStep = 'crossSell';
        $('#upSellStep').css('display', 'none');
        $('#crossSellStep').css('display', 'block');
        $('#mainForm').css('display', 'none');
        $('#deliveryStep').css('display', 'none');
    } else {
        this.currentStep = 'input';
        $('#upSellStep').css('display', 'none');
        $('#crossSellStep').css('display', 'none');
        $('#mainForm').css('display', 'block');
        $('#deliveryStep').css('display', 'none');
    }
}

Form.prototype.finishCrossSell = function() {
    this.currentStep = 'input';
    $('#upSellStep').css('display', 'none');
    $('#crossSellStep').css('display', 'none');
    $('#mainForm').css('display', 'block');
    $('#deliveryStep').css('display', 'none');
}

Form.prototype.finishInput = function() {
    if (!this.deliveryEnabled) {
        this.currentStep = 'final';
        this.onSubmit(1);
    } else {
        this.onSubmit(1);
    }
}

Form.prototype.finishDelivery = function() {
    var price = parseFloat($('#price').val());
    price += parseFloat(this.delivery.options[this.deliveryOption].price);
    $('#price').val(price.toFixed(2));

    $('#delivery').val(this.deliveryOption);

    this.currentStep = 'final';
    this.onSubmit(1);
}

Form.prototype.crossSellAddToCart = function() {
    var updatedVal = this.crossSellValue;
    if (this.crossSellAddedToCart) {
        if (this.crossSell.hasOwnProperty('price')) {
            var price = parseFloat($('#price').val());
            price -= parseFloat(this.crossSell.price);
            $('#price').val(price.toFixed(2));
        }
        updatedVal.enabled = false;
        this.updateCrossSellValue(updatedVal);
        $('#crossSellAddButton').removeClass('cross-sell-btn-added');
        $('#crossSellAddButton').html('Aggiungi');
    } else {
        if (this.crossSell.hasOwnProperty('price')) {
            var price = parseFloat($('#price').val());
            price += parseFloat(this.crossSell.price);
            $('#price').val(price.toFixed(2));
        }
        updatedVal.enabled = true;
        this.updateCrossSellValue(updatedVal);
        $('#crossSellAddButton').addClass('cross-sell-btn-added');
        $('#crossSellAddButton').html('Aggiunto!');
    }
    this.crossSellAddedToCart = !this.crossSellAddedToCart
}

Form.prototype.validateInput = function() {
    // Form validation
    $("#submit-button").prop("disabled", true);

    this.notValidatedInput = {};

    for (var i in this.input) {
        // TODO:
        // Generalize this
        // NOTE: This supposes that surname field is always after name. If name is after then
        // the this.notValidatedInput['name'] will be overwritten.
        if (i === 'surname') {
            var n = $('#name').val(); // Name
            var s = $('#name').val(); // Surname
            var ns = n.split(' ');
            if(ns.length > 1) {
                n = ns[0];
                s = '';
                for(var index = 1; index < ns.length; index++) {
                    if (index > 1) {
                        s += ' ';
                    }
                    s += ns[index];
                }
            }
            $('#surname').val(s);
            this.notValidatedInput['name'] = n;
            this.notValidatedInput['surname'] = s;
        } else {
            var value = $('#' + i).val();
            
            // Force phone format
            if (this.forcePhoneFormatIds.includes(i)) {
                value = formatPhone(value);
            }

            this.notValidatedInput[i] = value;
        }
    }

    var data = {
        input: JSON.stringify(this.notValidatedInput)
    };

    $.ajax({
        url: this.API_URL + 'forms/' + this.FORM_ID + '/input/validate',
        method: 'POST',
        data: data,
        success: function(data) {
            console.log('[i] INPUT VALIDATED');

            this.validatedInput = this.notValidatedInput;

            if (this.currentStep == 'final') {
                this.onSubmit(2);
            } else {
                if (this.deliveryEnabled) {
                    this.currentStep = 'delivery';
                    $('#upSellStep').css('display', 'none');
                    $('#crossSellStep').css('display', 'none');
                    $('#mainForm').css('display', 'none');
                    $('#deliveryStep').css('display', 'block');
                    this.setDeliveryOption(0);
                }
            }
            $("#submit-button").removeAttr("disabled");
        }.bind(this),
        error: function(response) {
            console.log('[!] INPUT NOT VALIDATED');

            this.errors = response.responseJSON.errors;
            this.inputSetErrors();
            this.goToInputStep(this.errorsInStep);

            $("#submit-button").removeAttr("disabled");
        }.bind(this)
    });
}

Form.prototype.inputClearErrors = function() {
    this.errors = [];
    for (var i in this.input) {
        this.inputClearError(i);
    }
}

Form.prototype.inputClearError = function(input) {
    $("#" + input + "Error").html("");
    $("#" + input + "Error").css("display", "none");
}

Form.prototype.inputSetErrors = function() {
    this.errorsInStep = this.inputSteps.length;
    for (var error in this.errors) {
        for (var i = 0; i < this.errorsInStep; i++) {
            if (this.inputSteps[i].hasOwnProperty(error)) {
                this.errorsInStep = i;
            }
        }
        if (error === 'surname') {
            this.inputSetError('name', this.errors[error][0]);
        } else {
            this.inputSetError(error, this.errors[error][0]);
        }
    }
}

Form.prototype.inputSetError = function(input, message) {
    $("#" + input + "Error").css("display", "block");
    $("#" + input + "Error").html(message);
    $("#" + input).css("border-color", "red");
    $("#" + input).focus();
}

Form.prototype.resetForm = function() {
    $("#submit-button").removeAttr("disabled");
    $('#mainForm').css('display', 'block');
    $('#upSellStep').css('display', 'none');
}

/*
 * Step 1 => form validation
 * Step 2 => up sell & submission
 */
Form.prototype.onSubmit = function(step) {
    if (this.sendingForm) return;

    this.sendingForm = true;

    // Form validation
    $("#submit-button").prop("disabled", true);

    this.inputClearErrors();

    if (step == 1) {
        this.validateInput();
        this.sendingForm = false;
        return;
    } else if (step == 2) {
        // Up sell & submission
        $("#submit-button").prop("disabled", true);

        this.networkData = this.validatedInput;
        this.networkData["quantity"] = $("#quantity").val();

        // Submit
        $('#network_data').val(JSON.stringify(this.networkData));

        var data = {};
        $.each($('#mainForm').serializeArray(), function(_, kv) {
            var value = kv.value;

            // Force phone format
            if (this.forcePhoneFormatIds.includes(kv.name)) {
                value = formatPhone(value);
            }

            data[kv.name] = value;
        }.bind(this));
        if (this.landing_id > 0) {
            data["landing_id"] = this.landing_id;
        }
        var query_params = getUrlParams();
        data["query_params"] = JSON.stringify(query_params);

        this.pluginManager.onSubmit();

        $.ajax({
            url: this.API_URL + 'orders',
            method: "POST",
            dataType: "json",
            data: data,
            success: function(data) {
                console.log("[+] STORED IN TRACKER DATABASE");
                console.log(data);

                // Store the order's info
                this.order = data.data;

                this.pluginManager.afterSubmit(data);

                // Show thank you page
                //var thankyou_page = $('#thankyou_page').val();
                //window.location.replace(thankyou_page);

                if (data.hasOwnProperty('redirect') && data.redirect != '') {
                    this.pluginManager.onRedirect();
                    window.location.replace(data.redirect);
                } else {
                    $('#upSellStep').css('display', 'none');
                    $('#crossSellStep').css('display', 'none');
                    $('#mainForm').css('display', 'none');
                    $('#deliveryStep').css('display', 'none');
                    $('#thankyou-page').css('display', 'block');
                }
                this.sendingForm = false;
            }.bind(this),
            error: function(data) {
                console.log("[!] ERROR: NOT STORED IN TRACKER DATABASE");
                console.log(data);
                $("#submit-button").removeAttr("disabled");
                alert("Errore ordine!");
                this.resetForm();
                this.sendingForm = false;
            }.bind(this)
        });
    }
}

Form.prototype.getUrlParameter = function(sParam) {
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

Form.prototype.attachPluginManager = function(pluginManager) {
    this.pluginManager = pluginManager;
    console.log("[i] PLUGIN MANAGER ATTACHED");
}

Form.prototype.signupNewsletter = function() {
    this.inputClearErrors('newsletterEmail');

    var data = {
        email: $("#newsletter-email").val()
    };
    $.ajax({
        url: this.API_URL + 'orders/' + this.order.id,
        method: "PUT",
        dataType: "json",
        data: data,
        success: function(data) {
            console.log("[+] CUSTOMER'S EMAIL STORED");
            console.log(data);

            $("#type-email").css("display", "none");
            $("#email-success").css("display", "block");
        }.bind(this),
        error: function(data) {
            console.log("[!] ERROR: CUSTOMER'S EMAIL NOT STORED");
            console.log(data);

            data = data.responseJSON;

            this.inputSetError('newsletterEmail', data.errors.email);
        }.bind(this)
    });
}

var this_js_script = $('#form-js');
var form_id = this_js_script.attr('data-form-id'); 
var debug = (this_js_script.attr('data-debug') == 'true');
var alpha = (this_js_script.attr('data-alpha') == 'true'); 
var beta = (this_js_script.attr('data-beta') == 'true'); 
var pluginsList = JSON.parse(this_js_script.attr('data-plugins'));

// Create plugin manager
var pluginManager = new PluginManager();

// Add plugins
for (const [key, value] of Object.entries(pluginsList)) {
    pluginManager.add(key, value);
}

// Create form
var form = new Form(form_id, debug, alpha, beta);

// Attach the plugin manager
form.attachPluginManager(pluginManager);

// Form visited
form.formVisited();
