<?php
$name = $_POST['name'];
$query = "INSERT INTO experiments (name,endUrl) VALUES ('$name','thankyou.php')";
include 'connect.php';
mysql_query($query);
echo mysql_insert_id();
?>
