<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['customer_name'] = $_POST['customer_name'];
    $_SESSION['email_address'] = $_POST['email'];
}


// Set a delay (in seconds) before the redirect to generate.php
sleep(3); // 3-second delay for demonstration

// Perform the redirection to generate.php
header('Location: generate.php');



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading...</title>
    <style>
        /* Full-screen overlay */
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Spinner */
        .spinner {
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }

        /* Spinner animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <!-- Full-screen loader -->
    <div class="spinner"></div>

</body>
</html>





