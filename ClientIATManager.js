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
  
  //key event handling
  $(document).keydown($.noop());
  $(document).keypress(function onKeyDown(event) {
    switch (event.keyCode) {
      case 13:
        $(document.activeElement).filter("input").each(function () {
          $(this).submit();
        });
        break;
      case 27:
        $(document.activeElement).filter("input[type=text]").each(function () {
          var $elem = $(this);
          $elem.val($elem.attr("original"));
          toggleTextInput($elem);
        });
    }
  });
  
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
    generateExperimentListItem : function (modifyCallback) {
      var $listItemDiv = $('<div class="experimentListItem">');
      $listItemDiv.append($('<span class="experimentNumber floatLeft">').text(this.experimentNumber));
      $listItemDiv.append($('<span class="experimentName floatLeft">').text(this.experimentName));
      $listItemDiv.append($('<span class="experimentActions floatRight">').text("Modify ").click(modifyCallback).append('<span class="experimentModifyArrow">\u27A1</span>'));
      $listItemDiv.append($('<span class="experimentActions floatRight">').text("Delete ").click(function ($self,experimentNumber) {
        return function() {
          if (confirm('Are you sure you want to delete this experiment and all of its data? This action cannot be undone.')) {
            sendRequest(bundleIATManagerRequestData('deleteExperiment',experimentNumber)).success(function (receivedData) {
              var data = JSON.parse(receivedData);
              if (data.success) {
                $self.remove();
                $.jnotify("Successfully Deleted");
              } else {
                $.jnotify(data.message);
              }
            });
          }
        }
      }($listItemDiv,this.experimentNumber)).append('<span class="experimentDeleteCross">X</span>'));
      return $listItemDiv;
    }
  }
  
  var ExperimentList = {
    array : [],
    authentication : null,
    generateExperimentList : function ($contentDiv) {
      var self = this;
      var $topDiv = $('<div>');
      var $list = $('<div class="stimuliGroup">');
      var listItemCallback = function(authentication,experimentListItem) {
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
      }
      var $header = $('<div>').append($('<button>+</button>').click(function () {
        var experimentListItem = Object.create(ExperimentListItem);
        sendRequest(bundleIATManagerRequestData("addExperiment")).success(function (receivedData) {
          var data = JSON.parse(receivedData);
          experimentListItem.experimentNumber = data.experiment.id;
          experimentListItem.experimentName = data.experiment.name;
          experimentListItem.experimentHash = data.experiment.hash;
          $list.append(experimentListItem.generateExperimentListItem(function (authentication,experimentListItem) {
            return listItemCallback.apply(self,[authentication,experimentListItem]);
          }(self.authentication,experimentListItem)));
        });
      }));
      for (var experiment in this.array) {
        $list.append(this.array[experiment].generateExperimentListItem(function (authentication,experimentListItem) {
          return listItemCallback.apply(this,[authentication,experimentListItem]);
        }(self.authentication,this.array[experiment])
      ));
      }
      $list.sortable({axis: 'y'});
      $topDiv.append($header).append($list);
      return $topDiv;
    }
  }
  
  //experiment constructors
  var Experiment = {
    //data
    name : null,
    experimentNumber : null,
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
      experimentManager : function() {
        function makeStimulusEntry(wordObject,temporary) {
          var $li = $('<li>').addClass('CategoryListItem');
          var $wrapper = $('<span class="listItemInnerWrapper">');
          var $liSpan = $('<span>').addClass('Stimulus').append(wordObject.word);
          $wrapper.append($liSpan);
          if (!temporary) {
            $liSpan.editable(function (value) {
              sendRequest(bundleIATManagerRequestData("setStimulusProperties",{
                "id" : wordObject.id,
                "word" : value
              })).success(function (receivedData) {
                var data = JSON.parse(receivedData);
                $.jnotify("Stimulus changed to '" + value + "'. " + data.message);
              });
              return value;
            });
            var $delete = $('<span class="StimulusDeleteSpan">X</span>').click(function () {
              $wrapper.find('.Stimulus').editable('disable');
              $wrapper.find('.StimulusDeleteSpan').unbind('click').text('');
              sendRequest(bundleIATManagerRequestData('deleteStimulus',wordObject)).success(function (receivedData) {
                var data = JSON.parse(receivedData);
                $li.remove();
                $.jnotify(data.message);
              });
            });
            $wrapper.append($delete);
          }
          $li.append($wrapper);
          return $li;
        }
        function generateCategoryList(stimulusCategory,temporary) {
          var $listFooter = $('<span>');
          var $listTopDiv = $('<div>').addClass('CategoryListContainer');
          var $listDiv = $('<span>');
          if (!temporary) {
            $listDiv.append($('<span>').append(stimulusCategory.name).addClass('CategoryListHeader').editable(function(value,settings) {
              sendRequest(bundleIATManagerRequestData("setStimulusCategoryProperties",{"id":stimulusCategory.id,"name":value})).success(function (receivedData) {
                var data = JSON.parse(receivedData);
                $.jnotify("Category title changed to '" + value + "'. " + data.message);
              });
              return value;
            }));
          }
          var $list = $('<ul>').addClass('CategoryList');
          for (var i in stimulusCategory.stimuli) {
            $list.append(makeStimulusEntry(stimulusCategory.stimuli[i],false));
          }
          $list.sortable();
          $listDiv.append($list);
          var $button = $('<button>+</button>').click(function () {
            var word = {"word":"new word","stimulusCategory":stimulusCategory.id,"experiment":stimulusCategory.experiment};
            var $li = makeStimulusEntry(word,true);
            sendRequest(bundleIATManagerRequestData("addStimulus",word)).success(function (receivedData) {
              var data = JSON.parse(receivedData);
              if (data.success) {
                $li.replaceWith(makeStimulusEntry(data.stimulus,false));
                $.jnotify("Stimulus added to " + stimulusCategory.name + ".");
              } else {
                $.jnotify(data.message);
              }
            });
            $list.append($li);
          });
          if (temporary) {
            $button.prop('disabled',true);
          }
          $listFooter.append($button);
          $listTopDiv.append($listDiv);
          $listTopDiv.append($listFooter);
          return $listTopDiv;
        }
        function generateFlowList(stimulusCategories,blocks) {
          function findCategory(categoryId) {
            return $.grep(stimulusCategories,function (item,index) {
              return item.id === categoryId;
            })[0];
          }
          var $flowList = $('<ul class="flowList">');
          for (var i in blocks) {
            var $block = $('<li class="flowListItem">');
            var $left = $('<div class="flowCategoryLeft">');
            var $right = $('<div class="flowCategoryRight">');
            if (blocks[i].components[1]) {
              $left.append($('<div>').append(findCategory(blocks[i].components[1].category).name));
            }
            if (blocks[i].components[2]) {
              $right.append($('<div>').append(findCategory(blocks[i].components[2].category).name));
            }
            if (blocks[i].components[3]) {
              $left.append($('<div>').append(findCategory(blocks[i].components[3].category).name));
            }
            if (blocks[i].components[4]) {
              $right.append($('<div>').append(findCategory(blocks[i].components[4].category).name));
            }
            var $blockCenter = $('<div class="flowCategoryText">');
            $blockCenter.append($('<div>').append($('<span>'+blocks[i].description+'</span>').editable(function (blockId) {
              return function (value) {
                sendRequest(bundleIATManagerRequestData('setBlockProperties',{
                  'block':blockId,
                  'description':value
                }));
                return value;
              }
            }(blocks[i].id),{
              style:"display:inline"
            })));
            $blockCenter.append('Trials: ').append($('<span>'+blocks[i].trials+'</span>').editable(function (value) {
              sendRequest(bundleIATManagerRequestData('setBlockProperties',{
                'block':blocks[i].id,
                'trials':value
              }));
              return value;
            },{
              style:"display:inline"
            }));
            $block.append($left).append($right).append($blockCenter);
            $flowList.append($block);
          }
          return $flowList;
        }
        function generateFlowSidePanel() {
          var $sidePanel = $('<div class="flowSidePanel">');
          var $balance = $('<div>').append($('<label><input type="checkbox" />Auto-balance</label>').change(function() {
            sendRequest(bundleIATManagerRequestData('setExperimentProperties',{
              'autoBalance':$(this).find('input').prop('checked'),
              'id':experimentManager.experimentNumber
            }));
          }).attr('title','If selected, the IAT will automatically randomize test takers into seeing blocks in the normal order and switching blocks 1,3,and 4 with blocks 5, 6, and 7, respectively.'));
          var $answerChecking = $('<div>').append($('<label><input type="checkbox" />Check errors</label>').change(function () {
            sendRequest(bundleIATManagerRequestData('setExperimentProperties',{
              'checkAnswers':$(this).find('input').prop('checked'),
              'id':experimentManager.experimentNumber
            }));
          }).attr('title','If selected, the IAT will inform test takers of incorrect responses and not allow them to proceed without a correct response. Scoring is not affected.'));
          $sidePanel.append($balance).append($answerChecking);
          return $sidePanel;
        }
        function generateSettingsDiv(experiment) {
          var $topDiv = $("<div>");
          var $settingsList = $('<ul>');
          var defaultOptions = {
            "about:blank":"Blank",
            "http://google.com":"Google"
          };
          var endUrl = defaultOptions[experiment.endUrl];
          var $endURL = $('<li>').append('Experiment end URL: ').append($('<span>' + (endUrl ? endUrl : 'Blank') + '</span>').editable(function (value) {
            sendRequest(bundleIATManagerRequestData('setExperimentProperties',{
              'endUrl':value,
              'id':experiment.experimentNumber
            }));
            if (defaultOptions[value]) return defaultOptions[value];
            return value;
          },{
              type:'selectWithOther',
              data:defaultOptions,
              submit:'save',
              style:"display:inline;"
            }));
          $settingsList.append($endURL);
          $topDiv.append($settingsList);
          return $topDiv;
        }
        function generatePairedCategoryDivs(stimulusCategories,categoryPairs) {
          var divArray = [];
          var remainingCategories = stimulusCategories.slice(0);
          for (var i in categoryPairs) {
            var positiveCategory;
            var negativeCategory;
            var unusedCategories = [];
            for (var ii in remainingCategories) {
              if (remainingCategories[ii].id === categoryPairs[i].positiveCategory) {
                positiveCategory = remainingCategories[ii];
              } else if (remainingCategories[ii].id === categoryPairs[i].negativeCategory) {
                negativeCategory = remainingCategories[ii];
              } else {
                unusedCategories.push(remainingCategories[ii]);
              }
            }
            remainingCategories = unusedCategories.slice(0);
            unusedCategories = undefined;
            var $pairDiv = $('<div class="pairDiv" id="categoryPair' + i + '">').append(generateCategoryList(positiveCategory));
            $pairDiv.append(generateCategoryList(negativeCategory));
            divArray.push($pairDiv.get(0));
          }
          if (remainingCategories.length > 0)
            console.log(remainingCategories.length + " unpaired categories ignored.");
          return divArray;
        }
        var experimentManager = this;
        var $tabDiv = $('<div id="tabDiv"><ul><li><a href="#tabs-1">Stimuli</a></li><li><a href="#tabs-2">Flow</a></li><li><a href="#tabs-3">Settings</a></li><li><a href="#tabs-4">Save and Close</a></ul></div>');
        var $stimuliDiv = $('<div id="tabs-1">').addClass('ExperimentManager');
        var $contentDiv = $('<div>').addClass('ExperimentManagerContent');
        var $headerDiv = $('<div>').addClass('ExperimentManagerHeader');
        var $flowDiv = $('<div id="tabs-2">');
        var $settingsDiv = $('<div id="tabs-3">').append(generateSettingsDiv(this));
        var $closeDiv = $('<div id="tabs-4">').append("Closing...");
        $contentDiv.append(generatePairedCategoryDivs(this.stimulusCategories,this.categoryPairs));
        $flowDiv.append(generateFlowList(this.stimulusCategories,this.blocks)).append(generateFlowSidePanel());
        $stimuliDiv.append($headerDiv);
        $stimuliDiv.append($contentDiv);
        $tabDiv.append($stimuliDiv);
        $tabDiv.append($flowDiv);
        $tabDiv.append($settingsDiv);
        $tabDiv.append($closeDiv);
        $tabDiv.tabs({
          select: function (event,ui) {
            if (ui.index === 3) {
              var $contentDiv = $tabDiv.parent();
              var experiments = generateExperimentSelector(function () {
                var $list = experiments.generateExperimentList($contentDiv);
                $tabDiv.hide("slide",{direction: "right", mode: "hide"},400,function () {
                  $tabDiv.remove();
                });
                $list.show("slide",{direction: "right", mode: "show"},400,function() {
                  $contentDiv.append($list);
                });
              },experimentManager.authentication);
            }
          }
        });
        return $tabDiv;
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
            experiment.experimentNumber = data[dataExp].id;
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
          $.extend(experiment,data);
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
      $.extend(experiment,data);
      experimentPromise.resolve();
    });
    return experiment;
  }
  return IAT;
})();
window.IAT = IAT;
})(window);
