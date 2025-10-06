<?php
    require_once 'User.php'; 

    session_start();

    $email = ""; 
    $password = ""; 
    $confirm_password = "";
    $username = "";
    
    $uerror = false;
    $eerror = false;
    $perror = false; 
    $cperror = false;
    $registration_error = false;
    $message = "";

    function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $username = !empty($_POST['username']) ? sanitizeInput($_POST['username']) : "";
        $email = !empty($_POST['email']) ? sanitizeInput($_POST['email']) : "";
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
        if (empty($password)) {
            $perror = true;
            $message = "Please enter a password.";
        } elseif (strlen($password) < 6) {
            $perror = true;
            $message = "Password must be at least 6 characters.";
        }
        if (empty($confirm_password)) {
            $cperror = true;
            $message = "Please confirm your password.";
        } elseif ($password !== $confirm_password) {
            $cperror = true;
            $message = "Passwords do not match!";
        }

        // If no errors, create user
        if (!$uerror && !$eerror && !$perror && !$cperror) {
            // Check if email already exists
            // You'll need to add a method to check if email exists in your User class
            // For now, assuming we can create the user
            
            $new_user = new User();
            // Assuming your User class has a create method
            // Adjust this based on your actual User class implementation
            $new_user->username = $username;
            $new_user->email = $email;
            $new_user->password = $password;
            
            $created = $new_user->insert();
            
            if ($created) {
                $_SESSION['user_id'] = $new_user->id;
                $_SESSION['email'] = $new_user->email;
                $_SESSION['username'] = $new_user->username;
                
                header("Location: game-page.php");
                exit;
            } else {
                $registration_error = true;
                $message = "Email/Username already exists or registration failed!";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="style/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Sign Up</h2>
        <h2 style="color:red;">
            <?php if (!empty($message)) echo htmlspecialchars($message); ?>
        </h2>

        <form action="sign-up.php" method="post">
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
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn">Sign Up</button>

            <div class="extra-options">
                <p>Already have an account? <a href="index.php">Log in</a></p>
            </div>
        </form>
    </div>
    
</body>
</html>