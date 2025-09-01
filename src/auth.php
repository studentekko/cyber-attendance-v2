<?php
session_start();  

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


// $isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin');
$isAdmin = true;
function requireAdmin() {
    global $isAdmin;
    if (!$isAdmin) {
        echo "<script>
            alert('Access denied. Only admins can manage teachers.');
            window.location.href = 'dashboard.php';
        </script>";
        exit;
    }
}
?>
