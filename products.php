<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost","root","","furniture_db");
if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

$products = $conn->query("SELECT * FROM products ORDER BY id ASC");
if(!$products){
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Products | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="bg-gradient-to-b from-amber-50 via-amber-100 to-amber-50 min-h-screen font-sans">

<div class="container mx-auto py-10">

    <!-- Top Bar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Products</h2>

        <div class="flex gap-3">
            <!-- Back to Dashboard Button -->
            <a href="admin_dashboard.php"
               class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition shadow flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>

            <!-- Add Product Button -->
            <a href="add_product.php"
               class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition shadow flex items-center gap-2">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    </div>

    <!-- Products Table -->
    <div class="overflow-x-auto rounded-lg shadow-lg bg-white">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">

            <?php if($products->num_rows > 0): ?>
                <?php while($row = $products->fetch_assoc()): ?>
                    <tr class="hover:bg-amber-50 transition">
                        <td class="px-6 py-4"><?php echo $row['id']; ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td class="px-6 py-4 text-green-600 font-semibold">
                            ₹<?php echo number_format($row['price'], 2); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php echo htmlspecialchars($row['description']); ?>
                        </td>
                        <td class="px-6 py-4 space-x-2">

                            <a href="edit_product.php?id=<?php echo $row['id']; ?>"
                               class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition inline-flex items-center gap-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>

                            <a href="delete_product.php?id=<?php echo $row['id']; ?>"
                               onclick="return confirm('Delete this product?');"
                               class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition inline-flex items-center gap-1">
                                <i class="fas fa-trash"></i> Delete
                            </a>

                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center p-4 text-gray-500">
                        No products found.
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

</body>
</html>