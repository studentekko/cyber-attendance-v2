<?php

session_start();
// require_once ('auth.php'); 
require_once('./db/connection.php'); 

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['password'])) {
           
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

             if ($_SESSION['role'] === 'teacher') {
               
                header("Location: teacher_dashboard.php");
                exit();
            } else {
          
                header("Location: dashboard.php"); 
                exit();
            }

        } else {
            $errors[] = "Incorrect password!";
        }
    } else {
        $errors[] = "Username not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Campus Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    height: 100vh;
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    display: flex;
    justify-content: center;
    align-items: center;
}
.card {
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 400px;
    padding: 2rem;
    background: #ffffffee;
    backdrop-filter: blur(10px);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
}
h3 { font-weight: 600; color: #4e73df; text-align: center; margin-bottom: 1.5rem; }
.form-control { border-radius: 50px; padding: 0.75rem 1rem; border: 1px solid #ddd; transition: 0.3s; }
.form-control:focus { border-color: #4e73df; box-shadow: 0 0 10px rgba(78,115,223,0.3); }
.btn-primary { border-radius: 50px; padding: 0.75rem; font-weight: 500; background: linear-gradient(45deg,#4e73df,#1cc88a); border:none; transition:0.3s; }
.btn-primary:hover { background: linear-gradient(45deg,#1cc88a,#4e73df); transform: translateY(-2px); }
a { color: #4e73df; text-decoration: none; }
a:hover { color: #1cc88a; text-decoration: underline; }
.mb-3 { margin-bottom: 1.2rem !important; }
.alert { border-radius: 15px; }
</style>
</head>
<body>

<div class="card">
    <h3>agement Login</h3>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) echo "<div>$error</div>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Enter username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Enter password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-3">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
        <div class="text-center">
            <a href="register.php">Don't have an account? Register</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
