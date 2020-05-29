
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap -->
<!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">-->

<style>
body {
	padding: 0px;
	margin: 0px;
}
.row::after {
    content: "";
    clear: both;
    display: table;
}
.col-6 {
	float: left;
    padding: 15px;
    border: 0px solid red;
	width: 50%;
}
* {
  box-sizing: border-box;
}
.span-error {
	color: white;
	display: none;
	background-color: #eaac00;
	padding: 0.5em;
	margin-bottom: 1em;
}
</style>

<div id="formContainer" style="background-color: #FE4444; width: 100%; color: white;">
	<form id="mainForm" method="post" target="dummyframe" action="" accept-charset="utf-8" role="form" style="padding: 15px; height: 100%;">
   
		<!-- Backend -->
		<input type="hidden" id="form_id" name="form_id" value="1">
		<input type="hidden" id="user_id" name="user_id" value="2">
		<input type="hidden" id="order_commission" name="commission" value="0.00">
		<input type="hidden" id="order_sub_commission" name="sub_commission" value="0.00">
		<input type="hidden" id="network_id" name="network_id" value="1">
		<input type="hidden" id="fb_pixel" name="fb_pixel" value="">
		<input type="hidden" id="thankyou_page" name="thankyou_page" value="http://future-gadget.com/">
		<input type="hidden" id="api_call_url" name="api_call_url" value="https://network.worldfilia.net/manager/inventory/buy/kalai_stepLuxe.json">
		<input type="hidden" id="api_call_method" name="api_call_method" value="POST">
		<input type="hidden" id="api_success_response" name="api_success_response" value='{"status":"success"}'>
		<input type="hidden" id="network_data" name="network_data" value="">

		<!-- Frontend -->
		<input type="hidden" id="product_id" name="product_id" value="3">
		<input type="hidden" name="quantity" id="quantity" value="1">
		<input type="hidden" name="price" id="price" value="49.00">

		<input type="hidden" id="source_id" name="source_id" value="913eb3f7a1d5">
		<input type="hidden" id="aff_sub1" name="aff_sub1" value="">
		<input type="hidden" id="aff_sub2" name="aff_sub2" value="">

		<label class="font-form" style="line-height: 1.3em; width: 100%; font-size: 18px; font-weight: 600;">Nome e Cognome</label>
		<div class="form-group">
			<input id="inputName" name="name" style='margin-bottom: 0.5em; font-weight: 400; width: 100%; border-radius: 3px; padding: 15px; font-size: 22px; border-width: 1px; border-style: double; border-color: #f2e832; box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5); font-family: "Open Sans",Sans-serif; font-weight: 400;' type="text" name="name" placeholder="" required="required"/>
			<span class="span-error" id="inputNameError"></span>
		</div>
		<label class="font-form" style="line-height: 1.3em; width: 100%; font-size: 18px; font-weight: 600;">Telefono (meglio cellulare)</label>
		<div class="form-group">
			<input id="inputPhone" name="phone" style='margin-bottom: 0.5em; font-weight: 400; width: 100%; border-radius: 3px; padding: 15px; font-size: 22px; border-width: 1px; border-style: double; border-color: #f2e832; box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5); font-family: "Open Sans",Sans-serif; font-weight: 400;' type="text" name="phone" placeholder="" required="required"/>
			<span class="span-error" id="inputPhoneError"></span>
		</div>
		<label class="font-form" style="line-height: 1.3em; width: 100%; font-size: 18px; font-weight: 600;">Città e indirizzo di consegna</label>
		<div class="form-group">
			<input id="inputAddress" name="address" style='margin-bottom: 0.5em; font-weight: 400; width: 100%; border-radius: 3px; padding: 15px; font-size: 22px; border-width: 1px; border-style: double; border-color: #f2e832; box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5); font-family: "Open Sans",Sans-serif; font-weight: 400;' type="text" name="address" placeholder="" required="required">
			<span class="span-error" id="inputAddressError"></span>
		</div>
		<button id="submit-button" class="btn btn-lg btn-warning new-sbm-btn font-form" style='margin-bottom: 3em; border: 1px solid #e0e0e0; font-size: 25px; padding: 10px; width: 90%; margin-left: auto !important; margin-right: auto !important; display: block; float: none; font-family: "Arial",Sans-serif;font-weight: 600; text-transform: none; color: #fff; background-color: #eaac00; border-radius: 8px; box-shadow: 0px 0px 10px 0px rgba(0,0,0,.5);'>Conferma l'Ordine</button>
	</form>
	<div id="mainForm2" style="display: none; padding-bottom: 15px; height: 100%;">
		<div style="background-color: black; padding: 10px;">
			<p style="color: white; text-align: center; font-family: 'Roboto',Sans-serif; font-weight: 400; font-size: 16px;">
				<span style="text-decoration: underline; color: #ff0000;"><strong>ATTENZIONE</strong></span>Solo per pochi minuti hai puoi ricevere 
				<span style="text-decoration: underline;"><strong>2 WiFaster</strong></span> 
				ad un Prezzo Speciale!
			</p>
		</div>
		<div class="row" style="margin-left: 0px; margin-right: 0px;">
			<div class="col-6">
				<img class="img" src="http://future-gadget.com/wp-content/uploads/2019/12/small-2-removebg-preview.png" style="width: 100%;">
			</div>
			<div class="col-6">
				<img class="img" src="http://future-gadget.com/wp-content/uploads/2019/12/small-2-removebg-preview.png" style="width: 100%;">
			</div>
		</div>
		<div style="background-color: #23a455; padding: 10px;">
			<p style="color: white; text-align: center; font-family: 'Roboto',Sans-serif; font-weight: 400; font-size: 24px; padding: 0px; margin: 0px;">
    			<strong>
    				2 WiFaster
    			</strong>
			</p>
		</div>
		<div class="row" style="margin-left: 0px; margin-right: 0px; background-color: white;">
			<div class="col-6" style="margin-bottom: 1em; margin-top: 1em; text-align: center;">
				<span style="color: #ff0a0a; text-align: center; font-family: 'Roboto',Sans-serif; font-weight: 400; font-size: 48px; text-decoration: line-through;">
					379
    			</span>
			</div>
			<div class="col-6" style="margin-bottom: 1em; margin-top: 1em;">
				<span style="color: #23a454; text-align: left; font-family: 'Roboto',Sans-serif; font-weight: 400; font-size: 58px;">
					79&euro;
    			</span>
			</div>
		</div>
		<button id="submit-button2-ok" class="btn btn-lg btn-warning new-sbm-btn font-form" style='margin-top: 1em; margin-bottom: 1em; border: 1px solid #e0e0e0; font-size: 25px; padding: 10px; width: 90%; margin-left: auto !important; margin-right: auto !important; display: block; float: none; font-family: "Arial",Sans-serif;font-weight: 600; text-transform: none; color: #fff; background-color: #eaac00; border-radius: 8px; box-shadow: 0px 0px 10px 0px rgba(0,0,0,.5);'><i aria-hidden="true" class="fas fa-gift"></i>&nbsp;Ricevi offerta</button>
		<button id="submit-button2-no" class="btn btn-lg btn-warning new-sbm-btn font-form" style='margin-bottom: 3em; border: 1px solid #e0e0e0; font-size: 20px; padding: 10px; width: 80%; margin-left: auto !important; margin-right: auto !important; display: block; float: none; font-family: "Arial",Sans-serif;font-weight: 600; text-transform: none; color: #fff; background-color: #888888; border-radius: 8px; box-shadow: 0px 0px 10px 0px rgba(0,0,0,.5);'><i aria-hidden="true" class="fas fa-gift"></i>&nbsp;No grazie</button>
	</div>
	<div id="thankyou-page" style="display: none; height: 100%;">
		<div style="text-align: center; padding: 15px;">
			<img class="img" src="http://future-gadget.com/wp-content/uploads/2019/12/Package-accept.png" style="width: 50%;">
		</div>
		<div style="background-color: #23a455; padding: 10px;">
			<p style="color: white; text-align: center; font-family: 'Roboto',Sans-serif; font-weight: 400; font-size: 24px; margin-bottom: 0px;">
				<strong>ORDINE CONFERMATO</strong>
			</p>
		</div>
		<div style="background-color: #3e9165; padding: 10px;">
			<p style="color: white; text-align: center; font-family: 'Roboto',Sans-serif; font-weight: 400; font-size: 16px; margin-bottom: 0px;">
				<strong>Il Tuo Ordine è Stato Inviato Correttamente</strong>
			</p>
		</div>
	</div>
