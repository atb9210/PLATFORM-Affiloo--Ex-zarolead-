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
		<input type="hidden" id="order_commission" name="commission" value="29.99">
		<input type="hidden" id="order_sub_commission" name="sub_commission" value="9.99">
		<input type="hidden" id="fb_pixel" name="fb_pixel" value="">
		<input type="hidden" id="network_data" name="network_data" value="">

		<!-- Frontend -->
		<input type="hidden" name="quantity" id="quantity" value="1">
		<input type="hidden" name="price" id="price" value="99.99">

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
<script id="form-js" src="js/form.js" data-form-id="1" data-debug="true" data-beta="false"></script>
