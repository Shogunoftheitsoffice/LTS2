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
            margin: 0; /* Remove default body margin */
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6; /* Light gray background for the content area */
        }

        /* --- Main Layout Container (Flexbox) --- */
        .container {
            display: flex;
        }

        /* --- Sidebar Styles --- */
        .sidebar {
            width: 250px; /* Fixed width for the sidebar */
            height: 100vh; /* Make sidebar full screen height */
            background-color: #2c3e50; /* Dark blue/gray background */
            padding: 20px;
            box-sizing: border-box; /* Include padding in the width calculation */
            color: white;
            position: sticky; /* Stick to the top left on scroll */
            top: 0;
        }

        .sidebar .logo {
            width: 200px; /* Set logo width as requested */
            height: auto;  /* Maintain aspect ratio */
            display: block;
            margin: 0 auto 30px auto; /* Center logo and add space below */
        }

        /* --- Main Content Area --- */
        .main-content {
            flex-grow: 1; /* Allow this area to grow and fill remaining space */
            padding: 25px;
            overflow-y: auto; /* Add a scrollbar if content is too long */
        }

        /* --- Table Styles (Unchanged) --- */
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
        .book-details-row {
            display: none;
        }
        .book-details-row td {
            border-bottom: 1px solid #ddd;
            padding: 0;
        }
        .details-content {
            padding: 15px 20px 15px 45px; /* Indent content to align with title text */
            background-color: #ffffff;
            line-height: 1.6;
        }
        .details-content strong {
            display: inline-block;
            width: 150px;
            color: #555;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="sidebar">
            <img src="Assets/logo.png" alt="Company Logo" class="logo">
            
            </div>

        <div class="main-content">
            <h2>All Textbooks from LTS2 Database</h2>

            <table>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $details_id = "details-" . htmlspecialchars($row['id']);
                    ?>

                            <tr class="book-title-row" data-target="#<?php echo $details_id; ?>">
                                <td><?php echo htmlspecialchars($row['book title'] ?? 'No Title'); ?></td>
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
                        } // end while loop
                    } else {
                        echo "<tr><td>0 results found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div> </div> <script>
        document.addEventListener('DOMContentLoaded', function () {
            const titleRows = document.querySelectorAll('.book-title-row');

            titleRows.forEach(row => {
                row.addEventListener('click', () => {
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
