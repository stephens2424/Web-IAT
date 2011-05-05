<?php

require_once 'KLogger/src/KLogger.php';
date_default_timezone_set("UTC");
$log = new KLogger("log", KLogger::INFO);

function logInfo($line) {
  global $log;
  $log -> logInfo($line);
}

function logDebug($line) {
  global $log;
  $log -> logDebug($line);
}
function logNotice($line) {
  global $log;
  $log -> logNotice($line);
}
function logWarn($line) {
  global $log;
  $log -> logWarn($line);
}
function logError($line) {
  global $log;
  $log -> logError($line);
}
function logFatal($line) {
  global $log;
  $log -> logFatal($line);
}

?>
