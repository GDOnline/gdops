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
$requestID = unparty($_POST["requestID"]);

Friends::accept_request($accountID, $targetAccountID, $requestID);
die('1');