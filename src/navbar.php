<?php
// session_start(); 
require_once('auth.php'); 
require_once('../db/connection.php');


$stmt = $pdo->query("SELECT * FROM teachers ORDER BY id ASC");
$teachers = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM students ORDER BY id ASC");
$students = $stmt->fetchAll();

$user_name = '';
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $user_name = $user['username'];
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">School Management</a>
        <div class="d-flex align-items-center ms-auto">
            <form method="post" action="search.php" class="d-flex align-items-center ms-auto">
                <input class="form-control me-2" name="search" type="search" placeholder="Search">
                <button type="submit" class="btn btn-light me-4"><i class="bi bi-search"></i></button>
            </form>
            <div class="text-white d-flex gap-4 me-4">
                <span><i class="bi bi-people-fill"></i> Students <span class="badge bg-light text-dark"><?=count($students)?></span></span>
                <span><i class="bi bi-person-video2"></i> Teachers <span class="badge bg-light text-dark"><?=count($teachers)?></span></span>
            </div>

            <?php if (!empty($user_name)): ?>
                <span class="text-white me-3">Welcome, <?=htmlspecialchars($user_name)?>!</span>
                <form method="post" action="logout.php" class="d-inline">
                    <button type="submit" class="btn btn-outline-light me-2">Logout</button>
                </form>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light me-2">Login</a>
                <a href="register.php" class="btn btn-warning">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
