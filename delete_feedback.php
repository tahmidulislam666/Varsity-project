<?php
session_start();

// Check if the user is an admin, if not, redirect to the login page
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "jnucafe_tahmid", "tahmid123@", "jnucafe_jnucafeteria");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the ID is provided via GET request
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the SQL query to delete the feedback
    $sql = "DELETE FROM feedback WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id); // "i" denotes an integer parameter

    if ($stmt->execute()) {
        // Redirect to the admin dashboard after successful deletion
        header("Location: admin_dashboard.php?message=Feedback+deleted+successfully");
        exit();
    } else {
        // Show error message if deletion fails
        echo "Error deleting feedback: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "No feedback ID specified.";
}

$conn->close();
?>
