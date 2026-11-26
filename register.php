<?php
session_start();

$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "furniture_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password))
        $errors[] = "All fields are required.";

    if (!preg_match("/^[A-Z][a-zA-Z ]*$/", $name))
        $errors[] = "Name must start with a capital letter and contain only letters and spaces.";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Invalid email format.";

    if ($password !== $confirm_password)
        $errors[] = "Passwords do not match.";

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0)
        $errors[] = "Email is already registered.";
    $stmt->close();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful! You can now log in.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | FurniFi</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .icon-btn svg { transition: transform 0.25s ease; }
    .icon-btn:hover svg { transform: scale(1.2); color: #b45309; } /* amber-700 */
</style>
</head>
<body class="bg-gradient-to-br from-amber-50 via-stone-100 to-amber-100 min-h-screen flex flex-col">

    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center">
        <div class="bg-white/90 backdrop-blur-sm p-10 rounded-2xl shadow-2xl w-full max-w-md border border-amber-100 transform transition hover:scale-105">
            <h2 class="text-3xl font-bold mb-6 text-center text-amber-800">Create Your FurniFi Account</h2>

            <?php if(!empty($errors)): ?>
                <div class="bg-red-100 text-red-700 p-3 mb-4 rounded-lg shadow-inner">
                    <?php foreach($errors as $error) echo "<p>• $error</p>"; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-5" novalidate>
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="John Doe"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-amber-700 transition" required>
                </div>

                <div>
                    <label class="block mb-1 font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" placeholder="you@example.com"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-amber-700 transition" required>
                </div>

                <!-- Password -->
                <div class="relative group">
                    <label class="block mb-1 font-semibold text-gray-700">Password</label>
                    <input type="password" id="password" name="password"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-12 focus:outline-none focus:border-amber-700 transition" required>
                    <button type="button" onclick="togglePassword('password', this)"
                        class="icon-btn absolute right-3 top-9 text-gray-500 hover:text-amber-700 transition"
                        aria-label="Show password">
                        <svg class="icon-show w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="icon-hide w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.27-2.943-9.544-7a10.05 10.05 0 012.223-3.58M6.12 6.12A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-1.24 2.53" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3l18 18" />
                        </svg>
                    </button>
                </div>

                <!-- Confirm Password -->
                <div class="relative group">
                    <label class="block mb-1 font-semibold text-gray-700">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-12 focus:outline-none focus:border-amber-700 transition" required>
                    <button type="button" onclick="togglePassword('confirm_password', this)"
                        class="icon-btn absolute right-3 top-9 text-gray-500 hover:text-amber-700 transition"
                        aria-label="Show confirm password">
                        <svg class="icon-show w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="icon-hide w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.27-2.943-9.544-7a10.05 10.05 0 012.223-3.58M6.12 6.12A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-1.24 2.53" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3l18 18" />
                        </svg>
                    </button>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-amber-700 to-yellow-600 text-white py-2.5 rounded-lg font-semibold hover:from-amber-800 hover:to-yellow-700 shadow-md transition">
                    Register
                </button>
            </form>

            <p class="mt-5 text-center text-gray-700 text-sm">
                Already have an account?
                <a href="login.php" class="text-amber-700 font-medium hover:underline">Login</a>
            </p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#a67c52] text-gray-200 py-12">
      <div class="container mx-auto px-6 text-center space-y-6">
        <!-- Social Links -->
        <div class="flex justify-center gap-8 text-2xl">
          <a href="#" class="hover:text-green-500 transition"><i class="fab fa-facebook"></i></a>
          <a href="#" class="hover:text-green-500 transition"><i class="fab fa-twitter"></i></a>
          <a href="#" class="hover:text-green-500 transition"><i class="fab fa-instagram"></i></a>
        </div>
        <!-- Copyright -->
        <p>&copy; 2026 FurniFi Furniture. All rights reserved.</p>
      </div>
    </footer>

<script>
const nameInput = document.getElementById("name");
if (nameInput) {
    nameInput.addEventListener("input", function() {
        let value = this.value;
        if (value.length > 0) {
            this.value = value.charAt(0).toUpperCase() + value.slice(1);
        }
    });
}

function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const showIcon = btn.querySelector('.icon-show');
    const hideIcon = btn.querySelector('.icon-hide');
    const isHidden = input.type === "password";
    input.type = isHidden ? "text" : "password";
    showIcon.classList.toggle('hidden', isHidden);
    hideIcon.classList.toggle('hidden', !isHidden);
}
</script>

</body>
</html>
