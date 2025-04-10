<?php
session_start();
include 'db_connect.php';

// Redirect if already logged in
if (isset($_SESSION['receptionist_id'])) {
    header("Location: index.php");
    exit();
}

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['firstName']);
    $last_name = trim($_POST['lastName']);
    $employee_id = trim($_POST['employeeId']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($first_name) || empty($last_name) || empty($employee_id) || empty($phone) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email or employee ID already exists
        $stmt = $conn->prepare("SELECT * FROM receptionists WHERE email = :email OR employee_id = :employee_id");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = "Email or Employee ID already exists.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new receptionist into the database
            $stmt = $conn->prepare("INSERT INTO receptionists (first_name, last_name, employee_id, phone, email, password) VALUES (:first_name, :last_name, :employee_id, :phone, :email, :password)");
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                $success = "Sign Up successful! Please log in.";
            } else {
                $error = "An error occurred during signup. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receptionist Sign Up</title>
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
        <h2>Receptionist Sign Up</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="signup.php">
            <div class="input-group">
                <label for="first-name">First Name:</label>
                <input type="text" id="first-name" name="firstName" required>
            </div>
            <div class="input-group">
                <label for="last-name">Last Name:</label>
                <input type="text" id="last-name" name="lastName" required>
            </div>
            <div class="input-group employee-id-spacing">
                <label for="employee-id">Employee ID:</label>
                <input type="text" id="employee-id" name="employeeId" required>
            </div>
            <div class="input-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="submit" class="btn" value="Sign Up">
            <div class="toggle-link" onclick="window.location.href='login.php'">Already have an account? Login</div>
        </form>
    </div>
</body>
</html>