<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "Connected to MySQL successfully\n";
    
    // Check if cosmiclib database exists
    $result = $db->query("SHOW DATABASES LIKE 'cosmiclib'");
    if ($result->rowCount() > 0) {
        echo "Database 'cosmiclib' exists\n";
    } else {
        echo "Database 'cosmiclib' does NOT exist\n";
        echo "Creating database...\n";
        $db->exec("CREATE DATABASE cosmiclib CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "Database created\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}