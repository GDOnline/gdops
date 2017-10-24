<?php
$songid = $_POST['songID'];

$xml = "songID=".$songid."&secret=Wmfd2893gb7";
$url = 'http://www.boomlings.com/database/getGJSongInfo.php';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
$response = curl_exec($ch);
curl_close($ch);
echo $response;