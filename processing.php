<?php session_start(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
      <title></title>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js" type="text/javascript"></script>
      <script type="text/javascript">
<?
  $subj = $_SESSION['subj'];
  echo "var subject = $subj;\n";
?>
        $(document).ready(function() {
          $.post("calculateScore.php",{
            subj:subject
          },function () {
            var endURL ='<?
  $set = $_SESSION['set'];
  $query = "SELECT endUrl FROM experiments WHERE `stimuli_set`=$set";
  include 'connect.php';
  $result = mysql_query($query);
  echo mysql_result($result, 0, "endUrl");
?>';
            if (endURL.substr(0,34) === "http://ucla.qualtrics.com/SE/?SID=") {
              endURL += "&iat-id=" + subject;
            }
            location.href=endURL;
          });
        });
    </script>
  </head>
  <body>
    <p><img src="ajaxloader.gif"></p>
    <p>Processing</p>
    <p>Please wait. You will be redirected shortly.</p>
  </body>
</html>
