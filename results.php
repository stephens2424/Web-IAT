<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
      <h1>Results</h1>
        <?php
          $subj = $_GET["subj"];
          $link = mysql_connect('127.0.0.1', 'root', 'tempest24') or die('Could not connect: ' . mysql_error());
          mysql_select_db('testIAT') or die('Could not select database');

          $query = "SELECT beginTime FROM subjects WHERE `id`=$subj";
          $result = mysql_query($query);
          $beginTime = mysql_result($result,0,"beginTime");
          mysql_free_result($result);

          echo "<h2>subject: $subj start time: $beginTime</h1><p>";

          $query = "SELECT stimuli.word,responses.response,responses.response_time FROM stimuli,responses,subjects WHERE `subj`=$subj && subjects.id=responses.subj && stimuli.stimulus_id=responses.stimulus";
          $result = mysql_query($query);
          $rows = mysql_num_rows($result);

          echo "<table>\n\t<tr><td>Stimulus</td><td>Response</td><td>Response Time</td></tr>\n";
          $i = 0;
          while ($i < $rows) {
            $stim = mysql_result($result,$i,"word");
            $resp = mysql_result($result,$i,"response");
            $rt = mysql_result($result,$i,"response_time");
            echo "\t<tr><td>$stim</td><td>$resp</td><td>$rt</td></tr>";
            $i++;
          }
          echo "</table>";

          mysql_close();
        ?>
    </body>
</html>
