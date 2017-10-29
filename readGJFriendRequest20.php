<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);
$requestID = unparty($_POST['requestID']);

if (!Accounts::verify_gjp($accountID, $gjp))
    die('-1');

if (Accounts::is_disabled($accountID))
    die('-1');

$q = $db->prepare("UPDATE opsFriendRequests SET isNew = 0 WHERE requestID = :r");
$q->execute([':r' => $requestID]);

die(1);