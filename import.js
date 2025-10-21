document.addEventListener('DOMContentLoaded', function() {
    
    // --- Get all necessary elements for Import/Export ---
    const importBtn = document.getElementById('import-btn');
    const exportBtn = document.getElementById('export-btn');
    const importModal = document.getElementById('import-modal');
    
    if (importModal) {
        const importCloseBtn = importModal.querySelector('.modal-close');
        const importForm = document.getElementById('import-form');
        const importMessage = document.getElementById('import-message');
        const importSubmitBtn = document.getElementById('import-submit-btn');
        const fileInput = document.getElementById('import-file-input');
        const fileNameDisplay = document.getElementById('file-name-display');

        // --- Function to open the modal ---
        const openImportModal = () => {
            importModal.style.display = 'flex';
        };

        // --- Function to close the modal and reset the form ---
        const closeImportModal = () => {
            importModal.style.display = 'none';
            importForm.reset(); // Clear all form fields
            fileNameDisplay.textContent = 'No file chosen';
            importMessage.textContent = '';
            importMessage.className = 'modal-message';
            importSubmitBtn.disabled = false;
            importSubmitBtn.textContent = 'Upload and Import';
        };

        // --- Event Listener for sidebar "Import" button ---
        importBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openImportModal();
        });

        // --- Event Listeners to close the modal ---
        importCloseBtn.addEventListener('click', closeImportModal);

        importModal.addEventListener('click', (e) => {
            if (e.target === importModal) {
                closeImportModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && importModal.style.display === 'flex') {
                closeImportModal();
            }
        });

        // --- Show selected file name ---
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
            } else {
                fileNameDisplay.textContent = 'No file chosen';
            }
        });

        // --- Event Listener for form submission ---
        importForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            const formData = new FormData(importForm);

            // Check if a file is actually selected
            if (!fileInput.files || fileInput.files.length === 0) {
                importMessage.textContent = 'Error: Please select a file to upload.';
                importMessage.className = 'modal-message error';
                return;
            }

            // --- Show processing state ---
            importMessage.textContent = 'Processing file... This may take a moment.';
            importMessage.className = 'modal-message processing';
            importSubmitBtn.disabled = true;
            importSubmitBtn.textContent = 'Importing...';

            // --- Send data to excelimport.php using fetch ---
            fetch('excelimport.php', {
                method: 'POST',
                body: formData // FormData handles the file upload
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    importMessage.textContent = data.message;
                    importMessage.className = 'modal-message success';
                    
                    // Reload the page after a short delay to see new data
                    setTimeout(() => {
                        closeImportModal();
                        location.reload(); 
                    }, 3000);

                } else {
                    // Show error message from the server
                    importMessage.textContent = data.message || 'An unknown error occurred.';
                    importMessage.className = 'modal-message error';
                    importSubmitBtn.disabled = false;
                    importSubmitBtn.textContent = 'Upload and Import';
                }
            })
            .catch(error => {
                // Handle network errors
                console.error('Error during import:', error);
                importMessage.textContent = 'A network error occurred. Please try again.';
                importMessage.className = 'modal-message error';
                importSubmitBtn.disabled = false;
                importSubmitBtn.textContent = 'Upload and Import';
            });
        });
    }

    // --- Event Listener for sidebar "Export" button ---
    exportBtn.addEventListener('click', (e) => {
        e.preventDefault();
        // Simply navigate to the export script, which triggers a download
        window.location.href = 'excelexport.php';
    });

});
