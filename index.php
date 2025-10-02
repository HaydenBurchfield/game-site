<?php
require_once 'User.php';
session_start();

$username = "";
$password = "";
$uerror = false;
$perror = false;
$invalid_login = false;

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = !empty($_POST['email']) ? sanitizeInput($_POST['email']) : "";
    $password = !empty($_POST['password']) ? sanitizeInput($_POST['password']) : "";

    if (empty($email)) $uerror = true;
    if (empty($password)) $perror = true;

    if (!$uerror && !$perror) {
        $userid = User::validateUser($email, $password);

        if ($userid != 0) {
            $test_user = new User();
            $test_user->populate($userid);

            $_SESSION['user_id'] = $test_user->id;
            $_SESSION['email'] = $test_user->email;

            header("Location: game-page.php");
            exit;
        } else {
            $invalid_login = true;
            $message = "Invalid email or password!";
        }
    } else {
        $message = "Please enter both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="style/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login: </h2>
        <h2 style="color:red;">
            <?php if (!empty($message)) echo htmlspecialchars($message); ?>
        </h2>

        <form action="index.php" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn">Log In</button>

            <div class="extra-options">
                <p>Don't have an account? <a href="sign-up.php">Sign up</a></p>
            </div>
        </form>
    </div>
</body>
</html>
