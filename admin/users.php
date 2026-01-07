<?php
session_start();

// Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.html");
    exit;
}

// DB Connection
$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Delete
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $conn->query("DELETE FROM Users WHERE user_id = $delete_id");
}

// Handle Update
if (isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $conn->query("UPDATE Users SET name='$name', email='$email', phone='$phone' WHERE user_id = $update_id");
}

// Fetch all users
$result = $conn->query("SELECT * FROM Users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
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
            <a href="#" class="block px-4 py-2 rounded bg-blue-700">Users</a>
            <a href="bookings.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Bookings</a>
            <a href="hotels.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Hotels</a>
            <a href="rooms.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Rooms</a>
            <a href="reviews.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Reviews</a>
            <a href="messages.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Messages</a>
            <form action="logout.php" method="POST">
                <button type="submit" class="w-full mt-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Logout
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 flex-1 p-10">
        <div class="max-w-6xl mx-auto bg-gray-800 p-8 rounded-lg shadow-lg">
            <h1 class="text-3xl font-semibold mb-6 text-center">All Users</h1>

            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-gray-700 text-white">
                        <tr>
                            <th class="p-3 text-left">ID</th>
                            <th class="p-3 text-left">Profile</th>
                            <th class="p-3 text-left">Name</th>
                            <th class="p-3 text-left">Email</th>
                            <th class="p-3 text-left">Phone</th>
                            <th class="p-3 text-left">Created</th>
                            <th class="p-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()) { ?>
                        <tr class="border-b border-gray-700 hover:bg-gray-700">
                            <form method="POST" class="text-sm">
                                <td class="p-3"><?php echo $user['user_id']; ?></td>
                                <td class="p-3">
                                    <img src="<?php echo htmlspecialchars($user['profile_pic'] ?? 'user.jpeg'); ?>" class="w-12 h-12 rounded-full">
                                </td>
                                <td class="p-3">
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="w-full px-2 py-1 rounded bg-gray-900 text-white border border-gray-600">
                                </td>
                                <td class="p-3">
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full px-2 py-1 rounded bg-gray-900 text-white border border-gray-600">
                                </td>
                                <td class="p-3">
                                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="w-full px-2 py-1 rounded bg-gray-900 text-white border border-gray-600">
                                </td>
                                <td class="p-3 text-gray-400"><?php echo $user['created_at']; ?></td>
                                <td class="p-3 space-x-1">
                                    <input type="hidden" name="update_id" value="<?php echo $user['user_id']; ?>">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded">Update</button>
                            </form>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="delete_id" value="<?php echo $user['user_id']; ?>">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded">Delete</button>
                            </form>
                                </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>

<?php $conn->close(); ?>
