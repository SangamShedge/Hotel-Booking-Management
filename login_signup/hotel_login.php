<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "hotelbooking"; // Update this as per your DB name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$hotel_name = $_POST['name'];
$hotel_password = $_POST['password'];

// Prepare and execute query
$sql = "SELECT * FROM Hotels WHERE name = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hotel_name, $hotel_password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Login successful
    $hotel = $result->fetch_assoc();
    $_SESSION['hotel_id'] = $hotel['hotel_id'];
    $_SESSION['hotel_name'] = $hotel['name'];
    // echo "Login successful! Welcome, " . htmlspecialchars($hotel['name']) . ".";
    // Redirect to hotel admin dashboard (replace 'dashboard.php' with actual page)
    header("Location: ../hotel_admin/dashboard.php");
    exit;
} else {
    echo "Invalid hotel name or password.";
}

$stmt->close();
$conn->close();
?>
