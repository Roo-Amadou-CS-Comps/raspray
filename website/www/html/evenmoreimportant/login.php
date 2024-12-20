<?php
session_start();

require_once '/var/www/html/evenmoreimportant/vendor/autoload.php';
use WhiteHat101\Crypt\APR1_MD5;

$filename = '/var/www/html/evenmoreimportant/data/hashes.txt';

function validate_user($username, $password, $filename) {
    $fp = fopen($filename, 'r');
    if (!$fp) {
        die('Password file not found.');
    }

    // Read each line and check for the username and hashed password
    while (($line = fgets($fp)) !== false) {
        // Each line is in the format: username:hashedpassword
        list($storedUser, $hashedPassword) = explode(':', trim($line), 2);

        // Check if the username matches
        if ($storedUser === $username) {
            // Debugging output for password check
            echo "Username matched. Checking password...<br>";

            // Verify the entered password using APR1_MD5::check() and the stored hash
            if (APR1_MD5::check($password, $hashedPassword)) {
                fclose($fp);
                echo "Password matched.<br>"; // Debugging output for success
                return true; // Valid username and password
            } else {
                echo "Password did not match.<br>"; // Debugging output for failure
                fclose($fp);
                return false; // Invalid password
            }
        }
    }

    echo "Username not found.<br>"; // Debugging output for username not found
    fclose($fp);
    return false; // Username not found
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
