<?php
	
        $subj = $_GET["subj"];
        $stim = $_GET["stim"];
        $response = $_GET["response"];
        $rt = $_GET["rt"];
        $query = "INSERT INTO responses (`subj`,`stimulus`,`response`,`response_time`) VALUES ($subj,$stim,'$response',$rt)";

	$link = mysql_connect('127.0.0.1', 'root', 'tempest24') or die('Could not connect: ' . mysql_error());
	mysql_select_db('testIAT') or die('Could not select database');
	//include("connect.php");

	mysql_query($query);
        mysql_close();

?>