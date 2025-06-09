<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LTS</title>
    <script>
        function deleteEntries() {
            // Ask for confirmation
            var confirmDelete = confirm("Are you sure you want to delete the selected entries?");
            if (confirmDelete) {
                // Make an AJAX call to delete.php
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "delete.php", true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // If delete.php executed successfully, you can handle the response here
                            console.log(xhr.responseText);
                            // For example, you can reload the table.php content to reflect changes
                            document.getElementById("table-container").innerHTML = xhr.responseText;
                            // Refresh the page after 4 seconds
                            setTimeout(function(){
                                location.reload();
                            }, 4000);
                        } else {
                            // Handle errors if any
                            console.error("Error:", xhr.statusText);
                        }
                    }
                };
                xhr.send();
            }
        }
    </script>
</head>
<body>‎ 
    <a href="add.php"><button title="Add new entries to my databse.">Add Entries</button></a>
    <a href="copy.php"><button title="Copy selected entries in my database.">Copy Entries</button></a>    
    <button onclick="deleteEntries()" title="Delete selected entries from my database.">Delete Entries</button>
    <a href="excelimport.php"><button title="Import entries from an .xlsx file.">Import Excel</button></a> 
     <a href="excelexport.php"><button title="Export entries to an .xlsx file.">Export Excel</button></a>
    <a href="table.php"><button title="Go to the just table view. To come back replace table.php with index.php in the URL.">Go To Table</button></a> 
    <a href="help.php"><button title="Submit feedback.">Help/Feedback</button></a> 
    <strong>L</strong>ibrary <strong>T</strong>extbook <strong>S</strong>ystem <strong>V1.3   </strong><strong>Tokyo Version</strong>
    <hr>
    <div id="table-container">
        <?php include 'table.php'; ?>
    </div>
</body>
</html>
