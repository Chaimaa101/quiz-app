<?php
include('database/config.php');
include('header.php');

$score = null;
$totalQuestions = 0;
$answers = [];

// Handle form submission and calculate results
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers'];
    $score = 0;

    foreach ($answers as $question_id => $answer) {
        $sql = "SELECT correct_answer FROM questions WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $stmt->bind_result($correct_answer_id);
        $stmt->fetch();
        $stmt->close();

        if ($answer == $correct_answer_id) {
            $score++;
        }
    }
} elseif (isset($_GET['category_id']) && isset($_GET['level'])) {
    $categoryId = $_GET['category_id'];
    $difficultyLevel = $_GET['level'];
}

function getQuestions($conn, $categoryId, $difficultyLevel)
{
    $sql = "SELECT * FROM questions WHERE category_id = ? AND difficulty_level = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $categoryId, $difficultyLevel);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
    }
    return $questions;
}

function getOptions($conn, $questionId)
{
    $sql = "SELECT * FROM options WHERE question_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $questionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $options = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $options[] = $row;
        }
    }
    return $options;
}

if (isset($categoryId) && isset($difficultyLevel)) {
    $questions = getQuestions($conn, $categoryId, $difficultyLevel);
    $totalQuestions = count($questions);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <style>
        @import url("https://fonts.googleapis.com/css?family=Baloo+Paaji|Open+Sans:300,300i,400,400i,600,600i,700,700i");

        .wrapper {
            max-width: 600px;
            margin: 80px auto 50px;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            position: relative;
            min-height: 400px;
            overflow: hidden;
        }

        .wrapper .wrap {
            width: 100%;
            position: relative;
        }

        .question-slide {
            display: none;
        }

        .question-slide:first-child {
            display: block;
        }

        .h4 {
            margin: 0;
            color: #212529;
        }

        label {
            display: block;
            margin-bottom: 15px;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .options {
            position: relative;
            padding-left: 30px;
        }

        .options input {
            opacity: 0;
        }

        .checkmark {
            position: absolute;
            top: 4px;
            left: 3px;
            height: 20px;
            width: 20px;
            border: 2px solid #444;
            border-radius: 50%;
        }

        .options input:checked~.checkmark:after {
            display: block;
        }

        .options .checkmark:after {
            content: "";
            width: 9px;
            height: 9px;
            display: block;
            background: white;
            position: absolute;
            top: 51%;
            left: 51%;
            border-radius: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: 300ms ease-in-out 0s;
        }

        .options input[type="radio"]:checked~.checkmark {
            background: #590995;
            border: 2px solid #590995;
            transition: 300ms ease-in-out 0s;
        }

        .options input[type="radio"]:checked~.checkmark:after {
            transform: translate(-50%, -50%) scale(1);
        }

        .fa-arrow-right,
        .fa-arrow-left {
            transition: 0.2s ease-in all;
        }

        .btn.btn-primary:hover .fa-arrow-right {
            transform: translate(8px);
        }

        .btn.btn-primary:hover .fa-arrow-left {
            transform: translate(-8px);
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 28px;
            background-color: inherit;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: #590995;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #000;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            transform: translateX(30px);
            background-color: #fff;
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .dark-theme {
            background-color: #222;
        }

        .timer {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            position: absolute;
            top: 20px;
            right: 20px;
            color: #6f42c1;
        }

        .question-number {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #6f42c1;
        }
    </style>
</head>

<body>
    <?php if ($score !== null) { ?>
        <div class="results-screen">
            <h2>Your Results</h2>
            <p>Your score: <?= $score; ?></p>
            <p>Total questions: <?= count($answers); ?></p>
        </div>
    <?php } elseif (!empty($questions)) { ?>
        <form id="quizForm" action="results.php" method="post">
            <?php foreach ($questions as $index => $question) { ?>
                <div class="wrapper question-slide" id="q<?= $index + 1 ?>">
                    <div class="wrap">
                        <div class="timer" id="timer-<?= $index + 1 ?>"><span class="fa-solid fa-stopwatch-20"></span> 30</div>
                        <div class="text-center pb-4">
                            <div class="question-number"><span id="number"><?= $index + 1 ?></span> of <?= $totalQuestions ?></div>
                        </div>
                        <div style="height: 50px;"></div>
                        <div class="h4 font-weight-bold text-center"><?= $index + 1 . ". " . $question['question_text'] ?></div>
                        <div class="pt-4">
                            <?php
                            $options = getOptions($conn, $question['id']);
                            foreach ($options as $option) { ?>
                                <label class="options"><?= $option['option_text'] ?>
                                    <input type="radio" name="answers[<?= $question['id'] ?>]" value="<?= $option['id'] ?>" required>
                                    <span class="checkmark"></span>
                                </label>
                            <?php } ?>
                        </div>
                        <div class="d-flex justify-content-between pt-2">
                            <?php if ($index > 0) { ?>
                                <button type="button" class="btn btn-info prev" data-prev="<?= $index ?>"><span class="fas fa-arrow-left"></span> Previous </button>
                            <?php } ?>
                            <?php if ($index < $totalQuestions - 1) { ?>
                                <button type="button" class="btn btn-primary next" data-next="<?= $index + 2 ?>">Next <span class="fas fa-arrow-right"></span></button>
                            <?php } else { ?>
                                <button type="submit" name="submit" class="btn btn-success">Submit</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </form>
    <?php } else { ?>
        <p>No questions found for the selected category and difficulty level.</p>
    <?php } ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nextButtons = document.querySelectorAll('.next');
            const prevButtons = document.querySelectorAll('.prev');
            const questionSlides = document.querySelectorAll('.question-slide');
            let timerInterval;

            function startTimer(index) {
                let timer = document.getElementById(`timer-${index}`);
                let timeLeft = 30;

                timerInterval = setInterval(function() {
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        showNextQuestion(index + 1);
                    } else {
                        timer.textContent = timeLeft;
                    }
                    timeLeft -= 1;
                }, 1000);
            }

            function showNextQuestion(nextId) {
                questionSlides.forEach(slide => slide.style.display = 'none');
                if (nextId <= questionSlides.length) {
                    document.getElementById('q' + nextId).style.display = 'block';
                    startTimer(nextId);
                }
            }

            nextButtons.forEach(button => {
                button.addEventListener('click', function() {
                    clearInterval(timerInterval);
                    const nextId = this.dataset.next;
                    showNextQuestion(nextId);
                });
            });

            prevButtons.forEach(button => {
                button.addEventListener('click', function() {
                    clearInterval(timerInterval);
                    const prevId = this.dataset.prev;
                    questionSlides.forEach(slide => slide.style.display = 'none');
                    document.getElementById('q' + prevId).style.display = 'block';
                    startTimer(prevId);
                });
            });

            startTimer(1);
        });
    </script>
    
</body>

</html>