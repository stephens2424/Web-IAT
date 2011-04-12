<?php

$group = $_GET['group'];
$stage = $_GET['stage'];
$query = "UPDATE stimuliGroups SET `stage`=$stage WHERE (`id`=$group)";
include 'connect.php';
mysql_query($query);
mysql_close();
?>
