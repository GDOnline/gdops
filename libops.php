<?php
error_reporting(E_ERROR);

function unparty($str) {
	return trim(stripslashes(array("'", "(", ")", "~", ":", '|'), "", $str)));
}

function blank(...$strs) {
	foreach ($strs as $str)
		if (trim($str) == "") return false;

	return true;
}

function xorchar($str, $key) {
	$key = text2ascii($key);
	$plaintext = text2ascii($str);
	$keysize = count($key);
	$input_size = count($plaintext);
	$cipher = "";

	for ($i = 0; $i < $input_size; $i++)
		$cipher .= chr($plaintext[$i] ^ $key[$i % $keysize]);

	return $cipher;
}

function text2ascii($text) {
    return array_map('ord', str_split($text));
}

function oplog(...$data) {
    return;
}

function adminLog(...$data) {
    file_put_contents('admin-logs.txt', implode(' ', $data) . "\n", FILE_APPEND);
}

function makeTime($timestamp) {
$ts = $timestamp;
$cts = time();

$str = "";
$result = $cts-$ts;

if ($result < 31556952) {
	if ($result < 2629746) {
		if ($result < 86400) {
			if ($result < 3600) {
				if ($result < 60) {
					$n = $result/1;
						if ($n == 1){
							$str = " second";
						}else{
							$str = " seconds";
						}
					$final = $n.$str;
				}else{
					$n = floor($result/60);
					if ($n == 1){
						$str = " minute";
  					}else{
  						$str = " minutes";
					}
					$final = $n.$str;
 				}
            }else{
            	$n = floor($result/3660);
            	if ($n == 1){
                    $str = " hour";
                    }else{
                    $str = " hours";
                    }
                    $final = $n.$str;
            	}
        	}else{
        $n = floor($result/86400);
        if ($n == 1){
                    $str = " day";
                    }else{
                    $str = " days";
                    }
                    $final = $n.$str;
        }
    }else{
    $n = floor($result/2629746);
    if ($n == 1){
                    $str = " month";
                    }else{
                    $str = " months";
                    }
                    $final = $n.$str;
    }
}else{
$n = floor($result/31556952);
if ($n == 1){
                    $str = " year";
                    }else{
                    $str = " years";
                    }
                    $final = $n.$str;
}
return $final;
}

class Accounts {
	function check_account_by_username($u) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsAccounts WHERE userName LIKE :userName");
		$q->execute([':userName' => $u]);

		return $q->rowCount() > 0;
	}

	function check_account_by_email($e) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsAccounts WHERE email LIKE :email");
		$q->execute([':email' => $e]);

		return $q->rowCount() > 0;
	}

	function register_new($u, $p, $e, $a) {
		include 'settings.php';

		$q = $db->prepare("INSERT INTO opsAccounts (userName, password, email, actCode) VALUES (:u, :p, :e, :h)");
		$q->execute([
			':u' => $u,
			':p' => $p,
			':e' => $e,
			':h' => $a['hash']
		]);
	}

	function get_by_auth($u, $p) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsAccounts WHERE userName LIKE :u AND password LIKE :p LIMIT 1");
		$q->execute([
			':u' => $u,
			':p' => $p
		]);

		if ($q->rowCount() == 0)
			return false;

		return $q->fetch(2);
	}

	function get_by_id($id) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsAccounts WHERE accountID = :id LIMIT 1");
		$q->execute([
			':id' => $id
		]);

		if ($q->rowCount() == 0)
			return false;

		return $q->fetch(2);
	}

	function is_disabled($id) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsDisabledAccounts WHERE accountID = :a LIMIT 1");
		$q->execute([
			':a' => $id
		]);

		return $q->rowCount() > 0;
	}

	function verify_gjp($id, $gjp) {
		include 'settings.php';

		$_e = $OPS_SETTINGS['gdps']['security'];
		$password = hash($_e['pass_algo'], xorchar(base64_decode($gjp), 37526) . $_e['salt']);

		$q = $db->prepare("SELECT * FROM opsAccounts WHERE accountID = :a AND password = :p LIMIT 1");
		$q->execute([
			':a' => $id,
			':p' => $password
		]);

		return $q->rowCount() > 0;
	}

	function get_profile($id) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsAccountProfiles WHERE accountID = :id LIMIT 1");
		$q->execute([
			':id' => $id
		]);

		if ($q->rowCount() == 0)
			return false;

		return $q->fetch(2);
	}

	function is_blocked_by($accountID, $targetAccountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsBlockedUsers WHERE accountID = :t AND targetAccountID = :id LIMIT 1");
		$q->execute([
			':id' => $accountID,
			':t' => $targetAccountID
		]);

		return $q->rowCount() > 0;
	}

	function block($accountID, $targetAccountID) {
		include 'settings.php';

		$q = $db->prepare("REPLACE INTO opsBlockedUsers (accountID, targetAccountID) VALUES (:a, :t)");
		$q->execute([':a' => $accountID, ':t' => $targetAccountID]);
	}

	function unblock($accountID, $targetAccountID) {
		include 'settings.php';

		$q = $db->prepare("DELETE FROM opsBlockedUsers WHERE accountID = :a AND targetAccountID = :t");
		$q->execute([':a' => $accountID, ':t' => $targetAccountID]);
	}
}

class Users {
	function check_user_by_account($id) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsUsers WHERE accountID = :a LIMIT 1");
		$q->execute([
			':a' => $id
		]);

		return $q->rowCount() > 0;
	}

	function get_by_account($id) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsUsers WHERE accountID = :a LIMIT 1");
		$q->execute([
			':a' => $id
		]);

		return $q->rowCount() > 0 ? $q->fetch(2) : false;
	}

	function get_by_id($id) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsUsers WHERE userID = :a LIMIT 1");
		$q->execute([
			':a' => $id
		]);

		return $q->rowCount() > 0 ? $q->fetch(2) : false;
	}

	function update_udid($id, $udid) {
		include 'settings.php';

		$q = $db->prepare("UPDATE opsUsers SET udid = :udid WHERE userID = :userID");
		$q->execute([
			':udid' => $udid,
			':userID' => $id
		]);
	}

	function create_new($udid, $accountID = 0) {
		include 'settings.php';

		$q = $db->prepare("REPLACE INTO opsUsers (udid, accountID) VALUES (:u, :a)");
		$q->execute([
			':u' => $udid,
			':a' => $accountID
		]);

		return $db->lastInsertId();
	}

	function get_by_udid($udid) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsUsers WHERE udid = :u LIMIT 1");
		$q->execute([
			':u' => $udid
		]);

		return $q->rowCount() > 0 ? $q->fetch(2) : false;
	}

	function check_user_by_udid($udid) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsUsers WHERE udid = :u LIMIT 1");
		$q->execute([
			':u' => $udid
		]);

		return $q->rowCount() > 0;
	}

	function is_banned($userID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsBannedUsers WHERE userID = :u LIMIT 1");
		$q->execute([
			':u' => $userID
		]);

		return $q->rowCount() > 0;
	}

	function calculate_top_position($userID) {
		if (Users::is_banned($userID))
			return 0;

		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsUserScores ORDER BY stars DESC");
		$q->execute();

		$r = $q->fetchAll();

		for ($i = 0; $i < count($r); $i++) {
			if ($r[$i]['userID'] == $userID)
				return $i + 1;
		}

		return 0;
	}

	function calculate_creator_points($userID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsLevels WHERE userID = :u AND stars != 0");
		$q->execute([':u' => $userID]);

		$cps = 0;

		foreach ($q->fetchAll() as $level) {
			$cps++;

			if ($level['isFeatured'] != '0')
				$cps++;

			if ($level['isEpic'] != '0')
				$cps++;
		}

		return $cps;
	}

	function calculate_creator_position($userID) {
        include "settings.php";

        $users = array();

        $q = $db->prepare("SELECT * FROM opsUsers LIMIT 100");
        $q->execute();

        $uusers = $q->fetchAll();

        foreach ($uusers as $u)
            $users[$u['userID']] = Users::calculate_creator_points($u['userID']);

        arsort($users);

        $rusers = array();

        foreach ($users as $key => $value)
           array_push($rusers, $key);

        for ($i = 0; $i < count($rusers); $i++) {
            if ($rusers[$i] == $userID)
                return $i + 1;
        }

        return 0;
    }

	function get_scores($userID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsUserScores WHERE userID = :u LIMIT 1");
		$q->execute([
			':u' => $userID
		]);

		return $q->rowCount() > 0 ? $q->fetch(2) : false;
	}
}

