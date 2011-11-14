require(["jquery","IAT","IATManager"], function ($,CoreIAT,IAT,IATManager) {
  $(function () {
    var IATManager = IAT.IATManager();
    IATManager.appendExperimentSelectorTo($('.contentDiv'));
  });
});
