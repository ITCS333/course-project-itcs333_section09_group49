<?php
include 'Config.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: HomePage.php");
    exit();
}

$message = '';
$message_type = ''; 
$action = $_GET['action'] ?? 'dashboard';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user_id']]);
                $message = "Password changed successfully!";
                $message_type = 'success';
            } else {
                $message = "New passwords don't match!";
                $message_type = 'error';
            }
        } else {
            $message = "Current password is incorrect!";
            $message_type = 'error';
        }
    }
    elseif (isset($_POST['add_student'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash('student123', PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'student')");
        $stmt->execute([$username, $password, $email]);
        $message = "Student added! Password: student123";
        $message_type = 'success';
    }
    elseif (isset($_POST['update_student'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $id]);
        $message = "Student updated successfully!";
        $message_type = 'success';
    }
    elseif (isset($_POST['delete_user'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $message = "User deleted!";
        $message_type = 'success';
    }
}


$students = [];
if ($action == 'students') {
    $stmt = $conn->query("SELECT * FROM users WHERE role = 'student'");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<html>
<head>
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial; max-width: 1200px; margin: auto; padding: 20px; }
        .header { background: #2c3e50; color: white; padding: 15px; }
        .nav { background: #34495e; padding: 10px; margin: 10px 0; }
        .nav a { color: white; text-decoration: none; margin: 0 10px; }
        .message { padding: 10px; margin: 10px 0; }
        .success { background: #dff0d8; color: green; }
        .error { background: #f2dede; color: red; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #34495e; color: white; }
        button { background: #3498db; color: white; padding: 8px 15px; border: none; margin: 5px; }
        .danger { background: #e74c3c; }
        .edit-form { background: #f8f9fa; padding: 15px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Panel</h1>
    </div>

    <div class="nav">
        <a href="HomePage.php">← Home</a>
        <a href="AdminPortal.php?action=dashboard">Dashboard</a>
        <a href="AdminPortal.php?action=students">Students</a>
        <a href="AdminPortal.php?action=password">Password</a>
        <a href="logout.php">Logout</a>
    </div>

    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div style="display: flex;">
        <div style="width: 200px; background: #ecf0f1; padding: 15px; margin-right: 20px;">
            <h3>Menu</h3>
            <a href="AdminPortal.php?action=dashboard">Dashboard</a><br>
            <a href="AdminPortal.php?action=students">Manage Students</a><br>
            <a href="AdminPortal.php?action=password">Change Password</a>
        </div>

        <div style="flex: 1;">
            <?php if ($action == 'dashboard'): ?>
                <h2>Dashboard</h2>
                <p>Welcome, <?php echo $_SESSION['username']; ?>!</p>

            <?php elseif ($action == 'students'): ?>
                <h2>Manage Students</h2>
                
                <div style="background: #f8f9fa; padding: 15px; margin: 15px 0;">
                    <h3>Add Student</h3>
                    <form method="POST">
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <button type="submit" name="add_student">Add Student</button>
                    </form>
                    <small>Default password: student123</small>
                </div>

                <h3>Student List</h3>
                <?php if (count($students) > 0): ?>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['id']; ?></td>
                            <td><?php echo $student['username']; ?></td>
                            <td><?php echo $student['email']; ?></td>
                            <td>
                                <button onclick="showEditForm(<?php echo $student['id']; ?>, '<?php echo $student['username']; ?>', '<?php echo $student['email']; ?>')">Edit</button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                                    <button type="submit" name="delete_user" class="danger" onclick="return confirm('Delete this student?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>No students found.</p>
                <?php endif; ?>

                <div id="editForm" class="edit-form" style="display: none;">
                    <h3>Edit Student</h3>
                    <form method="POST">
                        <input type="hidden" name="id" id="edit_id">
                        <input type="text" name="username" id="edit_username" placeholder="Username" required>
                        <input type="email" name="email" id="edit_email" placeholder="Email" required>
                        <button type="submit" name="update_student">Update Student</button>
                        <button type="button" onclick="hideEditForm()">Cancel</button>
                    </form>
                </div>

            <?php elseif ($action == 'password'): ?>
                <h2>Change Password</h2>
                <form method="POST">
                    <p>Current Password:<br>
                    <input type="password" name="current_password" required></p>
                    
                    <p>New Password:<br>
                    <input type="password" name="new_password" required></p>
                    
                    <p>Confirm New Password:<br>
                    <input type="password" name="confirm_password" required></p>
                    
                    <button type="submit" name="change_password">Change Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function showEditForm(id, username, email) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        document.getElementById('editForm').style.display = 'block';
    }
    
    function hideEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }
    </script>
</body>
</html>
