<?php
$escapedjson = $_POST['responseData'];
$subj = $_POST['subj'];
$json = stripcslashes($escapedjson);

include 'connect.php';

function insertResponse($subj,$response,$rt,$stim) {
  $query = "INSERT INTO responses (`subj`,`stimulus`,`response`,`response_time`) VALUES ($subj,$stim,'$response',$rt)";
  mysql_query($query);
}

$data = json_decode($json,true);
foreach ($data as $group) {
  foreach ($group['stimulus'] as $stimulus) {
    insertResponse($subj,$stimulus['response'],$stimulus['time'],$stimulus['stim']);
  }
}
mysql_close();

?>
