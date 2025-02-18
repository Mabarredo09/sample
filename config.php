<?php
// config.php - Database configuration file

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Set your password if needed
define('DB_NAME', 'LendingSystem');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper data handling
$conn->set_charset("utf8mb4");

// Utility functions
function cleanInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to log changes to audit trail
function logChange($conn, $table, $recordID, $action, $userID, $oldValues = null, $newValues = null)
{
    $stmt = $conn->prepare("INSERT INTO AuditLogs (TableName, RecordID, Action, ChangedBy, OldValues, NewValues) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $table, $recordID, $action, $userID, $oldValues, $newValues);
    $stmt->execute();
    $stmt->close();
}

// Start session
session_start();

// Check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Check if user has admin role
function isAdmin()
{
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
}

// Redirect if not logged in
function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Format money values
function formatMoney($amount)
{
    return '$' . number_format($amount, 2);
}

// Calculate loan payment
function calculateLoanPayment($principal, $rate, $term)
{
    $monthlyRate = $rate / 100 / 12;
    $payment = $principal * $monthlyRate * pow(1 + $monthlyRate, $term) / (pow(1 + $monthlyRate, $term) - 1);
    return $payment;
}
?>