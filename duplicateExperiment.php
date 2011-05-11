<?php
include 'connect.php';
include 'GlobalKLogger.php';
$oldSet = $_GET['set'];
try {
  $query = "START TRANSACTION";
  mysql_query($query);
  
  $query = "SELECT `name` FROM experiments WHERE `stimuli_set`=$oldSet";
  $result = mysql_query($query);
  $oldName = mysql_result($result, 0, 'name');
  $query = "INSERT INTO experiments (name,endUrl) VALUES ('$oldName','thankyou.php')";
  if (!mysql_query($query)) {
      throw new Exception("Query failed");
    }
  $newSet = mysql_insert_id();
  include 'hashGenerator.php';
  $hash = HashGenerator::udihash($newSet);
  $query = "UPDATE experiments SET `hash`='$hash' WHERE `stimuli_set`=$newSet";
  if (!mysql_query($query)) {
      throw new Exception("Query failed");
    }

  $query = "SELECT * FROM stimulusCategories WHERE experiment=$oldSet";
  $result = mysql_query($query);
  $translateCategoryArray;
  while ($row = mysql_fetch_assoc($result)) {
    $name = $row['name'];
    $query = "INSERT INTO stimulusCategories (`name`,`experiment`) VALUES ('$name',$newSet)";
    if (!mysql_query($query)) {
      throw new Exception("Query failed");
    }
    $key = $row['id'];
    $value = mysql_insert_id();
    $translateCategoryArray["$key"] = "$value";
  }

  $query = "SELECT * FROM stimuliGroups WHERE stimuliSet=$oldSet";
  $result = mysql_query($query);
  $translateGroupArray;
  while ($row = mysql_fetch_assoc($result)) {
    $order = $row['order'];
    $name = $row['name'];
    $randomize = $row['randomize'];
    $stage = $row['stage'];
    if ($stage == null) $stage = 'null';
    $query = "INSERT INTO stimuliGroups (`stimuliSet`,`order`,`name`,`randomize`,`stage`) VALUES ($newSet,$order,'$name',$randomize,$stage)";
    if (!mysql_query($query)) {
      throw new Exception("Query failed");
    }
    $key = $row['id'];
    $value = mysql_insert_id();
    $translateGroupArray["$key"] = "$value";
  }

  $query = "SELECT * FROM stimuli WHERE `set`=$oldSet";
  $result = mysql_query($query);
  $dupStimuliQuery = "INSERT INTO stimuli (`set`,`category1`,`category2`,`subcategory1`,`subcategory2`,`word`,`correct_response`,`instruction`,`mask`,`order`,`group`,`stimulusCategory`) VALUES ";
  $first = true;
  while ($row = mysql_fetch_assoc($result)) {
    if (!$first) $dupStimuliQuery .= ",";
    $first = false;
    $cat1 = $row['category1'];
    if ($cat1 == null) $cat1 = 'null';
    $cat2 = $row['category2'];
    if ($cat2 == null) $cat2 = 'null';
    $subcat1 = $row['subcategory1'];
    if ($subcat1 == null) $subcat1 = 'null';
    $subcat2 = $row['subcategory2'];
    if ($subcat2 == null) $subcat2 = 'null';
    $word = mysql_real_escape_string(stripslashes($row['word']));
    $correct = $row['correct_response'];
    if ($correct == null) $correct = 'null';
    $instruction = mysql_real_escape_string(stripslashes($row['instruction']));
    $mask = $row['mask'];
    if ($mask == null) $mask = 'null';
    $order = $row['order'];
    if ($order == null) $order = 'null';
    $group = $translateGroupArray[$row['group']];
    if ($group == null) $group = 'null';
    $stimCat = $translateCategoryArray[$row['stimulusCategory']];
    if ($stimCat == null) $stimCat = 'null';
    
    $dupStimuliQuery .= "($newSet,$cat1,$cat2,$subcat1,$subcat2,";
    if ($word == null) $dupStimuliQuery .= 'null';
    else $dupStimuliQuery .= "'$word'";
    $dupStimuliQuery .= ",$correct,";
    if ($instruction == null) $dupStimuliQuery .= 'null';
    else $dupStimuliQuery .= "'$instruction'";
    $dupStimuliQuery .= ",$mask,$order,$group,$stimCat)";
  }
  if (!mysql_query($dupStimuliQuery)) {
    throw new Exception(@"Query failed - $dupStimuliQuery");
  }
  $query = "COMMIT";
  if (!mysql_query($query)) {
    throw new Exception("Commit query failed");
  }
} catch (Exception $e) {
  $query = "ROLLBACK";
  if (!mysql_query($query)) {
    logFatal($e->getMessage());
    echo 3; //unable to rollback
    exit;
  } else {
    logFatal($e->getMessage());
    echo 2;
    exit;
  }
}
echo 0;
?>
