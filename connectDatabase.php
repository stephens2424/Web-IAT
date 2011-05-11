<?php

$databaseDomain = 'localhost';
$databasePort = '/tmp/mysql.sock';
$databaseUsername = 'root';
$databasePassword = 'tempest24';
$databaseName = 'testIAT';

function getDatabaseConnection() {
  $link = mysql_connect($databaseDomain . ':' . $databasePort,$databaseUsername,$databasePassword) or die('Could not connect: ' . mysql_error());
  mysql_select_db($databaseName) or die('Could not select database');
}


?>
