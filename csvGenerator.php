<?php
  include 'connect.php';
  $query = "SELECT * FROM responses";
  $result = mysql_query($query);
  $columns = mysql_num_fields($result);
  $out = '';
    // Put the name of all fields
  for ($i = 0; $i < $columns; $i++) {
    $l= mysql_field_name($result, $i);
    $out .= '"'.$l.'",';
  }
  $out = trim($out,",");
  $out .="\n";
  // Add all values in the table
  while ($l = mysql_fetch_array($result)) {
    for ($i = 0; $i < $columns; $i++) {
      $out .='"'.$l["$i"].'",';
    }
    $out = trim($out,",");
    $out .="\n";
  }
  $filename = "results.csv";
  // Output to browser with appropriate mime type
  //header("Content-type: text/x-csv");
  //header("Content-type: text/csv");
  header("Content-type: application/csv");
  header("Content-Disposition: attachment; filename=$filename");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Disposition: attachment; filename = $filename");
  header("Content-Length: " . strlen($out));
  echo $out;
  exit;
?>
