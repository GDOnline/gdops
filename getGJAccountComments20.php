<?php
include 'settings.php';
require_once 'libops.php';

$accountID = unparty(htmlspecialchars($_POST['accountID']));
$page = unparty($_POST['page']);

if (!blank($accountID, $page))
	die('-1');

$makeTime = 'makeTime';

$cs = AccountComments::get_in_account($accountID, $page);

for ($i = 0; $i < 10; $i++) {
	if ($cs[$i+$page*10] == null)
		break;

	$c = $cs[$i+$page*10];

	if ($i != 0)
		echo '|';

	echo <<<LOLXD
2~{$c["comment"]}~3~{$c["accountID"]}~4~{$c["likes"]}~5~0~7~{$c['isSpam']}~9~{$makeTime($c["uploadTime"])}~6~{$c["commentID"]}
LOLXD;
}

echo "#" . count($cs) . ":$page:10";