define(["IAT",
        "../scripts/Lightbox_me/jquery.lightbox_me.js",
        "../scripts/sha1.js",
        "../scripts/jquery-ui.js",
        "../scripts/jquery.jeditable/jquery.jeditable.js",
        "../scripts/selectWithOther.jeditable/selectWithOther.jeditable.js"
      ],function (IAT) {

  /*
   * This replaces the default sendRequest method defined in CoreIAT with a more
   * complex one that can handle reauthentication of an expired
   */
  IAT.sendRequest = function(requestObject,recursion) {
      var deferred = $.Deferred();
      if (!recursion) recursion = 0;
      else if (recursion > 3) return undefined;
      $.post(IAT.managerFilePath,requestObject).done(function (receivedData,textStatus,jqXHR) {
        var data = JSON.parse(receivedData);
        if (data && data.errorCode === '1003') {
          var authentication = IATManager.authenticate("Your authentication is invalid or has expired. Please log in again.");
          authentication.promise.done(function () {
            IAT.sendRequest(requestObject,recursion+1).done(function (data) {
              deferred.resolveWith(this,[data])
            });
          });
        } else if (data && data.errorCode === '1004') {
          $.jnotify("You do not have sufficient permission for the attempted function.");
        } else {
          deferred.resolveWith(this,[data]);
        }
      });
      return deferred;
    }

  /*
 * This object encapsulates all the functionality of the IAT Manager.
 */
  var IATManager = {

    /*
   * Supplies an interface allowing an experiment selector to be added to an
   * existing div.
   */
    appendExperimentSelectorTo : function ($domObj) {
      return generateExperimentSelector(function (selector) {
        var $list = selector.generateExperimentList($domObj);
        $domObj.append($list);
      },IATManager.authenticate());
    },

    /*
   * Forwards a request for authenticated experiment object.
   */
    getExperimentManager : function (experimentNumber,callback,authentication) {
      return requestExperimentWithAuthentication(experimentNumber,callback,authentication);
    },

    /*
   * Handles authentication. When called, it calls to the server checking
   * for an existing valid session, but immediately passes out a deferred
   * authentication object. When the server returns, if the authentication
   * is already valid, the deffered object is resolved. If no valid session
   * is found, a login lightbox is displayed, with options to create a new
   * account or recover a lost password.
   */
    authenticate : function(message) {

      /*
     * This function packages authentication input from HTML form elements
     * into an object useable by the rest of the authentication
     * infrastructure.
     */
      function packageAuthenticationFromDOM($username,$password,$email) {
        var authentication = {};
        var username;
        var password;
        var passwordHash;
        var email;
        if ($username.find('input').size() === 0) {
          username = $username.val();
          authentication.username = username;
        } else {
          username = $username.find('input').val();
          authentication.username = username;
        }
        if ($password.find('input').size() === 0) {
          password = $password.val();
          passwordHash = hex_sha1(password);
          password = '';
          authentication.passwordHash = passwordHash;
        } else {
          password = $password.find('input').val();
          passwordHash = hex_sha1(password);
          password = '';
          authentication.passwordHash = passwordHash;
        }
        if ($email && $email.find('input').size() === 0) {
          email = $email.val();
          authentication.email = email;
        } else if ($email) {
          email = $email.find('input').val();
          authentication.email = email;
        }
        return authentication;
      }

      /*
     * Produces the registration form to be used on the authentication
     * lightbox.
     */
      function registerDiv($containingDiv,$currentContent) {
        var $div = $('<div class="innerAuthentication">');
        var $form = $('<form class="floatRight">');
        var $labels = $('<div class="floatLeft">')
        .append('<div>Username:</div>')
        .append('<div>Password:</div>')
        .append('<div>Retype Password:</div>')
        .append('<div>Email:</div>');
        var $username = $('<div><input class="registerInput" type="textbox" /></div>');
        $username.find('input').change(function () {
          var $that = $(this);
          var currentValue = $that.val();
          IAT.sendRequest(IAT.bundleIATManagerRequestData('checkUsernameAvailability',currentValue))
          .done(function (data) {
            if (data.available) {
              $that.css('background-color','#CCFF99');
            } else {
              $that.css('background-color','#FFCCCC');
              $.jnotify('Username taken. Please choose another.');
            }
          });
        })
        var $password = $('<div><input class="registerInput" type="password" /></div>');
        var $retypePassword = $('<div><input class="registerInput" type="password" /></div>');
        var $email = $('<div><input class="registerInput" type="textbox" /></div>');
        var $submit = $('<div><input type="submit" value="Register"></div>');
        $form.append($username).append($password).append($retypePassword).append($email).append($submit);
        $div.append($form).append($labels);
        $containingDiv.css('width','+=100px').css('height','+=120px').css('left','+=50px');
        $containingDiv.append($div.slideDown());
        $currentContent.slideDown().remove();
        $form.bind('submit',function () {
          $submit.prop('disabled','true');
          if ($password.val() !== $retypePassword.val()) {
            $submit.prop('disabled','false');
            $.jnotify("Passwords do not match.");
          } else {
            var authenticationInfo = packageAuthenticationFromDOM($username,$password,$email);
            IAT.sendRequest(IAT.bundleIATManagerRequestData('registerUser',authenticationInfo)).done(function() {
              $div.replaceWith('<div class="authenticationBoxNotice">Your registration email has been sent. Once you confirm your email, an administrator will complete your registration.</div>');
              authentication.promise.resolve();
            });
          }
          return false;
        });
      }

      /*
     * Produces a form to request a password reset for the authentication
     * lightbox.
     */
      function forgotDiv() {
        var $div = $('<div>');
        var $form = $('<form class="floatRight">');
        var $labels = $('<div class="floatLeft">').append('<div>Email: </div>');
        var $email = $('<div><input class="registerInput" type="textbox" /></div>');
        var $submit = $('<div><input type="submit" value="Send Email"></div>');
        $form.append($email);
        $form.bind('submit',function () {
          $submit.prop('disabled','true');
          IAT.sendRequest(IAT.bundleIATManagerRequestData('sendForgotEmail',$email.find('input').val())).done(function (data) {
            if (data.success) {
              $.jnotify("Email to reset password was sent.");
            } else {
              $.jnotify("Email to reset password was not sent.");
            }
          });
          $.jnotify("Notice: the server is not yet configured to send email.");
        })
        $div.append($form).append($labels).append($submit);
        return $div;
      }
      var authentication = Object.create({});
      authentication.promise = $.Deferred();
      var authenticationRequest = this.verifyAuthentication();
      authenticationRequest.done(function (data) {
        if (data == true) {
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
          var $register = $('<a class="actionLink">').text("Request login").click(function () {
            var $newDiv = registerDiv($(this).parents('div').eq(2),$(this).parents('div').eq(1));

          });
          var $forgot = $('<a class="actionLink">').text("Forgot").click(function () {
            $(this).parents('div').eq(1).replaceWith(forgotDiv());
          });
          $form.append($('<div class="registerforgotdiv">').append($register).append('/').append($forgot));
          $form.append($('<div class="authenticationError">').append('<span id="authenticationErrorSpan">'));
          if (message) {
            $('#authenticationErrorSpan',$form).text(message);
          }
          $form.submit(function () {
            $form.find().each().prop('disabled',true);
            var authenticationInfo = packageAuthenticationFromDOM($username,$password);
            IAT.sendRequest(IAT.bundleIATManagerRequestData('authenticate',{
              username:authenticationInfo.username,
              passwordHash:authenticationInfo.passwordHash
            })).done(function (data) {
              if (data.errorString) {
                $('#authenticationErrorSpan').text(data.errorString);
              }
              $('#authenticationErrorSpan').text(data.authenticationMessage);
              authentication.valid = data.valid;
              if (authentication.valid === true) {
                authentication.data = data;
                authentication.valid = data.valid;
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

    /*
   * Verifies with the server if a valid authenticated session exists.
   */
    verifyAuthentication : function() {
      return IAT.sendRequest(IAT.bundleIATManagerRequestData('verifyAuthentication'));
    }
  }


  /*
     * Prototype for experiment list items in the IAT Manager.
     */
  var ExperimentListItem = {

    /*
       * The experiment's ID number in the database.
       */
    experimentNumber : null,

    /*
       * The experiment's name.
       */
    experimentName : null,

    /*
       * The experiment's unique short hash. This exists to obfuscate the ID
       * number, solely to prevent user error in the event someone accidentally
       * modifying a URI parameter.
       */
    experimentHash : null,

    /*
       * Constructs the HTML for an experiment list item. Accepts a function to
       * be called when the modify link is clicked.
       */
    generateExperimentListItem : function (modifyCallback) {
      var $listItemDiv = $('<div class="experimentListItem">');
      $listItemDiv.append($('<span class="experimentNumber floatLeft">').text(this.experimentNumber));
      $listItemDiv.append($('<span class="experimentName floatLeft">').text(this.experimentName));
      $listItemDiv.append($('<span class="experimentActions floatRight">').text("Modify ").click(modifyCallback).append('<span class="experimentModifyArrow">\u27A1</span>'));
      $listItemDiv.append($('<span class="experimentActions floatRight">').text("Delete ").click(function ($self,experimentNumber) {
        return function() {
          if (confirm('Are you sure you want to delete this experiment and all of its data? This action cannot be undone.')) {
            IAT.sendRequest(IAT.bundleIATManagerRequestData('deleteExperiment',experimentNumber)).done(function (data) {
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

  /*
     * Prototype for experiment lists.
     */
  var ExperimentList = {

    /*
       * Holds list data objects received from the server.
       */
    experiments : [],

    /*
       * Holds an authentication object as a record.
       */
    authentication : null,

    /*
       * Generates the HTML representation of the experiment list.
       */
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
            $contentDiv.hide("slide",{
              direction: "left",
              mode: "hide"
            },400,function () {
              $list.remove();
            });
            var $newContentDiv = $('<div class="contentDiv">');
            $('body').append($newContentDiv);
            $newContentDiv.append($stimulusTable);
            $newContentDiv.show("slide",{
              direction: "right"
            },400);
          });
        };
      }
      var $header = $('<div>').append($('<button>+</button>').click(function () {
        var experimentListItem = Object.create(ExperimentListItem);
        IAT.sendRequest(IAT.bundleIATManagerRequestData("addExperiment")).done(function (data) {
          experimentListItem.experimentNumber = data.experiment.id;
          experimentListItem.experimentName = data.experiment.name;
          experimentListItem.experimentHash = data.experiment.hash;
          $list.append(experimentListItem.generateExperimentListItem(function (authentication,experimentListItem) {
            return listItemCallback.apply(self,[authentication,experimentListItem]);
          }(self.authentication,experimentListItem)));
        });
      }));
      for (var experiment in this.experiments) {
        $list.append(this.experiments[experiment].generateExperimentListItem(function (authentication,experimentListItem) {
          return listItemCallback.apply(this,[authentication,experimentListItem]);
        }(self.authentication,this.experiments[experiment])
          ));
      }
      $list.sortable({
        axis: 'y'
      });
      $topDiv.append($header).append($list);
      return $topDiv;
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
        return IAT.sendRequest(IAT.bundleIATManagerRequestData("removeExperiment",{
          'experimentNumber' : experimentNumber,
          'data' : null
        }));
      },
      copyExperiment : function(experimentNumber) {
        return IAT.sendRequest(IAT.bundleIATManagerRequestData("copyExperiment",{
          'experimentNumber' : experimentNumber,
          'data' : null
        }));
      },
      setExperimentProperties : function(experimentNumber,dataObject) {
        return IAT.sendRequest(IAT.bundleIATManagerRequestData("setExperimentProperties",{
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
              IAT.sendRequest(IAT.bundleIATManagerRequestData("setStimulusProperties",{
                "id" : wordObject.id,
                "word" : value
              })).done(function (data) {
                $.jnotify("Stimulus changed to '" + value + "'. " + data.message);
              });
              return value;
            });
            var $delete = $('<span class="StimulusDeleteSpan">X</span>').click(function () {
              $wrapper.find('.Stimulus').editable('disable');
              $wrapper.find('.StimulusDeleteSpan').unbind('click').text('');
              IAT.sendRequest(IAT.bundleIATManagerRequestData('deleteStimulus',wordObject)).done(function (data) {
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
              IAT.sendRequest(IAT.bundleIATManagerRequestData("setStimulusCategoryProperties",{
                "id":stimulusCategory.id,
                "name":value
              })).done(function (data) {
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
            var word = {
              "word":"new word",
              "stimulusCategory":stimulusCategory.id,
              "experiment":stimulusCategory.experiment
            };
            var $li = makeStimulusEntry(word,true);
            IAT.sendRequest(IAT.bundleIATManagerRequestData("addStimulus",word)).done(function (data) {
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
          var $flowList = $('<ul class="flowList" id="flowList">');
          for (var i in blocks) {
            $flowList.append(generateBlock(blocks[i],false,stimulusCategories));
          }
          return $flowList.sortable({cancel: '.flowListItem'});
        }
        function generateBlock(blockData,instruction,stimulusCategories) {
          var $block = $('<li class="flowListItem">');
          var $left = $('<div class="flowCategoryLeft">');
          var $right = $('<div class="flowCategoryRight">');
          if (blockData.components[1]) {
            $left.append($('<div>').append(stimulusCategories[blockData.components['1'].category].name));
          }
          if (blockData.components[2]) {
            $right.append($('<div>').append(stimulusCategories[blockData.components['2'].category].name));
          }
          if (blockData.components[3]) {
            $left.append($('<div>').append(stimulusCategories[blockData.components['3'].category].name));
          }
          if (blockData.components[4]) {
            $right.append($('<div>').append(stimulusCategories[blockData.components['4'].category].name));
          }
          var $blockCenter = $('<div class="flowCategoryText">');
          $blockCenter.append($('<div>').append($('<span>'+blockData.description+'</span>').editable(function (blockId) {
            return function (value) {
              IAT.sendRequest(IAT.bundleIATManagerRequestData('setBlockProperties',{
                'block':blockId,
                'description':value
              }));
              return value;
            }
          }(blockData.id),{
            style:"display:inline"
          })));
          $blockCenter.append('Trials: ').append($('<span>'+blockData.trials+'</span>').editable(function (blockId) {
            return function (value) {
              IAT.sendRequest(IAT.bundleIATManagerRequestData('setBlockProperties',{
                'block':blockId,
                'trials':value
              }));
              return value;
            }
          }(blockData.id),{
            style:"display:inline"
          }));
          $block.append($left).append($right).append($blockCenter);
          return $block;
        }
        function generateFlowSidePanel() {
          var $sidePanel = $('<div class="flowSidePanel">');
          var $addInstruction = $('<div>').append($('<button>Add instruction block</button>').click(function () {
            $('#flowList').append($('<li class="flowListItem">').append($('<div class="flowCategoryText">Click here to edit</div>').editable(function (value,settings) {
              $.jnotify("Saving instructions not yet implemented.");
              return value;
            },{
              type:'textarea',
              submit:'Save'
            })));
          }));
          var $balance = $('<div>').append($('<label><input type="checkbox" />Auto-balance</label>').change(function() {
            IAT.sendRequest(IAT.bundleIATManagerRequestData('setExperimentProperties',{
              'autoBalance':$(this).find('input').prop('checked'),
              'id':experimentManager.experimentNumber
            }));
          }).attr('title','If selected, the IAT will automatically randomize test takers into seeing blocks in the normal order and switching blocks 1,3,and 4 with blocks 5, 6, and 7, respectively.'));
          var $errorNotifications = $('<div>').append($('<label><input type="checkbox" id="errorNotification" />Display error notifications</label>').change(function () {
            IAT.sendRequest(IAT.bundleIATManagerRequestData('setExperimentProperties',{
              'errorNotifications':$(this).find('input').prop('checked'),
              'id':experimentManager.experimentNumber
            }));
          }).attr('title','If selected, the IAT will display a notification when the user makes an incorrect response. Not available unless also error checking.'));
          var $answerChecking = $('<div>').append($('<label><input type="checkbox" />Check errors</label>').change(function () {
            var checked = $(this).find('input').prop('checked');
            IAT.sendRequest(IAT.bundleIATManagerRequestData('setExperimentProperties',{
              'checkAnswers':checked,
              'id':experimentManager.experimentNumber
            }));
            if (checked) {
              $('#errorNotification').prop('disabled',false);
            } else {
              if ($('#errorNotification').prop('checked')) {
                $('#errorNotification').click();
              }
              $('#errorNotification').prop('disabled',true);
            }
          }).attr('title','If selected, the IAT will inform test takers of incorrect responses and not allow them to proceed without a correct response. Scoring is not affected.'));
          if (experimentManager.checkAnswers === '1') {
            $('input',$answerChecking).prop('checked',true);
            if (experimentManager.errorNotifications === '1') {
              $('input',$errorNotifications).prop('checked',true);
            }
          } else {
            $('input',$errorNotifications).prop('disabled',true);
          }
          if (experimentManager.autoBalance === '1') {
            $('input',$balance).prop('checked',true);
          }
          $sidePanel.append($addInstruction).append($balance).append($answerChecking).append($errorNotifications);
          return $sidePanel;
        }
        function generateSettingsDiv(experiment) {
          function generateEndUrlListItem(endUrl) {
            return $('<li>').append('First end URL: ').append($('<span>' + (endUrl ? endUrl : 'Blank') + '</span>').editable(function (value) {
              IAT.sendRequest(IAT.bundleIATManagerRequestData('setExperimentProperties',{
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
          }
          function generateSecondEndUrlListItem(endUrl) {
            return $('<li>').append('Second end URL: ').append($('<span>' + (endUrl ? endUrl : 'Blank') + '</span>').editable(function (value) {
              IAT.sendRequest(IAT.bundleIATManagerRequestData('setExperimentProperties',{
                'secondEndUrl':value,
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
          }
          var $topDiv = $("<div>");
          var $settingsList = $('<ul>');
          var defaultOptions = {
            "about:blank":"Blank",
            "http://google.com":"Google"
          };
          var $download = $('<li>');
          var $downloadSelect = $('<select>')
          .append('<option value="greenwaldScores">Greenwald D-Scores</option>')
          .append('<option value="rawScores">Raw Scores</option>')
          .append('<option value="log">Log</option>');
          var $downloadButton = $('<button>Download</button>').click(function () {
            $.jnotify("Downloads not yet implemented.");
          });
          $download.append('Download: ')
          .append($downloadSelect)
          .append($downloadButton);
          var linkHref = IAT.IATBaseURL + '?' + 'i=' + experiment.hash;
          var $link = $('<a>').attr('href',linkHref).append(linkHref);
          var $linkListItem = $('<li>').append('Experiment link: ').append($link);
          var $active = $('<input type="checkbox">').click(function () {
            function setActiveExperiment(active) {
              IAT.sendRequest(IAT.bundleIATManagerRequestData('setExperimentProperties',{
                'id':experiment.experimentNumber,
                'active':active
              })).done(function (data) {
                if (!data.success) {
                  $checkbox.prop('checked',!active);
                  $.jnotify("Experiment " + (active ? "inactive. " : "active. ") + data.message);
                } else {
                  $.jnotify("Experiment " + (active ? "active." : "inactive."));
                }
              });
            }
            var $checkbox = $(this);
            if ($checkbox.prop('checked')) {
              if (!confirm("Are you sure you want to start collecting live data?")) {
                $checkbox.prop('checked',false);
              } else {
                setActiveExperiment(true);
              }
            } else {
              if (!confirm("Are you sure you want to stop recording data?")) {
                $checkbox.prop('checked',true);
              } else {
                setActiveExperiment(false);
              }
            }
          });
          var $activeListItem = $('<li>').append('Active: ').append($active);
          $settingsList.append(generateEndUrlListItem(defaultOptions[experiment.endUrl]));
          $settingsList.append($download);
          $settingsList.append($linkListItem);
          $settingsList.append($activeListItem);
          $topDiv.append($settingsList);
          return $topDiv;
        }
        function generatePairedCategoryDivs(stimulusCategories,categoryPairs) {
          var divArray = [];
          $.each(categoryPairs,function (index,pair) {
            var $pairDiv = $('<div class="pairDiv" id="categoryPair' + index + '">').append(generateCategoryList(stimulusCategories[pair.positiveCategory]));
            $pairDiv.append(generateCategoryList(stimulusCategories[pair.negativeCategory]));
            divArray.push($pairDiv.get(0));
          });
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
                $tabDiv.hide("slide",{
                  direction: "right",
                  mode: "hide"
                },400,function () {
                  $tabDiv.remove();
                });
                $list.show("slide",{
                  direction: "right",
                  mode: "show"
                },400,function() {
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

    function requestExperimentWithAuthentication(experimentNumber,callback,authentication) {
      var experimentPromise = $.Deferred().done(callback);
      var experiment = Object.create(IAT.Experiment);
      experiment.experimentNumber = experimentNumber;
      experiment.experimentPromise = experimentPromise;
      experiment.authentication = authentication;
      authentication.promise.done(function () {
        if (authentication.valid === true) {
          for (var propName in ExperimentManager) {
            experiment[propName] = ExperimentManager[propName];
          }
          IAT.sendRequest(IAT.bundleIATManagerRequestData('requestExperiment',experimentNumber,null)).done(function (data) {
            $.extend(experiment,data);
            experimentPromise.resolve();
          });
        }
      });
      return experiment;
    }

        var DISCLOSURE_HEADER_STRING = '<span class="disclosure"><img src="disclosureTriangle.png"></span>';
    function generateExperimentSelector(callback,authentication) {
      var experiments = Object.create(ExperimentList);
      var experimentListPromise = $.Deferred().done(function() {
        callback(experiments)
        });
      experiments.promise = experimentListPromise;
      experiments.authentication = authentication;
      authentication.promise.done(function () {
        if (authentication.valid === true) {
          IAT.sendRequest(IAT.bundleIATManagerRequestData('requestExperimentList')).done(function (data) {
            for (var dataExp in data) {
              var experiment = Object.create(ExperimentListItem);
              experiment.experimentNumber = data[dataExp].id;
              experiment.experimentHash = data[dataExp].hash;
              experiment.experimentName = data[dataExp].name;
              experiment.authentication = authentication;
              experiments.experiments[dataExp] = experiment;
            }
            experimentListPromise.resolve();
          });
        }
      });
      return experiments;
    }

  IAT.IATManager = function () {
    return Object.create(IATManager);
  }
  return IAT;
});
