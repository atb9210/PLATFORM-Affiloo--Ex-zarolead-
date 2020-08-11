<html>

<head>
</head>

<body>

<div style="text-align: center; margin-top: 200px;">
    <form method="get" action="{{ route('psw-maker') }}">
        <input type="text" name="psw" value="{{ $psw }}">
        <input type="submit" value="Encrypt">
    </form>

    @if(isset($result))
        <div id="result">{{ $result }}</div>
        <button onclick="copyToClipboard();">Copy</button>
    @endif
</div>

<script>
function fallbackCopyTextToClipboard(text) {
  var textArea = document.createElement("textarea");
  textArea.value = text;
  
  // Avoid scrolling to bottom
  textArea.style.top = "0";
  textArea.style.left = "0";
  textArea.style.position = "fixed";

  document.body.appendChild(textArea);
  textArea.focus();
  textArea.select();

  try {
    var successful = document.execCommand('copy');
    var msg = successful ? 'successful' : 'unsuccessful';
    console.log('Fallback: Copying text command was ' + msg);
  } catch (err) {
    console.error('Fallback: Oops, unable to copy', err);
  }

  document.body.removeChild(textArea);
}
function copyTextToClipboard(text) {
  if (!navigator.clipboard) {
    fallbackCopyTextToClipboard(text);
    return;
  }
  navigator.clipboard.writeText(text).then(function() {
    console.log('Async: Copying to clipboard was successful!');
  }, function(err) {
    console.error('Async: Could not copy text: ', err);
  });
}
function copyToClipboard() {
    var copyText = document.getElementById("result").innerHTML;

    copyTextToClipboard(copyText);

    /* Copy the text inside the text field */
    document.execCommand("copy")
}
</script>

</body>

</html>