class Anticheat {
	function check_values($userID, $stars, $coins, $userCoins, $demons) {
	    include 'settings.php';

		$maxStars = 200;
		$maxCoins = 100;
		$maxUserCoins = 10;
		$maxDemons = 4;

        $q = $db->prepare("SELECT * FROM opsLevels WHERE stars != 0 ORDER BY levelID DESC");
        $q->execute();

        $levels = $q->fetchAll();

		foreach ($levels as $l) {
		    $maxStars += $l['stars'];

		    if ($l['levelDifficulty'] == 50 && $l['isDemon'] == 1)
		        $maxDemons++;

		    if ($l['isVerified'])
		        $maxUserCoins += $l['coins'];
        }

        $q = $db->prepare("SELECT * FROM opsDailyLevels");
        $q->execute();

        foreach ($q->fetchAll() as $l) {
            $lvl = Levels::get_by_id($l['levelID'])[0];

            $maxStars += $lvl['stars'];
            if ($lvl['isVerified'] == '1')
                $maxUserCoins += $lvl['coins'];

            if ($lvl['isDemon'])
                $maxDemons += 1;
        }

        $mappacks = MapPacks::get();

		foreach ($mappacks as $m) {
            $maxStars += $m['packStars'];
            $maxCoins += $m['packCoins'];
        }

		if ($stars > $maxStars || $coins > $maxCoins || $userCoins > $maxUserCoins || $demons > $maxDemons) {
		    $q = $db->prepare("REPLACE INTO opsBannedUsers (userID) VALUES (:u)");
		    $q->execute([':u' => $userID]);

		    return false;
        } else
            return true;
	}
}

class AccountComments {
	function get_in_account($accountID, $page) {
		include 'settings.php';

		$sql = "SELECT * FROM opsAccountComments WHERE accountID = :a ORDER BY commentID DESC";

		$q = $db->prepare($sql);
		$q->execute([
			':a' => $accountID
		]);

		return $q->fetchAll();
	}

	function create_new($accountID, $comment) {
		include 'settings.php';

		$q = $db->prepare("INSERT INTO opsAccountComments (accountID, comment, uploadTime) VALUES (:a, :c, :t)");
		$q->execute([
			':a' => $accountID,
			':c' => $comment,
			':t' => time()
		]);
	}

	function is_banned($accountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsAccCommentBans WHERE accountID = :a LIMIT 1");
		$q->execute([':a' => $accountID]);

		return $q->rowCount() > 0;
	}

    function get_ban_reason($accountID) {
        include 'settings.php';

        $q = $db->prepare("SELECT * FROM opsAccCommentBans WHERE accountID = :a LIMIT 1");
        $q->execute([':a' => $accountID]);

        $r = $q->fetch(2);

        if ($r['reason'] == '')
            return '';
        else
            return '_' . $r['reason'];
    }
}

class Messages {
	function get_recv($accountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsMessages WHERE targetAccountID = :a ORDER BY messageID DESC");
		$q->execute([':a' => $accountID]);

		return $q->fetchAll();
	}

	function get_sent($accountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsMessages WHERE accountID = :a ORDER BY messageID DESC");
		$q->execute([':a' => $accountID]);

		return $q->fetchAll();
	}

	function count_unread($accountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsMessages WHERE targetAccountID = :a AND isRead = 0");
		$q->execute([':a' => $accountID]);

		return $q->rowCount();
	}

	function send($accountID, $toAccountID, $subject, $body) {
		include 'settings.php';

		$q = $db->prepare("INSERT INTO opsMessages (accountID, targetAccountID, subject, body, uploadTime) VALUES (:a, :t, :s, :b, :u)");
		$q->execute([
			':a' => $accountID,
			':t' => $toAccountID,
			':s' => $subject,
			':b' => $body,
			':u' => time()
		]);
	}

	function remove($messageID) {
        include 'settings.php';

        $q = $db->prepare("DELETE FROM opsMessages WHERE messageID = :m");
        $q->execute([':m' => $messageID]);
    }
}

class Friends {
	function get_friendships($accountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsFriends WHERE accountID = :a");
		$q->execute([':a' => $accountID]);

		return $q->fetchAll();
	}

	function get_blocked($accountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsBlockedUsers WHERE accountID = :a");
		$q->execute([':a' => $accountID]);

		return $q->fetchAll();
	}

	function count_new($accountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsFriends WHERE accountID = :a AND isNew != 0");
		$q->execute([':a' => $accountID]);

		return $q->rowCount();
	}

	function get_sent_requests($accountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsFriendRequests WHERE accountID = :a ORDER BY requestID DESC");
		$q->execute([':a' => $accountID]);

		return $q->fetchAll();
	}

	function get_recv_requests($accountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsFriendRequests WHERE targetAccountID = :a ORDER BY requestID DESC");
		$q->execute([':a' => $accountID]);

		return $q->fetchAll();
	}

	function count_new_requests($accountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsFriendRequests WHERE targetAccountID = :a AND isNew != 0");
		$q->execute([':a' => $accountID]);

		return $q->rowCount();
	}

	function new_request($accountID, $toAccountID, $comment) {
		include 'settings.php';

		$q = $db->prepare("INSERT INTO opsFriendRequests (accountID, targetAccountID, comment, uploadTime) VALUES (:a, :t, :c, :u)");
		$q->execute([
			':a' => $accountID,
			':t' => $toAccountID,
			':c' => $comment,
			':u' => time()
		]);
	}

	function accept_request($accountID, $targetAccountID, $requestID) {
		include 'settings.php';

		$q = $db->prepare("DELETE FROM opsFriendRequests WHERE requestID = :r");
		$q->execute([':r' => $requestID]);

		$q = $db->prepare("INSERT INTO opsFriends (accountID, targetAccountID) VALUES (:a, :t)");
		$q->execute([':a' => $accountID, ':t' => $targetAccountID]);

		$q = $db->prepare("INSERT INTO opsFriends (accountID, targetAccountID) VALUES (:t, :a)");
		$q->execute([':a' => $accountID, ':t' => $targetAccountID]);
	}

