<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Redirecting to HDFC</title>
    <script>
        window.onload = function(){ document.forms[0].submit(); };
    </script>
    <style>body{font-family:Arial, sans-serif;margin:40px;text-align:center}</style>
    </head>
<body>
    <p>Redirecting to HDFC, please wait...</p>
    <form method="post" action="{{ $gateway }}">
        <input type="hidden" name="encRequest" value="{{ $encRequest }}">
        <input type="hidden" name="access_code" value="{{ $accessCode }}">
        <noscript>
            <button type="submit">Continue</button>
        </noscript>
    </form>
</body>
</html>


