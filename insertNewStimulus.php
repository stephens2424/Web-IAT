<?php

$below = $_POST['below'];
if (isset($_POST['copy'])) {
  $copy = $_POST['copy'];
} else {
  $copy = false;
}
$position = $_POST['position'];
$set = $_POST['stim_set'];
$group = $_POST['group'];
$order;
include 'connect.php';
$query = "SELECT `order` FROM stimuli WHERE (`set`=$set AND `group`=$group) ORDER BY `order`";
$result = mysql_query($query);
if ($below == "true") {
  $order = mysql_result($result, $position, "order") + 1;
  $query = "UPDATE stimuli SET `order`=(`order` + 1) WHERE (`set`=$set AND `group`=$group AND `order`>=$order)";
} else {
  $order = mysql_result($result, $position, "order");
  $query = "UPDATE stimuli SET `order`=(`order` + 1) WHERE (`set`=$set AND `group`=$group AND `order`>=$order)";
}
mysql_query($query);
if ($copy == true) {
  $order--;
  $query = "SELECT * FROM stimuli WHERE (`set`=$set AND `group`=$group AND `order`=$order)";
  $order++;
  $result = mysql_query($query);
  $cat1 = mysql_result($result, 0, "category1");
  $cat2 = mysql_result($result, 0, "category2");
  $subcat1 = mysql_result($result, 0, "subcategory1");
  $subcat2 = mysql_result($result, 0, "subcategory2");
  $mask = mysql_result($result, 0, "mask");
  $newWord = $_POST['newWord'];
  $query = "INSERT INTO stimuli (`category1`,`category2`,`subcategory1`,`subcategory2`,`word`,`mask`,`set`,`order`,`group`) VALUES ('$cat1','$cat2','$subcat1','$subcat2','$newWord','$mask','$set','$order','$group')";
  $result = mysql_query($query);
} else {
  $query = "INSERT INTO stimuli (`set`,`order`,`group`) VALUES ($set,$order,$group)";
  $result = mysql_query($query);
}
$stim_id = mysql_insert_id();
$query = "SELECT stimulus_id,category1,category2,subcategory1,subcategory2,word,correct_response,instruction,mask FROM stimuli WHERE `stimulus_id`=$stim_id";
$result = mysql_query($query);
$array[] = array(
    "stim_id" => mysql_result($result, $row, "stimulus_id"),
    "category1" => mysql_result($result, $row, "category1"),
    "category2" => mysql_result($result, $row, "category2"),
    "subcategory1" => mysql_result($result, $row, "subcategory1"),
    "subcategory2" => mysql_result($result, $row, "subcategory2"),
    "word" => mysql_result($result, $row, "word"),
    "correct_response" => mysql_result($result, $row, "correct_response"),
    "instruction" => mysql_result($result, $row, "instruction"),
    "mask" => mysql_result($result, $row, "mask")
);
echo json_encode($array);
?>
