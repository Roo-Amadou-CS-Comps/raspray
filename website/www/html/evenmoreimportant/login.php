<?php
session_start();

$filename = '/var/www/html/evenmoreimportant/data/hashes.txt';

function validate_user($username, $password) {
    // Predefined usernames and passwords
    $credentials = [
        'admin' => 'password',
        'Jeff' => 'Dancer',
        'Olga' => 'DietDrPepper',
        'Minnesotan' => 'Carb0n7',
        'BringItOn' => 'SecurityUsIO!1',
        'MorrisDancer37' => 'SdA1&XPUU'
    ];

    // Check if the username exists and if the password matches
    return isset($credentials[$username]) && $credentials[$username] === $password;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve inputted username and password from form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate username and password
    if (validate_user($username, $password, $filename)) {
        // Set session to mark the user as logged in
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;

        // Redirect to the secret page after successful login
        header('Location: data/secret.php');
        exit();
    } else {
        $error_message = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>
    <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