	function read_new_friends($accountID) {
		include 'settings.php';

		$q = $db->prepare("UPDATE opsFriends SET isNew = 0 WHERE accountID = :a");
		$q->execute([':a' => $accountID]);
	}

	function is_friend($accountID, $targetAccountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsFriends WHERE accountID = :a AND targetAccountID = :t");
		$q->execute([':a' => $accountID, ':t' => $targetAccountID]);

		return $q->rowCount() > 0;
	}

	function me_sent_req($accountID, $targetAccountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsFriendRequests WHERE accountID = :a AND targetAccountID = :t");
		$q->execute([':a' => $accountID, ':t' => $targetAccountID]);

		return $q->rowCount() > 0;
	}

	function he_sent_req($accountID, $targetAccountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsFriendRequests WHERE accountID = :t AND targetAccountID = :a");
		$q->execute([':a' => $accountID, ':t' => $targetAccountID]);

		return $q->rowCount() > 0;
	}

	function cancel_friendship($accountID, $targetAccountID) {
		include 'settings.php';

		$q = $db->prepare("DELETE FROM opsFriends WHERE accountID = :a AND targetAccountID = :t");
		$q->execute([':a' => $accountID, ':t' => $targetAccountID]);

		$q = $db->prepare("DELETE FROM opsFriends WHERE accountID = :t AND targetAccountID = :a");
		$q->execute([':a' => $accountID, ':t' => $targetAccountID]);
	}

	function remove_multiple($accountID, $accounts, $isSender) {
	    include 'settings.php';

        $accountString = '';
        $account = explode(',', $accounts);

	    if ($isSender != '0') {
            for ($i = 0; $i < count($account); $i++) {
                if ($i != 0)
                    $accountString .= ' OR ';

                $accountString .= 'targetAccountID = ' . $account[$i];
            }

            $q = $db->prepare("DELETE FROM opsFriendRequests WHERE accountID = :a AND $accountString");
            $q->execute([':a' => $accountID]);
        } else {
            for ($i = 0; $i < count($account); $i++) {
                if ($i != 0)
                    $accountString .= ' OR ';

                $accountString .= 'accountID = ' . $account[$i];
            }

            $q = $db->prepare("DELETE FROM opsFriendRequests WHERE targetAccountID = :a AND $accountString");
            $q->execute([':a' => $accountID]);
        }
    }

    function remove_one($accountID, $targetAccountID, $isSender) {
        include 'settings.php';

        if ($isSender != '0') {
            $q = $db->prepare("DELETE FROM opsFriendRequests WHERE accountID = :a AND targetAccountID = :t");
            $q->execute([':a' => $accountID, ':t' => $targetAccountID]);
        } else {
            $q = $db->prepare("DELETE FROM opsFriendRequests WHERE accountID = :t AND targetAccountID = :a");
            $q->execute([':a' => $accountID, ':t' => $targetAccountID]);
        }
    }
}

class Levels {
	function generate_hash_for_get($lvlsarray) {
		include "settings.php";
		$hash = "";

		foreach($lvlsarray as $id){
			$query=$db->prepare("SELECT * FROM opsLevels WHERE levelID = :i");
			$query->execute(array('i' => $id));
			$result2 = $query->fetchAll();
			$result = $result2[0];
			$hash = $hash . $result["levelID"][0].$result["levelID"][strlen($result["levelID"])-1].$result["stars"].$result["isVerified"];
		}
		return sha1($hash . "xI25fpAapCQg");
	}

	function generate_hash_for_dl_1($levelString){
	    $t = $levelString;
	    $s = "aaaaa";
	    $size = strlen($t);
	    $val = intval($size/40);
	    $p = 0;
	    for($k = 0; $k < $size ; $k= $k+$val){
	        if($p > 39) break;
	        $s[$p] = $t[$k]; 
	        $p++;
	    }
	    return sha1($s."xI25fpAapCQg");
	}

	function generate_hash_for_dl_2($string) {
		return sha1($string . "xI25fpAapCQg");
	}

	function add_condition(&$conds, $cond, $or = false) {
		if ($conds != '')
			$conds .= ($or ? ' OR' : ' AND')." $cond";
		else
			$conds = $cond;
	}

	function add_where(&$conds) {
		if ($conds != '')
			$conds = 'WHERE ' . $conds;
		else
			$conds = $conds;
	}

	function make_demon($demonFilter) {
		switch ($demonFilter) {
			case '1':
				return 3;
			case '2':
				return 4;
			case '3':
				return 0;
			case '4':
				return 5;
			case '5':
				return 6;
		}
	}

	function get_by_id($id) {
		include "settings.php";

		$q = $db->prepare("SELECT * FROM opsLevels WHERE levelID = :id ORDER BY likes DESC");
		$q->execute([':id' => $id]);

		return $q->fetchAll();
	}

	function get_by_str($str) {
		include "settings.php";

		$q = $db->prepare("SELECT * FROM opsLevels WHERE levelName LIKE CONCAT(:id, '%') AND isUnlisted != 1 ORDER BY likes DESC");
		$q->execute([':id' => $str]);

		return $q->fetchAll();
	}

	function get_most_downloaded($conds) {
		include "settings.php";

		$c = $conds;
		Levels::add_condition($c, 'isUnlisted != 1');
		Levels::add_where($c);

		$q = $db->prepare("SELECT * FROM opsLevels $c ORDER BY downloads DESC");
		$q->execute();

		return $q->fetchAll();
	}

	function get_most_liked($conds) {
		include "settings.php";

		$c = $conds;
		Levels::add_condition($c, 'isUnlisted != 1');
		Levels::add_where($c);

		$q = $db->prepare("SELECT * FROM opsLevels $c ORDER BY likes DESC");
		$q->execute();

		return $q->fetchAll();
	}

	function get_recent($conds) {
		include "settings.php";

		$c = $conds;
		Levels::add_condition($c, 'isUnlisted != 1');
		Levels::add_where($c);

		$q = $db->prepare("SELECT * FROM opsLevels $c ORDER BY levelID DESC");
		$q->execute();

		return $q->fetchAll();
	}

	function get_fame($conds) {
		include "settings.php";

		$c = $conds;
		Levels::add_condition($c, 'isUnlisted != 1');
		Levels::add_condition($c, 'isFame != 0');
		Levels::add_where($c);

		$q = $db->prepare("SELECT * FROM opsLevels $c ORDER BY levelID DESC");
		$q->execute();

		return $q->fetchAll();
	}

	function get_by_user($user) {
		include "settings.php";

		$q = $db->prepare("SELECT * FROM opsLevels WHERE userID = :u AND isUnlisted != 1 ORDER BY levelID DESC");
		$q->execute([':u' => $user]);

		return $q->fetchAll();
	}

	function get_featured() {
		include "settings.php";

		$q = $db->prepare("SELECT * FROM opsLevels WHERE isFeatured != 0 AND isUnlisted != 1 ORDER BY levelID DESC");
		$q->execute([':u' => $user]);

		return $q->fetchAll();
	}

	function get_magic() {
		include "settings.php";

		$q = $db->prepare("SELECT * FROM opsLevels WHERE objects > 9999 AND isUnlisted != 1 ORDER BY levelID DESC");
		$q->execute([':u' => $user]);

		return $q->fetchAll();
	}

