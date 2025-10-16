<?php
// --- Include the database connection file ---
require_once 'db_connect.php';

// --- SQL Query to fetch data ---
$sql = "SELECT * FROM textbooks";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Textbook Management</title>
    <style>
        /* --- Basic Reset and Body Styles --- */
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
        }
        .container { display: flex; }

        /* --- Sidebar Styles (Unchanged) --- */
        .sidebar {
            width: 250px; height: 100vh; background-color: #ffffff;
            padding: 0; box-sizing: border-box; position: sticky; top: 0;
            border-right: 1px solid #e0e0e0; box-shadow: 3px 0px 15px rgba(0, 0, 0, 0.05);
            display: flex; flex-direction: column;
        }
        .sidebar-nav { list-style: none; padding: 0; margin: 0; }
        .sidebar-nav .nav-button {
            display: flex; align-items: center; padding: 12px 15px; text-decoration: none;
            color: #333; font-weight: bold; background-color: #f9f9f9;
            border-bottom: 1px solid #ddd; transition: background-color 0.2s, color 0.2s;
        }
        .sidebar-nav .nav-button:hover { background-color: #e9e9e9; color: #333; }
        .nav-icon { width: 20px; height: 20px; margin-right: 12px; }

        /* --- Main Content Area --- */
        .main-content { flex-grow: 1; padding: 25px; overflow-y: auto; }

        /* --- NEW: Hot List Table Styles --- */
        .book-table {
            width: 100%;
            border-collapse: collapse; /* Changed for a classic table look */
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            overflow: hidden; /* Ensures border-radius is respected by children */
        }
        .book-table th, .book-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eef0f2; /* Lighter border for a softer look */
        }
        .book-table thead th {
            background-color: #f9fafb;
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
        }
        .book-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .book-table td:first-child, .book-table th:first-child {
            width: 40px; /* Fixed width for checkbox column */
            text-align: center;
        }
        .item-title {
            font-weight: 600;
            color: #333;
        }
        .status-available { color: #28a745; font-weight: 500; }
        .status-out { color: #dc3545; font-weight: 500; }
    </style>
</head>
<body>

    <div class="container">
        <div class="sidebar">
            <nav class="sidebar-nav">
                <a href="#" class="nav-button"><img src="Assets/add.png" alt="" class="nav-icon"><span>Add Entries</span></a>
                <a href="#" class="nav-button"><img src="Assets/remove.png" alt="" class="nav-icon"><span>Delete Entries</span></a>
                <a href="#" class="nav-button"><img src="Assets/import.png" alt="" class="nav-icon"><span>Import Excel</span></a>
                <a href="#" class="nav-button"><img src="Assets/export.png" alt="" class="nav-icon"><span>Export Excel</span></a>
                <a href="#" class="nav-button"><img src="Assets/exit.png" alt="" class="nav-icon"><span>Exit Admin Mode</span></a>
            </nav>
        </div>

        <div class="main-content">
            <table class="book-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" title="Select all"></th>
                        <th>Book Title</th>
                        <th>TUID</th>
                        <th>Course</th>
                        <th>Barcode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <tr>
                                <td><input type="checkbox" class="item-checkbox"></td>
                                <td class="item-title"><?php echo htmlspecialchars($row['book title'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['tuid'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['course'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['barcode'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php
                                    // Logic for the status column
                                    $isCheckedOut = isset($row['checkedout']) && strtolower(trim($row['checkedout'])) === 'yes';
                                    if ($isCheckedOut) {
                                        echo '<span class="status-out">Checked Out</span>';
                                        if (!empty($row['expected return'])) {
                                            echo ' (' . htmlspecialchars($row['expected return']) . ')';
                                        }
                                    } else {
                                        echo '<span class="status-available">Available</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding: 20px;'>No results found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    </body>
</html>
<?php
// --- Close Connection ---
$conn->close();
?>
