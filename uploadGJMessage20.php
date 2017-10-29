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

if (!IPLimits::limit_messages($_SERVER['REMOTE_ADDR']))
    die('-1');

$toAccountID = unparty($_POST["toAccountID"]);
$subject = unparty($_POST["subject"]);
$body = unparty($_POST["body"]);

if (Accounts::is_blocked_by($accountID, $toAccountID))
	die('-1');

$p = Accounts::get_profile($toAccountID);

if (!Friends::is_friend($accountID, $toAccountID) && $p['allowMessages'] == '1')
	die('-1');

if ($p['allowMessages'] == '2')
	die('-1');

Messages::send($accountID, $toAccountID, $subject, $body);
die('1');