<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);
$targetAccountID = unparty($_POST["targetAccountID"]);

if (!blank($accountID, $gjp, $targetAccountID))
	die('-1');

if (!Accounts::verify_gjp($accountID, $gjp))
	die('-1');

if (Accounts::is_disabled($accountID))
	die('-1');

oplog('getGJUserInfo', 'has been initiated by', $accountID, 'and trying to get profile of', $targetAccountID);

$account = Accounts::get_by_id($targetAccountID);

if ($account['actCode'] != '')
	die('-1');

if (Accounts::is_blocked_by($targetAccountID, $accountID)
	|| Accounts::is_blocked_by($accountID, $targetAccountID))
	die('-1');

$user = Users::get_by_account($targetAccountID);
$profile = Accounts::get_profile($targetAccountID);

if (!$profile) {
	$profile['allowMessages'] = 0;
	$profile['allowFriendRequests'] = 0;
	$profile['youtube'] = '';
	$profile['twitter'] = '';
	$profile['twitch'] = '';
}

$q = $db->prepare("SELECT * FROM opsUserScores WHERE userID = :u");
$q->execute([':u' => $user['userID']]);

$s = $q->fetch(2);

$ccp = 'Users::calculate_creator_points';
$ctp = 'Users::calculate_top_position';

$cur = 'Messages::count_unread';
$fur = 'Friends::count_new';
$frr = 'Friends::count_new_requests';

if ($accountID == $targetAccountID) {
	$response = <<<RESPONSE
1:{$s['userName']}:2:{$user['userID']}:13:{$s['coins']}:17:{$s['userCoins']}:10:{$s['color1']}:11:{$s['color2']}:3:{$s['stars']}:46:{$s['diamonds']}:4:{$s['demons']}:8:{$ccp($user['userID'])}:18:{$profile['allowMessages']}:19:{$profile['allowFriendRequests']}:20:{$profile['youtube']}:21:{$s['accIcon']}:22:{$s['accShip']}:23:{$s['accBall']}:24:{$s['accBird']}:25:{$s['accDart']}:26:{$s['accRobot']}:28:{$s['accGlow']}:43:{$s['accSpider']}:47:1:30:{$ctp($user['userID'])}:16:{$user['accountID']}:31:0:44:{$profile['twitter']}:45:{$profile['twitch']}:38:{$cur($user['accountID'])}:39:{$frr($user['accountID'])}:40:{$fur($user['accountID'])}:29:1
RESPONSE;

	die($response);
} else {
	$fStatus = 0;

	if (Friends::is_friend($accountID, $targetAccountID))
		$fStatus = 1;
	elseif (Friends::me_sent_req($accountID, $targetAccountID))
		$fStatus = 4;
	elseif (Friends::he_sent_req($accountID, $targetAccountID))
		$fStatus = 3;
	else
		$fStatus = 0;

	$response = <<<RESPONSE
1:{$s['userName']}:2:{$user['userID']}:13:{$s['coins']}:17:{$s['userCoins']}:10:{$s['color1']}:11:{$s['color2']}:3:{$s['stars']}:46:{$s['diamonds']}:4:{$s['demons']}:8:{$ccp($user['userID'])}:18:{$profile['allowMessages']}:19:{$profile['allowFriendRequests']}:20:{$profile['youtube']}:21:{$s['accIcon']}:22:{$s['accShip']}:23:{$s['accBall']}:24:{$s['accBird']}:25:{$s['accDart']}:26:{$s['accRobot']}:28:{$s['accGlow']}:43:{$s['accSpider']}:47:1:30:{$ctp($user['userID'])}:16:{$user['accountID']}:31:{$fStatus}:44:{$profile['twitter']}:45:{$profile['twitch']}:38:0:39:0:40:0:29:1
RESPONSE;

	die($response);
}