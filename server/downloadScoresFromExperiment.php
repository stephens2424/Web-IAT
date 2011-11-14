<?php
include 'CSVMaker.php';
include 'connect.php';
$set = $_GET['set'];
$query = "SELECT DISTINCT subjects.id,subjects.qualtrics_id,subjects.score FROM stimuli JOIN (responses,subjects) ON responses.`stimulus`=stimuli.`stimulus_id` AND subjects.`id`=responses.`subj` WHERE stimuli.set=$set";
$result = mysql_query($query);
mysql_close();
$csv = CSVMaker::CSVStringFromMySQLResult($result);
CSVMaker::downloadCSV($csv,"scoreData.csv");
?>
