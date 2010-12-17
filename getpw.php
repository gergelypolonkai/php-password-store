<?php
require 'config.php';
include 'sanitychecks.php';
include 'class.db.php';

// Initialize the session
include 'session.php';

// Check if the user is logged in
include 'check_user.php';

if (!pwsdbh($dbDSN)->passwordAccessible($_POST['id'], $_SESSION['loginuser']))
{
	exit;
}

pwsdbh($dbDSN)->setKey(file_get_contents($masterKey));
$pwRecord = pwsdbh($dbDSN)->getPasswordData($_POST['id']);

header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n";
?>
<pws-results>
	<result>
		<id><?php echo $pwRecord['id'] ?></id>
		<short><![CDATA[<?php echo $pwRecord['short'] ?>]]></short>
		<long><![CDATA[<?php echo $pwRecord['long'] ?>]]></long>
		<username><![CDATA[<?php echo $pwRecord['username'] ?>]]></username>
		<password><![CDATA[<?php echo $pwRecord['password'] ?>]]></password>
		<additional><![CDATA[<?php echo $pwRecord['additional'] ?>]]></additional>
	</result>
</pws-results>
