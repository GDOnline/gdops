<?php
include 'settings.php';
require_once 'libops.php';

ini_set('memory_limit', '-1');

$type = unparty(htmlspecialchars($_POST["type"]));
$str = unparty(htmlspecialchars($_POST["str"]));
$accountID = unparty(htmlspecialchars($_POST["accountID"]));
$page = unparty(htmlspecialchars($_POST["page"]));
$diff = unparty(htmlspecialchars($_POST["diff"]));
$star = unparty(htmlspecialchars($_POST["star"]));
$noStar = unparty(htmlspecialchars($_POST["noStar"]));
$featured = unparty(htmlspecialchars($_POST["featured"]));
$len = unparty(htmlspecialchars($_POST["len"]));
$twoPlayer = unparty(htmlspecialchars($_POST["twoPlayer"]));
$followed = unparty(htmlspecialchars($_POST["followed"]));
$song = unparty(htmlspecialchars($_POST["song"]));
$epic = unparty(htmlspecialchars($_POST["epic"]));
$demonFilter = unparty(htmlspecialchars($_POST["demonFilter"]));
$gauntlet = unparty(htmlspecialchars($_POST["gauntlet"]));

$conds = '';

if ($gauntlet != null) {
    $q = $db->prepare("SELECT * FROM opsGauntlets WHERE gauntletID = :g");
    $q->execute([':g' => $gauntlet]);

    $r = $q->fetch(2);

    $levels = Levels::get_multiple_levels($r['levels']);

    $lIDs = array();

    for ($i = 0; $i < 6; $i++) {
        if ($levels[$i] == null)
            break;

        if ($i != 0) {
            echo '|';
            $users .= '|';
        }

        $level = $levels[$i];
        array_push($lIDs, $level['levelID']);

        echo "1:".$level["levelID"].":2:".$level["levelName"].":5:".$level["levelVersion"].":6:".$level["userID"].":8:10:9:".$level['levelDifficulty'].":10:".$level["downloads"].":12:".$level["song"].":13:".$level["gameVersion"].":14:".$level["likes"].":17:".$level['isDemon'].":43:".$level["demonType"].":25:".$level['isAuto'].":18:".$level["stars"].":19:".$level["isFeatured"].":3:".$level["levelDesc"].":15:".$level["levelLength"].":30:".$level["originalID"].":31:0:37:".$level["coins"].":38:".$level["isVerified"].":39:".$level["requestedStars"].":35:".$level["customSongID"].":42:".$level["isEpic"];
        echo ':45:0:46:1:47:2:44:' . $gauntlet;

        $u = Users::get_by_id($level['userID']);
        $us = Users::get_scores($u['userID']);

        $users .= $u["userID"].":".$us["userName"].":".$u["accountID"];
    }

    echo "#$users##" . count($levels) . ":$page:10#" . Levels::generate_hash_for_get($lIDs);
    exit;
}

if ($diff != '-') {
	if ($diff <= -1 && $diff >= -3) {
		switch ($diff) {
			case '-1':
				Levels::add_condition($conds, 'levelDifficulty = 0');
				break;

			case '-2':
				Levels::add_condition($conds, 'isDemon = 1');
				break;

			case '-3':
				Levels::add_condition($conds, 'isAuto = 1');
				break;
		}
	} else {
		$diffs = explode(',', $diff);

		for ($i = 0; $i < count($diffs); $i++)
			Levels::add_condition($conds, 'levelDifficulty = ' . $diffs[$i], true);
	}
}

if ($demonFilter != '') {
	if ($diff == '-2') {
		$demonType = Levels::make_demon($demonFilter);
		Levels::add_condition($conds, 'demonType = ' . $demonType);
	}
}

if ($song != '') {
	if ($_POST['customSong'] == '1')
		Levels::add_condition($conds, 'customSongID = ' . $song);
	else
		Levels::add_condition($conds, 'song = ' . $song - 1 . ' AND customSongID = 0'); 
}

if ($star != '')
	Levels::add_condition($conds, 'stars != 0');

if ($noStar != '')
	Levels::add_condition($conds, 'stars == 0');

if ($featured != '0')
	Levels::add_condition($conds, 'isFeatured != 0');

if ($epic == '1')
	Levels::add_condition($conds, 'isEpic != 0');

if ($twoPlayer != '0')
	Levels::add_condition($conds, 'isTwoPlayer != 0');

if ($len != '-') {
	$lens = explode(',', $len);

	for ($i = 0; $i < count($lens); $i++)
		Levels::add_condition($conds, 'levelLength = ' . $lens[$i], true);
}

$levels = array();

switch ($type) {
	case 0:
		if (is_numeric($str))
			$levels = Levels::get_by_id($str);
		else
			$levels = Levels::get_by_str($str);
		break;

	case 1:
		$levels = Levels::get_most_downloaded($conds);
		break;

	case 2:
		$levels = Levels::get_most_liked($conds);
		break;

	case 4:
		$levels = Levels::get_recent($conds);
		break;

	case 16:
		$levels = Levels::get_fame($conds);
		break;

	case 5:
		$levels = Levels::get_by_user($str);
		break;

	case 6:
		$levels = Levels::get_featured();
		break;

	case 7:
		$levels = Levels::get_magic();
		break;

	case 11:
		$levels = Levels::get_star_rated();
		break;

	case 12:
		$levels = Levels::get_by_followed($followed);
		break;

	case 13:
		$levels = Levels::get_by_friends($accountID);
		break;

	case 10:
		$levels = Levels::get_multiple_levels($str);
		break;

	default:
		$levels = Levels::get_recent($conds);
		break;
}

$lIDs = array();
$users = '';

for ($i = 0; $i < 10; $i++) {
	if ($levels[$i+$page*10] == null)
		break;

	if ($i != 0) {
		echo '|';
		$users .= '|';
	}

	$level = $levels[$i+$page*10];
	array_push($lIDs, $level['levelID']);

	echo "1:".$level["levelID"].":2:".$level["levelName"].":5:".$level["levelVersion"].":6:".$level["userID"].":8:10:9:".$level['levelDifficulty'].":10:".$level["downloads"].":12:".$level["song"].":13:".$level["gameVersion"].":14:".$level["likes"].":17:".$level['isDemon'].":43:".$level["demonType"].":25:".$level['isAuto'].":18:".$level["stars"].":19:".$level["isFeatured"].":3:".$level["levelDesc"].":15:".$level["levelLength"].":30:".$level["originalID"].":31:0:37:".$level["coins"].":38:".$level["isVerified"].":39:".$level["requestedStars"].":35:".$level["customSongID"].":42:".$level["isEpic"];

	$u = Users::get_by_id($level['userID']);
	$us = Users::get_scores($u['userID']);

	$users .= $u["userID"].":".$us["userName"].":".$u["accountID"];
}

echo "#$users##" . count($levels) . ":$page:10#" . Levels::generate_hash_for_get($lIDs);