<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <h1 align="center">Login Form</h1>
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            background-color: lightpink;
        }
        .form-container {
            background-color: skyblue;
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            margin: auto;
            margin-top: 50px;
        }

        .form-group.position-relative {
            position: relative;
            margin-bottom: 20px;
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
        }

        .form-check {
            margin-bottom: 20px;
        }

        .form-btn {
            text-align: center;
        }

        .g-recaptcha {
            margin-top: 20px;
        }

        .alert {
            margin-bottom: 20px;
        }

        p {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        $errors = []; // Array to store error messages

        if (isset($_POST["login"])) {
            $email = $_POST["email"];
            $password = $_POST["password"];

            require_once "database.php";
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
            
            if ($user) {
                if (password_verify($password, $user["password"])) {
                    // Only check reCAPTCHA after correct credentials are entered
                    $recaptchaResponse = $_POST['g-recaptcha-response'];
                    
                    if (empty($recaptchaResponse)) {
                        $errors[] = "Please complete the reCAPTCHA verification.";
                    } else {
                        $secretKey = '6LdCb08qAAAAAGquamMpqoBVR5dN50vJ9feWpnx5'; // Secret key from Google reCAPTCHA
                        $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaResponse");
                        $responseData = json_decode($verifyResponse, true);

                        if ($responseData['success']) {
                            $_SESSION["user"] = "yes";
                            header("Location: index.php");
                            die();
                        } else {
                            $errors[] = "reCAPTCHA verification failed. Please try again.";
                        }
                    }
                } else {
                    // Only show "Password does not match" message if passwords are incorrect
                    $errors[] = "Password does not match.";
                }
            } else {
                $errors[] = "Email does not match.";
            }
        }

        // Display any errors
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
        ?>

        <div class="form-container">
            <form action="login.php" method="post">
                <div class="form-group position-relative">
                    <i class="fas fa-envelope"></i>
                    <input type="email" placeholder="Enter Email:" name="email" class="form-control" required>
                </div>
                <div class="form-group position-relative">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Enter Password:" name="password" class="form-control" required id="password">
                </div>

                <!-- Show Password and Remember Me Checkbox -->
                <div class="form-check d-flex justify-content-between">
                    <div>
                        <input type="checkbox" class="form-check-input" id="togglePassword">
                        <label class="form-check-label" for="togglePassword">Show Password</label>
                    </div>
                    <div>
                        <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember Me</label>
                    </div>
                </div>

                <!-- reCAPTCHA widget -->
                <div class="g-recaptcha" data-sitekey="6LdCb08qAAAAANUf9si6vudDOLSpbEwOoSaVH2E2"></div> 

                <div class="form-btn mt-3">
                    <input type="submit" value="Login" name="login" class="btn btn-primary">
                </div>
            </form>

            <div><p>Not registered yet? <a href="registration.php">Register Here</a></p></div>
            <p><a href="forgot_password.php">Forgot Password?</a></p>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById("password");
        const togglePassword = document.getElementById("togglePassword");

        togglePassword.addEventListener("change", function() {
            if (this.checked) {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        });
    </script>
</body>
</html>
