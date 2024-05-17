<?php

if (isset($_SESSION['test'])) {
    $filename = $_SESSION['test'];
}
else {
        header('Location: /');
}

$json = file_get_contents($testDir.$filename.'.json');
$questions = json_decode($json, true);
$title = $questions[0]['title'];
$answersFileExists = file_exists($answerDir.$title.'.json');

if ($_POST){
    if (!$answersFileExists) {
        if (!is_dir($answerDir)) {
            if (!mkdir($answerDir, 0755, true)) {
                die('Error, couldn`t create directory...');
            }
        }

        if (!is_writable($answerDir)) {
            die('Directory is unavailable');
        }

        $answersData = [];
        foreach ($_POST as $question => $answer) {
            if (strpos($question, 'question_') === 0) {
		if (is_array($answer)) {
                $answersData[$question] = implode(', ', $answer);}
		else $answersData[$question] = $answer;
            }
        }
        file_put_contents($answerDir.$title.'.json', json_encode($answersData));
        unset($_SESSION['success']);
        unset($_SESSION['test']);
        header('Location: /');
    }
    else {
    foreach ($_POST as $key => &$value) {
        $value = empty($value) ? 'IamCheater' : $value;
    }

    if ($answersFileExists) {
        $answers = file_get_contents($answerDir . $_POST['title'] . '.json');
        $answers = json_decode($answers, true);

	foreach ($_POST as $key => &$value) {
            $value = empty($value) ? '(None_answer)' : $value;
	}

        $success = 0;

        $_SESSION['your_answers'] = [];
        $_SESSION['correct_answers'] = [];

        foreach ($_POST as $question => $answer) {
            if ($question !== 'title') {
                if (isset($answers[$question])) {
                    if (is_array($answer)) {
                        $flag = true;
                        foreach ($answer as $i => $option) {
                            $flag = isset($answers[$question][$i]) && explode(', ', $answers[$question])[$i] === $option ? $flag : false;
                        }
                        if ($flag) {
                            $success++;
                        }
			else {
                            $question_id = explode('_', $question)[1] + 1;
                            $_SESSION['your_answers'][$questions[$question_id]['question_text']] = $answer;
                            $_SESSION['correct_answers'][$questions[$question_id]['question_text']] = $answers[$question];
                        }
                    } 
		    else {
                        if (isset($answers[$question]) && $answers[$question] === $answer) {
                            $success++;
                        } 
			else {
                            $question_id = explode('_', $question)[1] + 1;
                            $_SESSION['your_answers'][$questions[$question_id]['question_text']] = $answer;
                            $_SESSION['correct_answers'][$questions[$question_id]['question_text']] = $answers[$question];
                        }
                    }
                }
	    }
	}

        $totalQuestions = count($answers);
        $score = $success / $totalQuestions;

        if ($score <= 0.61) $_SESSION['mark'] = 'F';
        else if ($score <= 0.72) $_SESSION['mark'] = 'C';
        else if ($score <= 0.86) $_SESSION['mark'] = 'B';
        else $_SESSION['mark'] = 'A';
        header('Location: test.php');
        }
    }
    exit;
}
