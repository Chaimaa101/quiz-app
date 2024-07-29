<?php
session_start();
include('database/config.php');
include('header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE name = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: questions_manage.php");
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<section class="section" id="contact">
    <div class="container text-center">
        <p class="section-subtitle">How can you communicate?</p>
        <h6 class="section-title mb-5">Admin Login</h6>
        <form action="login.php" method="post" class="contact-form col-md-10 col-lg-8 m-auto">
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo "<p>$error</p>"; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } ?>
            <div class="form-group">
                <input type="text" name="username" class="form-control" id="exampleFormControlInput1" placeholder="Username">
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" id="exampleFormControlInput1" placeholder="Password">
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
    </div>
</section>
<?php include('footer.php'); ?>
</body>
</html>