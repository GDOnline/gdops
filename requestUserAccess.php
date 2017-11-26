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

if (Moderation::is_mod_or_admin($accountID)) {
    if (Moderation::is_mod($accountID))
        die('1');
    else if (Moderation::is_admin($accountID))
        die('2');
} else
	die('-1');