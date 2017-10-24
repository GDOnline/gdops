<?php
include 'settings.php';
require_once 'libops.php';
require_once 'anubis.php';

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);

if (!blank($accountID, $gjp))
	die('-1');

if (!Accounts::verify_gjp($accountID, $gjp))
	die('-1');

if (Accounts::is_disabled($accountID))
	die('-1');

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

$u = Users::get_by_account($accountID);

$need_id_replace = $levelID != '0' ? ', levelID' : '';
$nir = $levelID != '0' ? ', :levelID' : '';

$sql = <<<SQLText
REPLACE INTO opsLevels (levelName, userID, levelVersion, levelDesc, coins, objects, isTwoPlayer, customSongID, song, extraString, password, levelLength, levelInfo, originalID, gameVersion, requestedStars, uploadTime, updateTime$need_id_replace) VALUES (:levelName, :userID, :levelVersion, :levelDesc, :coins, :objects, :twoPlayer, :songID, :audioTrack, :extraString, :password, :levelLength, :levelInfo, :original, :gameVersion, :requestedStars, :uploadTime, :updateTime$nir)
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
	':updateTime' => time()
];

if ($nir != '')
	$data[':levelID'] = $levelID;

$q = $db->prepare($sql);
$q->execute($data);

$anubis = new Anubis();
$anubis->setKey($OPS_SETTINGS['gdps']['security']['anubis_salt']);

$ok = file_put_contents('data/levels/' . $db->lastInsertId() . '.level', $anubis->encrypt($levelString));

if ($ok === false)
	die('-1');

die($db->lastInsertId());