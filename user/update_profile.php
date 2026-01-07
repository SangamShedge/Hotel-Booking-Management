<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];

$profile_picture = $_FILES['profile_picture']['name'];
$upload_path = '';

if (!empty($profile_picture)) {
    $upload_path = "../uploads/" . basename($profile_picture);
    move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path);
    $sql = "UPDATE Users SET name=?, email=?, phone=?, profile_picture=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $phone, $upload_path, $user_id);
} else {
    $sql = "UPDATE Users SET name=?, email=?, phone=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
}

if ($stmt->execute()) {
    header("Location: dashboard.php");
} else {
    echo "Update failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
