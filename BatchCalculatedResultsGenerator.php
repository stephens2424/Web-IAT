<?php

function standard_deviation($array) {
  if ($array == null)
    return null;
  if (count($array) <= 1)
    return null;
  $mean = mean($array);
  foreach ($array as $value) {
    $variance += pow($value - $mean, 2);
  }
  return sqrt($variance / (count($array) - 1));
}

function mean($array) {
  if ($array == null)
    return null;
  $sum = array_sum($array);
  $num = count($array);
  return $sum / $num;
}

function reconcileStage($correctArray, $incorrectArray) {
  $combinedArray = $correctArray;
  if ($incorrectArray != null) {
    $addAmount = standard_deviation($correctArray) * 2; //can also be simply 600
    $passArray = array(
        "array" => &$combinedArray,
        "add" => $addAmount
    );
    array_walk($incorrectArray, reconcileThroughArrayWalk, $passArray);
  }
  return $combinedArray;
}

function reconcileThroughArrayWalk($value, $index, $passArray) {
  $passArray['array'][] = $value + $passArray['add'];
}

function handleRowWithMatchingSubject($theRow, $s3, $s3in, $s6, $s6in, $s4, $s4in, $s7, $s7in) {
  if (intval($theRow['response_time'], 10) > 10000) {
    return;
  }
  switch ($theRow['stage']) {
    case 3: {
        if (checkResponse($theRow['response'], $theRow['correct_response'])) {
          $s3[] = intval($theRow['response_time'], 10);
        } else {
          $s3in[] = intval($theRow['response_time'], 10);
        }
        break;
      }
    case 4: {
        if (checkResponse($theRow['response'], $theRow['correct_response'])) {
          $s4[] = intval($theRow['response_time'], 10);
        } else {
          $s4in[] = intval($theRow['response_time'], 10);
        }
        break;
      }
    case 6: {
        if (checkResponse($theRow['response'], $theRow['correct_response'])) {
          $s6[] = intval($theRow['response_time'], 10);
        } else {
          $s6in[] = intval($theRow['response_time'], 10);
        }
        break;
      }
    case 7: {
        if (checkResponse($theRow['response'], $theRow['correct_response'])) {
          $s7[] = intval($theRow['response_time'], 10);
        } else {
          $s7in[] = intval($theRow['response_time'], 10);
        }
        break;
      }
    default: {
        
      }
  }
}

function checkResponse($response, $correct_response) {
  switch ($correct_response) {
    case 0: {
        if ($response == "right") {
          return false;
        } else {
          return true;
        }
        break;
      }
    case 1: {
        if ($response == "right") {
          return true;
        } else {
          return false;
        }
        break;
      }
    default: {
        return false;
      }
  }
}

function handleSubject($currentSubject, $subjectScores, $stage3correctArray, $stage3incorrectArray, $stage6correctArray, $stage6incorrectArray, $stage4correctArray, $stage4incorrectArray, $stage7correctArray, $stage7incorrectArray) {
  //reconcile incorrect arrays
  $stage3array = reconcileStage($stage3correctArray, $stage3incorrectArray);
  $stage6array = reconcileStage($stage6correctArray, $stage6incorrectArray);
  $stage4array = reconcileStage($stage4correctArray, $stage4incorrectArray);
  $stage7array = reconcileStage($stage7correctArray, $stage7incorrectArray);
  //null check -- not part of greenwald procedure, but necessary programming check
  if ($stage3array == null)
    return;
  if ($stage6array == null)
    return;
  if ($stage4array == null)
    return;
  if ($stage7array == null)
    return;
  //"inclusive" standard deviations
  $stage3_6sd = standard_deviation(array_merge($stage3array, $stage6array));
  $stage4_7sd = standard_deviation(array_merge($stage4array, $stage7array));
  //take means of the stages
  $stage3mean = mean($stage3array);
  $stage6mean = mean($stage6array);
  $stage4mean = mean($stage4array);
  $stage7mean = mean($stage7array);
  //calculate subject score
  $subjectScores[$currentSubject] = mean(array(
      ($stage6mean - $stage3mean) / $stage3_6sd,
      ($stage7mean - $stage4mean) / $stage4_7sd
          ));
}

include 'connect.php';
$set = $_GET['set'];
$query = "SELECT responses.subj,subjects.qualtrics_id,responses.response,responses.response_time,stimuli.correct_response,stimuli.stimulusCategory,stimuliGroups.stage FROM responses JOIN (stimuli,stimuliGroups,subjects) ON (responses.stimulus=stimuli.stimulus_id AND stimuli.group=stimuliGroups.id AND subjects.id=responses.`subj`) WHERE (stimuli.set=14) ORDER BY responses.subj,responses.stimulus";
$result = mysql_query($query);

while ($row = mysql_fetch_assoc($result)) {
  if (isset($currentSubject)) {
    if ($currentSubject == $row['subj']) {
      handleRowWithMatchingSubject($row, &$stage3correctArray, &$stage3incorrectArray, &$stage6correctArray, &$stage6incorrectArray, &$stage4correctArray, &$stage4incorrectArray, &$stage7correctArray, &$stage7incorrectArray);
    } else {
      handleSubject($currentSubject, &$subjectScores, &$stage3correctArray, &$stage3incorrectArray, &$stage6correctArray, &$stage6incorrectArray, &$stage4correctArray, &$stage4incorrectArray, &$stage7correctArray, &$stage7incorrectArray);
      //reset for next subject
      $currentSubject = $row['subj'];
      $qualtricsIds[$currentSubject] = $row['qualtrics_id'];
      unset($stage3correctArray);
      unset($stage3incorrectArray);
      unset($stage4correctArray);
      unset($stage4incorrectArray);
      unset($stage6correctArray);
      unset($stage6incorrectArray);
      unset($stage7correctArray);
      unset($stage7incorrectArray);
      handleRowWithMatchingSubject($row, &$stage3correctArray, &$stage3incorrectArray, &$stage6correctArray, &$stage6incorrectArray, &$stage4correctArray, &$stage4incorrectArray, &$stage7correctArray, &$stage7incorrectArray);
    }
  } else {
    $currentSubject = $row['subj'];
    $qualtricsIds[$currentSubject] = $row['qualtrics_id'];
    handleRowWithMatchingSubject($row, &$stage3correctArray, &$stage3incorrectArray, &$stage6correctArray, &$stage6incorrectArray, &$stage4correctArray, &$stage4incorrectArray, &$stage7correctArray, &$stage7incorrectArray);
  }
}
handleSubject($currentSubject, &$subjectScores, &$stage3correctArray, &$stage3incorrectArray, &$stage6correctArray, &$stage6incorrectArray, &$stage4correctArray, &$stage4incorrectArray, &$stage7correctArray, &$stage7incorrectArray);

$columns = 2;
$out = '';
// Put the name of all fields
$out .= '"subject","qualtricsId","score"';
$out .= "\n";

// Add all values in the table
$row = 0;
foreach ($subjectScores as $key => $value) {
  $out .= '"' . $key . '","' . $qualtricsIds[$key] . '","' . $value . '"';
  $out .= "\n";
  $row++;
}
$filename = "results.csv";
// Output to browser with appropriate mime type
//header("Content-type: text/x-csv");
//header("Content-type: text/csv");
header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=$filename");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Disposition: attachment; filename = $filename");
header("Content-Length: " . strlen($out));
echo $out;
exit;
?>
