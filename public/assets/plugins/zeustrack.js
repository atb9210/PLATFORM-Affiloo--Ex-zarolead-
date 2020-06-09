class ZeusTrackPlugin extends Plugin {
    data = {};

    sub_id = '';
    iframe_id = 'trackerClick';
    iframe_style = 'position:absolute;width:1px;height:1px;left:0;right:0;';
    zeustrack_code = '';

    constructor(data) {
        super(data);

        this.data = data;
        this.sub_id = this.data.sub_id;

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

    onVisit() {
        console.log("ZeusTrackPlugin onVisit");
    }

    onSubmit() {
        console.log("ZeusTrackPlugin onSubmit");

        $("#trackerClick").attr("src", offer_url);
    }

    onRedirect() {
        console.log("ZeusTrackPlugin onRedirect");
    }
}
