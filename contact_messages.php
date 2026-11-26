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

$result = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC");
if(!$result){
    die("Query failed: ".$conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Contact Messages</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="bg-gray-100">

<div class="container mx-auto py-10">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold">Contact Messages</h2>
        <a href="admin_dashboard.php" class="bg-blue-600 text-white px-4 py-2 rounded">
            Back to Dashboard
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Subject</th>
                    <th class="p-3 text-left">Message</th>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="border-b">
                            <td class="p-3"><?php echo $row['id']; ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['message']); ?></td>
                            <td class="p-3"><?php echo $row['created_at']; ?></td>
                            <td class="p-3">
                                <a href="delete_message.php?id=<?php echo $row['id']; ?>"
                                   onclick="return confirm('Delete this message?');"
                                   class="bg-red-600 text-white px-3 py-1 rounded">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center p-4">No messages found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>