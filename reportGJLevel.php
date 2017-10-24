<?php
include 'settings.php';
require_once 'libops.php';

Levels::report(unparty($_POST['levelID']));
die('1');