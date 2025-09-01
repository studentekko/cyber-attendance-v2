<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Basic index.php file

// Display a simple message
echo "Welcome to the Cyberproject!";

?>