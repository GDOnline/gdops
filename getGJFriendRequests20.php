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

$page = unparty($_POST["page"]);
$getSent = unparty($_POST["getSent"]);

$r = array();

if ($getSent != '')
	$r = Friends::get_sent_requests($accountID);
else
	$r = Friends::get_recv_requests($accountID);

if (count($r) == 0)
	die('-2');

for ($i = 0; $i < 10; $i++) {
	if ($r[$i+$page*10] == null)
		break;

	if ($i != 0)
		echo '|';

	$f = $r[$i=$page*10];

	$u = array();

	if ($getSent != '')
		$u = Users::get_by_account($f['targetAccountID']);
	else
		$u = Users::get_by_account($f['accountID']);

	$user = Users::get_scores($u['userID']);

	echo "1:".$user["userName"].":2:".$user["userID"].":9:".$user["icon"].":10:".$user["color1"].":11:".$user["color2"].":14:".$user["iconType"].":15:".$user["special"].":16:".$u["accountID"].":32:".$f["requestID"].":35:".$f["comment"].":41:".$f['isNew'].":37:".makeTime($f["uploadTime"]);
}

echo '#' . count($r) . ":$page:10";