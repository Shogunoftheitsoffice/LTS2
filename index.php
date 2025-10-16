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

        /* --- Main Layout Container (Flexbox) --- */
        .container {
            display: flex;
        }

        /* --- Sidebar Styles --- */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #ffffff;
            padding: 0; /* CHANGED: Removed padding to make buttons full-width */
            box-sizing: border-box;
            position: sticky;
            top: 0;
            border-right: 1px solid #e0e0e0;
            box-shadow: 3px 0px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }

        /* --- Navigation Menu Styles --- */
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav .nav-button {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            background-color: #f9f9f9;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.2s, color 0.2s;
        }

        .sidebar-nav .nav-button:hover,
        .sidebar-nav .nav-button.active {
            background-color: #9d2235;
            color: #ffffff;
        }
        
        .nav-icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
        }


        /* --- Main Content Area --- */
        .main-content {
            flex-grow: 1;
            padding: 25px;
            overflow-y: auto;
        }

        /* --- Table Styles --- */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .book-title-row td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
            display: flex; /* ADDED: To align checkbox and text */
            align-items: center; /* ADDED: To align checkbox and text */
        }
        .book-title-row:hover td {
            background-color: #f1f1f1;
        }
        .book-title-row td::before {
            content: '+';
            display: inline-block;
            margin-right: 10px;
            font-size: 1.2em;
            color: #333;
        }
        .book-title-row.active td::before {
            content: '−';
        }
        
        /* --- ADDED: Checkbox Styling --- */
        .row-checkbox {
            margin-right: 10px;
            cursor: pointer;
        }
        
        .book-title-text {
            flex-grow: 1; /* Allows title to take remaining space */
        }
        
        .book-details-row {
            display: none;
        }
        .book-details-row td {
            border-bottom: 1px solid #ddd;
            padding: 0;
        }
        .details-content {
            padding: 15px 20px 15px 45px;
            background-color: #ffffff;
            line-height: 1.6;
        }
        .details-content strong {
            display: inline-block;
            width: 150px;
            color: #9d2235;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="sidebar">
            <nav class="sidebar-nav">
                <a href="#" class="nav-button active">
                    <img src="Assets/add.png" alt="" class="nav-icon">
                    <span>Add Entries</span>
                </a>
                <a href="#" class="nav-button">
                    <img src="Assets/remove.png" alt="" class="nav-icon">
                    <span>Delete Entries</span>
                </a>
                <a href="#" class="nav-button">
                    <img src="Assets/import.png" alt="" class="nav-icon">
                    <span>Import Excel</span>
                </a>
                <a href="#" class="nav-button">
                    <img src="Assets/export.png" alt="" class="nav-icon">
                    <span>Export Excel</span>
                </a>
                <a href="#" class="nav-button">
                    <img src="Assets/exit.png" alt="" class="nav-icon">
                    <span>Exit Admin Mode</span>
                </a>
            </nav>
        </div>

        <div class="main-content">
            <table>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $details_id = "details-" . htmlspecialchars($row['id']);
                    ?>

                            <tr class="book-title-row" data-target="#<?php echo $details_id; ?>">
                                <td>
                                    <input type="checkbox" class="row-checkbox" onclick="event.stopPropagation()">
                                    
                                    <span class="book-title-text">
                                        <?php echo htmlspecialchars($row['book title'] ?? 'No Title'); ?>
                                    </span>
                                </td>
                            </tr>

                            <tr class="book-details-row" id="<?php echo $details_id; ?>">
                                <td>
                                    <div class="details-content">
                                        <strong>TUID:</strong> <?php echo htmlspecialchars($row['tuid'] ?? 'N/A'); ?><br>
                                        <strong>Course:</strong> <?php echo htmlspecialchars($row['course'] ?? 'N/A'); ?><br>
                                        <strong>Course Title:</strong> <?php echo htmlspecialchars($row['course title'] ?? 'N/A'); ?><br>
                                        <strong>Name:</strong> <?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?><br>
                                        <strong>Checked Out:</strong> <?php echo htmlspecialchars($row['checkedout'] ?? 'N/A'); ?><br>
                                        <strong>Last Checkout:</strong> <?php echo htmlspecialchars($row['last checkout'] ?? 'N/A'); ?><br>
                                        <strong>Expected Return:</strong> <?php echo htmlspecialchars($row['expected return'] ?? 'N/A'); ?><br>
                                        <strong>Barcode:</strong> <?php echo htmlspecialchars($row['barcode'] ?? 'N/A'); ?><br>
                                        <strong>Book:</strong> <?php echo htmlspecialchars($row['book'] ?? 'N/A'); ?><br>
                                        <strong>ID:</strong> <?php echo htmlspecialchars($row['id'] ?? 'N/A'); ?>
                                    </div>
                                </td>
                            </tr>

                    <?php
                        }
                    } else {
                        echo "<tr><td>0 results found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Updated to handle clicks on the row but not on the checkbox
            document.querySelectorAll('.book-title-row td').forEach(cell => {
                cell.addEventListener('click', (e) => {
                    // Only toggle if the click is not on the checkbox itself
                    if (e.target.type !== 'checkbox') {
                        const row = cell.parentElement;
                        row.classList.toggle('active');
                        const targetSelector = row.getAttribute('data-target');
                        const detailsRow = document.querySelector(targetSelector);

                        if (detailsRow) {
                            if (detailsRow.style.display === 'table-row') {
                                detailsRow.style.display = 'none';
                            } else {
                                detailsRow.style.display = 'table-row';
                            }
                        }
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
