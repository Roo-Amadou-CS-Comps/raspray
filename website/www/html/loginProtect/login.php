<?php
session_start();   

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once '/var/www/html/evenmoreimportant/vendor/autoload.php';
use WhiteHat101\Crypt\APR1_MD5;

$filename = '/var/www/html/evenmoreimportant/data/hashes.txt';

function add_log($username) {
    $logTitle = $username . "_log";
    
    // Accessing the session variable safely
    $logSerialized = $_SESSION[$logTitle] ?? null; 

    if ($logSerialized === null) { 
        $logUnserialized = array(); // Initialize a new array if not set
    } else {
        $logUnserialized = unserialize($logSerialized);
    }

    // Add the current timestamp
    array_push($logUnserialized, time());
    
    // Update the session with the new serialized data
    $_SESSION[$logTitle] = serialize($logUnserialized);

    // Update the Apache environment variable
    apache_setenv($logTitle, serialize($logUnserialized));
}

function check_login_logs($username) {
    $logTitle = $username . "_log";
    
    // Retrieve the serialized log from the session
    $logSerialized = $_SESSION[$logTitle] ?? null; 

    if ($logSerialized === null) { 
        return true; // No logs mean it's safe to allow login
    } 

    $logUnserialized = unserialize($logSerialized);
    
    // Remove entries older than 10 minutes (600 seconds)
    $currentTime = time();
    foreach ($logUnserialized as $key => $timestamp) {
        if ($currentTime - $timestamp >= 600) {
            unset($logUnserialized[$key]);
        }
    }

    // Re-index the array after removal
    $logUnserialized = array_values($logUnserialized);

    // Update the session with the cleaned array
    $_SESSION[$logTitle] = serialize($logUnserialized);

    // Check if the number of recent logins exceeds the limit
    if (count($logUnserialized) >= 10) {
        return false; // Too many attempts in the last 10 minutes
    }
    return true; // Safe to allow login
}

function validate_user($username, $password, $filename) {
    $fp = fopen($filename, 'r');
    if (!$fp) {
        die('Password file not found.');
    }

    // Check if login attempts are within acceptable limits
    if (!check_login_logs($username)) {
        fclose($fp);
        echo "Too many login attempts. Please try again later.<br>";
        return false; // Deny login due to too many attempts
    }

    // Read each line and check for the username and hashed password
    while (($line = fgets($fp)) !== false) {
        // Each line is in the format: username:hashedpassword
        list($storedUser, $hashedPassword) = explode(':', trim($line), 2);

        // Check if the username matches
        if ($storedUser === $username) {                
            // Verify the entered password using APR1_MD5::check() and the stored hash
            if (APR1_MD5::check($password, $hashedPassword)) {
                add_log($username); // Log the successful attempt
                fclose($fp);
                echo "Password matched.<br>"; // Debugging output for success
                return true; // Valid username and password
            } else {
                add_log($username); // Log the failed attempt
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
