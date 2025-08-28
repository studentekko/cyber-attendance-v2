<?php
require_once('../db/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchTerm = trim($_POST['search']);
    $searchTerm = "%" . $searchTerm . "%";

    $stmt = $pdo->prepare("SELECT * FROM students WHERE name LIKE ? OR student_id LIKE ?");
    $stmt->execute([$searchTerm, $searchTerm]);
    $students = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE name LIKE ? OR teacher_id LIKE ?");
    $stmt->execute([$searchTerm, $searchTerm]);
    $teachers = $stmt->fetchAll();

    // Return results as JSON
    header('Content-Type: application/json');
    echo json_encode(['students' => $students, 'teachers' => $teachers]);
    exit();
}
?>

