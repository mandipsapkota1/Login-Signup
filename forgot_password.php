<?php
session_start();
require_once "database.php";

$errors = [];
$success = "";

// Check if the form is submitted
if (isset($_POST["submit"])) {
    $email = $_POST["email"];

    // Check if the email exists in the database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if ($user) {
        // Email exists, redirect to new_password.php
        $_SESSION["email"] = $email;  // Store the email in the session for use in new_password.php
        header("Location: new_password.php");
        exit();
    } else {
        $errors[] = "Email address not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: lightpink;
        }
        .form-container {
            background-color: skyblue;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center">Forgot Password</h2>

            <!-- Display any success message -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <!-- Display any errors -->
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <form action="forgot_password.php" method="post">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>

                <div class="form-btn mt-3">
                    <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                </div>
            </form>

            <div class="mt-3"><a href="login.php">Back to Login</a></div>
        </div>
    </div>
</body>
</html>
