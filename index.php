<?php
require 'config.php';
include 'sanitychecks.php';

// Initialize the session
include 'session.php';

// Check if the user is logged in
include 'check_user.php';
include 'smarty_init.php';

include 'class.db.php';

$tpl->assign('isadmin', pwsdbh($dbDSN)->isAdmin($_SESSION['loginuser']));
$tpl->assign('passwordgroups', pwsdbh($dbDSN)->userPasswordgroups($_SESSION['loginuser']));

$tpl->display('search.tpl');
