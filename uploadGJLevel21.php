<?php
include 'settings.php';
require_once 'libops.php';
require_once 'anubis.php';

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);
$udid = unparty($_POST['udid']);

if ($accountID != '') {
    if (!blank($accountID, $gjp))
        die('-1');

    if (!Accounts::verify_gjp($accountID, $gjp))
        die('-1');

    if (Accounts::is_disabled($accountID))
        die('-1');
}

if (!IPLimits::limit_levels($_SERVER['REMOTE_ADDR']))
    die('-1');

$levelName = unparty($_POST["levelName"]);
$levelID = unparty($_POST["levelID"]);
$original = unparty($_POST["original"]);
$levelString = unparty($_POST["levelString"]);
$levelDesc = unparty($_POST["levelDesc"]);
$levelInfo = unparty($_POST["levelInfo"]);
$extraString = unparty($_POST["extraString"]);
$songID = unparty($_POST["songID"]);
$audioTrack = unparty($_POST["audioTrack"]);
$twoPlayer = unparty($_POST["twoPlayer"]);
$objects = unparty($_POST["objects"]);
$gameVersion = unparty($_POST["gameVersion"]);
$password = unparty($_POST["password"]);
$coins = unparty($_POST["coins"]);
$levelVersion = unparty($_POST["levelVersion"]);
$levelLength = unparty($_POST["levelLength"]);
$requestedStars = unparty($_POST["requestedStars"]);
$unlisted = unparty($_POST['unlisted']);
$ldm = unparty($_POST['ldm']);

if (strlen($levelName) > 20)
    die('-1');

if ($levelID < 0)
    die('-1');

if ($songID < 0)
    die('-1');

if ($twoPlayer > 1 || $twoPlayer < 0)
    die('-1');

if ($objects < 0)
    die('-1');

if ($coins > 3 || $coins < 0)
    die('-1');

if ($levelVersion < 0)
    die('-1');

if ($requestedStars > 10 || $requestedStars < 0)
    die('-1');

if ($levelLength > 4 || $levelLength < 0)
    die('-1');

if ($unlisted != '1')
    $unlisted = 0;
else
    $unlisted = 1;

if ($accountID != '')
    $u = Users::get_by_account($accountID);
else
    $u = Users::get_by_udid($udid);

if ($levelID == '0') {

    $sql = <<<SQLText
INSERT INTO opsLevels (levelName,
userID,
levelVersion,
levelDesc,
coins,
objects,
isTwoPlayer,
customSongID,
song,
extraString,
password,
levelLength,
levelInfo, 
originalID, 
gameVersion, 
requestedStars, 
uploadTime, 
updateTime,
isUnlisted,
ldm) VALUES (:levelName, 
:userID, 
:levelVersion, 
:levelDesc, 
:coins, 
:objects, 
:twoPlayer, 
:songID, 
:audioTrack,
:extraString,
:password, 
:levelLength, 
:levelInfo, 
:original, 
:gameVersion, 
:requestedStars, 
:uploadTime, 
:updateTime,
:unlisted,
:ldm)
SQLText;

    $data = [
        ':levelName' => $levelName,
        ':userID' => $u['userID'],
        ':levelVersion' => $levelVersion,
        ':levelDesc' => $levelDesc,
        ':coins' => $coins,
        ':objects' => $objects,
        ':twoPlayer' => $twoPlayer,
        ':songID' => $songID,
        ':audioTrack' => $audioTrack,
        ':extraString' => $extraString,
        ':password' => $password,
        ':levelLength' => $levelLength,
        ':levelInfo' => $levelInfo,
        ':original' => $original,
        ':gameVersion' => $gameVersion,
        ':requestedStars' => $requestedStars,
        ':uploadTime' => time(),
        ':updateTime' => time(),
        ':unlisted' => $unlisted,
        ':ldm' => $ldm
    ];

} else {
    $sql = <<<SQLText
UPDATE opsLevels SET levelVersion = :levelVersion, 
levelDesc = :levelDesc, 
coins = :coins, 
objects = :objects,
customSongID = :songID,
song = :audioTrack,
isTwoPlayer = :twoPlayer,
extraString = :extraString,
password = :password,
levelLength = :levelLength,
levelInfo = :levelInfo,
gameVersion = :gameVersion,
requestedStars = :requestedStars,
updateTime = :updateTime,
ldm = :ldm WHERE levelID = :levelID
SQLText;

    $data = [
        ':levelVersion' => $levelVersion,
        ':levelDesc' => $levelDesc,
        ':coins' => $coins,
        ':objects' => $objects,
        ':twoPlayer' => $twoPlayer,
        ':songID' => $songID,
        ':audioTrack' => $audioTrack,
        ':extraString' => $extraString,
        ':password' => $password,
        ':levelLength' => $levelLength,
        ':levelInfo' => $levelInfo,
        ':gameVersion' => $gameVersion,
        ':requestedStars' => $requestedStars,
        ':updateTime' => time(),
        ':levelID' => $levelID,
        ':ldm' => $ldm
    ];
}

$q = $db->prepare($sql);
$q->execute($data);

$anubis = new Anubis();
$anubis->setKey($OPS_SETTINGS['gdps']['security']['anubis_salt']);

$insertedID = $db->lastInsertId();

if ($levelID != '0')
    $insertedID = $levelID;

$ok = file_put_contents('data/levels/' . $insertedID . '.level', $anubis->encrypt($levelString));

if ($ok === false)
	die('-1 (filerr)');

die($insertedID);