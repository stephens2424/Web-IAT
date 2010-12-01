<html>
  <head>
    <?php
      //TODO add web management tool for stimuli
      //TODO figure out how to make this full screen
      $development = false;
      $stim_set = $_GET['s'];
      echo "<script type=\"text/javascript\">\n";

      include 'connect.php';

      $query = "SELECT * FROM stimuli WHERE `set`=$stim_set";
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
        echo "var instructionArray = new Array($num-1);\n";
        while ($i < $num) {
          $text = mysql_result($result, $i, "word");
          $stimNum = mysql_result($result, $i, "stimulus_id");
          $catLeft = mysql_result($result, $i, "category1");
          $catRight = mysql_result($result, $i, "category2");
          $subCatLeft = mysql_result($result, $i, "subcategory1");
          $subCatRight = mysql_result($result, $i, "subcategory2");
          $instruction = mysql_result($result, $i, "instruction");
          echo "wordArray[$i]=\"$text\";\n";
          echo "stimArray[$i]=\"$stimNum\";\n";
          echo "catLeftArray[$i]=\"$catLeft\";\n";
          echo "catRightArray[$i]=\"$catRight\";\n";
          echo "subCatLeftArray[$i]=\"$subCatLeft\";\n";
          echo "subCatRightArray[$i]=\"$subCatRight\";\n";
          echo "instructionArray[$i]=\"$instruction\";\n";
          $i++;
        }
        echo "var dataArray = new Array($num-1);\n";
      }

      mysql_free_result($result);

      $query = "INSERT INTO subjects VALUES ()"; //TODO make sure that the timezone for the beginTime inserted into the database will be consistent/understandable
      $result = mysql_query($query);
      $subj = mysql_insert_id();
      printf("var subj=%d;\n</script>", $subj);

      mysql_close();
    ?>

      <script type="text/javascript">
        var wordNum = 0;
        var instruction = false;
        var wordShowed;

        function show_key ( the_key ) {
          //TODO add safeguard so only the proper keys trigger any changes
          //TODO make this able to track arrow keys
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
      if (wordArray[wordNum] == '') {
        toggleLayer('IAT');
        toggleLayer('instructionDiv');
        instruction = true;
        show_new_word()
      } else {
        if (instruction) {
          toggleLayer('instructionDiv');
          toggleLayer('IAT')
        }
        change_categories(0);
        document.getElementById('word').textContent = "%%%%%%%%%%%%%%";
        setTimeout("new_word_one (document.getElementById('word'))",200);
      }
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
      document.getElementById('instruction').textContent = instructionArray[wordNum];
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
    function toggleLayer( whichLayer )
    {
      var elem, vis;
      if( document.getElementById ) // this is the way the standards work
        elem = document.getElementById( whichLayer );
      else if( document.all ) // this is the way old msie versions work
        elem = document.all[whichLayer];
      else if( document.layers ) // this is the way nn4 works
        elem = document.layers[whichLayer];
      vis = elem.style;
      if(vis.display=='none' || vis.display=='')
        vis.display = 'block';
      else
        vis.display = 'none';
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
    <div id="instructionDiv" style="display: none;">
      <table class="center">
        <tr>
          <td>
            <h1 id="instruction" class="center"></h1>
          </td>
        </tr>
      </table>
    </div>
    <div id="IAT" style="display:block;">
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