<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "furniture_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

// Fetch user info
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    if ($password) {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['user_name'] = $name; // Update session name
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile: " . $stmt->error;
    }
    $stmt->close();

    // Refresh user info
    $stmt = $conn->prepare("SELECT name, email FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile | FurniFi</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<!-- Navbar -->
<nav class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Edit Profile</h1>
    <div class="flex items-center space-x-4">
        <a href="index.html" class="text-red-600 hover:underline">Home</a>
        <a href="dashboard.php" class="text-green-600 hover:underline">Dashboard</a>
        <a href="shop.php" class="text-blue-600 hover:underline">Shop</a>
        <a href="logout.php" class="text-red-600 hover:underline">Logout</a>
    </div>
</nav>

<div class="container mx-auto py-10 px-4 flex-1">
    <?php if ($message): ?>
        <div class="bg-green-100 text-green-700 p-3 mb-6 rounded"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
        <h2 class="text-2xl font-bold mb-6">Update Your Profile</h2>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 mb-2">Full Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required class="w-full border px-3 py-2 rounded">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full border px-3 py-2 rounded">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">New Password (leave blank to keep current)</label>
                <div class="relative">
    <input id="password" type="password" name="password" class="w-full border px-3 py-2 rounded pr-10">
    <button type="button" id="togglePassword" class="absolute right-2 top-2 text-gray-600">
        <!-- Eye SVG -->
        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
    </button>
</div>
            </div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Save Changes</button>
        </form>
    </div>
</div>

<script>
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');

togglePassword.addEventListener('click', () => {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
});
</script>

<!-- Footer -->
<footer class="py-12 bg-[#a67c52] text-gray-200 mt-auto">
    <div class="container mx-auto px-6 text-center space-y-6">
        <p class="text-gray-400">&copy; 2026 FurniFi Furniture. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
