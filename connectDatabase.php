<?php

function getDatabaseConnection() {
  global $databaseDomain;
  global $databaseUsername;
  global $databasePassword;
  global $databaseName;
  $link = @mysql_connect($databaseDomain,$databaseUsername,$databasePassword) or die(json_encode(array('errorCode' => 100, 'errorString' => 'MySQL connection failed: ' . mysql_error())));
  @mysql_select_db($databaseName) or die(json_encode(array('errorCode' => 100, 'errorString' => 'Could not select database')));
  return $link;
}


?>
