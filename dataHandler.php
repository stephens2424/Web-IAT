<?php

  $subj = $_GET["subj"];
  $stim = $_GET["stim"];
  $response = $_GET["response"];
  $rt = $_GET["rt"];
  $query = "INSERT INTO responses (`subj`,`stimulus`,`response`,`response_time`) VALUES ($subj,$stim,'$response',$rt)";

  include "connect.php";

  mysql_query($query);
  mysql_close();
?>