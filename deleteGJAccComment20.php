<?php
include 'settings.php';
require_once 'libops.php';

$commentID = unparty($_POST["commentID"]);

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);

if (!blank($accountID, $gjp))
	die('-1');

if (!Accounts::verify_gjp($accountID, $gjp))
	die('-1');

if (Accounts::is_disabled($accountID))
	die('-1');

$q = $db->prepare("DELETE FROM opsAccountComments WHERE commentID = :c");
$q->execute([':c' => $commentID]);

die('1');