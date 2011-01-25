<?php
$group_id = $_POST['groupId'];
$name = $_POST['name'];
$query = "UPDATE stimuliGroups SET `name`=\"$name\" WHERE `id`=$group_id";
include 'connect.php';
mysql_query($query);
mysql_close();
?>