	function get_star_rated() {
		include "settings.php";

		$q = $db->prepare("SELECT * FROM opsLevels WHERE stars != 0 AND isUnlisted != 1 ORDER BY levelID DESC");
		$q->execute();

		return $q->fetchAll();
	}

	function get_by_followed($followed) {
		$lvls = array();

		foreach (explode(',', $followed) as $acc) {
			$user = Users::get_by_account($acc);

			array_push($lvls, Levels::get_by_user($user['userID']));
		}

		return $lvls;
	}

	function get_by_friends($accountID) {
        include "settings.php";

		$q = $db->prepare("SELECT * FROM opsFriends WHERE accountID = :a");
		$q->execute([':a' => $accountID]);

		$r = $q->fetchAll();

		$levels = array();

		for ($i = 0; $i < count($r); $i++) {
		    foreach (Levels::get_by_user(Users::get_by_account($r[$i]['targetAccountID'])['userID']) as $l)
		        array_push($levels, $l);
        }

        return $levels;
	}

	function remove($accountID, $levelID) {
        include "settings.php";

        $q = $db->prepare("DELETE FROM opsLevels WHERE levelID = :l AND userID = :u");
        $q->execute([':l' => $levelID, ':u' => Users::get_by_account($accountID)['userID']]);

        return unlink('data/levels/' . $levelID . '.level');
    }

	function get_multiple_levels($ids) {
	    include "settings.php";

		$lids = explode(',', $ids);

		$c = '';

		foreach($lids as $id)
			Levels::add_condition($c, "levelID = $id", true);

		Levels::add_where($c);

		$q = $db->prepare("SELECT * FROM opsLevels $c");
		$q->execute();

		return $q->fetchAll();
	}

	function encode_password($password) {
		$key = text2ascii(26364);
		$plaintext = text2ascii($password);
		$keysize = count($key);
		$input_size = count($plaintext);
		$cipher = "";
		for ($i = 0; $i < $input_size; $i++)
			$cipher .= chr($plaintext[$i] ^ $key[$i % $keysize]);
		return base64_encode($cipher);
	}

	function download($id) {
		include "settings.php";

		$q = $db->prepare("SELECT * FROM opsLevels WHERE levelID = :id");
		$q->execute([':id' => $id]);

		return $q->fetch(2);
	}

	function increase_dl($id, $ip) {
		include "settings.php";

        $q = $db->prepare("SELECT * FROM opsIPDownloaded WHERE levelID = :l AND IP = '$ip'");
        $q->execute([':l' => $id]);

        if ($q->rowCount() > 0)
            return;

        $q = $db->prepare("INSERT INTO opsIPDownloaded (levelID, IP) VALUES (:l, '$ip')");
        $q->execute([':l' => $id]);

		$q = $db->prepare("UPDATE opsLevels SET downloads = downloads + 1 WHERE levelID = :id");
		$q->execute([':id' => $id]);
	}

	function report($levelID) {
        include "settings.php";

        $q = $db->prepare("SELECT * FROM opsLevelReports WHERE levelID = :l");
        $q->execute([':l' => $levelID]);

        if ($q->rowCount() > 0)
            $q = $db->prepare("UPDATE opsLevelReports SET reportCount = reportCount + 1 WHERE levelID = :l");
        else
            $q = $db->prepare("INSERT INTO opsLevelReports (levelID) VALUES (:l)");

        $q->execute([':l' => $levelID]);
    }

    function rate($levelID, $stars, $ip) {
        include "settings.php";

        $q = $db->prepare("SELECT * FROM opsIPRated WHERE IP = :ip AND levelID = :l");
        $q->execute([':ip' => $ip, ':l' => $levelID]);

        if ($q->rowCount() > 0)
            return;

        $q = $db->prepare("INSERT INTO opsIPRated (IP, levelID) VALUES (:ip, :l)");
        $q->execute([':ip' => $ip, ':l' => $levelID]);

        $q = $db->prepare("SELECT * FROM opsLevelRates WHERE levelID = :l");
        $q->execute([':l' => $levelID]);

        if ($q->rowCount() > 0) {
            $r = $q->fetch(2);

            if ($r['totalRates'] > 49) {
                $diff = Moderation::extract_diff_from_stars($r['totalStars'] % $r['totalRates']);

                if ($diff == 50 && $r['totalStars'] % $r['totalRates'] == 1)
                    $diff = 10;

                $q = $db->prepare("UPDATE levels SET levelDifficulty = :l, isDemon = 0, isAuto = 0 WHERE levelID = :l AND stars = 0");
                $q->execute([':l' => $levelID]);

                $q = $db->prepare("UPDATE opsLevelRates SET totalRates = 0, totalStars = 0 WHERE levelID = :l");
                $q->execute([':l' => $levelID]);
            } else {
                $q = $db->prepare("UPDATE opsLevelRates SET totalRates = totalRates + 1, totalStars = totalStars + :s WHERE levelID = :l");
                $q->execute([':l' => $levelID, ':s' => $stars]);
            }
        } else {
            $q = $db->prepare("INSERT INTO opsLevelRates (levelID, totalRates, totalStars) VALUES (:l, 1, :s)");
            $q->execute([':l' => $levelID, ':s' => $stars]);
        }
    }

    function rate_new($levelID, $stars, $ip) {
        include "settings.php";

        $q = $db->prepare("SELECT * FROM opsIPRated WHERE udid = :ip AND levelID = :l");
        $q->execute([':ip' => $ip, ':l' => $levelID]);

        if ($q->rowCount() > 0)
            return;

        $q = $db->prepare("INSERT INTO opsIPRated (udid, levelID) VALUES (:ip, :l)");
        $q->execute([':ip' => $ip, ':l' => $levelID]);

        $q = $db->prepare("SELECT * FROM opsLevelRates WHERE levelID = :l");
        $q->execute([':l' => $levelID]);

        if ($q->rowCount() > 0) {
            $r = $q->fetch(2);

            if ($r['totalRates'] > 49) {
                $diff = Moderation::extract_diff_from_stars($r['totalStars'] % $r['totalRates']);

                if ($diff == 50 && $r['totalStars'] % $r['totalRates'] == 1)
                    $diff = 10;

                $q = $db->prepare("UPDATE levels SET levelDifficulty = :l, isDemon = 0, isAuto = 0 WHERE levelID = :l AND stars = 0");
                $q->execute([':l' => $levelID]);

                $q = $db->prepare("UPDATE opsLevelRates SET totalRates = 0, totalStars = 0 WHERE levelID = :l");
                $q->execute([':l' => $levelID]);
            } else {
                $q = $db->prepare("UPDATE opsLevelRates SET totalRates = totalRates + 1, totalStars = totalStars + :s WHERE levelID = :l");
                $q->execute([':l' => $levelID, ':s' => $stars]);
            }
        } else {
            $q = $db->prepare("INSERT INTO opsLevelRates (levelID, totalRates, totalStars) VALUES (:l, 1, :s)");
            $q->execute([':l' => $levelID, ':s' => $stars]);
        }
    }
}

class Moderation {
	function is_mod_or_admin($id) {
		include "settings.php";

		$q = $db->prepare("SELECT * FROM opsModerators WHERE accountID = :id");
		$q->execute([':id' => $id]);

		return $q->rowCount() > 0;
	}

