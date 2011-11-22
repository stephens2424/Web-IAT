define(["CoreIAT","jquery.jnotify/lib/jquery.jnotify"],function (CoreIAT) {
    /*
     * Produces an IAT object and puts an IAT experiment interface into the
     * passed in div. It is unauthenticated and does not contain access to
     * manipulation functions.
     */
    var IAT = function (experimentHash,div) {
      var experiment = requestExperimentWithHash(experimentHash,function () {
        var $experimentDiv = experiment.iat();
        div.append($experimentDiv);
      });
    }

    /*
     * Prototype of Experiment objects
     */
    var Experiment = function () {

      //Private variables

      /*
       * Outer reference to self, for the purpose of simplifying closures.
       */
      var self;

      /*
       * An array of response objects.
       */
      var responses = [];
      var beginTime;

      /*
       * A record of the current stimulus ID.
       */

      var currentTrial = 0;
      var currentBlock,remainingBlocks;

      var styleModules = [];


      //Private functions


      var stepDisplay;
      var stepDisplayDeferred = $.Deferred();
      var initialize = function initialize() {
        require(['IATStyleLoader'].concat(this.stylePaths),function (IATStyleLoader) {
          var requiredStyleModules = Array.prototype.slice.call(arguments).slice(1);
          /*
           * Updates user interface to move from stimuli to stimuli, including
           * categories.
           */
          stepDisplay = function stepDisplay($context) {
            $(document).off('keydown');
            var styleModule,$styleDeferred;
            currentTrial += 1;
            if (currentTrial > parseInt(currentBlock.trials,10)) {
              currentTrial = 1;
              currentBlock = remainingBlocks.shift();
              if (currentBlock == null || currentBlock == undefined) {
                endIAT();
                return;
              }
            }
            var styleData = currentBlock.style;
            if (styleModules[styleData.id]) {
              $styleDeferred = $.Deferred();
              styleModule = styleModules[styleData.id];
              $styleDeferred.resolve();
            } else {
              $styleDeferred = IATStyleLoader.loadStyle(styleData.prefix);
              styleModule = $.grep(requiredStyleModules,function(elem,idx) {
                return elem.id == styleData.id;
              });
              styleModule = styleModule[0];
            }
            $styleDeferred.done(function () {
              styleModule.prepare(self,stepDisplay);
              var $populatedStyle = styleModule.populateStyle(self.stimulusCategories,currentBlock,$styleDeferred.loadedStyle);
              var responseDeferred = styleModule.getResponse().done(function () {
                if (beginTime == null || beginTime == undefined) {
                  beginTime = responseDeferred.response.timeShown;
                }
                responses.push(responseDeferred.response);
              });
              styleModule.displayIn($populatedStyle,$('#iat'));
            });
          }
          stepDisplayDeferred.resolve();
        });
      }
      function endIAT() {
        $.jnotify("End reached. Moving to end URLs not implemented.");
        CoreIAT.sendRequest(CoreIAT.bundleIATManagerRequestData("recordResponses",{
          responses: responses,
          experiment: self.experimentNumber,
          beginTime: beginTime
        })).done(function (data) {
          $.jnotify(data.message);
        });
      }

      /*
       * Exports public variables and functions.
       */
      return {
        name : null,
        experimentNumber : null,
        stimulusCategories : null,
        authentication: null,
        iat : function() {
          self = this;
          var $iat = $('<div id="iat">');
          remainingBlocks = self.blocks;
          currentBlock = remainingBlocks.shift();
          stepDisplayDeferred.done(function () {
            stepDisplay.apply(self,$iat);
          });

          initialize.apply(self);
          return $iat;
        }
      }
    }
    IAT.Experiment = Experiment();

  function requestExperiment(experimentNumber,callback) {
    var experimentPromise = $.Deferred().done(callback);
    var experiment = Object.create(IAT.Experiment);
    experiment.experimentNumber = experimentNumber;
    experiment.experimentPromise = experimentPromise;
    experiment.authentication = null;
    CoreIAT.sendRequest(CoreIAT.bundleIATManagerRequestData('requestExperiment',experimentNumber,null)).done(function (data) {
      $.extend(experiment,data);
      var stylePaths = [];
      var styles = [];
      $.each(experiment.blocks,function (idx,elem) {
        stylePaths.push(CoreIAT.IATStyleBaseURL + elem.style.filePath + '/' + elem.style.filePath + '.js');
        styles.push(elem.style);
      });
      experiment.styles = styles;
      experiment.stylePaths = stylePaths;
      experimentPromise.resolve();
    });
    return experiment;
  }
  function requestExperimentWithHash(experimentHash,callback) {
    var receivedData = CoreIAT.sendSynchronousRequest(CoreIAT.bundleIATManagerRequestData('getExperimentNumberFromHash',experimentHash));
    var data = JSON.parse(receivedData.responseText);
    return requestExperiment(data.experimentNumber,callback);
  }

  return $.extend(IAT,CoreIAT);

});


