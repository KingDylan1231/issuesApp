<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$issue_id = $_GET['issue_id'] ?? null;
if (!$issue_id) {
    header("Location: issues_list.php");
    exit();
}

$pdo = Database::connect();
$issue_stmt = $pdo->prepare("SELECT * FROM iss_issues WHERE id = ?");
$issue_stmt->execute([$issue_id]);
$issue = $issue_stmt->fetch(PDO::FETCH_ASSOC);

$comments_stmt = $pdo->prepare(
    "SELECT c.*, CONCAT(p.fname, ' ', p.lname) AS full_name 
    FROM iss_comments c 
    JOIN iss_persons p ON c.per_id = p.id 
    WHERE c.iss_id = ? 
    ORDER BY c.posted_date DESC"
);
$comments_stmt->execute([$issue_id]);
$comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
Database::disconnect();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_comment'])) {
    $short_comment = $_POST['short_comment'];
    $long_comment = $_POST['long_comment'];
    $user_id = $_SESSION['user_id'];
    $posted_date = date('Y-m-d');

    $pdo = Database::connect();
    $insert_stmt = $pdo->prepare(
        "INSERT INTO iss_comments (per_id, iss_id, short_comment, long_comment, posted_date) VALUES (?, ?, ?, ?, ?)"
    );
    $insert_stmt->execute([$user_id, $issue_id, $short_comment, $long_comment, $posted_date]);
    Database::disconnect();
    header("Location: issue_detail.php?issue_id=$issue_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            text-align: center;
            color: #333;
        }
        .comment-box {
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }
        .comment-box strong {
            color: #007bff;
        }
        .comment-box p {
            margin: 5px 0;
        }
        .add-comment {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .add-comment textarea, .add-comment input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .add-comment button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .add-comment button:hover {
            opacity: 0.8;
        }
        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Issue: <?= htmlspecialchars($issue['short_description']) ?></h2>
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($issue['long_description'])) ?></p>
        
        <h3>Comments</h3>
        <?php foreach ($comments as $comment): ?>
            <div class="comment-box">
                <p><strong><?= htmlspecialchars($comment['full_name']) ?> (<?= $comment['posted_date'] ?>):</strong></p>
                <p><em><?= htmlspecialchars($comment['short_comment']) ?></em></p>
                <p><?= nl2br(htmlspecialchars($comment['long_comment'])) ?></p>
            </div>
        <?php endforeach; ?>
        
        <div class="add-comment">
            <h3>Add a Comment</h3>
            <form method="post">
                <input type="text" name="short_comment" placeholder="Short comment" required>
                <textarea name="long_comment" placeholder="Long comment" rows="4" required></textarea>
                <button type="submit" name="add_comment">Submit</button>
            </form>
        </div>
        
        <a href="issues_list.php" class="back-btn">Back to Issues</a>
    </div>
</body>
</html>