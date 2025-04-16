<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pdo = Database::connect();
$stmt = $pdo->prepare("SELECT id, admin FROM iss_persons WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
Database::disconnect();

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $pdo = Database::connect();
    $delete_stmt = $pdo->prepare("DELETE FROM iss_issues WHERE id = ?");
    $delete_stmt->execute([$delete_id]);
    Database::disconnect();
    header("Location: issues_list.php");
    exit();
}

if (isset($_POST['update_issue'])) {
    $issue_id = $_POST['issue_id'];
    $short_description = $_POST['short_description'];
    $long_description = $_POST['long_description'];
    $priority = $_POST['priority'];
    $open_date = $_POST['open_date'];
    $close_date = $_POST['close_date'] ?: null;

    $pdo = Database::connect();
    $update_stmt = $pdo->prepare("UPDATE iss_issues SET short_description = ?, long_description = ?, priority = ?, open_date = ?, close_date = ? WHERE id = ?");
    $update_stmt->execute([$short_description, $long_description, $priority, $open_date, $close_date, $issue_id]);
    Database::disconnect();
    header("Location: issues_list.php");
    exit();
}

$pdo = Database::connect();
$sql = "SELECT iss_issues.*, iss_persons.fname, iss_persons.lname 
        FROM iss_issues 
        LEFT JOIN iss_persons ON iss_issues.per_id = iss_persons.id 
        ORDER BY iss_issues.open_date DESC";
$issues = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
Database::disconnect();

