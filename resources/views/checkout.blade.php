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

<!-- Google maps API (Places) -->
<script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBXvXzU_AAxACrNdbHiWTLeY5l63jArjTM&libraries=places"></script>

<!-- Stripe -->
<script src="https://js.stripe.com/v3/"></script>

<style>
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

.step {
    width: 100%;
    display: none;
}

.hidden {
    display: none;
}

.bg-white {
    background-color: #ffffff;
}

.bg-gray {
    background-color: #cccccc;
}

.bg-green-500 {
    background-color: #23a455;
}

.paymentMethodDescription {
    display: none;
}

/* CSS rule created by me to fix the height of the stripe card element */
#cardElement {
    padding-top: 0.65rem !important;
    padding-bottom: 0.65rem !important;
}
</style>

<style>
.countdown {
    display: none;
    width: 100%;
    padding: 0.2rem;
    font-size: 3.5rem;
    display: none;
    grid-template-columns: auto auto auto;
    font-weight: bold;
}

.countdown-text-container {
    padding: 0.5rem 0.5rem;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.countdown-text {
    background-color: #ff2233;
    color: #ffffff;
    border-radius: 0.4rem;
    padding: 0rem 0.4rem;
}

.countdown-time {
    font-size: 3rem;
    color: #000000;
}

.people-viewing {
    display: none;
    width: 100%;
    padding: 0.2rem;
    text-align: center;
    color: #000000;
    font-weight: bold;
}

.people-viewing-count {
    background-color: #ff2233;
    color: #ffffff;
    border-radius: 0.4rem;
    padding: 0rem 0.4rem;
}

.available-quantity {
    display: none;
    width: 100%;
    padding: 0.2rem;
}

.available-quantity-bar {
    --barColor: #ff330f;
    position: relative;
    width: 80%;
    margin-right: auto;
    margin-left: auto;
    height: 2.2em;
    background-color: #919191;
    color: #ffffff;
    border-radius: 0.5em;
}

.available-quantity-bar::before {
    content: '';
    display: flex;
    align-items: center;
    position: absolute;
    left: 0.0em;
    top: 0.0em;
    bottom: 0.0em;
    width: 20%;
    min-width: 2rem;
    max-width: calc(100% - 1em);
    background-color: var(--barColor);
    border-radius: 0.5em;
    padding: 1em;
}

.available-quantity-bar::after {
    content: attr(data-label);
    display: flex;
    align-items: center;
    position: absolute;
    right: 3.5em;
    top: 0.5em;
    bottom: 0.6em;
}

.trust-badge {
    width: 100%;
    padding: 0.2rem;
    font-size: 3.5rem;
    margin-bottom: 1rem;
    display: none;
    grid-template-columns: auto auto auto;
}

.trust-badge-paragraph {
    font-size: 0.26em;
}
</style>

<style>
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
</style>

</head>

<body onload="onLoad();">

<div id="loading">
    <div id="loader"></div>
</div>

<div id="app">

    <!-- Countdown -->
    <div class="countdown">
        <div class="countdown-text-container">
            <span class="countdown-text">L'OFFERTA SCADE TRA:</span>
        </div>
        <div><p class="countdown-time">10:00</p></div>
    </div>

    <!-- People viewing -->
    <div class="people-viewing">
        <i class="fas fa-eye"></i>
        <span class="people-viewing-count">10</span>
        <span>Persone stanno visualizzando</span>
    </div>

    <!-- Available quantity -->
    <div class="available-quantity">
        <div class="available-quantity-bar" data-label="5/100 DISPONIBILI"></div>
    </div>

    <!-- Checkout form -->
    <form id="mainForm" class="p-3" action="#" novalidate>

        <div id="step1" class="step">
            <h2 class="mb-3 text-center">
                INFORMAZIONI CONTATTO
            </h2>
            <div class="mb-3">
                <!--<label for="fullName" class="form-label">Nome e Cognome</label>-->
                <input id="fullName" class="form-control py-2" type="text" placeholder="NOME E COGNOME" required minlength="5" value="" />
                <div class="invalid-feedback">Nome e Cognome non validi</div>
            </div>

            <div class="mb-3">
                <!--<label for="phone" class="form-label">Numero di telefono</label>-->
                <input id="phone" class="form-control py-2" type="text" placeholder="TELEFONO O CELLULARE" required minlength="8" value="" />
                <div class="invalid-feedback">Numero di telefono non valido</div>
            </div>

            <!-- Trust badge -->
            <div class="trust-badge row">
                <div class="col-3">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="col-9 p-2">
                    <p class="trust-badge-paragraph">
                        I tuoi dati sono al sicuro.<br>
                        Un nostro consulente ti contatterà per aiutarti a completare il tuo ordine.
                    </p>
                </div>
            </div>

        </div>

        <div id="step2" class="step">
            <div class="bg-black p-2 mb-3">
                <p class="text-light text-center fs-6">
                    <span class="text-decoration-underline text-danger"><strong>ATTENZIONE!</strong></span> Solo per pochi minuti puoi ricevere 
                    <span class="text-decoration-underline"><strong>un'offerta</strong></span> 
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
                        <div class="col-5 ps-5 pt-2">
                            1 pezzo
                        </div>
                        <div class="col-4 pt-2 pe-0 ps-0">
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
                        <div class="col-5 ps-5 pt-2">
                            2 pezzi
                        </div>
                        <div class="col-4 pt-2 pe-0 ps-0">
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
                TOTALE €<span id="upSellTotalAmount"></span>
            </div>
        </div>

        <div id="step3" class="step">

            <h2 class="mb-3 text-center">
                INDIRIZZO SPEDIZIONE
            </h2>

            <div class="mb-3">
                <!--<label for="address" class="form-label">Trova indirizzo</label>-->
                <input id="address" class="form-control py-2" type="text" placeholder="TROVA INDIRIZZO" value="" />
                <div class="invalid-feedback">Indirizzo non valido</div>
            </div>

            <div class="row">
                <div class="col-8">
                    <div class="mb-3">
                        <!--<label for="streetAddress" class="form-label">Nome via</label>-->
                        <input id="streetAddress" class="form-control py-2" type="text" placeholder="INDIRIZZO (STRADA)" required />
                        <div class="invalid-feedback">Nome via non valido</div>
                    </div>
                </div>
                <div class="col-4 ps-0">
                    <div class="mb-3">
                        <!--<label for="streetNumber" class="form-label">Num Civico</label>-->
                        <input id="streetNumber" class="form-control py-2" type="text" placeholder="NR. CIVICO" required />
                        <div class="invalid-feedback">Non valido</div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <!--<label for="cap" class="form-label">CAP (Codice Postale)</label>-->
                <input id="cap" class="form-control py-2" type="text" placeholder="CAP (CODICE POSTALE)" required />
                <div class="invalid-feedback">CAP non valido</div>
            </div>

            <div class="mb-3">
                <!--<label for="city" class="form-label">Comune/Città</label>-->
                <input id="city" class="form-control py-2" type="text" placeholder="CITTÀ/COMUNE" required minlength="4" />
                <div class="invalid-feedback">Comune non valido</div>
            </div>

            <div class="mb-3">
                <!--<label for="province" class="form-label">Provincia</label>-->
                <select id="province" class="form-select py-2" placeholder="PROVINCIA" required>
                    <option value="" selected="selected">SELEZIONA PROVINCIA</option>
                    <option value="AG">Agrigento</option>
                    <option value="AL">Alessandria</option>
                    <option value="AN">Ancona</option>
                    <option value="AO">Aosta</option>
                    <option value="AR">Arezzo</option>
                    <option value="AP">Ascoli Piceno</option>
                    <option value="AT">Asti</option>
                    <option value="AV">Avellino</option>
                    <option value="BA">Bari</option>
                    <option value="BT">Barletta-Andria-Trani</option>
                    <option value="BL">Belluno</option>
                    <option value="BN">Benevento</option>
                    <option value="BG">Bergamo</option>
                    <option value="BI">Biella</option>
                    <option value="BO">Bologna</option>
                    <option value="BZ">Bolzano</option>
                    <option value="BS">Brescia</option>
                    <option value="BR">Brindisi</option>
                    <option value="CA">Cagliari</option>
                    <option value="CL">Caltanissetta</option>
                    <option value="CB">Campobasso</option>
                    <option value="CI">Carbonia-Iglesias</option>
                    <option value="CE">Caserta</option>
                    <option value="CT">Catania</option>
                    <option value="CZ">Catanzaro</option>
                    <option value="CH">Chieti</option>
                    <option value="CO">Como</option>
                    <option value="CS">Cosenza</option>
                    <option value="CR">Cremona</option>
                    <option value="KR">Crotone</option>
                    <option value="CN">Cuneo</option>
                    <option value="EN">Enna</option>
                    <option value="FM">Fermo</option>
                    <option value="FE">Ferrara</option>
                    <option value="FI">Florence</option>
                    <option value="FG">Foggia</option>
                    <option value="FC">Forl“-Cesena</option>
                    <option value="FR">Frosinone</option>
                    <option value="GE">Genoa</option>
                    <option value="GO">Gorizia</option>
                    <option value="GR">Grosseto</option>
                    <option value="IM">Imperia</option>
                    <option value="IS">Isernia</option>
                    <option value="SP">La Spezia</option>
                    <option value="AQ">L'Aquila</option>
                    <option value="LT">Latina</option>
                    <option value="LE">Lecce</option>
                    <option value="LC">Lecco</option>
                    <option value="LI">Livorno</option>
                    <option value="LO">Lodi</option>
                    <option value="LU">Lucca</option>
                    <option value="MC">Macerata</option>
                    <option value="MN">Mantua</option>
                    <option value="MS">Massa and Carrara</option>
                    <option value="MT">Matera</option>
                    <option value="VS">Medio Campidano</option>
                    <option value="ME">Messina</option>
                    <option value="MI">Milan</option>
                    <option value="MO">Modena</option>
                    <option value="MB">Monza and Brianza</option>
                    <option value="NA">Naples</option>
                    <option value="NO">Novara</option>
                    <option value="NU">Nuoro</option>
                    <option value="OG">Ogliastra</option>
                    <option value="OT">Olbia-Tempio</option>
                    <option value="OR">Oristano</option>
                    <option value="PD">Padua</option>
                    <option value="PA">Palermo</option>
                    <option value="PR">Parma</option>
                    <option value="PV">Pavia</option>
                    <option value="PG">Perugia</option>
                    <option value="PU">Pesaro and Urbino</option>
                    <option value="PE">Pescara</option>
                    <option value="PC">Piacenza</option>
                    <option value="PI">Pisa</option>
                    <option value="PT">Pistoia</option>
                    <option value="PN">Pordenone</option>
                    <option value="PZ">Potenza</option>
                    <option value="PO">Prato</option>
                    <option value="RG">Ragusa</option>
                    <option value="RA">Ravenna</option>
                    <option value="RC">Reggio Calabria</option>
                    <option value="RE">Reggio Emilia</option>
                    <option value="RI">Rieti</option>
                    <option value="RN">Rimini</option>
                    <option value="RM">Rome</option>
                    <option value="RO">Rovigo</option>
                    <option value="SA">Salerno</option>
                    <option value="SS">Sassari</option>
                    <option value="SV">Savona</option>
                    <option value="SI">Siena</option>
                    <option value="SO">Sondrio</option>
                    <option value="SR">Syracuse</option>
                    <option value="TA">Taranto</option>
                    <option value="TE">Teramo</option>
                    <option value="TR">Terni</option>
                    <option value="TP">Trapani</option>
                    <option value="TN">Trento</option>
                    <option value="TV">Treviso</option>
                    <option value="TS">Trieste</option>
                    <option value="TO">Turin</option>
                    <option value="UD">Udine</option>
                    <option value="VA">Varese</option>
                    <option value="VE">Venice</option>
                    <option value="VB">Verbano-Cusio-Ossola</option>
                    <option value="VC">Vercelli</option>
                    <option value="VR">Verona</option>
                    <option value="VV">Vibo Valentia</option>
                    <option value="VI">Vicenza</option>
                    <option value="VT">Viterbo</option>
                </select>
            </div>

        </div>


        <div id="step4" class="step">

            <h2 class="mb-3 text-center">
                RIEPILOGO
            </h2>

            <div class="mb-3 text-center">
                <p class="text-center fs-5 mb-0">
                    <span id="reviewProductName"></span>
                </p>
                <p class="text-center fs-6 mb-0">
                    <span class="text-gray">(Quantità: <span id="reviewQuantity"></span>)</span>
                </p>
                <p class="text-center fs-5">
                    TOTALE: € <span id="reviewTotalAmount"></span>
                </p>
            </div>

            <hr />

            <h2 class="mb-3 text-center">
                METODO DI PAGAMENTO
            </h2>

            <div class="mb-3">
                <div class="m-0 p-0 bg-gray border border-2">
                    <p class="m-0 p-4">
                        <input id="pmInputRadioCod" name="paymentMethod" type="radio" value="paymentMethodCod" class="paymentMethodInputRadio" checked="checked">
                        <label for="pmInputRadioCod">Alla consegna (commissione +1.95€)</label>
                    </p>
                    <div class="m-0 p-4 bg-white paymentMethodDescription" id="paymentMethodCod">
                         Selezionando l'opzione pagamento alla consegna, il corriere aggiungerà una commissione che si dovrà pagare insieme all'ordine. Di seguito, puoi vedere altri metodi di pagamento più economici (con carta di credito o PayPal).
                    </div>
                </div>
                <div class="m-0 p-0 bg-gray border border-2">
                    <p class="m-0 p-4">
                        <input id="pmInputRadioOnline" name="paymentMethod" type="radio" value="paymentMethodOnline" class="paymentMethodInputRadio">
                        <label for="pmInputRadioOnline">Carta di credito</label>
                    </p>
                    <div class="m-0 p-4 bg-white paymentMethodDescription" id="paymentMethodOnline">
                        <label class="form-label">Carta di credito</label>
                        <div id="cardElement"></div>
                        <div id="cardErrors" role="alert" class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

        </div>


        <div id="step5" class="step">

            <div class="text-center mb-3">
                <img class="w-50" src="{{ asset('images/order-confirmed.jpg') }}">
            </div>
            <div class="bg-green-500 p-3">
                <p class="text-white text-center mb-0">
                    <strong>ORDINE CONFERMATO</strong>
                </p>
            </div>
            <div class="bg-success p-3">
                <p class="text-white text-center mb-0">
                    <strong>Il Tuo Ordine è Stato Inviato Correttamente</strong>
                </p>
            </div>

        </div>

        <div class="mb-3 mt-3">
            <button id="nextStepButton" class="hidden btn btn-primary w-100 p-2 fs-4" type="submit">Submit</button>
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

<!-- Form plugins -->
<script id="plugin-js" src="{{ asset('js/plugins/plugin.js') }}"></script>
@foreach($plugins as $plugin => $value)
    @if(file_exists(public_path('js/plugins/' . $plugin . '.js')))
        <script id="plugin-{{$plugin}}-js" src="{{ asset('js/plugins/' . $plugin . '.js') }}"></script>
    @endif
@endforeach
<?php
$plugins = json_encode($plugins);
$debug = env('APP_LOCAL', 'false') ? 'true' : 'false';
$alpha = env('APP_ALPHA', 'false') ? 'true' : 'false';
$beta = env('APP_BETA', 'false') ? 'true' : 'false';
?>

<!-- Form script -->
<script id="checkout-js" src="{{ asset('js/checkout.js') }}" data-product-id="{{ $product_id }}" data-plugins="{{ $plugins }}" data-debug="{{ $debug }}" data-alpha="{{ $alpha }}" data-beta="{{ $beta }}"></script>

</body>

</html>
