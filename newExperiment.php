<?php
$name = $_POST['name'];
$query = "INSERT INTO experiments (name) VALUES ('$name')";
include 'connect.php';
mysql_query($query);
echo mysql_insert_id();
?>
