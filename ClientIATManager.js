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
      stimuliCategories : null,
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
        var $stimuliHeader = $('<th>').addClass('center').addClass('stimuliHeader');
        $stimuliHeader.text(group.name);
        $tableHeader.append($stimuliHeader);
        $tableHeader.append($('<th>').addClass('center').addClass('greenwaldHeader').text('greenwald selector'));
        $tableHeader.append($('<th>').addClass('center').addClass('groupActionHeader').text('action selector'));
        $tableHeader.append($('<th>').addClass('center').addClass('randomization').text('randomization box'));
        $innerTable.append($tableHeader);
        $cell.append($innerTable);
        $row.append($cell);
        return $row;
      },
      //index-id translations
      groupIdFromIndex : function(index) {
        return this.stimuliGroups[index].id;
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
      experiment.stimuliCategories = data.stimuliCategories;
      experimentPromise.resolve();
    });
    return experiment;
  }
  return IAT;
})();
window.IAT = IAT;
})(window);
