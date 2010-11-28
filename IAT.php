<html>
  <head>
    <?php
      $development = false;
      echo "<script language=\"JavaScript1.7\" type=\"text/javascript\">\n";

      $link = mysql_connect('127.0.0.1', 'root', 'tempest24') or die('Could not connect: ' . mysql_error());
      mysql_select_db('testIAT') or die('Could not select database');

      $query = "SELECT * FROM stimuli";
      $result = mysql_query($query);

      $num = mysql_num_rows($result);

      $i = 0;

      if ($num == 0)
        print "Error - No records found";
      elseif ($num > 0) {
        echo "var wordArray = new Array($num-1);\n";
        echo "var stimArray = new Array($num-1);\n";
        echo "var catLeftArray = new Array($num-1);\n";
        echo "var catRightArray = new Array($num-1);\n";
        echo "var subCatLeftArray = new Array($num-1);\n";
        echo "var subCatRightArray = new Array($num-1);\n";
        while ($i < $num) {
          $text = mysql_result($result, $i, "word");
          $stimNum = mysql_result($result, $i, "stimulus_id");
          $catLeft = mysql_result($result, $i, "category1");
          $catRight = mysql_result($result, $i, "category2");
          $subCatLeft = mysql_result($result, $i, "subcategory1");
          $subCatRight = mysql_result($result, $i, "subcategory2");
          echo "wordArray[$i]=\"$text\";\n";
          echo "stimArray[$i]=\"$stimNum\";\n";
          echo "catLeftArray[$i]=\"$catLeft\";\n";
          echo "catRightArray[$i]=\"$catRight\";\n";
          echo "subCatLeftArray[$i]=\"$subCatLeft\";\n";
          echo "subCatRightArray[$i]=\"$subCatRight\";\n";
          $i++;
        }
        echo "var dataArray = new Array($num-1);\n";
      }

      mysql_free_result($result);

      $query = "INSERT INTO subjects VALUES ()";
      $result = mysql_query($query);
      $subj = mysql_insert_id();
      printf("var subj=%d;\n</script>", $subj);

      mysql_close();
    ?>

    <script language="JavaScript1.7" type="text/javascript">
      var wordNum = 0;
      var wordShowed;
		
      function show_key ( the_key ) {
        var date = new Date().getTime();
        sendData(String.fromCharCode(the_key),(date - wordShowed).toString());
        if (wordNum >= wordArray.length) {
          location.href="<?php
            if ($development) {
              echo "results.php?subj=$subj";
            } else {
              echo "thankyou.php";
            }
            ?>";
        }
        new_word ();
      }
      function new_word () {
        change_categories(0);
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
        wordShowed = new Date().getTime();
        wordNum++;
      }
      function change_categories (wordNumShift) {
        document.getElementById('catLeft').textContent = catLeftArray[wordNum + wordNumShift];
        document.getElementById('catRight').textContent = catRightArray[wordNum + wordNumShift];
        document.getElementById('subCatLeft').textContent = subCatLeftArray[wordNum + wordNumShift];
        document.getElementById('subCatRight').textContent = subCatRightArray[wordNum + wordNumShift];
      }
		
      function sendData(response,time) {
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
        xmlhttp.open("GET","dataHandler.php?subj=" + subj.toString() + "&stim=" + stimArray[wordNum-1] + "&response=" + response + "&rt=" + time,true);
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
  <body onkeypress="show_key(event.which);" onload="new_word()">
    <div>
      <table class="center">
        <tr>
          <td class="categoryLeft">
            <h1 class="categoryLeft" id="catLeft"></h1>
          </td>
          <td class="categoryRight">
            <h1 class="categoryRight" id="catRight"></h1>
          </td>
        </tr>
        <tr>
          <td class="categoryLeft">
            <h1 class="categoryLeft" id="subCatLeft"></h1>
          </td>
          <td class="categoryRight">
            <h1 class="categoryRight" id="subCatRight"></h1>
          </td>
        </tr>
        <tr></tr>
        <tr>
          <td colspan="2">
            <h1 class="center" id="word">Error - No Stimulus Data</h1>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>