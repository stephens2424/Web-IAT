<?php
  $group = $_POST['group'];
  include 'connect.php';
  $query = "DELETE FROM stimuliGroups WHERE `id`=$group";
  $result = mysql_query($query);
?>
