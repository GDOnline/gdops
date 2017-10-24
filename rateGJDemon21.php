<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);
$mode = unparty($_POST["mode"]);

if ($mode != '1')
	die('1');

if (!blank($accountID, $gjp))
	die('-1');

if (!Accounts::verify_gjp($accountID, $gjp))
	die('-1');

if (Accounts::is_disabled($accountID))
	die('-1');

$rating = unparty($_POST["rating"]);
$levelID = unparty($_POST["levelID"]);

if (!Moderation::is_mod_or_admin($accountID))
	die('-2');

if (Moderation::is_admin($accountID)) {
	Moderation::rate_demon($levelID, $rating);
	die('1');
} else {
    Moderation::suggest_demon($accountID, $levelID, $rating);
    die('1');
}