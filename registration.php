<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
}

// Initialize the errors array
$errors = array();

if (isset($_POST["submit"])) {
    $fullName = $_POST["fullname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["confirm_password"];
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Get reCAPTCHA response
    if (empty($recaptchaResponse)) {
        $errors[] = "Please complete the reCAPTCHA verification.";
    } else {
        // Verify the reCAPTCHA
        $secretKey = '6LdCb08qAAAAAGquamMpqoBVR5dN50vJ9feWpnx5'; // secret key from Google reCAPTCHA
        $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaResponse");
        $responseData = json_decode($verifyResponse);

        if (!$responseData->success) {
            $errors[] = "reCAPTCHA verification failed. Please try again.";
        }
    }

    // Proceed only if no errors related to reCAPTCHA
    if (empty($errors)) {
        // Password hash
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Validate other form inputs
        if (empty($fullName) || empty($email) || empty($password) || empty($passwordRepeat)) {
            $errors[] = "All fields are required";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email is not valid";
        }
        if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[@]/", $password)) {
            $errors[] = "Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, one number, and the special character '@'.";
        }
        if ($password !== $passwordRepeat) {
            $errors[] = "Passwords do not match";
        }

        // Check for existing email
        require_once "database.php"; 
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $rowCount = mysqli_num_rows($result);
        if ($rowCount > 0) {
            $errors[] = "Email already exists!";
        }

        // If no other errors, proceed with user registration
        if (count($errors) === 0) {
            $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $passwordHash);
                mysqli_stmt_execute($stmt);

                // Display success message
                echo "<div class='alert alert-success'>You have successfully registered! Thank you.</div>";
            } else {
                die("Something went wrong");
            }
        }
    }

    // Display all accumulated errors
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <h1 align="center">Registration Form</h1>
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script> <!-- reCAPTCHA Script -->
    <style>
        body {
            background-color: lightpink; /* Background color outside the form */
        }
        .container {
            background-color: skyblue; /* Background inside the form */
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
        }
        .form-group.position-relative {
            position: relative;
            margin-bottom: 20px; /* Increase space between form groups */
        }
        .form-group .form-control {
            padding-left: 40px;
        }
        .form-group i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #888;
            z-index: 1;
        }
        #password-requirements {
            font-size: 0.9em;
            color: red;
            display: none;
        }
        #password-requirements.active {
            display: block;
        }
        #password-strength {
            font-size: 0.9em;
            margin-top: 5px;
            display: none;
            font-weight: bold; /* Make the text bold */
        }
        #password-match {
            color: green;
            font-size: 0.9em;
            display: none;
        }
        #password-mismatch {
            color: red;
            font-size: 0.9em;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="registration.php" method="post">
            <!-- Full Name -->
            <div class="form-group position-relative mb-3">
                <i class="fas fa-user"></i>
                <input type="text" class="form-control" name="fullname" placeholder="Full Name:" required>
            </div>

            <!-- Email -->
            <div class="form-group position-relative mb-3">
                <i class="fas fa-envelope"></i>
                <input type="email" class="form-control" name="email" placeholder="Email:" required>
            </div>

            <!-- Password -->
            <div class="form-group position-relative mb-3">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password:" required>
                <div id="password-requirements">
                    Password must be at least 8 characters, contain one uppercase letter, one lowercase letter, one number, and the special character '@'.
                </div>
                <span id="password-strength" class="strength-indicator"></span>
            </div>

            <!-- Confirm Password -->
            <div class="form-group position-relative mb-3">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password:" required>
                <span id="password-match">Passwords match!</span>
                <span id="password-mismatch">Passwords do not match!</span>
            </div>

            <!-- Show Password Checkbox -->
            <div class="form-check mt-2 mb-3">
                <input type="checkbox" class="form-check-input" id="showPassword">
                <label class="form-check-label" for="showPassword">Show Password</label>
            </div>

            <!-- reCAPTCHA widget -->
            <div class="g-recaptcha mb-3" data-sitekey="6LdCb08qAAAAANUf9si6vudDOLSpbEwOoSaVH2E2"></div>

            <!-- Register Button -->
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div>
            <p>Already Registered? <a href="login.php">Login Here</a></p>
        </div>
    </div>

    <script>
        const password = document.getElementById("password");
        const confirmPassword = document.getElementById("confirm_password");
        const passwordMatch = document.getElementById("password-match");
        const passwordMismatch = document.getElementById("password-mismatch");
        const passwordStrength = document.getElementById("password-strength");
        const passwordRequirements = document.getElementById("password-requirements");
        const showPasswordCheckbox = document.getElementById("showPassword");

        // Show password requirements when user clicks on the password field
        password.addEventListener("focus", function () {
            passwordRequirements.classList.add("active");
        });

        // Hide password requirements when user leaves the password field
        password.addEventListener("blur", function () {
            passwordRequirements.classList.remove("active");
        });

        // Show/Hide password strength while typing
        password.addEventListener("input", function () {
            const value = password.value;
            let strength = "Weak";
            let color = "red";

            if (value.length >= 8 && /[A-Z]/.test(value) && /[a-z]/.test(value) && /[0-9]/.test(value) && /[@]/.test(value)) {
                strength = "Strong";
                color = "green";
            } else if (value.length >= 6) {
                strength = "Medium";
                color = "orange";
            }

            if (value.length > 0) {
                passwordStrength.textContent = `Password Strength: ${strength}`;
                passwordStrength.style.color = color;
                passwordStrength.style.display = "block"; // Show password strength when typing
            } else {
                passwordStrength.style.display = "none"; // Hide password strength when field is empty
            }
        });

        // Password match check
        confirmPassword.addEventListener("input", function () {
            if (password.value === confirmPassword.value && password.value !== "") {
                passwordMatch.style.display = "block";
                passwordMismatch.style.display = "none";
            } else if (password.value !== confirmPassword.value && confirmPassword.value !== "") {
                passwordMatch.style.display = "none";
                passwordMismatch.style.display = "block";
            } else {
                passwordMatch.style.display = "none";
                passwordMismatch.style.display = "none";
            }
        });

        // Show/Hide Password checkbox functionality
        showPasswordCheckbox.addEventListener("change", function () {
            const passwordFieldType = showPasswordCheckbox.checked ? "text" : "password";
            password.type = passwordFieldType;
            confirmPassword.type = passwordFieldType;
        });
    </script>
</body>
</html>
