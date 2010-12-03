<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <script type="text/javascript">
      function requestStimuliSet (parameters) {
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
            } else {
              var stimuli = JSON.parse(this.responseText);
              var num = stimuli.length;
              for (var i=0; i < num; i++) {
                addStimulusRow(stimuli[i].stim_id,stimuli[i].category1,stimuli[i].category2,stimuli[i].subcategory1,stimuli[i].subcategory2,stimuli[i].word,stimuli[i].correct_response,stimuli[i].instruction);
              }
            }
          }
        };
        xmlhttp.open("POST","requestStimuliSet.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.setRequestHeader("Content-length", parameters.length);
        xmlhttp.setRequestHeader("Connection", "close");
        xmlhttp.send(parameters);
      }
      function get(obj) {
        var poststr = "leftCategory=" + encodeURI( document.getElementById("categoryLeft").value ) +
          "&rightCategory=" + encodeURI( document.getElementById("categoryRight").value ) +
          "&subLeftCategory=" + encodeURI( document.getElementById("subCategoryLeft").value ) +
          "&subRightCategory=" + encodeURI( document.getElementById("subCategoryRight").value ) +
          "&word=" + encodeURI( document.getElementById("word").value );
        submitToHandler(poststr);
      }
      function addStimulusRow (stim_id,cat1,cat2,subcat1,subcat2,word,correct,instruction) {
        var stimuliTable = document.getElementById('stimuliBody');
        var stimulusRow = stimuliTable.insertRow(-1);

        // id cell
        var idCell = stimulusRow.insertCell(0);
        idCell.appendChild(document.createTextNode(stim_id));

        //stimulus cell
        var stimulusCell = stimulusRow.insertCell(1);
        if (instruction == null) {
          var stimulusTable = document.createElement('table');
          var row0 = stimulusTable.insertRow(-1);
          var row1 = stimulusTable.insertRow(-1);
          var cat1Cell = row0.insertCell(0);
          cat1Cell.appendChild(document.createTextNode(cat1));
          var middleCell = row0.insertCell(1);
          middleCell.rowSpan = 2;
          middleCell.appendChild(document.createTextNode(word));
          row0.insertCell(2).appendChild(document.createTextNode(cat2));
          row1.insertCell(0).appendChild(document.createTextNode(subcat1));
          row1.insertCell(1).appendChild(document.createTextNode(subcat2));
          stimulusCell.appendChild(stimulusTable);
        } else {
          stimulusCell.appendChild(document.createTextNode(instruction));
        }

        // edit cell
        
        var editCell = stimulusRow.insertCell(2);
        var button = document.createElement('button');
        button.innerHTML = "Edit";
        editCell.appendChild(button);
      }
      function remove_all_stimuli() {
        for(var i = document.getElementById("stimuliTable").rows.length; i > 0;i--)
        {
          document.getElementById("stimuliTable").deleteRow(i -1);
        }
      }
      function experiment_change() {
        remove_all_stimuli();
        var selectBox = document.getElementById("experiment_selector");
        requestStimuliSet("set=" + selectBox.options[selectBox.selectedIndex].value);
      }
    </script>
    <style type="text/css">
      table.center {
        width: 50%;
        height: 25%;
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
  <body onload="experiment_change();">
    <select id="experiment_selector" onchange="experiment_change();">
      <?php
        include 'connect.php';
        $query = "SELECT name,stimuli_set FROM experiments";
        $result = mysql_query($query);
        $num = mysql_num_rows($result);
        $i = 0;
        while ($i < $num) {
          $name = mysql_result($result, $i, "name");
          $set = mysql_result($result, $i, "stimuli_set");
          echo "<option value=\"$set\">$name</option>";
          $i++;
        }
        mysql_free_result($result);
        mysql_close();
      ?>
    </select>
    <div id="stimuliList">
      <table id="stimuliTable" style="border-width:2px; border-color:black;">
        <thead><tr><th>id</th><th>Stimulus</th><th>Edit</th></tr></thead><tbody id="stimuliBody"></tbody>
      </table>
    </div>
    <div id="stimuliForm" style="display:none;">
      <form action="javascript:get(document.getElementById('myform'));" name="iatForm">
        <table class="center">
          <tr>
            <td class="categoryLeft"> <!-- TODO change the styles -->
              <input type="text" name="categoryLeft" id="categoryLeft" />
            </td>
            <td class="categoryRight">
              <input type="text" name="categoryRight" id="categoryRight" />
            </td>
          </tr>
          <tr>
            <td class="categoryLeft">
              <input type="text" name="subCategoryLeft" id="subCategoryLeft" />
            </td>
            <td class="categoryRight">
              <input type="text" name="subCategoryRight" id="subCategoryRight" />
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="2">
              <input type="text" name="word" id="word" />
            </td>
          </tr>
          <tr><td colspan="2"></td><td><input type="button" name="button" value="Submit" onclick="javascript:get(this.parentNode);"></tr>
        </table>
      </form>
    </div>
    <div id="response">
    </div>
  </body>
</html>
