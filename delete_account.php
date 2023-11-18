<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Delete account and associated data from the database
$deleteAccountQuery = "DELETE FROM dealers WHERE id = $dealer_id";

if ($conn->query($deleteAccountQuery) === TRUE) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("Location: login.php");
    exit();
} else {
    echo "Error deleting account: " . $conn->error;
}
?>
