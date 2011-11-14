<?php

require_once 'KLogger/src/KLogger.php';
require_once '../configuration/config.php';
date_default_timezone_set("UTC");
$_log = new KLogger('..' . DIRECTORY_SEPARATOR . 'logs','default', KLogger::INFO);

function logInfo($line) {
  global $_log;
  $_log -> logInfo($line);
}
function logDebug($line) {
  global $_log;
  $_log -> logDebug($line);
}
function logNotice($line) {
  global $_log;
  $_log -> logNotice($line);
}
function logWarn($line) {
  global $_log;
  $_log -> logWarn($line);
}
function logError($line) {
  global $_log;
  $_log -> logError($line);
}
function logFatal($line) {
  global $_log;
  $_log -> logFatal($line);
}

function eLogInfo($line,$log) {
  $log -> logInfo($line);
}
function eLogDebug($line,$log) {
  $log -> logDebug($line);
}
function eLogNotice($line,$log) {
  $log -> logNotice($line);
}
function eLogWarn($line,$log) {
  $log -> logWarn($line);
}
function eLogError($line,$log) {
  $log -> logError($line);
}
function eLogFatal($line,$log) {
  $log -> logFatal($line);
}
?>
