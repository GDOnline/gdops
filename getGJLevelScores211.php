<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);
$type = unparty($_POST['type']);

if (!blank($accountID, $gjp))
    die('-1');

if (!Accounts::verify_gjp($accountID, $gjp))
    die('-1');

if (Accounts::is_disabled($accountID))
    die('-1');

$percent = unparty($_POST["percent"]);
$levelID = unparty($_POST["levelID"]);

Leaderboards::update_level_score($accountID, $levelID, $percent);

$scores = array();

if ($type == 0)
    $scores = Leaderboards::get_level_leaderboard($accountID, $levelID);
else if ($type == 1)
    $scores = Leaderboards::get_level_gleaderboard($levelID);
else if ($type == 2)
    $scores = Leaderboards::get_level_gleaderboard($levelID);

for ($i = 0; $i < count($scores); $i++) {
    if ($i != 0)
        echo '|';

    $r = $scores[$i];
    $u = Users::get_by_account($r['accountID']);

    $us = Users::get_scores($u['userID']);

    echo "1:".$us["userName"].":2:".$u["userID"].":9:".$us["icon"].":10:".$us["color1"].":11:".$us["color2"].":14:".$us["iconType"].":15:".$us["special"].":16:".$u["accountID"].":3:".$r["percent"].":6:".($i + 1).":42:".makeTime($r["updateTime"]);
}