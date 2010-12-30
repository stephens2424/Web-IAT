<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <script type="text/javascript">
      var maskArray;
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
              var data = JSON.parse(this.responseText);
              document.getElementById("responseCount").innerHTML = data.responseCount;
              var stimuli = data.stimuli;
              var num = stimuli.length;
              maskArray = new Array(num);
              for (var i=0; i < num; i++) {
                addStimulusRow(stimuli[i].stim_id,stimuli[i].category1,stimuli[i].category2,stimuli[i].subcategory1,stimuli[i].subcategory2,stimuli[i].word,stimuli[i].correct_response,stimuli[i].instruction);
                maskArray[i] = stimuli[i].mask;
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
      function save_stimulus_row (stimuliRow) {
        var stimulusTable = stimuliRow.childNodes[1].childNodes[0];
        var poststr;
        if (stimulusTable.rows) {
          poststr = "leftCategory=" + encodeURI( stimulusTable.rows[0].cells[0].lastChild.value ) +
            "&rightCategory=" + encodeURI( stimulusTable.rows[0].cells[2].lastChild.value ) +
            "&subLeftCategory=" + encodeURI( stimulusTable.rows[1].cells[0].lastChild.value ) +
            "&subRightCategory=" + encodeURI( stimulusTable.rows[1].cells[1].lastChild.value ) +
            "&word=" + encodeURI( stimulusTable.rows[0].cells[1].lastChild.value ) +
            "&mask=" + encodeURI( (stimulusTable.rows[0].cells[3].childNodes[7].checked == true)?"1":"0") +
            "&stim_id=" + encodeURI(stimuliRow.childNodes[0].childNodes[0].alt);
        } else {
          poststr = "instruction=" + encodeURI( stimulusTable.textContent) +
            "&stim_id=" + encodeURI(stimuliRow.childNodes[0].childNodes[0].alt);
        }
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
              i = 0;
              stimuliRow.parentNode.replaceChild(createStimulusRow(stimuli[i].stim_id,stimuli[i].category1,stimuli[i].category2,stimuli[i].subcategory1,stimuli[i].subcategory2,stimuli[i].word,stimuli[i].correct_response,stimuli[i].instruction),stimuliRow);
              maskArray[stimuli[i].stim_id-1] = stimuli[i].mask;
            }
          }
        };
        xmlhttp.open("POST","updateStimulus.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(poststr);
      }
      function createStimulusRow(stim_id,cat1,cat2,subcat1,subcat2,word,correct,instruction) {
        var stimulusRow = document.createElement('tr');

        // id cell
        var idCell = stimulusRow.insertCell(0);
        idCell.appendChild(document.createTextNode(stim_id));

        //stimulus cell
        var stimulusCell = stimulusRow.insertCell(1);
        stimulusCell.appendChild(createStimulusTable(cat1,cat2,subcat1,subcat2,word,correct,instruction));

        // edit cell

        var editCell = stimulusRow.insertCell(2);
        var button = document.createElement('button');
        button.onclick = make_row_editable;
        button.innerHTML = "Edit";
        editCell.appendChild(button);
        return stimulusRow;
      }
      function createStimulusTable (cat1,cat2,subcat1,subcat2,word,correct,instruction) {
        if (instruction == null || instruction == '') {
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
          return stimulusTable;
        } else {
          return document.createTextNode(instruction);
        }
      }
      function changeStimulusType (row,type) {
        switch (type) {
          case 1: //IAT or Sequential Prime
            alert("changing to iat/sequential prime");
            break;
          case 2: //Instruction
            alert("changing to instruction");
            break;
        }
      }
      function addOptionsCell (table) {
        var row = table.rows[0];
        var cell = row.insertCell(-1);
        cell.rowSpan = "2";
        var iatButton = document.createElement('input');
        iatButton.onclick = function() {changeStimulusType(table,1)};
        iatButton.type = "radio";
        iatButton.name = "stimulusType";
        var instructionButton = document.createElement('input');
        instructionButton.onclick = function() {changeStimulusType(table,2)};
        instructionButton.type = "radio";
        instructionButton.name = "stimulusType";
        var maskingButton = document.createElement('input');
        maskingButton.type = "checkbox";
        maskingButton.name = "masking";
        maskingButton.checked = (maskArray[parseInt(table.parentNode.parentNode.childNodes[0].textContent)-1] == "0") ? false : true;
        cell.appendChild(iatButton);
        cell.appendChild(document.createTextNode("IAT/Sequential Prime"));
        cell.appendChild(document.createElement('br'));
        cell.appendChild(instructionButton);
        cell.appendChild(document.createTextNode("Instruction"));
        cell.appendChild(document.createElement('br'));
        cell.appendChild(document.createElement('br'));
        cell.appendChild(maskingButton);
        cell.appendChild(document.createTextNode("Mask"));
      }
      function addStimulusRow (stim_id,cat1,cat2,subcat1,subcat2,word,correct,instruction) {
        var stimuliTable = document.getElementById('stimuliBody');
        stimuliTable.appendChild(createStimulusRow(stim_id,cat1,cat2,subcat1,subcat2,word,correct,instruction));
      }
      function make_row_editable() {
        //TODO disable all other edit buttons
        //TODO make this work for instruction rows. also. make it possible to switch.
        var stimulusTable = this.parentNode.parentNode.childNodes[1].childNodes[0];
        var row = 0;
        if (stimulusTable.rows) {
          while (row < stimulusTable.rows.length) {
            var cell = 0;
            while (cell < stimulusTable.rows[row].cells.length) {
              var text = stimulusTable.rows[row].cells[cell].childNodes[0].textContent;
              stimulusTable.rows[row].cells[cell].removeChild(stimulusTable.rows[row].cells[cell].childNodes[0]);
              var elem = document.createElement('input');
              elem.type = 'text';
              elem.value = text;
              stimulusTable.rows[row].cells[cell].appendChild(elem);
              cell++;
            }
            row++;
          }
        } else {
          var text = stimulusTable.textContent;
          var elem = document.createElement('input');
          elem.type = 'text';
          elem.value = text;
          stimulusTable.parentNode.appendChild(elem);
          stimulusTable.parentNode.removeChild(stimulusTable);
        }
        addOptionsCell(this.parentNode.parentNode.childNodes[1].childNodes[0]);
        this.onclick = make_row_uneditable;
        this.innerHTML = "Save";
      }
      function make_row_uneditable(node) {
        //TODO reenable all edit buttons
        var stimuliRow = this.parentNode.parentNode;
        var loading = document.createElement('img');
        loading.alt = stimuliRow.childNodes[0].childNodes[0].textContent;
        stimuliRow.childNodes[0].removeChild(stimuliRow.childNodes[0].childNodes[0]);
        stimuliRow.childNodes[0].appendChild(loading);
        loading.src = "ajaxloader.gif";
        var stimulusTable = stimuliRow.childNodes[1].childNodes[0];
        var row = 0;
        if (stimulusTable.rows) {
          while (row < stimulusTable.rows.length) {
            var cell = 0;
            while (cell < stimulusTable.rows[row].cells.length) {
              stimulusTable.rows[row].cells[cell].childNodes[0].disabled = true;
              cell++;
            }
            row++;
          }
        } else {
          stimulusTable.disabled = true;
        }
        
        save_stimulus_row(stimuliRow);
      }
      function remove_all_stimuli() {
        if (document.getElementById("stimuliTable") == null) return;
        for(var i = document.getElementById("stimuliTable").rows.length; i > 1;i--)
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
    </select> Responses: <span id="responseCount"></span>
    <div id="stimuliList">
      <table id="stimuliTable" style="border-width:2px; border-color:black;">
        <thead><tr><th>id</th><th>Stimulus</th><th>Edit</th></tr></thead><tbody id="stimuliBody"></tbody>
      </table>
    </div>
  </body>
</html>
