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
$sql = "SELECT * FROM Users WHERE user_id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-900 text-white min-h-screen font-sans">

<aside class="w-64 bg-gray-800 p-6 fixed h-full">
    <div class="flex items-center space-x-3">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Meetup_Logo.png" alt="Logo" class="w-10 h-10">
        <span class="text-2xl font-bold text-blue-600">My Stay</span>
    </div>
    <nav class="space-y-3 mt-6">
        <a href="../main/index.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Home</a>
        <a href="dashboard.php" class="block px-4 py-2 rounded bg-blue-700">Profile</a>
        <a href="my_bookings.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">My Bookings</a>
        <a href="my_reviews.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">My Reviews</a>
        <form action="logout.php" method="POST">
            <button type="submit" class="w-full mt-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                Logout
            </button>
        </form>
    </nav>
</aside>

<main class="ml-64 flex-1 p-10">
    <div class="max-w-3xl mx-auto bg-gray-800 p-8 rounded-lg shadow-lg relative">
        <h1 class="text-3xl font-semibold mb-6 text-center text-blue-400">Welcome, <?= htmlspecialchars($user['name']); ?></h1>

        <div class="flex flex-col items-center space-y-4 mb-6">
            <img src="<?= !empty($user['profile_picture']) ? '../uploads/' . $user['profile_picture'] : 'user.jpeg'; ?>" alt="Profile Picture" class="w-32 h-32 rounded-full border-4 border-white shadow-md">
            <p class="text-lg"><strong>Name:</strong> <?= htmlspecialchars($user['name']); ?></p>
            <p class="text-lg"><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
            <p class="text-lg"><strong>Phone:</strong> <?= htmlspecialchars($user['phone']); ?></p>
            <p class="text-lg"><strong>Joined:</strong> <?= date("F j, Y", strtotime($user['created_at'])); ?></p>
        
        <button onclick="document.getElementById('popup').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded shadow text-white">
            Update Profile
        </button>
        
        </div>
    </div>
</main>

<!-- Popup -->
<div id="popup" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
    <div class="bg-white text-black p-6 rounded-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Update Profile</h2>
        <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="w-full p-2 border rounded" required>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full p-2 border rounded" required>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="w-full p-2 border rounded" required>
            <input type="file" name="profile_pic" accept="image/*" class="w-full p-2 border rounded">

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('popup').classList.add('hidden')" class="px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
