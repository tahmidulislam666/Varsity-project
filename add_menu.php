<?php
session_start();

// Ensure the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "jnucafe_tahmid", "tahmid123@", "jnucafe_jnucafeteria");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dish_name = trim($_POST['dish_name']);
    $price = trim($_POST['price']);
    $image_url = trim($_POST['image_url']); // URL input

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = "uploads/"; // Directory to save uploaded images
        $image_name = basename($_FILES['image']['name']);
        $target_path = $upload_dir . $image_name;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_url = $target_path; // Use the uploaded file path as the image URL
        } else {
            echo "Error uploading file.";
        }
    }

    // Validate inputs
    if (!empty($dish_name) && !empty($price) && !empty($image_url)) {
        // Insert the menu item into the database
        $sql = "INSERT INTO menus (dish_name, price, image_url) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $dish_name, $price, $image_url);

        if ($stmt->execute()) {
            echo "Menu item added successfully.";
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "All fields, including an image, are required.";
    }
}

$conn->close();
?>