</div>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<!-- Bootstrap -->
<!--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>-->

<!-- Custom script -->
<script>
var DEBUG = false;
var BETA = true;

var UPSELL_ENABLED = false;
var UPSELL = {
	'submit-button2-ok': {
		'quantity': '2',
		'price': 79.00,
		'order_commission': 79.00,
	},
	'submit-button2-no': {
		'quantity': '1',
		'price': 49.00,
		'order_commission': 49.00,
	},
};

function formVisited() {
	visitData = {
		form_id: $('#form_id').val(),
	};

	// Submit
	var api_url = 'http://';
    if (BETA) {
        api_url += 'beta2.';
    }
    if (DEBUG) {
        api_url += 'localhost/';
    } else {
        api_url += 'zarolead.com/';
    }
    api_url += 'api/v1/';

	$.ajax({
		url: api_url + 'form_visited',
		method: 'POST',
		dataType: 'json',
		data: visitData,
		success: function(data) {
			console.log('[i] FORM VISITED');
		},
		error: function(data) {
			console.log('[!] FORM NOT VISITED');
		}
	});
}
formVisited();

function validatePhoneNumber(phoneNumber) {
	if (phoneNumber.length < 9) {
		return false;
	}
	var allowedChars = ['+', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ' '];
	for (i = 0; i < phoneNumber.length; i++) {
		if (!allowedChars.includes(phoneNumber[i])) {
			return false;
		}
	}
	return true;
}

