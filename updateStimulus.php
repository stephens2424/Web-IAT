<?php
  include 'connect.php';
  $cat1 = $_POST['leftCategory'];
  $cat2 = $_POST['rightCategory'];
  $subcat1 = $_POST['subLeftCategory'];
  $subcat2 = $_POST['subRightCategory'];
  $word = $_POST['word'];
  $instruction = $_POST['instruction'];
  $stim_id = $_POST['stim_id'];
  $query = "UPDATE stimuli SET category1='$cat1',category2='$cat2',subcategory1='$subcat1',subcategory2='$subcat2',word='$word',instruction='$instruction' WHERE stimulus_id=$stim_id";
  mysql_query($query);
  $query = "SELECT stimulus_id,category1,category2,subcategory1,subcategory2,word,correct_response,instruction FROM stimuli WHERE `stimulus_id`=$stim_id";
  $result = mysql_query($query);
  $array[] = array(
    "stim_id" => mysql_result($result, $row, "stimulus_id"),
    "category1" => mysql_result($result, $row, "category1"),
    "category2" => mysql_result($result, $row, "category2"),
    "subcategory1" => mysql_result($result, $row, "subcategory1"),
    "subcategory2" => mysql_result($result, $row, "subcategory2"),
    "word" => mysql_result($result, $row, "word"),
    "correct_response" => mysql_result($result, $row, "correct_response"),
    "instruction" => mysql_result($result, $row, "instruction")
  );
  echo json_encode($array);
?>
