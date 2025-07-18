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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $menu_result = $conn->query("SELECT * FROM menus WHERE id = $id");
    $menu = $menu_result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $dish_name = trim($_POST['dish_name']);
    $price = trim($_POST['price']);
    $image_url = trim($_POST['image_url']);

    if (!empty($dish_name) && !empty($price) && !empty($image_url)) {
        $sql = "UPDATE menus SET dish_name = ?, price = ?, image_url = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $dish_name, $price, $image_url, $id);
        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error updating menu item: " . $conn->error;
        }
    } else {
        echo "All fields are required.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu Item</title>
    <style>
        /* General Reset */
body, h1, h2, p, form, input, button {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body Styling */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f9f9f9;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    padding: 20px;
}

/* Form Container */
form {
    background-color: #ffffff;
    padding: 20px 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
}

/* Form Heading */
form h1 {
    font-size: 24px;
    color: #007bff;
    text-align: center;
    margin-bottom: 20px;
}

/* Form Labels */
form label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333333;
}

/* Form Inputs */
form input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Input Focus State */
form input:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    outline: none;
}

/* Submit Button */
form button {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Button Hover State */
form button:hover {
    background-color: #0056b3;
}

/* Responsive Styling */
@media (max-width: 500px) {
    form {
        padding: 15px 20px;
    }

    form h1 {
        font-size: 20px;
    }
}

    </style>
</head>
<body>
    <form action="edit_menu.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($menu['id']); ?>">
        <label for="dish_name">Dish Name:</label><br>
        <input type="text" name="dish_name" value="<?php echo htmlspecialchars($menu['dish_name']); ?>" required><br>
        <label for="price">Price:</label><br>
        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($menu['price']); ?>" required><br>
        <label for="image_url">Image URL:</label><br>
        <input type="text" name="image_url" value="<?php echo htmlspecialchars($menu['image_url']); ?>" required><br>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
