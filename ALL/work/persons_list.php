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
    $delete_stmt = $pdo->prepare("DELETE FROM iss_persons WHERE id = ?");
    $delete_stmt->execute([$delete_id]);
    Database::disconnect();
    header("Location: persons_list.php");
    exit();
}

// Handle Update
if (isset($_POST['update_persons'])) {
    $id = $_POST['id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];

    $pdo = Database::connect();
    $update_stmt = $pdo->prepare("UPDATE iss_persons SET fname = ?, lname = ?, mobile = ?, email = ? WHERE id = ?");
    $update_stmt->execute([$fname, $lname, $mobile, $email, $id]);
    Database::disconnect();
    header("Location: persons_list.php");
    exit();
}

// Fetch Persons
$pdo = Database::connect();
$sql = "SELECT * FROM iss_persons ORDER BY id DESC";
$persons = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
Database::disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Persons List</title>
    <style>
        /* same styles as issues_list.php */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px 20px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        td button {
            padding: 5px 10px;
            margin: 2px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .view-btn { background-color: #007bff; color: white; }
        .update-btn { background-color: #ffc107; color: white; }
        .delete-btn { background-color: #dc3545; color: white; }
        .add-btn, .logout-btn, .back-btn {
            padding: 10px 15px;
            margin: 5px 5px 0 0;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            display: inline-block;
        }
        .add-btn { background-color: #28a745; }
        .logout-btn { background-color: #dc3545; }
        .back-btn { background-color: rgb(177, 61, 115); }
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .modal button[type="submit"] {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Persons List</h2>
    <a href="register.php" class="add-btn">+ Add Person</a>
    <a href="issues_list.php" class="back-btn">Back to Issues</a>
    <a href="logout.php" class="logout-btn">Logout</a>

    <table>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>Admin</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($persons as $person): ?>
            <tr>
                <td><?= htmlspecialchars($person['id']) ?></td>
                <td><?= htmlspecialchars($person['fname']) ?></td>
                <td><?= htmlspecialchars($person['lname']) ?></td>
                <td><?= htmlspecialchars($person['mobile']) ?></td>
                <td><?= htmlspecialchars($person['email']) ?></td>
                <td><?= htmlspecialchars($person['admin']) ?></td>
                <td>
                    <button class="view-btn" onclick='openReadModal(<?= json_encode($person) ?>)'>R</button>
                    <?php if ($user['admin'] == 'y' || $person['id'] == $user['id']): ?>
                        <button class="update-btn" onclick='openUpdateModal(<?= json_encode($person) ?>)'>U</button>
                        <a href="persons_list.php?delete_id=<?= $person['id'] ?>"><button class="delete-btn">D</button></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- Read Modal -->
<div id="readModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('readModal')">&times;</span>
        <h3>Person Details</h3>
        <p><strong>ID:</strong> <span id="read-id"></span></p>
        <p><strong>First Name:</strong> <span id="read-fname"></span></p>
        <p><strong>Last Name:</strong> <span id="read-lname"></span></p>
        <p><strong>Mobile:</strong> <span id="read-mobile"></span></p>
        <p><strong>Email:</strong> <span id="read-email"></span></p>
        <p><strong>Admin:</strong> <span id="read-admin"></span></p>
    </div>
</div>

<!-- Update Modal -->
<div id="updateModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('updateModal')">&times;</span>
        <h3>Update Person</h3>
        <form method="post">
            <input type="hidden" name="id" id="update-id">
            <label>First Name:</label>
            <input type="text" name="fname" id="update-fname" required>
            <label>Last Name:</label>
            <input type="text" name="lname" id="update-lname" required>
            <label>Mobile:</label>
            <input type="text" name="mobile" id="update-mobile" required>
            <label>Email:</label>
            <input type="email" name="email" id="update-email" required>
            <button type="submit" name="update_persons">Update</button>
        </form>
    </div>
</div>

<script>
    function openReadModal(data) {
        document.getElementById('read-id').textContent = data.id;
        document.getElementById('read-fname').textContent = data.fname;
        document.getElementById('read-lname').textContent = data.lname;
        document.getElementById('read-mobile').textContent = data.mobile;
        document.getElementById('read-email').textContent = data.email;
        document.getElementById('read-admin').textContent = data.admin;
        document.getElementById('readModal').style.display = 'block';
    }

    function openUpdateModal(data) {
        document.getElementById('update-id').value = data.id;
        document.getElementById('update-fname').value = data.fname;
        document.getElementById('update-lname').value = data.lname;
        document.getElementById('update-mobile').value = data.mobile;
        document.getElementById('update-email').value = data.email;
        document.getElementById('updateModal').style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // Close modals on outside click
    window.onclick = function(event) {
        const readModal = document.getElementById('readModal');
        const updateModal = document.getElementById('updateModal');
        if (event.target === readModal) readModal.style.display = "none";
        if (event.target === updateModal) updateModal.style.display = "none";
    }
</script>
</body>
</html>
