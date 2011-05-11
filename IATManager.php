<?php

require_once 'connectDatabase.php';
require_once 'GlobalKLogger.php';

$requestObject = $_GET['requestObject'];
$iatManager = new IATManager;

$requestName = $requestObject['requestName'];
echo $iatManager->$requestName($requestObject['data']);

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
  
  function addExperiment();
  function removeExperiment($experimentNumber);
  function copyExperiment();
  function setExperimentProperties();
  
  function addStimulus();
  function removeStimulus();
  function insertStimulus();
  function moveStimulus();
  function setStimulusProperties();
  
  function addStimulusGroup();
  function removeStimulusGroup();
  function insertStimulusGroup();
  function moveStimulusGroup();
  function copyStimulusGroup();
  function setStimulusGroupProperties();
  
  function addStimulusCategory($name);
  function removeStimulusCategory($name);
  
}

?>
