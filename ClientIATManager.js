

const newExperimentRequestType = "NewExperimentRequestType";

function bundleIATManagerRequestData(requestName, experimentNumber, dataObject) {
  return {"requestName":requestName,"experimentNumber":experimentNumber,"data":dataObject};
}

function sendRequest(requestObject) {
  return $.post('IATManagerRequestInterpreter.php',requestObject)
}
