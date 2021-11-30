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

<!-- Google maps API (Places) -->
<script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBXvXzU_AAxACrNdbHiWTLeY5l63jArjTM&libraries=places"></script>

<!-- Stripe -->
<script src="https://js.stripe.com/v3/"></script>

<style>
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

.step {
    width: 100%;
    display: none;
}

.bg-green-500 {
    background-color: #23a455;
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
    /*display: none;*/
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
    /*display: none;*/
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
    /*display: none;*/
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
    /*display: none;*/
    grid-template-columns: auto auto auto;
}

.trust-badge-paragraph {
    font-size: 0.26em;
}
</style>

</head>

<body onload="onLoad();">

<div id="loading">
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
            <div class="mb-3">
                <label for="fullName" class="form-label">Nome e Cognome</label>
                <input id="fullName" class="form-control" type="text" placeholder="Nome e Cognome" required minlength="5" value="Alberto Brambilla" />
                <div class="invalid-feedback">Nome e Cognome non validi</div>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Numero di telefono</label>
                <input id="phone" class="form-control" type="text" placeholder="Numero di telefono" required minlength="8" value="+39 331 87 65 441" />
                <div class="invalid-feedback">Numero di telefono non valido</div>
            </div>

            <!-- Trust badge -->
            <div class="trust-badge row">
                <div class="col-3">
                    <i class="fas fa-lock"></i>
                </div>
                <!-- style="padding: 0.5rem 0.5rem; font-size: 0.7rem; display: flex; align-items: center;" -->
                <div class="col-9 p-2">
                    <p class="trust-badge-paragraph">
                        I tuoi dati sono al sicuro.<br>
                        Un nostro consulente ti contatterà per aiutarti a completare il tuo ordine.
                    </p>
                </div>
            </div>

        </div>

        <div id="step3" class="step">

            <div class="mb-3">
                <label for="address" class="form-label">Trova indirizzo</label>
                <input id="address" class="form-control" type="text" placeholder="Trova indirizzo" value="Via Verdi 1, " />
                <div class="invalid-feedback">Indirizzo non valido</div>
            </div>

            <div class="row">
                <div class="col-8">
                    <div class="mb-3">
                        <label for="streetAddress" class="form-label">Nome via</label>
                        <input id="streetAddress" class="form-control" type="text" placeholder="Esempio: Via Montenapoleone" required />
                        <div class="invalid-feedback">Nome via non valido</div>
                    </div>
                </div>
                <div class="col-4 pe-0">
                    <div class="mb-3">
                        <label for="streetNumber" class="form-label">Num Civico</label>
                        <input id="streetNumber" class="form-control" type="text" placeholder="Esempio: 22" required />
                        <div class="invalid-feedback">Non valido</div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="cap" class="form-label">CAP (Codice Postale)</label>
                <input id="cap" class="form-control" type="text" placeholder="Esempio: 20019" required />
                <div class="invalid-feedback">CAP non valido</div>
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">Comune/Città</label>
                <input id="city" class="form-control" type="text" placeholder="Esempio: Milano" required minlength="4" />
                <div class="invalid-feedback">Comune non valido</div>
            </div>

            <div class="mb-3">
                <label for="province" class="form-label">Provincia</label>
                <select id="province" class="form-select" required>
                    <option value="" selected="selected">Seleziona Provincia</option>
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

            <div class="mb-3">
                <label class="form-label">Pagamento con Carta di Credito</label>
                <div id="cardElement"></div>
                <div id="cardErrors" role="alert" class="invalid-feedback"></div>
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
            <button id="nextStepButton" class="btn btn-primary w-100" type="submit">Submit</button>
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
<script id="form-js" src="{{ asset('js/checkout.js') }}" data-form-id="{{ $form_id }}" data-plugins="{{ $plugins }}" data-debug="{{ $debug }}" data-alpha="{{ $alpha }}" data-beta="{{ $beta }}"></script>

</body>

</html>
