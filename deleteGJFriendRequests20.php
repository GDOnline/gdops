<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);

if (!blank($accountID, $gjp))
    die('-1');

if (!Accounts::verify_gjp($accountID, $gjp))
    die('-1');

if (Accounts::is_disabled($accountID))
    die('-1');

$targetAccountID = unparty($_POST["targetAccountID"]);
$isSender = unparty($_POST["isSender"]);
$accounts = unparty($_POST["accounts"]);

if($targetAccountID != "0") {
    Friends::remove_one($accountID, $targetAccountID, $isSender);
    die('1');
} else {
    Friends::remove_multiple($accountID, $accounts, $isSender);
    die('1');
}