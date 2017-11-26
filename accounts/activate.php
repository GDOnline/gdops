<?php
include '../settings.php';
require_once '../libops.php';

$_s = $OPS_SETTINGS['gdps']['registration'];

if ($_GET['act'] != null) {
    $q = $db->prepare("SELECT * FROM opsAccounts WHERE actCode = :c");
    $q->execute([':c' => $_GET['act']]);

    if ($q->rowCount() == 0)
        die("Failed to activate account - nothing found.");

    $r = $q->fetch(2);

    $q = $db->prepare("UPDATE opsAccounts SET actCode = '' WHERE accountID = :a");
    $q->execute([':a' => $r['accountID']]);

    die("Account has been successfully activated.");
}