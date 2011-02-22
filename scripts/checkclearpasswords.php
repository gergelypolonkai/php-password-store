<?php
// Check and update clear text password in the database


// OPTIONS
$webDir = '/var/www/localhost/htdocs/passwordstore';

require_once $webDir . '/config.php';
require_once $webDir . '/class.db.php';

pwsdbh($dbDSN)->setKey(file_get_contents($masterKey));

$passwords = pwsdbh($dbDSN)->getClearPasswords();

foreach ($passwords as $rec)
{
	echo "Updating password with ID " . $rec['id'] . "\n";
	$rec['password'] = $rec[1] = substr($rec['password'], 7);
	pwsdbh($dbDSN)->updatePassword($rec['id'], $rec['password']);
}

