<?php

require_once 'connectDatabase.php';
require_once 'GlobalKLogger.php';


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
  
  function requestExperimentList() {
    $query = "SELECT stimuli_set,name,hash FROM experiments";
    $result = mysql_query($query,  $this->databaseConnection);
    return json_encode(arrayFromResult($result));
  }
  function requestExperiment($experimentNumber) {
    $query = "SELECT ";
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

function arrayFromResult($result) {
    while ($row = mysql_fetch_assoc($result)) {
      $array[] = $row;
    }
    return $array;
  }

?>
