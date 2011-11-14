<?php
$set = $_GET['set'];
include_once 'hashGenerator.php';
include_once 'KLogger/src/KLogger.php';
if ($set == "default") {
  $hash = "default";
} else {
  $hash = HashGenerator::udihash($set);
}
$log = KLogger::instance($hash, KLogger::OFF);
$success = $log->deleteLog();
echo $success;
?>
