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
    <link rel="stylesheet" href="style.css">
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
                <a href="#" class="nav-button"><img src="Assets/stats.png" alt="" class="nav-icon"><span>Stats</span></a>
                <a href="#" class="nav-button"><img src="Assets/uncheck.png" alt="" class="nav-icon"><span>Uncheck</span></a>
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
