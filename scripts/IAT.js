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

      /*
       * A record of the current stimulus ID.
       */
      var currentStimulus;

      var currentTrial = 0;
      var currentBlock = 0;

      /*
       * A variable reflecting the configuration of whether or not to require
       * correct answers of participants. Default is set to false.
       */
      var fixingError = false;

      /*
       * The error latency of the current trial
       */
      var errorLatency = 0;

      /*
       * The previous display time of the current trial
       */
      var previousDisplayTime;

      /*
       * The begin time of the current IAT
       */
      var beginTime;

      var stimuli =[];
      var currentStimuli = [];
      var iatDoms = [];
      var iatDependencies = [];

      //Private functions

      /*
       * Binds arrow keys and handles user input during the IAT.
       */
      function bindKeys(experiment) {
        $(document).keydown(function (event) {
          var answer = checkAnswer(event.which);
          if (answer) {
            responses.push({
              stimulus: currentStimulus.id,
              response: event.which,
              response_time: fixingError ? errorLatency : event.timeStamp - previousDisplayTime,
              timeShown: previousDisplayTime
            });
            fixingError = false;
            stepDisplay.apply(experiment);
          } else if (answer === false) {
            fixingError = true;
            errorLatency = event.timeStamp - previousDisplayTime;
            if (self.errorNotifications === '1') {
              $.jnotify("Incorrect");
            }
          }
        });
        function checkAnswer(key) {
          if (experiment.checkAnswers === "1") {
            var leftTop;
            var leftBottom;
            var rightTop;
            var rightBottom;
            if (experiment.blocks[currentBlock].components['1']) {
              leftTop = experiment.blocks[currentBlock].components['1'].category;
            }
            if (experiment.blocks[currentBlock].components['3']) {
              leftBottom = experiment.blocks[currentBlock].components['3'].category;
            }
            if (experiment.blocks[currentBlock].components['2']) {
              rightTop = experiment.blocks[currentBlock].components['2'].category;
            }
            if (experiment.blocks[currentBlock].components['4']) {
              rightBottom = experiment.blocks[currentBlock].components['4'].category;
            }
            if (key === 37 && (currentStimulus.stimulusCategory === leftTop || currentStimulus.stimulusCategory === leftBottom)) {
              return true;
            } else if (key === 39 && (currentStimulus.stimulusCategory === rightTop || currentStimulus.stimulusCategory === rightBottom)) {
              return true;
            } else if (key !== 37 && key != 39) {
              return null;
            } else {
              return false;
            }
          }
          return true;
        }
      }
      var stepDisplay;
      var stepDisplayDeferred = $.Deferred();
      var initialize = function initialize() {
        require(['IATStyleLoader'].concat(this.stylePaths),function (IATStyleLoader) {
          var styleModules = Array.prototype.slice.call(arguments).slice(1);
          /*
           * Updates user interface to move from stimuli to stimuli, including
           * categories.
           */
          stepDisplay = function stepDisplay($context) {
            currentTrial += 1;
            if (currentTrial > currentBlock.trials) {
              currentTrial = 1;
              currentBlock += 1;
              if (!this.blocks[currentBlock]) {
                endIAT();
                return;
              }
            }
            var styleData = this.blocks[currentBlock].style;
            var $styleDeferred = IATStyleLoader.loadStyle(styleData.prefix);
            var styleModule = $.grep(styleModules,function(elem,idx) {
              return elem.id == styleData.id;
            });
            styleModule = styleModule[0];
            $styleDeferred.done(function () {
              var $populatedStyle = styleModule.populateStyle(self.stimulusCategories,self.blocks[currentBlock],$styleDeferred.loadedStyle);
              previousDisplayTime = new Date().getTime();
              $('#iat').html($populatedStyle);
            });
          }
          stepDisplayDeferred.resolve();
        });
      }
      function endIAT() {
        $.jnotify("End reached. Moving to end URLs not implemented.");
        $(document).unbind("keydown");
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
          bindKeys(this,$iat);
          stepDisplayDeferred.done(function () {
            stepDisplay.apply(self,$iat);
          });
          initialize.apply(self);
          beginTime = previousDisplayTime;
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


