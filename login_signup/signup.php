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
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $phone    = $_POST['phone'];

    $check = $conn->prepare("SELECT * FROM Users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "Email already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO Users (name, email, password, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $phone);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user'] = [
                'user_id' => $user_id,
                'name' => $name,
                'email' => $email
            ];

            if ($user_id == 1) {
                header("Location: ../admin/dashboard.php");
                exit;
            } else {
                header("Location: ../main/index.php");
                exit;
            }
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>
