<?php
include('database/config.php');
 include('header.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Start your development with Meyawo landing page.">
    <meta name="author" content="Devcrud">
    <title>Quiz App</title>
</head>
    <!-- page header -->
    <header id="home" class="header">
        <div class="overlay"></div>
        <div class="header-content container">
            <h1 class="header-title">
                <span class="up">HI!</span>
                <span class="down">Are you ready </span>
            </h1>
            <p class="header-subtitle"> to test your knowledge and challenge yourself with our exciting quizzes?</p>

            <a href="quiz.php"><button class="btn btn-primary">Start the quiz</button></a>
        </div>
    </header>
<?php include('footer.php');?>
</body>
</html>