	function is_mod($id) {
		include "settings.php";

		$q = $db->prepare("SELECT * FROM opsModerators WHERE accountID = :id AND modType = 1");
		$q->execute([':id' => $id]);

		return $q->rowCount() > 0;
	}

	function is_admin($id) {
		include "settings.php";

		$q = $db->prepare("SELECT * FROM opsModerators WHERE accountID = :id AND modType = 2");
		$q->execute([':id' => $id]);

		return $q->rowCount() > 0;
	}

	function extract_diff_from_stars($stars) {
		switch ($stars) {
			case '2':
				return 10;

			case '3':
				return 20;

			case '4':
                return 30;

			case '5':
				return 30;

			case '6':
                return 40;

			case '7':
				return 40;

			case '1':
                return 50;

			case '9':
                return 50;

			case '10':
				return 50;
			
			default:
				return 0;
		}
	}

	function rate($accountID, $levelID, $stars, $featured) {
		include "settings.php";

		if ($stars == 10)
			$sql = "UPDATE opsLevels SET stars = :s, isFeatured = :f, isDemon = 1, isAuto = 0, levelDifficulty = :d WHERE levelID = :l";
		else if ($stars == 1)
			$sql = "UPDATE opsLevels SET stars = :s, isFeatured = :f, isAuto = 1, isDemon = 0, levelDifficulty = :d WHERE levelID = :l";
		else
			$sql = "UPDATE opsLevels SET stars = :s, isFeatured = :f, isDemon = 0, isAuto = 0, levelDifficulty = :d WHERE levelID = :l";

		$q = $db->prepare($sql);
		$q->execute([
			':s' => $stars,
			':f' => $featured,
			':l' => $levelID,
			':d' => Moderation::extract_diff_from_stars($stars)
		]);

		adminLog(Accounts::get_by_id($accountID)['userName'], 'has just rated', $levelID);
	}

	function suggest($accountID, $levelID, $stars, $isFeatured) {
        include "settings.php";

        $q = $db->prepare("REPLACE INTO opsLevelRequests (accountID, levelID, stars, isFeatured) VALUES (:a, :l, :s, :i)");
        $q->execute([':a' => $accountID, ':l' => $levelID, ':s' => $stars, ':i' => $isFeatured]);
    }

	function rate_demon($accountID, $levelID, $r) {
		include "settings.php";

		$q = $db->prepare("UPDATE opsLevels SET demonType = :d WHERE levelID = :l");
		$q->execute([':d' => Levels::make_demon($r), ':l' => $levelID]);

        adminLog(Accounts::get_by_id($accountID)['userName'], 'has just rated demon', $levelID);
	}

    function suggest_demon($accountID, $levelID, $r) {
        include "settings.php";

        $q = $db->prepare("REPLACE INTO opsLevelRequests (accountID, levelID, stars, isFeatured, isDemon, demonType) VALUES (:a, :l, 10, 0, 1, :t)");
        $q->execute([':a' => $accountID, ':l' => $levelID, ':t' => Levels::make_demon($r)]);
    }

    function get_requests() {
        include "settings.php";

        $q = $db->prepare("SELECT * FROM opsLevelRequests");
        $q->execute();

        return $q->fetchAll();
    }
}

class Leaderboards {
	function get_top() {
		include "settings.php";

		$q = $db->prepare("SELECT * FROM opsUserScores ORDER BY stars DESC LIMIT 100");
		$q->execute();

		return $q->fetchAll();
	}

	function get_friends_top($accountID) {
		include 'settings.php';

		$q = $db->prepare("SELECT * FROM opsFriends WHERE accountID = :a");
		$q->execute([':a' => $accountID]);

		$u_f = $q->fetchAll();
		$users = array();
		$us = array();

		foreach ($u_f as $u)
		    array_push($users, Users::get_by_account($u['targetAccountID']));

		array_push($users, Users::get_by_account($accountID));

		foreach ($users as $u)
		    array_push($us, Users::get_scores($u['userID']));

		return $us;
	}

	function get_relative_top($userID) {
		include "settings.php";

		$q = $db->prepare("SELECT * FROM opsUserScores ORDER BY stars DESC");
		$q->execute();

		$e = $q->fetchAll();

		$my_id = 0;
		if (is_numeric($userID))
			$user = Users::get_by_account($userID);
		else 
			$user = Users::get_by_udid($userID);

		for ($i = 0; $i < count($e); $i++) {
			if ($e[$i]['userID'] == $user['userID']) {
				$my_id = $i;
				break;
			}
		}

		if ($my_id > 4)
			$my_id_offset = $my_id - 5;
		else
			$my_id_offset = 0;

		$sql = "SELECT * FROM opsUserScores ORDER BY stars DESC LIMIT 10 OFFSET $my_id_offset";

		$q = $db->prepare($sql);
		$q->execute();

		return $q->fetchAll();
	}

	function get_creators_top() {
		include "settings.php";

		$users = array();

		$q = $db->prepare("SELECT * FROM opsUsers");
		$q->execute();

		$uusers = $q->fetchAll();

		$q = $db->prepare("SELECT userID, stars, isFeatured, isEpic FROM opsLevels");
		$q->execute();

		$levels = $q->fetchAll();

		foreach ($uusers as $u) {
		    $e = 0;

            foreach ($levels as $l) {
                if ($l['userID'] == $u['userID'] && $l['stars'] != 0) {
                   $e++;

                   if ($l['isFeatured'] != 0)
                       $e++;

                   if ($l['isEpic'] != 0)
                       $e++;
                }
            }

            $users[$u['userID']] = $e;
        }

		arsort($users, SORT_NUMERIC);

		$rusers = array();

		foreach ($users as $key => $value)
			array_push($rusers, Users::get_scores($key));

		return $rusers;
	}

	function get_level_leaderboard($accountID, $levelID) {
        include "settings.php";

        $q = $db->prepare("SELECT * FROM opsPercentages WHERE levelID = :l ORDER BY percent DESC");
        $q->execute([':l' => $levelID]);
        $r = $q->fetchAll();

        if (count($r) == 0)
            return array();

        $f = Friends::get_friendships($accountID);

        $correct = array();

        foreach ($r as $a) {
            if ($a['accountID'] == $accountID) {
                array_push($correct, $a);
                continue;
            }

            foreach ($f as $fr) {
                if ($a['accountID'] == $fr['targetAccountID'])
                    array_push($correct, $a);
            }
        }

        return $correct;
    }

    function get_level_gleaderboard($levelID) {
        include "settings.php";

        $q = $db->prepare("SELECT * FROM opsPercentages WHERE levelID = :l ORDER BY percent DESC");
        $q->execute([':l' => $levelID]);
        $r = $q->fetchAll();

        if (count($r) == 0)
            return array();

        return $r;
    }

    function update_level_score($accountID, $levelID, $percent) {
        include "settings.php";

        $q = $db->prepare("REPLACE INTO opsPercentages (accountID, levelID, percent, updateTime) VALUES (:a, :l, :p, :t)");
        $q->execute([
            ':l' => $levelID,
            ':a' => $accountID,
            ':p' => $percent,
            ':t' => time()
            ]);
    }
}

