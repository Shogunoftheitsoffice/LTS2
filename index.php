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
<body>
    <div class="container">
        <h1 class="LTS-title">Library Textbook System V1.3 - Tokyo Version</h1>
        <hr class="mb-3">
        <a href="add.php" class="button btn-success" title="Add new entries to my database.">Add Entries</a>
        <a href="copy.php" class="button btn-success" title="Copy selected entries in my database.">Copy Entries</a>    
        <button onclick="deleteEntries()" class="btn-danger" title="Delete selected entries from my database.">Delete Entries</button>
        <a href="excelimport.php" class="button btn-success" title="Import entries from an .xlsx file.">Import Excel</a> 
        <a href="excelexport.php" class="button btn-success" title="Export entries to an .xlsx file.">Export Excel</a>
        <a href="table.php" class="button btn-secondary" title="Go to the just table view. To come back replace table.php with index.php in the URL.">Go To Table</a> 
        <a href="help.php" class="button btn-info" title="Submit feedback.">Help/Feedback</a> 
        <hr>
        <div id="table-container">
            <?php include 'table.php'; ?>
        </div>
    </div>
</body>
</html>
