<?php
include 'settings.php';
require_once 'libops.php';

$levelID = unparty($_POST['levelID']);
$stars = unparty($_POST['stars']);

$accountID = unparty($_POST['accountID']);
$gjp = unparty($_POST['gjp']);
$udid = unparty($_POST['udid']);
$uuid = unparty($_POST['uuid']);

if ($accountID != '' || $accountID != '0') {
    if (!Accounts::verify_gjp($accountID, $gjp))
        die('-1');

    if (Accounts::is_disabled($accountID))
        die('-1');
} else
    $accountID = 0;

if (!IPLimits::limit_rates_new($udid, $accountID, $uuid))
    die('-1');

Levels::rate_new($levelID, $stars, $udid);
die('1');