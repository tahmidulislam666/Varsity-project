<?php
// Start the session (if needed for user authentication)
session_start();

// Database connection
$conn = new mysqli("localhost", "jnucafe_tahmid", "tahmid123@", "jnucafe_jnucafeteria");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle feedback form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $menu_id = $_POST['menu_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $comment = trim($_POST['comment']);

    // Validate input
    if (!empty($menu_id) && !empty($name) && !empty($comment)) {
        // Prepare SQL query to insert feedback into the database
        $sql = "INSERT INTO feedback (menu_id, name, email, comment, approved) VALUES (?, ?, ?, ?, FALSE)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $menu_id, $name, $email, $comment);

        if ($stmt->execute()) {
            // Redirect to the menu page with a success message
            echo "Thank you for your feedback!";
            header("Location: menu.php"); // Redirect back to the menu page
            exit();
        } else {
            // Error message if feedback insertion fails
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "All required fields must be filled.";
    }
}

$conn->close();
?>
