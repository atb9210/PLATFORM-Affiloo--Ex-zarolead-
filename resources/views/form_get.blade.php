<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

<style>
/* TODO: to delete this? Check if everything is fine graphically */
/*
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
*/

.span-error {
	display: none;
	color: #9c0000;
	background-color: white;

	padding: 0.5em;
	margin-bottom: 1em;
}

#formContainer {
    width: 100%;

    /*
    color: white;
    background-color: #FE4444;
    */
    color: black;
    background-color: white;
}

#mainForm {
   padding: 15px;
   height: 100%;
}

#submit-button {
    margin-bottom: 3em;
    border: 1px solid #e0e0e0;
    font-size: 25px;
    padding: 10px;
    width: 90%;
    margin-left: auto !important;
    margin-right: auto !important;
    display: block;
    float: none;
    font-family: "Arial",Sans-serif;
    ont-weight: 600;
    text-transform: none;
    color: #fff;
    background-color: #eaac00;
    border-radius: 8px;
    box-shadow: 0px 0px 10px 0px rgba(0,0,0,.5);
}

.form-button {
    margin-bottom: 3em;
    border: 1px solid #e0e0e0;
    font-size: 25px;
    padding: 10px;
    width: 90%;
    margin-left: auto !important;
    margin-right: auto !important;
    display: block;
    float: none;
    font-family: "Arial",Sans-serif;
    font-weight: 600;
    text-transform: none;
    color: #fff;
    background-color: #eaac00;
    border-radius: 8px;
    box-shadow: 0px 0px 10px 0px rgba(0,0,0,.5);
}

#upSellOptions {
    transition: opacity .2s ease;
    opacity: 1;
    padding: 5px;
}

.upSellOption {
    background: rgb(255, 255, 255);
    box-shadow: 0 1px 1px rgba(0,0,0,.1), 0 9px 0 -5px #eee, 0 9px 1px -4px rgba(0,0,0,.1), 0 18px 0 -10px #eee, 0 18px 1px -9px rgba(0,0,0,.1);
    transition: box-shadow .2s cubic-bezier(.4,0,1,1),transform .2s cubic-bezier(.4,0,1,1);
    border: 1px solid #e2e2e2;
    border-radius: 10px;
    position: relative;
    margin-bottom: 20px;
    padding: 10px 0px;
}

.upSellOptionActive {
    background: rgb(255, 219, 99);
    border: 3px solid #444444;
    border-left-width: 10px;
}

.upSellOptionBestSeller {
    border: 1px solid #444444;
}

.upSellOptionHeader {
    padding: 0;
    border: none;
}

.upSellOptionHeaderBestSeller {
    background-color: #444444;
    color: white;
    text-transform: uppercase;
    padding-top: 2px;
    padding-bottom: 2px;
    padding-right: 10px;
    padding-left: 5px;
    border-top-right-radius: 50px;
    border-bottom-right-radius: 50px;
}

.upSellOptionContent {
    padding: 0;
    border: none;
}

.upSellOptionBodyDiscount {
    font-weight: 600;
    padding: 5px 8px;
    margin: 3px 0;
    display: inline-flex;
    line-height: 1;
    font-size: 12px;
    max-height: 24px;
    border: 1px solid #484848;
    flex-shrink: 0!important;
    color: #484848;
    align-items: center;
    justify-content: center;
    border-radius: 50px;
    text-transform: uppercase;
}

.upSellOptionBodyPriceAmount {
    font-size: 18px;
    font-weight: 700;
}

.upSellOptionBodyPriceLabel {
    color: #5f5f5f;
    font-size: 11px;
}

.upSellTotalReview {
    font-size: 20px;
    font-weight: 700;
}

/********************/

#upSellTable {
    border-collapse: initial;
    border-spacing: 10px 0px;
    width: 100%;
    margin-bottom: 30px;
}

#upSellTable > tbody {
    color: #333;
}

#upSellTable .BEST-SELLER {
    font-size: 11px;
    font-weight: 700;
    font-style: normal;
    font-stretch: normal;
    letter-spacing: normal;
    text-align: center;
    background-color: #f3ca66;
    color: #232f3e;
    padding: 2px 0 0!important;
}

#upSellTable td {
    text-align: center !important;
    padding: 5px 0px !important;
}

#upSellTable .product_price_top {
    border: 1.5px solid #f3ca66;
    width: 33%;
    box-shadow: 1px 1px 2px 0 rgba(0,0,0,.16);
    background-color: #fff6e1;
    cursor: pointer;
}

