<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: application/json');
// --- DB CONFIG ---
$db_host = 'localhost';
$db_name = 'spsfsymposium_db';
$db_user = 'spsfsymposium_user';
$db_pass = 'Symposium@1415';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    // FIXED: Added 'reg_id' to the SELECT statement
    $stmt = $pdo->query("SELECT id, reg_id, full_name, email, affiliation, contact_number, category, special_requirements, registration_date FROM registrations ORDER BY id DESC");
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($registrations);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>