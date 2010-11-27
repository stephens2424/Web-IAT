<html>
<head>
<?php
		//echo "php is working";
		echo "<script language=\"JavaScript1.7\" type=\"text/javascript\">\n";
		
		$link = mysql_connect('127.0.0.1', 'root', 'tempest24') or die('Could not connect: ' . mysql_error());
                mysql_select_db('testIAT') or die('Could not select database');
		
		$query="SELECT * FROM stimuli";
		$result=mysql_query($query);

		$num=mysql_numrows($result);
		
		$i = 0;  

		if ($num == 0)  
			print "Error - No records found";  
		elseif ($num > 0)  {  
			echo "var wordArray = new Array($num-1);\n";
			echo "var stimArray = new Array($num-1);\n";
			while ($i < $num)  {
				$text = mysql_result($result, $i, "word");
				$stimNum = mysql_result($result, $i, "stimulus_id");
				echo "wordArray[$i]=\"$text\";\n";
				echo "stimArray[$i]=\"$stimNum\";\n";
				$i++;
			}
			echo "var dataArray = new Array($num-1);\n";
		}
		
		mysql_free_result($result);
		
		$query = "INSERT INTO subjects VALUES ()";
		$result = mysql_query($query);
		printf("var subj=%d;\n</script>",mysql_insert_id());
		
		mysql_close();
		
		?>

<script language="JavaScript1.7" type="text/javascript">
		var wordNum = 0;
		var wordShowed;
		
		function show_key ( the_key ) {
			var date = new Date().getTime();
			sendData((date - wordShowed).toString());
    		if ( ! the_key ) {
        		the_key = event.keyCode;
    		}
    		
    		/*
			//replace all before 'change word' with php/mysql database insertion
			var tbl = document.getElementById('data');
  			var lastRow = tbl.rows.length;
  				// if there's no header row in the table, then iteration = lastRow + 1
  			var iteration = lastRow;
  			var row = tbl.insertRow(lastRow);
  
  				// left cell
  			var cellLeft = row.insertCell(0);
  			var textNode = document.createTextNode(String.fromCharCode ( the_key ));
  			cellLeft.appendChild(textNode);
  
  				// right cell
  			var cellRight = row.insertCell(1);
			var otherTextNode = document.createTextNode(date.toString());
  			cellRight.appendChild(otherTextNode);
  			*/
				//change word
			new_word ();
		}
		function new_word () {
			document.getElementById('word').textContent = "%%%%%%%%%%%%%%";
			setTimeout("new_word_one (document.getElementById('word'))",200);
		}
		function new_word_one () {
			document.getElementById('word').textContent = "#########";
			setTimeout("new_word_two (document.getElementById('word'))",200);
		}
		function new_word_two () {
			document.getElementById('word').textContent = "@@@@@@@";
			setTimeout("new_word_three (document.getElementById('word'))",200);
		}
		function new_word_three () {
			document.getElementById('word').textContent = "xxxxxxxxxx";
			setTimeout("show_new_word(document.getElementById('word'))",200);
		}
		function show_new_word () {
			document.getElementById('word').textContent = wordArray[wordNum];
			//new_word_timestamp ();
			wordShowed = new Date().getTime();
			wordNum++;
		}
		function new_word_timestamp () {
			var date = new Date().getTime();

			/*
			//replace the following with php/mysql database insertion
			var tbl = document.getElementById('data');
  			var lastRow = tbl.rows.length;
  				// if there's no header row in the table, then iteration = lastRow + 1
  			var iteration = lastRow;
  			var row = tbl.insertRow(lastRow);
  
  				// left cell
  			var cellLeft = row.insertCell(0);
  			var textNode = document.createTextNode("New Word");
  			cellLeft.appendChild(textNode);
  
  				// right cell
  			var cellRight = row.insertCell(1);
			var otherTextNode = document.createTextNode(date.toString());
  			cellRight.appendChild(otherTextNode);
  			*/
		}
		function load_timestamp () {
			var date = new Date().getTime();
			/*
			var tbl = document.getElementById('data');
  			var lastRow = tbl.rows.length;
  				// if there's no header row in the table, then iteration = lastRow + 1
  			var iteration = lastRow;
  			var row = tbl.insertRow(lastRow);
  
  				// left cell
  			var cellLeft = row.insertCell(0);
  			var textNode = document.createTextNode("Page Load");
  			cellLeft.appendChild(textNode);
  
  				// right cell
  			var cellRight = row.insertCell(1);
			var otherTextNode = document.createTextNode(date.toString());
  			cellRight.appendChild(otherTextNode);
			*/
			//sendData(date.toString());
			new_word();
			
		}
		
		function sendData(data) {
			if (window.XMLHttpRequest)
			  {// code for IE7+, Firefox, Chrome, Opera, Safari
			  	xmlhttp=new XMLHttpRequest();
			  }
			else
			  {// code for IE6, IE5
			  	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			  }
                        xmlhttp.onreadystatechange = function (aEvt) {
                            if (this.readyState == 4) {
                                if(this.status !== 200) {
                                    location.href="servererror.php?status=" + this.status + "&statusText=" + encodeURIComponent(this.statusText);
                                }
                            }
                        };
			xmlhttp.open("GET","dataHandler.php?subj=" + subj.toString() + "&stim=" + stimArray[wordNum] + "&rt=" + data,true);
			xmlhttp.send();
		}
		
	</script>
<title>IAT</title>
<style type="text/css">
table.center {
	width: 100%;
	height: 75%;
	font-family: "Helvetica"
}

td.categoryLeft {
	text-align: left;
	padding-left: 0%;
}

td.categoryRight {
	text-align: right;
	padding-right: 0%;
}

h1.categoryLeft {
	text-align: left;
}

h1.categoryRight {
	text-align: right;
}

h1.center {
	text-align: center;
}
</style>
</head>
<body onkeypress="show_key(event.which);" onload="load_timestamp()">
<div>
<table class="center">
	<tr>
		<td class="categoryLeft">
		<h1 class="categoryLeft">Cat One</h1>
		</td>
		<td class="categoryRight"><h1 class="categoryRight">Cat Two</h1></td></tr>
	<tr>
		<td class="categoryLeft">
		<h1 class="categoryLeft">Second Cat One</h1>
		</td>
		<td class="categoryRight"><h1 class="categoryRight">Second Cat Two</h1></td></tr>
	<tr></tr>
	<tr>
		<td colspan="2">
		<h1 class="center" id="word">Error - No Stimulus Data</h1>
		</td>
	</tr>
</table>
</div>
<table id="data">
	<tr>
		<td>key</td>
		<td>timestamp</td>
	</tr>
</table>
</body>
</html>