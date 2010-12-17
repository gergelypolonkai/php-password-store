<?php
require 'config.php';
include 'sanitychecks.php';
include 'class.db.php';

// Initialize the session
include 'session.php';

// Check if the user is logged in
include 'check_user.php';

if (!pwsdbh($dbDSN)->passwordgroupAccessible($_POST['name'], $_SESSION['loginuser']))
{
	exit;
}

$groupData = pwsdbh($dbDSN)->getPasswordgroupData($_POST['name']);

if ($groupData === null)
{
	exit;
}

	header('Content-Type: text/xml; charset=utf-8');
	echo '<?xml version="1.0" encoding="utf-8"?>', "\n";
?>
<pws-results>
	<result>
		<id><![CDATA[<?php echo $groupData['id'] ?>]]></id>
		<name><![CDATA[<?php echo $groupData['groupname'] ?>]]></name>
		<description><![CDATA[<?php echo $groupData['description'] ?>]]></description>
	</result>
</pws-results>
