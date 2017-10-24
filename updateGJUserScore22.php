<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);
$userName = unparty($_POST["userName"]);
$stars = unparty($_POST["stars"]);
$demons = unparty($_POST["demons"]);
$coins = unparty($_POST["coins"]);
$userCoins = unparty($_POST["userCoins"]);
$special = unparty($_POST["special"]);
$accIcon = unparty($_POST["accIcon"]);
$accShip = unparty($_POST["accShip"]);
$accBall = unparty($_POST["accBall"]);
$accBird = unparty($_POST["accBird"]);
$accDart = unparty($_POST["accDart"]);
$accRobot = unparty($_POST["accRobot"]);
$accGlow = unparty($_POST["accGlow"]);
$color1 = unparty($_POST["color1"]);
$color2 = unparty($_POST["color2"]);
$iconType = unparty($_POST["iconType"]);
$icon = unparty($_POST['icon']);
$udid = unparty($_POST['udid']);
$accSpider = unparty($_POST["accSpider"]);
$accExplosion = unparty($_POST["accExplosion"]);
$diamonds = unparty($_POST["diamonds"]);

$userID = '';

if ($accountID != '') {
	if (!Accounts::verify_gjp($accountID, $gjp))
		exit('-1 (Error: invalid GJP)');

	if (Accounts::get_by_id($accountID)['actCode'] != '')
		exit('-1');

	if (Accounts::is_disabled($accountID))
		exit('-1');

	$userID = Users::get_by_account($accountID)['userID'];
} else {
	if (!blank($udid))
		die('-1');

	if (!Users::check_user_by_udid($udid))
		$userID = Users::create_new($udid);
	else
		$userID = Users::get_by_udid($udid)['userID'];
}

if ($userID != '') {
	if (!Anticheat::check_values($userID, $stars, $coins, $userCoins, $demons))
		die('-1');

	$sql_notcreated = <<<SQText
INSERT INTO opsUserScores (
	userName,
	stars,
	demons,
	coins,
	userCoins,
	special,
	accIcon,
	accShip,
	accBall,
	accBird,
	accDart,
	accRobot,
	accGlow,
	accSpider,
	accExplosion,
	diamonds,
	color1,
	color2,
	iconType,
	icon,
	userID
) VALUES (
	:userName,
	:stars,
	:demons,
	:coins,
	:userCoins,
	:special,
	:accIcon,
	:accShip,
	:accBall,
	:accBird,
	:accDart,
	:accRobot,
	:accGlow,
	:accSpider,
	:accExplosion,
	:diamonds,
	:color1,
	:color2,
	:iconType,
	:icon,
	:userID
)
SQText;

	$sql_created = <<<SQText1
UPDATE opsUserScores SET
	userName = :userName,
	stars = :stars,
	demons = :demons,
	coins = :coins,
	userCoins = :userCoins,
	special = :special,
	accIcon = :accIcon,
	accShip = :accShip,
	accBall = :accBall,
	accBird = :accBird,
	accDart = :accDart,
	accRobot = :accRobot,
	accGlow = :accGlow,
	accSpider = :accSpider,
	accExplosion = :accExplosion,
	diamonds = :diamonds,
	color1 = :color1,
	color2 = :color2,
	iconType = :iconType,
	icon = :icon
WHERE
	userID = :userID
SQText1;
	
	$q = $db->prepare("SELECT * FROM opsUserScores WHERE userID = :userID LIMIT 1");
	$q->execute([':userID' => $userID]);

	if ($q->rowCount() > 0) {
		$q = $db->prepare($sql_created);
		$q->execute([
			':userName' => $userName,
			':stars' => $stars,
			':demons' => $demons,
			':coins' => $coins,
			':userCoins' => $userCoins,
			':special' => $special,
			':accIcon' => $accIcon,
			':accShip' => $accShip,
			':accBall' => $accBall,
			':accBird' => $accBird,
			':accDart' => $accDart,
			':accRobot' => $accRobot,
			':accGlow' => $accGlow,
			':accSpider' => $accSpider,
			':accExplosion' => $accExplosion,
			':diamonds' => $diamonds,
			':color1' => $color1,
			':color2' => $color2,
			':iconType' => $iconType,
			':icon' => $icon,
			':userID' => $userID
		]);

		die($userID);
	} else {
		$q = $db->prepare($sql_notcreated);
		$q->execute([
			':userName' => $userName,
			':stars' => $stars,
			':demons' => $demons,
			':coins' => $coins,
			':userCoins' => $userCoins,
			':special' => $special,
			':accIcon' => $accIcon,
			':accShip' => $accShip,
			':accBall' => $accBall,
			':accBird' => $accBird,
			':accDart' => $accDart,
			':accRobot' => $accRobot,
			':accGlow' => $accGlow,
			':accSpider' => $accSpider,
			':accExplosion' => $accExplosion,
			':diamonds' => $diamonds,
			':color1' => $color1,
			':color2' => $color2,
			':iconType' => $iconType,
			':icon' => $icon,
			':userID' => $userID
		]);

		die($userID);
	}

} else
	die('-1');