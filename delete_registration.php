<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: application/json');
// --- DB CONFIG - MAKE SURE THESE ARE CORRECT ---
$db_host = 'localhost';
$db_name = 'spsfsymposium_db';
$db_user = 'spsfsymposium_user';
$db_pass = 'Symposium@1415';

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID is required.']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("DELETE FROM registrations WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Registration deleted.']);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Registration not found.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
?>