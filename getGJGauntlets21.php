<?php
include 'settings.php';
require_once 'libops.php';

$q = $db->prepare("SELECT * FROM opsGauntlets");
$q->execute();

$r = $q->fetchAll();

$str = '';

for ($i = 0; $i < count($r); $i++) {
    if ($i != 0) {
        echo '|';
        $str .= ',';
    }

    $g = $r[$i];

    $str .= $g["gauntletID"] . $g["levels"];
    echo "1:" . $g["gauntletID"] . ":3:" . $g["levels"];
}

echo '#' . sha1($str . 'xI25fpAapCQg');