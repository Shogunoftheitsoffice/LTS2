<div id="import-modal" class="modal-overlay">
    <div class="modal-content">
        <button class="modal-close">&times;</button>
        <h2>Import from Excel</h2>
        <form id="import-form" onsubmit="return false;" enctype="multipart/form-data">
            
            <p>Select an Excel file (.xlsx, .xls) to import. The file must have headers in the first row and data starting on the second.
            <br><strong>Required columns:</strong> `Barcode`, `Book Title`
            <br><strong>Optional columns:</strong> `Course`, `Course Title`, `Name`, `Book`
            </p>
            
            <div class="form-group-full">
                <label for="import-file-input" class="file-input-label">Choose File</label>
                <input type="file" id="import-file-input" name="file" accept=".xlsx, .xls, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                <span id="file-name-display">No file chosen</span>
            </div>
            
            <div id="import-message" class="modal-message"></div>
            <button type="submit" id="import-submit-btn" class="modal-submit-btn">Upload and Import</button>
        </form>
    </div>
</div>
