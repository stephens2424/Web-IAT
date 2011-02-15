<?php
$name = $_POST['name'];
$query = "INSERT INTO experiments (name,endUrl) VALUES ('$name','thankyou.php')";
include 'connect.php';
mysql_query($query);
$newSetId = mysql_insert_id();
include 'hashGenerator.php';
$hash = HashGenerator::udihash($newSetId);
$query = "UPDATE experiments SET `hash`='$hash' WHERE `stimuli_set`=$newSetId";
mysql_query($query);
$array = array(
  id => $newSetId,
  hash => $hash,
  name => $name
);
echo json_encode($array);
?>
