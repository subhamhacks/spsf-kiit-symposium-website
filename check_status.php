<?php
header('Content-Type: application/json');

// --- CONFIGURATION ---
// Make sure these credentials match your other files
$db_host = 'localhost';
$db_name = 'spsfsymposium_db';
$db_user = 'spsfsymposium_user';
$db_pass = 'Symposium@1415';

// Define the registration limit
$limit = 250;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to count the total number of rows in the registrations table
    $stmt = $pdo->query("SELECT COUNT(*) FROM registrations");
    $current_registrations = $stmt->fetchColumn();

    // Send back the current count and the limit
    echo json_encode([
        'currentRegistrations' => (int)$current_registrations,
        'limit' => $limit
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error.']);
}

?>