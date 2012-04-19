<?php
error_reporting(E_ALL); 
ini_set("display_errors", 1);

DEFINE('PINGDOM_USR', 'user@domain.com');
DEFINE('PINGDOM_PWD', 'yoursecurepassword');

require_once('../lib/Pingdom/API.php');
?>