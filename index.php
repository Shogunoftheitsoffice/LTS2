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

        /* --- Sidebar Styles (Unchanged) --- */
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
        .sidebar-nav { list-style: none; padding: 0; margin: 0; }
        .sidebar-nav .nav-button {
            display: flex; align-items: center; padding: 12px 15px;
            text-decoration: none; color: #333; font-weight: bold;
            background-color: #f9f9f9; border-bottom: 1px solid #ddd;
            transition: background-color 0.2s, color 0.2s;
        }
        .sidebar-nav .nav-button:hover { background-color: #e9e9e9; color: #333; }
        .nav-icon { width: 20px; height: 20px; margin-right: 12px; }

        /* --- Main Content Area --- */
        .main-content {
            flex-grow: 1;
            padding: 25px;
            overflow-y: auto;
        }

        /* --- NEW: Card Grid Layout --- */
        .card-grid {
            display: grid;
            /* Create as many columns as can fit, with a minimum width of 380px each */
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 20px; /* Space between cards */
        }
        
        /* --- NEW: Individual Card Styling --- */
        .book-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px; /* Rounded corners for modern look */
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .card-title {
            font-weight: bold;
            font-size: 1.1em;
            margin: 0;
        }
        .card-content { line-height: 1.6; }

        /* --- Detail & Copy Icon Styles (Unchanged) --- */
        .detail-item { display: flex; align-items: center; padding: 4px 0; }
        .copy-icon { width: 16px; height: 16px; margin-right: 8px; cursor: pointer; opacity: 1; }
        .detail-item strong { display: inline-block; width: 150px; color: #9d2235; flex-shrink: 0; }
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
            <div class="card-grid">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                ?>
                        <div class="book-card">
                            <div class="card-header">
                                <p class="card-title"><?php echo htmlspecialchars($row['book title'] ?? 'No Title'); ?></p>
                                <input type="checkbox" class="row-checkbox">
                            </div>
                            <div class="card-content">
                                <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong>TUID:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['tuid'] ?? 'N/A'); ?></span></div>
                                <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong>Course:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['course'] ?? 'N/A'); ?></span></div>
                                <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong>Course Title:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['course title'] ?? 'N/A'); ?></span></div>
                                <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong>Name:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?></span></div>
                                <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong>Checked Out:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['checkedout'] ?? 'N/A'); ?></span></div>
                                <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong>Last Checkout:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['last checkout'] ?? 'N/A'); ?></span></div>
                                <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong>Expected Return:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['expected return'] ?? 'N/A'); ?></span></div>
                                <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong>Barcode:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['barcode'] ?? 'N/A'); ?></span></div>
                                <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong>Book:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['book'] ?? 'N/A'); ?></span></div>
                                <div class="detail-item"><img src="Assets/copy.png" class="copy-icon" alt="Copy"><strong>ID:</strong> <span class="detail-data"><?php echo htmlspecialchars($row['id'] ?? 'N/A'); ?></span></div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p>0 results found</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // UPDATED: The expand/collapse functionality is no longer needed.
            
            // Function to copy text to clipboard with animation
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
                    } catch (err) {
                        console.error('Fallback copy failed:', err);
                    }
                    document.body.removeChild(textArea);
                }
            };

            // Attach event listener to all copy icons
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
