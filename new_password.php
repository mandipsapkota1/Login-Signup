<?php
session_start();
require_once "database.php";

$errors = [];
$success = "";

// Ensure the user came from the forgot_password.php page
if (!isset($_SESSION["email"])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION["email"];

// Check if the form is submitted
if (isset($_POST["submit"])) {
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    // Server-side password validation
    $passwordPattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

    if (!preg_match($passwordPattern, $newPassword)) {
        $errors[] = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character (@, $, !, %, *, ?, &).";
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $sql = "UPDATE users SET password = '$hashedPassword' WHERE email = '$email'";
        if (mysqli_query($conn, $sql)) {
            $success = "Your password has been successfully updated.";
            unset($_SESSION["email"]);  // Clear the session once the password is reset
        } else {
            $errors[] = "Failed to update password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
            <h2 class="text-center">Reset Password</h2>

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

            <form action="new_password.php" method="post" id="passwordForm">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Enter new password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm new password" required>
                </div>
                <div class="form-btn mt-3">
                    <input type="submit" name="submit" value="Reset Password" class="btn btn-primary">
                </div>
            </form>

            <div class="mt-3"><a href="login.php">Back to Login</a></div>
        </div>
    </div>

    <script>
        document.getElementById('passwordForm').addEventListener('submit', function (e) {
            var newPassword = document.getElementById('new_password').value;
            var passwordPattern = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!passwordPattern.test(newPassword)) {
                alert("Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character (@, $, !, %, *, ?, &).");
                e.preventDefault();  // Prevent form submission if password does not meet criteria
            }
        });
    </script>
</body>
</html>
