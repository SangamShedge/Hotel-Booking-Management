<?php
session_start();

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete
if (isset($_GET['delete'])) {
    $review_id = intval($_GET['delete']);
    $conn->query("DELETE FROM Reviews WHERE review_id = $review_id");
    header("Location: reviews.php");
    exit;
}

// Handle edit submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_review_id'])) {
    $review_id = intval($_POST['edit_review_id']);
    $rating = intval($_POST['edit_rating']);
    $comment = $conn->real_escape_string($_POST['edit_comment']);
    
    $conn->query("UPDATE Reviews SET rating = $rating, comment = '$comment' WHERE review_id = $review_id");
    header("Location: reviews.php");
    exit;
}

// Fetch reviews
$sql = "SELECT Reviews.*, Users.name, Hotels.name AS hotel_name 
        FROM Reviews 
        JOIN Users ON Reviews.user_id = Users.user_id 
        JOIN Hotels ON Reviews.hotel_id = Hotels.hotel_id 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviews</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-900 text-white font-sans min-h-screen">

<aside class="w-64 bg-gray-800 p-6 fixed h-full">
    <div class="flex items-center space-x-3">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Meetup_Logo.png" alt="Logo" class="w-10 h-10">
        <span class="text-2xl font-bold text-blue-600">My Stay</span>
    </div>
    <nav class="space-y-3 mt-6">
        <a href="../main/index.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Home</a>
        <a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Profile</a>
        <a href="users.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Users</a>
        <a href="bookings.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Bookings</a>
        <a href="hotels.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Hotels</a>
        <a href="rooms.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Rooms</a>
        <a href="#" class="block px-4 py-2 rounded bg-blue-700">Reviews</a>
        <a href="messages.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Messages</a>
        <form action="logout.php" method="POST">
            <button type="submit" class="w-full mt-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                Logout
            </button>
        </form>
    </nav>
</aside>

<main class="ml-64 flex-1 p-10">
    <div class="max-w-7xl mx-auto bg-gray-800 p-8 rounded shadow-lg">
        <h1 class="text-3xl font-bold text-blue-400 mb-6 text-center">Hotel Reviews</h1>

        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse bg-gray-900">
                <thead>
                    <tr class="bg-gray-700 text-left">
                        <th class="p-3">Review ID</th>
                        <th class="p-3">Hotel</th>
                        <th class="p-3">User</th>
                        <th class="p-3">Rating</th>
                        <th class="p-3">Comment</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="border-t border-gray-700">
                                <td class="p-3">#<?= $row['review_id'] ?></td>
                                <td class="p-3"><?= htmlspecialchars($row['hotel_name']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="p-3 text-yellow-400 font-semibold"><?= $row['rating'] ?>/5</td>
                                <td class="p-3"><?= htmlspecialchars($row['comment']) ?></td>
                                <td class="p-3 text-sm text-gray-400"><?= date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
                                <td class="p-3">
                                    <div class="flex gap-2">
                                        <button onclick="editReview(<?= $row['review_id'] ?>, <?= $row['rating'] ?>, '<?= htmlspecialchars(addslashes($row['comment'])) ?>')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">Edit</button>
                                        <a href="?delete=<?= $row['review_id'] ?>" onclick="return confirm('Delete this review?')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-gray-400 py-6">No reviews found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Edit Popup -->
<div id="editPopup" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white text-black p-6 rounded-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Edit Review</h2>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="edit_review_id" id="edit_review_id">
            <label class="block text-sm font-semibold">Rating</label>
            <input type="number" name="edit_rating" id="edit_rating" class="w-full p-2 border rounded" min="1" max="5" required>
            <label class="block text-sm font-semibold">Comment</label>
            <textarea name="edit_comment" id="edit_comment" class="w-full p-2 border rounded" required></textarea>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('editPopup').classList.add('hidden')" class="px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function editReview(id, rating, comment) {
    document.getElementById('edit_review_id').value = id;
    document.getElementById('edit_rating').value = rating;
    document.getElementById('edit_comment').value = comment;
    document.getElementById('editPopup').classList.remove('hidden');
}
</script>

</body>
</html>

<?php $conn->close(); ?>