function validateName(name) {
	return (name != "");
}

function inputError(inputId, message) {
	$("#" + inputId + "Error").css("display", "block");
	$("#" + inputId + "Error").html(message);
	$("#" + inputId).css("border-color", "red");
	$("#" + inputId).focus();
}

var sessionId = '';
var orderId = 0;
var networkData = {};

function resetForm() {
	$("#submit-button").attr("disabled", false);
	$('#mainForm').css('display', 'block');
	$('#mainForm2').css('display', 'none');
}

/*
 * Step 1 => form validation
 * Step 2 => upsell & submission
 */
function onSubmit(step) {
	console.log("STEP " + step);

	if (step == 1) {
		// Form validation
		$("#submit-button").attr("disabled", true);

		if (!validateName($("#inputName").val())) {
			inputError("inputName", "Inserisci correttamente nome e cognome");
			resetForm();
			return false;
		}
		if(!validatePhoneNumber($("#inputPhone").val())) {
			inputError("inputPhone", "Inserisci correttamente il numero di telefono");
			resetForm();
			return false;
		}
		if($("#inputAddress").val().length < 1) {
			inputError("inputAddress", "Inserisci correttamente il tuo indirizzo");
			resetForm();
			return false;
		}

		if (UPSELL_ENABLED) {
			$('#mainForm').css('display', 'none');
			$('#mainForm2').css('display', 'block');
		} else {
			onSubmit(2);
		}
	} else if (step == 2) {
		// Upsell & submission

		networkData = {
			source_id: $('#source_id').val(),
			aff_sub1: $('#aff_sub1').val(),
			aff_sub2: $('#aff_sub2').val(),
			name: $('#inputName').val(),
			phone: $("#inputPhone").val(),
			quantity: $("#quantity").val(),
			address: $("#inputAddress").val(),
			doubleCheck: 1,
		};

		// Submit
        var api_url = 'http://';
        if (BETA) {
            api_url += 'beta2.';
        }
        if (DEBUG) {
            api_url += 'localhost/';
        } else {
            api_url += 'zarolead.com/';
        }
        api_url += 'api/v1/';

		$('#network_data').val(JSON.stringify(networkData));
		console.log($('#network_data').val());

		$.ajax({
			url: api_url + 'order',
			method: "POST",
			dataType: "json",
			data: $("#mainForm").serialize(),
			success: function(data) {
				console.log("[+] STORED IN TRACKER DATABASE");

				console.log(data);

				//data = JSON.parse(data);
				//orderId = data.data.id;
				//$('#ID').val(orderId);

				fbPurchase();

				// Show thank you page
				//var thankyou_page = $('#thankyou_page').val();
				//window.location.replace(thankyou_page);

				if (data.hasOwnProperty('redirect') && data.redirect != '') {
					window.location.replace(data.redirect);
				} else {
					$('#mainForm').css('display', 'none');
					$('#mainForm2').css('display', 'none');
					$('#thankyou-page').css('display', 'block');
					
				}
			},
			error: function(data) {
				console.log("[!] ERROR: NOT STORED IN TRACKED DATABASE");
				console.log(data);
				alert("Errore ordine!");
				resetForm();
			}
		});
	}
}

$('#mainForm').submit(false);

$("#submit-button").click(function() {
	onSubmit(1);
});

for (buttonId in UPSELL) {
	$('#' + buttonId).click(function(event) {
		var quantity = UPSELL[event.target.id]['quantity'];
		var price = UPSELL[event.target.id]['price'];
		var order_commission = UPSELL[event.target.id]['order_commission'];
		$('#quantity').val(quantity);
		$('#price').val(price);
		$('#order_commission').val(order_commission);
		onSubmit(2);
	});
}

$(".addtocart-button").each(function() {
	fbAddToCart();
});

function getUrlParameter(sParam) {
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

!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');

// Default pixel is in fb_pixel input
var pixel = getUrlParameter('pi');
if (pixel == '') {
	pixel = $('#fb_pixel').val();
}
$('#fb_pixel').val(pixel);

fbq('init', pixel);
fbq('track', 'ViewContent');
 
function fbAddToCart() {
	// Grab those link clickers!!
	fbq('track', 'AddToCart');
	
}

function fbPurchase() {
	fbq('track', 'Purchase');
}
</script>

