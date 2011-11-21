define([],function () {
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
  var replaceAtPos = function (pos) {
    if (pos === 0) {
      replaceCategoryNameForPos(pos)
    } else {

    }
  }
  return {
    name : "Greenwald Stimulus",
    id : 1,
    populateStyle : function (categories,currentBlock,$style) {
      categories = currentCategories(categories,currentBlock);
      var stimulus = randomStimulusFromCategories(categories).word;
      $('#iatBlockPos1',$style).text(categories.categoryOne ? categories.categoryOne.name : '');
      $('#iatBlockPos2',$style).text(categories.categoryTwo ? categories.categoryTwo.name : '');
      $('#iatBlockPos3',$style).text(categories.subCategoryOne ? categories.subCategoryOne.name : '');
      $('#iatBlockPos4',$style).text(categories.subCategoryTwo ? categories.subCategoryTwo.name : '');
      $('#GreenwaldStimulus_iatStimulus',$style).text(stimulus);
      return $style;
    }
  }
});



