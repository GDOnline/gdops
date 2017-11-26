<?php
include 'settings.php';
require_once 'libops.php';

$page = unparty($_POST['page']);
$mode = unparty($_POST['mode']);
$userID = unparty($_POST['userID']);
$count = unparty($_POST['count']);

if ($count == '')
    $count = 10;

if ($mode == 0)
    $mode = 'uploadTime';
else
    $mode = 'likes';

$comments = Comments::get_by_user($userID, $mode);

if (count($comments) == 0)
    die('-2');

for ($i = 0; $i < $count; $i++) {
    if ($comments[$i+$page*10] == null)
        break;

    if ($i != 0)
        echo '|';

    $c = $comments[$i+$page*10];
    $user = Users::get_scores($c['userID']);
    $u = Users::get_by_id($c['userID']);

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
        ."~1~"
        .$c['levelID']
        ."~3~"
        .$c["userID"]
        ."~4~"
        .$c["likes"]
        ."~7~"
        .$c['isSpam']
        ."~10~"
        .$c["percent"];

    if ($modType != 0) {
        echo "~11~"
            .$modType
            ."~12~"
            .$modColor;
    }
    echo "~9~"
        .makeTime($c["uploadTime"])
        ."~6~"
        .$c["commentID"].
        ":1~".$user["userName"]."~9~".$user["icon"]."~10~".$user["color1"]
        ."~11~".$user["color2"]."~14~".$user["iconType"]."~15~".$user["special"]."~16~".$u["accountID"];
}

echo '#' . count($comments) . ":".($page*10).":10";