<?php

include 'connect.php';
$set = $_POST['set'];
$group = $_POST['group'];
$query = "SELECT `order` FROM stimuli WHERE (`set`=$set AND `group`=$group) ORDER BY `order` DESC";
$result = mysql_query($query);
if (mysql_num_rows($result) > 0) {
  $order = mysql_result($result, 0, "order") + 1;
} else {
  $order = 1;
}
$query = "INSERT INTO stimuli (`set`,`order`,`group`) VALUES ($set,$order,$group)";
$result = mysql_query($query);
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
