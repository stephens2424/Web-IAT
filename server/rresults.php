<?php 
session_set_cookie_params(1800);
session_start(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
  </head>
  <body>
    <h1>Results</h1>
    <?
    $subj = $_SESSION["subj"];
    $set = $_SESSION['set'];
    include 'connect.php';

    $query = "SELECT beginTime,score FROM subjects WHERE `id`=$subj";
    $result = mysql_query($query);
    $beginTime = mysql_result($result, 0, "beginTime");
    mysql_free_result($result);
    $score = $_SESSION['score'];

    $adjective;
    $biasedString;

    if (abs($score) > .65) {
      $adjective = "very biased ";
    } else if (abs($score) > .35) {
      $adjective = "somewhat biased ";
    } else if (abs($score) > .15) {
      $adjective = "mildly biased ";
    } else {
      $adjective = "not biased ";
    }
    if ($adjective == "not biased ") {
      $biasString = "in either direction";
    } else {
      $query = "SELECT name FROM `stimulusCategories` WHERE `experiment`=$set ORDER BY `id`";
      $result = mysql_query($query);
      if ($score > 0) {
        $category = mysql_result($result, 3, 'name');
      } else {
        $category = mysql_result($result, 4, 'name');
      }
      $biasString = "toward " . $category;
    }
    ?>
    <h2>Your results show that you are <?
    echo $adjective . $biasString;
    ?></h2>
    <h3><a href="<?
    $query = "SELECT secondEndURL FROM `experiments` WHERE `stimuli_set`=$set";
    $result = mysql_query($query);
    $url = mysql_result($result, 0, 'secondEndURL');
    echo $url;
    ?>">Please click here to continue with the study.</a></h3>
  </body>

           <? mysql_close() ?>
</html>
