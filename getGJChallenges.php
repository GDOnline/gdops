<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty($_POST['accountID']);
$udid = unparty($_POST['udid']);
$chk = unparty($_POST['chk']);

$quests = UserRewards::load_quests($udid, $chk, $accountID);

$string = base64_encode(xorchar($quests, 19847));
exit('AAAAA' . $string . '|' . sha1($string . 'oC36fpYaPtdg'));