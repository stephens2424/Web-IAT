<?php
  include 'connect.php';
  $id = $_GET['id'];
  $query = "DELETE FROM stimulusCategories WHERE `id`=$id";
  mysql_query($query);
  mysql_close();
?>
