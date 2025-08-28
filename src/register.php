<?php
// register.php
session_start();
// require_once ('auth.php'); 
require_once ('../db/connection.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $role      = $_POST['role'];
    $confirm   = $_POST['confirm-password'];

   
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match!";
    }

    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        $errors[] = "Username or email already exists!";
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password,first_name, last_name, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([ $username, $email, $hashed,$firstname, $lastname,$role,]);
        $_SESSION['success'] = "Registration successful! You can now login.";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - School Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { margin:0; font-family:'Poppins', sans-serif; height:100vh; display:flex; justify-content:center; align-items:center; background: linear-gradient(120deg,#4e73df,#1cc88a);}
.card { width:100%; max-width:450px; max-height:95vh; padding:2.5rem; border-radius:20px; background:rgba(255,255,255,0.95); box-shadow:0 20px 50px rgba(0,0,0,0.15); overflow-y:auto;}
.card::-webkit-scrollbar { width:6px; }
.card::-webkit-scrollbar-thumb { background-color: rgba(78,115,223,0.5); border-radius:3px; }
h3 { text-align:center; color:#4e73df; font-weight:700; margin-bottom:2rem; }
.form-control, .form-select { border-radius:50px; padding:0.85rem 1rem; border:1px solid #ddd; transition:0.3s; }
.form-control:focus, .form-select:focus { border-color:#4e73df; box-shadow:0 0 10px rgba(78,115,223,0.25);}
.btn-primary { border-radius:50px; padding:0.85rem; font-weight:600; background:linear-gradient(135deg,#4e73df,#1cc88a); border:none; transition:0.3s; }
.btn-primary:hover { background:linear-gradient(135deg,#1cc88a,#4e73df); transform:translateY(-2px); }
a { color:#4e73df; text-decoration:none; } a:hover { color:#1cc88a; text-decoration:underline; }
.mb-3 { margin-bottom:1.2rem !important; }
</style>
</head>
<body>

<div class="card">
    <h3>School Management Register</h3>

    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $error) echo "<div>$error</div>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="firstname" class="form-label">First Name</label>
            <input type="text" class="form-control" name="firstname" placeholder="Enter your first name" required>
        </div>
        <div class="mb-3">
            <label for="lastname" class="form-label">Last Name</label>
            <input type="text" class="form-control" name="lastname" placeholder="Enter your last name" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Choose a username" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="">Select Role</option>
                <option>Student</option>
                <option>Teacher</option>
                <option>Admin</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Create a password" required>
        </div>
        <div class="mb-3">
            <label for="confirm-password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="confirm-password" placeholder="Confirm password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            <i class="fas fa-user-plus"></i> Register
        </button>
        <div class="text-center">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
