<?php
	
        $subj = $_GET["subj"];
        $stim = $_GET["stim"];
        $rt = $_GET["rt"];
        $query = "INSERT INTO responses (`subj`,`stimulus`,`response_time`) VALUES ($subj,$stim,$rt)";

	$link = mysql_connect('127.0.0.1', 'root', 'tempest24') or die('Could not connect: ' . mysql_error());
	mysql_select_db('testIAT') or die('Could not select database');
	//include("connect.php");

	mysql_query($query);
        mysql_close();

?>