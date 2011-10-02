<?php
/*!
 * WebIAT
 * This project was originally developed for Margaret Shih and Geoff Ho at the UCLA Anderson School of Management by Stephen Searles.
 * This code is licensed under the Eclipse Public License (EPL) version 1.0, which is available here: http://www.eclipse.org/legal/epl-v10.html.
 * 
 * Author: Stephen Searles
 * Date: May 10, 2011
 */
session_set_cookie_params(1800);
session_start();

require_once '../configuration/config.php';
require_once '../connectDatabase.php';
require_once '../GlobalKLogger.php';

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
    if ($this->_authenticate($credentials)) {
      $authenticationResult = array();
      $_SESSION['authenticated'] = true;
      $authenticationResult['authenticationMessage'] = 'Authentication successful';
      $authenticationResult['valid'] = true;
      return json_encode($authenticationResult);
    } else {
      $authenticationResult = array();
      $authenticationResult['authenticationMessage'] = 'Authentication failed';
      $authenticationResult['valid'] = false;
      return json_encode($authenticationResult);
    }
  }
  private function _authenticate($credentials) {
    $username = $credentials['username'];
    $query = "SELECT * FROM users WHERE `username`='$username'";
    $result = mysql_query($query);
    if (mysql_num_rows($result) < 1) {
      return false;
    }
    if (mysql_result($result, 0, 'passwordHash') === $credentials['passwordHash']) {
      if (mysql_result($result, 0, 'userAdministration') === '1') {
        $_SESSION['userAdministration'] = true;
      }
      return true;
    } else {
      return false;
    }
  }
  private function _verifyAuthentication($permission = null) {
    if ($permission) {
      return $_SESSION[$permission];
    }
    return $_SESSION['authenticated'];
  }
  function verifyAuthentication($permission = null) {
    return json_encode($this->_verifyAuthentication($permission));
  }
  private function _createAuthenticationFailedReturnValue($commandName,$arguments = null) {
    return json_encode(array('success'=>false,
        'errorCode' => '1003',
        'message'=>'Authentication failed.',
        'command' => $commandName,
        'arguments' => $arguments));
  }
  private function _createInsufficientPermissionReturnValue($commandName,$arguments = null) {
    return json_encode(array('success'=>false,
        'errorCode' => '1004',
        'message'=>'Permission denied.',
        'command' => $commandName,
        'arguments' => $arguments));
  }
  function getExperimentNumberFromHash($hash) {
    $query = "SELECT `id` FROM experiments WHERE `hash`='$hash'";
    $result = mysql_query($query, $this->databaseConnection);
    $id = mysql_result($result,0,'id');
    return json_encode(array('experimentNumber' => $id));
  }
  function requestExperimentList() {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    $query = "SELECT id,name,hash FROM experiments";
    $result = mysql_query($query,  $this->databaseConnection);
    return json_encode(arrayFromResult($result));
  }
  function requestExperiment($experimentNumber) {
    $experiment = $this->getExperiment($experimentNumber);
    $experiment['stimulusCategories'] = $this->getStimulusCategories($experimentNumber);
    $experiment['blocks'] = $this->getBlocks($experimentNumber);
    $experiment['categoryPairs'] = $this->getCategoryPairs($experimentNumber);
    return json_encode($experiment);
  }
  function getExperiment($experimentNumber) {
    $query = "SELECT * FROM experiments WHERE id=$experimentNumber";
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
  function getBlocks($experimentNumber) {
    $query = "SELECT * FROM `blocks` WHERE `experiment`=$experimentNumber";
    $result = mysql_query($query,  $this->databaseConnection);
    $blocks = arrayFromResult($result);
    foreach ($blocks as &$block) {
      $id = $block['id'];
      $query = "SELECT * FROM `blockComponents` WHERE `block`=$id";
      $result = mysql_query($query,  $this->databaseConnection);
      $block['components'] = assocArrayFromResult($result,'position');
    }
    return $blocks;
  }
  function getStimuliForCategory($categoryNumber) {
    $query = "SELECT * FROM stimuli WHERE `stimulusCategory`=$categoryNumber ORDER BY `id`";
    $result = mysql_query($query, $this->databaseConnection);
    return arrayFromResult($result);
  }
  function getStimulusCategory($categoryNumber) {
    $query = "SELECT * FROM stimulusCategories WHERE `id`=$categoryNumber";
    $result = mysql_query($query);
    return objectFromResult($result);
  }
  function getStimulusCategories($experimentNumber) {
    $query = "SELECT * FROM stimulusCategories WHERE `experiment`=$experimentNumber";
    $result = mysql_query($query);
    $categories = assocArrayFromResult($result,"id");
    foreach ($categories as &$category) {
      $category['stimuli'] = $this->getStimuliForCategory($category['id']);
    }
    unset($category);
    return $categories;
  }
  function getCategoryPairs($experimentNumber) {
    $query = "SELECT * FROM categoryPairs WHERE `experiment`=$experimentNumber";
    $result = mysql_query($query,$this->databaseConnection);
    $pairs = arrayFromResult($result);
    return $pairs;
  }
  function setStimulusProperties($requestObject) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
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
  function addExperiment($requestObject) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    require_once 'hashGenerator.php';
    if ($requestObject["name"]) {
      $name = $requestObject["name"];
    } else {
      $name = "New Experiment";
    }
    $query = "INSERT INTO `experiments` SET `name`='$name'";
    $result = mysql_query($query);
    $id = mysql_insert_id();
    $hash = HashGenerator::udiHash($id);
    $query = "UPDATE `experiments` SET `hash`='$hash' WHERE `id`=$id";
    $result = mysql_query($query);
    $experiment = array('name' => $name, 'hash' => $hash, 'id' => $id);
    if (!$requestObject['type']) {
      $this->_applyDefaultGreenwaldBlocks($id);
    }
    return json_encode(array('success' => true, 'experiment' => $experiment));
  }
  function deleteExperiment($experimentNumber) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    if ($this->verifyAuthentication()) {
      $query = "START TRANSACTION";
      $result = mysql_query($query);

      $query = "SELECT `id` FROM `blocks` WHERE `experiment`=$experimentNumber";
      $result = mysql_query($query);
      while ($row = mysql_fetch_assoc($result)) {
        $blockId = $row['id'];
        $query = "DELETE FROM `blockComponents` WHERE `block`=$blockId";
        $intResult = mysql_query($query);
        if (!$intResult) $success = false;
      }
      $query = "DELETE FROM `blocks` WHERE `experiment`=$experimentNumber";
      $result = mysql_query($query);

      $query = "DELETE FROM `categoryPairs` WHERE `experiment`=$experimentNumber";
      $result = mysql_query($query);

      $query = "SELECT `id` FROM `stimuli` WHERE `experiment`=$experimentNumber";
      $result = mysql_query($query);
      while ($row = mysql_fetch_assoc($result)) {
        $stimulus = $row['id'];
        $query = "DELETE FROM `responses` WHERE `stimulus`=$stimulus";
        $intResult = mysql_query($query);
      }

      $query = "DELETE FROM `stimuli` WHERE `experiment`=$experimentNumber";
      $result = mysql_query($query);

      $query = "DELETE FROM `stimulusCategories` WHERE `experiment`=$experimentNumber";
      $result = mysql_query($query);

      $query = "DELETE FROM `subjects` WHERE `experiment`=$experimentNumber";
      $result = mysql_query($query);

      $query = "DELETE FROM `experiments` WHERE `id`=$experimentNumber";
      $result = mysql_query($query);

      $query = "COMMIT";
      $result = mysql_query($query);
      return json_encode(array('success' => true));
    } else {
      return json_encode(array('success' => false,'message'=>'Authentication failed.'));
    }
  }
  function copyExperiment() {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    
  }
  function setExperimentProperties($requestObject) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    if (!$requestObject['id']) {
      return json_encode(array('success'=>false,'message'=>'No experiment ID provided.'));
    }
    $set = "";
    if ($requestObject['checkAnswers']) {
      $checkAnswers = $requestObject['checkAnswers'] === "true" ? 1 : 0;
      $set .= " `checkAnswers`=$checkAnswers";
    }
    if ($requestObject['errorNotifications']) {
      $errorNotifications = $requestObject['errorNotifications'] === "true" ? 1 : 0;
      $set .= " `errorNotifications`=$errorNotifications";
    }
    if ($requestObject['autoBalance']) {
      $autoBalance = $requestObject['autoBalance'] === "true" ? 1 : 0;
      $set .= " `autoBalance`=$autoBalance";
    }
    if ($requestObject['endUrl']) {
      $endUrl = $requestObject['endUrl'];
      $set .= " `endUrl`='$endUrl'";
    }
    if ($requestObject['secondEndUrl']) {
      $secondEndUrl = $requestObject['secondEndUrl'];
      $set .= " `endUrl`='$secondEndUrl'";
    }
    if ($requestObject['active']) {
      $active = $requestObject['active'];
      $set .= " `active`=$active";
    }
    if ($set === "") {
      return json_encode(array('success'=>false,'message'=>'Nothing to change.'));
    } else {
      $id = $requestObject['id'];
      $query = "UPDATE `experiments` SET " . $set . " WHERE `id`=$id";
      $result = mysql_query($query);
      if ($result)
        return json_encode(array('success' => true));
      else
        return json_encode(array('success' => false,'message'=>'Update failed.'));
    }
  }
  private function _applyDefaultGreenwaldBlocks($experiment) {
    $firstCategory = $this->_addStimulusCategory($experiment);
    $secondCategory = $this->_addStimulusCategory($experiment);
    $thirdCategory = $this->_addStimulusCategory($experiment);
    $fourthCategory = $this->_addStimulusCategory($experiment);
    $firstCategoryPair = $this->_pairCategories($firstCategory['stimulusCategory']['id'],$secondCategory['stimulusCategory']['id'],$experiment);
    $firstCategoryPair = $firstCategoryPair['pair'];
    $secondCategoryPair = $this->_pairCategories($thirdCategory['stimulusCategory']['id'],$fourthCategory['stimulusCategory']['id'],$experiment);
    $secondCategoryPair = $secondCategoryPair['pair'];
    $this->_associatePairs($firstCategoryPair['id'],$secondCategoryPair['id']);
    $block = $this->_addBlock($experiment,20,"Block 1, Practice");
    $this->_addBlockComponent($block['blockId'],$firstCategoryPair[positiveCategory],1);
    $this->_addBlockComponent($block['blockId'],$firstCategoryPair[negativeCategory],2);
    $block = $this->_addBlock($experiment,20,"Block 2, Practice");
    $this->_addBlockComponent($block['blockId'],$secondCategoryPair[positiveCategory],1);
    $this->_addBlockComponent($block['blockId'],$secondCategoryPair[negativeCategory],2);
    $block = $this->_addBlock($experiment,20,"Block 3, Practice");
    $this->_addBlockComponent($block['blockId'],$firstCategoryPair[positiveCategory],1);
    $this->_addBlockComponent($block['blockId'],$firstCategoryPair[negativeCategory],2);
    $this->_addBlockComponent($block['blockId'],$secondCategoryPair[positiveCategory],3);
    $this->_addBlockComponent($block['blockId'],$secondCategoryPair[negativeCategory],4);
    $block = $this->_addBlock($experiment,20,"Block 4, Test");
    $this->_addBlockComponent($block['blockId'],$firstCategoryPair[positiveCategory],1);
    $this->_addBlockComponent($block['blockId'],$firstCategoryPair[negativeCategory],2);
    $this->_addBlockComponent($block['blockId'],$secondCategoryPair[positiveCategory],3);
    $this->_addBlockComponent($block['blockId'],$secondCategoryPair[negativeCategory],4);
    $block = $this->_addBlock($experiment,20,"Block 5, Practice");
    $this->_addBlockComponent($block['blockId'],$firstCategoryPair[negativeCategory],1);
    $this->_addBlockComponent($block['blockId'],$firstCategoryPair[positiveCategory],2);
    $block = $this->_addBlock($experiment,20,"Block 6, Practice");
    $this->_addBlockComponent($block['blockId'],$firstCategoryPair[negativeCategory],1);
    $this->_addBlockComponent($block['blockId'],$firstCategoryPair[positiveCategory],2);
    $this->_addBlockComponent($block['blockId'],$secondCategoryPair[positiveCategory],3);
    $this->_addBlockComponent($block['blockId'],$secondCategoryPair[negativeCategory],4);
    $block = $this->_addBlock($experiment,20,"Block 7, Test");
    $this->_addBlockComponent($block['blockId'],$firstCategoryPair[negativeCategory],1);
    $this->_addBlockComponent($block['blockId'],$firstCategoryPair[positiveCategory],2);
    $this->_addBlockComponent($block['blockId'],$secondCategoryPair[positiveCategory],3);
    $this->_addBlockComponent($block['blockId'],$secondCategoryPair[negativeCategory],4);
    return json_encode(array('success' => true));
  }
  private function _addBlockComponent($block,$category,$position) {
    $query = "INSERT INTO `blockComponents` SET `block`=$block,`category`=$category,`position`=$position";
    $result = mysql_query($query,  $this->databaseConnection);
    return array('success' => true,'blockComponentId' => mysql_insert_id()); //TODO add code to handle failure
  }
  private function _addBlock($experiment,$trials = 20,$description = "New Block") {
    $query = "INSERT INTO `blocks` SET `trials`=$trials,`description`='$description',`experiment`=$experiment";
    $result = mysql_query($query,$this->databaseConnection);
    return array('success' => true, 'blockId' => mysql_insert_id()); //TODO add code to handle failure
  }
  function addBlockComponent($block,$category,$position) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    return json_encode(_addBlockComponent($block,$category,$position));
  }
  function addBlock($experiment,$trials = 20,$description = "New Block") {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    return json_encode(_addBlock($experiment,$trials,$description));
  }
  private function _setBlockProperties($block,$trials = null,$description = null) {
    $set = "";
    if ($trials) {
        $set .= " `trials`=$trials";
    }
    if ($description) {
      $set .= " `description`='$description'";
    }
    if ($set == "") {
      return array('success'=>false,'message'=>'Nothing to change');
    } else {
      $query = "UPDATE `blocks` SET ". $set ." WHERE `id`=$block";
      $result = mysql_query($query);
      return array('success'=>true);
    }
  }
  function setBlockProperties($requestObject) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    return $this->_setBlockProperties($requestObject['block'], $requestObject['trials'], $requestObject['description']);
  }
  function addStimulus($requestObject) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
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
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
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
  function recordResponses($requestObject) {
    $query = "INSERT INTO `subjects` SET ";
    $beginTime = $requestObject['beginTime'];
    $query .= "`beginTime`=$beginTime";
    $experiment = $requestObject['experiment'];
    $query .= ",`experiment`=$experiment";
    if ($requestObject['qualtrics_id']) {
      $qid = $requestObject['qualtrics_id'];
      $query .= ",`qualtrics_id='$qid' ";
    }
    $result = mysql_query($query,$this->databaseConnection);
    $subjectId = mysql_insert_id();
    foreach ($requestObject['responses'] as $response) {
      $stimulus = $response['stimulus'];
      $responseText = $response['response'];
      $responseTime = $response['response_time'];
      $timeShown = $response['timeShown'];
      $query = "INSERT INTO `responses` SET ";
      $query .= "`subj`=$subjectId ";
      $query .= ",`stimulus`=$stimulus";
      $query .= ",`response`=$responseText ";
      $query .= ",`response_time`=$responseTime ";
      $query .= ",`timeShown`=$timeShown";
      mysql_query($query,$this->databaseConnection);
    }
    return json_encode(array('success'=>true,'message'=>"Responses recorded."));
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
  private function _addStimulusCategory($experiment,$name = null) {
    $query = "INSERT INTO `stimulusCategories` SET ";
    $set = "";
    if ($experiment) {
      $set .= "`experiment`=" . $experiment;
    } else {
      return array('success' => false,'message'=>"Error: new category is missing experiment number.");
    }
    if ($name) {
      $set .= ",`name`='" . $name . "'";
    } else {
      $set .= ",`name`='New Category'";
    }
    $query .= $set;
    $result = mysql_query($query);
    if ($result) {
      $stimulusCategory = $this->getStimulusCategory(mysql_insert_id());
      if ($stimulusCategory) {
        return array('success'=>true,'stimulusCategory'=>$stimulusCategory,'message'=>"Category added.");
      } else {
        return array('success'=>false,'message'=>"Error returning new category.");
      }
    } else {
      return array('success' => false,'message'=>"Error: adding new category failed.");
    }
  }
  private function _pairCategories($positiveCategory,$negativeCategory,$experiment) {
    $query = "INSERT INTO `categoryPairs` SET `positiveCategory`=$positiveCategory,`negativeCategory`=$negativeCategory,`experiment`=$experiment";
    $result = mysql_query($query);
    if ($result) {
      $id = mysql_insert_id();
      $pair = array('id' => $id,'positiveCategory'=>$positiveCategory,'negativeCategory'=>$negativeCategory,'experiment'=>$experiment);
      $query = "UPDATE `stimulusCategories` SET `inPair`=$id WHERE `id`=$positiveCategory OR $negativeCategory";
      $result = mysql_query($query);
      return array('success' => true, 'pair'=>$pair);
    } else {
      return array('success' => false, 'message' => 'Unable to pair categories.');
    }
  }
  function pairCategories($requestObject) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    return json_encode(_pairCategories($requestObject['positiveCategory'],$requestObject['negativeCategory'],$requestObject['experiment']));
  }
  private function _associatePairs($firstPair,$secondPair) {
    $query = "UPDATE `categoryPairs` SET `associatedPair`=$secondPair WHERE `id`=$firstPair";
    $result = mysql_query($query);
    $query = "UPDATE `categoryPairs` SET `associatedPair`=$firstPair WHERE `id`=$secondPair";
    $result = mysql_query($query);
    return array('success' => true);
  }
  function associatePairs($requestObject) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    return json_encode(_associatePairs($requestObject['firstPair'],$requestObject['secondpair']));
  }
  function addStimulusCategory($requestObject) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    return json_encode(_addStimulusCategory($requestObject['experiment'],$requestObject['name']));
  }
  function removeStimulusCategory($name) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
  }
  function setStimulusCategoryProperties($data) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
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
  function setUserPrivileges($data) {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    if (!$this->_verifyAuthentication('userAdministration')) return $this->_createInsufficientPermissionReturnValue(__FUNCTION__);
    $query = "UPDATE `users` SET ";
    if (isset($data['userAdministration'])) {
      if ($data['userAdministration'] === "true") {
        $query .= "`userAdministration`=1 ";
      } else {
        $query .= "`userAdministration`=0 ";
      }
    }
    $query .= "WHERE `id`=" . $data['id'];
    $result = mysql_query($query,  $this->databaseConnection);
    if ($result === false) {
      return json_encode(array('success'=> false,'message'=>"Setting user privileges failed."));
    } else {
      return json_encode(array('success'=> true,'message'=>"Setting user privileges succeeded."));
    }
  }
  function getUsers() {
    if (!$this->_verifyAuthentication()) return $this->_createAuthenticationFailedReturnValue(__FUNCTION__);
    if (!$this->_verifyAuthentication('userAdministration')) return $this->_createInsufficientPermissionReturnValue(__FUNCTION__);
    $query = "SELECT `username`,`userAdministration`,`email`,`id` FROM `users`";
    $result = mysql_query($query);
    return json_encode(array('success' => true,
        'users' => arrayFromResult($result)));
  }
  function registerUser($data) {
    $username = $data['username'];
    $passwordHash = $data['passwordHash'];
    $email = $data['email'];
    $query = "INSERT INTO `users` SET `username`='$username',`passwordHash`='$passwordHash',`email`='$email'";
    $result = mysql_query($query, $this->databaseConnection);
    if ($result)
      return json_encode(array('success' => true));
    else
      return json_encode(array('success' => false));
  }
}
function arrayOfArraysFromResult($result,$rowOffset = 0) {
  if ($result == null) return array();
  $array = array();
  @mysql_data_seek($result, $rowOffset);
  while ($row = mysql_fetch_array($result)) {
    $array[] = $row;
  }
  return $array;
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
function assocArrayFromResult($result,$keyField,$valueField = null,$rowOffset = 0) {
  if ($result == null) return array();
  $array = array();
  @mysql_data_seek($result, $rowOffset);
  if ($valueField === null) {
    while ($row = mysql_fetch_assoc($result)) {
      $array[$row[$keyField]] = $row;
    }
  } else {
    while ($row = mysql_fetch_assoc($result)) {
      $array[$row[$keyField]] = $row[$valueField];
    }
  }
  return $array;
}

?>
