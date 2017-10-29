<?php
include 'settings.php';
require_once 'libops.php';

$itemID = unparty($_POST["itemID"]);
$like = unparty($_POST["like"]);
$type = unparty($_POST["type"]);

$ip = $_SERVER['REMOTE_ADDR'];

if (!IPLimits::limit_likes($ip))
    die('-1');

switch ($type) {
    case '1':
        Likes::level($like, $itemID, $ip);
        die('1');

    case '2':
        Likes::comment($like, $itemID, $ip);
        die('1');

    case '3':
        Likes::accComment($like, $itemID, $ip);
        die('1');
}

die ('-1');