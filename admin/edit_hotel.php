<?php
session_start();

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.html");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get hotel ID
if (!isset($_GET['id'])) {
    die("Hotel ID not provided.");
}
$hotel_id = intval($_GET['id']);

// Fetch current hotel data
$hotel = $conn->query("SELECT * FROM Hotels WHERE hotel_id = $hotel_id")->fetch_assoc();
if (!$hotel) {
    die("Hotel not found.");
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $rating = $_POST['rating'];

    // Handle optional image upload
    $image = $hotel['main_image_url']; // keep old if not changed
    if (isset($_FILES["main_image"]) && $_FILES["main_image"]["error"] === UPLOAD_ERR_OK) {
        $uploadDir = "../uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

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
            echo "<script>alert('Only image files are allowed.');</script>";
        }
    }

    // Update DB
    $stmt = $conn->prepare("UPDATE Hotels SET name=?, location=?, description=?, rating=?, main_image_url=? WHERE hotel_id=?");
    $stmt->bind_param("sssdsi", $name, $location, $description, $rating, $image, $hotel_id);

    if ($stmt->execute()) {
        echo "<script>alert('Hotel updated successfully.'); window.location.href='hotels.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen p-10">
    <div class="max-w-xl mx-auto bg-gray-800 p-8 rounded shadow-lg">
        <h1 class="text-3xl font-bold text-blue-400 mb-6 text-center">Edit Hotel</h1>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="name" value="<?php echo htmlspecialchars($hotel['name']); ?>" required
                class="w-full p-2 rounded bg-gray-700 border border-gray-600" placeholder="Hotel Name">

            <input type="text" name="location" value="<?php echo htmlspecialchars($hotel['location']); ?>" required
                class="w-full p-2 rounded bg-gray-700 border border-gray-600" placeholder="Location">

            <textarea name="description" class="w-full p-2 rounded bg-gray-700 border border-gray-600"
                placeholder="Description"><?php echo htmlspecialchars($hotel['description']); ?></textarea>

            <input type="number" step="0.1" min="0" max="5" name="rating"
                value="<?php echo htmlspecialchars($hotel['rating']); ?>"
                class="w-full p-2 rounded bg-gray-700 border border-gray-600" placeholder="Rating">

            <div>
                <p class="mb-2">Current Image:</p>
                <img src="../uploads/<?php echo htmlspecialchars($hotel['main_image_url']); ?>" class="w-32 h-32 object-cover mb-4 rounded">
                <input type="file" name="main_image" accept="image/*"
                    class="w-full p-2 rounded bg-gray-700 border border-gray-600">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">Update Hotel</button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
