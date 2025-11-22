<?php
include 'Config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: HomePage.php");
        exit();
    } else {
        $error = "Wrong username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Course System</title>
    <style>
        body { font-family: Arial; max-width: 1000px; margin: auto; padding: 20px; }
        .header { background: #34355eff; color: white; padding: 15px; }
        .nav { background: #34355eff; padding: 10px; margin: 10px 0; }
        .nav a { color: white; text-decoration: none; margin: 0 10px; }
        .login-box { background: #f5f5f5; padding: 20px; margin: 20px 0; }
        .error { color: red; background: #ffebee; padding: 10px; }
        input { padding: 8px; margin: 5px 0; width: 200px; }
        button { background: #3498db; color: white; padding: 8px 15px; border: none; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Course Management System</h1>
    </div>

    <div class="nav">
        <a href="HomePage.php">Home</a>
        <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?>
                <a href="AdminPortal.php">Admin</a>
            <?php endif; ?>
            <a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
        <?php else: ?>
            <a href="#login">Login</a>
        <?php endif; ?>
    </div>

    <div style="display: flex; gap: 20px;">
        <div style="flex: 2;">
            <h2>Welcome to Web Development Course</h2>
            <p>This is your course management system.</p>
        </div>

        <div style="flex: 1;">
            <?php if (!isLoggedIn()): ?>
                <div class="login-box">
                    <h3>Login</h3>
                    
                    <?php if (isset($error)): ?>
                        <div class="error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <p>Username:<br>
                        <input type="text" name="username" required></p>
                        
                        <p>Password:<br>
                        <input type="password" name="password" required></p>
                        
                        <button type="submit" name="login">Login</button>
                    </form>

                    <p><b>Demo Login:</b><br>
                    Admin: admin / password<br>
                    Instructor: instructor1 / password<br>
                    Student: student1 / password</p>
                </div>
            <?php else: ?>
                <h3>Hello, <?php echo $_SESSION['username']; ?>!</h3>
                <p>Role: <?php echo $_SESSION['role']; ?></p>
                
                <?php if (isAdmin()): ?>
                    <a href="AdminPortal.php"><button>Admin Panel</button></a>
                <?php else: ?>
                    <p>Welcome <?php echo $_SESSION['username']; ?>! You are logged in as a student.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>