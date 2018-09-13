<?php
$config = require(__DIR__."/include/config.php");
require_once(__DIR__."/include/functions.php");
$db = include_once(__DIR__."/include/use_db.php");

$user = include_once(__DIR__."/models/user.php");
$session = include_once(__DIR__."/models/session.php");
$cookie_manager = include(__DIR__."/lib/cookiemanager.class.php");

$session->linkCookieManager($cookie_manager);
$user->linkDB($db);
$user->linkSession($session);
?>