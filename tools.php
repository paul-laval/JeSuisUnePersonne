<?php
function generate_uuid() {
    $bytes = random_bytes(16);
    $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
    $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
}

function checkReCaptcha($recaptchaKey) {
    $recaptcha = $_POST["g-recaptcha-response"];
    $url = "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaKey}&response={$recaptcha}";
    $response = file_get_contents($url);
    return json_decode($response);
}

function traceError($trace) {
    $date = getdate();
    error_log("{$date["year"]}-{$date["mon"]}-{$date["mday"]} {$date["hours"]}:{$date["minutes"]}:{$date["seconds"]} {$trace}", 0);
}
?>