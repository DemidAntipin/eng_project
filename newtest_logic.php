<?php

function selectedIf($key, $value){
                if (!empty($_POST[$key]) && $_POST[$key] === $value){ return ' selected';}
                return '';
        }

if ($_POST){

        if (!is_dir($testDir)) {
            if (!mkdir($testDir, 0755, true)) {
                die('Error, couldn`t create directory...');
            }
        }

        if (!is_writable($testDir)) {
            die('Directory is unavailable');
        }

        $errors = [];
        foreach ($_POST as $key => $value) {
                $_POST[$key]= trim(strtolower($value) ?? '');
                $value = $_POST[$key];
                if  (empty($value)) {$errors[$key]='Fill the field.';}
                if (preg_match('/question_(text|type)_(\d+)/', $key, $matches)) {
                        $questionNumbers[] = (int)$matches[2];
                }
        }

        $title = str_replace(' ', '_', $_POST['title']);
        $description = $_POST['description'];

        if (file_exists($testDir.$title.'.json')) $errors['title'] = 'Test with this title already exists. Please, input another title.';

        if (empty($errors)){
        $maxQuestionNumber = max($questionNumbers);

        $number_of_questions = $maxQuestionNumber;

        $form_data = [];

        $form_data[] = ['title' => $title];

        for ($i = 1; $i <= $number_of_questions; $i++) {
                $question = $_POST['question_text_'. $i];
                $question_type = $_POST['question_type_' . $i];
                $number_of_answers = $_POST['number_of_answers_' . $i];

                $answers = [];
                for ($j = 1; $j <= $number_of_answers; $j++) {
                        $answers[] = $_POST['answer_' . $i . '_' . $j];
                }

                $form_data[] = [
                        'question_text' => $question,
                        'question_type' => $question_type,
                        'answers' => $answers
                ];
        }

        $file_name = $testDir.$title.'.json';
        file_put_contents($file_name, json_encode($form_data));

        $sql = $db->prepare('INSERT INTO tests (title, description, created) VALUES (:title, :description, NOW());');
        $sql->execute(['title' => $title, 'description' => $description]);

        $_SESSION['success'] = 'Enter correct answers.';
        $_SESSION['test'] = $title;
        header('Location: test.php');
        exit;
        }
}
