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

$levelID = unparty($_POST["levelID"]);

$s = Levels::remove($accountID, $levelID);

if ($s)
    die('1');
else
    die('-1');