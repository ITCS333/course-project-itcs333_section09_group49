<?php
session_start();

setcookie('user_id', '', time() - 3600, "/");
setcookie('username', '', time() - 3600, "/");
setcookie('role', '', time() - 3600, "/");

session_destroy();
header("Location: HomePage.php");
exit();
?>