class Likes {
    function level($like, $id, $ip) {
        include 'settings.php';

        $q = $db->prepare("SELECT * FROM opsIPLiked WHERE IP = '$ip' AND itemID = :id AND itemType = :t");
        $q->execute([':id' => $id, ':t' => 1]);

        if ($q->rowCount() > 0)
            return;

        $q = $db->prepare("INSERT INTO opsIPLiked (IP, itemID, itemType) VALUES ('$ip', :id, :t)");
        $q->execute([':id' => $id, ':t' => 1]);

        $e = '- 1';
        if ($like == "1")
            $e = '+ 1';

        $q = $db->prepare("UPDATE opsLevels SET likes = likes $e WHERE levelID = :l");
        $q->execute([':l' => $id]);
    }

    function level_new($like, $id, $udid) {
        include 'settings.php';

        $q = $db->prepare("SELECT * FROM opsIPLiked WHERE udid = :udid AND itemID = :id AND itemType = :t");
        $q->execute([':id' => $id, ':t' => 1, ':udid' => $udid]);

        if ($q->rowCount() > 0)
            return;

        $q = $db->prepare("INSERT INTO opsIPLiked (udid, itemID, itemType) VALUES (:udid, :id, :t)");
        $q->execute([':id' => $id, ':t' => 1, ':udid' => $udid]);

        $e = '- 1';
        if ($like == "1")
            $e = '+ 1';

        $q = $db->prepare("UPDATE opsLevels SET likes = likes $e WHERE levelID = :l");
        $q->execute([':l' => $id]);
    }

    function accComment($like, $id, $ip) {
        include 'settings.php';

        $q = $db->prepare("SELECT * FROM opsIPLiked WHERE IP = '$ip' AND itemID = :id AND itemType = :t");
        $q->execute([':id' => $id, ':t' => 3]);

        if ($q->rowCount() > 0)
            return;

        $q = $db->prepare("INSERT INTO opsIPLiked (IP, itemID, itemType) VALUES ('$ip', :id, :t)");
        $q->execute([':id' => $id, ':t' => 3]);

        $e = '- 1';
        if ($like == "1")
            $e = '+ 1';

        $q = $db->prepare("UPDATE opsAccountComments SET likes = likes $e WHERE commentID = :l");
        $q->execute([':l' => $id]);
    }

    function accComment_new($like, $id, $udid) {
        include 'settings.php';

        $q = $db->prepare("SELECT * FROM opsIPLiked WHERE udid = :udid AND itemID = :id AND itemType = :t");
        $q->execute([':id' => $id, ':t' => 3, ':udid' => $udid]);

        if ($q->rowCount() > 0)
            return;

        $q = $db->prepare("INSERT INTO opsIPLiked (udid, itemID, itemType) VALUES (:udid, :id, :t)");
        $q->execute([':id' => $id, ':t' => 3, ':udid' => $udid]);

        $e = '- 1';
        if ($like == "1")
            $e = '+ 1';

        $q = $db->prepare("UPDATE opsAccountComments SET likes = likes $e WHERE commentID = :l");
        $q->execute([':l' => $id]);
    }

    function comment($like, $id, $ip) {
        include 'settings.php';

        $q = $db->prepare("SELECT * FROM opsIPLiked WHERE IP = '$ip' AND itemID = :id AND itemType = :t");
        $q->execute([':id' => $id, ':t' => 2]);

        if ($q->rowCount() > 0)
            return;

        $q = $db->prepare("INSERT INTO opsIPLiked (IP, itemID, itemType) VALUES ('$ip', :id, :t)");
        $q->execute([':id' => $id, ':t' => 2]);

        $e = '- 1';
        if ($like == "1")
            $e = '+ 1';

        $q = $db->prepare("UPDATE opsComments SET likes = likes $e WHERE commentID = :l");
        $q->execute([':l' => $id]);
    }

    function comment_new($like, $id, $udid) {
        include 'settings.php';

        $q = $db->prepare("SELECT * FROM opsIPLiked WHERE udid = :udid AND itemID = :id AND itemType = :t");
        $q->execute([':id' => $id, ':t' => 2, ':udid' => $udid]);

        if ($q->rowCount() > 0)
            return;

        $q = $db->prepare("INSERT INTO opsIPLiked (udid, itemID, itemType) VALUES (:udid, :id, :t)");
        $q->execute([':id' => $id, ':t' => 2, ':udid' => $udid]);

        $e = '- 1';
        if ($like == "1")
            $e = '+ 1';

        $q = $db->prepare("UPDATE opsComments SET likes = likes $e WHERE commentID = :l");
        $q->execute([':l' => $id]);
    }
}

class IPLimits {
    function limit_likes($IP) {
        include 'settings.php';

        $limits = $OPS_SETTINGS['gdps']['limits'];

        $q = $db->prepare("SELECT * FROM opsIPLimits WHERE IP = :ip");
        $q->execute([':ip' => $IP]);

        if ($q->rowCount() > 0) {
            $r = $q->fetch(2);

            if ($r['likes'] >= $limits['likes']) {
                if (time() - $r['lr'] > 43200) {
                    $q = $db->prepare("UPDATE opsIPLimits SET likes = 0, lr = :t WHERE IP = :ip");
                    $q->execute([':t' => time(), ':ip' => $IP]);

                    return true;
                } else
                    return false;
            } else {
                $q = $db->prepare("UPDATE opsIPLimits SET likes = likes + 1 WHERE IP = :ip");
                $q->execute([':ip' => $IP]);

                return true;
            }
        } else {
            $q = $db->prepare("INSERT INTO opsIPLimits (likes, lr, IP) VALUES (1, :t, :ip)");
            $q->execute([':t' => time(), ':ip' => $IP]);

            return true;
        }
    }

    function limit_likes_new($udid, $accountID, $uuid) {
        include 'settings.php';

        $limits = $OPS_SETTINGS['gdps']['limits'];

        $q = $db->prepare("SELECT * FROM opsIPLimits WHERE udid = :udid");
        $q->execute([':udid' => $udid]);

        if ($q->rowCount() > 0) {
            $r = $q->fetch(2);

            if ($r['userID'] != $uuid) {
                if ($r['accountID'] != $accountID)
                    return false;
            }

            if ($r['likes'] >= $limits['likes']) {
                if (time() - $r['lr'] > 43200) {
                    $q = $db->prepare("UPDATE opsIPLimits SET likes = 0, lr = :t WHERE udid = :udid");
                    $q->execute([':t' => time(), ':udid' => $udid]);

                    return true;
                } else
                    return false;
            } else {
                $q = $db->prepare("UPDATE opsIPLimits SET likes = likes + 1 WHERE udid = :udid");
                $q->execute([':udid' => $udid]);

                return true;
            }
        } else {
            $q = $db->prepare("INSERT INTO opsIPLimits (likes, lr, udid, userID, accountID) VALUES (1, :t, :udid, :uuid, :accountID)");
            $q->execute([':t' => time(), ':udid' => $udid, ':uuid' => $uuid, ':accountID' => $accountID]);

            return true;
        }
    }

