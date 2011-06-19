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
    appendExperimentSelectorTo : function ($domObj) {
      return generateExperimentSelector(function (selector) {
        var $list = selector.generateExperimentList($domObj);
        $domObj.append($list);
      },IATManager.authenticate());
    },
    getExperimentManager : function (experimentNumber,callback,authentication) {
      return requestExperimentWithAuthentication(experimentNumber,callback,authentication);
    },
    authenticate : function() {
      var authentication = Object.create({});
      authentication.promise = $.Deferred();
      this.verifyAuthentication().success(function (receivedData) {
        if (JSON.parse(receivedData) == true) {
          authentication.valid = true;
          authentication.promise.resolve();
        } else {
          var $authenticationBox = $('<div>').addClass('authenticationBox');
          var $authenticationDiv = $('<div>').addClass('innerAuthentication');
          var $labelSpan = $('<span>').append($('<div>').append('Username: ')).append($('<div>').append('Password: ')).addClass('floatLeft');
          var $inputSpan = $('<span>').addClass('floatRight');
          var $form = $('<form id="loginForm" action="javascript:$.noop();">');
          var $username = $('<input type="text" name="username" id="usernameInput">').addClass('innerAuthenticationInput');
          var $password = $('<input type="password" name="password" id="passwordInput">').addClass('innerAuthenticationInput');
          $inputSpan.append($('<div>').append($username));
          $inputSpan.append($('<div>').append($password));
          $form.append($labelSpan).append($inputSpan);
          $form.append($('<div>').append($('<input type="submit" value="Log in">').addClass('center').addClass('innerAuthenticationSubmit')).addClass('floatRight'));
          $form.append($('<div class="authenticationError">').append('<span id="authenticationErrorSpan">'));
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
              if (parsedData.errorString) {
                $('#authenticationErrorSpan').text(parsedData.errorString);
              }
              $('#authenticationErrorSpan').append(parsedData.authenticationMessage);
              authentication.valid = parsedData.valid;
              if (authentication.valid === true) {
                authentication.data = parsedData;
                authentication.valid = parsedData.valid;
                authentication.promise.resolve();
                $form.trigger('close');
              }
            });
          });
          $authenticationDiv.append($form);
          $authenticationBox.append($authenticationDiv);
          $authenticationBox.lightbox_me({
            onLoad: function () {
              $('#usernameInput').focus();
            },
            closeEsc:false,
            closeClick:false,
            destroyOnClose:true
          });
        }
      });
      return authentication;
    },
    verifyAuthentication : function() {
      return sendRequest(bundleIATManagerRequestData('verifyAuthentication'));
    }
  };
  IAT.IATManager = function () {
    return Object.create(IATManager);
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
  
  var ExperimentListItem = {
    experimentNumber : null,
    experimentName : null,
    experimentHash : null,
    generateExperimentListItem : function (chooserCallback) {
      var $listItemDiv = $('<div class="experimentListItem">');
      $listItemDiv.append($('<span class="experimentNumber floatLeft">').text(this.experimentNumber));
      $listItemDiv.append($('<span class="experimentName floatLeft">').text(this.experimentName));
      $listItemDiv.append($('<span class="experimentActions floatRight">').text("Modify ").click(chooserCallback).append('<span class="experimentModifyArrow">\u27A1</span>'));
      return $listItemDiv;
    }
  }
  
  var ExperimentList = {
    array : [],
    authentication : null,
    generateExperimentList : function ($contentDiv) {
      var $list = $('<div class="stimuliGroup">');
      for (var experiment in this.array) {
        $list.append(this.array[experiment].generateExperimentListItem(function (authentication,experimentListItem) {
          return function () {
            $(this).find('.experimentModifyArrow').replaceWith('<img src="ajaxLoader.gif" />');
            var $stimulusTable;
            var experiment = requestExperimentWithAuthentication(experimentListItem.experimentNumber,function () {
              $stimulusTable = experiment.experimentManager();
            },authentication);
            experiment.experimentPromise.done(function () {
              $contentDiv.hide("slide",{direction: "left", mode: "hide"},400,function () {
                $list.remove();
              });
              var $newContentDiv = $('<div class="contentDiv">');
              $('body').append($newContentDiv);
              $newContentDiv.append($stimulusTable);
              $newContentDiv.show("slide",{direction: "right"},400);
            });
          };
        }(this.authentication,this.array[experiment])
      ));
      }
      $list.sortable({axis: 'y'});
      return $list;
    }
  }
  
  //experiment constructors
  var Experiment = {
    //data
    name : null,
    experimentNumber : null,
    stimuliGroups :  null,
    stimulusCategories : null,
    authentication: null,
    //translations
    groupIdFromIndex : function(index) {
      return this.stimuliGroups[index].id;
    },
    categoryNameFromId : function(id) {
      if (id === "0" | id === null | id === undefined) return "\u2013";
      else return this.stimulusCategories[id];
    }
  }
  var ExperimentManager = function () {
    var stimuliTableDomObj;
    var changedItems = [];
      return {
      //data
      changedItems : [],
      //manipulation functions
      stimuliTableDomObj : null,
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
      experimentManager : function () {
        var $div = $('<div>');
        $div.append(this.generateStimuliTable());
        $div.prepend(this.generateOptionsHeader());
        return $div;
      },
      generateOptionsHeader : function () {
        var $div = $('<div class="experimentManagerOptionsHeader">');
        var $modify = $('<span class="experimentReturnLink actionLink">').append('<span class="experimentModifyArrow">\u2B05</span> Return');
        $modify.click(function (authentication) {
          return function () {
            $(this).find('.experimentModifyArrow').replaceWith('<img src="ajaxLoader.gif" />');
            var selector = generateExperimentSelector(function() {
              var $content = $('.contentDiv');
              $content.hide("slide",{direction: "right", mode: "hide"},400,function () {
                $content.remove();
              });
              var $newContentDiv = $('<div class="contentDiv">');
              $('body').append($newContentDiv);
              var $list = selector.generateExperimentList($newContentDiv);
              $newContentDiv.append($list);
              $newContentDiv.show("slide",{direction: "left"},400);
            },authentication);
          };
        }(this.authentication));
        $div.append($modify);
        $div.append($('<div class="experimentManagerTitle">').append(this.name));
        $div.append("other options");
        return $div;
      },
      generateStimuliTable : function() {
        var $table = $('<div>').attr('id','stimuliGroupDiv').addClass('stimuliGroupDiv');
        for (var group in this.stimuliGroups) {
          $table.append(this.groupFromObject(this.stimuliGroups[group]));
        }
        var myExp = this;
        $table.sortable({
          update : function (event, ui) {
            myExp.changedItems.push("group order");
          },
          axis: 'y'
        });
        this.stimuliTableDomObj = $table;
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
        $groupDiv.append($groupHeader);
        var myExp = this;
        for (var stimulus in group.stimuli) {
          $stimuli.append(this.stimulusDivFromObject(group.stimuli[stimulus]));
        }
        $stimuli.sortable({
          update: function(event,ui) {
            var $stimuliGroup = $(this);
            if ($stimuliGroup.attr('changed') === "true") {
              return;
            } else {
              $stimuliGroup.attr('changed',"true")
              myExp.changedItems.push(myExp.stimuliGroups[$stimuliGroup.index()]);
            }
          },
          axis: 'y'
        });
        $stimuli.attr('id','stimuliGroupDiv_' + group.id);
        $groupDiv.append($stimuli);
        $groupFooter.append($('<span>').addClass('groupFooter').addClass('copyGroup').append($('<a>copy</a>').addClass('actionLink').click(function () {alert('Implement: copy.')})));
        $groupFooter.append($('<span>').addClass('groupFooter').addClass('deleteGroup').append($('<a>delete</a>').addClass('actionLink').click(function () {alert('Implement: delete.')})));
        $groupDiv.append($groupFooter);
        $groupDiv.attr('id','group_' + group.id);
        $groupDiv[0].groupModel = group;
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
        $stimulus[0].stimulusModel = stimulus;
        stimulus.stimulusView = $stimulus[0];
        $stimulus.attr('id','stimulus_' + stimulus.stimulus_id);
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
      },
      saveChanged : function () {
        $('#stimuliGroupDiv changed="true"').addClass('.changed');
      }
      //dynamic actions
    };
  }
  ExperimentManager = ExperimentManager();
  
  var DISCLOSURE_HEADER_STRING = '<span class="disclosure"><img src="disclosureTriangle.png"></span>';
  function generateExperimentSelector(callback,authentication) {
    var experiments = Object.create(ExperimentList);
    var experimentListPromise = $.Deferred().done(function() {callback(experiments)});
    experiments.promise = experimentListPromise;
    experiments.authentication = authentication;
    authentication.promise.done(function () {
      if (authentication.valid === true) {
        sendRequest(bundleIATManagerRequestData('requestExperimentList')).success(function (receivedData) {
          var data = JSON.parse(receivedData);
          for (var dataExp in data) {
            var experiment = Object.create(ExperimentListItem);
            experiment.experimentNumber = data[dataExp].stimuli_set;
            experiment.experimentHash = data[dataExp].hash;
            experiment.experimentName = data[dataExp].name;
            experiment.authentication = authentication;
            experiments.array[dataExp] = experiment;
          }
          experimentListPromise.resolve();
        });
      }
    });
    return experiments;
  }
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
