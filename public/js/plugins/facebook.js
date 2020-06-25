function FacebookPlugin(data) {
    Plugin.call(this, data);

    this.pixel = this.data.pixel;
    // TODO: Check if this is still useful
    $(".addtocart-button").each(function() {
        this.fbAddToCart();
    }.bind(this));
}

FacebookPlugin.prototype.onVisit = function() {
    fbq('init', this.pixel);
    fbq('track', 'ViewContent');

    console.log("FacebookPlugin onVisit");
}

FacebookPlugin.prototype.onSubmit = function() {
    console.log("FacebookPlugin onSubmit");
    this.fbPurchase();
}

FacebookPlugin.prototype.onRedirect = function() {
}

FacebookPlugin.prototype.fbAddToCart = function() {
    // Grab those link clickers!!
    fbq('track', 'AddToCart');
}

FacebookPlugin.prototype.fbPurchase = function() {
    fbq('track', 'Purchase');
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
