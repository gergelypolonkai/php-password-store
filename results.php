<?php
require 'config.php';
include 'sanitychecks.php';
include 'class.db.php';

// Initialize the session
include 'session.php';

// Check if the user is logged in
include 'check_user.php';

$startTime = microtime(true);
$allowedPasswords = array();

foreach (pwsdbh($dbDSN)->findPasswords($_POST['querytext']) as $passwordRow)
{
	if (pwsdbh($dbDSN)->passwordAccessible($passwordRow['id'], $_SESSION['loginuser']) && !array_key_exists($passwordRow['id'], $allowedPasswords))
	{
		$allowedPasswords[$passwordRow['id']] = $passwordRow;
	}
}

$endTime = microtime(true);

header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n";
?>
<pws-results>
	<query><![CDATA[<?php echo htmlspecialchars($_POST['querytext']) ?>]]></query>
	<elapsed-time><?php printf("%.2f", $endTime - $startTime) ?></elapsed-time>
	<results>
<?php
	foreach ($allowedPasswords as $pwRecord):
?>
		<row>
			<id><?php echo $pwRecord['id'] ?></id>
			<short><![CDATA[<?php echo $pwRecord['short'] ?>]]></short>
		</row>
<?php
	endforeach;
?>
	</results>
</pws-results>
