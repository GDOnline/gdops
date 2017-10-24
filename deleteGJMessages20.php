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

$messageID = unparty($_POST["messageID"]);
$messages = unparty($_POST["messages"]);

if ($messageID != '')
    Messages::remove($messageID);
else {
    foreach (explode(',', $messages) as $m)
        Messages::remove($m);
}

die('1');