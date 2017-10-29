<?php
include 'settings.php';
require_once 'libops.php';

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

$q = $db->prepare("SELECT * FROM opsDailyLevels ORDER BY dailyID DESC LIMIT 1");
$q->execute();

$level = $q->fetch(2);

$next = strtotime("tomorrow 00:00:00");
$left = $next - time();

exit($level['dailyID'] . '|' . $left);