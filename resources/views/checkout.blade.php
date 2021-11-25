<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- Google maps API (Places) -->
    <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBXvXzU_AAxACrNdbHiWTLeY5l63jArjTM&libraries=places"></script>
 
    <script src="js/checkout.js" defer></script>

    <style>
    input {
        border-radius: 0.3em;
    }

    .input-error {
        border-color: red;
    }
    </style>
</head>

<body onload="onLoad();">
    <div id="app">
        <form id="mainForm" class="row p-3" action="#" novalidate>

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

            <div class="mb-3">
                <label for="address" class="form-label">Indirizzo di spedizione</label>
                <input id="address" class="form-control" type="text" placeholder="Esempio: Via Montenapoleone 22, Milano" required minlength="4" value="Via Verdi 1, " />
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

            <div class="mb-3 mt-3">
                <button class="btn btn-primary w-100" type="submit">Submit</button>
            </div>

        </form>
    </div>
</body>

</html>
