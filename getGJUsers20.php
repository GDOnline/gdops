<?php
include 'settings.php';
require_once 'libops.php';

$str = htmlspecialchars($_POST["str"]);
$page = $_POST["page"];

$q = $db->prepare("SELECT * FROM opsUserScores WHERE userName LIKE CONCAT(:s, '%')");
$q->execute([':s' => $str]);

$r = $q->fetchAll();

if (count($r) == 0)
	exit("-2");

$ccp = 'Users::calculate_creator_points';
$ctp = 'Users::calculate_top_position';

for ($i = 0; $i < 10; $i++) {
	if ($r[$i+$page*10] == null)
		break;

	if ($i != 0)
		echo '|';

	$user = $r[$i+$page*10];
	$u = Users::get_by_id($user['userID']);

	echo <<<EOF
1:{$user["userName"]}:2:{$user["userID"]}:13:{$user["coins"]}:17:{$user["userCoins"]}:6:{$ctp($user['userID'])}:9:{$user["icon"]}:10:{$user["color1"]}:11:{$user["color2"]}:14:{$user["iconType"]}:15:{$user["special"]}:16:{$u["accountID"]}:3:{$user["stars"]}:8:{$ccp($user['userID'])}:4:{$user["demons"]}
EOF;
}

exit("#" . count($r) . ":".($page*10).":10");