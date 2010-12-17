<?php
require 'config.php';
include 'session.php';

session_destroy();
header('Location: login.php');
echo 'Logout successful. If you are not redirected back to the login page, <a href="login.php">click here</a>.';
exit;
