function ZeusTrackPlugin(data) {
    Plugin.call(this, data);

    this.sub_id = this.data.sub_id;

    this.iframe_id = 'trackerClick';
    this.iframe_style = 'position:absolute;width:1px;height:1px;left:0;right:0;';
    this.zeustrack_code = `
<!-- Zeus Track -->
<iframe src="" id="trackerClick"></iframe>
<style>
    #trackerClick {
        position:absolute;
        width: 1px;
        height: 1px;
        left: 0;
        right: 0;
    }
</style>
<script>

var offer_url = "{snippet:offer_url}";

</script>
<script>
var offer_url = "{snippet:offer_url}";
$(document).ready(function(){
   $("a:link").click(function(e) {
      e.preventDefault();
      $('html, body').animate({scrollTop: $('#mainForm').offset().top}, 600);
        setTimeout(function(){
            $('#inputName').focus();
        }, 600)
   });
});

</script>
    `;
    $(document.body).append(this.zeustrack_code);
}

ZeusTrackPlugin.prototype.onVisit = function() {
    console.log("ZeusTrackPlugin onVisit");
}

ZeusTrackPlugin.prototype.onSubmit = function() {
    console.log("ZeusTrackPlugin onSubmit");

    $("#trackerClick").attr("src", offer_url);
}

ZeusTrackPlugin.prototype.afterSubmit = function(response) {
    console.log("ZeusTrackPlugin afterSubmit");
}

ZeusTrackPlugin.prototype.onRedirect = function() {
    console.log("ZeusTrackPlugin onRedirect");
}
