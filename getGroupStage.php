<?php
  $group = $_GET['group'];
  $query = "SELECT stage FROM stimuliGroups WHERE (`id`=$group)";
  include 'connect.php';
  $result = mysql_query($query);
  $stage = mysql_result($result, 0, 'stage');
  mysql_close();
  echo $stage;
?>
