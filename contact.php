<?php
session_start();

$conn = new mysqli("localhost", "root", "", "furniture_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$feedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if(empty($name) || empty($email) || empty($subject) || empty($message)){
        $feedback = "<div class='bg-red-100 text-red-700 p-3 rounded'>All fields are required.</div>";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $feedback = "<div class='bg-red-100 text-red-700 p-3 rounded'>Invalid email format.</div>";
    }
    else{

        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            $feedback = "<div class='bg-red-100 text-red-700 p-3 rounded'>Database error: ".$conn->error."</div>";
        } else {

            $stmt->bind_param("ssss", $name, $email, $subject, $message);

            if ($stmt->execute()) {
                $feedback = "<div class='bg-green-100 text-green-700 p-3 rounded'>Message sent successfully!</div>";
            } else {
                $feedback = "<div class='bg-red-100 text-red-700 p-3 rounded'>Failed to send message.</div>";
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us | FurniFi</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-amber-50 via-stone-100 to-amber-100 min-h-screen font-sans flex flex-col">

<header class="bg-[#f5f5dc] shadow sticky top-0 z-50">
  <div class="container mx-auto flex justify-between items-center py-4 px-6">
    <h1 class="text-2xl font-bold text-green-700">FurniFi</h1>
    <nav class="space-x-6">
      <a href="index.html" class="text-gray-800 hover:text-green-700 font-semibold">Home</a>
      <a href="shop.php" class="text-gray-800 hover:text-green-700 font-semibold">Shop</a>
      <a href="contact.php" class="text-green-700 font-semibold">Contact</a>
    </nav>
  </div>
</header>

<main class="flex-1 container mx-auto py-16 px-6">
  <h2 class="text-4xl font-bold text-center text-green-700 mb-12">Contact Us</h2>

  <?php if(!empty($feedback)): ?>
      <div class="max-w-xl mx-auto mb-6">
          <?php echo $feedback; ?>
      </div>
  <?php endif; ?>

  <div class="max-w-xl mx-auto bg-white shadow-xl rounded-2xl p-8">

      <form method="POST" class="space-y-5">

        <div>
          <label class="block text-gray-700 mb-2">Full Name</label>
          <input type="text" name="name" required
                 class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-green-500">
        </div>

        <div>
          <label class="block text-gray-700 mb-2">Email</label>
          <input type="email" name="email" required
                 class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-green-500">
        </div>

        <div>
          <label class="block text-gray-700 mb-2">Subject</label>
          <input type="text" name="subject" required
                 class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-green-500">
        </div>

        <div>
          <label class="block text-gray-700 mb-2">Message</label>
          <textarea name="message" rows="5" required
                    class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-green-500"></textarea>
        </div>

        <button type="submit"
                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold w-full">
          Send Message
        </button>

      </form>
  </div>
</main>

<footer class="py-6 bg-[#a67c52] text-gray-200 text-center">
  <p>&copy; 2026 FurniFi Furniture. All Rights Reserved.</p>
</footer>

</body>
</html>