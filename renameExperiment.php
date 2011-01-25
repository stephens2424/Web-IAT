<?php
  $name = $_POST['name'];
  $experiment = $_POST['experiment'];
  $query = "UPDATE experiments SET `name`=\"$name\" WHERE `stimuli_set`=$experiment";
  include 'connect.php';
  mysql_query($query);
  mysql_close();
?>
