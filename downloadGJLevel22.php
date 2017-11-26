<?php
include 'settings.php';
require_once 'libops.php';
require_once 'anubis.php';

$levelID = unparty($_POST["levelID"]);
$dailyID = 0;

if ($levelID == '-1' || $levelID == '-2') {
    if ($levelID == '-1')
        $q = $db->prepare("SELECT * FROM opsDailyLevels WHERE isWeekly = 0 ORDER BY dailyID DESC LIMIT 1");
    else
        $q = $db->prepare("SELECT * FROM opsDailyLevels WHERE isWeekly = 1 ORDER BY dailyID DESC LIMIT 1");

    $q->execute();
    $r = $q->fetch(2);

    $levelID = $r['levelID'];
    $dailyID = $r['dailyID'];
}

$result = Levels::download($levelID);

$anubis = new Anubis();
$anubis->setKey($OPS_SETTINGS['gdps']['security']['anubis_salt']);

$un = file_get_contents('data/levels/' . $levelID . '.level');

if ($un === false)
	die('-1');

$levelString = $anubis->decrypt($un);

echo "40:".$result['ldm'].":1:".$result["levelID"].":2:".$result["levelName"].":3:".$result["levelDesc"].":4:".$levelString.":5:".$result["levelVersion"].":6:".$result["userID"].":8:10:9:".$result["levelDifficulty"].":10:".$result["downloads"].":11:1:12:".$result["song"].":13:".$result["gameVersion"].":14:".$result["likes"].":17:".$result["isDemon"].":43:".$result["demonType"].":25:".$result["isAuto"].":18:".$result["stars"].":19:".$result["isFeatured"].":42:".$result["isEpic"].":45:0:15:".$result["levelLength"].":30:".$result["originalID"].":31:0:28:".makeTime($result["uploadTime"]). ":29:".makeTime($result["updateTime"]). ":35:".$result["customSongID"].":36:".$result["extraString"].":37:".$result["coins"].":38:".$result["isVerified"].":39:".$result["requestedStars"].":46:1:47:2:27:" . Levels::encode_password($result["password"]);

if ($dailyID != 0)
    echo ':41:' . $dailyID;

echo '#' . Levels::generate_hash_for_dl_1($levelString);
$sh = $result["userID"] . "," . $result["stars"] . "," . $result["isDemon"] . "," . $result["levelID"] . "," . $result["isVerified"] . "," . $result["isFeatured"] . "," . $result["password"] . "," .$dailyID;
echo "#" . Levels::generate_hash_for_dl_2($sh) . "#";

if ($dailyID != 0) {
    $user = Users::get_by_id($result['userID']);
    $us = Users::get_scores($user['userID']);

    echo $user["userID"] . ":" . $us["userName"] . ":" . $user["accountID"];
} else
    echo $sh;

Levels::increase_dl($levelID, $_SERVER['REMOTE_ADDR']);