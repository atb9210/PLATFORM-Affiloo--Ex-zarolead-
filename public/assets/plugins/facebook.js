class FacebookPlugin extends Plugin {
    data = {};

    pixel = '';

    constructor(data) {
        super(data);

        this.data = data;
        this.pixel = this.data.pixel;

        // TODO: Check if this is still useful
        $(".addtocart-button").each(function() {
            this.fbAddToCart();
        }.bind(this));
    }

    onVisit() {
        fbq('init', this.pixel);
        fbq('track', 'ViewContent');

        console.log("FacebookPlugin onVisit");
    }

    onSubmit() {
        console.log("FacebookPlugin onSubmit");
        this.fbPurchase();
    }

    onRedirect() {
    }

    fbAddToCart() {
        // Grab those link clickers!!
        fbq('track', 'AddToCart');
    }

    fbPurchase() {
        fbq('track', 'Purchase');
    }
}

// Facebook stuff
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
