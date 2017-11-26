<?php
include 'settings.php';
require_once 'libops.php';

$page = unparty($_POST['page']);
$getSent = unparty($_POST['getSent']);

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);

if (!blank($accountID, $gjp))
	die('-1');

if (!Accounts::verify_gjp($accountID, $gjp))
	die('-1');

if (Accounts::is_disabled($accountID))
	die('-1');

$msgs = array();

if ($getSent != '')
	$msgs = Messages::get_sent($accountID);
else
	$msgs = Messages::get_recv($accountID);

if (count($msgs) == 0)
	die('-2');

$users = '';

$makeTime = 'makeTime';

for ($i = 0; $i < 50; $i++) {
	if ($msgs[$i+$page*50] == null)
		break;

	if ($i != 0) {
		echo '|';
		$users .= '|';
	}

	$m = $msgs[$i+$page*50];
	$u = array();

	if ($getSent != '')
		$u = Users::get_by_account($m['targetAccountID']);
	else
		$u = Users::get_by_account($m['accountID']);

	$q = $db->prepare("SELECT * FROM opsUserScores WHERE userID = :u");
	$q->execute([':u' => $u['userID']]);

	$us = $q->fetch(2);

	$is = $getSent=='1'?'1':'0';

	if ($is == '1') {
		echo "6:" . $us['userName'] . ":3:" . $u['userID'] . ":2:" . $m['targetAccountID'] . ":1:" . $m['messageID'] . ":4:" . $m['subject'] . ":8:" . $m['isRead'] . ":9:1:7:" . makeTime($m['uploadTime']);
	} else {
		echo "6:" . $us['userName'] . ":3:" . $u['userID'] . ":2:" . $m['accountID'] . ":1:" . $m['messageID'] . ":4:" . $m['subject'] . ":8:" . $m['isRead'] . ":9:0:7:" . makeTime($m['uploadTime']);
	}
	
	$users .= $u['userID'] . ':' . $us['userName'] . ':' . $is == '1' ? $m['targetAccountID'] : $m['accountID'];
}

echo "#$users#" . count($msgs) . ":50:".($page*50);