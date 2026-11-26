<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost","root","","furniture_db");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

if(isset($_POST['add'])){
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $desc = $conn->real_escape_string($_POST['description']);
    $conn->query("INSERT INTO products (name, price, description) VALUES ('$name','$price','$desc')");
    header("Location: products.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product | Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#e0d4b7] from-gray-50 to-gray-100 min-h-screen flex items-center justify-center font-sans">

<div class="bg-white shadow-2xl rounded-2xl p-10 w-full max-w-lg border border-gray-100">
    <h2 class="text-3xl font-extrabold text-gray-800 mb-6 text-center">✨ Add New Product</h2>
    <p class="text-gray-500 text-center mb-8"></p>
    
    <form method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
            <input type="text" name="name" placeholder="e.g. Modern Sofa"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:outline-none transition">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Price (₹)</label>
            <input type="number" name="price" placeholder="e.g. 499.99" step="0.01"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:outline-none transition">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" placeholder="Write a short description..."
                required
                rows="4"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:outline-none transition resize-none"></textarea>
        </div>

        <button type="submit" name="add"
            class="w-full bg-green-600 text-white font-semibold py-3 rounded-lg shadow-md hover:bg-green-700 hover:shadow-lg transition transform hover:-translate-y-0.5">
            ➕ Add Product
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="products.php" class="text-green-600 hover:text-green-700 text-sm font-medium transition">
            ← Back to Products
        </a>
    </div>
</div>

</body>
</html>
