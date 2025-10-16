<?php
// --- Include the database connection file ---
require_once 'db_connect.php';

// --- SQL Query to fetch data ---
$sql = "SELECT * FROM textbooks";
$result = $conn->query($sql);

// --- NEW: Sort data into two separate arrays ---
$checkedOutBooks = [];
$availableBooks = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (isset($row['checkedout']) && strtolower(trim($row['checkedout'])) === 'yes') {
            $checkedOutBooks[] = $row;
        } else {
            $availableBooks[] = $row;
        }
    }
}

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
            width: 200px; height: 100vh; background-color: #ffffff;
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
        .list-header {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.5em;
            color: #333;
        }
        .list-header:not(:first-child) { margin-top: 40px; } /* Add space above second list */
        .no-results {
            background-color: #ffffff; padding: 20px; text-align: center; color: #6c757d;
            border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        /* --- List Table Styles --- */
        .book-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .book-table th, .book-table td {
            padding: 10px 5px; /* Adjusted padding */
            text-align: left;
            border-bottom: 1px solid #eef0f2;
            border-right: 1px solid #eef0f2; /* ADDED: Faint vertical lines */
        }
        /* ADDED: Remove border from the last column */
        .book-table th:last-child, .book-table td:last-child {
            border-right: none;
        }
        .book-table thead th {
            background-color: #f9fafb;
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
        }
        .book-table tbody tr.main-row:hover { background-color: #f8f9fa; }
        .main-row { cursor: pointer; }
        .item-title { font-weight: 600; color: #333; }
        .status-available { color: #28a745; font-weight: 500; }
        .status-out { color: #dc3545; font-weight: 500; }

        /* --- Expandable Area Styles --- */
        .details-row { display: none; }
        .details-cell { padding: 0 !important; border-right: none; }
        .details-container { background-color: #fafafa; padding: 20px 25px; }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 10px 20px;
        }
        .detail-item { font-size: 0.9em; }
        .detail-item strong { display: inline-block; width: 120px; color: #6c757d; }
    </style>
</head>
<body>

    <div class="container">
        <div class="sidebar">
            <nav class="sidebar-nav">
                <a href="#" class="nav-button"><img src="Assets/checkout.png" alt="" class="nav-icon"><span>Checkout</span></a>
                <a href="#" class="nav-button"><img src="Assets/search.png" alt="" class="nav-icon"><span>Search</span></a>
                <a href="#" class="nav-button"><img src="Assets/add.png" alt="" class="nav-icon"><span>Add</span></a>
                <a href="#" class="nav-button"><img src="Assets/edit.png" alt="" class="nav-icon"><span>Edit</span></a>
                <a href="#" class="nav-button"><img src="Assets/remove.png" alt="" class="nav-icon"><span>Delete</span></a>
                <a href="#" class="nav-button"><img src="Assets/import.png" alt="" class="nav-icon"><span>Import</span></a>
                <a href="#" class="nav-button"><img src="Assets/export.png" alt="" class="nav-icon"><span>Export</span></a>
                <a href="#" class="nav-button"><img src="Assets/settings.png" alt="" class="nav-icon"><span>Settings</span></a>
                <a href="#" class="nav-button"><img src="Assets/help.png" alt="" class="nav-icon"><span>Help</span></a>
                <a href="#" class="nav-button"><img src="Assets/exit.png" alt="" class="nav-icon"><span>Exit</span></a>
            </nav>
        </div>

        <div class="main-content">
            <h2 class="list-header">Checked Out</h2>
            <?php if (!empty($checkedOutBooks)): ?>
                <table class="book-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Book Title</th>
                            <th>TUID</th>
                            <th>Course</th>
                            <th>Barcode</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checkedOutBooks as $row): ?>
                            <tr class="main-row">
                                <td onclick="event.stopPropagation()"><input type="checkbox" class="item-checkbox"></td>
                                <td class="item-title"><?php echo htmlspecialchars($row['book title'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['tuid'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['course'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['barcode'] ?? 'N/A'); ?></td>
                                <td><span class="status-out">Checked Out</span><?php if (!empty($row['expected return'])) { echo ' (' . htmlspecialchars($row['expected return']) . ')'; } ?></td>
                            </tr>
                            <tr class="details-row">
                                <td colspan="6" class="details-cell">
                                    <div class="details-container">
                                        <div class="details-grid">
                                            <div class="detail-item"><strong>Course Title:</strong> <?php echo htmlspecialchars($row['course title'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Checked Out To:</strong> <?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Last Checkout:</strong> <?php echo htmlspecialchars($row['last checkout'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Database ID:</strong> <?php echo htmlspecialchars($row['id'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Book Type:</strong> <?php echo htmlspecialchars($row['book'] ?? 'N/A'); ?></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-results">No checked out books.</div>
            <?php endif; ?>

            <h2 class="list-header">Available</h2>
            <?php if (!empty($availableBooks)): ?>
                <table class="book-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Book Title</th>
                            <th>TUID</th>
                            <th>Course</th>
                            <th>Barcode</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($availableBooks as $row): ?>
                             <tr class="main-row">
                                <td onclick="event.stopPropagation()"><input type="checkbox" class="item-checkbox"></td>
                                <td class="item-title"><?php echo htmlspecialchars($row['book title'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['tuid'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['course'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['barcode'] ?? 'N/A'); ?></td>
                                <td><span class="status-available">Available</span></td>
                            </tr>
                            <tr class="details-row">
                                <td colspan="6" class="details-cell">
                                    <div class="details-container">
                                        <div class="details-grid">
                                             <div class="detail-item"><strong>Course Title:</strong> <?php echo htmlspecialchars($row['course title'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Checked Out To:</strong> <?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Last Checkout:</strong> <?php echo htmlspecialchars($row['last checkout'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Database ID:</strong> <?php echo htmlspecialchars($row['id'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Book Type:</strong> <?php echo htmlspecialchars($row['book'] ?? 'N/A'); ?></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-results">No available books.</div>
            <?php endif; ?>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.main-row').forEach(row => {
                row.addEventListener('click', () => {
                    const detailsRow = row.nextElementSibling;
                    if (detailsRow && detailsRow.classList.contains('details-row')) {
                        detailsRow.style.display = (detailsRow.style.display === 'table-row') ? 'none' : 'table-row';
                    }
                });
            });
        });
    </script>

</body>
</html>
<?php
// --- Close Connection ---
$conn->close();
?>
