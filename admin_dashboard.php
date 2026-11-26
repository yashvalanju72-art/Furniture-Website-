<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | FurniFi</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="bg-gradient-to-b from-amber-50 via-amber-100 to-amber-50 min-h-screen font-sans">

<!-- Navbar -->
<nav class="bg-white shadow-md p-4 flex justify-between items-center sticky top-0 z-50">
    <h1 class="text-3xl font-bold text-amber-700">FurniFi Admin</h1>
    <a href="logout.php" class="text-red-600 font-semibold hover:text-red-700 transition">Logout</a>
</nav>

<!-- Dashboard Cards -->
<div class="container mx-auto py-12 px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8">

    <a href="products.php" class="bg-green-600 hover:bg-green-700 transform hover:scale-105 transition rounded-xl shadow-lg p-8 flex flex-col items-center text-white font-semibold">
        <i class="fas fa-couch text-5xl mb-4"></i>
        <span class="text-xl">Products</span>
    </a>

    <a href="users.php" class="bg-blue-600 hover:bg-blue-700 transform hover:scale-105 transition rounded-xl shadow-lg p-8 flex flex-col items-center text-white font-semibold">
        <i class="fas fa-users text-5xl mb-4"></i>
        <span class="text-xl">Users</span>
    </a>

    <a href="orders.php" class="bg-yellow-600 hover:bg-yellow-700 transform hover:scale-105 transition rounded-xl shadow-lg p-8 flex flex-col items-center text-white font-semibold">
        <i class="fas fa-box-open text-5xl mb-4"></i>
        <span class="text-xl">Orders</span>
    </a>

    <a href="cart_items.php" class="bg-purple-600 hover:bg-purple-700 transform hover:scale-105 transition rounded-xl shadow-lg p-8 flex flex-col items-center text-white font-semibold">
        <i class="fas fa-shopping-cart text-5xl mb-4"></i>
        <span class="text-xl">Cart Items</span>
    </a>

    <!-- NEW CONTACT MESSAGES CARD -->
    <a href="contact_messages.php" class="bg-red-600 hover:bg-red-700 transform hover:scale-105 transition rounded-xl shadow-lg p-8 flex flex-col items-center text-white font-semibold">
        <i class="fas fa-envelope text-5xl mb-4"></i>
        <span class="text-xl">Contact Messages</span>
    </a>

</div>

</body>
</html>