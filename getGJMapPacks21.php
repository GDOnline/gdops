<?php
include 'settings.php';
require_once 'libops.php';

$page = unparty($_POST["page"]);

$packs = MapPacks::get();

$str = '';

for ($i = 0; $i < 10; $i++) {
    if ($packs[$i+$page*10] == null)
        break;

    if ($i != 0) {
        echo '|';
        $str .= ',';
    }

    $mp = $packs[$i+$page*10];

    echo "1:".$mp["packID"].":2:".$mp["packName"].":3:".$mp["packLevels"].":4:".$mp["packStars"].":5:".$mp["packCoins"].":6:".$mp["packDifficulty"].":7:".$mp["packColor"].":8:".$mp["packColor"];
    $str .= $mp['packID'];
}

echo '#' . count($packs) . ":$page:10#" . MapPacks::generate_hash($str);