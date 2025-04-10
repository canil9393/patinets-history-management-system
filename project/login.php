<?php
session_start(); // Start the session
include 'db_connect.php';

// Check if the user is already logged in
if (isset($_SESSION['receptionist_id'])) {
    header("Location: index.php");
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Check if the email exists
        $stmt = $conn->prepare("SELECT * FROM receptionists WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $receptionist = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($receptionist) {
            // Verify password (assuming password is hashed in the database)
            if (password_verify($password, $receptionist['password'])) {
                // Login successful, set session variables
                $_SESSION['receptionist_id'] = $receptionist['id'];
                $_SESSION['receptionist_name'] = $receptionist['first_name'] . ' ' . $receptionist['last_name'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receptionist Authentication</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('hospital_img.jpg') no-repeat center center fixed;
            background-size: cover;
            backdrop-filter: blur(5px);
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
        .employee-id-spacing {
            margin-top: 15px;
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
        .toggle-link, .forgot-link {
            margin-top: 8px;
            color: #13c62b;
            cursor: pointer;
            text-decoration: underline;
            font-size: 12px;
        }
        .hidden {
            display: none;
        }
        .error {
            color: red;
            font-size: 12px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 id="form-title">Receptionist Login</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php" id="auth-form">
            <div id="name-fields" style="display: none;">
                <div class="input-group">
                    <label for="first-name">First Name:</label>
                    <input type="text" id="first-name" name="firstName">
                </div>
                <div class="input-group">
                    <label for="last-name">Last Name:</label>
                    <input type="text" id="last-name" name="lastName">
                </div>
                <div class="input-group employee-id-spacing">
                    <label for="employee-id">Employee ID:</label>
                    <input type="text" id="employee-id" name="employeeId">
                </div>
                <div class="input-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone">
                </div>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="submit" class="btn" id="submit-btn" value="Login">
            <div class="toggle-link" onclick="window.location.href='signup.php'">Don't have an account? Sign Up</div>
            <div class="forgot-link" onclick="window.location.href='forgot_password.php'">Forgot Password?</div>
        </form>
    </div>
</body>
</html>