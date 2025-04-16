<?php
session_start();
require '../database/database.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $mobile = trim($_POST['mobile']);
    
    // Validate mobile format
    if (!preg_match('/^\d{3}-\d{3}-\d{4}$/', $mobile)) {
        $error = "Invalid mobile number format. Use XXX-XXX-XXXX.";
    } elseif (!empty($fname) && !empty($lname) && !empty($email) && !empty($password) && !empty($mobile)) {
        try {
            $pdo = Database::connect();
            
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM iss_persons WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $error = "Email already exists. Please use another.";
            } else {
                // Generate random salt and hash the salted password
                $salt = bin2hex(random_bytes(16)); // 32-character hex salt
                $hashed_password = md5($salt . $password);
                $default_admin = 'n';

                // Insert user with salted hash
                $stmt = $pdo->prepare("INSERT INTO iss_persons (fname, lname, mobile, email, pwd_hash, pwd_salt, admin) 
                                       VALUES (:fname, :lname, :mobile, :email, :pwd_hash, :pwd_salt, :admin)");
                $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
                $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
                $stmt->bindParam(':mobile', $mobile, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':pwd_hash', $hashed_password, PDO::PARAM_STR);
                $stmt->bindParam(':pwd_salt', $salt, PDO::PARAM_STR);
                $stmt->bindParam(':admin', $default_admin, PDO::PARAM_STR);
                
                if ($stmt->execute()) {
                    Database::disconnect();
                    header("Location: login.php?success=registered");
                    exit();
                } else {
                    $error = "Error inserting user.";
                }
            }
            
            Database::disconnect();
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - DSR</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            margin: 0;
        }
        .register-container {
            background: white;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        .register-container h2 {
            margin-bottom: 20px;
        }
        .register-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .register-container button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .register-container button:hover {
            background-color: #218838;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Create an Account</h2>
        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form action="register.php" method="post">
            <input type="text" name="fname" placeholder="First Name" required>
            <input type="text" name="lname" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="mobile" placeholder="Mobile (XXX-XXX-XXXX)" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
