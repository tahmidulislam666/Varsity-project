<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "jnucafe_jnucafeteria");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all menu items from the database
$menu_query = "SELECT * FROM menus";
$menu_result = $conn->query($menu_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Cafe JnU - Menu</title>
    <style>
        /* Modal Styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%;
            background-color: rgba(0,0,0,0.4); 
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Center the content inside the modal */
        .modal-content input, .modal-content textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        /* Feedback Button Styling */
.feedback-btn {
    background-color: #069C54;
    color: white;
    padding: 10px 10px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    text-align: center;
    display: inline-block;
}

/* Hover effect */
.feedback-btn:hover {
    background-color: #0056b3; /* Darker blue */
    transform: scale(1.05); /* Slightly enlarge the button */
}

/* Focus effect */
.feedback-btn:focus {
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.7); /* Light blue glow when focused */
}

/* Disabled button styling */
.feedback-btn:disabled {
    background-color: #cccccc; /* Gray color when disabled */
    cursor: not-allowed;
}
.read-more-btn {
    background: none;
    color: #007bff;
    border: none;
    cursor: pointer;
    font-size: 14px;
    text-decoration: underline;
}

.read-more-btn:hover {
    color: #0056b3;
}


    </style>
</head>
<body>
<!--========== HEADER ==========-->
<header class="l-header" id="header">
    <nav class="nav bd-container">
        <div class="nav__menu" id="nav-menu">
            <ul class="nav__list">
                <li class="nav__item"><a href="index.html" class="nav__link active-link">Home</a></li>
                <li class="nav__item"><a href="assets/history.html" class="nav__link">About</a></li>
                <li class="nav__item"><a href="hours.html" class="nav__link">Operating Hour</a></li>
                <li class="nav__item"><a href="menu.php" class="nav__link">Menu</a></li>
                <li class="nav__item"><a href="admin_login.php" class="nav__link">Admin Login</a></li>
                <li><i class='bx bx-moon change-theme' id="theme-button"></i></li>
            </ul>
        </div>
        <div class="nav__toggle" id="nav-toggle">
            <i class='bx bx-menu'></i>
        </div>
    </nav>
</header>

<!--========== MAIN CONTENT ==========-->
<main class="l-main">
    <section class="menu section bd-container" id="menu">
        <h1 class="section-title">Our Menu</h1>
        <div class="menu__container bd-grid">
            <?php
            // Display menu items dynamically
            if ($menu_result->num_rows > 0) {
                while ($menu = $menu_result->fetch_assoc()) {
                    echo "<div class='menu__content'>";
                    echo "<img src='" . htmlspecialchars($menu['image_url']) . "' alt='Menu Item' class='menu__img'>";
                    echo "<h3 class='menu__name'>" . htmlspecialchars($menu['dish_name']) . "</h3>";
                    echo "<span class='menu__preci'>" . htmlspecialchars($menu['price']) . " Taka</span>";

                    // Display Approved Feedback
                    $menu_id = $menu['id'];
                    $feedback_query = "SELECT * FROM feedback WHERE menu_id = $menu_id AND approved = TRUE";
                    $feedback_result = $conn->query($feedback_query);

                    echo "<div class='feedback-section'>";
                    echo "<h4>Feedback:</h4>";
                    if ($feedback_result->num_rows > 0) {
                        while ($feedback = $feedback_result->fetch_assoc()) {
                            $full_comment = htmlspecialchars($feedback['comment']);
                            $short_comment = substr($full_comment, 0, 20); // Show first 20 characters
                    
                            echo "<p><strong>" . htmlspecialchars($feedback['name']) . ":</strong> ";
                            echo "<span class='short-feedback'>" . $short_comment . (strlen($full_comment) > 100 ? "..." : "") . "</span>";
                    
                            // Add a "Read More" button if the feedback is long
                            if (strlen($full_comment) > 20) {
                                echo "<button class='read-more-btn' onclick='openFeedbackModal2(\"" . addslashes($full_comment) . "\")'>Read More</button>";
                            }
                            echo "</p>";
                        }
                    } else {
                        echo "<p>No feedback available for this item.</p>";
                    }
                    echo "</div>";
                    // Button to open feedback form
                    echo "<button class='feedback-btn' onclick='openFeedbackForm(" . $menu['id'] . ")'>Give Feedback</button>";

                    echo "</div>";
                }
            } else {
                echo "<p>No menu items available at the moment. Please check back later.</p>";
            }
            ?>
        </div>
    </section>
