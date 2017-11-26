<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty($_POST["accountID"]);
$gjp = unparty($_POST["gjp"]);
$udid = unparty($_POST['udid']);
$weekly = unparty($_POST['weekly']);

if ($accountID != '') {
    if (!blank($accountID, $gjp))
        die('-1');

    if (!Accounts::verify_gjp($accountID, $gjp))
        die('-1');

    if (Accounts::is_disabled($accountID))
        die('-1');
}

if ($weekly == '1') {
    $q = $db->prepare("SELECT * FROM opsDailyLevels WHERE isWeekly = 1 ORDER BY dailyID DESC LIMIT 1");
} else {
    $q = $db->prepare("SELECT * FROM opsDailyLevels WHERE isWeekly = 0 ORDER BY dailyID DESC LIMIT 1");
}

$q->execute();

$level = $q->fetch(2);

if ($weekly == '1')
    $next = strtotime("next week 00:00:00");
else
    $next = strtotime("tomorrow 00:00:00");
$left = $next - time();

exit($level['dailyID'] . '|' . $left);