<?php
// Set the content type to JSON for API-like responses
header('Content-Type: application/json');

// --- CONFIGURATION ---
$db_host = 'localhost';
$db_name = 'spsfsymposium_db';
$db_user = 'spsfsymposium_user';
$db_pass = 'Symposium@1415';
$mail_from_name = 'SPSF Symposium 2025';
$mail_from = 'no-reply@yourdomain.com';

// --- SCRIPT START ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$full_name = trim($input['fullName'] ?? '');
$email = trim($input['email'] ?? '');
$affiliation = trim($input['affiliation'] ?? '');
$contact_number = trim($input['contactNumber'] ?? '');
$category = trim($input['category'] ?? '');
$special_requirements = trim($input['specialRequirements'] ?? '');

// --- VALIDATION ---
$errors = [];
if (empty($full_name)) $errors[] = 'Full Name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
if (empty($affiliation)) $errors[] = 'Affiliation is required.';
if (empty($contact_number)) $errors[] = 'Contact Number is required.';
if (empty($category)) $errors[] = 'Category is required.';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
    exit();
}

// --- DATABASE INTERACTION ---
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Check for duplicate email
    $stmt = $pdo->prepare("SELECT id FROM registrations WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'A registration with this email already exists.']);
        exit();
    }

    // 2. Insert new registration
    $sql = "INSERT INTO registrations (full_name, email, affiliation, contact_number, category, special_requirements) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$full_name, $email, $affiliation, $contact_number, $category, $special_requirements]);

    // --- NEW LOGIC TO GENERATE AND SAVE reg_id ---
    // Get the numeric ID of the user we just inserted
    $last_id = $pdo->lastInsertId();
    
    // Create the formatted ID (e.g., 'SPSF' + '0042' -> 'SPSF0042')
    $reg_id = 'SPSF' . str_pad($last_id, 4, '0', STR_PAD_LEFT);
    
    // Update the new row with the generated reg_id
    $update_sql = "UPDATE registrations SET reg_id = ? WHERE id = ?";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([$reg_id, $last_id]);
    // --- END OF NEW LOGIC ---

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    exit();
}

// --- SEND CONFIRMATION EMAIL ---
// MODIFIED: Updated the email content to include the new reg_id
$subject = "Registration Confirmed: SPSF Symposium 2025 (ID: {$reg_id})";
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: $mail_from_name <$mail_from>" . "\r\n";

$message = "
<html>
<body>
    <h2>Registration Confirmed for SPSF Symposium 2025</h2>
    <p>Dear {$full_name},</p>
    <p>Thank you for registering. Your registration has been received successfully.</p>
    <p style='font-size: 1.2em;'><b>Your Registration ID is: {$reg_id}</b></p>
    <hr>
    <p><strong>Event Details:</strong></p>
    <ul>
        <li><strong>Date:</strong> 15th November 2025</li>
        <li><strong>Venue:</strong> Auditorium, Campus 11, KIIT Deemed to be University, Bhubaneswar</li>
    </ul>
    <p>We look forward to welcoming you.</p>
    <p>Sincerely,<br>The Organizing Committee</p>
</body>
</html>";

if (mail($email, $subject, $message, $headers)) {
    http_response_code(200);
    // MODIFIED: The success message now includes the Registration ID for you to see
    echo json_encode(['status' => 'success', 'message' => "Registration successful! Your Registration ID is {$reg_id}."]);
} else {
    http_response_code(200);
    // MODIFIED: This message also includes the ID
    echo json_encode(['status' => 'success', 'message' => "Registration successful (ID: {$reg_id})"]);
}

?>