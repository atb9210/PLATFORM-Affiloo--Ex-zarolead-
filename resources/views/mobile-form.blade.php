<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

<div id="formContainer" style="background-color: #FE4444; width: 100%; color: white; padding: 15px;">
	<form id="mainForm" method="post" target="dummyframe" action="" accept-charset="utf-8" role="form">
   
		<!-- Backend -->
		<input type="hidden" id="order_commission" name="order_commission" value="18">
		<input type="hidden" id="order_network" name="order_network" value="bionness">
		<input type="hidden" id="fb_pixel" name="fb_pixel" value="">
		<input type="hidden" id="thankyou_page" name="thankyou_page" value="http://future-gadget.com/">
		<input type="hidden" id="api_call_url" name="api_call_url" value="">

		<!-- Frontend -->
		<input type="hidden" name="ID" id="ID" value="21389999">
		<input type="hidden" name="IDProduct" id="IDProduct" value="198">
		<input type="hidden" id="product" name="product" value="WiFaster">
		<input type="hidden" name="quantity" id="quantity" value="1">

		<label class="font-form" style="line-height: 1.3em; width: 100%; font-size: 18px; font-weight: 600;">Nome e Cognome</label>
		<div class="form-group">
			<input id="inputName" name="name" style='margin-bottom: 0.5em; font-weight: 400; width: 100%; border-radius: 3px; padding: 15px; font-size: 22px; border-width: 1px; border-style: double; border-color: #f2e832; box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5); font-family: "Open Sans",Sans-serif; font-weight: 400;' type="text" name="name" placeholder="" required="required"/>
			<span style="color: white; display: none;" id="inputNameError"></span>
		</div>
		<label class="font-form" style="line-height: 1.3em; width: 100%; font-size: 18px; font-weight: 600;">Telefono (meglio cellulare)</label>
		<div class="form-group">
			<input id="inputPhone" name="phone" style='margin-bottom: 0.5em; font-weight: 400; width: 100%; border-radius: 3px; padding: 15px; font-size: 22px; border-width: 1px; border-style: double; border-color: #f2e832; box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5); font-family: "Open Sans",Sans-serif; font-weight: 400;' type="text" name="phone" placeholder="" required="required"/>
			<span style="color: white; display: none;" id="inputPhoneError"></span>
		</div>
		<label class="font-form" style="line-height: 1.3em; width: 100%; font-size: 18px; font-weight: 600;">Città e indirizzo di consegna</label>
		<div class="form-group">
			<input id="inputAddress" name="address" style='margin-bottom: 0.5em; font-weight: 400; width: 100%; border-radius: 3px; padding: 15px; font-size: 22px; border-width: 1px; border-style: double; border-color: #f2e832; box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5); font-family: "Open Sans",Sans-serif; font-weight: 400;' type="text" name="address" placeholder="" required="required">
			<span style="color: white; display: none;" id="inputAddressError"></span>
		</div>
		<button id="submit-button" class="btn btn-lg btn-warning new-sbm-btn font-form" type="submit" style='margin-bottom: 3em; border: 1px solid #e0e0e0; font-size: 25px; padding: 10px; width: 90%; margin-left: auto !important; margin-right: auto !important; display: block; float: none; font-family: "Arial",Sans-serif;font-weight: 600; text-transform: none; color: #fff; background-color: #eaac00; border-radius: 8px; box-shadow: 0px 0px 10px 0px rgba(0,0,0,.5);'>Conferma l'Ordine</button>
	</form>
</div>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<!-- Bootstrap -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

<!-- Custom script -->
<script>
var DEBUG = true;

function makeRandomString(length) {
	var result           = '';
	var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	var charactersLength = characters.length;
	for ( var i = 0; i < length; i++ ) {
		result += characters.charAt(Math.floor(Math.random() * charactersLength));
	}
	return result;
}

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
	var names = name.split(" ");
	return (names.length >= 2 && names[0] != '' && names[1] != '');
}

function inputError(inputId, message) {
	$("#" + inputId + "Error").css("display", "block");
	$("#" + inputId + "Error").html(message);
	$("#" + inputId).css("border-color", "red");
}

var sessionId = '';
var orderId = 0;

