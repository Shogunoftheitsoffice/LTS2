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

        /* --- NEW: Modern List Table Styles --- */
        .book-table {
            width: 100%;
            border-collapse: separate; /* Allows for space between rows */
            border-spacing: 0 15px; /* Creates vertical gap between list items */
        }
        .book-table td {
            padding: 0; /* Padding is handled by the inner container */
        }
        .list-item-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.07);
            border: 1px solid #e0e0e0;
        }
        .item-header {
            display: flex;
            align-items: center;
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        .item-checkbox { margin-right: 15px; }
        .item-title {
            font-size: 1.2em;
            font-weight: 600;
            margin: 0;
            flex-grow: 1;
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Responsive 2-column grid */
            gap: 8px 20px; /* Row and Column gap */
        }

        /* --- Detail & Copy Icon Styles --- */
        .detail-item {
            display: flex;
            align-items: center;
            font-size: 0.9em;
        }
        .copy-icon { width: 16px; height: 16px; margin-right: 8px; cursor: pointer; opacity: 1; }
        .detail-item strong {
            display: inline-block;
            width: 120px;
            color: #9d2235;
            flex-shrink: 0;
        }
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
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <tr>
                                <td>
                                    <div class="list-item-container">
                                        <div class="item-header">
                                            <input type="checkbox" class="item-checkbox">
                                            <h3 class="item-title"><?php echo htmlspecialchars($row['book title'] ?? 'No Title'); ?></h3>
                                        </div>
                                        <div class="details-grid">
                                            <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"> <span class="detail-data"><?php echo htmlspecialchars($row['tuid'] ?? 'N/A'); ?></span></div>
                                            <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"> <span class="detail-data"><?php echo htmlspecialchars($row['course'] ?? 'N/A'); ?></span></div>
                                            <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"> <span class="detail-data"><?php echo htmlspecialchars($row['course title'] ?? 'N/A'); ?></span></div>
                                            <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"> <span class="detail-data"><?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?></span></div>
                                            <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"> <span class="detail-data"><?php echo htmlspecialchars($row['checkedout'] ?? 'N/A'); ?></span></div>
                                            <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"> <span class="detail-data"><?php echo htmlspecialchars($row['last checkout'] ?? 'N/A'); ?></span></div>
                                            <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"> <span class="detail-data"><?php echo htmlspecialchars($row['expected return'] ?? 'N/A'); ?></span></div>
                                            <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"> <span class="detail-data"><?php echo htmlspecialchars($row['barcode'] ?? 'N/A'); ?></span></div>
                                            <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"> <span class="detail-data"><?php echo htmlspecialchars($row['book'] ?? 'N/A'); ?></span></div>
                                            <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"> <span class="detail-data"><?php echo htmlspecialchars($row['id'] ?? 'N/A'); ?></span></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td>No results found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const copyToClipboard = (text, iconElement) => {
                const performAnimation = () => {
                    const originalSrc = iconElement.src;
                    iconElement.src = 'Assets/check.gif';
                    setTimeout(() => { iconElement.src = originalSrc; }, 1500);
                };

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(performAnimation);
                } else {
                    const textArea = document.createElement("textarea");
                    textArea.value = text;
                    textArea.style.position = "absolute";
                    textArea.style.left = "-9999px";
                    document.body.appendChild(textArea);
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        performAnimation();
                    } catch (err) { console.error('Fallback copy failed:', err); }
                    document.body.removeChild(textArea);
                }
            };

            document.querySelectorAll('.copy-icon').forEach(icon => {
                icon.addEventListener('click', (e) => {
                    const dataToCopy = e.target.parentElement.querySelector('.detail-data').textContent;
                    copyToClipboard(dataToCopy, e.target);
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
