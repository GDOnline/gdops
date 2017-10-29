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

$type = $_POST['type'];

if ($type == '0') {
	$friends = Friends::get_friendships($accountID);
	
	if (count($friends) == 0)
		die('-2');

	for ($i = 0; $i < count($friends); $i++) {
		$f = $friends[$i];

		$u = Users::get_by_account($f['targetAccountID']);
		$us = Users::get_scores($u['userID']);

		echo "1:".$us["userName"].":2:".$us["userID"].":9:".$us["icon"].":10:".$us["color1"].":11:".$us["color2"].":14:".$us["iconType"].":15:".$us["special"].":16:".$u["accountID"].":18:0:41:".$f['isNew']."|";
	}

	Friends::read_new_friends($accountID);
} else {
	$blocked = Friends::get_blocked($accountID);

	if (count($blocked) == 0)
		die('-2');

	for ($i = 0; $i < count($blocked); $i++) {
		$f = $blocked[$i];

		$u = Users::get_by_account($f['targetAccountID']);
		$us = Users::get_scores($u['userID']);

		echo "1:".$us["userName"].":2:".$us["userID"].":9:".$us["icon"].":10:".$us["color1"].":11:".$us["color2"].":14:".$us["iconType"].":15:".$us["special"].":16:".$u["accountID"].":18:0:41:".$f['isNew']."|";
	}
}