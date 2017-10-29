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

$levelDesc = unparty($_POST["levelDesc"]);
$levelID = unparty($_POST["levelID"]);

$q = $db->prepare("UPDATE opsLevels SET levelDesc = :d WHERE levelID = :l AND userID = :u");
$q->execute([
   ':d' => $levelDesc,
   ':l' => $levelID,
   ':u' => Users::get_by_account($accountID)['userID']
]);

die('1');