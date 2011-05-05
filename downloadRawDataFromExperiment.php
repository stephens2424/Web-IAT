<?php
include 'connect.php';
require_once 'CSVMaker.php';
$set = $_GET['set'];
$query = "SELECT subj,subjects.`qualtrics_id`,stimulus,response_id,response,correct_response,response_time FROM responses JOIN (stimuli,subjects) ON (responses.stimulus=stimuli.stimulus_id AND responses.subj=subjects.`id`) WHERE stimuli.set=$set ORDER BY subj,response_id ASC";
$result = mysql_query($query);
mysql_close();
$csv = CSVMaker::CSVStringFromMySQLResult($result);
CSVMaker::downloadCSV($csv,"rawData.csv");
?>