$modalContent = '';
$modalType = '';
if (isset($_GET['read_id']) || isset($_GET['update_id'])) {
    $modalType = isset($_GET['read_id']) ? 'read' : 'update';
    $id = $_GET[$modalType . '_id'];

    $pdo = Database::connect();
    $stmt = $pdo->prepare("SELECT * FROM iss_issues WHERE id = ?");
    $stmt->execute([$id]);
    $issue = $stmt->fetch(PDO::FETCH_ASSOC);
    Database::disconnect();

    if ($modalType == 'read') {
        $pdo = Database::connect();
        $comments_stmt = $pdo->prepare("SELECT c.short_comment, c.long_comment, c.posted_date, p.fname, p.lname 
                                        FROM iss_comments c 
                                        JOIN iss_persons p ON c.per_id = p.id 
                                        WHERE c.iss_id = ? 
                                        ORDER BY c.posted_date DESC");
        $comments_stmt->execute([$issue['id']]);
        $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
        Database::disconnect();

        $commentsHtml = '';
        foreach ($comments as $comment) {
            $commentsHtml .= "
                <div class='comment'>
                    <strong>{$comment['fname']} {$comment['lname']}</strong> ({$comment['posted_date']}):<br>
                    <em>" . htmlspecialchars($comment['short_comment']) . "</em><br>
                    <p>" . nl2br(htmlspecialchars($comment['long_comment'])) . "</p>
                    <hr>
                </div>";
        }

        $modalContent = "
        <h3>Issue Details</h3>
        <p><strong>Short Description:</strong> " . htmlspecialchars($issue['short_description']) . "</p>
        <p><strong>Long Description:</strong> " . htmlspecialchars($issue['long_description']) . "</p>
        <p><strong>Priority:</strong> " . htmlspecialchars($issue['priority']) . "</p>
        <p><strong>Open Date:</strong> " . htmlspecialchars($issue['open_date']) . "</p>
        <p><strong>Close Date:</strong> " . htmlspecialchars($issue['close_date'] ?? 'N/A') . "</p>
        <div class='comments-scroll'><h4>Comments</h4>{$commentsHtml}</div>";
    } else {
        $modalContent = '
        <h3>Update Issue</h3>
        <form method="post">
            <input type="hidden" name="issue_id" value="' . $issue['id'] . '">
            <label>Short Description:</label>
            <input type="text" name="short_description" value="' . htmlspecialchars($issue['short_description']) . '" required>
            <label>Long Description:</label>
            <textarea name="long_description" required>' . htmlspecialchars($issue['long_description']) . '</textarea>
            <label>Priority:</label>
            <input type="text" name="priority" value="' . htmlspecialchars($issue['priority']) . '" required>
            <label>Open Date:</label>
            <input type="date" name="open_date" value="' . htmlspecialchars($issue['open_date']) . '" required>
            <label>Close Date:</label>
            <input type="date" name="close_date" value="' . htmlspecialchars($issue['close_date']) . '">
            <button type="submit" name="update_issue">Update Issue</button>
        </form>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issues List</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f7fa; }
        .container { max-width: 1000px; margin: auto; padding: 20px; background-color: #ffffff; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 21px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: white; }
        td button { padding: 5px 10px; margin: 3px; border: none; border-radius: 5px; cursor: pointer; }
        td button:hover { opacity: 0.8; }
        .view-btn { background-color: #007bff; color: white; }
        .update-btn { background-color: #ffc107; color: white; }
        .delete-btn { background-color: #dc3545; color: white; }
        .add-btn, .logout-btn, .persons-btn, .comments-btn {
            display: inline-block; margin-top: 20px; padding: 10px 15px; text-decoration: none; color: white; background-color: #28a745; border-radius: 5px;
        }
        .logout-btn { background-color: #dc3545; }
        .persons-btn { background-color: rgb(38, 176, 180); }
        .comments-btn { background-color: rgb(66, 201, 40); }
        .logout-btn:hover, .add-btn:hover { opacity: 0.8; }

        /* Modal Styling */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); display: flex; justify-content: center; align-items: center;
            z-index: 1000;
        }
        .modal {
            background: #fff; padding: 20px; max-width: 600px; width: 90%;
            border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.3); position: relative; max-height: 80vh; overflow-y: auto;
        }
        .modal textarea, .modal input[type="text"], .modal input[type="date"] {
            width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px;
        }
        .modal button, .modal .close-btn {
            padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer;
        }
        .modal .close-btn {
            background: #6c757d; color: #fff; position: absolute; top: 10px; right: 10px;
        }
        .comments-scroll {
            max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; border-radius: 5px; background-color: #f9f9f9;
            margin-top: 15px;
        }
        .comment p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Issues List</h2>
        <a href="add_issue.php" class="add-btn">+ Add Issue</a>
        <a href="logout.php" class="logout-btn">Logout</a>
        <a href="persons_list.php" class="persons-btn">people</a>
        <a href="comments_list.php" class="comments-btn">comments</a>

        <table>
            <tr>
                <th>ID</th>
                <th>Short Description</th>
                <th>Open Date</th>
                <th>Close Date</th>
                <th>Assigned</th>
                <th>Priority</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($issues as $issue): ?>
                <tr>
                    <td><?= htmlspecialchars($issue['id']) ?></td>
                    <td><?= htmlspecialchars($issue['short_description']) ?></td>
                    <td><?= htmlspecialchars($issue['open_date']) ?></td>
                    <td><?= htmlspecialchars($issue['close_date'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($issue['fname'] . ' ' . $issue['lname']) ?></td>
                    <td><?= htmlspecialchars($issue['priority']) ?></td>
                    <td>
                        <a href="issues_list.php?read_id=<?= $issue['id'] ?>"><button class="view-btn">R</button></a>
                        <a href="issue_detail.php?issue_id=<?= $issue['id'] ?>"><button class="comment-btn">C</button></a>
                        <?php if ($user['admin'] == 'y' || $issue['per_id'] == $user['id']): ?>
                            <a href="issues_list.php?update_id=<?= $issue['id'] ?>"><button class="update-btn">U</button></a>
                            <a href="issues_list.php?delete_id=<?= $issue['id'] ?>"><button class="delete-btn">D</button></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php if ($modalContent): ?>
        <div class="modal-overlay" onclick="closeModal(event)">
            <div class="modal" onclick="event.stopPropagation();">
                <?= $modalContent ?>
                <button class="close-btn" onclick="window.location.href='issues_list.php'">X</button>
            </div>
        </div>
        <script>
            function closeModal(e) {
                if (e.target.classList.contains('modal-overlay')) {
                    window.location.href = 'issues_list.php';
                }
            }
        </script>
    <?php endif; ?>
</body>
</html>
