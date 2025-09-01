<?php
try {
    // Database connection details from your InfinityFree panel
    $host = 'yamabiko.proxy.rlwy.net';
    $dbname = 'cyber';
    $user = 'root';
    $password = 'GMnExWwRKkXhUcodjLInPtoQKwdkkyNN';
    $port = 18491;

    // Connect to the database
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $password);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ...existing code...
} catch (PDOException $e) {
    throw new Exception("Connection failed: " . $e->getMessage());
}
?>