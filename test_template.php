<!DOCTYPE html>
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
    height: 45px;
    margin: auto;
    padding-bottom: 10px;
}

.site-header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 80%;
    margin: auto;
}

.create-test, .admin-login, .admin-logout {
    margin-top: 10px;
}

.btn {
    padding: 10px 15px;
    color: white;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.test-result {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    padding: 20px;
    margin-bottom: 20px;
}

.mark {
    font-weight: bold;
}

.return-btn {
    background-color: #28a745;
}

.return-btn:hover {
    background-color: #218838;
}

.btn:not(:disabled) {
    opacity: 1;
    cursor: pointer;
}

.btn:disabled {
    opacity: 0.5;
    cursor: default;
}

.question-block {
    display: none;
    background-color: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    margin-bottom: 20px;
}

.question-block.active {
    display: block;
}

.question-label {
    display: block;
    margin-bottom: 15px;
    font-size: 1.2em;
    font-weight: bold;
}

.answer-label {
    display: inline-block;
    margin-top: 10px;
}

.skip-btn {
    background-color: #ddd;
    color: #888;
    border: 1px solid #ccc;
}

.skip-btn:hover {
    background-color: #999;
    color: #fff;
}

.next-btn {
    background-color: #17a2b8;
    display: none;
}

.next-btn:not(:disabled):hover {
    background-color: #138496;
}

.submit-btn {
    background-color: #007bff;
    display: none;
}

.submit-btn:not(:disabled):hover {
    background-color: #0056b3;
}
.control {
    width: 100%;
    text-align: center;
    justify-content: center;
    align-items: center;
    padding-top: 10px;
}
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container">
        </div>
    </header>
    <main class="content">
        <div class="container">
            <?php if (isset($_SESSION['mark'])): ?>
    <div class="test-result">
        <p class="mark">Your mark: <?= htmlspecialchars($_SESSION['mark']) ?></p>
        <?php if (isset($_SESSION['your_answers']) && isset($_SESSION['correct_answers'])): ?>
            <?php foreach($_SESSION['your_answers'] as $question => $answer): ?>
                <div class="question-block" style="display: block;">
                    <div class="question"><?= $question ?></div><br>
                    <div class="answers">Your answer: <?= is_array($answer) ? implode(', ', $answer) : $answer ?></div><br>
                    <div class="answers">Correct answer: <?= is_array($_SESSION['correct_answers'][$question]) ? implode(', ', $_SESSION['correct_answers'][$question]) : $_SESSION['correct_answers'][$question] ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <form action="index.php" method="get" class="return-form">
            <button type="submit" class="btn return-btn">Go to main page</button>
        </form>
    </div>
    <?php
        unset($_SESSION['mark']);
        unset($_SESSION['test']);
    ?>
    <?php exit; ?>
<?php endif; ?>
            <form id="questionForm" method="post" class="test-form">
                <input type="hidden" name="title" id="title" value="<?= htmlspecialchars($title) ?>">
                <?php array_shift($questions); ?>
                <?php foreach ($questions as $i => $question): ?>
                    <div class="question-block">
                        <div class="question" id="question_<?= $i ?>">
                            <label class="question-label"><?= htmlspecialchars($question['question_text']) ?></label><hr>
                            <?php $type = $question['question_type']; ?>
                            <div class="answers">
                                <?php foreach ($question['answers'] as $j => $answer): ?>
                                    <div class="answer">
                                        <?php if ($type !== 'text'): ?>
                                            <input type="<?= htmlspecialchars($type) ?>" id="answer_<?= $i ?>_<?= $j ?>" name="question_<?= $i ?>[]" value="<?= htmlspecialchars($answer) ?>" class="answer-input" onchange="enableNextButton(<?= $i ?>)">
                                            <label for="answer_<?= $i ?>_<?= $j ?>" class="answer-label"><?= htmlspecialchars($answer) ?></label>
                                        <?php else: ?>
                                            <input type="text" id="answer_<?= $i ?>_<?= $j ?>" name="question_<?= $i ?>" class="answer-input" oninput="enableNextButton(<?= $i ?>)">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
			    <div class="control">
                                <button type="button" id="next_<?= $i ?>" class="btn next-btn" onclick="showNextQuestion(<?= $i ?>)" disabled>Next question</button>
				<button type="submit" class="btn submit-btn" disabled>Submit</button>
                            <?php if (!isset($_SESSION['success']) || $_SESSION['success'] !== 'Enter correct answers.'): ?>
                                <button type="button" id="skip_<?= $i ?>" class="btn skip-btn" onclick="skipQuestion(<?= $i ?>)">Skip question</button>
                            <?php endif; ?>
			    </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </form>
        </div>
    </main>
    <script>
        let currentQuestion = 0;
document.addEventListener('DOMContentLoaded', (event) => {
    showQuestion(currentQuestion);
});

function enableNextButton(index) {
    let questions = document.querySelectorAll('.question-block');
    console.log(questions.length, index);
    let nextButton;
    if (index < questions.length - 1) {
    nextButton = document.getElementById('next_' + index);}
    else { nextButton = questions[index].querySelector('.submit-btn');}
    if (nextButton) {
        nextButton.disabled = false;
    }
}

function showQuestion(index) {
    let questions = document.querySelectorAll('.question-block');
    if (index > 0){
        let answersDiv = questions[index-1].querySelector('.answers');
        let inputs = answersDiv.querySelectorAll('.answer input');
        let allEmpty = Array.from(inputs).every(input => {
            return (input.type === 'text' && input.value.trim() === '') ||
                   ((input.type === 'radio' || input.type === 'checkbox') && !input.checked);
        });

        if (allEmpty) {
            let falseInput = document.createElement('input');
            falseInput.type = 'hidden';
            falseInput.name = 'question_'+(index-1);
            falseInput.value = '_';
            answersDiv.append(falseInput);
        }
    }

    questions.forEach((question, i) => {
        question.style.display = i === index ? 'block' : 'none';
    });


    if (index === questions.length - 1) {
        let submitButton = questions[index].querySelector('.submit-btn');
        console.log(submitButton);
        if (submitButton) {
            submitButton.style.display = 'inline-block';
        }
    } else {
        let nextButton = document.getElementById('next_' + index);
        if (nextButton) {
            nextButton.style.display = 'inline-block';
        }
    }
}

function showNextQuestion(index) {
    showQuestion(index + 1);
    currentQuestion = index + 1;
}

function skipQuestion(index) {
    let questions = document.querySelectorAll('.question-block');
    let answers = questions[index].querySelectorAll('.answer input');
    let inputType = answers.length > 0 ? answers[0].type : null;
    let form = document.getElementById('questionForm');
    answers.forEach(answer => {
        if (answer.type === 'radio' || answer.type === 'checkbox') {
            answer.checked = false;
        }
    });
    if (inputType === 'radio' || inputType === 'checkbox') {
        if (!document.getElementById('default_answer_' + index)) {
            let defaultInput = document.createElement('input');
            defaultInput.type = 'hidden';
            defaultInput.id = 'default_answer_' + index;
            defaultInput.name = 'question_' + index;
            defaultInput.value = '(Skipped question)';
            form.appendChild(defaultInput);
        }
    } else {
        answers.forEach(answer => {
            answer.value = 'Imtoostupid_default_skip_question';
        });
    }
    console.log(form);
    if (index === questions.length - 1) {
        document.getElementById('questionForm').submit();
    } else {
        showNextQuestion(index);
    }
}

document.querySelector('#questionForm').addEventListener('submit', function(event){
    let questions = document.querySelectorAll('.question-block');
    let answersDiv = questions[questions.length-1].querySelector('.answers');
    let inputs = answersDiv.querySelectorAll('.answer input');
    let allEmpty = Array.from(inputs).every(input => {
        return (input.type === 'text' && input.value.trim() === '') ||
               ((input.type === 'radio' || input.type === 'checkbox') && !input.checked);
    });

    if (allEmpty) {
        let falseInput = document.createElement('input');
        falseInput.type = 'hidden';
        falseInput.name = 'question_'+(questions.length-1);
        falseInput.value = '_';
        answersDiv.append(falseInput);
    }
});

    </script>
</body>
</html>
