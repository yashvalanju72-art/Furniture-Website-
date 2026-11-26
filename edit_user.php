<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){ 
    header("Location: admin_login.php"); 
    exit(); 
}

$conn = new mysqli("localhost","root","","furniture_db");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$id=intval($_GET['id']);
$user=$conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();

if(isset($_POST['update'])){
    $name=$conn->real_escape_string($_POST['name']);
    $email=$conn->real_escape_string($_POST['email']);
    $password=$_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];
    $conn->query("UPDATE users SET name='$name', email='$email', password='$password' WHERE id=$id");
    header("Location: users.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit User | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="bg-gradient-to-b from-amber-50 via-amber-100 to-amber-50 min-h-screen font-sans">

<div class="container mx-auto py-12">
    <div class="max-w-lg mx-auto bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800 text-center">Edit User</h2>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']);?>" required class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-amber-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']);?>" required class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-amber-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" name="password" placeholder="Leave blank to keep current password" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-amber-400 focus:outline-none">
            </div>
            <button type="submit" name="update" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded-lg shadow transition flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Update User
            </button>
        </form>
        <a href="users.php" class="mt-4 block text-center text-blue-600 hover:underline">← Back to Users</a>
    </div>
</div>

</body>
</html>
