<?php session_start(); ?>
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
    include 'connect.php';

    $query = "SELECT beginTime,score FROM subjects WHERE `id`=$subj";
    $result = mysql_query($query);
    $beginTime = mysql_result($result, 0, "beginTime");
    mysql_free_result($result);
    $score = $_SESSION['score'];

    echo "<h2>subject:$subj score:$score start time:$beginTime</h1><p>";

    $query = "SELECT stimuli.word,responses.response,responses.response_time FROM stimuli,responses,subjects WHERE `subj`=$subj && subjects.id=responses.subj && stimuli.stimulus_id=responses.stimulus";
    $result = mysql_query($query);
    $rows = mysql_num_rows($result);

    echo "<table>\n\t<tr><td>Stimulus</td><td>Response</td><td>Response Time</td></tr>\n";
    $i = 0;
    while ($i < $rows) {
      $stim = mysql_result($result, $i, "word");
      $resp = mysql_result($result, $i, "response");
      $rt = mysql_result($result, $i, "response_time");
      echo "\t<tr><td>$stim</td><td>$resp</td><td>$rt</td></tr>";
      $i++;
    }
    echo "</table>";

    mysql_close();
    ?>
  </body>
</html>
