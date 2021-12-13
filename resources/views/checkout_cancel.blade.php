<!DOCTYPE html>
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
</style>

</head>

<body onload="onLoad();">

<div id="loading">
    <div id="loader"></div>
</div>

<div id="app">

    <div id="step1" class="row hidden p-3">
        <h2 class="mb-3 text-center">
            RIEPILOGO
        </h2>

        <div class="bg-gray p-3 border border-black shadow-sm mb-3">
            <ul>
                <li>Numero ordine: <strong id="orderNumber">0000</strong></li>
                <li>Data: <strong id="orderDate">0 gennaio 1999</strong></li>
                <li>Prodotto: <strong id="orderProductName"></strong></li>
                <li>Quantità: <strong id="orderQuantity"></strong></li>
                <li>Totale: <strong>€ <span id="orderTotal">0.00</span></strong></li>
            </ul>
        </div>

        <div class="col-12 p-2">
            <h3>Premi "Prosegui" per annullare il tuo ordine.</h3>
        </div>
        <div class="col-12 p-2">
            <button id="nextStepButton" class="btn w-100">Prosegui</button>
        </div>
    </div>

    <div id="step2" class="row hidden p-3">
        <div class="col-12 p-2">
            <h3>Sei sicuro di voler anullare il tuo ordine?</h3>
        </div>
        <div class="col-12 p-2">
            <button id="confirmDeleteButton" class="btn w-100">Sì</button>
        </div>
        <div class="col-12 p-2">
            <button id="cancelDeleteButton" class="btn w-100">No</button>
        </div>
    </div>

    <div id="step3" class="row hidden p-3">
        <h2 class="mb-3 text-center">
            Prodotto annullato.
        </h2>
    </div>

</div>

<!-- Form script -->
<script id="checkout-js" src="{{ asset('js/checkout/cancel.js') }}" data-lead-id="{{ $lead_id }}"></script>

</body>

</html>
