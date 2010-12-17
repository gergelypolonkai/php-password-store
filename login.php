<?php
require 'config.php';
include 'sanitychecks.php';
include 'session.php';
include 'class.db.php';

$loginerror = false;
$valid = false;

if (
	array_key_exists('username', $_POST)
	&& array_key_exists('password', $_POST)
	&& !(
		array_key_exists('loginuser', $_SESSION)
		&& pwsdbh($dbDSN)->userExists($_SESSION['loginuser'])
	)
)
{
	try
	{
		if (($valid = pwsdbh($dbDSN)->authUser($_POST['username'], $_POST['password'])) === true)
		{
			$_SESSION['loginuser'] = $_POST['username'];
		}
	}
	catch (PWSdbhException $e)
	{
		$loginerror = $e->getMessage();
	}
}

if ($valid === true)
{
	$params = (array_key_exists('redirect', $_POST) && ($_POST['redirect'] != '')) ? '?' . $_POST['redirect'] : '';

	header('Location: index.php' . $params);
	echo 'Login successful. If you are not redirected, <a href="index.php' . $params . '">click here</a>.';
	exit;
}

include 'smarty_init.php';

if (array_key_exists('redir', $_GET))
{
	$tpl->assign('redirect', $_GET['redir']);
}
elseif (array_key_exists('redirect', $_POST))
{
	$tpl->assign('redirect', $_POST['redirect']);
}
else
{
	$tpl->assign('redirect', '');
}

if (array_key_exists('username', $_POST))
{
	$tpl->assign('username', $_POST['username']);
}

$tpl->assign('loginerror', $loginerror);
$tpl->display('login.tpl');
