<html>

<head>

<meta name="viewport" content="width=device-width, initial-scale=1" />

<script src="https://js.stripe.com/v3/"></script>

</head>

<body>

<div id="stripe-elements"></div>

<script>
var stripe = Stripe('pk_test_51HZhdsKZ0GYSGPZVGo4ggAfbpvnXbooyq1kATn5fn5HUcZmm15W63iUzTLztDS9iUebQr3VjkHUHJkUrYDgPMj1x00c5Xc1pyG');
var elements = stripe.elements();
var options = {
    classes: {
    },
    style: {
        /*
        base: {
            iconColor: '#c4f0ff',
            color: '#fff',
            fontWeight: 500,
            fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
            fontSize: '16px',
            fontSmoothing: 'antialiased',
            ':-webkit-autofill': {
                color: '#fce883',
            },
            '::placeholder': {
                color: '#87BBFD',
            },
        },
        */
    }
};
var cardElement = elements.create('card', options);
cardElement.mount(document.getElementById('stripe-elements'));
</script>

</body>

</html>
