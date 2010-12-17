<?php
require 'config.php';
include 'sanitychecks.php';
include 'class.db.php';

header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n";
?>
<pws-results>
	<results>
<?php
foreach (pwsdbh($dbDSN)->getAllPasswords($_POST['group']) as $pwRec):
?>
		<row>
			<id><?php echo $pwRec['id'] ?></id>
			<short><![CDATA[<?php echo $pwRec['short'] ?>]]></short>
		</row>
<?php
endforeach;
?>
	</results>
</pws-results>
