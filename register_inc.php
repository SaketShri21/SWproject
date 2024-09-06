<?php

if (isset($_POST['submit'])) {
    // Connect to the database
    require "dashboard/includes/conn.php";

    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Error handling
    if (empty($username) || empty($password) || empty($confirm_password)) {
        header("Location:index.php?error=emptyfields&username=" . $username);
        exit();
    }
    // Check for invalid username characters
    elseif (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location:register.php?error=invalidfields&username=" . $username);
        exit();
    }
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        header("Location:register.php?error=passworddonotmatch&username=" . $username);
        exit();
    }
    else {
        // Check if the username already exists
        $sql = "SELECT username FROM users WHERE username = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location:index.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $rowCount = mysqli_stmt_num_rows($stmt);

            if ($rowCount > 0) {
                header("Location:index.php?error=usernametaken&username=" . $username);
                exit();
            } else {
                // Insert new user into the database with hashed password
                $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
                $stmt = mysqli_stmt_init($conn);

                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location:index.php?error=sqlerror");
                    exit();
                } else {
                    // Hash the password
                    $hashedPass = password_hash($password, PASSWORD_DEFAULT);

                    // Bind and execute the statement
                    mysqli_stmt_bind_param($stmt, "ss", $username, $hashedPass);
                    mysqli_stmt_execute($stmt);

                    header("Location:index.php?success=registered");
                    exit();
                }
            }
        }
    }
}
?>
