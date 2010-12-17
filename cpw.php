<?php
require 'config.php';
include 'sanitychecks.php';
include 'session.php';
include 'check_user.php';
include 'smarty_init.php';
include 'class.db.php';

$error = 0;

if (array_key_exists('oldpw', $_POST) && array_key_exists('newpw1', $_POST) && array_key_exists('newpw2', $_POST))
{
	$error = '';

	try {
		pwsdbh($dbDSN)->changePassword($_SESSION['loginuser'], $_POST['oldpw'], $_POST['newpw1'], $_POST['newpw2']);
		$error = 0;
	}
	catch (PDOException $e)
	{
		$error = 255;
	}
	catch (PWSdbhException $e)
	{
		$error = $e->getCode();
	}
	catch (Exception $e)
	{
		$error = 254;
	}
}

$tpl->assign('errno', $error);
$tpl->assign('isadmin', pwsdbh($dbDSN)->isAdmin($_SESSION['loginuser']));
$tpl->display('cpw.tpl');

