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

require_once 'connectDatabase.php';
require_once 'GlobalKLogger.php';

$FAILED_AUTHENTICATION_RETURN_VALUE = json_encode(array('success'=>false,'message'=>'Authentication failed.'));


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
    $categories = arrayFromResult($result);
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
      $this->applyDefaultGreenwaldBlocks($id);
    }
    return json_encode(array('success' => true, 'experiment' => $experiment));
  }
  function deleteExperiment($experimentNumber) {
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
    
  }
  function setExperimentProperties($requestObject) {
    if (!$requestObject['id']) {
      return json_encode(array('success'=>false,'message'=>'No experiment ID provided.'));
    }
    $set = "";
    if ($requestObject['checkAnswers']) {
      $checkAnswers = $requestObject['checkAnswers'] === "true" ? 1 : 0;
      $set .= " `checkAnswers`=$checkAnswers";
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
  function applyDefaultGreenwaldBlocks($experiment) {
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
    return json_encode(_addBlockComponent($block,$category,$position));
  }
  function addBlock($experiment,$trials = 20,$description = "New Block") {
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
    return $this->_setBlockProperties($requestObject['block'], $requestObject['trials'], $requestObject['description']);
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
    return json_encode(_associatePairs($requestObject['firstPair'],$requestObject['secondpair']));
  }
  function addStimulusCategory($requestObject) {
    return json_encode(_addStimulusCategory($requestObject['experiment'],$requestObject['name']));
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
