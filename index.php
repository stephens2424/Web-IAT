<?php
session_start();
  ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="./jquery.jnotify/css/jquery.jnotify-bottom.css" />
    <link rel="stylesheet" type="text/css" href="./blitzer/jquery-ui-1.8.14.custom.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
    <!-- <script type="text/javascript" src="jquery.js"></script> -->
    <script type="text/javascript" src="jquery-ui.js"></script>
    <script type="text/javascript" src="./jquery.jnotify/lib/jquery.jnotify.js"></script>
    <script type="text/javascript" src="sha1.js"></script>
    <script type="text/javascript" src="corner/jquery.corner.js"></script>
    <script type="text/javascript" src="jquery-animate-css-rotate-scale/jquery-css-transform/jquery-css-transform.js"></script>
    <script type="text/javascript" src="jquery-animate-css-rotate-scale/jquery-animate-css-rotate-scale.js"></script>
    <script type="text/javascript" src="Lightbox_me/jquery.lightbox_me.js"></script>
    <script type="text/javascript" src="jquery.jeditable/jquery.jeditable.js"></script>
    <script type="text/javascript" src="jsundoable/jsundoable.js"></script>
    <script type="text/javascript" src="selectWithOther.jeditable/selectWithOther.jeditable.js"></script>
    <script type="text/javascript" src="ClientIATManager.js"></script>
    <script type="text/javascript" src="config.js"></script>
    <link rel="stylesheet" type="text/css" href="index.css"/>
    <title>IAT Web</title>
  </head>
  <body>
    <div id="contentDiv"></div>
    <?php
    if ($_GET['i']) {
      $hash = $_GET['i'];
      echo "<script type='text/javascript'>";
      echo "IAT.managerFilePath = 'admin/IATManager.php';\n";
      echo "IAT('$hash',\$('#contentDiv'));\n";
      echo "</script>";
    } else {
      echo "
        <h1>WebIAT</h1>
        <p>This is software is currently undergoing development and testing. Please do not use this for actual experiments until further notice.</p>
        <p><a href=\"about.html\">About</a></p>
      ";
    }
    ?>
  </body>
</html>
