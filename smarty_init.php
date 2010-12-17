<?php
include $smartyInstallDir . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'Smarty.class.php';

$tpl = new Smarty();
$tpl->template_dir = $smartyTemplateDir;
$tpl->compile_dir = $smartyCache;

if (array_key_exists('loginuser', $_SESSION))
{
	$tpl->assign('username', $_SESSION['loginuser']);
}
