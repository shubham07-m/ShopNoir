<?php
// Example Database Configuration
// Rename or copy this file to 'database.php' and insert your real database credentials.
// 'database.php' is ignored by Git to prevent leaking credentials.

$host     = 'localhost';
$dbname   = 'your_database_name_here';
$username = 'your_username_here';
$password = 'your_password_here';

$pdo = null;
$db_error = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $db_error = 'Database connection failed. Please make sure the database is set up.';
}
