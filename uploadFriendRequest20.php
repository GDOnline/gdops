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

$comment = unparty($_POST["comment"]);
$toAccountID = unparty($_POST["toAccountID"]);

$p = Accounts::get_profile($toAccountID);

if ($p['allowFriendRequests'] != '0' && $p != false)
	die('-1');

Friends::new_request($accountID, $toAccountID, $comment);
die('1');