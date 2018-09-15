<?php
require_once(__DIR__."/bootstrap-core.php");

$user = include_once(__DIR__."/models/user.php");
$session = include_once(__DIR__."/models/session.php");
$cookie_manager = include(__DIR__."/lib/cookiemanager.class.php");

$session->linkCookieManager($cookie_manager);
$user->linkDB($db);
$user->linkSession($session);
?>