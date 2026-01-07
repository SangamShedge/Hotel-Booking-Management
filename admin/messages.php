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

$sql = "SELECT cm.*, u.name AS user_name, u.email AS user_email 
        FROM ContactMessages cm 
        JOIN Users u ON cm.user_id = u.user_id 
        ORDER BY cm.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Messages</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-900 text-white min-h-screen font-sans">

<!-- Sidebar -->
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
        <a href="reviews.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Reviews</a>
        <a href="messages.php" class="block px-4 py-2 rounded bg-blue-700">Messages</a>
        <form action="logout.php" method="POST">
            <button type="submit" class="w-full mt-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                Logout
            </button>
        </form>
    </nav>
</aside>

<!-- Main Content -->
<main class="ml-64 flex-1 p-10">
    <div class="max-w-4xl mx-auto bg-gray-800 p-8 rounded-lg shadow-lg">
        <h1 class="text-3xl font-semibold mb-6 text-center">Contact Messages</h1>

        <?php if ($result->num_rows > 0): ?>
            <div class="space-y-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="border border-gray-700 rounded-md">
                        <button class="w-full text-left px-4 py-3 bg-gray-700 hover:bg-gray-600 focus:outline-none toggle-btn">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold"><?php echo htmlspecialchars($row['user_name']); ?> (<?php echo htmlspecialchars($row['user_email']); ?>)</span>
                                <span class="text-sm text-gray-300"><?php echo $row['created_at']; ?></span>
                            </div>
                        </button>
                        <div class="hidden px-4 py-3 bg-gray-900 toggle-content">
                            <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-400">No messages found.</p>
        <?php endif; ?>

    </div>
</main>

<script>
    // Toggle message visibility
    document.querySelectorAll('.toggle-btn').forEach(button => {
        button.addEventListener('click', () => {
            const content = button.nextElementSibling;
            content.classList.toggle('hidden');
        });
    });
</script>

</body>
</html>

<?php $conn->close(); ?>
