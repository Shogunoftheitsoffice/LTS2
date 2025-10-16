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
            padding: 0;
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

        /* CHANGED: Reverted hover color from red to grey */
        .sidebar-nav .nav-button:hover,
        .sidebar-nav .nav-button.active {
            background-color: #e9e9e9;
            color: #333;
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
            display: flex;
            align-items: center;
        }
        .book-title-row:hover td {
            background-color: #f1f1f1;
        }
        
        .row-checkbox {
            margin-right: 10px;
            cursor: pointer;
        }

        /* ADDED: Styling for the new +/- span element */
        .expand-icon {
            display: inline-block;
            margin-right: 10px;
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
            width: 1em;
            text-align: center;
        }
        
        .book-title-text {
            flex-grow: 1;
        }
        
        .book-details-row {
            display: none;
        }
        .book-details-row td {
            border-bottom: 1px solid #ddd;
            padding: 0;
        }
        
        /* UPDATED: Details content styling */
        .details-content {
            padding: 15px 20px 15px 25px;
            background-color: #ffffff;
        }
        
        /* ADDED: Styling for new detail items and copy icons */
        .detail-item {
            display: flex;
            align-items: center;
            padding: 4px 0;
        }
        .copy-icon {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            cursor: pointer;
            opacity: 0.5;
            transition: opacity 0.2s;
        }
        .copy-icon:hover {
            opacity: 1;
        }
        .detail-item strong {
            display: inline-block;
            width: 150px;
            color: #9d2235;
            flex-shrink: 0; /* Prevents the label from shrinking */
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="sidebar">
            <nav class="sidebar-nav">
                <a href="#" class="nav-button active"><img src="Assets/add.png" alt="" class="nav-icon"><span>Add Entries</span></a>
                <a href="#" class="nav-button"><img src="Assets/remove.png" alt="" class="nav-icon"><span>Delete Entries</span></a>
                <a href="#" class="nav-button"><img src="Assets/import.png" alt="" class="nav-icon"><span>Import Excel</span></a>
                <a href="#" class="nav-button"><img src="Assets/export.png" alt="" class="nav-icon"><span>Export Excel</span></a>
                <a href="#" class="nav-button"><img src="Assets/exit.png" alt="" class="nav-icon"><span>Exit Admin Mode</span></a>
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
                                    <input type="checkbox" class="row-checkbox">
                                    <span class="expand-icon">+</span>
                                    <span class="book-title-text"><?php echo htmlspecialchars($row['book title'] ?? 'No Title'); ?></span>
                                </td>
                            </tr>

                            <tr class="book-details-row" id="<?php echo $details_id; ?>">
                                <td>
                                    <div class="details-content">
                                        <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong >TUID:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['tuid'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong >Course:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['course'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong >Course Title:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['course title'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong >Name:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong >Checked Out:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['checkedout'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong >Last Checkout:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['last checkout'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong >Expected Return:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['expected return'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong >Barcode:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['barcode'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong >Book:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['book'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong >ID:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['id'] ?? 'N/A'); ?></span></div>
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
            document.querySelectorAll('.book-title-row td').forEach(cell => {
                cell.addEventListener('click', (e) => {
                    // Prevent toggle if the click was on the checkbox
                    if (e.target.type === 'checkbox') {
                        return;
                    }

                    const row = cell.parentElement;
                    row.classList.toggle('active');

                    // UPDATED: Toggle the text content of the expand icon
                    const icon = row.querySelector('.expand-icon');
                    if (row.classList.contains('active')) {
                        icon.textContent = '−';
                    } else {
                        icon.textContent = '+';
                    }
                    
                    const targetSelector = row.getAttribute('data-target');
                    const detailsRow = document.querySelector(targetSelector);

                    if (detailsRow) {
                        detailsRow.style.display = (detailsRow.style.display === 'table-row') ? 'none' : 'table-row';
                    }
                });
            });

            // ADDED: Functionality for the new copy buttons
            document.querySelectorAll('.copy-icon').forEach(icon => {
                icon.addEventListener('click', (e) => {
                    e.stopPropagation(); // Prevents the main row from collapsing
                    const dataToCopy = e.target.parentElement.querySelector('.detail-data').textContent;
                    
                    navigator.clipboard.writeText(dataToCopy).then(() => {
                        // You could add a "Copied!" tooltip here for user feedback
                        console.log('Copied:', dataToCopy); 
                    }).catch(err => {
                        console.error('Failed to copy text: ', err);
                    });
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
