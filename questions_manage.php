<?php
include('database/config.php');
include('header.php');

// Handle category management
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle question management
    if (isset($_POST['add_question'])) {
        $category_id = $_POST['category_id'];
        $question_text = $_POST['question_text'];
        $difficulty_level = $_POST['difficulty_level'];
        $sql = "INSERT INTO questions (category_id, question_text, difficulty_level) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $category_id, $question_text, $difficulty_level);
        if ($stmt->execute()) {
            $question_id = $stmt->insert_id;
            $options = $_POST['options'];
            $correct_option = $_POST['correct_option'];
            $correct_option_id = null;

            // Insert the options
            foreach ($options as $index => $option_text) {
                $is_correct = ($index == $correct_option) ? 1 : 0;
                $sql_option = "INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)";
                $stmt_option = $conn->prepare($sql_option);
                $stmt_option->bind_param("isi", $question_id, $option_text, $is_correct);
                $stmt_option->execute();

                // Capture the correct option ID
                if ($is_correct) {
                    $correct_option_id = $stmt_option->insert_id;
                }
            }

            // Update the question with the correct answer ID
            if ($correct_option_id !== null) {
                $sql_update = "UPDATE questions SET correct_answer = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ii", $correct_option_id, $question_id);
                $stmt_update->execute();
            }

            $message = "Question added successfully!";
        } else {
            $error = "Failed to add question.";
        }
    }

    if (isset($_POST['update_question'])) {
        $question_id = $_POST['question_id'];
        $category_id = $_POST['category_id'];
        $question_text = $_POST['question_text'];
        $difficulty_level = $_POST['difficulty_level'];
        $sql = "UPDATE questions SET category_id = ?, question_text = ?, difficulty_level = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $category_id, $question_text, $difficulty_level, $question_id);
        if ($stmt->execute()) {
            $sql_delete_options = "DELETE FROM options WHERE question_id = ?";
            $stmt_delete_options = $conn->prepare($sql_delete_options);
            $stmt_delete_options->bind_param("i", $question_id);
            $stmt_delete_options->execute();

            $options = $_POST['options'];
            $correct_option = $_POST['correct_option'];
            $correct_option_id = null;

            // Insert the options
            foreach ($options as $index => $option_text) {
                $is_correct = ($index == $correct_option) ? 1 : 0;
                $sql_option = "INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)";
                $stmt_option = $conn->prepare($sql_option);
                $stmt_option->bind_param("isi", $question_id, $option_text, $is_correct);
                $stmt_option->execute();

                // Capture the correct option ID
                if ($is_correct) {
                    $correct_option_id = $stmt_option->insert_id;
                }
            }

            // Update the question with the correct answer ID
            if ($correct_option_id !== null) {
                $sql_update = "UPDATE questions SET correct_answer = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ii", $correct_option_id, $question_id);
                $stmt_update->execute();
            }

            $message = "Question updated successfully!";
        } else {
            $error = "Failed to update question.";
        }
    }

    if (isset($_POST['delete_question'])) {
        $question_id = $_POST['question_id'];
        $sql_delete_options = "DELETE FROM options WHERE question_id = ?";
        $stmt_delete_options = $conn->prepare($sql_delete_options);
        $stmt_delete_options->bind_param("i", $question_id);
        $stmt_delete_options->execute();

        $sql = "DELETE FROM questions WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $question_id);
        if ($stmt->execute()) {
            $message = "Question deleted successfully!";
        } else {
            $error = "Failed to delete question.";
        }
    }
}

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

function getQuestions($conn)
{
    $sql = "SELECT q.*, c.name as category_name FROM questions q JOIN categories c ON q.category_id = c.id";
    $result = $conn->query($sql);
    $questions = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
    }
    return $questions;
}

$categories = getCategories($conn);
$questions = getQuestions($conn);
?>


<!DOCTYPE html>
<html lang="en">
<style>
    table input {
        border: none;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>

<body>

    <section class="section" id="contact">
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

        <h6 class="section-title mb-5 text-center">Manage Questions</h6>
        <form action="questions_manage.php" method="post" class="contact-form col-md-10 col-lg-8 m-auto">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Question Text</th>
                        <th scope="col">Category</th>
                        <th scope="col">Difficulty Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $question) { ?>
                        <tr>
                            <form action="questions_manage.php" method="post" style="display:inline;">
                                <td><input type="hidden" name="question_id" value="<?= $question['id']; ?>">
                                    <?= $question['id']; ?></td>
                                <td><input type="text" name="question_text" value="<?= $question['question_text']; ?>"></td>
                                <td><?= $question['category_name']; ?></td>
                                <td>
                                    <select name="difficulty_level">
                                        <option value="easy" <?= $question['difficulty_level'] == 'easy' ? 'selected' : ''; ?>>Easy</option>
                                        <option value="medium" <?= $question['difficulty_level'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                                        <option value="hard" <?= $question['difficulty_level'] == 'hard' ? 'selected' : ''; ?>>Hard</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="update_question" class="btn btn-info" onclick="confirmAction(event, 'update')">Update</button>
                                    <button type="submit" name="delete_question" class="btn btn-danger" onclick="confirmAction(event, 'delete')">Delete</button>
                                </td>
                            </form>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            </div>
    </section>
    <script>
        function confirmAction(event, action) {
            const confirmation = confirm(`Are you sure you want to ${action} this category?`);
            if (!confirmation) {
                event.preventDefault();
            }
        }
    </script>

    <?php include('footer.php'); ?>

</body>

</html>