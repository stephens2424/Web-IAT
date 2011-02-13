<?php
  include 'connect.php';
  $name = $_GET['name'];
  $set = $_GET['set'];
  $query = "INSERT INTO stimulusCategories (`name`,`experiment`) VALUES ('$name',$set)";
  mysql_query($query);
  mysql_close();
?>
