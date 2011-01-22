<?php
  $group_id = $_POST['group_id'];
  $randomize = $_POST['randomize'];
  include 'connect.php';
  $query = "UPDATE stimuliGroups SET `randomize`=$randomize WHERE `id`=$group_id";
  mysql_query($query);
  mysql_close();
?>
