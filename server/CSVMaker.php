<?php
abstract class CSVMaker {
  public static function CSVStringFromMySQLResult($result) {
    $out = '';
    while ($field = mysql_fetch_field($result)->name) {
      $out .= "$field,";
    }
    $out .= "\n";
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      foreach ($row as $value) {
        $out .= "$value,";
      }
      $out .= "\n";
    }
    return $out;
  }
  public static function downloadCSV($csvString,$filename) {
  // Output to browser with appropriate mime type
  //header("Content-type: text/x-csv");
  //header("Content-type: text/csv");
    header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=$filename");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Disposition: attachment; filename = $filename");
    header("Content-Length: " . strlen($csvString));
    echo $csvString;
    exit;
  }
  public static function generateCSV($data,$columnNames) {
    $columns = 2;
    $out = '';
  // Put the name of all fields
    $first = true;
    foreach ($columnNames as $value) {
      if ($first) {
        $out .= "\"$value\"";
        $first = false;
      } else {
        $out .= ",\"$value\"";
      }
    }
    $out .= "\n";

  // Add all values in the table
    $row = 0;
    foreach ($data as $key => $value) {
      $out .= '"' . $key . '","' . $qualtricsIds[$key] . '","' . $value . '"';
      $out .= "\n";
      $row++;
    }
  }
}
?>
