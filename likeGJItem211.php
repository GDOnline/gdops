<?php
include 'settings.php';
require_once 'libops.php';

$itemID = unparty($_POST["itemID"]);
$like = unparty($_POST["like"]);
$type = unparty($_POST["type"]);
$accountID = unparty($_POST['accountID']);
$gjp = unparty($_POST['gjp']);
$udid = unparty($_POST['udid']);
$uuid = unparty($_POST['uuid']);

if ($accountID != '' || $accountID != '0') {
    if (!Accounts::verify_gjp($accountID, $gjp))
        die('-1');

    if (Accounts::is_disabled($accountID))
        die('-1');
} else
    $accountID = 0;

if (!IPLimits::limit_likes_new($udid, $accountID, $uuid))
    die('-1');

switch ($type) {
    case '1':
        Likes::level_new($like, $itemID, $udid);
        die('1');

    case '2':
        Likes::comment_new($like, $itemID, $udid);
        die('1');

    case '3':
        Likes::accComment_new($like, $itemID, $udid);
        die('1');
}

die ('-1');