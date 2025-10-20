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
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="container">
        <div class="sidebar">
            <nav class="sidebar-nav">
                <a href="#" class="nav-button"><img src="Assets/checkout.png" alt="" class="nav-icon"><span>Checkout</span></a>
                <a href="#" id="search-btn" class="nav-button"><img src="Assets/search.png" alt="" class="nav-icon"><span>Search</span></a>
                <a href="#" class="nav-button"><img src="Assets/add.png" alt="" class="nav-icon"><span>Add</span></a>
                <a href="#" class="nav-button"><img src="Assets/edit.png" alt="" class="nav-icon"><span>Edit</span></a>
                <a href="#" class="nav-button"><img src="Assets/remove.png" alt="" class="nav-icon"><span>Delete</span></a>
                <a href="#" class="nav-button"><img src="Assets/import.png" alt="" class="nav-icon"><span>Import</span></a>
                <a href="#" class="nav-button"><img src="Assets/export.png" alt="" class="nav-icon"><span>Export</span></a>
                <a href="#" class="nav-button"><img src="Assets/settings.png" alt="" class="nav-icon"><span>Settings</span></a>
                <a href="#" class="nav-button"><img src="Assets/help.png" alt="" class="nav-icon"><span>Help</span></a>
                <a href="#" class="nav-button"><img src="Assets/stats.png" alt="" class="nav-icon"><span>Stats</span></a>
                <a href="#" class="nav-button"><img src="Assets/select.png" alt="" class="nav-icon"><span>Select</span></a>
                <a href="#" class="nav-button"><img src="Assets/uncheck.png" alt="" class="nav-icon"><span>Deselect</span></a>
                <a href="#" class="nav-button"><img src="Assets/exit.png" alt="" class="nav-icon"><span>Exit</span></a>
            </nav>
        </div>

        <div class="main-content">
            <h2 class="list-header">Checked Out</h2>
            <?php if (!empty($checkedOutBooks)): ?>
                <table class="book-table">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Action</th>
                            <th>Book ID</th>
                            <th>Barcode</th>
                            <th>Book Title</th>
                            <th>TUID</th>
                            <th>Professor</th> <th>Last Checkout</th>
                            <th>Time Remaining</th> </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checkedOutBooks as $row): ?>
                            <tr class="main-row" data-id="<?php echo htmlspecialchars($row['id']); ?>">
                                <td><button class="action-btn return-btn">Return</button></td>
                                <td><?php echo htmlspecialchars($row['book'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['barcode'] ?? 'N/A'); ?></td>
                                <td class="item-title"><?php echo htmlspecialchars($row['book title'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['tuid'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['last checkout'] ?? 'N/A'); ?></td>
                                <td class="countdown-cell" data-return-time="<?php echo htmlspecialchars($row['expected return'] ?? ''); ?>">
                                    --:--:--
                                </td>
                            </tr>
                            <tr class="details-row">
                                <td colspan="8" class="details-cell"> <div class="details-container">
                                        <div class="details-grid">
                                            <div class="detail-item"><strong>Course:</strong> <?php echo htmlspecialchars($row['course'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Course Title:</strong> <?php echo htmlspecialchars($row['course title'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Expected Return:</strong> <?php echo htmlspecialchars($row['expected return'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Database ID:</strong> <?php echo htmlspecialchars($row['id'] ?? 'N/A'); ?></div>
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
                            <th style="width: 100px;">Action</th>
                            <th>Book ID</th>
                            <th>Barcode</th>
                            <th>Book Title</th>
                            <th>Course</th>
                            <th>Course Title</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($availableBooks as $row): ?>
                             <tr class="main-row" data-id="<?php echo htmlspecialchars($row['id']); ?>">
                                <td><button class="action-btn checkout-btn">Checkout</button></td>
                                <td><?php echo htmlspecialchars($row['book'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['barcode'] ?? 'N/A'); ?></td>
                                <td class="item-title"><?php echo htmlspecialchars($row['book title'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['course'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['course title'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr class="details-row">
                                <td colspan="6" class="details-cell">
                                    <div class="details-container">
                                        <div class="details-grid">
                                            <div class="detail-item"><strong>TUID:</strong> <?php echo htmlspecialchars($row['tuid'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Professor:</strong> <?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?></div> <div class="detail-item"><strong>Last Checkout:</strong> <?php echo htmlspecialchars($row['last checkout'] ?? 'N/A'); ?></div>
                                            <div class="detail-item"><strong>Database ID:</strong> <?php echo htmlspecialchars($row['id'] ?? 'N/A'); ?></div>
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

    <div id="search-modal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close">&times;</button>
            <h2>Search Textbooks</h2>
            <form id="search-form" onsubmit="return false;">
                <input type="text" id="search-input" placeholder="Search by Title, TUID, Course, Barcode..." autocomplete="off">
                <button type="submit">Search</button>
            </form>
            <div id="search-results">
                </div>
        </div>
    </div>

    <div id="checkout-modal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close">&times;</button>
            <h2>Checkout Book</h2>
            <form id="checkout-form" onsubmit="return false;">
                <p>Please enter the 9-digit TUID to check out this item.</p>
                <label for="checkout-tuid-input">TUID:</label>
                <input type="text" id="checkout-tuid-input" placeholder="e.g., 912345678" autocomplete="off" maxlength="9">
                <div id="checkout-message" class="checkout-message"></div>
                <button type="submit" id="checkout-submit-btn">Checkout</button>
            </form>
        </div>
    </div>
    <script src="scripts.js"></script>

</body>
</html>
<?php
// --- Close Connection ---
$conn->close();
?>
