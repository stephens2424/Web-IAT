<?php
session_start();
require_once 'GreenwaldIATProcessor.php';
$subj = $_POST['subj'];
if (isset($_GET['subj'])) {
  $subj = $_GET['subj'];
}
$score = GreenwaldIATProcessor::calculateAndSetScore($subj);
$_SESSION['score'] = $score;
echo $score;
exit;
?>
