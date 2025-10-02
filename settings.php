<?php
require_once 'User.php'; 
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user = new User();
$user->populate($_SESSION['user_id']); // Assuming your User class has this method

$email = $user->email; 
$username = $user->username;
$password = "";
$confirm_password = "";

$uerror = false;
$eerror = false;
$perror = false;
$cperror = false;
$update_error = false;
$message = "";

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = !empty($_POST['username']) ? sanitizeInput($_POST['username']) : $username;
    $email = !empty($_POST['email']) ? sanitizeInput($_POST['email']) : $email;
    $password = !empty($_POST['password']) ? sanitizeInput($_POST['password']) : "";
    $confirm_password = !empty($_POST['confirm_password']) ? sanitizeInput($_POST['confirm_password']) : "";

    // Validation
    if (empty($username)) {
        $uerror = true;
        $message = "Please enter a username.";
    }
    if (empty($email)) {
        $eerror = true;
        $message = "Please enter an email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $eerror = true;
        $message = "Please enter a valid email address.";
    }
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $perror = true;
            $message = "Password must be at least 6 characters.";
        }
        if ($password !== $confirm_password) {
            $cperror = true;
            $message = "Passwords do not match!";
        }
    }

    // If no errors, update user
    if (!$uerror && !$eerror && !$perror && !$cperror) {
        $user->username = $username;
        $user->email = $email;
        if (!empty($password)) {
            $user->password = $password; // make sure your User->update() hashes password
        }
        
        $updated = $user->update();

        if ($updated) {
            $_SESSION['email'] = $user->email;
            $_SESSION['username'] = $user->username;
            $message = "Profile updated successfully!";
        } else {
            $update_error = true;
            $message = "Update failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="style/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Account Settings</h2>
        <h2 style="color:red;">
            <?php if (!empty($message)) echo htmlspecialchars($message); ?>
        </h2>

        <form action="settings.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="<?php echo htmlspecialchars($username); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?php echo htmlspecialchars($email); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">New Password (leave blank to keep current)</label>
                <input type="password" id="password" name="password">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>

            <button type="submit" class="btn">Save Changes</button>

            <div class="extra-options">
                <p><a href="game-page.php">Back to Game</a></p>
            </div>
        </form>
    </div>
</body>
</html>
