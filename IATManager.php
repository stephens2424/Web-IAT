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
  function getStimulus($id) {
    $query = "SELECT * FROM stimuli WHERE `id`=$id";
    $result = mysql_query($query,$this->databaseConnection);
    if ($result) {
      return array("success"=>true,"stimulus"=>objectFromResult($result));
    } else {
      return array("success"=>false,"message"=>"Error accessing stimulus.");
    }
  }
  function getStimuliForCategory($categoryNumber) {
    $query = "SELECT * FROM stimuli WHERE `stimulusCategory`=$categoryNumber ORDER BY `id`";
    $result = mysql_query($query, $this->databaseConnection);
    return arrayFromResult($result);
  }
  function getStimulusCategories($experimentNumber) {
    $query = "SELECT * FROM stimulusCategories WHERE `experiment`=$experimentNumber";
    $result = mysql_query($query);
    $categories = arrayFromResult($result, "id", "name");
    foreach ($categories as &$category) {
      $category['stimuli'] = $this->getStimuliForCategory($category['id']);
    }
    unset($category);
    return $categories;
  }
  function setStimulusProperties($requestObject) {
    $stimulus_id = $requestObject['id'];
    $query = "UPDATE `stimuli` SET ";
    if ($requestObject[word]) {
      $query .= "`word`='" . $requestObject['word'] . "'";
    }
    $query .= " WHERE `id`=" . $requestObject['id'];
    $result = mysql_query($query);
    if ($result) {
      return json_encode(array('success' => true,'message' => "Updating stimulus succeeded."));
    } else {
      return json_encode(array('success' => false,'message' => "Updating stimulus failed."));
    }
  }
  function addExperiment() {
    
  }
  function removeExperiment($experimentNumber) {
    
  }
  function copyExperiment() {
    
  }
  function setExperimentProperties() {
    
  }

  function addStimulus($requestObject) {
    $query = "INSERT INTO `stimuli` SET ";
    $set = "";
    if ($requestObject['experiment'] && $requestObject['stimulusCategory']) {
      $set .= "`experiment`=" . $requestObject['experiment'] . ",`stimulusCategory`=" . $requestObject['stimulusCategory'];
    } else {
      return json_encode(array('success' => false,'message'=>"Error: new stimulus is missing experiment number or category."));
    }
    if ($requestObject['word']) {
      $set .= ",`word`='" . $requestObject['word'] . "'";
    }
    if ($requestObject['correct_response']) {
      $set .= ",`correct_response`=" . $requestObject['correct_response'];
    }
    $query .= $set;
    $result = mysql_query($query);
    if ($result) {
      $stimulusResponse = $this->getStimulus(mysql_insert_id());
      if ($stimulusResponse['success']) {
        return json_encode(array('success'=>true,'stimulus'=>$stimulusResponse['stimulus'],'message'=>"Stimulus added."));
      } else {
        return json_encode(array('success'=>false,'message'=>"Error returning new stimulus."));
      }
    } else {
      return json_encode(array('success' => false,'message'=>"Error: adding new stimulus failed."));
    }
  }
  function deleteStimulus($requestObject) {
    if (!$requestObject['id']) {
      return json_encode(array('success' => false,'message'=>"Error: no stimulus id for deletion."));
    }
    $query = "DELETE FROM `stimuli` WHERE `id`=" . $requestObject['id'];
    $result = mysql_query($query,$this->databaseConnection);
    if ($result) {
      return json_encode(array('success' => true,'message'=>"Stimulus deleted."));
    } else {
      return json_encode(array('success' => false,'message'=>"Error: deleting stimulus failed."));
    }
  }
  function insertStimulus() {
    
  }
  function moveStimulus() {
    
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
  
  function addStimulusCategory($requestObject) {
    $query = "INSERT INTO `stimulusCategories` SET ";
    $set = "";
    if ($requestObject['experiment']) {
      $set .= "`experiment`=" . $requestObject['experiment'];
    } else {
      return json_encode(array('success' => false,'message'=>"Error: new category is missing experiment number."));
    }
    if ($requestObject['name']) {
      $set .= ",`name`='" . $requestObject['name'] . "'";
    }
    $query .= $set;
    $result = mysql_query($query);
    if ($result) {
      $stimulusResponse = $this->getStimulus(mysql_insert_id());
      if ($stimulusResponse['success']) {
        return json_encode(array('success'=>true,'stimulus'=>$stimulusResponse['stimulus'],'message'=>"Category added."));
      } else {
        return json_encode(array('success'=>false,'message'=>"Error returning new category."));
      }
    } else {
      return json_encode(array('success' => false,'message'=>"Error: adding new category failed."));
    }
  }
  function removeStimulusCategory($name) {
    
  }
  function setStimulusCategoryProperties($data) {
    $query = "UPDATE `stimulusCategories` SET ";
    if ($data.name) {
      $query .= "`name`='" . $data['name'] . "'";
    }
    $query .= " WHERE `id`=" . $data['id'];
    $result = mysql_query($query,$this->databaseConnection);
    if ($result === false) {
      return json_encode(array('success' => false,'message' => "Updating category failed."));
    } else {
      return json_encode(array('success' => true,'message' => "Updating category succeeded."));
    }
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
