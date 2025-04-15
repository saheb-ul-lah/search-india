<?php
// justdial-public/includes/db.php

define('DB_HOST', 'localhost'); // Replace with your database host
define('DB_USER', 'root');      // Replace with your database username
define('DB_PASS', '');          // Replace with your database password
define('DB_NAME', 'justdial_admin');  // Replace with your database name (assuming 'justdial' based on db.sql)

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e){
    // Display error message on connection failure
    // In production, log this error instead of displaying it
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Store the connection in a global variable or return it
// For simplicity here, functions in functions.php will include this file
// and use the $pdo variable.
?>