<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty($_POST["accountID"]);
$udid = unparty($_POST["udid"]);
$chk = unparty($_POST["chk"]);
$gjp = unparty($_POST["gjp"]);
$rewardType = unparty($_POST["rewardType"]);

if ($accountID != '0') {
    if (!blank($accountID, $gjp))
        die('-1');

    if (!Accounts::verify_gjp($accountID, $gjp))
        die('-1');

    if (Accounts::is_disabled($accountID))
        die('-1');
}

$string = base64_encode(xorchar(UserRewards::load_chests($accountID, $udid, $chk, $rewardType), 59182));
exit('AAAAA' . $string . '|' . sha1($string . 'pC26fpYaQCtg'));