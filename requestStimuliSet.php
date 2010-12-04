<?php
  include 'connect.php';
  $set = $_POST['set'];
  $query = "SELECT stimulus_id,category1,category2,subcategory1,subcategory2,word,correct_response,instruction FROM stimuli WHERE `set`=$set";
  $result = mysql_query($query);
  $rows = mysql_num_rows($result);
  $row = 0;
  while ($row < $rows) {
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
    $row++;
  }
  $upperArray["stimuli"] = $array;
  mysql_free_result($result);
  $query = "SELECT COUNT(DISTINCT subj) AS responses FROM responses INNER JOIN stimuli ON responses.stimulus=stimuli.stimulus_id WHERE stimuli.`set`=$set";
  $result = mysql_query($query);
  $upperArray["responseCount"] = mysql_result($result, 0, "responses");
  mysql_close();
  echo json_encode($upperArray);
?>
