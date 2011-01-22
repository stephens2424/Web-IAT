<?php

  $subj = $_POST["subj"];
  $stim = $_POST["stim"];
  $response = $_POST["response"];
  $rt = $_POST["rt"];
  $query = "INSERT INTO responses (`subj`,`stimulus`,`response`,`response_time`) VALUES ($subj,$stim,'$response',$rt)";

  include "connect.php";

  mysql_query($query);
  mysql_close();
?>