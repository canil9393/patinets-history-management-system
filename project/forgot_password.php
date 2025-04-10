<?php
session_start();
include 'db_connect.php';

// Redirect if already logged in
if (isset($_SESSION['receptionist_id'])) {
    header("Location: index.php");
    exit();
}

// Handle forgot password form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reset_input = trim($_POST['resetEmail']);

    // Validate input
    if (empty($reset_input)) {
        $error = "Please enter your email or phone number.";
    } else {
        // Check if the email or phone exists
        $stmt = $conn->prepare("SELECT * FROM receptionists WHERE email = :input OR phone = :input");
        $stmt->bindParam(':input', $reset_input);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $success = "A reset link has been sent to " . htmlspecialchars($reset_input) . " (simulated).";
        } else {
            $error = "No account found with that email or phone number.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('images/hospitalimg.jpg') no-repeat center center fixed;
            background-size: cover;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }
        .container {
            background-color: rgb(227, 236, 244);
            padding: 31px;
            width: 350px;
            box-shadow: 0px 4px 10px rgba(237, 139, 139, 0.1);
            border-radius: 10px;
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            margin-top: 50px;
            margin-bottom: 50px;
        }
        h2 {
            margin-bottom: 15px;
            color: #0f8c2c;
            font-size: 20px;
        }
        .input-group {
            text-align: left;
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #15a447;
            font-size: 14px;
        }
        .input-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn {
            background-color: #19d729;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #21d56c;
        }
        .toggle-link {
            margin-top: 8px;
            color: #13c62b;
            cursor: pointer;
            text-decoration: underline;
            font-size: 12px;
        }
        .error, .success {
            font-size: 12px;
            margin-bottom: 15px;
        }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="forgot_password.php">
            <div class="input-group">
                <label for="reset-email">Email or Phone Number:</label>
                <input type="text" id="reset-email" name="resetEmail" required>
            </div>
            <input type="submit" class="btn" value="Send Reset Link">
            <div class="toggle-link" onclick="window.location.href='login.php'">Back to Login</div>
        </form>
    </div>
</body>
</html>