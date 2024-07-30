<?php

include('database/config.php');
include('header.php');

function getCategories($conn)
{
    $sql = "SELECT * FROM categories";
    $result = $conn->query($sql);
    $categories = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    return $categories;
}
$categories = getCategories($conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
</head>

<section class="section">
    <div class="container text-center">
        <h6 class="section-title mb-5">Add Question</h6>
        <form action="questions_manage.php" method="post" class="contact-form col-md-10 col-lg-8 m-auto">
            <?php if (isset($message)) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php }
            if (isset($error)) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } ?>
            <div class="form-group">
                <input class="form-control" type="text" name="question_text" placeholder="Question Text" required>
            </div>
            <div class="form-group">
                <select class="form-control" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?= $category['id']; ?>"><?= $category['name']; ?></option>
                    <?php } ?>

                </select>
            </div>

            <div class="form-group">
                <select class="form-control" name="difficulty_level" required>
                    <option value="">Select Difficulty Level</option>
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
            <div id="options">
                <div class="form-group">
                    <input class="form-control" type="text" name="options[]" placeholder="Option 1" required>
                </div>
                <div class="form-group">
                    <input class="form-control" type="text" name="options[]" placeholder="Option 2" required>

                </div>
                <div class="form-group">
                    <input class="form-control" type="text" name="options[]" placeholder="Option 3" required>
                </div>
                <div class="form-group">
                    <input class="form-control" type="text" name="options[]" placeholder="Option 4" required>
                </div>
            </div>
            <div class="form-group">
                <label for="correct_option">Correct Option:</label>
                <select class="form-control" name="correct_option" required>
                    <option value="0">Option 1</option>
                    <option value="1">Option 2</option>
                    <option value="2">Option 3</option>
                    <option value="3">Option 4</option>
                </select>
            </div>
            <button class="btn btn-primary" name="add_question" type="submit">Add Question</button>
        </form>
    </div>
    </div>
</section>
<?php include('footer.php'); ?>
</body>

</html>