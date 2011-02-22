<?php
// Check for inactive users


// OPTIONS
$webDir = '/var/www/localhost/htdocs/passwordstore';

require_once $webDir . '/config.php';
require_once $webDir . '/class.db.php';

$users = pwsdbh($dbDSN)->getInactiveUsers($userInactiveTime);

foreach ($users as $user)
{
	printf("User %s is inactive since %s%s\n", $user['username'], $user['lastlogin'], ($user['administrator'] == 1) ? ' (this user is an administrator)' : '');
}

