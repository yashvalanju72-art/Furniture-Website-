<?php
session_start();

// Admin authentication
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die('Access denied. <a href="admin_login.php">Login as admin</a>');
}

// Database connection
$conn = new mysqli("localhost", "root", "", "furniture_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get tables
$tables_result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $tables_result->fetch_array()) {
    $tables[] = $row[0];
}

// Selected table
$selected_table = isset($_GET['table']) ? $_GET['table'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$rows = [];
$columns = [];
$total_rows = 0;

if ($selected_table && in_array($selected_table, $tables)) {
    // Get columns
    $result = $conn->query("SHOW COLUMNS FROM `$selected_table`");
    while ($col = $result->fetch_assoc()) {
        $columns[] = $col['Field'];
    }

    // Build query
    $query = "SELECT * FROM `$selected_table`";
    if ($search) {
        $search_terms = [];
        foreach ($columns as $col) {
            $search_terms[] = "`$col` LIKE '%$search%'";
        }
        $query .= " WHERE " . implode(" OR ", $search_terms);
    }

    // Count total rows for pagination
    $count_result = $conn->query(str_replace("SELECT *", "SELECT COUNT(*) as cnt", $query));
    $total_rows = $count_result->fetch_assoc()['cnt'];

    $query .= " LIMIT $offset, $limit";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

// Pagination
$total_pages = ceil($total_rows / $limit);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel | FurniFi</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-amber-50 min-h-screen flex font-sans">

<!-- Sidebar -->
<div class="w-64 bg-gradient-to-b from-amber-700 to-amber-800 text-white min-h-screen p-6 flex flex-col">
    <h2 class="text-3xl font-extrabold mb-8 text-center tracking-wide">Admin Panel</h2>
    <ul class="flex-1 space-y-2">
        <?php foreach ($tables as $table): ?>
            <li>
                <a href="?table=<?php echo $table; ?>" class="block px-4 py-2 rounded-lg hover:bg-amber-600 transition font-medium <?php echo $selected_table==$table?'bg-amber-600':''; ?>">
                    <?php echo $table; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="logout.php" class="mt-6 px-4 py-2 bg-red-600 rounded-lg text-center hover:bg-red-700 font-semibold transition">Logout</a>
</div>

<!-- Main Content -->
<div class="flex-1 p-8 bg-amber-50">
<?php if ($selected_table): ?>
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <h2 class="text-3xl font-bold text-gray-800 mb-2 md:mb-0">Table: <?php echo htmlspecialchars($selected_table); ?></h2>
        <form method="GET" class="flex space-x-2">
            <input type="hidden" name="table" value="<?php echo htmlspecialchars($selected_table); ?>">
            <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>" class="border px-3 py-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-700">
            <button type="submit" class="bg-amber-700 text-white px-4 py-2 rounded-lg hover:bg-amber-800 transition">Search</button>
        </form>
    </div>

    <a href="admin_edit.php?table=<?php echo $selected_table; ?>&action=add" class="bg-amber-700 text-white px-5 py-2 rounded-lg hover:bg-amber-800 transition mb-4 inline-block shadow">+ Add New Row</a>

    <div class="overflow-x-auto rounded-lg shadow-lg bg-white">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-amber-100">
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"><?php echo htmlspecialchars($col); ?></th>
                    <?php endforeach; ?>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($rows as $row): ?>
                    <tr class="hover:bg-amber-50 transition">
                        <?php foreach ($columns as $col): ?>
                            <td class="px-6 py-3 text-gray-700"><?php echo htmlspecialchars($row[$col]); ?></td>
                        <?php endforeach; ?>
                        <td class="px-6 py-3 space-x-2">
                            <a href="admin_edit.php?table=<?php echo $selected_table; ?>&action=edit&id=<?php echo $row[$columns[0]]; ?>" class="text-blue-600 hover:underline font-medium">Edit</a>
                            <a href="admin_edit.php?table=<?php echo $selected_table; ?>&action=delete&id=<?php echo $row[$columns[0]]; ?>" class="text-red-600 hover:underline font-medium" onclick="return confirm('Delete this row?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="<?php echo count($columns)+1; ?>" class="text-center p-4 text-gray-500">No data found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center space-x-2">
        <?php for ($p=1; $p<=$total_pages; $p++): ?>
            <a href="?table=<?php echo $selected_table; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $p; ?>" class="px-3 py-1 rounded-lg <?php echo $p==$page?'bg-amber-700 text-white':'bg-amber-100 text-gray-700 hover:bg-amber-200'; ?> transition"><?php echo $p; ?></a>
        <?php endfor; ?>
    </div>

<?php else: ?>
    <div class="flex justify-center items-center h-full">
        <p class="text-gray-700 text-xl font-medium">Select a table from the sidebar to view its contents.</p>
    </div>
<?php endif; ?>
</div>
</body>
</html>
