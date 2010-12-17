<?php
header('Pragma: no-cache');
header('Cache-Control: no-cache');
header('Content-Type: text/html; charset=utf-8');

if (!isset($sessionName) || ($sessionName == ''))
{
	echo "No session name is set in the configuration file!";
	exit;
}

if (array_key_exists($sessionName, $_COOKIE))
{
	session_id($_COOKIE[$sessionName]);
}

$path = dirname($_SERVER['PHP_SELF']) . '/';

session_name($sessionName);
session_cache_expire(15);
session_set_cookie_params(900, $path, $_SERVER['SERVER_NAME'], $sslOnly, TRUE);
session_start();
setcookie($sessionName, session_id());
