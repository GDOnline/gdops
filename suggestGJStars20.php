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

if (!Moderation::is_mod_or_admin($accountID))
	die('-2');

$stars = unparty($_POST["stars"]);
$featured = unparty($_POST["feature"]);
$levelID = unparty($_POST["levelID"]);

if (Moderation::is_admin($accountID)) {
	Moderation::rate($levelID, $stars, $featured);
	die('1');
} else {
    Moderation::suggest($accountID, $levelID, $stars, $isFeatured == null ? 0 : 1);
    die('1');
}