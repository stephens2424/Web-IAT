/*!
 * WebIAT
 * shihlabs.xtreemhost.com
 * Lab of Margaret Shih, UCLA Anderson School of Management
 * Copyright 2011, All Rights Reserved
 * 
 * Author: Stephen Searles
 * Date: May 10, 2011
 */
if (typeof Object.create !== 'function') {
    Object.create = function (o) {
        function F() {}
        F.prototype = o;
        return new F();
    };
}
(function( window, undefined ) {
var IAT = (function() {
  var IAT = function (experimentNumber,callback) {
    return requestExperiment(experimentNumber,callback);
  }
  //Server Upload Connection functions
  function bundleIATManagerRequestData(requestName, dataObject) {
    return {"requestName":requestName,"data":dataObject};
  }
  function sendRequest(requestObject) {
    return $.post('IATManager.php',requestObject);
  }
  
  //IAT Static functions
  IAT.addExperiment = function() {
    return sendRequest(bundleIATManagerRequestData("addExperiment",null));
  }
  IAT.requestExperimentList = function() {
    return sendRequest(bundleIATManagerRequestData("requestExperimentList",null));
  }
  
  //experiment constructor
  var ExperimentPrototype = {
      //data
      experimentNumber : null,
      stimuliGroups :  null,
      stimulusCategories : null,
      //manipulation functions
      removeExperiment : function(experimentNumber) {
        return sendRequest(bundleIATManagerRequestData("removeExperiment",{
          'experimentNumber' : experimentNumber,
          'data' : null
        }));
      },
      copyExperiment : function(experimentNumber) {
        return sendRequest(bundleIATManagerRequestData("copyExperiment",{
          'experimentNumber' : experimentNumber,
          'data' : null
        }));
      },
      setExperimentProperties : function(experimentNumber,dataObject) {
        return sendRequest(bundleIATManagerRequestData("setExperimentProperties",{
          'experimentNumber' : experimentNumber,
          'data' : dataObject
        }));
      },
      addStimulus : function(experimentNumber) {
        return sendRequest(bundleIATManagerRequestData("addStimulus",{
          'experimentNumber' : experimentNumber,
          'data' : null
        }));
      },
      removeStimulus : function(experimentNumber,dataObject) {
        return sendRequest(bundleIATManagerRequestData("removeStimulus",{
          'experimentNumber' : experimentNumber,
          'data' : null
        }));
      },
      insertStimulus : function(experimentNumber,index) {
        return sendRequest(bundleIATManagerRequestData("insertStimuli",{
          'experimentNumber' : experimentNumber,
          'data' : {
            "insertIndex":index
          }
        }));
      },
      moveStimulus : function(experimentNumber,dataObject) {
        return sendRequest(bundleIATManagerRequestData("moveStimulus",{
          'experimentNumber' : experimentNumber,
          'data' : dataObject
        }));
      },
      setStimulusProperties : function(experimentNumber,dataObject) {
        return sendRequest(bundleIATManagerRequestData("setStimulusProperties",{
          'experimentNumber' : experimentNumber,
          'data' : dataObject
        }));
      },
      addStimulusGroup : function(experimentNumber) {
        return sendRequest(bundleIATManagerRequestData("addStimulusGroup",{
          'experimentNumber' : experimentNumber,
          'data' : null
        }));
      },
      removeStimulusGroup : function(experimentNumber,dataObject) {
        return sendRequest(bundleIATManagerRequestData("removeStimulusGroup",{
          'experimentNumber' : experimentNumber,
          'data' : dataObject
        }));
      },
      insertStimulusGroup : function(experimentNumber,dataObject) {
        return sendRequest(bundleIATManagerRequestData("insertStimulusGroup",{
          'experimentNumber' : experimentNumber,
          'data' : dataObject
        }));
      },
      moveStimulusGroup : function(experimentNumber,dataObject) {
        return sendRequest(bundleIATManagerRequestData("moveStimulusGroup",{
          'experimentNumber' : experimentNumber,
          'data' : dataObject
        }));
      },
      copyStimulusGroup : function(experimentNumber,dataObject) {
        return sendRequest(bundleIATManagerRequestData("copyStimulusGroup",{
          'experimentNumber' : experimentNumber,
          'data' : dataObject
        }));
      },
      setStimulusGroupProperties : function(experimentNumber,dataObject) {
        return sendRequest(bundleIATManagerRequestData("setStimulusGroupProperties",{
          'experimentNumber' : experimentNumber,
          'data' : dataObject
        }));
      },
      addStimulusCategory : function(experimentNumber,name) {
        return sendRequest(bundleIATManagerRequestData("addStimulusCategory",{
          'experimentNumber' : experimentNumber,
          'data' : {
            "name" : name
          }
        }));
      },
      removeStimulusCategory : function(experimentNumber,index) {
        return sendRequest(bundleIATManagerRequestData("removeStimulusCategory",{
          'experimentNumber' : experimentNumber,
          'data' : {
            "index" : index
          }
        }));
      },
      generateStimuliTable : function() {
        var $table = $('<table>').attr('id','stimuliGroupTable').addClass('stimuliGroupTable');
        for (var group in this.stimuliGroups) {
          $table.append(this.groupRowFromObject(this.stimuliGroups[group]));
        }
        return $table;
      },
      groupRowFromObject : function(group) {
        var $row = $('<tr>');
        var $cell = $('<td>');
        var $innerTable = $('<table>').addClass('stimuliTable');
        var $tableHeader = $('<thead>');
        $tableHeader.append($(DISCLOSURE_HEADER_CELL_STRING));
        var $stimuliHeader = $('<th>').addClass('stimuliHeader');
        $stimuliHeader.text(group.name);
        $tableHeader.append($stimuliHeader);
        $tableHeader.append($('<th>').addClass('greenwaldHeader').text('greenwald selector'));
        $tableHeader.append($('<th>').addClass('groupActionHeader').text('action selector'));
        $tableHeader.append($('<th>').addClass('randomization').text('randomization box'));
        $innerTable.append($tableHeader);
        for (var stimulus in group.stimuli) {
          $innerTable.append(this.stimulusRowFromObject(group.stimuli[stimulus]));
        }
        $cell.append($innerTable);
        $row.append($cell);
        return $row;
      },
      stimulusRowFromObject : function(stimulus) {
        var $row = $('<tr>');
        var $emptyShifterCell = $('<td>');
        var $stimulusEditButtonCell = $('<td>').append('TODO - edit button');
        var $stimulusDataCell = this.stimulusDataTableFromObject(stimulus);
        var $stimulusActionsCell = $('<td>').append('TODO - stimulus actions');
        $row.append($emptyShifterCell).append($stimulusDataCell).append($stimulusEditButtonCell).append($stimulusActionsCell);
        return $row;
      },
      stimulusDataTableFromObject : function(stimulus) {
        var $table = $('<table>').addClass('stimulusDataTable');
        var $topRow = $('<tr>');
        var $middleRow = $('<tr>');
        var $bottomRow = $('<tr>');
        
        var $cat1cell = $('<td>').addClass('stimulusDataCell').addClass('leftCategoryCell').addClass('topCategoryCell');
        $cat1cell.text(this.categoryNameFromId(stimulus.category1));
        var $cat2cell = $('<td>').addClass('stimulusDataCell').addClass('rightCategoryCell').addClass('topCategoryCell');
        $cat2cell.text(this.categoryNameFromId(stimulus.category2));
        var $subcat1cell = $('<td>').addClass('stimulusDataCell').addClass('leftCategoryCell').addClass('bottomCategoryCell');
        $subcat1cell.text(this.categoryNameFromId(stimulus.subcategory1));
        var $subcat2cell = $('<td>').addClass('stimulusDataCell').addClass('rightCategoryCell').addClass('bottomCategoryCell');
        $subcat2cell.text(this.categoryNameFromId(stimulus.subcategory2));
        var $wordCell = $('<td colspan="2">').addClass('stimulusDataCell').addClass('wordCell');
        $wordCell.text(stimulus.word);
        
        $topRow.append($cat1cell).append($cat2cell);
        $middleRow.append($subcat1cell).append($subcat2cell);
        $bottomRow.append($wordCell);
        $table.append($topRow).append($middleRow).append($bottomRow);
        return $table;
      },
      //translations
      groupIdFromIndex : function(index) {
        return this.stimuliGroups[index].id;
      },
      categoryNameFromId : function(id) {
        if (id === "0" | id === null | id === undefined) return "";
        else return this.stimulusCategories[id];
      }
  };
  
  const DISCLOSURE_HEADER_CELL_STRING = '<th class="disclosure">d</th>';

  function requestExperiment(experimentNumber,callback) {
    var experimentPromise = $.Deferred().done(callback);
    var experiment = Object.create(ExperimentPrototype,{
      'experimentNumber' : {
        value : experimentNumber,
        writable : false
      },
      'experimentPromise' : {
        value : experimentPromise,
        writable : true
      }
    });
    sendRequest(bundleIATManagerRequestData('requestExperiment',experimentNumber,null)).success(function (receivedData) {
      var data = JSON.parse(receivedData);
      experiment.hash = data.hash;
      experiment.name = data.name;
      experiment.active = data.active;
      experiment.endUrl = data.endUrl;
      experiment.secondEndUrl = data.secondEndUrl;
      experiment.stimuliGroups = data.stimuliGroups;
      experiment.stimulusCategories = data.stimulusCategories;
      experimentPromise.resolve();
    });
    return experiment;
  }
  return IAT;
})();
window.IAT = IAT;
})(window);
