<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">
      var stimuliData;
      var set;
      var groupOptions = {
        def : "Group Actions",
        rename : "Rename Group",
        addAbove : "Add Group Above",
        addBelow : "Add Group Below",
        remove : "Remove Group"
      };
      function requestStimuliSet () {
        $.ajax({
          url:"requestStimuliSet.php",
          type:"POST",
          data:{
            stim_set:set
          },
          success:function (received_data, textStatus, XMLHttpRequest) {
            var data = JSON.parse(received_data);
            _parseEndURLAndSetSelectBox(data.endURL);
            $('#responseCount').text(data.responseCount);
            if (data.stimuliGroups) {
              var num = data.stimuliGroups.length;
              stimuliData = data.stimuliGroups;
              for (var i=0; i < num; i++) {
                insertGroup(i,data.stimuliGroups[i].groupName,data.stimuliGroups[i].stimuli,data.stimuliGroups[i].randomize,data.stimuliGroups[i].group_id);
              }
            } else {
              addNoGroup();
            }
          },
          error:function (XMLHttpRequest, textStatus, errorThrown) {
            alert("Error requesting stimuli data. Please check your network connection.");
          }
        });
      }
      function _parseEndURLAndSetSelectBox(url) {
        url.toLowerCase();
        if (url === "thankyou.php") {
          $('#end_of_experiment_selector').attr('selectedIndex',0).change();
        } else if (url === "results.php") {
          $('#end_of_experiment_selector').attr('selectedIndex',4).change();
        } else {
          if (url.substr(0,7) === "http://") {
            if (url.substr(0,34) === "http://ucla.qualtrics.com/SE/?SID=") {
              $('#end_of_experiment_selector').attr('selectedIndex',1).change();
              $('#end_of_experiment_zone').children('input').attr('value',url.substr(34));
            } else {
              $('#end_of_experiment_selector').attr('selectedIndex',3).change();
              $('#end_of_experiment_zone').children('input').attr('value',url.substr(7));
            }
          } else {
            $('#end_of_experiment_selector').attr('selectedIndex',2).change();
          }
        }
      }
      function addNoGroup () {
        $('#stimuliBody').append($('<tr>').click(function() {replaceNoGroup($(this))}).append($('<td>No groups. Click here to add one.</td>').attr("colspan",4)));
      }
      function replaceNoGroup ($row) {
        $.post("insertGroup.php",{
          set:set,
          position:0,
          below:"false"
        },function(receivedData) {
          var data = JSON.parse(receivedData);
          insertGroup(0,data.name,data.stimuli,data.randomize,data.group_id);
          $row.remove();
          stimuliData = new Array(data);
        });
      }
      function insertGroup (atPosition,name,content,randomize,groupId) {
        var $row = $('#stimuliBody').children().eq(atPosition);
        var newRow = _createGroupRow(name,content,randomize,groupId,atPosition);
        if ($row.size() <= atPosition) {
          $('#stimuliBody').append(newRow);
        } else {
          $row.before(newRow);
        }
      }
      function _createGroupRow (name,content,randomize,groupId,groupNum) {
        var body = $('<tr>').appendTo($('<tbody>')).append('<td>').append($('<td>').attr('colspan','2').append(_createGroupContent(content)));
        var disclose = $('<input>').attr('type','image').click(function () {discloseGroupToggle(body)}).attr('src','disclosureTriangle.png');
        var $actions = _createGroupActionsSelectBox();
        var head = $('<thead>').append($('<th>').append(disclose)).append('<th>' + name + '</th>').append($('<th>' + groupId + '</th>').attr('style','display:none')).append($('<th>').append($actions)).append($("<th>").append($('<input>').attr("type","checkbox").attr("checked",((randomize === "1") ? true : false)).click(function () {toggleGroupRandomization(groupNum,groupId)})).append(" Randomize"));
        var table = $('<table>').append(head).append(body);
        var tableRow = $('<tr>').append(table);
        return tableRow;
      }
      function _createGroupActionsSelectBox() {
        var $actions = $('<select>');
        $.each(groupOptions,function (val,text) {
          $actions.append(
          $('<option></option>').val(val).html(text)
        );
        });
        $actions.change(function () {handleGroupAction(this);});
        return $actions;
      }
      function _createGroupContent (content) {
        var table = $('<table>');
        if (content.length > 0) {
          for (var i = 0; i < content.length; i++) {
            var tr_elem = _createStimulusRow(content[i]);
            $(table).append($(tr_elem));
          }
        } else {
          addNoStimuliNoticeRow(table);
        }
        return table;
      }
      function toggleGroupRandomization(groupNum,groupId) {
        $.post("toggleGroupRandomization.php",{
          group_id:groupId,
          randomize:(stimuliData[groupNum].randomize === "0" ? "1" : "0")
        });
        stimuliData[groupNum].randomize = stimuliData[groupNum].randomize === "0" ? "1" : "0";
      }
      function removeGroup($groupRow) {
        $.post(
        "removeGroup.php",{
          group:stimuliData[$groupRow.index()].group_id
        }
      );
        stimuliData.splice($groupRow.index(),1);
        $groupRow.remove();
        if ($('#stimuliBody').children('tr').length === 0) {
          addNoGroup();
        }
      }
      function replaceRowWithNewStimulus($row) {
        $.post("addNewStimulus.php",{
          set:set,
          group:parseInt($row.parents('table').eq(1).find('th').eq(2).text(),10)
        }, function (received_data) {
          var data = JSON.parse(received_data)[0];
          stimuliData[$row.parents('table').eq(1).index()].stimuli.splice(0,0,data);
          $row.replaceWith(createStimulusRow(data.stim_id,data.category1,data.category2,data.subcategory1,data.subcategory2,data.word,data.correct_response,data.instruction));
        });
      }
      function makeGroupNameEditable($groupRow) {
        //change name into text box
        $textCell = $groupRow.children().eq(1);
        var text = $textCell.text();
        $textCell.html($('<input>').attr("type","text").attr("value",text));
        //change actions menu into save button
        $buttonCell = $groupRow.children().eq(3);
        $saveButton = $('<button>').click(function () {saveGroupName($groupRow);}).html("Save");
        $buttonCell.children('select').replaceWith($saveButton);
      }
      function saveGroupName($groupRow) {
        var name = $groupRow.find('input').eq(1).attr("value");
        //replace disclosure triangle with spinning wheel
        //make all the components disabled
        //post to database
        $.post("renameGroup.php",{
          groupId:$groupRow.children().eq(2).text(),
          name:name
        },function (receivedData){
          //restore disclosure triangle and other components upon success
          $groupRow.find('button').replaceWith(_createGroupActionsSelectBox());
          $groupRow.find('input').eq(1).replaceWith(name);
        });
      }
      function handleGroupAction(selectBox) {
        switch (selectBox.selectedIndex) {
          case 0:
            selectBox.selectedIndex = 0;
            break;
          case 1: {
              makeGroupNameEditable($(selectBox).parent().parent());
              selectBox.selectedIndex = 0;
              break;
            }
          case 2:
            $.post("insertGroup.php",{
              set:set,
              position:$(selectBox).parent().parent().index(),
              below:"false"
            },function(receivedData) {
              var data = JSON.parse(receivedData);
              insertGroup($(selectBox).parent().parent().index(),data.name,data.stimuli,data.randomize,data.group_id);
              stimuliData.splice($(selectBox).parent().parent().index(),0,data);
            });
            selectBox.selectedIndex = 0;
            break;
          case 3:
            $.post("insertGroup.php",{
              set:set,
              position:$(selectBox).parent().parent().index(),
              below:"true"
            },function(receivedData) {
              var data = JSON.parse(receivedData);
              insertGroup($(selectBox).parent().parent().index()+1,data.name,data.stimuli,data.randomize,data.group_id);
              stimuliData.splice($(selectBox).parent().parent().index()+1,0,data);
            });
            selectBox.selectedIndex = 0;
            break;
          case 4:
            removeGroup($(selectBox).closest('tr'));
            selectBox.selectedIndex = 0;
            break;
          default:
            selectBox.selectedIndex = 0;
            break;
        }
      }
      function _createStimulusRow (data) {
        return createStimulusRow(data.stim_id,data.category1,data.category2,data.subcategory1,data.subcategory2,data.word,data.correct_response,data.instruction);
      }
      function discloseGroupToggle($groupTable) {
        $groupTable.toggleClass("hidden");
      }
      function save_stimulus_row (stimuliRow) {
        var $row = $(stimuliRow)
        var $table = $row.find('table');
        $.ajax({
          type:"POST",
          url:"updateStimulus.php",
          data:{
            leftCategory:$table.find('input').eq(0).val(),
            rightCategory:$table.find('input').eq(2).val(),
            subLeftCategory:$table.find('input').eq(6).val(),
            subRightCatebory:$table.find('input').eq(7).val(),
            word:$table.find('input').eq(1).val(),
            mask:$table.find('input').eq(5).attr('checked') === true ? 1 : 0,
            stim_id:$row.closest('tr').find('img').attr('alt'),
            correct:$table.find('input[name="correct"]').eq(0).attr('checked') == true ? '0' : ($table.find('input[name="correct"]').eq(1).attr('checked') == true ? '1' : 'NULL')
          },
          success:function (data, textStatus, XMLHttpRequest) {
            var stimuli = JSON.parse(data);
            i = 0;
            $row.replaceWith(createStimulusRow(stimuli[i].stim_id,stimuli[i].category1,stimuli[i].category2,stimuli[i].subcategory1,stimuli[i].subcategory2,stimuli[i].word,stimuli[i].correct_response,stimuli[i].instruction),stimuliRow);
          },
          error:function (XMLHttpRequest, textStatus, errorThrown) {
            alert("Server request failed. Please check your network settings.");
          }
        });
      }
      function createStimulusRow(stim_id,cat1,cat2,subcat1,subcat2,word,correct,instruction) {
        var stimulusRow = document.createElement('tr');

        // id cell
        var idCell = stimulusRow.insertCell(0);
        idCell.appendChild(document.createTextNode(stim_id));

        //stimulus cell
        var stimulusCell = stimulusRow.insertCell(1);
        $(stimulusCell).append($(createStimulusTable(cat1,cat2,subcat1,subcat2,word,correct,instruction)));

        // edit cell
        var editCell = stimulusRow.insertCell(2);
        var button = document.createElement('button');
        $(button).click(make_row_editable);
        button.innerHTML = "Edit";
        editCell.appendChild(button);

        //add remove cell
        var selectBoxCell = stimulusRow.insertCell(3);
        var selectBox = document.createElement('select');
        var noOption = document.createElement('option');
        noOption.appendChild(document.createTextNode("Actions"));
        var removeOption = document.createElement('option');
        removeOption.appendChild(document.createTextNode("Remove"));
        var addAboveOption = document.createElement('option');
        addAboveOption.appendChild(document.createTextNode("Add Row Above"));
        var addBelowOption = document.createElement('option');
        addBelowOption.appendChild(document.createTextNode("Add Row Below"));
        var copyOption = document.createElement('option');
        copyOption.appendChild(document.createTextNode("Copy"));
        selectBox.appendChild(noOption);
        selectBox.appendChild(removeOption);
        selectBox.appendChild(addAboveOption);
        selectBox.appendChild(addBelowOption);
        selectBox.onchange = function () {handleRowAction(selectBox);};
        selectBoxCell.appendChild(selectBox);
        return stimulusRow;
      }
      function handleRowAction (selectBox) {
        switch (selectBox.selectedIndex) {
          case 0:
            alert("no option");
            break;
          case 1:
            remove_row(selectBox.parentNode.parentNode);
            break;
          case 2:
            $.post("insertNewStimulus.php",{
              below:false,
              position:$(selectBox).parent().parent().index(),
              stim_set:set,
              group:selectBox.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.childNodes[0].childNodes[2].textContent
            }, function (received_data) {
              var data = JSON.parse(received_data)[0];
              stimuliData[$(selectBox).parent().parent().parent().parent().parent().parent().parent().parent().index()].stimuli.splice($(selectBox).parent().parent().index()-1,0,data);
              $(selectBox).parent().parent().before(createStimulusRow(data.stim_id,data.category1,data.category2,data.subcategory1,data.subcategory2,data.word,data.correct_response,data.instruction));
            });
            selectBox.selectedIndex = 0;
            break;
          case 3:
            $.post("insertNewStimulus.php",{
              below:true,
              position:$(selectBox).parent().parent().index(),
              stim_set:set,
              group:selectBox.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.childNodes[0].childNodes[2].textContent
            }, function (received_data) {
              var data = JSON.parse(received_data)[0];
              stimuliData[$(selectBox).parent().parent().parent().parent().parent().parent().parent().parent().index()].stimuli.splice($(selectBox).parent().parent().index(),0,data);
              $(selectBox).parent().parent().after(createStimulusRow(data.stim_id,data.category1,data.category2,data.subcategory1,data.subcategory2,data.word,data.correct_response,data.instruction));
            });
            selectBox.selectedIndex = 0;
            break;
          case 4:
            alert("copy");
            selectBox.selectedIndex = 0;
            break;
          default:
            selectBox.selectedIndex = 0;
        }
      }
      function remove_row (row) {
        var stim_id;
        if (row.childNodes[0].childNodes[0].alt) {
          stim_id = row.childNodes[0].childNodes[0].alt;
        } else {
          stim_id = row.childNodes[0].childNodes[0].textContent;
        }
        var poststr = "&stim_id=" + stim_id;
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
        xmlhttp.open("POST","removeStimulus.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(poststr);
        stimuliData[$(row).parent().parent().parent().parent().parent().parent().index()].stimuli.splice($(row).index(),1);
        $table = $(row).closest('table');
        row.parentNode.removeChild(row);
        if ($table.length === 1) {
          addNoStimuliNoticeRow($table);
        }
      }
      function addNoStimuliNoticeRow($table) {
        $row = $('<tr>');
        $row.click(function() {replaceRowWithNewStimulus($row)});
        $table.append($row.append($('<td>Empty Group. Click here to add a stimulus</td>').attr("colspan",4)));
      }
      function createStimulusTable (cat1,cat2,subcat1,subcat2,word,correct,instruction) {
        if (instruction == null || instruction == '') {
          var $table = $('<table>'), $row0 = $('<tr>'), $row1 = $('<tr>'), $row2 = $('<tr>');
          var $t0x0 = $('<td>').text(cat1);
          var $t0x1 = $('<td>').text(word).attr('rowspan',2);
          var $t0x2 = $('<td>').text(cat2);
          var $t1x0 = $('<td>').text(subcat1);
          var $t1x2 = $('<td>').text(subcat2);
          var $t2x0 = $('<td>').append($('<input>').attr('type','radio').attr('name','correct').attr('value','0').attr('disabled','true'));
          var $t2x1 = $('<td>').text("Correct");
          var $t2x2 = $('<td>').append($('<input>').attr('type','radio').attr('name','correct').attr('value','1').attr('disabled','true'));
          if (correct === '0') {
            $t2x0.children('input').attr('checked','true');
          } else if (correct === '1') {
            $t2x2.children('input').attr('checked','true');
          }
          $row0.append($t0x0).append($t0x1).append($t0x2);
          $row1.append($t1x0).append($t1x2);
          $row2.append($t2x0).append($t2x1).append($t2x2);
          $table.append($row0).append($row1).append($row2);
          return $table.get();
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
        maskingButton.checked = (stimuliData[$(row).parent().parent().index()].stimuli[$(row).index()].mask == "0") ? false : true;
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
      function insert_row(index) {
        alert("insert row at " + index);
      }
      function make_row_editable() {
        //TODO make this work for instruction rows. also. make it possible to switch.
        var $stimulusTable = $(this).closest('table').find('table');
        var row = 0;
        if ($stimulusTable.find('tr').length > 0) {
          while (row < 2) {
            var cell = 0;
            while (cell < $stimulusTable.find('tr').eq(row).find('td').length) {
              var text = $stimulusTable.find('tr').eq(row).find('td').eq(cell).text();
              $stimulusTable.find('tr').eq(row).find('td').eq(cell).text('');
              var $elem = $('<input>').attr('type','text').val(text);
              $stimulusTable.find('tr').eq(row).find('td').eq(cell).append($elem);
              cell++;
            }
            row++;
          }
          $stimulusTable.find('input[name="correct"]').removeAttr('disabled');
        } else {
          var text = $stimulusTable.text();
          var elem = document.createElement('input');
          elem.type = 'text';
          elem.value = text;
          $stimulusTable.parent().append(elem);
          $stimulusTable.remove();
        }
        addOptionsCell($stimulusTable.get(0));
        var $button = $stimulusTable.closest('tr').find('button').eq(0);
        $button.unbind('click').click(make_row_uneditable).html("Save");
      }
      function make_row_uneditable(evt) {
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
          while (row < 2) {
            var cell = 0;
            while (cell < stimulusTable.rows[row].cells.length) {
              stimulusTable.rows[row].cells[cell].childNodes[0].disabled = true;
              cell++;
            }
            row++;
          }
          $(stimulusTable).find('input[name="correct"]').attr('disabled','true');
        } else {
          stimulusTable.disabled = true;
        }
        
        save_stimulus_row(stimuliRow);
      }
      function remove_all_stimuli() {
        $('#stimuliBody').replaceWith('<tbody id="stimuliBody"></tbody>');
      }
      function experiment_change() {
        remove_all_stimuli();
        var selectBox = document.getElementById("experiment_selector");
        set = selectBox.options[selectBox.selectedIndex].value;
        if (set === "default") {
          $('#experiment_action_selector').attr("disabled","true");
          $('#end_of_experiment_selector').attr("disabled","true");
        } else if (set === "new experiment") {
          new_experiment();
        } else {
          requestStimuliSet();
          $('#experiment_action_selector').removeAttr("disabled");
          $('#end_of_experiment_selector').removeAttr("disabled");
        }
      }
      function makeExperimentNameEditable() {
        var oldName = $('#experiment_selector :selected').text();
        var newName = prompt("New experiment name:",$('#experiment_selector :selected').text());
        $('#experiment_selector :selected').text(newName);
        $.ajax({
          url:"renameExperiment",
          type:'POST',
          data:{
            name:newName,
            experiment:$('#experiment_selector').val()
          },
          error:function (XMLHttpRequest, textStatus, errorThrown) {
            $('#experiment_selector :selected').text(oldName);
            alert("Experiment rename failed. Please check your network settings.");
          }
        });
      }
      function new_experiment() {
        $('#experiment_selector').attr("disabled","true");
        $('#experiment_action_selector').attr("disabled","true");
        var newName = prompt("New experiment name:","New Experiment");
        while (newName === "Make New Experiment") {
          newName = prompt("Invalid experiment name. Please choose a new experiment name:","New Experiment");
        }
        $.ajax({
          url:"newExperiment.php",
          type:'POST',
          data:{
            name:newName
          },
          success:function (data, textStatus, XMLHttpRequest) {
            $newOption = $('<option>').val(data).text(newName).insertBefore($("#experiment_selector").children('option').last()).attr('selected','selected');
            $("#experiment_selector").removeAttr("disabled");
            $('#experiment_action_selector').removeAttr("disabled");
            experiment_change();
          },
          error:function (XMLHttpRequest, textStatus, errorThrown) {
            $("#experiment_selector").removeAttr("disabled");
            $('#experiment_action_selector').removeAttr("disabled");
            alert("Creating experiment failed. Please check your network settings.");
          }
        });
      }
      function handle_experiment_action() {
        switch ($('#experiment_action_selector').attr("selectedIndex")) {
          case 0: {
              $('#experiment_action_selector').attr("selectedIndex","0");
              break;
            }
          case 1: {//rename
              makeExperimentNameEditable();
              $('#experiment_action_selector').attr("selectedIndex","0");
              break;
            }
          case 2: {//delete experiment
              var c = confirm("Are you sure you want to delete this experiment?\nThis action cannot be undone.");
              if (c === true) {
                $.post("deleteExperiment.php",{
                  experiment:set
                },function (receivedData) {
                  $("#experiment_selector").children().eq($("#experiment_selector").attr('selectedIndex')).remove();
                  $("#experiment_selector").attr('selectedIndex','0');
                  experiment_change();
                });
              }
              $('#experiment_action_selector').attr("selectedIndex","0");
              break;
            }
          default: {
              $('#experiment_action_selector').attr("selectedIndex","0");
              break;
            }
        }
      }
      function handle_end_of_experiment_change() {
        switch ($('#end_of_experiment_selector').attr('selectedIndex')) {
          case 0: {
              $('#end_of_experiment_zone').text("");
              $('#end_of_experiment_zone').append($('<img>').attr('src','ajaxloader.gif'));
              $.ajax({
                type:"POST",
                url:"updateExperimentEndURL.php",
                data:{
                  newURL:"results.php",
                  set:set
                },
                success:function (data, textStatus, XMLHttpRequest) {
                  $('#end_of_experiment_zone').children('img').remove();
                },
                error:function (XMLHttpRequest, textStatus, errorThrown) {
                  $('#end_of_experiment_zone').children('img').remove();
                  alert("Error. Please check your network settings.");
                }
              });
              break;
            }
          case 1: {
              $('#end_of_experiment_zone').text("http://ucla.qualtrics.com/SE/?SID=");
              var $box = $('<input>').attr('type','text');
              $box.change(function () {
                $box.after($('<img>').attr('src','ajaxloader.gif'));
                $.ajax({
                  type:"POST",
                  url:"updateExperimentEndURL.php",
                  data:{
                    newURL:"http://ucla.qualtrics.com/SE/?SID=" + $box.attr('value'),
                    set:set
                  },
                  success:function (data, textStatus, XMLHttpRequest) {
                    $box.siblings('img').remove();
                  },
                  error:function (XMLHttpRequest, textStatus, errorThrown) {
                    $box.siblings('img').remove();
                    alert("Error. Please check your network settings.");
                  }
                });
              });
              $('#end_of_experiment_zone').append($box);
              break;
            }
          case 2: {
              $('#end_of_experiment_zone').text("This option not yet implemented. Will default to Thank You page");
              break;
            }
          case 3: {
              $('#end_of_experiment_zone').text("http://");
              var $box = $('<input>').attr('type','text');
              $box.change(function () {
                $box.after($('<img>').attr('src','ajaxloader.gif'));
                $.ajax({
                  type:"POST",
                  url:"updateExperimentEndURL.php",
                  data:{
                    newURL:"http://" + $box.attr('value'),
                    set:set
                  },
                  success:function (data, textStatus, XMLHttpRequest) {
                    $box.siblings('img').remove();
                  },
                  error:function (XMLHttpRequest, textStatus, errorThrown) {
                    $box.siblings('img').remove();
                    alert("Error. Please check your network settings.");
                  }
                });
              });
              $('#end_of_experiment_zone').append($box);
              break;
            }
          case 4: {
              $('#end_of_experiment_zone').text("");
              $('#end_of_experiment_zone').append($('<img>').attr('src','ajaxloader.gif'));
              $.ajax({
                type:"POST",
                url:"updateExperimentEndURL.php",
                data:{
                  newURL:"results.php",
                  set:set
                },
                success:function (data, textStatus, XMLHttpRequest) {
                  $('#end_of_experiment_zone').children('img').remove();
                },
                error:function (XMLHttpRequest, textStatus, errorThrown) {
                  $('#end_of_experiment_zone').children('img').remove();
                  alert("Error. Please check your network settings.");
                }
              });
              break;
            }
          }
        }
    </script>
    <style type="text/css">
      .hidden {
        display: none;
      }
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
    <fieldset>
      <legend>
        <select id="experiment_selector" onchange="experiment_change();">
          <option value="default">Please choose an experiment to begin</option>
          <?php
            include 'connect.php';
            $query = "SELECT name,stimuli_set FROM experiments";
            $result = mysql_query($query);
            $num = mysql_num_rows($result);
            $i = 0;
            while ($i < $num) {
              $name = mysql_result($result, $i, "name");
              $set = mysql_result($result, $i, "stimuli_set");
              echo "<option value=\"$set\">$name - ID:$set</option>";
              $i++;
            }
            mysql_free_result($result);
            mysql_close();
          ?>
          <option value="new experiment">Make New Experiment</option>
        </select>
      </legend>
      <select id="experiment_action_selector" onchange="handle_experiment_action()">
        <option>Experiment Actions</option>
        <option>Rename Experiment</option>
        <option>Delete Experiment</option>
      </select>
      End of experiment action:
      <select id="end_of_experiment_selector" onchange="handle_end_of_experiment_change()">
        <option>Thank you page</option>
        <option>Link to Qualtrics</option>
        <option>Upload Page</option>
        <option>Custom URL</option>
        <option>Results Page (Recommended for development only)</option>
      </select><span id="end_of_experiment_zone"></span>
      <p>
        Active: <span id="active"></span><br>
        Responses: <span id="responseCount"></span>
      </p>
    </fieldset>
    <fieldset><legend>Stimuli</legend>
      <div id="stimuliList">
        <table id="stimuliTable" style="border-width:2px; border-color:black;">
          <tbody id="stimuliBody"></tbody>
        </table>
      </div>
    </fieldset>
  </body>
</html>
