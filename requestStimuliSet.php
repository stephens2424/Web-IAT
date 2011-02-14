<?php
  include 'connect.php';
  $set = $_POST['stim_set'];
  $query = "SELECT * FROM stimuliGroups WHERE `stimuliSet`=$set ORDER BY `order` ASC";
  $result = mysql_query($query);
  for ($row = 0; $row < mysql_num_rows($result); $row++) {
    $group = mysql_result($result,$row,"id");
    $query2 = "SELECT * FROM stimuli WHERE (`set`=$set AND `group`=$group)";
    $result2 = mysql_query($query2);
    $rows2 = mysql_num_rows($result2);
    for ($row2 = 0; $row2 < mysql_num_rows($result2); $row2++) {
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
      "group_id" => $group,
      "randomize" => mysql_result($result,$row,"randomize"),
      "groupName" => mysql_result($result,$row,"name"),
      "stage" => mysql_result($result,$row,"stage"),
      "stimuli" => $array ? $array : array()
    );
    unset($array);
  }
  mysql_free_result($result);
  $upperArray["stimuliGroups"] = $groupArray;
  $query = "SELECT COUNT(DISTINCT subj) AS responses FROM responses INNER JOIN stimuli ON responses.stimulus=stimuli.stimulus_id WHERE stimuli.`set`=$set";
  $result = mysql_query($query);
  $upperArray["responseCount"] = mysql_result($result, 0, "responses");
  $query = "SELECT endUrl FROM experiments WHERE `stimuli_set`=$set";
  $result = mysql_query($query);
  $upperArray['endURL'] = mysql_result($result,0,"endUrl");
  mysql_free_result($result);
  mysql_close();
  echo json_encode($upperArray);
?>
