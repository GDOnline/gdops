<?php
include '../settings.php';
require_once '../libops.php';

$userName = unparty(htmlspecialchars($_POST['userName']));
$password = unparty(htmlspecialchars($_POST['password']));
$udid = unparty(htmlspecialchars($_POST['udid']));

if (!blank($userName, $password, $udid))
	die('-1');

$_e = $OPS_SETTINGS['gdps']['security'];
$password = hash($_e['pass_algo'], $password . $_e['salt']);

$account = Accounts::get_by_auth($userName, $password);

if (!$account)
	die('-1');

if (Accounts::is_disabled($account['accountID']))
	die('-12');

if ($account['actCode'] != '')
	die('-1');

if (Users::check_user_by_account($account['accountID'])) {
	$user = Users::get_by_account($account['accountID']);

	die($account['accountID'] . ',' . $user['userID']);
} else {
	$userID = Users::create_new($udid, $account['accountID']);

	die($account['accountID'] . ',' . $userID);
}