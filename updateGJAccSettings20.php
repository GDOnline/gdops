<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);
$frS = unparty($_POST["frS"]);
$mS = unparty($_POST["mS"]);
$yt = unparty($_POST["yt"]);
$twitter = unparty($_POST["twitter"]);
$twitch = unparty($_POST["twitch"]);

if (!blank($accountID, $gjp))
	die('-1');

if (!Accounts::verify_gjp($accountID, $gjp))
	die('-1');

if (Accounts::is_disabled($accountID))
	die('-1');

$q = $db->prepare("REPLACE INTO opsAccountProfiles (accountID, allowFriendRequests, allowMessages, youtube, twitter, twitch) VALUES (:a, :af, :am, :y, :ter, :tch)");
$q->execute([
	':a' => $accountID,
	':af' => $frS,
	':am' => $mS,
	':y' => $yt,
	':ter' => $twitter,
	':tch' => $twitch
]);

die('1');