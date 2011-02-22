<?php
// Check for inactive users


// OPTIONS
$webDir = '/var/www/localhost/htdocs/passwordstore';

require_once $webDir . '/config.php';
require_once $webDir . '/class.db.php';

$passwords = pwsdbh($dbDSN)->getInactivePasswords($passwordInactiveTime);

foreach ($passwords as $password)
{
	if ($password['username'] == '')
	{
		printf("The password for %s (%s) was not accessed since %s\n", $password['short'], $password['long'], $password['lastaccess']);
	}
	else
	{
		printf("The password for %s on %s (%s) was not accessed since %s\n", $password['username'], $password['short'], $password['long'], $password['lastaccess']);
	}
}

