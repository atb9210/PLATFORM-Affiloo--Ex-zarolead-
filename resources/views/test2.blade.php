<html>

<form id="mainForm" method="post" target="dummyframe" action="" accept-charset="utf-8" role="form">
    
<!-- Backend -->
<input type="hidden" id="order_product" name="order_product" value="nome del prodotto">
<input type="hidden" id="order_commission" name="order_commission" value="23.99">
<input type="hidden" id="order_network" name="order_network" value="bionness">
<input type="hidden" id="fb_pixel" name="fb_pixel" value="">
<input type="hidden" id="thankyou_page" name="thankyou_page" value="http://google.com?pi=655765675">
<input type="hidden" id="api_call_url" name="api_call_url" value="">

<!-- Frontend -->
<input type="hidden" name="ID" id="ID" value="">
<input type="hidden" name="IDProduct" id="IDProduct" value="198">
<input type="hidden" name="quantity" id="quantity" value="1">

<!--<input type="hidden" name="source_id" id="source_id" value="57308485b8777">
<input type="hidden" name="aff_sub1" id="aff_sub1">
<input type="hidden" name="aff_sub2" id="aff_sub2">
<input type="hidden" name="country" id="country" value="IT">
<input type="hidden" name="price" id="price" value="139.90">  
<input type="hidden" name="crm" id="crm" value="100000082">
-->

<center><h3 class="title font-form" style="font-size: 1.75rem !important;font-weight: 500 !important;">COMPILA IL MODULO</h3></center>

<div class="form-group">
	<label class="font-form">Nome e Cognome</label>
	<input id="inputName" type="text" name="name" class="form-control font-form" placeholder="Inserisci nome e cognome" required="required"/>
	<span style="color: red; display: none;" id="inputNameError"></span>
</div>

<div class="form-group">
    <label class="font-form">Telefono (meglio cellulare)</label>
    <input id="inputPhone" type="text" name="phone" class="form-control font-form" placeholder="Inserisci il tuo numero di telefono" required="required"/>
	<span style="color: red; display: none;" id="inputPhoneError"></span>
</div>

<div class="form-group">
    <label class="font-form">Città e indirizzo di consegna</label>
    <input id="inputAddress" type="text" name="address" class="form-control font-form" placeholder="ES. Via Aldo moro, 130" required="required">
	<span style="color: red; display: none;" id="inputAddressError"></span>
</div>

<!--<div class="form-group">
    <label class="font-form">Note per il corriere</label>
    <input name="notes" id="notes" type="text" class="form-control font-form" placeholder="ES. Citofonare al Sig. Rossi"/>
</div>-->
 
<div class="form-group font-form">
   <div class="text-center">
    <span style="
      color: black;
      padding: 4px;
      background: #ffc205;
      font-weight: bold;
      border-radius: 5px;
      ">&nbsp;<i class="fa fa-truck"></i> La spedizione è gratuita!! </span>
  </div>
</div>              
<div class="preloader loader-1"></div>
    <center>
        <div id="info-privacy" class="font-form"><small>Cliccando "Completa l'acquisto" confermi di aver preso visione 
        <a style="color: #007bff;" href="https://www.iubenda.com/privacy-policy/73319133/full-legal" target="_blank">dell'informativa sulla privacy</a>.</small></div>
        <button id="submit-button" class="btn btn-lg btn-warning new-sbm-btn font-form" type="submit" style="font-weight: bold !important;">Completa l'acquisto</button>
    </center>
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
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
	return ($("#inputName").val().split(" ").length >= 2);
}

function inputError(inputId, message) {
	$("#" + inputId + "Error").css("display", "block");
	$("#" + inputId + "Error").html(message);
	$("#" + inputId).css("border-color", "red");
}

var sessionId = '';
var orderId = 0;

$("#mainForm").submit(function () {
	
	if (!validateName($("#inputName").val())) {
		inputError("inputName", "Inserisci correttamente nome e cognome");
		return false;
	}
	if(!validatePhoneNumber($("#inputPhone").val())) {
		inputError("inputPhone", "Inserisci correttamente il numero di telefono");
		return false;
	}
	if($("#inputAddress").val().length < 1) {
		inputError("inputAddress", "Inserisci correttamente il tuo indirizzo");
		return false;
	}

	sessionId = makeRandomString(32);

	$.ajax({
		url: 'http://zarolead.com/api/order',
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
						for (i = 1; i < nameArray.length - 1; i += 1) {
							clientName += " " + nameArray[i];
						}
						
						var clientSurame = nameArray[nameArray.length - 1];
						
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
								if(data.result == true) {
									console.log("[i] BIONESS NEW ORDER CREATED");
								} else {
									console.log("[!] ERROR: BIONESS NEW ORDER NOT CREATED");
								}
							},
							error: function(data) {
								console.log("[!] ERROR: BIONESS NEW ORDER NOT CREATED");
								console.log(data);
							}
						});
						
					} else {
						console.log("[i] CANNOT LOG INTO BIONESS");
					}
					
				},
				error: function(data) {
					console.log("[!] ERROR: NOT SENT TO BIONESS");
					console.log(data);
				}
			});
			
			var thankyou_page = $('#thankyou_page').val();
			//window.location.replace(thankyou_page);
		},
		error: function() {
			console.log("[!] ERROR: NOT STORED IN TRACKED DATABASE");
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

</html>