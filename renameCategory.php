<?php
  include 'connect.php';
  $name = $_GET['name'];
  $id = $_GET['id'];
  $query = "UPDATE stimulusCategories SET `name`='$name' WHERE (`id`=$id)";
  mysql_query($query);
  mysql_close();
?>
