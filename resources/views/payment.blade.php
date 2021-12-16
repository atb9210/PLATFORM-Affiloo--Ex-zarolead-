<html>

<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<!-- Font Awsome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" integrity="sha512-RXf+QSDCUQs5uwRKaDoXt55jygZZm2V++WUZduaU/Ui/9EGp3f/2KZVahFZBKGH0s774sd3HmrhUy+SgOFQLVQ==" crossorigin="anonymous"></script>

<!-- axios -->
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<!-- Google maps API (Places) -->
<script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBXvXzU_AAxACrNdbHiWTLeY5l63jArjTM&libraries=places"></script>

<!-- Stripe -->
<script src="https://js.stripe.com/v3/"></script>

<style>
.fs-7 {
    font-size: 0.75rem !important;
}

.btn {
    background-color: #2b4369;
    color: #ffffff;
    border-color: #1a1158;
}

#loading {
    position: fixed;
    display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    text-align: center;
    opacity: 0.7;
    background-color: #555555;
    z-index: 99;
}

#loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('//upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Phi_fenomeni.gif/50px-Phi_fenomeni.gif') 
        50% 50% no-repeat rgb(249,249,249);
}

.hidden {
    display: none;
}

.bg-white {
    background-color: #ffffff;
}

.bg-gray {
    background-color: #eeeeee;
}

.bg-green-500 {
    background-color: #23a455;
}

.text-green {
    color: green;
}

.step {
    width: 100%;
    display: none;
}

/* CSS rule created by me to fix the height of the stripe card element */
#cardElement {
    padding-top: 0.65rem !important;
    padding-bottom: 0.65rem !important;
}
</style>

</head>

<body onload="onLoad();">

<div id="loading">
    <div id="loader"></div>
</div>

<div id="app">

    <!-- Checkout form -->
    <form id="mainForm" class="p-3" action="#" novalidate>

        <div id="step1" class="step">

            <h2 class="mb-3 text-center">
                RIEPILOGO
            </h2>

            <div id="review" class="mb-3">
            </div>

            <hr />

            <h2 class="mb-3 text-center">
                PAGAMENTO ONLINE CON CARTA DI CREDITO
            </h2>

            <div class="mb-3">
                <label class="form-label">Carta di credito</label>
                <div id="cardElement"></div>
                <div id="cardErrors" role="alert" class="invalid-feedback"></div>
            </div>

            <div class="mb-3 mt-3">
                <button id="payButton" class="btn btn-primary w-100 p-2 fs-4" type="submit">Paga ora</button>
            </div>

        </div>


        <div id="step2" class="step">

            <h2 class="mb-3 text-center">
                ORDINE COMPLETATO
            </h2>

            <div class="bg-gray p-3 border border-black shadow-sm mb-3">
                <strong class="text-green">Grazie. Pagamento effettuato.</strong>
                <ul>
                    <li>Numero ordine: <strong id="orderNumber">0000</strong></li>
                    <li>Data: <strong id="orderDate">0 gennaio 1999</strong></li>
                    <li>Totale: <strong>€ <span id="orderTotal">0.00</span></strong></li>
                    <li>Metodo di pagamento: <strong id="orderPaymentMethod">Pagamento alla consegna</strong></li>
                </ul>
            </div>

            <h2>Dettagli ordine</h2>
            <div id="completeReview" class="row fs-7">

                <div class="row fw-bold">
                    <div class="col-6 text-start">
                        PRODOTTO
                    </div>
                    <div class="col-6 text-end">
                        TOTALE
                    </div>
                </div>

                <hr class="my-0">

            </div>

        </div>
        

        <div class="mb-3 mt-5 row fs-6">

            <div class="col-3">
                <img class="w-100" src="/images/checkout/icon1.png">
                <p class="w-100 m-0 p-0">
                    100% acquisto sicuro
                </p>
            </div>
            <div class="col-3">
                <img class="w-100" src="/images/checkout/icon2.png">
                <p class="w-100 m-0 p-0">
                    Garanzia 100% soddisfatti
                </p>
            </div>
            <div class="col-3">
                <img class="w-100" src="/images/checkout/icon3.png">
                <p class="w-100 m-0 p-0">
                    Consegna veloce
                </p>
            </div>
            <div class="col-3">
                <img class="w-100" src="/images/checkout/icon4.png">
                <p class="w-100 m-0 p-0">
                    Paga in modo semplice e sicuro
                </p>
            </div>
            
        </div>

    </form>
</div>

<?php
$debug = env('APP_LOCAL', 'false') ? 'true' : 'false';
$alpha = env('APP_ALPHA', 'false') ? 'true' : 'false';
$beta = env('APP_BETA', 'false') ? 'true' : 'false';
?>

<!-- Form script -->
<script id="checkout-js" src="{{ asset('js/checkout/payment.js') }}" data-lead-id="{{ $lead_id }}" data-stripe-key="{{ $stripe_key }}" data-debug="{{ $debug }}" data-alpha="{{ $alpha }}" data-beta="{{ $beta }}"></script>

</body>

</html>
