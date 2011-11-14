<?php
  $experiment = $_POST['experiment'];
  include 'connect.php';
  $query = "DELETE FROM experiments WHERE `stimuli_set`=$experiment";
  $result = mysql_query($query);
?>