$("#mainForm").submit(function () {
	$("#submit-button").attr("disabled", true);

	if (!validateName($("#inputName").val())) {
		inputError("inputName", "Inserisci correttamente nome e cognome");
		$("#submit-button").attr("disabled", false);
		return false;
	}
	if(!validatePhoneNumber($("#inputPhone").val())) {
		inputError("inputPhone", "Inserisci correttamente il numero di telefono");
		$("#submit-button").attr("disabled", false);
		return false;
	}
	if($("#inputAddress").val().length < 1) {
		inputError("inputAddress", "Inserisci correttamente il tuo indirizzo");
		$("#submit-button").attr("disabled", false);
		return false;
	}

	sessionId = makeRandomString(32);

	var api_url = '';
	if (!DEBUG) {
		api_url = 'http://zarolead.com/';
	}

	$.ajax({
		url: api_url + 'api/order',
		method: "POST",
		dataType: "json",
		data: $("#mainForm").serialize(),
		success: function(data) {
			console.log("[+] STORED IN TRACKER DATABASE");

			//data = JSON.parse(data);
			orderId = data.data.id;
			$('#ID').val(orderId);

			// Log into bioness
			$.ajax({
				url: 'http://biogest.bioness.online/api/v.1.0/query.php?device=affiliator&action=login&sessionid=' + sessionId,
				method: "POST",
				dataType: "json",
				data: {
					User: 'Asraf',
					Password: 'Agke5VSxKFcX'
				},
				success: function(data) {
					console.log("[i] SENT TO BIONESS");
					if(data.result == true) {
						console.log("[i] LOGGED INTO BIONESS");

						var nameArray = $("#inputName").val().split(" ");
						var clientName = nameArray[0];
						var clientSurname = '';
						for (i = 1; i < nameArray.length - 1; i += 1) {
							clientName += " " + nameArray[i];
							if (nameArray[i] != '') {
								clientSurame = nameArray[i];
							}
						}

						if (nameArray[nameArray.length - 1] != '') {
							clientSurame = nameArray[nameArray.length - 1];
						}

						var data2 = {
							"ID": parseInt($("#ID").val()),
							"IDProduct": parseInt($("#IDProduct").val()),
							"NomeCliente": clientName,
							"CognomeCliente": clientSurame,
							"Telefono": $("#inputPhone").val(),
							"QuantitaOriginale": $("#quantity").val(),


							// Not mandatory
							"Telefono2": "",
							"CittaCliente": "",
							"ProvinciaCliente": "",
							"CAPCliente": "",
							"IndirizzoCliente": $("#inputAddress").val(),
							"EmailCliente": "",
							"PrezzoFinale": "",
							"NoteGenerali": ""
						};

						console.log(data2);

						// Create new order
						$.ajax({
							url: 'http://biogest.bioness.online/api/v.1.0/query.php?device=affiliator&action=new-order&sessionid=' + sessionId,
							method: "POST",
							dataType: "json",
							data: data2,
							success: function(data) {
								console.log("[i] SENT TO BIONESS");
								console.log(data);
								if(data.result == true || data.errorNumber == 14) {
									console.log("[i] BIONESS NEW ORDER CREATED");
									$.ajax({
										url: api_url + 'api/order_sent_to_network/' + orderId,
										method: "GET",
										success: function() {
											var thankyou_page = $('#thankyou_page').val();
											window.location.replace(thankyou_page);
										}
									});
								} else {
									console.log("[!] ERROR: BIONESS NEW ORDER NOT CREATED");
									alert("Errore ordine!");
									$("#submit-button").attr("disabled", false);
								}
							},
							error: function(data) {
								console.log("[!] ERROR: BIONESS NEW ORDER NOT CREATED");
								console.log(data);
								alert("Errore ordine!");
								$("#submit-button").attr("disabled", false);
							}
						});

					} else {
						console.log("[i] CANNOT LOG INTO BIONESS");
						alert("Errore ordine!");
						$("#submit-button").attr("disabled", false);
					}

				},
				error: function(data) {
					console.log("[!] ERROR: NOT SENT TO BIONESS");
					console.log(data);
					alert("Errore ordine!");
					$("#submit-button").attr("disabled", false);
				}
			});
		},
		error: function() {
			console.log("[!] ERROR: NOT STORED IN TRACKED DATABASE");
			alert("Errore ordine!");
			$("#submit-button").attr("disabled", false);
		}
	});
	return false;
});

function getUrlParameter(sParam) {
   var sPageURL = decodeURIComponent(window.location.search.substring(1)),
	   sURLVariables = sPageURL.split('&'),
	   sParameterName,
	   i;
   for (i = 0; i < sURLVariables.length; i++) {
	   sParameterName = sURLVariables[i].split('=');
	   if (sParameterName[0] === sParam) {
		   return sParameterName[1] === undefined ? true : sParameterName[1];
	   }
   }
}

var pixel = getUrlParameter('pi');
document.getElementById('fb_pixel').value = pixel;

!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', pixel);
fbq('track', 'ViewContent');
 
function fbAddToCart() {
	// Grab those link clickers!!
	window._fbq.push('track', 'AddToCart');
}
</script>