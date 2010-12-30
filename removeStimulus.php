<?php
  include 'connect.php';
  $stim_id = $_POST['stim_id'];
  $query = "DELETE FROM stimuli WHERE stimulus_id=$stim_id";
  $result = mysql_query($query);
?>
