<?php
session_start();

// Inbuilt admin credentials
$admin_user = "admin";
$admin_pass = "admin123";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username === $admin_user && $password === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Admin Login | FurniFi</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-amber-100 via-amber-200 to-amber-100 flex items-center justify-center min-h-screen font-sans">

  <div class="bg-white p-10 rounded-2xl shadow-2xl w-full max-w-sm">
    <h1 class="text-3xl font-extrabold mb-6 text-center text-gray-800">Admin Login</h1>

    <?php if(isset($error)): ?>
      <div class="bg-red-100 text-red-700 p-3 mb-4 rounded-lg shadow-sm text-center">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5" autocomplete="off">
      <!-- Username -->
      <input
        type="text"
        name="username"
        placeholder="Username"
        required
        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
      />

      <!-- Password with eye toggle (inline SVGs) -->
      <div class="relative">
        <input
          type="password"
          name="password"
          id="password"
          placeholder="Password"
          required
          class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
        />

        <button
          type="button"
          id="togglePassword"
          aria-label="Show password"
          aria-pressed="false"
          class="absolute inset-y-0 right-3 flex items-center focus:outline-none"
        >
          <!-- Eye (visible when password is hidden) -->
          <svg id="eye" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>

          <!-- Eye Off (visible when password is shown) -->
          <svg id="eyeOff" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A3 3 0 0113.42 13.42M9.88 5.88C8.27 6.89 6.95 8.22 6 9.88 7.274 12.943 11.065 15.886 15.542 15.886c1.02 0 2-.184 2.91-.53M14.12 18.12C13.11 18.64 11.84 19 10.58 19 6.103 19 2.312 16.057 1.038 12 1.58 10.07 2.58 8.37 4 7"/>
          </svg>
        </button>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        name="login"
        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-3 rounded-lg shadow-lg transition flex items-center justify-center gap-2"
      >
        <!-- simple lock svg to avoid external icons -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
          <path d="M7 11V7a5 5 0 0110 0v4"></path>
        </svg>
        Login
      </button>
    </form>
  </div>

  <!-- Toggle script -->
  <script>
    (function(){
      const pw = document.getElementById('password');
      const toggle = document.getElementById('togglePassword');
      const eye = document.getElementById('eye');
      const eyeOff = document.getElementById('eyeOff');

      // initialize aria + icons based on current type
      function updateToggleState() {
        const isVisible = pw.type === 'text';
        toggle.setAttribute('aria-pressed', isVisible ? 'true' : 'false');
        toggle.setAttribute('aria-label', isVisible ? 'Hide password' : 'Show password');
        if (isVisible) {
          eye.classList.add('hidden');
          eyeOff.classList.remove('hidden');
        } else {
          eye.classList.remove('hidden');
          eyeOff.classList.add('hidden');
        }
      }

      updateToggleState();

      toggle.addEventListener('click', function () {
        pw.type = pw.type === 'password' ? 'text' : 'password';
        updateToggleState();
        // keep focus on the password input after toggling
        pw.focus();
      });
    })();
  </script>

</body>
</html>
