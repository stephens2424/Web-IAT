<?php
  include 'connect.php';
  $set = $_POST['stim_set'];
  $query = "SELECT `id`,`order`,`randomize` FROM stimuliGroups WHERE `stimuliSet`=$set";
  $result = mysql_query($query);
  $rows = mysql_num_rows($result);
  for ($row = 0; $row < $rows; $row++) {
    $group = mysql_result($result, $row,"id");
    if (mysql_result($result, $row,"randomize") == "0") {
      $query2 = "SELECT stimulus_id,category1,category2,subcategory1,subcategory2,word,correct_response,instruction,mask,stimuli.`order` AS stimulusOrder FROM stimuli WHERE (`set`=$set AND `group`=$group) ORDER BY stimulusOrder";
    } else {
      $query2 = "SELECT stimulus_id,category1,category2,subcategory1,subcategory2,word,correct_response,instruction,mask FROM stimuli WHERE (`set`=$set AND `group`=$group) ORDER BY RAND()";
    }
    $result2 = mysql_query($query2);
    $rows2 = mysql_num_rows($result2);
    for ($row2 = 0; $row2 < $rows2; $row2++) {
      $array[] = array(
        "stim_id" => mysql_result($result2, $row2, "stimulus_id"),
        "category1" => mysql_result($result2, $row2, "category1"),
        "category2" => mysql_result($result2, $row2, "category2"),
        "subcategory1" => mysql_result($result2, $row2, "subcategory1"),
        "subcategory2" => mysql_result($result2, $row2, "subcategory2"),
        "word" => mysql_result($result2, $row2, "word"),
        "correct_response" => mysql_result($result2, $row2, "correct_response"),
        "instruction" => mysql_result($result2, $row2, "instruction"),
        "mask" => mysql_result($result2, $row2, "mask")
      );
    }
    mysql_free_result($result2);
    $groupArray[] = array(
      "stimulus" => $array
    );
    unset($array);
  }
  $query = "SELECT name,id FROM stimulusCategories WHERE `experiment`=$set";
  $result = mysql_query($query);
  while ($row = mysql_fetch_assoc($result)) {
    $categoryArray[$row['id']] = $row['name'];
  }
  $query = "SELECT endUrl FROM experiments WHERE `stimuli_set`=$set";
  $result = mysql_query($query);
  $upperArray = array(
    "stimuli" => $groupArray,
    "endURL" => mysql_result($result, 0, "endUrl"),
    "categories" => $categoryArray
  );
  mysql_free_result($result);
  mysql_close();
  echo json_encode($upperArray);
?>
