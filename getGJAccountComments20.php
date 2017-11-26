<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty(htmlspecialchars($_POST['accountID']));
$page = unparty($_POST['page']);

if (!blank($accountID, $page))
	die('-1');

$makeTime = 'makeTime';

$cs = AccountComments::get_in_account($accountID, $page);

for ($i = 0; $i < 10; $i++) {
	if ($cs[$i+$page*10] == null)
		break;

	$c = $cs[$i+$page*10];

	if ($i != 0)
		echo '|';

    $modType = 0;
    $modColor = "255,255,255";

    $accountID = Users::get_by_id($c['userID'])['accountID'];
    if (Moderation::is_mod_or_admin($accountID)) {
        if (Moderation::is_admin($accountID)) {
            $modType = 2;
            $modColor = "238,0,255";
        } else if (Moderation::is_mod($accountID)) {
            $modType = 1;
            $modColor = "8,255,0";
        }
    }

    echo "2~"
        .$c["comment"]
        ."~3~"
        .$c["userID"]
        ."~4~"
        .$c["likes"]
        ."~7~"
        .$c['isSpam'];

    if ($modType != 0) {
        echo "~11~"
            .$modType
            ."~12~"
            .$modColor;
    }
    echo "~9~"
        .makeTime($c["uploadTime"])
        ."~6~"
        .$c["commentID"];
}

echo "#" . count($cs) . ":".($page*10).":10";