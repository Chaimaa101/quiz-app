<?php
include('database/config.php');
include('header.php');

if (isset($_POST['submit'])) {

$answers = $_POST['answers'];
$score = 0;


    foreach ($answers as $question_id => $answer) {
        $sql = "SELECT correct_answer FROM questions WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $stmt->bind_result($correct_answer_id);
        $stmt->fetch();

        if ($answer == $correct_answer_id) {
            $score++;
        }
}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <style>
        .results-screen {
            max-width: 600px;
            margin: 80px auto 50px;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .results-screen h2 {
            margin-bottom: 20px;
        }

        .results-screen p {
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <div class="results-screen">
        <h2>Your Results</h2>
        <p>Your score: <?= $score; ?></p>
        <p>Total questions: <?= count($answers); ?></p>
    </div>
    <?php include('footer.php'); ?>
</body>

</html>
