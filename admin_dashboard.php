<?php
session_start();

// Ensure the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "jnucafe_jnucafeteria");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all menu items
$menu_result = $conn->query("SELECT * FROM menus");

// Fetch all feedback items
$feedback_result = $conn->query("SELECT * FROM feedback");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <style>
        /* General Reset */
body, h1, h2, h3, p, ul, li, table, th, td {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body Styling */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f9f9f9;
    color: #333;
    line-height: 1.6;
    margin: 0;
    padding: 0;
}

/* Header Styling */
header {
    background: #007bff;
    color: #fff;
    padding: 10px 20px;
    text-align: center;
}

header nav ul {
    list-style: none;
    display: flex;
    justify-content: center;
    padding: 10px 0;
}

header nav ul li {
    margin: 0 15px;
}

header nav ul li a {
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    padding: 5px 10px;
    transition: background-color 0.3s ease;
}

header nav ul li a:hover {
    background-color: #0056b3;
    border-radius: 5px;
}

/* Main Section */
main {
    padding: 20px;
    max-width: 1200px;
    margin: 20px auto;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Section Titles */
h1, h2 {
    color: #007bff;
    margin-bottom: 20px;
    text-align: center;
}

/* Tables */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    text-align: left;
    background-color: #fff;
    overflow-x: auto;
}

table th, table td {
    padding: 10px;
    border: 1px solid #ddd;
}

table th {
    background-color: #f3f3f3;
    color: #333;
    font-weight: bold;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tr:hover {
    background-color: #f1f1f1;
}

/* Buttons */
button, a {
    display: inline-block;
    padding: 10px 15px;
    background-color: #007bff;
    color: #fff;
    text-decoration: none;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    transition: background-color 0.3s ease;
    cursor: pointer;
}

button:hover, a:hover {
    background-color: #0056b3;
}

button:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
}

/* Form Styling */
form {
    margin-bottom: 20px;
    padding: 20px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
}

form label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

form input {
    width: 98%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

form button {
    width: 100%;
    background-color: #007bff;
    border: none;
    color: white;
    padding: 10px;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

form button:hover {
    background-color: #0056b3;
}

/* Footer */
footer {
    text-align: center;
    padding: 10px;
    background-color: #007bff;
    color: white;
    margin-top: 20px;
}

footer p {
    margin: 0;
}

    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Welcome to the Admin Dashboard, <?php echo $_SESSION['admin']; ?>!</h1>

        <h2>Menu Management</h2>
        <table>
            <tr>
                <th>Dish Name</th>
                <th>Price</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
            <?php while ($menu = $menu_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($menu['dish_name']); ?></td>
                    <td><?php echo htmlspecialchars($menu['price']); ?> Taka</td>
                    <td><img src="<?php echo $menu['image_url']; ?>" alt="Dish Image" width="50"></td>
                    <td>
                        <a href="edit_menu.php?id=<?php echo $menu['id']; ?>">Edit</a> |
                        
                        <a href="delete_menu.php?id=<?php echo $menu['id']; ?>" 
                        onclick="return confirm('Are you sure you want to delete this menu item?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <h2>Add New Menu Item</h2>
        <form action="add_menu.php" method="POST" enctype="multipart/form-data">
    <label for="dish_name">Dish Name:</label><br>
    <input type="text" name="dish_name" required><br>
    <label for="price">Price:</label><br>
    <input type="number" name="price" required><br>
    <label for="image">Upload Image:</label><br>
    <input type="file" name="image" accept="image/*"><br>
    <button type="submit">Add Menu Item</button>
</form>


        <h2>Feedback Management</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Comment</th>
                <th>Action</th>
            </tr>

            <?php while ($feedback = $feedback_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($feedback['name']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['comment']); ?></td>
                    <td>
                <?php if ($feedback['approved'] == 0) { ?>
                    <a href="approve_feedback.php?id=<?php echo $feedback['id']; ?>">Approve</a> | 
                   
                <?php } ?>
                <a href="delete_feedback.php?id=<?php echo $feedback['id']; ?>" 
                   onclick="return confirm('Are you sure you want to delete this feedback?');">Delete</a>
            </td>
                </tr>
            <?php } ?>
        </table>

    </main>

    <footer>
        <p>&copy; 2024 JnU Cafeteria</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
