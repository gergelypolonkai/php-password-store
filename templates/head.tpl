<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>Password Store - {$title}</title>
{if ($needjquery == 1)}
		<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
		<script type="text/javascript" src="js/jquery.tooltip.min.js"></script>

		<script type="text/javascript" src="js/search.js"></script>
		<script type="text/javascript" src="js/pws.js"></script>

		<link rel="stylesheet" type="text/css" href="style/tooltip.css" media="screen" />
{/if}
{if $login}
		<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
		<script type="text/javascript" src="js/login.js"></script>
{/if}
		<link rel="stylesheet" type="text/css" href="style/pws.css" media="screen" />
	</head>
	<body>
		<div id="flashbox"></div>
		<h1>Password Store - {$title}</h1>
{if !$login}
		<p style="text-align: right;">Logged in as{if $isadmin} administrator{/if} <span id="username">[{$username}]</span>. [<a href="index.php">Search</a>] [<a href="cpw.php">Change password</a>] [<a href="logout.php">Logout</a>]</p>
{/if}
