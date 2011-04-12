<?php

include 'connect.php';
include 'hashGenerator.php';
$query = "SELECT `stimuli_set` FROM experiments";
$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
  $id = $row['stimuli_set'];
  $hash = HashGenerator::udihash($id);
  $query = "UPDATE experiments SET `hash`='$hash' WHERE `stimuli_set`=$id";
  mysql_query($query);
}
echo "Complete";
?>
