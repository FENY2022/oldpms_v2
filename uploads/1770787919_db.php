<?php 

// Database configuration
$host = 'localhost';
$dbname = 'v2_oldpms'; // Change to your database name
$username = 'root'; // Change to your database username
$password = '';     // Change to your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$contact_msg = '';

// $_COOKIE = array_map('htmlspecialchars', $_COOKIE);
// $_ENV = array_map('htmlspecialchars_vvp', $_ENV);
?>