<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(401);
    die('Unauthorized');
}

// --- DB CONFIG ---
$db_host = 'localhost';
$db_name = 'spsfsymposium_db';
$db_user = 'spsfsymposium_user';
$db_pass = 'Symposium@1415';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    
    // FIXED: Select 'reg_id' and remove 'registration_date'
    $stmt = $pdo->query("SELECT reg_id, full_name, email, affiliation, contact_number, category, special_requirements FROM registrations ORDER BY id ASC");
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=SPSF_Registrations.csv');
    
    $output = fopen('php://output', 'w');
    
    // FIXED: Update header row
    fputcsv($output, ['Registration ID', 'Full Name', 'Email', 'Affiliation', 'Contact Number', 'Category', 'Special Requirements']);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // FIXED: Prepend a tab character to the contact number to force Excel to treat it as a string
        $row['contact_number'] = "\t" . $row['contact_number'];
        fputcsv($output, $row);
    }
    
    fclose($output);

} catch (PDOException $e) {
    die("Database error. Could not export data.");
}
?>