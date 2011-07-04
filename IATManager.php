<?php
session_set_cookie_params(1800);
session_start();

require_once 'connectDatabase.php';
require_once 'GlobalKLogger.php';

$FAILED_AUTHENTICATION_RETURN_VALUE = json_encode(array());


$iatManager = new IATManager;
$requestObject = $_POST['data'];
$requestName = $_POST['requestName'];
echo $iatManager->$requestName($requestObject);

/**
 * Description of IATManager
 *
 * @author Stephen Sealres
 */
class IATManager {
  
  public $databaseConnection;
  
  function __construct() {
    $this->databaseConnection = getDatabaseConnection();
  }
  
  function authenticate($credentials) {
    $username = $credentials['username'];
    $query = "SELECT * FROM users WHERE `username`='$username'";
    $result = mysql_query($query);
    if (mysql_num_rows($result) < 1) {
      return $this->authenticationFailed();
    }
    if (mysql_result($result, 0, 'passwordHash') === $credentials['passwordHash']) {
      return $this->authenticationSuccess();
    } else {
      return $this->authenticationFailed();
    }
    
  }
  function authenticationFailed() {
    $authenticationResult = array();
    $authenticationResult['authenticationMessage'] = 'Authentication failed';
    $authenticationResult['valid'] = false;
    return json_encode($authenticationResult);
  }
  function authenticationSuccess() {
    $authenticationResult = array();
    $_SESSION['authenticated'] = true;
    $authenticationResult['authenticationMessage'] = 'Authentication successful';
    $authenticationResult['valid'] = true;
    return json_encode($authenticationResult);
  }
  function verifyAuthentication() {
    return json_encode($_SESSION['authenticated']);
  }
  function requestExperimentList() {
    if (isset($_SESSION['authenticated'])) {
      if ($_SESSION['authenticated'] == false) return $FAILED_AUTHENTICATION_RETURN_VALUE;
    } else {
      return $FAILED_AUTHENTICATION_RETURN_VALUE;
    }
    $query = "SELECT stimuli_set,name,hash FROM experiments";
    $result = mysql_query($query,  $this->databaseConnection);
    return json_encode(arrayFromResult($result));
  }
  function requestExperiment($experimentNumber) {
    $experiment = $this->getExperiment($experimentNumber);
    $experiment['stimulusCategories'] = $this->getStimulusCategories($experimentNumber);
    return json_encode($experiment);
  }
  function getExperiment($experimentNumber) {
    $query = "SELECT * FROM experiments WHERE stimuli_set=$experimentNumber";
    $result = mysql_query($query, $this->databaseConnection);
    $experiment = objectFromResult($result);
    return objectFromResult($result);
  }
  function getStimuliForCategory($categoryNumber) {
    $query = "SELECT * FROM stimuli WHERE `stimulusCategory`=$categoryNumber ORDER BY `stimulus_id`";
    $result = mysql_query($query, $this->databaseConnection);
    return arrayFromResult($result);
  }
  function getStimulusCategories($experimentNumber) {
    $query = "SELECT * FROM stimulusCategories WHERE `experiment`=$experimentNumber";
    $result = mysql_query($query);
    $categories = assocArrayFromResult($result, "id", "name");
    foreach ($categories as $key => $value) {
      $finalCategories[$key] = array("name" => $value, "stimuli" => $this->getStimuliForCategory($key));
    }
    return $finalCategories;
  }
  function addExperiment() {
    
  }
  function removeExperiment($experimentNumber) {
    
  }
  function copyExperiment() {
    
  }
  function setExperimentProperties() {
    
  }

  function addStimulus() {
    
  }
  function removeStimulus() {
    
  }
  function insertStimulus() {
    
  }
  function moveStimulus() {
    
  }
  function setStimulusProperties() {
    
  }
  
  function addStimulusGroup() {
    
  }
  function removeStimulusGroup() {
    
  }
  function insertStimulusGroup() {
    
  }
  function moveStimulusGroup() {
    
  }
  function copyStimulusGroup() {
    
  }
  function setStimulusGroupProperties() {
    
  }
  
  function addStimulusCategory($name) {
    
  }
  function removeStimulusCategory($name) {
    
  }
  
}

function objectFromResult($result,$rowOffset = 0) {
  if ($result == null) return array();
  @mysql_data_seek($result, $rowOffset);
  return mysql_fetch_assoc($result);
}
function arrayFromResult($result,$rowOffset = 0) {
  if ($result == null) return array();
  $array = array();
  @mysql_data_seek($result, $rowOffset);
  while ($row = mysql_fetch_assoc($result)) {
    $array[] = $row;
  }
  return $array;
}
function assocArrayFromResult($result,$keyField,$valueField,$rowOffset = 0) {
  if ($result == null) return array();
  $array = array();
  @mysql_data_seek($result, $rowOffset);
  while ($row = mysql_fetch_assoc($result)) {
    $array[$row[$keyField]] = $row[$valueField];
  }
  return $array;
}

?>
