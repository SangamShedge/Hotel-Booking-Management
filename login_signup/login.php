<?php
session_start();

$host = 'localhost';
$db = 'hotelbooking';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user'] = $user;

        if ($user['user_id'] == 1) {
            header("Location: ../admin/dashboard.php");
            exit;
        } else {
            header("Location: ../main/index.php");
            exit;
        }
    } else {
        echo "Invalid email or password.";
    }
}
?>