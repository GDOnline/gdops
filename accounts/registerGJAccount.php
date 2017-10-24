<?php
include '../settings.php';
require_once '../libops.php';

$userName = unparty(htmlspecialchars($_POST['userName']));
$password = unparty(htmlspecialchars($_POST['password']));
$email = unparty(htmlspecialchars($_POST['email']));

if (!blank($userName, $password, $email))
	die('-1');

if (Accounts::check_account_by_username($userName))
	die('-2');

$_s = $OPS_SETTINGS['gdps']['registration'];

$_e = $OPS_SETTINGS['gdps']['security'];
$password = hash($_e['pass_algo'], $password . $_e['salt']);

$act = array(
	'hash' => hash($_s['hash_algo'], $userName . $_s['activation_salt'] . $password),
	'key' => base64_encode($userName . ';' . $password . ';' . $email)
);

if ($_s['enable_email_verify']) {
	if (Accounts::check_account_by_email($email))
		die('-3');

	$body = <<<BODY
Thanks for registration in Geometry Dash Online Private Server! Please, activate your account.

Link: http://gdops.tk/?act=activate

Secret key: {$act['key']}
Activation code: {$act['key']}

BODY;

	$email = array(
		'subject' => 'Geometry Dash Online',
		'body' => $body,
		'from' => 'geodashonline@gmail.com'
	);

	$email_sent = mail($email, $email['subject'], $email['body'], 'From: ' . $email['from']);

	if (!$email_sent)
		die('-1');
} else
	$act['hash'] = '';

Accounts::register_new($userName, $password, $email, $act);
die('1');