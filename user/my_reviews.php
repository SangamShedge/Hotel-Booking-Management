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

// Handle Add Review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $hotel_id = $_POST['hotel_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO Reviews (user_id, hotel_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iids", $user_id, $hotel_id, $rating, $comment);
    $stmt->execute();
    $stmt->close();
    $_SESSION['review_success'] = "Review added successfully.";
    header("Location: my_reviews.php");
    exit;
}

// Handle Edit Review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $review_id = $_POST['review_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("UPDATE Reviews SET rating = ?, comment = ? WHERE review_id = ? AND user_id = ?");
    $stmt->bind_param("dsii", $rating, $comment, $review_id, $user_id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['review_success'] = "Review updated successfully.";
    header("Location: my_reviews.php");
    exit;
}

// Get user reviews
$reviewsSql = "
    SELECT r.review_id, h.name AS hotel_name, r.rating, r.comment, r.created_at
    FROM Reviews r
    JOIN Hotels h ON r.hotel_id = h.hotel_id
    WHERE r.user_id = ?
";
$stmt = $conn->prepare($reviewsSql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reviews = $stmt->get_result();

// Get hotels for review
$hotelsSql = "
    SELECT DISTINCT h.hotel_id, h.name
    FROM Bookings b
    JOIN Rooms r ON b.room_id = r.room_id
    JOIN Hotels h ON r.hotel_id = h.hotel_id
    WHERE b.user_id = ? AND b.check_out_date < CURDATE()
    AND h.hotel_id NOT IN (SELECT hotel_id FROM Reviews WHERE user_id = ?)
";
$stmt2 = $conn->prepare($hotelsSql);
$stmt2->bind_param("ii", $user_id, $user_id);
$stmt2->execute();
$hotelsToReview = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reviews</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="flex bg-gray-900 text-white min-h-screen font-sans" x-data="{ addModal: false, editModal: false, currentReview: {} }">

<!-- Sidebar -->
<aside class="w-64 bg-gray-800 p-6 fixed h-full">
    <div class="flex items-center space-x-3">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Meetup_Logo.png" alt="Logo" class="w-10 h-10">
        <span class="text-2xl font-bold text-blue-600">My Stay</span>
    </div>
    <nav class="space-y-3 mt-6">
        <a href="../main/index.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Home</a>
        <a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Profile</a>
        <a href="my_bookings.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">My Bookings</a>
        <a href="my_reviews.php" class="block px-4 py-2 rounded bg-blue-700">My Reviews</a>
        <form action="logout.php" method="POST">
            <button type="submit" class="w-full mt-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</button>
        </form>
    </nav>
</aside>

<!-- Main Content -->
<main class="ml-64 flex-1 p-10">
    <div class="max-w-4xl mx-auto bg-gray-800 p-8 rounded shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-blue-400">My Reviews</h1>
            <button @click="addModal = true" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">+ Add Review</button>
        </div>

        <?php if (isset($_SESSION['review_success'])): ?>
            <div class="bg-green-600 p-3 rounded mb-4 text-white text-center">
                <?= $_SESSION['review_success']; unset($_SESSION['review_success']); ?>
            </div>
        <?php endif; ?>

        <?php if ($reviews->num_rows > 0): ?>
            <?php while($row = $reviews->fetch_assoc()): ?>
                <div class="bg-gray-700 p-4 mb-4 rounded">
                    <h2 class="text-xl font-bold"><?= htmlspecialchars($row['hotel_name']) ?> (Rating: <?= $row['rating'] ?>/5)</h2>
                    <p class="mt-2"><?= nl2br(htmlspecialchars($row['comment'])) ?></p>
                    <p class="text-sm text-gray-400 mt-1">Reviewed on: <?= $row['created_at'] ?></p>
                    <div class="mt-3 space-x-2">
                        <button
                            @click="editModal = true; currentReview = {id: <?= $row['review_id'] ?>, rating: <?= $row['rating'] ?>, comment: `<?= htmlspecialchars(addslashes($row['comment'])) ?>`}"
                            class="bg-blue-500 px-3 py-1 rounded hover:bg-blue-600">Edit</button>
                        <a href="delete_review.php?id=<?= $row['review_id'] ?>"
                           class="bg-red-500 px-3 py-1 rounded hover:bg-red-600"
                           onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-300">No reviews yet.</p>
        <?php endif; ?>
    </div>

    <!-- Add Modal -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 bg-black bg-opacity-60 flex justify-center items-center">
        <div class="bg-white text-black p-6 rounded-lg w-full max-w-md" @click.away="addModal = false">
            <h2 class="text-xl font-bold mb-4 text-center">Add Review</h2>
            <?php if ($hotelsToReview->num_rows > 0): ?>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="add">
                    <label class="block">
                        Hotel:
                        <select name="hotel_id" class="w-full p-2 border rounded">
                            <?php while($hotel = $hotelsToReview->fetch_assoc()): ?>
                                <option value="<?= $hotel['hotel_id'] ?>"><?= htmlspecialchars($hotel['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </label>
                    <label class="block">Rating:
                        <input type="number" name="rating" min="1" max="5" required class="w-full p-2 border rounded">
                    </label>
                    <label class="block">Comment:
                        <textarea name="comment" rows="4" required class="w-full p-2 border rounded"></textarea>
                    </label>
                    <div class="flex justify-end space-x-2">
                        <button @click="addModal = false" type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Cancel</button>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Submit</button>
                    </div>
                </form>
            <?php else: ?>
                <p class="text-center text-red-600">No hotels to review currently.</p>
                <div class="text-center mt-4">
                    <button @click="addModal = false" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 text-white rounded">Close</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 bg-black bg-opacity-60 flex justify-center items-center">
        <div class="bg-white text-black p-6 rounded-lg w-full max-w-md" @click.away="editModal = false">
            <h2 class="text-xl font-bold mb-4 text-center">Edit Review</h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="review_id" :value="currentReview.id">
                <label class="block">Rating:
                    <input type="number" name="rating" min="1" max="5" required class="w-full p-2 border rounded" :value="currentReview.rating">
                </label>
                <label class="block">Comment:
                    <textarea name="comment" rows="4" required class="w-full p-2 border rounded" x-text="currentReview.comment"></textarea>
                </label>
                <div class="flex justify-end space-x-2">
                    <button @click="editModal = false" type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>
</main>

</body>
</html>

<?php $conn->close(); ?>
