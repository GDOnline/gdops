<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty(htmlspecialchars($_POST['accountID']));
$gjp = unparty(htmlspecialchars($_POST['gjp']));
$comment = unparty(htmlspecialchars($_POST['comment']));

if (!blank($accountID, $gjp, $comment))
	die('-1');

if (!Accounts::verify_gjp($accountID, $gjp))
	die('-1');

if (AccountComments::is_banned($accountID))
	die('-10');

if (Accounts::is_disabled($accountID))
	die('-1');

if (!IPLimits::limit_accComments($_SERVER['REMOTE_ADDR']))
    die('-1');

AccountComments::create_new($accountID, $comment);
die('1');