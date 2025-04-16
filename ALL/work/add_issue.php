<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$pdo = Database::connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $short_description = trim($_POST['short_description']);
    $long_description = trim($_POST['long_description']);
    $open_date = $_POST['open_date'];
    $close_date = $_POST['close_date'];
    $priority = trim($_POST['priority']);
    $per_id = $_SESSION['user_id'];

    if (!empty($short_description) && !empty($long_description) && !empty($open_date) && !empty($priority)) {
        $stmt = $pdo->prepare("INSERT INTO iss_issues (short_description, long_description, open_date, close_date, priority, per_id) 
                               VALUES (:short_description, :long_description, :open_date, :close_date, :priority, :per_id)");
        $stmt->execute([
            ':short_description' => $short_description,
            ':long_description' => $long_description,
            ':open_date' => $open_date,
            ':close_date' => $close_date ?: null,
            ':priority' => $priority,
            ':per_id' => $per_id
        ]);

        Database::disconnect();
        header("Location: issues_list.php");
        exit();
    } else {
        $error = "All fields except Close Date are required.";
    }
}

Database::disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Issue</title>
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
        .form-container {
            background: white;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .form-container h2 {
            text-align: center;
        }
        .form-container input, .form-container textarea, .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container button {
            width: 105%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .back-btn {
            display: inline-block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            text-align: center;
            background-color: #6c757d;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add Issue</h2>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form action="add_issue.php" method="post">
            <input type="text" name="short_description" placeholder="Short Description" required>
            <textarea name="long_description" placeholder="Long Description" required></textarea>
            <label>Open Date:</label>
            <input type="date" name="open_date" required>
            <label>Close Date (optional):</label>
            <input type="date" name="close_date">
            <label>Priority:</label>
            <select name="priority" required>
                <option value="">Select Priority</option>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>
            <button type="submit">Submit Issue</button>
        </form>
        <a href="issues_list.php" class="back-btn">Back to Issues</a>
    </div>
</body>
</html>
