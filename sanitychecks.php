<?php
require_once 'config.php';

if (!in_array($encryptionAlg, mcrypt_list_algorithms()))
{
	echo "Cipher set in config.php is not supported by your mcrypt installation.";
	exit;
}

if (!in_array('ecb', mcrypt_list_modes()))
{
	echo "ECB mode is not supported by your mcrypt installation.";
	exit;
}

// Check if the key is under the server's DOCUMENT_ROOT. If so, we won't allow
// to use it.
if (!$keyInDocroot && (substr($masterKey, 0, strlen($_SERVER['DOCUMENT_ROOT'])) == $_SERVER['DOCUMENT_ROOT']))
{
	echo "Your key may be compromised, as it is downloadable!";
	exit;
}

// Check if the key can be read by anyone other than the web server user.
// However, this is still not secure enough in a multi-hosting environment!
$perm = fileperms($masterKey);
if (($perm & 0x20) || ($perm & 0x2))
{
	echo "Your key may be compromised as its file permissions are not strict enough!";
	exit;
}

// Check if the key itself is readable be us.
if (!is_readable($masterKey))
{
	echo "The master key is not readable by the server.";
	exit;
}
