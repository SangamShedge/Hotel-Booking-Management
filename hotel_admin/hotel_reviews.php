<?php
session_start();

if (!isset($_SESSION['hotel_id'])) {
    header("Location: login.html");
    exit;
}

$hotel_id = $_SESSION['hotel_id'];

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch hotel name
$hotel_name = '';
$hotel_result = $conn->query("SELECT name FROM Hotels WHERE hotel_id = $hotel_id");
if ($hotel_result && $hotel_result->num_rows > 0) {
    $hotel_name = $hotel_result->fetch_assoc()['name'];
}

// Fetch reviews for this hotel
$sql = "
    SELECT r.rating, r.comment, r.created_at, u.name AS user_name
    FROM Reviews r
    JOIN Users u ON r.user_id = u.user_id
    WHERE r.hotel_id = ?
    ORDER BY r.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Reviews</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-900 text-white min-h-screen font-sans">

<!-- Sidebar -->
<aside class="w-64 bg-gray-800 p-6 fixed h-full">
    <div class="flex items-center space-x-3 mb-6">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Meetup_Logo.png" alt="Logo" class="w-10 h-10">
        <span class="text-2xl font-bold text-blue-600">My Stay</span>
    </div>
    <nav class="space-y-3">
        <a href="../main/index.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Home</a>
        <a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Profile</a>
        <a href="manage_rooms.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Manage Rooms</a>
        <a href="view_bookings.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">View Bookings</a>
        <a href="hotel_reviews.php" class="block px-4 py-2 rounded bg-blue-700">Reviews</a>
        <form action="logout.php" method="POST">
            <button type="submit" class="w-full mt-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                Logout
            </button>
        </form>
    </nav>
</aside>

<!-- Main Content -->
<main class="ml-64 flex-1 p-10">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-400">Reviews for <?= htmlspecialchars($hotel_name) ?></h1>

        <?php if ($result->num_rows > 0): ?>
            <div class="space-y-6">
                <?php while ($review = $result->fetch_assoc()): ?>
                    <div class="bg-gray-800 p-6 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-2">
                            <h2 class="text-lg font-semibold text-blue-300"><?= htmlspecialchars($review['user_name']) ?></h2>
                            <span class="text-yellow-400 font-bold"><?= str_repeat("★", $review['rating']) ?><?= str_repeat("☆", 5 - $review['rating']) ?></span>
                        </div>
                        <p class="text-gray-200"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        <p class="text-sm text-gray-400 mt-2">Posted on <?= date("F j, Y, g:i a", strtotime($review['created_at'])) ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-400">No reviews yet for your hotel.</p>
        <?php endif; ?>
    </div>
</main>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
