<?php

include('database/config.php');
include('header.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_category'])) {
        $category_name = $_POST['category_name'];
        $sql = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category_name);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Category added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add category.";
        }
    }

    if (isset($_POST['update_category'])) {
        $category_id = $_POST['category_id'];
        $category_name = $_POST['category_name'];
        $sql = "UPDATE categories SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $category_name, $category_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Category updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update category.";
        }
    }

    if (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id'];
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Category deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete category.";
        }
    }

    header("Location: category_manage.php");
    exit();
}

function getcategories($conn)
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

$categories = getcategories($conn);
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

    <section class="section">
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['message'] . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['error'] . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
            unset($_SESSION['error']);
        }
        ?>

        <div class="container text-center">
            <form action="category_manage.php" method="post" class="contact-form col-md-10 col-lg-8 m-auto">
                <div class="form-group">
                    <input class="form-control" type="text" name="category_name" placeholder="Category Name" required>
                </div>
                <button class="btn btn-primary" name="add_category" type="submit">Add Category</button>
            </form>
            <h6 class="section-secondary-title mt-5">Categories</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Category Id</th>
                        <th scope="col">Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category) { ?>
                        <tr>
                            <form action="category_manage.php" method="post" style="display:inline;">
                                <td>
                                    <input type="hidden" name="category_id" value="<?= $category['id']; ?>">
                                    <?= $category['id']; ?>
                                </td>
                                <td><input type="text" name="category_name" value="<?= $category['name']; ?>" required></td>
                                <td>
                                    <button type="submit" name="update_category" class="btn btn-info" onclick="return confirm('Are you sure you want to update this category?')">Update</button>
                                    <button type="submit" name="delete_category" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                                </td>
                            </form>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
        </div>
    </section>

   <?php include('footer.php');?>
</body>

</html>