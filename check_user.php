<?php
require 'config.php';
include_once 'class.db.php';

if (!array_key_exists('loginuser', $_SESSION) || !pwsdbh($dbDSN)->userExists($_SESSION['loginuser']))
{
	header('Location: login.php');
	exit;
}
