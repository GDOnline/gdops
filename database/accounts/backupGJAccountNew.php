<?php
include '../../rel/geodashonline/settings.php';
require_once '../../rel/geodashonline/libops.php';
require_once '../../rel/geodashonline/anubis.php';

$userName = unparty(htmlspecialchars($_POST['userName']));
$password = unparty(htmlspecialchars($_POST['password']));

$_e = $OPS_SETTINGS['gdps']['security'];
$password = hash($_e['pass_algo'], $password . $_e['salt']);

$account = Accounts::get_by_auth($userName, $password);

if (!$account)
    die('-1');

$anubis = new Anubis();
$anubis->setKey($password);

$ok = file_put_contents('../../rel/geodashonline/data/backups/' . $userName . '.backup', $anubis->encrypt($_POST["saveData"]));

if ($ok === false)
    die('-1 fileerror');

die('1');