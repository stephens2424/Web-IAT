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
  var IATManager = {
    authenticate : function() {
      var authentication = Object.create({});
      authentication.promise = $.Deferred();
      var $authenticationBox = $('<div>').addClass('authenticationBox');
      var $authenticationDiv = $('<div>').addClass('innerAuthentication');
      var $form = $('<form id="loginForm" action="javascript:$.noop();">');
      var $username = $('<input type="text" name="username">');
      var $password = $('<input type="password" name="password">');
      $form.append($('<div>').append('Username: ').append($username));
      $form.append($('<div>').append('Password: ').append($password));
      $form.append($('<div>').append($('<input type="submit" value="Log in">')));
      $form.submit(function () {
        $form.find().each().prop('disabled',true);
        var username = $username.val();
        var password = $password.val();
        var passwordHash = hex_sha1(password);
        password = '';
        sendRequest(bundleIATManagerRequestData('authenticate',{
          username:username,
          passwordHash:passwordHash
        })).success(function (data) {
          var parsedData = JSON.parse(data);
          $form.append($('<div>').append(parsedData.authenticationMessage));
          authentication.data = parsedData;
          authentication.valid = parsedData.valid;
          authentication.promise.resolve();
          if (authentication.valid === true) $form.trigger('close');
        });
      });
      $authenticationDiv.append($form);
      $authenticationBox.append($authenticationDiv);
      $authenticationBox.lightbox_me();
      return authentication;
    },
    verifyAuthentication : function(authentication) {
      sendRequest(bundleIATManagerRequestData('verifyAuthentication',{
        authentication:authentication
      }));
    }
  };
  IAT.IATManager = function (experimentNumber,callback) {
    return requestExperimentWithAuthentication(experimentNumber,callback,IATManager.authenticate());
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
  
  //experiment constructors
  var Experiment = {
    //data
    experimentNumber : null,
    stimuliGroups :  null,
    stimulusCategories : null,
    authenticated: null,
    //translations
    groupIdFromIndex : function(index) {
      return this.stimuliGroups[index].id;
    },
    categoryNameFromId : function(id) {
      if (id === "0" | id === null | id === undefined) return "\u2013";
      else return this.stimulusCategories[id];
    }
  }
  var ExperimentManager = {
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
        var $table = $('<div>').attr('id','stimuliGroupDiv').addClass('stimuliGroupDiv');
        for (var group in this.stimuliGroups) {
          $table.append(this.groupFromObject(this.stimuliGroups[group]));
        }
        return $table;
      },
      groupFromObject : function(group) {
        var $groupDiv = $('<div>').addClass('stimuliGroup').corner();
        var $groupHeader = $('<div>').addClass('stimuliGroupHeader');
        var $stimuli = $('<div>').addClass('stimuliDiv');
        var $groupFooter = $('<div>').addClass('stimuliGroupFooter');
        var $disclosureSpan = $(DISCLOSURE_HEADER_STRING).addClass('groupHeader');
        $disclosureSpan.children('img').first().click(function() {
          if (group.disclosed === undefined | group.disclosed === null) {
            group.disclosed = false;
          }
          group.disclosed = !group.disclosed;
          if (group.disclosed === true) $groupFooter.find('.actionLink').fadeTo(400,0);
          else $groupFooter.find('.actionLink').fadeTo(400,1);
          $stimuli.slideToggle();
          if (group.disclosed) $groupDiv.find('img').first().animate({rotate : '-90deg'});
          else $groupDiv.find('img').first().animate({rotate : '0deg'});
        });
        $groupHeader.append($disclosureSpan);
        var $stimuliHeader = $('<span>').addClass('groupHeader').addClass('stimuliHeader');
        $stimuliHeader.text(group.name);
        $groupHeader.append($stimuliHeader);
        $groupHeader.append($('<span>').addClass('groupHeader').addClass('greenwaldHeader').text('greenwald selector'));
        $groupHeader.append($('<span>').addClass('groupHeader').addClass('groupActionHeader').text('action selector'));
        $groupHeader.append($('<span>').addClass('groupHeader').addClass('randomization').text('randomization box'));
        $groupHeader.append($('<span>').addClass('groupHeader').addClass('groupUpArrow').append($("<a>\u2B06</a>").click(function () {alert('Implement:Up');})));
        $groupDiv.append($groupHeader);
        for (var stimulus in group.stimuli) {
          $stimuli.append(this.stimulusDivFromObject(group.stimuli[stimulus]));
        }
        $groupDiv.append($stimuli);
        $groupFooter.append($('<span>').addClass('groupFooter').addClass('groupDownArrow').append($("<a>\u2B07</a>").click(function () {alert('Implement:Up');})));
        $groupFooter.append($('<span>').addClass('groupFooter').addClass('copyGroup').append($('<a>copy</a>').addClass('actionLink').click(function () {alert('Implement: copy.')})));
        $groupFooter.append($('<span>').addClass('groupFooter').addClass('deleteGroup').append($('<a>delete</a>').addClass('actionLink').click(function () {alert('Implement: delete.')})));
        $groupDiv.append($groupFooter);
        return $groupDiv;
      },
      stimulusDivFromObject : function(stimulus) {
        var $stimulus = $('<div>').addClass('stimulus');
        var $stimulusData = this.stimulusDataFromObject(stimulus).addClass('floatLeft');
        var $stimulusOptions = $('<span>').addClass('floatRight').addClass('stimulusOptions');
        var $stimulusEditButton = $('<div>').append('TODO - edit button');
        var $stimulusActions = $('<div>').append('TODO - stimulus actions');
        var $clear = $('<div>').addClass('clear');
        $stimulusOptions.append($stimulusEditButton).append($stimulusActions);
        $stimulus.append($stimulusOptions).append($stimulusData).append($clear);
        return $stimulus;
      },
      stimulusDataFromObject : function(stimulus) {
        var $table = $('<span>').addClass('stimulusData');
        var $categoryDiv = $('<div>');
        var $leftSpan = $('<span>').addClass('floatLeft');
        var $rightSpan = $('<span>').addClass('floatRight');
        var $centerDiv = $('<span>').addClass('center');
        var $clear = $('<div>').addClass('clear');
        
        var $cat1 = $('<div>').addClass('stimulusDatum').addClass('leftCategory').addClass('topCategory');
        $cat1.text(this.categoryNameFromId(stimulus.category1));
        var $cat2 = $('<div>').addClass('stimulusDatum').addClass('rightCategory').addClass('topCategory');
        $cat2.text(this.categoryNameFromId(stimulus.category2));
        var $subcat1 = $('<div>').addClass('stimulusDatum').addClass('leftCategory').addClass('bottomCategory');
        $subcat1.text(this.categoryNameFromId(stimulus.subcategory1));
        var $subcat2 = $('<div>').addClass('stimulusDatum').addClass('rightCategory').addClass('bottomCategory');
        $subcat2.text(this.categoryNameFromId(stimulus.subcategory2));
        var $word = $('<div>').addClass('stimulusDatum').addClass('stimulusWord');
        $word.text(stimulus.word);
        
        $leftSpan.append($cat1).append($subcat1);
        $rightSpan.append($cat2).append($subcat2);
        $categoryDiv.append($leftSpan).append($rightSpan).append($clear);
        $centerDiv.append($word);
        $table.append($categoryDiv).append($centerDiv);
        return $table;
      }
      //dynamic actions
  };
  
  const DISCLOSURE_HEADER_STRING = '<span class="disclosure"><img src="disclosureTriangle.png"></span>';
  function requestExperimentWithAuthentication(experimentNumber,callback,authentication) {
    var experimentPromise = $.Deferred().done(callback);
    var experiment = Object.create(Experiment);
    experiment.experimentNumber = experimentNumber;
    experiment.experimentPromise = experimentPromise;
    experiment.authentication = authentication;
    authentication.promise.done(function () {
      if (authentication.valid === true) {
        for (var propName in ExperimentManager) {
          experiment[propName] = ExperimentManager[propName];
        }
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
      }
    });
    return experiment;
  }
  function requestExperiment(experimentNumber,callback) {
    var experimentPromise = $.Deferred().done(callback);
    var experiment = Object.create(Experiment);
    experiment.experimentNumber = experimentNumber;
    experiment.experimentPromise = experimentPromise;
    experiment.authentication = null;
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
