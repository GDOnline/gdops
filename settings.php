<?php
/*

Geometry Dash Online Private Server

Developed by: Ruppet
Tested by: EndmaN, Zimnior12, Pooshnya, BoyOfTheCones, Monstahhh

Game version: 2.1

*/

$OPS_SETTINGS = array(
	'gdps' => array (
		'name' => 'Geometry Dash Online Private Server',
		'slug' => 'gdops',
		'registration' => array(
			'enable_email_verify' => false,
			'activation_salt' => '',
			'hash_algo' => 'sha1'
		),
		'security' => array(
			'salt' => '',
			'anubis_salt' => '',
			'pass_algo' => 'sha256'
		),
        'limits' => array(
            'messages' => 9999,
            'likes' => 9999,
            'comments' => 9999,
            'accComments' => 9999,
            'levels' => 9999,
            'rates' => 9999
        )
	),
	'anticheat' => array(
		'enable' => true,
		'allow_score_jumps' => false
	),
	'gzip' => array(
		'store_in_files' => true
	),
	'database' => array(
		'server' => 'localhost',
		'username' => 'root',
		'password' => '',
		'dbname' => ''
	),
    'quests' => array(
        array(
            'name' => array(
                'The Magic Perfection',
                'Give me the mana!',
                'It\'s time to visit the shop!'
            ),
            'position' => 1,
            'needed' => array(
                1000,
                500,
                5000,
                1500
            ),
            'reward' => 10
        ),
        array(
            'name' => array(
                'Coin catcher',
                'Find it under a rock',
                'Get \'em'
            ),
            'position' => 2,
            'needed' => array(
                3,
                6,
                9
            ),
            'reward' => 15
        ),
        array(
            'name' => array(
                'Star factory',
                'Like a sun',
                'HOT HOT HOT!',
                'So shiny!'
            ),
            'position' => 3,
            'needed' => array(
                10,
                50,
                100
            ),
            'reward' => 20
        )
    ),
    'chests' => array(
        'small' => array(
            'min' => array(
                'orbs' => 0,
                'diamonds' => 0,
                'shards' => 0,
                'other' => 0
            ),
            'max' => array(
                'orbs' => 500,
                'diamonds' => 1,
                'shards' => 1,
                'other' => 0
            )
        ),
        'big' => array(
            'min' => array(
                'orbs' => 500,
                'diamonds' => 0,
                'shards' => 1,
                'other' => 0
            ),
            'max' => array(
                'orbs' => 2000,
                'diamonds' => 2,
                'shards' => 3,
                'other' => 1
            )
        )
    )
);

# Database

try {
	$_dbs = $OPS_SETTINGS['database'];
	$db = new PDO('mysql:dbname=' . $_dbs['dbname'] . ';host=' . $_dbs['server'],
		$_dbs['username'],
		$_dbs['password']);

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
	$errpage = <<<NOWDOC
<html>
	<head>
		<meta charset="utf-8">
		<title>Fatal error!</title>
	</head>
	<body>
		<h1>An unrecoverable error occured</h1>
		<p>Stack trace:</p>
		<code>
			{$ex->getMessage()}
		</code>
	</body>
</html>
NOWDOC;

	echo $errpage;
}