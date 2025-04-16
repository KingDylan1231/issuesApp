<?php
session_start();
require '../database/database.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pdo = Database::connect();

// Get current user info
$stmt = $pdo->prepare("SELECT id, admin FROM iss_persons WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Delete Comment
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // First, check ownership or admin
    $check = $pdo->prepare("SELECT per_id FROM iss_comments WHERE id = ?");
    $check->execute([$delete_id]);
    $commentOwner = $check->fetchColumn();
    if ($user['admin'] == 'y' || $commentOwner == $user['id']) {
        $del = $pdo->prepare("DELETE FROM iss_comments WHERE id = ?");
        $del->execute([$delete_id]);
    }
    header("Location: comments_list.php");
    exit();
}

// Update Comment
if (isset($_POST['update_comment'])) {
    $comment_id = $_POST['comment_id'];
    $short_comment = $_POST['short_comment'];
    $long_comment = $_POST['long_comment'];

    $update = $pdo->prepare("UPDATE iss_comments SET short_comment = ?, long_comment = ? WHERE id = ?");
    $update->execute([$short_comment, $long_comment, $comment_id]);

    header("Location: comments_list.php");
    exit();
}

// Fetch comments list
$sql = "SELECT c.id, c.short_comment, c.long_comment, c.posted_date, 
               c.per_id, p.fname, p.lname, i.short_description, i.project 
        FROM iss_comments c
        JOIN iss_persons p ON c.per_id = p.id
        JOIN iss_issues i ON c.iss_id = i.id
        ORDER BY c.posted_date DESC";
$comments = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Prepare modal content
$modalContent = '';
$modalType = '';

if (isset($_GET['read_id'])) {
    $modalType = 'read';
    $read_id = $_GET['read_id'];
    $stmt = $pdo->prepare("
        SELECT c.*, p.fname, p.lname 
        FROM iss_comments c
        JOIN iss_persons p ON c.per_id = p.id
        WHERE c.id = ?
    ");
    $stmt->execute([$read_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $modalContent = "
        <h3>Comment Details</h3>
        <p><strong>Created By:</strong> " . htmlspecialchars($comment['fname'] . ' ' . $comment['lname']) . "</p>
        <p><strong>Short:</strong> " . htmlspecialchars($comment['short_comment']) . "</p>
        <p><strong>Long:</strong> " . htmlspecialchars($comment['long_comment']) . "</p>
        <p><strong>Date:</strong> " . htmlspecialchars($comment['posted_date']) . "</p>";
}

if (isset($_GET['update_id'])) {
    $modalType = 'update';
    $update_id = $_GET['update_id'];
    $stmt = $pdo->prepare("SELECT * FROM iss_comments WHERE id = ?");
    $stmt->execute([$update_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Only show update form if user is admin or owner
    if ($user['admin'] == 'y' || $comment['per_id'] == $user['id']) {
        $modalContent = '
            <h3>Update Comment</h3>
            <form method="post">
                <input type="hidden" name="comment_id" value="' . $comment['id'] . '">
                <label>Short Comment:</label>
                <input type="text" name="short_comment" value="' . htmlspecialchars($comment['short_comment']) . '" required>
                <label>Long Comment:</label>
                <textarea name="long_comment" required>' . htmlspecialchars($comment['long_comment']) . '</textarea>
                <button type="submit" name="update_comment" class="update-btn">Update</button>
            </form>';
    }
}

Database::disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Comments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 20px;
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
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .logout-btn {
            margin-top: 20px;
            padding: 10px 15px;
            text-decoration: none;
            color: white;
            background-color: #dc3545;
            border-radius: 5px;
        }
        .action-btn {
            padding: 5px 10px;
            margin: 2px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .view-btn { background-color: #007bff; color: white; }
        .update-btn { background-color: #ffc107; color: white; }
        .delete-btn { background-color: #dc3545; color: white; }
        .exit-btn {
            background-color: #6c757d;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
        }
        
        /* Modal Styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal {
            background: #fff;
            padding: 20px;
            max-width: 500px;
            width: 90%;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            position: relative;
        }
        .modal textarea, .modal input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .modal button, .modal .close-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal .close-btn {
            background: #6c757d;
            color: #fff;
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>All Comments</h2>
    <a href="issues_list.php" class="logout-btn">Back to Issues</a>

    <!-- Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Issue</th>
            <th>Comment By</th>
            <th>Short</th>
            <th>Posted</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($comments as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['short_description']) ?></td>
            <td><?= htmlspecialchars($c['fname'] . ' ' . $c['lname']) ?></td>
            <td><?= htmlspecialchars($c['short_comment']) ?></td>
            <td><?= $c['posted_date'] ?></td>
            <td>
                <a href="comments_list.php?read_id=<?= $c['id'] ?>"><button class="action-btn view-btn">R</button></a>
                <?php if ($user['admin'] == 'y' || $c['per_id'] == $user['id']): ?>
                    <a href="comments_list.php?update_id=<?= $c['id'] ?>"><button class="action-btn update-btn">U</button></a>
                    <a href="comments_list.php?delete_id=<?= $c['id'] ?>"><button class="action-btn delete-btn">D</button></a>
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
            <button class="close-btn" onclick="window.location.href='comments_list.php'">X</button>
        </div>
    </div>
    <script>
        function closeModal(e) {
            if (e.target.classList.contains('modal-overlay')) {
                window.location.href = 'comments_list.php';
            }
        }
    </script>
<?php endif; ?>

</body>
</html>