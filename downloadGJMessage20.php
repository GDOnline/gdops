<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);
$messageID = unparty($_POST["messageID"]);

if (!blank($accountID, $gjp))
	die('-1');

if (!Accounts::verify_gjp($accountID, $gjp))
	die('-1');

if (Accounts::is_disabled($accountID))
	die('-1');

$q = $db->prepare("UPDATE opsMessages SET isRead = 1 WHERE messageID = :m");
$q->execute([':m' => $messageID]);

$q = $db->prepare("SELECT * FROM opsMessages WHERE messageID = :m");
$q->execute([':m' => $messageID]);

$m = $q->fetch(2);

$u = array();

if ($m['targetAccountID'] == $accountID) {
	$u = Users::get_by_account($m['accountID']);
} else {
	$u = Users::get_by_account($m['targetAccountID']);
}

$us = Users::get_scores($u['userID']);

exit("6:".$us["userName"].":3:".$u["userID"].":2:".$u["accountID"].":1:".$m["messageID"].":4:".$m["subject"].":8:1:9:0:5:".$m["body"].":7:".makeTime($m["uploadTime"]));