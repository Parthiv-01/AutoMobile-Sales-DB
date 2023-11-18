<?php
session_start();
include('config.php');

// Check if the user is already logged in
if (isset($_SESSION['dealer_id'])) {
    header("Location: index.php");
    exit();
}

// Check if the login form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate the login credentials (Add your authentication logic here)
    $loginQuery = "SELECT id FROM dealers WHERE username = '$username' AND password = '$password'";
    $loginResult = $conn->query($loginQuery);

    if ($loginResult->num_rows > 0) {
        // Login successful
        $row = $loginResult->fetch_assoc();
        $_SESSION['dealer_id'] = $row['id'];

        // Redirect to index.php
        header("Location: index.php");
        exit();
    } else {
        // Login failed, display error message
        $_SESSION['login_error'] = "Invalid username or password.";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error-message {
            color: #ff0000;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <h2>Login</h2>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <input type="submit" value="Login">
        </form>

        <?php
        // Display error message if login fails
        if (isset($_SESSION['login_error'])) {
            echo "<p class='error-message'>{$_SESSION['login_error']}</p>";
            unset($_SESSION['login_error']);
        }
        ?>
    </div>

</body>

</html>
