<?php
require('dashboard/includes/conn.php');
$error = '';
session_start();

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch the user with the provided username
    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $error = "<div class='alert alert-danger'>SQL Error</div>";
    } else {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Verify the password using password_verify
            if (password_verify($password, $row['password'])) {
                // Password matches, proceed with session setup
                $_SESSION['ROLE'] = $row['username'];
                $_SESSION['IS_LOGIN'] = 'yes';

                // Set session variables based on the role
                $_SESSION['sessionId'] = $row['id'];
                $_SESSION['sessionUsername'] = $row['username'];

                header('location:dashboard/index.php');
                die();
            } else {
                $error = "<div class='alert alert-danger'>Invalid password</div>";
            }
        } else {
            $error = "<div class='alert alert-danger'>User not found</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHARMACY</title>
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/style.css">
</head>
<body>
    <div class="wrapper">
        <section class="form sign up">
            <center><header>Login</header></center>
            <form class="header" method="post">
                <div class="field input">
                    <label for="">Username</label>
                    <input type="text" name="username" placeholder="Provide your Username">
                </div>
                <div class="field input">
                    <label for="">Password</label>
                    <input type="password" name="password" placeholder="Password">
                </div>
                <div class="field button"> 
                    <input type="submit" name="submit" value="LOGIN">
                </div>
                <?php echo $error; ?>
            </form>
            <p>Register here <a href="register.php">Here</a></p>
        </section>
    </div>
</body>
</html>
