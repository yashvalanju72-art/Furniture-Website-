<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){ header("Location: admin_login.php"); exit(); }
$conn = new mysqli("localhost","root","","furniture_db");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

if(isset($_POST['add'])){
    $name=$conn->real_escape_string($_POST['name']);
    $email=$conn->real_escape_string($_POST['email']);
    $password=password_hash($_POST['password'], PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (name,email,password) VALUES ('$name','$email','$password')");
    header("Location: users.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add User | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="container mx-auto py-10">
<h2 class="text-3xl font-bold mb-6">Add User</h2>
<form method="POST" class="space-y-4 max-w-md">
<input type="text" name="name" placeholder="Name" required class="w-full border rounded px-3 py-2">
<input type="email" name="email" placeholder="Email" required class="w-full border rounded px-3 py-2">
<input type="password" name="password" placeholder="Password" required class="w-full border rounded px-3 py-2">
<button type="submit" name="add" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add</button>
</form>
</div>
</body>
</html>
