<?php session_start(); ?>
<html>
  <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">
      var stimuliData;
      var categories;
      var successfulResponses = 0;
      var responses = 0;
      var totalStimuli = 0;
    <?
      //TODO add web management tool for stimuli
      //TODO figure out how to make this full screen
      $development = false;
      include 'connect.php';
      if (isset($_GET['qid'])) {
        $qid = $_GET['qid'];
        $query = "INSERT INTO subjects (`qualtrics_id`) VALUES ($qid)";
      } else {
        $query = "INSERT INTO subjects VALUES ()";
      }
      $result = mysql_query($query);
      $subj = mysql_insert_id();
      $_SESSION['subj'] = $subj;
      printf("var subj=%d;\n", $subj);
      mysql_close();
      ?>
        function load() {
          $.ajax({
            url:"requestStimuliSetForIAT.php",
            data:{
              stim_set_hash:'<? echo $_GET['s'] ?>'
            },
            type:"POST",
            success:function (data, textStatus, XMLHttpRequest) {
              if (data = '1') {
                $('#ajaxImage').remove();
                $('#word').text("Error: This stimuli set does not exist");
                return;
              }
              var upperData = JSON.parse(data);
              stimuliData = upperData.stimuli;
              categories = upperData.categories;
              for (var i = 0; i < stimuliData.length; i++) {
                totalStimuli += stimuliData[i].stimulus.length;
              }
              $("#ajaxImage").remove();
              new_word();
            },
            error:function (XMLHttpRequest, textStatus, errorThrown) {
              $("#ajaxImage").remove();
              $("#word").text("Error loading stimulus data.");
            }
          });
        }
        var wordNum = 0;
        var groupNum = 0;
        var instruction = false;
        var wordShowedTime;
        var wordShowed = false;

        function detect_keydown ( e ) {
          //TODO add safeguard so only the proper keys trigger any changes
          var time = new Date().getTime();
          if (!wordShowed) return;
          var keynum;
          var keychar;
          if(window.event) // IE
          {
            keynum = e.keyCode;
          }
          else if(e.which) // Netscape/Firefox/Opera
          {
            keynum = e.which;
          }
          switch (keynum) {
            case 37:
              keychar = "left";
              break;
            case 38:
              keychar = "up";
              return;
            case 39:
              keychar = "right";
              break;
            case 40:
              keychar = "down";
              return;
            default:
              return;
              //keychar = String.fromCharCode(keynum);
          }
          wordShowed = false;
          sendData(keychar,(time - wordShowedTime).toString());
          if (wordNum >= stimuliData[groupNum].stimulus.length) {
            if (groupNum >= stimuliData.length - 1) {
              done = true;
            } else {
              groupNum++;
              wordNum = 0;
              new_word();
            }
          } else {
            new_word ();
          }
      }
      function new_word () {
        if (stimuliData[groupNum].stimulus[wordNum].word === '') {
          toggleLayer('IAT');
          toggleLayer('instructionDiv');
          instruction = true;
        } else {
          if (instruction) {
            toggleLayer('instructionDiv');
            toggleLayer('IAT')
          }
          change_categories(0);
        }
        if (stimuliData[groupNum].stimulus[wordNum].mask === "1")
          new_word_zero();
        else
          show_new_word();
      }
      function new_word_zero () {
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
        document.getElementById('word').textContent = stimuliData[groupNum].stimulus[wordNum].word;
        document.getElementById('instruction').textContent = stimuliData[groupNum].stimulus[wordNum].word;
        wordShowedTime = new Date().getTime();
        wordShowed = true;
        wordNum++;
      }
      function change_categories (wordNumShift) {
        var cat1 = categories[stimuliData[groupNum].stimulus[wordNum + wordNumShift].category1];
        var cat2 = categories[stimuliData[groupNum].stimulus[wordNum + wordNumShift].category2];
        var subcat1 = categories[stimuliData[groupNum].stimulus[wordNum + wordNumShift].subcategory1];
        var subcat2 = categories[stimuliData[groupNum].stimulus[wordNum + wordNumShift].subcategory2];
        $('#catLeft').text(cat1 ? cat1 : '');
        $('#catRight').text(cat2 ? cat2 : '');
        $('#subCatLeft').text(subcat1 ? subcat1 : '');
        $('#subCatRight').text(subcat2 ? subcat2 : '');
      }

      function sendData(response,time) {
        $.ajax({
          type:"POST",
          url:"dataHandler.php",
          data:{"response":response,"rt":time,"subj":subj,"stim":stimuliData[groupNum].stimulus[wordNum-1].stim_id},
          success:function (data, textStatus, XMLHttpRequest) {
            successfulResponses++;
            responses++;
            if (responses >= totalStimuli) {location.href="processing.php";}
          },
          error:function (XMLHttpRequest, textStatus, errorThrown) {
            responses++;
            alert("Server error: " + textStatus + "\nerror: " + errorThrown);
          }
        });
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
      .center {
        text-align: center;
      }
    </style>
  </head>
  <body onkeydown="detect_keydown(event);" onload="load()">
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
            <span class="center" id="wordSpace"><img src="ajaxloader.gif" id="ajaxImage"><h1 id="word"></h1></span>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>