    function limit_comments($IP) {
        include 'settings.php';

        $limits = $OPS_SETTINGS['gdps']['limits'];

        $q = $db->prepare("SELECT * FROM opsIPLimits WHERE IP = :ip");
        $q->execute([':ip' => $IP]);

        if ($q->rowCount() > 0) {
            $r = $q->fetch(2);

            if ($r['comments'] >= $limits['comments']) {
                if (time() - $r['cr'] > 43200) {
                    $q = $db->prepare("UPDATE opsIPLimits SET comments = 0, cr = :t WHERE IP = :ip");
                    $q->execute([':t' => time(), ':ip' => $IP]);

                    return true;
                } else
                    return false;
            } else {
                $q = $db->prepare("UPDATE opsIPLimits SET comments = comments + 1 WHERE IP = :ip");
                $q->execute([':ip' => $IP]);

                return true;
            }
        } else {
            $q = $db->prepare("INSERT INTO opsIPLimits (comments, cr, IP) VALUES (1, :t, :ip)");
            $q->execute([':t' => time(), ':ip' => $IP]);

            return true;
        }
    }

    function limit_accComments($IP) {
        include 'settings.php';

        $limits = $OPS_SETTINGS['gdps']['limits'];

        $q = $db->prepare("SELECT * FROM opsIPLimits WHERE IP = :ip");
        $q->execute([':ip' => $IP]);

        if ($q->rowCount() > 0) {
            $r = $q->fetch(2);

            if ($r['accountComments'] >= $limits['accComments']) {
                if (time() - $r['ar'] > 43200) {
                    $q = $db->prepare("UPDATE opsIPLimits SET accountComments = 0, ar = :t WHERE IP = :ip");
                    $q->execute([':t' => time(), ':ip' => $IP]);

                    return true;
                } else
                    return false;
            } else {
                $q = $db->prepare("UPDATE opsIPLimits SET accountComments = accountComments + 1 WHERE IP = :ip");
                $q->execute([':ip' => $IP]);

                return true;
            }
        } else {
            $q = $db->prepare("INSERT INTO opsIPLimits (accountComments, ar, IP) VALUES (1, :t, :ip)");
            $q->execute([':t' => time(), ':ip' => $IP]);

            return true;
        }
    }

    function limit_messages($IP) {
        include 'settings.php';

        $limits = $OPS_SETTINGS['gdps']['limits'];

        $q = $db->prepare("SELECT * FROM opsIPLimits WHERE IP = :ip");
        $q->execute([':ip' => $IP]);

        if ($q->rowCount() > 0) {
            $r = $q->fetch(2);

            if ($r['messages'] >= $limits['messages']) {
                if (time() - $r['mr'] > 43200) {
                    $q = $db->prepare("UPDATE opsIPLimits SET messages = 0, mr = :t WHERE IP = :ip");
                    $q->execute([':t' => time(), ':ip' => $IP]);

                    return true;
                } else
                    return false;
            } else {
                $q = $db->prepare("UPDATE opsIPLimits SET messages = messages + 1 WHERE IP = :ip");
                $q->execute([':ip' => $IP]);

                return true;
            }
        } else {
            $q = $db->prepare("INSERT INTO opsIPLimits (messages, mr, IP) VALUES (1, :t, :ip)");
            $q->execute([':t' => time(), ':ip' => $IP]);

            return true;
        }
    }

    function limit_levels($IP) {
        include 'settings.php';

        $limits = $OPS_SETTINGS['gdps']['limits'];

        $q = $db->prepare("SELECT * FROM opsIPLimits WHERE IP = :ip");
        $q->execute([':ip' => $IP]);

        if ($q->rowCount() > 0) {
            $r = $q->fetch(2);

            if ($r['levels'] >= $limits['levels']) {
                if (time() - $r['ler'] > 43200) {
                    $q = $db->prepare("UPDATE opsIPLimits SET levels = 0, mr = :t WHERE IP = :ip");
                    $q->execute([':t' => time(), ':ip' => $IP]);

                    return true;
                } else
                    return false;
            } else {
                $q = $db->prepare("UPDATE opsIPLimits SET levels = levels + 1 WHERE IP = :ip");
                $q->execute([':ip' => $IP]);

                return true;
            }
        } else {
            $q = $db->prepare("INSERT INTO opsIPLimits (levels, ler, IP) VALUES (1, :t, :ip)");
            $q->execute([':t' => time(), ':ip' => $IP]);

            return true;
        }
    }

    function limit_rates($IP) {
        include 'settings.php';

        $limits = $OPS_SETTINGS['gdps']['limits'];

        $q = $db->prepare("SELECT * FROM opsIPLimits WHERE IP = :ip");
        $q->execute([':ip' => $IP]);

        if ($q->rowCount() > 0) {
            $r = $q->fetch(2);

            if ($r['rates'] >= $limits['rates']) {
                if (time() - $r['rr'] > 43200) {
                    $q = $db->prepare("UPDATE opsIPLimits SET rates = 0, rr = :t WHERE IP = :ip");
                    $q->execute([':t' => time(), ':ip' => $IP]);

                    return true;
                } else
                    return false;
            } else {
                $q = $db->prepare("UPDATE opsIPLimits SET rates = rates + 1 WHERE IP = :ip");
                $q->execute([':ip' => $IP]);

                return true;
            }
        } else {
            $q = $db->prepare("INSERT INTO opsIPLimits (rates, rr, IP) VALUES (1, :t, :ip)");
            $q->execute([':t' => time(), ':ip' => $IP]);

            return true;
        }
    }

    function limit_rates_new($udid, $accountID, $uuid) {
        include 'settings.php';

        $limits = $OPS_SETTINGS['gdps']['limits'];

        $q = $db->prepare("SELECT * FROM opsIPLimits WHERE udid = :ip");
        $q->execute([':ip' => $udid]);

        if ($q->rowCount() > 0) {
            $r = $q->fetch(2);

            if ($r['userID'] != $uuid) {
                if ($r['accountID'] != $accountID)
                    return false;
            }

            if ($r['rates'] >= $limits['rates']) {
                if (time() - $r['rr'] > 43200) {
                    $q = $db->prepare("UPDATE opsIPLimits SET rates = 0, rr = :t WHERE udid = :ip");
                    $q->execute([':t' => time(), ':ip' => $udid]);

                    return true;
                } else
                    return false;
            } else {
                $q = $db->prepare("UPDATE opsIPLimits SET rates = rates + 1 WHERE udid = :ip");
                $q->execute([':ip' => $udid]);

                return true;
            }
        } else {
            $q = $db->prepare("INSERT INTO opsIPLimits (rates, rr, udid) VALUES (1, :t, :ip)");
            $q->execute([':t' => time(), ':ip' => $udid]);

            return true;
        }
    }
}

class Comments {
    function get_in_level($levelID, $mode) {
        include 'settings.php';

        $q = $db->prepare("SELECT * FROM opsComments WHERE levelID = :l ORDER BY $mode DESC");
        $q->execute([':l' => $levelID]);

        return $q->fetchAll();
    }

    function get_by_user($userID, $mode) {
        include 'settings.php';

        $q = $db->prepare("SELECT * FROM opsComments WHERE userID = :u ORDER BY $mode DESC");
        $q->execute([':u' => $userID]);

        return $q->fetchAll();
    }

