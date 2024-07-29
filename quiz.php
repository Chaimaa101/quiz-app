<?php
include('database/config.php');
include('header.php');

function getCategories($conn) {
    $sql = "SELECT * FROM categories";
    $result = $conn->query($sql);
    $categories = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
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
    <title>Quiz App</title>
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
  
    <section class="section" id="levels-section">
        <div class="container text-center">
            <h6 class="section-title mb-5">Select a Difficulty Level</h6>

            <form action="" class="contact-form col-md-10 col-lg-8 m-auto">
                <?php
                $levels = ['Easy', 'Medium', 'Hard'];
                foreach ($levels as $level) { ?>
                    <button type="button" class="btn btn-outline-primary rounded" onclick="showCategories('<?= $level; ?>')">
                        <?= ucfirst($level); ?>
                    </button>
                <?php } ?>
            </form>
        </div>
    </section>

    <section class="section hidden" id="categories-section">
        <div class="container text-center">
            <h6 class="section-title mb-5">Select a Category</h6>
            <form action="question.php" method="get" class="contact-form col-md-10 col-lg-8 m-auto">
                <input type="hidden" name="level" id="selected-level" value="">
                <?php foreach ($categories as $category) { ?>
                    <button type="submit" name="category_id" value="<?= $category['id']; ?>" class="btn btn-outline-primary rounded">
                        <?= $category['name']; ?>
                    </button>
                <?php } ?>
            </form>
        </div>
    </section>

<script>
    function showCategories(level) {
        document.getElementById('levels-section').classList.add('hidden');
        document.getElementById('categories-section').classList.remove('hidden');
        document.getElementById('selected-level').value = level;
    }
</script>

<?php include('footer.php');?>

</body>
</html>