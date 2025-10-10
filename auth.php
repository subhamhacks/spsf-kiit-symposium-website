<?php
session_start();

// --- CONFIGURATION ---
$admin_password = 'SymposiumSPSF@123459876'; // Change this to a strong, unique password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password']) && $_POST['password'] === $admin_password) {
        // Password is correct, set session variable
        $_SESSION['is_admin'] = true;
        header('Location: admin.php'); // Redirect to the admin panel
        exit();
    } else {
        // Incorrect password
        header('Location: login.html?error=1');
        exit();
    }
}
?>