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

if (Comments::is_banned($accountID))
    die('-10');

$comment = unparty($_POST["comment"]);
$levelID = unparty($_POST["levelID"]);
$percent = $_POST["percent"];

$ip = $_SERVER['REMOTE_ADDR'];

if (!IPLimits::limit_comments($ip))
    die('-1');

Comments::upload($comment, $accountID, $levelID, $percent);
die('1');