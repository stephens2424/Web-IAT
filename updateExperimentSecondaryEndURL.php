<?php
  $set = $_POST['set'];
  $url = $_POST['newURL'];
  $query = "UPDATE experiments SET `secondEndUrl`='$url' WHERE `stimuli_set`=$set";
  include 'connect.php';
  mysql_query($query);
  mysql_close();

?>
