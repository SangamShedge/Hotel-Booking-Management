<?php
session_start();

// Only allow admin (user_id = 1)
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $rating = $_POST['rating'];
    $password = $_POST['password']; // ⚠ Plain Text Password

    // Upload image
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $image = "hotel_img.jpg"; // fallback
    if (isset($_FILES["main_image"]) && $_FILES["main_image"]["error"] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES["main_image"]["name"]);
        $targetFile = $uploadDir . $imageName;

        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $targetFile)) {
                $image = $imageName;
            } else {
                echo "<script>alert('Image upload failed.');</script>";
            }
        } else {
            echo "<script>alert('Only JPG, JPEG, PNG, GIF, WEBP files allowed.');</script>";
        }
    }

    $sql = "INSERT INTO Hotels (name, location, description, rating, main_image_url, password) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdss", $name, $location, $description, $rating, $image, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Hotel added successfully'); window.location.href='hotels.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen p-10">
    <div class="max-w-xl mx-auto bg-gray-800 p-8 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-400">Add Hotel</h1>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input name="name" required placeholder="Hotel Name"
                class="w-full p-2 rounded bg-gray-700 border border-gray-600">
            <input name="location" required placeholder="Location"
                class="w-full p-2 rounded bg-gray-700 border border-gray-600">
            <textarea name="description" placeholder="Description"
                class="w-full p-2 rounded bg-gray-700 border border-gray-600"></textarea>
            <input name="rating" type="number" step="0.1" min="0" max="5" placeholder="Rating"
                class="w-full p-2 rounded bg-gray-700 border border-gray-600">

            <input type="file" name="main_image" accept="image/*"
                class="w-full p-2 rounded bg-gray-700 border border-gray-600">

            <input name="password" type="text" required placeholder="Hotel Credential"
                class="w-full p-2 rounded bg-gray-700 border border-gray-600">

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">Add Hotel</button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
