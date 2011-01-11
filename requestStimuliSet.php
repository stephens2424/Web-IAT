<?php
  include 'connect.php';
  $set = $_POST['set'];
  $query = "SELECT stimulus_id,category1,category2,subcategory1,subcategory2,word,correct_response,instruction,mask,stimuli.`order` AS stimulusOrder,stimuliGroups.id AS group_id,stimuliGroups.name AS groupName,stimuliGroups.order AS groupOrder FROM stimuli JOIN stimuliGroups ON stimuli.`group`=stimuliGroups.`id` WHERE `set`=$set ORDER BY groupOrder,stimulusOrder";
  $result = mysql_query($query);
  $rows = mysql_num_rows($result);
  $row = 0;
  $group = mysql_result($result,$row, "group_id");
  do {
    while ($group != mysql_result($result, $row, "group_id")) {
      if ($changed == true) {
        $groupArray[] = array(
          "group_id" => $group,
          "groupName" => mysql_result($result,$row-1,"groupName"),
          "stimuli" => $array
        );
        $changed = false;
        unset($array);
      }
      $group++;
    }
    $changed = true;
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
    $row++;
  } while ($row < $rows);
  
  $groupArray[] = array(
    "group_id" => $group,
    "groupName" => mysql_result($result,$row-1,"groupName"),
    "stimuli" => $array
  );
    
  $upperArray["stimuliGroups"] = $groupArray;
  mysql_free_result($result);
  $query = "SELECT COUNT(DISTINCT subj) AS responses FROM responses INNER JOIN stimuli ON responses.stimulus=stimuli.stimulus_id WHERE stimuli.`set`=$set";
  $result = mysql_query($query);
  $upperArray["responseCount"] = mysql_result($result, 0, "responses");
  mysql_close();
  echo json_encode($upperArray);
?>