#upSellTable .product_price_basic {
    border: 1px solid #bfbfbf;
    width: 33%;
    cursor: pointer;
}

#upSellTable .total_price_qty_btns {
    background-color: #ccc;
    border: 0;
    height: 1px;
    width: 70%;
}

#upSellTable .total_qty_price {
    font-size: 15px;
    color: #a9a9a9;
    display: block;
    line-height: 19px;
    margin-top: 5px;
}

#crossSellStep {
}

#crossSellStep > div.crossSellProduct {
    padding: 0px 10px;
    margin-bottom: 30px;
}

#crossSellAddButton {
    width: 100%;
    margin-bottom: 0em;
    background-color: #5fba04;
}

.cross-sell-btn-added {
    background-color: #9dc476 !important;
}
</style>

<div id="formContainer">

    <!-- Order form page -->
	<form id="mainForm" method="post" target="dummyframe" action="" accept-charset="utf-8" role="form">
   
		<!-- Backend -->
		<input type="hidden" id="form_id" name="form_id" value="{{ $form_id }}">
		<input type="hidden" id="commission" name="commission" value="29.99">
		<input type="hidden" id="sub_commission" name="sub_commission" value="9.99">
		<input type="hidden" id="fb_pixel" name="fb_pixel" value="">
		<input type="hidden" id="network_data" name="network_data" value="">

		<!-- Frontend -->
		<input type="hidden" name="quantity" id="quantity" value="1">
		<input type="hidden" name="price" id="price" value="0">
		<input type="hidden" name="cross_sell" id="cross_sell" value="{}">
		<input type="hidden" name="delivery" id="delivery" value="">

        <div id="form-inputs">
            <!--
            <div class="form-group">
                <label class="font-form" style="line-height: 1.3em; width: 100%; font-size: 18px; font-weight: 600;">Nome e Cognome</label>
                <input id="name" value="test test" name="name" style='margin-bottom: 0.5em; font-weight: 400; width: 100%; border-radius: 3px; padding: 15px; font-size: 22px; border-width: 1px; border-style: double; border-color: #f2e832; box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5); font-family: "Open Sans",Sans-serif; font-weight: 400;' type="text" name="name" placeholder="" required="required"/>
                <span class="span-error" id="nameError"></span>
            </div>
            <div class="form-group">
                <label class="font-form" style="line-height: 1.3em; width: 100%; font-size: 18px; font-weight: 600;">Telefono (meglio cellulare)</label>
                <input id="phone" value="123456789" name="phone" style='margin-bottom: 0.5em; font-weight: 400; width: 100%; border-radius: 3px; padding: 15px; font-size: 22px; border-width: 1px; border-style: double; border-color: #f2e832; box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5); font-family: "Open Sans",Sans-serif; font-weight: 400;' type="text" name="phone" placeholder="" required="required"/>
                <span class="span-error" id="phoneError"></span>
            </div>
            <div class="form-group">
                <label class="font-form" style="line-height: 1.3em; width: 100%; font-size: 18px; font-weight: 600;">Città e indirizzo di consegna</label>
                <input id="address" value="Via moro 1" name="address" style='margin-bottom: 0.5em; font-weight: 400; width: 100%; border-radius: 3px; padding: 15px; font-size: 22px; border-width: 1px; border-style: double; border-color: #f2e832; box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5); font-family: "Open Sans",Sans-serif; font-weight: 400;' type="text" name="address" placeholder="" required="required">
                <span class="span-error" id="addressError"></span>
            </div>
            -->
        </div>
		<button id="submit-button" class="btn btn-lg btn-warning new-sbm-btn font-form form-button">Conferma l'Ordine</button>
	</form>

    <!-- Upsell step -->
	<div id="upSellStep" style="display: block; padding-bottom: 15px; height: 100%;">
		<div style="background-color: black; padding: 10px; margin-bottom: 15px;">
			<p style="color: white; text-align: center; font-family: 'Roboto',Sans-serif; font-weight: 400; font-size: 16px;">
				<span style="text-decoration: underline; color: #ff0000;"><strong>ATTENZIONE!</strong></span> Solo per pochi minuti puoi ricevere 
				<span style="text-decoration: underline;"><strong>un'offerta</strong></span> 
				ad un Prezzo Speciale!
			</p>
		</div>
        <div id="upSellOptions">
            <!--
            <div class="upSellOption upSellOptionActive">
                <div class="upSellOptionHeader w-100">
                    <span class="upSellOptionHeaderBestSeller">
                        il più venduto
                    </span>
                </div>
                <div class="upSellOptionContent row w-100">
                    <div class="col-5 pl-5 pt-2">
                        1 pezzo
                    </div>
                    <div class="col-4 pt-2 pr-0 pl-0">
                        <span class="upSellOptionBodyDiscount">
                            RISPARMIA 10%
                        </span>
                    </div>
                    <div class="col-3">
                        <div class="upSellOptionBodyPriceAmount pt-2">€17,99</div>
                        <div class="upSellOptionBodyPriceLabel pb-1">al pezzo</div>
                    </div>
                </div>
            </div>
            <div class="upSellOption">
                <div class="upSellOptionContent row w-100">
                    <div class="col-5 pl-5 pt-2">
                        2 pezzi
                    </div>
                    <div class="col-4 pt-2 pr-0 pl-0">
                        <span class="upSellOptionBodyDiscount">
                            RISPARMIA 10%
                        </span>
                    </div>
                    <div class="col-3">
                        <div class="upSellOptionBodyPriceAmount pt-2">€17,99</div>
                        <div class="upSellOptionBodyPriceLabel pb-1">al pezzo</div>
                    </div>
                </div>
            </div>
            -->
        </div>
        <div class="upSellTotalReview w-100 text-center mb-3">
            TOTALE €<span id="upSellTotalAmount">17,99</span>
        </div>
        <!-- Old up sell -->
        <!--
        <table id="upSellTable">
            <tbody>
                <tr>
                    <td class="product_price_top_up"></td>
                    <td class="product_price_top_up BEST-SELLER">IL PIÙ VENDUTO</td>
                    <td class="product_price_top_up"></td>
                </tr>
                <tr>
                    <td class="product_qty mp_product_qty_1 product_price_top">
                        <b>1</b>x <span class="price_qty">21,99€</span><span class="per_qty">/pz.</span>
                        <hr class="total_price_qty_btns">
                        <span class="total_qty_price"> Importo totale: 21,99€ </span>
                    </td>
                    <td class="product_qty mp_product_qty_2 product_price_basic"><b>2</b>x <span class="price_qty">17,59€</span><span class="per_qty">/pz.</span>
                        <hr class="total_price_qty_btns">
                        <span class="total_qty_price"> Importo totale: 35,18€ </span>
                    </td>
                    <td class="product_qty mp_product_qty_3 product_price_basic"><b>3</b>x <span class="price_qty">15,39€</span><span class="per_qty">/pz.</span>
                        <hr class="total_price_qty_btns">
                        <span class="total_qty_price"> Importo totale: 46,17€ </span>
                    </td>
                </tr>
            </tbody>
        </table>
        -->
		<button id="up-sell-done" class="btn btn-lg btn-warning new-sbm-btn font-form form-button">Continua</button>
	</div>

    <!-- Cross-sell step -->
    <div id="crossSellStep" style="display: none; padding-bottom: 15px; height: 100%;">
		<div style="background-color: black; padding: 10px; margin-bottom: 15px;">
			<p style="color: white; text-align: center; font-family: 'Roboto',Sans-serif; font-weight: 400; font-size: 16px;">
				<span style="text-decoration: underline; color: #ff0000;"><strong>ATTENZIONE!</strong></span> Solo per pochi minuti puoi ricevere 
				<span style="text-decoration: underline;"><strong>un'offerta</strong></span> 
				ad un Prezzo Speciale!
			</p>
		</div>
        <div class="crossSellProduct">
            <div style="width: 100%; border-radius: 5px; box-shadow: 0 0 3px #000; border: 1px solid #555555;">
                <img id="crossSellImg" style="width: 100%; border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom: 1px solid #999;" src="">
                <div class="row">
                    <div class="col-6" style="text-align: center; font-size: 32px;">
                        +<span id="crossSellPrice"></span>€
                    </div>
                    <div class="col-6">
		                <button id="crossSellAddButton" class="btn btn-lg btn-warning new-sbm-btn font-form form-button">Aggiungi</button>
                    </div>
                </div>
                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-12" style="text-align: center; font-size: 20px;">
                        <span id="crossSellTitle"></span>
                    </div>
                    <div class="col-12" style="text-align: center; font-size: 14px;">
                        <span id="crossSellDescription"></span>
                    </div>
                </div>
            </div>
        </div>
		<button id="cross-sell-done" class="btn btn-lg btn-warning new-sbm-btn font-form form-button">Continua</button>
	</div>

    <!-- Delivery step -->
    <div id="deliveryStep" style="display: none; padding: 15px; height: 100%;">
        <div class="row">
            <div class="col-12" style="text-align: center; margin-bottom: 20px;">
                <span style="font-size: 22px;">Scegli il tipo di spedizione:</span>
            </div>
        </div>
        <div id="deliveryOptions" class="row" style="margin-bottom: 20px;">
            <div class="col-12">
                <input type="radio" id="male" name="gender" value="male">
                <label for="male">Standard delivery 5 giorni +5,00€</label>
            </div>
            <div class="col-12">
                <span>Express delivery 2 giorni +7,00€</span>
            </div>
        </div>
        <div id="cartReview" class="row" style="margin-bottom: 30px; text-align: center; font-size: 22px;">
            <div class="col-12">
                Totale ordine: <span id="cartTotalAmount"></span>€
            </div>
            <div class="col-12" style="margin-bottom: 20px;">
                Consegna stimata: entro il <span id="cartDeliveryDay"></span>
            </div>
        </div>
		<button id="delivery-done" class="btn btn-lg btn-warning new-sbm-btn font-form form-button">Continua</button>
	</div>

    <!-- Thankyou page -->
	<div id="thankyou-page" style="display: none; height: 100%;">
		<div style="text-align: center; padding: 15px;">
			<img class="img" src="{{ asset('images/order-confirmed.jpg') }}" style="width: 50%;">
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
        <div>
            <div id="type-email">
                <p>Inserisci la tua email per ricevere informazioni riguardo al tuo ordine ed offerte speciali</p>
                <div class="form-group">
                    <label class="font-form" style="line-height: 1.3em; width: 100%; font-size: 18px; font-weight: 600;">Email</label>
                    <input type="email" id="newsletter-email" placeholder="example@email.com" style='margin-bottom: 0.5em; font-weight: 400; width: 100%; border-radius: 3px; padding: 15px; font-size: 22px; border-width: 1px; border-style: double; border-color: #f2e832; box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5); font-family: "Open Sans",Sans-serif; font-weight: 400;' type="text" name="name" placeholder="" required="required"/>
                    <span class="span-error" id="newsletterEmailError"></span>
                </div>
                <button id="submit-button3" class="btn btn-lg btn-warning new-sbm-btn font-form" style='margin-top: 1em; margin-bottom: 1em; border: 1px solid #e0e0e0; font-size: 25px; padding: 10px; width: 90%; margin-left: auto !important; margin-right: auto !important; display: block; float: none; font-family: "Arial",Sans-serif;font-weight: 600; text-transform: none; color: #fff; background-color: #eaac00; border-radius: 8px; box-shadow: 0px 0px 10px 0px rgba(0,0,0,.5);'><i aria-hidden="true" class="fas fa-gift"></i>&nbsp;Ricevi informazioni</button>
            </div>
            <div id="email-success" style="display: none;">
                <div style="background-color: #3e9165; padding: 10px;">
                    <p style="color: white; text-align: center; font-family: 'Roboto',Sans-serif; font-weight: 400; font-size: 16px; margin-bottom: 0px;">
                        <strong>Riceverai informazioni riguardo al tuo ordine per email</strong>
                    </p>
                </div>
            </div>
		</div>
	</div>

</div>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<!-- Bootstrap -->
<!--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>-->

<!-- Custom script -->
<script id="plugin-js" src="{{ asset('js/plugins/plugin.js') }}"></script>
@foreach($plugins as $plugin => $value)
    @if(file_exists(public_path('js/plugins/' . $plugin . '.js')))
        <script id="plugin-{{$plugin}}-js" src="{{ asset('js/plugins/' . $plugin . '.js') }}"></script>
    @endif
@endforeach
<?php
$plugins = json_encode($plugins);
$debug = env('APP_LOCAL', 'false') ? 'true' : 'false';
$beta = env('APP_BETA', 'false') ? 'true' : 'false';
?>
<script id="form-js" src="{{ asset('js/form.js') }}" data-form-id="{{ $form_id }}" data-plugins="{{ $plugins }}" data-debug="{{ $debug }}" data-beta="{{ $beta }}"></script>
