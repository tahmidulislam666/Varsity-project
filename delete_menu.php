<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "jnucafe_tahmid", "tahmid123@", "jnucafe_jnucafeteria");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if `id` is passed in the query string
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Confirm the deletion
    $sql = "DELETE FROM menus WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?message=Menu+item+deleted+successfully");
        exit();
    } else {
        echo "Error deleting menu item: " . $conn->error;
    }
}

$conn->close();
?>
