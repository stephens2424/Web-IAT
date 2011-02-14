<?php
  $group = $_POST['group'];
  include 'connect.php';
  $query = "SELECT `order` FROM stimuliGroups WHERE `id`=$group";
  $result = mysql_query($query);
  $order = mysql_result($result, 0,'order');
  $query = "UPDATE stimuliGroups SET `order`=(`order`-1) WHERE (`order`>$order)";
  mysql_query($query);
  $query = "DELETE FROM stimuliGroups WHERE `id`=$group";
  $result = mysql_query($query);
?>
