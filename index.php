<?php
session_start();
if (!isset($_SESSION["user"])) {
   header("Location: login.php");
   exit();
}

// Fetch the user's full name from the session (assuming it's stored in the session during login)
$fullName = isset($_SESSION["full_name"]) ? $_SESSION["full_name"] : 'User'; // Default to 'User' if no name is found
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>User Dashboard</title>
    <style>
        /* Navbar styling */
        .navbar {
            background-color: #ff4d4d; /* Red background color */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        }

        .navbar .btn-warning {
            font-size: 1.2rem;
            border-radius: 20px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .navbar .btn-warning:hover {
            background-color: #ffcc33; /* Slightly lighter yellow on hover */
            transform: scale(1.05); /* Button grows slightly on hover */
        }

        /* Container styling */
        body {
            background-color: #f0f8ff; /* Light blue background */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding-top: 60px; /* Adjust padding to leave space for navbar */
        }

        .container {
            background-color: #ffffff; /* White background for the container */
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
            text-align: center;
            max-width: 500px;
            animation: fadeIn 1.2s ease-in-out;
        }

        /* Welcome text styling */
        .welcome-message {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff; /* Bootstrap primary color */
            margin-bottom: 20px;
            animation: fadeIn 1.5s ease-in-out;
        }

        .user-greeting {
            font-size: 1.5rem;
            color: #ff4d4d; /* Red color to match the navbar */
            animation: slideIn 1.8s ease-in-out;
        }

        /* Keyframe animations */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            0% {
                opacity: 0;
                transform: translateX(-100%);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Background gradient animation */
        body {
            background: linear-gradient(-45deg, #f0f8ff, #cce7ff, #99d6ff, #66c2ff);
            background-size: 400% 400%;
            animation: gradientBackground 15s ease infinite;
        }

        @keyframes gradientBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container-fluid">
            <div class="d-flex">
                <a href="logout.php" class="btn btn-warning">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Welcome Message -->
    <div class="container mt-5">
        <h1 class="welcome-message">Hello, <span class="user-greeting"><?php echo htmlspecialchars($fullName); ?></span> !</h1>
        <p class="welcome-message">Welcome to Dashboard!</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
