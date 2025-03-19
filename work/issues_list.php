<?php
session_start();
require '../database/database.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user details (admin status and user ID)
$pdo = Database::connect();
$stmt = $pdo->prepare("SELECT id, admin FROM iss_persons WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
Database::disconnect();

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $pdo = Database::connect();
    $delete_stmt = $pdo->prepare("DELETE FROM iss_issues WHERE id = ?");
    $delete_stmt->execute([$delete_id]);
    Database::disconnect();
    header("Location: issues_list.php");
    exit();
}

// Handle Update
if (isset($_POST['update_issue'])) {
    $issue_id = $_POST['issue_id'];
    $short_description = $_POST['short_description'];
    $long_description = $_POST['long_description'];
    $priority = $_POST['priority'];
    $project = $_POST['project'];

    $pdo = Database::connect();
    $update_stmt = $pdo->prepare("UPDATE iss_issues SET short_description = ?, long_description = ?, priority = ?, project = ? WHERE id = ?");
    $update_stmt->execute([$short_description, $long_description, $priority, $project, $issue_id]);
    Database::disconnect();
    header("Location: issues_list.php");
    exit();
}

// Fetch Issues
$pdo = Database::connect();
$sql = "SELECT * FROM iss_issues ORDER BY open_date DESC";
$issues = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
Database::disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issues List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        td button {
            padding: 5px 10px;
            margin: 3px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        td button:hover {
            opacity: 0.8;
        }
        .view-btn {
            background-color: #007bff;
            color: white;
        }
        .update-btn {
            background-color: #ffc107;
            color: white;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        .add-btn, .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            text-decoration: none;
            color: white;
            background-color: #28a745;
            border-radius: 5px;
        }
        .logout-btn {
            background-color: #dc3545;
        }
        .logout-btn:hover, .add-btn:hover {
            opacity: 0.8;
        }
        .issue-details, .update-form {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .update-form input, .update-form textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .update-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .exit-btn {
            background-color: #6c757d;
            color: white;
            padding: 5px 10px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .exit-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Issues List</h2>

        <!-- Read or Update Section (At the Top) -->
        <?php
        // Handle Read: Show details of the selected issue
        if (isset($_GET['read_id'])):
            $read_id = $_GET['read_id'];
            $pdo = Database::connect();
            $read_stmt = $pdo->prepare("SELECT * FROM iss_issues WHERE id = ?");
            $read_stmt->execute([$read_id]);
            $issue = $read_stmt->fetch(PDO::FETCH_ASSOC);
            Database::disconnect();
        ?>
            <div class="issue-details">
                <h3>Issue Details</h3>
                <p><strong>Short Description:</strong> <?= htmlspecialchars($issue['short_description']) ?></p>
                <p><strong>Long Description:</strong> <?= htmlspecialchars($issue['long_description']) ?></p>
                <p><strong>Priority:</strong> <?= htmlspecialchars($issue['priority']) ?></p>
                <p><strong>Project:</strong> <?= htmlspecialchars($issue['project']) ?></p>
                <p><strong>Open Date:</strong> <?= htmlspecialchars($issue['open_date']) ?></p>
                <p><strong>Close Date:</strong> <?= htmlspecialchars($issue['close_date'] ?? 'N/A') ?></p>
                <a href="issues_list.php" class="exit-btn">Exit View</a>
            </div>
        <?php endif; ?>

        <?php
        // Handle Update: Show the form to update the issue
        if (isset($_GET['update_id'])):
            $update_id = $_GET['update_id'];
            $pdo = Database::connect();
            $update_stmt = $pdo->prepare("SELECT * FROM iss_issues WHERE id = ?");
            $update_stmt->execute([$update_id]);
            $issue_to_update = $update_stmt->fetch(PDO::FETCH_ASSOC);
            Database::disconnect();
        ?>
            <div class="update-form">
                <h3>Update Issue</h3>
                <form method="post">
                    <input type="hidden" name="issue_id" value="<?= $issue_to_update['id'] ?>">
                    <label for="short_description">Short Description:</label>
                    <input type="text" name="short_description" value="<?= htmlspecialchars($issue_to_update['short_description']) ?>" required>
                    <label for="long_description">Long Description:</label>
                    <textarea name="long_description" required><?= htmlspecialchars($issue_to_update['long_description']) ?></textarea>
                    <label for="priority">Priority:</label>
                    <input type="text" name="priority" value="<?= htmlspecialchars($issue_to_update['priority']) ?>" required>
                    <label for="project">Project:</label>
                    <input type="text" name="project" value="<?= htmlspecialchars($issue_to_update['project']) ?>" required>
                    <button type="submit" name="update_issue">Update Issue</button>
                </form>
                <a href="issues_list.php" class="exit-btn">Exit Update</a>
            </div>
        <?php endif; ?>

        <!-- Issue Table -->
        <table>
            <tr>
                <th>ID</th>
                <th>Short Description</th>
                <th>Open Date</th>
                <th>Close Date</th>
                <th>Priority</th>
                <th>Project</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($issues as $issue): ?>
                <tr>
                    <td><?= htmlspecialchars($issue['id']) ?></td>
                    <td><?= htmlspecialchars($issue['short_description']) ?></td>
                    <td><?= htmlspecialchars($issue['open_date']) ?></td>
                    <td><?= htmlspecialchars($issue['close_date'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($issue['priority']) ?></td>
                    <td><?= htmlspecialchars($issue['project']) ?></td>
                    <td>
                        <!-- Read Button (always visible) -->
                        <a href="issues_list.php?read_id=<?= $issue['id'] ?>" class="view-btn">R</a>

                        <?php
                        // Update and Delete buttons only visible if the user created the issue or is an admin
                        if ($user['admin'] == 'y' || $issue['per_id'] == $user['id']): ?>
                            <!-- Update Button -->
                            <a href="issues_list.php?update_id=<?= $issue['id'] ?>" class="update-btn">U</a>
                            <!-- Delete Button -->
                            <a href="issues_list.php?delete_id=<?= $issue['id'] ?>" class="delete-btn">D</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        
        <!-- Add Issue & Logout Buttons -->
        <a href="add_issue.php" class="add-btn">+ Add Issue</a>
        <a href="login.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