    function upload($comment, $accountID, $levelID, $percent)
    {
        include 'settings.php';

        $q = $db->prepare("INSERT INTO opsComments (userID, levelID, percent, comment, uploadTime) VALUES (:u, :l, :p, :c, :up)");
        $q->execute([
            ':u' => Users::get_by_account($accountID)['userID'],
            ':l' => $levelID,
            ':p' => $percent != "" ? $percent : 0,
            ':c' => $comment,
            ':up' => time()
        ]);
    }

    function is_banned($accountID) {
        include 'settings.php';

        $q = $db->prepare("SELECT * FROM opsCommentBans WHERE accountID = :a LIMIT 1");
        $q->execute([':a' => $accountID]);

        return $q->rowCount() > 0;
    }

    function get_ban_reason($accountID) {
        include 'settings.php';

        $q = $db->prepare("SELECT * FROM opsCommentBans WHERE accountID = :a LIMIT 1");
        $q->execute([':a' => $accountID]);

        $r = $q->fetch(2);

        if ($r['reason'] == '')
            return '';
        else
            return '_' . $r['reason'];
    }

    function delete($commentID, $accountID)
    {
        include 'settings.php';

        if (Moderation::is_admin($accountID)) {
            $q = $db->prepare("DELETE FROM opsComments WHERE commentID = :a");
            $q->execute([':a' => $commentID]);
        } else {
            $q = $db->prepare("DELETE FROM opsComments WHERE commentID = :a AND userID = :u");
            $q->execute([':a' => $commentID, ':u' => Users::get_by_account($accountID)['userID']]);
        }
    }
}

class MapPacks {
    function get() {
        include 'settings.php';

        $q = $db->prepare("SELECT * FROM opsMapPacks");
        $q->execute();

        return $q->fetchAll();
    }

    function generate_hash($lvlsmultistring) {
        $lvlsarray = explode(",", $lvlsmultistring);
        include "settings.php";
        $hash = "";
        foreach($lvlsarray as $id){
            $query=$db->prepare("SELECT * FROM opsMapPacks WHERE packID = :i");
            $query->execute(array('i' => $id));
            $result2 = $query->fetchAll();
            $result = $result2[0];
            $hash = $hash . $result["packID"][0].$result["packID"][strlen($result["packID"])-1].$result["packStars"].$result["packCoins"];
        }
        return sha1($hash . "xI25fpAapCQg");
    }
}

class UserRewards {
    function load_quests($udid, $chk, $accountID) {
        include 'settings.php';

        $quests = $OPS_SETTINGS['quests'];

        $chk = xorchar(base64_decode(substr($chk, 5)), 19847);

        if ($accountID == '0')
            $user = Users::get_by_udid($udid);
        else
            $user = Users::get_by_account($accountID);

        $q = $db->prepare("SELECT * FROM opsUserRewards WHERE userID = :u AND type = 'quest'");
        $q->execute([':u' => $user['userID']]);

        $timeLeft = 0;

        if ($q->rowCount() == 0) {
            $q = $db->prepare("INSERT INTO opsUserRewards (userID, getTime, type) VALUES (:u, :t, 'quest')");
            $q->execute([':u' => $user['userID'], ':t' => time() + 21600]);
        } else {
            $r = $q->fetch(2);

            if (time() - $r['getTime'] > 21599) {
                $q = $db->prepare("UPDATE opsUserRewards SET getTime = :t WHERE userID = :u AND type = 'quest'");
                $q->execute([':u' => $user['userID'], ':t' => time() + 21600]);
            } else
                $timeLeft = $r['getTime'] - time();
        }

        $rnd = UserRewards::gen_random_id();

        $tmp = array(
            'AAAAA',
            $user['userID'],
            $chk,
            $udid,
            $accountID,
            $timeLeft,
            implode(',', array(
                $rnd,
                $quests[0]['position'],
                $quests[0]['needed'][random_int(0, count($quests[0]['needed']) - 1)],
                $quests[0]['reward'],
                $quests[0]['name'][random_int(0, count($quests[0]['name']) - 1)]
            )),
            implode(',', array(
                $rnd + 1,
                $quests[1]['position'],
                $quests[1]['needed'][random_int(0, count($quests[1]['needed']) - 1)],
                $quests[1]['reward'],
                $quests[1]['name'][random_int(0, count($quests[1]['name']) - 1)]
            )),
            implode(',', array(
                $rnd + 2,
                $quests[2]['position'],
                $quests[2]['needed'][random_int(0, count($quests[2]['needed']) - 1)],
                $quests[2]['reward'],
                $quests[2]['name'][random_int(0, count($quests[2]['name']) - 1)]
            ))
        );

        return implode(':', $tmp);
    }

    function load_chests($accountID, $udid, $chk, $rewardType) {
        include 'settings.php';

        $small = $OPS_SETTINGS['chests']['small'];
        $big = $OPS_SETTINGS['chests']['big'];

        $chk = xorchar(base64_decode(substr($chk, 5)), 59182);

        if ($accountID != '0')
            $user = Users::get_by_account($accountID);
        else
            $user = Users::get_by_udid($udid);

        $co = 0;
        $bo = 0;
        $ct = 0;
        $bt = 0;

        $q = $db->prepare("SELECT * FROM opsUserChests WHERE userID = :u");
        $q->execute([':u' => $user['userID']]);

        if ($q->rowCount() > 0) {
            $r = $q->fetch(2);

            $ct = time() - $r['smallChestTime'];
            $bt = time() - $r['bigChestTime'];

            if ($ct >= 3600)
                $ct = 0;
            else
                $ct = 3600 - $ct;

            if ($bt >= 12800)
                $bt = 0;
            else
                $bt = 12800 - $bt;

            $co = $r['smallChestOpened'];
            $bo = $r['bigChestOpened'];
        } else {
            $q = $db->prepare("INSERT INTO opsUserChests (userID) VALUES (:u)");
            $q->execute([':u' => $user['userID']]);
        }

        if ($rewardType == 1) {
            $q = $db->prepare("UPDATE opsUserChests SET smallChestOpened = :s, smallChestTime = :t WHERE userID = :u");
            $q->execute([':s' => $co + 1, ':t' => time(), ':u' => $user['userID']]);
            $co += 1;
            $ct = 3600;
        } else if ($rewardType == 2) {
            $q = $db->prepare("UPDATE opsUserChests SET bigChestOpened = :s, bigChestTime = :t WHERE userID = :u");
            $q->execute([':s' => $bo + 1, ':t' => time(), ':u' => $user['userID']]);
            $bo += 1;
            $bt = 12800;
        }

        $tmp = array(
            'AAAAA',
            $user['userID'],
            $chk,
            $udid,
            $accountID,
            $ct,
            implode(',', array(
                random_int($small['min']['orbs'], $small['max']['orbs']),
                random_int($small['min']['diamonds'], $small['max']['diamonds']),
                random_int($small['min']['shards'], $small['max']['shards']),
                random_int($small['min']['other'], $small['max']['other'])
            )),
            $co,
            $bt,
            implode(',', array(
                random_int($big['min']['orbs'], $big['max']['orbs']),
                random_int($big['min']['diamonds'], $big['max']['diamonds']),
                random_int($big['min']['shards'], $big['max']['shards']),
                random_int($big['min']['other'], $big['max']['other'])
            )),
            $bo,
            $rewardType
        );

        return implode(':', $tmp);
    }

    function gen_random_id() {
        return floor(time() / rand(0, 1337)) + 420;
    }
}