</main>

<!-- Feedback Modal -->
<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeFeedbackForm()">&times;</span>
        <h3>Give Your Feedback</h3>
        <form action="submit_feedback.php" method="POST" id="feedbackForm">
            <input type="hidden" name="menu_id" id="menu_id">
            <label for="name">Your Name:</label><br>
            <input type="text" name="name" required><br><br>
            <label for="email">Your Email:</label><br>
            <input type="email" name="email"><br><br>
            <label for="comment">Your Feedback:</label><br>
            <textarea name="comment" rows="4" required></textarea><br><br>
            <button type="submit" color=green>Submit Feedback</button>
        </form>
    </div>
</div>

<!--========== FOOTER ==========-->
<footer class="footer section bd-container">
            <div class="footer__container bd-grid">
                <div class="footer__content">
                    <a href="#" class="footer__logo">JnU Cafeteria</a>
                    <div>
                        <a href="#" class="footer__social"><i class='bx bxl-facebook'></i></a>
                        <a href="#" class="footer__social"><i class='bx bxl-instagram'></i></a>
                        <a href="#" class="footer__social"><i class='bx bxl-twitter'></i></a>
                    </div>
                </div>

                <div class="footer__content">
                    <h3 class="footer__title">Services</h3>
                    <ul>
                        <li><a href="#services" class="footer__link">Parcel</a></li>
                        <li><a href="#services" class="footer__link">Pricing</a></li>
                    </ul>
                </div>

                <div class="footer__content">
                    <h3 class="footer__title">Information</h3>
                    <ul>
                        <li><a href="#" class="footer__link">Event</a></li>
                        <li><a href="#" class="footer__link">Contact us</a></li>
                        <li><a href="#" class="footer__link">Privacy policy</a></li>
                        <li><a href="#" class="footer__link">Terms of services</a></li>
                    </ul>
                </div>

                <div class="footer__content">
                    <h3 class="footer__title">Address</h3>
                    <ul>
                        <a href="https://www.google.com/maps/place/Jagannath+University+Cafetaria/@23.7090013,90.4109191,20.76z/data=!4m6!3m5!1s0x3755b9073281ec5d:0x8816f9e2bcfe08c0!8m2!3d23.7090744!4d90.4107672!16s%2Fg%2F1hm60rbsb?authuser=0&entry=ttu&g_ep=EgoyMDI0MDkwMi4xIKXMDSoASAFQAw%3D%3D" target="_blank">
                        <li>অবকাশ ভবন, Shankari Bazar Rd, Dhaka 1100</li>
                        <li>Located in: Jagannath University</li>
                        <li>029534255</li>
                    </a>   
                    </ul>
                </div>
            </div>

            <p class="footer__copy">All right reserved by dream weavers. 2024 &#169</p>
        </footer>

<script src="assets/js/main.js"></script>
<script>
// Open feedback form modal
function openFeedbackForm(menu_id) {
    document.getElementById("feedbackModal").style.display = "block";
    document.getElementById("menu_id").value = menu_id;
}

// Close the feedback form modal
function closeFeedbackForm() {
    document.getElementById("feedbackModal").style.display = "none";
}

// Close modal if the user clicks outside of the modal content
window.onclick = function(event) {
    if (event.target == document.getElementById("feedbackModal")) {
        closeFeedbackForm();
    }
}
</script>
<script>
// Function to open the feedback modal with full feedback text
function openFeedbackModal2(feedbackText) {
    // Set the full feedback text inside the modal
    document.getElementById("feedbackText").innerText = feedbackText;

    // Display the modal
    document.getElementById("feedbackModal2").style.display = "block";
}

// Function to close the feedback modal
function closeFeedbackModal2() {
    document.getElementById("feedbackModal2").style.display = "none";
}

// Close the modal if the user clicks outside of the modal content
window.onclick = function(event) {
    if (event.target == document.getElementById("feedbackModal2")) {
        closeFeedbackModal2();
    }
}
</script>


<!-- Modal for Feedback -->
<div id="feedbackModal2" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeFeedbackModal2()">&times;</span>
        <h3>Full Feedback</h3>
        <p id="feedbackText"></p>
    </div>
</div>


</body>
</html>

<?php
$conn->close();
?>
