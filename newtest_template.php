<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .site-header, .site-footer {
            background: #333;
            color: white;
        }

        .content {
            padding: 20px 0;
        }

        .container {
            width: 80%;
            margin: auto;
            padding-bottom: 10px;
        }

        .btn {
            padding: 10px 15px;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .test-result, .form-container {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
        }

        .mark, .form-title {
            font-weight: bold;
        }

        .return-btn {
            background-color: #28a745; 
        }

        .return-btn:hover {
            background-color: #218838;
        }

        .error-message {
            color: red;
        }

        .question-block {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
        }

        .question-label, .answer-label {
            display: inline-block;
            margin-bottom: 15px;
            font-size: 1.2em;
            font-weight: bold;
        }

        .input-text {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
        }

        .input-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .submit-btn {
            background-color: #007bff;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="content">
        <div class="container form-container">
            <form action="newtest.php" method="POST">
                <label for='title' class="question-label">Test title</label>
                <input type='text' name='title' id='title' class="input-text" value="<?= $_POST['title'] ?? '' ?>">
                <div class="error-message"><?= $errors['title'] ?? '' ?></div>

                <label for='description' class="question-label">Test description</label>
                <textarea name='description' id='description' class="input-textarea" rows="6"><?= $_POST['description'] ?? '' ?></textarea>
                <div class="error-message"><?= $errors['description'] ?? '' ?></div>

                <div id="questions" class="content">
			<div id="question1" class="question-block">
    <h3 class="form-title">Question 1</h3>
    <label for="question_text_1" class="question-label">Question:</label>
    <input type="text" name="question_text_1" id="question_text_1" class="input-text" value="<?= $_POST['question_text_1'] ?? '' ?>">
    <div class="error-message"><?= $errors['question_text_1'] ?? '' ?></div>

    <label for="question_type_1" class="question-label">Type of question:</label>
    <select name="question_type_1" id="question_type_1" class="input-text">
        <option value="text" <?= selectedIf('question_type_1', 'text') ?>>Text</option>
        <option value="radio" <?= selectedIf('question_type_1', 'radio') ?>>Radio</option>
        <option value="checkbox" <?= selectedIf('question_type_1', 'checkbox') ?>>Checkbox</option>
    </select>
    <div class="error-message"><?= $errors['question_type_1'] ?? '' ?></div>

    <label for="number_of_answers_1" class="question-label">Number of answers:</label>
    <input  type="number" name="number_of_answers_1" id="number_of_answers_1" class="input-text" value="<?= $_POST['number_of_answers_1'] ?? 1 ?>" min="1">
    <div class="error-message"><?= $errors['number_of_answers_1'] ?? '' ?></div>

    <div id="answers_1" class="answers-container">
    <div class="answer-block">
	<label for="answer_1_1" class="answer-label">Answer 1: </label>
        <input type="text" id="answer_1_1" name="answer_1_1" class="answer-input">
    </div>
</div>
</div>
		</div>
		<input type="button" value="Add question" class="btn return-btn" onclick="addQuestion()"><br><br>
                <input type="submit" value="Save test" class="btn submit-btn">
            </form>
        </div>
</div>

    <script>
        function addQuestion() {
    let errors = <?= json_encode($errors) ?>;
    let post = <?= json_encode($_POST) ?>;
    let questionCount = document.querySelectorAll('#questions > div').length + 1;
    let questionHTML = `
        <div id="question${questionCount}" class="question-block">
            <h3 class="form-title">Question ${questionCount}</h3>
            <label for="question_text_${questionCount}" class="question-label">Question:</label>
            <input type="text" name="question_text_${questionCount}" id="question_text_${questionCount}" class="input-text" value="${post[`question_text_${questionCount}`] ?? ''}">
            <div class="error-message">${errors[`question_text_${questionCount}`] ?? ''}</div>

            <label for="question_type_${questionCount}" class="question-label">Type of question:</label>
            <select name="question_type_${questionCount}" id="question_type_${questionCount}" class="input-text">
                <option value="text" ${post[`question_type_${questionCount}`] === "text" ? 'selected' : ''}">Text</option>
                <option value="radio" ${post[`question_type_${questionCount}`] === "radio" ? 'selected' : ''}>Multiple choice</option>
                <option value="checkbox" ${post[`question_type_${questionCount}`] === "checkbox" ? 'selected' : ''}>Checkbox</option>
            </select>
            <div class="error-message">${errors[`question_type_${questionCount}`] ?? ''}</div>

            <label for="number_of_answers_${questionCount}" class="question-label">Number of answers:</label>
            <input type="number" name="number_of_answers_${questionCount}" id="number_of_answers_${questionCount}" class="input-text" value="${post[`number_of_answers_${questionCount}`] ?? 1}" min="1" onchange="addAnswerFields(${questionCount}, this.value)">
            <div class="error-message">${errors[`number_of_answers_${questionCount}`] ?? ''}</div>

            <div id="answers_${questionCount}" class="answers-container">
            </div>
        </div>
    `;

    document.querySelector('#questions').insertAdjacentHTML('beforeend', questionHTML);
    addAnswerFields(questionCount, post[`number_of_answers_${questionCount}`] ?? 1);
}

        function addAnswerFields(questionNumber, numberOfAnswers) {
    let errors = <?= json_encode($errors) ?>;
    let post = <?= json_encode($_POST) ?>;
    let answersDiv = document.getElementById(`answers_${questionNumber}`);
    answersDiv.innerHTML = '';
    if (numberOfAnswers < 1) { 
        numberOfAnswers = 1; 
        document.getElementById(`number_of_answers_${questionNumber}`).value = 1; 
    }
    for (let i = 1; i <= numberOfAnswers; i++) {
        let answerHTML = `
            <div class="answer-block">
		<label for="answer_${questionNumber}_${i}" class="answer-label">Answer ${i}: </label>
                <input type="text" id="answer_${questionNumber}_${i}" name="answer_${questionNumber}_${i}" class="answer-input" value="${post[`answer_${questionNumber}_${i}`] ? post[`answer_${questionNumber}_${i}`] : ''}">
                <div class="error-message">${errors['answer_' + questionNumber + '_' + i] ? errors['answer_' + questionNumber + '_' + i] : ''}</div>
            </div>
        `;
        answersDiv.insertAdjacentHTML('beforeend', answerHTML);
    }
}

document.getElementById('questions').addEventListener('change', function(event) {
    if (event.target.matches('[id*="number_of_answers"]')) {
        let questionNumber = event.target.id.split('_')[3];
        let numberOfAnswers = event.target.value;
        addAnswerFields(questionNumber, numberOfAnswers);
    }
});

        function restorePage(){
                let questionsArray = <?= json_encode($_POST); ?>;
                let questionCount = 0;
                for (let key in questionsArray) {
                        if (key.match(/^question_text_/)) {
                                questionCount++;
                        }
                }
                for (let i = 1; i<questionCount; i++) addQuestion();
                for (let i = 1; i<questionCount+1; i++) addAnswerFields(i, document.querySelector('#number_of_answers_' + i).value);
        }
        restorePage();
    </script>
</body>
</html>
