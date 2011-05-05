<?php
session_start();
require_once 'GreenwaldIATProcessor.php';
require_once 'hashGenerator.php';
$subj = $_POST['subj'];
if (isset($_GET['subj'])) {
  $subj = $_GET['subj'];
}
$set = $_POST['set'];
if (isset($_GET['set'])) {
  $set = $_GET['set'];
}
$hash = HashGenerator::udihash($set);
$score = GreenwaldIATProcessor::calculateAndSetScore($subj,$hash);
$_SESSION['score'] = $score;
echo $score;
exit;
?>
