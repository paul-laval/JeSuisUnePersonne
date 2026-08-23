<?php
$SqlLocalhost = "XXXXXXXXXXXXXXXXXXXX";
$SqlDB = "XXXXXXXXXXXXXXXXXXXX";
$SqlUser = "XXXXXXXXXXXXXXXXXXXX";
$SqlPassword = "XXXXXXXXXXXXXXXXXXXX";
$reCaptchaKey = "XXXXXXXXXXXXXXXXXXXX";
$reCaptchaPublicKey = "XXXXXXXXXXXXXXXXXXXX";

function getMySqlLocalhost() {
    global $SqlLocalhost;
    return $SqlLocalhost;
}
function getMySqlDB() {
    global $SqlDB;
    return $SqlDB;
}
function getMySqlUser() {
    global $SqlUser;
    return $SqlUser;
}
function getMySqlPassword() {
    global $SqlPassword;
    return $SqlPassword;
}
function getReCaptchaKey() {
    global $reCaptchaKey;
    return $reCaptchaKey;
}
function getReCaptchaPublicKey() {
    global $reCaptchaPublicKey;
    return $reCaptchaPublicKey;
}
?>