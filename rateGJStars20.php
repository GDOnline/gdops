<?php
include 'settings.php';
require_once 'libops.php';

$levelID = unparty($_POST['levelID']);
$stars = unparty($_POST['stars']);

if (!IPLimits::limit_rates(unparty($_SERVER['REMOTE_ADDR'])))
    die('-1');

Levels::rate($levelID, $stars, unparty($_SERVER['REMOTE_ADDR']));
die('1');