<?php
$set = $_POST['set'];
$order = $_POST['below'] == "true" ? $_POST['position'] + 1 : $_POST['position'];
$query = "UPDATE stimuliGroups SET `order`=(`order` + 1) WHERE (`stimuliSet`=$set AND `order`>=$order)";
include 'connect.php';
mysql_query($query);
$query = "INSERT INTO stimuliGroups (`stimuliSet`,`order`,`name`) VALUES ($set,$order,\"New Group\")";
mysql_query($query);
$id = mysql_insert_id();
$query = "SELECT * FROM stimuliGroups WHERE `id`=$id";
$result = mysql_query($query);
$array = array(
  "group_id" => $id,
  "randomize" => mysql_result($result,0,"randomize"),
  "name" => mysql_result($result,0,"name"),
  "stimuli" => array()
);
mysql_free_result($result);
mysql_close();
echo json_encode($array);
?>
