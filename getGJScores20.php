<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);
$type = unparty($_POST["type"]);
$udid = unparty($_POST['udid']);

if ($accountID != '') {
	if (!blank($accountID, $gjp))
		die('-1');

	if (!Accounts::verify_gjp($accountID, $gjp))
		die('-1');

	if (Accounts::is_disabled($accountID))
		die('-1');
}

oplog('getGJScores', 'has been initiated by', $accountID == '' ? $udid : $accountID);

$users = array();
$max = 100;

switch ($type) {
	case 'top':
		$users = Leaderboards::get_top();
		break;

	case 'friends':
		$users = Leaderboards::get_friends_top($accountID);
		$max = count($users);
		break;

	case 'relative':
		$users = Leaderboards::get_relative_top($accountID == '' ? $udid : $accountID);
		$max = 10;
		break;

	case 'creators':
		$users = Leaderboards::get_creators_top();
		break;
}

if (count($users) == 0)
	die('-2');

for ($i = 0; $i < $max; $i++) {
	if ($i != 0)
		echo '|';

	$user = $users[$i];

	if ($user == null)
		break;

	if (Users::is_banned($user['userID']))
		continue;

	echo "1:".$user["userName"].":2:".$user["userID"].":13:".$user["coins"].":17:".$user["userCoins"].":6:".($type == 'creators' ? Users::calculate_creator_position($user['userID']) : Users::calculate_top_position($user['userID'])).":9:".$user["icon"].":10:".$user["color1"].":11:".$user["color2"].":14:".$user["iconType"].":15:".$user["special"].":16:".Users::get_by_id($user['userID'])['accountID'].":3:".$user["stars"].":8:".Users::calculate_creator_points($user['userID']).":4:".$user["demons"].":7:".Users::get_by_id($user['userID'])['accountID'].":46:".$user["diamonds"];
}