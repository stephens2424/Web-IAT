<?php
session_start();
  ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="./scripts/jquery.jnotify/css/jquery.jnotify-bottom.css" />
    <link rel="stylesheet" type="text/css" href="./css/blitzer/jquery-ui-1.8.14.custom.css" />
    <?php
    
    if ($_GET['i']) {
      $hash = $_GET['i'];
      $running = true;
      echo "<script type='text/javascript' src='./scripts/require-jquery.js' data-main='./scripts/index.js'></script>";
    } else {
      $running = false;
    }
    
    ?>
    <link rel="stylesheet" type="text/css" href="./css/index.css"/>
    <title>IAT Web</title>
  </head>
  <body>
    <div id="contentDiv"></div>
    <?php
    if (!$running) {
      echo "
        <h1>WebIAT</h1>
        <p>This is software is currently undergoing development and testing. Please do not use this for actual experiments until further notice.</p>
        <p><a href=\"about.html\">About</a></p>
      ";
    }
    ?>
  </body>
</html>
