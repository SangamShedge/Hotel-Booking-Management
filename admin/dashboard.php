<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "hotelbooking"; // Change this to your actual database name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user profile data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM Users WHERE user_id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            <a href="dashboard.php" class="block px-4 py-2 rounded bg-blue-700">Profile</a>
            <a href="users.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Users</a>
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
        <div class="max-w-3xl mx-auto bg-gray-800 p-8 rounded-lg shadow-lg">
            <h1 class="text-3xl font-semibold mb-6 text-center">Welcome, <?php echo htmlspecialchars($user['name']); ?></h1>

            <div class="flex flex-col items-center space-y-4 mb-8">
                <img src="<?php echo $user['profile_picture'] ?? 'user.jpeg'; ?>" alt="Profile" class="w-32 h-32 rounded-full border-4 border-white shadow-md">
                <p class="text-lg"><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p class="text-lg"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p class="text-lg"><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            </div>
        </div>
    </main>

</body>
</html>

<?php
$conn->close();
?>
