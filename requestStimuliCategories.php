<?php
  include 'connect.php';
  $set = $_POST['set'];
  $query = "SELECT name,id FROM stimulusCategories WHERE `experiment`=$set";
  $result = mysql_query($query);
  while ($row = mysql_fetch_assoc($result)) {
    $array[] = array(
      id => $row["id"],
      name => $row["name"]
    );
  }
  echo json_encode($array);
?>
