<?php

// ============================================
// Database Configuration
// ============================================

// Get the database host from an environment variable.
// Default to localhost if DB_HOST is not configured.
$host = getenv('DB_HOST') ?: 'localhost';

// Get the database name from an environment variable.
// Default to integration_demo.
$dbname = getenv('DB_NAME') ?: 'integration_demo';

// Get the database username from an environment variable.
// Default to root for local development.
$username = getenv('DB_USER') ?: 'root';

// Get the database password from an environment variable.
// Default to an empty string if not configured.
$password = getenv('DB_PASSWORD') ?: '';


// ============================================
// Establish MySQL Database Connection
// ============================================

try {

    // Create a new PDO database connection.
    //
    // mysql:       Database driver
    // host:        MySQL server hostname
    // dbname:      Database name
    // charset:     Character encoding (UTF-8)
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",

        // Database username
        $username,

        // Database password
        $password,

        // PDO connection options
        [
            // Throw exceptions when database errors occur.
            // Makes it easier to handle and debug errors.
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            // Return query results as associative arrays.
            // Example: ['id' => 1, 'name' => 'Chris']
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Disable emulated prepared statements.
            // Use native MySQL prepared statements.
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // Connection established successfully.
    // The $pdo object can now be used for database queries.

} catch (PDOException $e) {

    // Set HTTP response status to 500 (Internal Server Error).
    http_response_code(500);

    // Display a generic error message.
    // Avoid exposing sensitive database details.
    die("Database connection failed.");
}