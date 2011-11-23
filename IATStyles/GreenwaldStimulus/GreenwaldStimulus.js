define([],function () {
  var responseDeferred,currentBlock,currentStimulus,fixingError,errorLatency,previousDisplayTime;

  function pushResponse(response) {
    responseDeferred.response = response;
    responseDeferred.resolve();
  }

  var currentCategories = function (stimulusCategories,currentBlock) {
    var categories = {};
    $.each(currentBlock.components,function (index,component) {
      switch (component.position) {
        case '1':
          categories.categoryOne = stimulusCategories[component.category];
          break;
        case '2':
          categories.categoryTwo = stimulusCategories[component.category];
          break;
        case '3':
          categories.subCategoryOne = stimulusCategories[component.category];
          break;
        case '4':
          categories.subCategoryTwo = stimulusCategories[component.category];
          break;
      }
    });
    return categories;
  }
  var randomStimulusFromCategories = function (categories) {
    var stimuli = [];
    var totalOptions;
    $.each(categories,function (index,category) {
      stimuli = stimuli.concat(category.stimuli);
    });
    totalOptions = stimuli.length;
    var choiceCountdown = Math.floor(Math.random() * totalOptions);
    var chosenStimulus;
    $.each(categories,function (index,category) {
      if (choiceCountdown >= category.stimuli.length) {
        choiceCountdown -= category.stimuli.length;
        return true;
      } else {
        chosenStimulus = category.stimuli[choiceCountdown];
        return false;
      }
    });
    return chosenStimulus;
  }
  /*
   * Binds arrow keys and handles user input during the IAT.
   */
  function bindKeys(experiment,stepDisplay) {
    $(document).keydown(function (event) {
      /*
       * This is a workaround. This code should use event.timeStamp for a more
       * accurate measure, but there has been an open bug in Firefox since
       * 2004 regarding this value populating correctly. It is impossible to get
       * this level of accuracy from Firefox while this bug remains unresolved.
       *
       * https://bugzilla.mozilla.org/show_bug.cgi?id=238041
       *
       */
      var eventTime = new Date().getTime();
      var answer = checkAnswer(event.which);
      if (answer) {
        pushResponse({
          stimulus: currentStimulus.id,
          response: event.which,
          response_time: fixingError ? errorLatency : eventTime - previousDisplayTime,
          timeShown: previousDisplayTime
        });
        fixingError = false;
        stepDisplay.apply(experiment);
      } else if (answer === false) {
        fixingError = true;
        errorLatency = eventTime - previousDisplayTime;
        if (experiment.errorNotifications === '1') {
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
        if (currentBlock.components['1']) {
          leftTop = currentBlock.components['1'].category;
        }
        if (currentBlock.components['3']) {
          leftBottom = currentBlock.components['3'].category;
        }
        if (currentBlock.components['2']) {
          rightTop = currentBlock.components['2'].category;
        }
        if (currentBlock.components['4']) {
          rightBottom = currentBlock.components['4'].category;
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
  return {
    name : "Greenwald Stimulus",
    id : 1,
    prepare : function (experiment,stepDisplay) {
      bindKeys(experiment,stepDisplay);
    },
    populateStyle : function (categories,_currentBlock,$style) {
      currentBlock = _currentBlock;
      categories = currentCategories(categories,currentBlock);
      currentStimulus = randomStimulusFromCategories(categories);
      $('#iatBlockPos1',$style).text(categories.categoryOne ? categories.categoryOne.name : '');
      $('#iatBlockPos2',$style).text(categories.categoryTwo ? categories.categoryTwo.name : '');
      $('#iatBlockPos3',$style).text(categories.subCategoryOne ? categories.subCategoryOne.name : '');
      $('#iatBlockPos4',$style).text(categories.subCategoryTwo ? categories.subCategoryTwo.name : '');
      $('#GreenwaldStimulus_iatStimulus',$style).text(currentStimulus.word);
      return $style;
    },
    displayIn : function ($div,$iat) {
      previousDisplayTime = new Date().getTime();
      $iat.html($div);
      return previousDisplayTime;
    },
    getResponse : function () {
      responseDeferred = $.Deferred();
      return responseDeferred;
    }
  }
});



