document.addEventListener('DOMContentLoaded', function() {
    
    // --- Get all necessary elements for the 'Edit' modal ---
    const editBtn = document.getElementById('edit-btn');
    const editModal = document.getElementById('edit-modal');
    const editCloseBtn = editModal.querySelector('.modal-close');
    const editForm = document.getElementById('edit-form');
    const editMessage = document.getElementById('edit-message');
    const editSubmitBtn = document.getElementById('edit-submit-btn');

    // --- Form input fields ---
    const idInput = document.getElementById('edit-id');
    const barcodeInput = document.getElementById('edit-barcode');
    const bookIdInput = document.getElementById('edit-book-id');
    const bookTitleInput = document.getElementById('edit-book-title');
    const courseInput = document.getElementById('edit-course');
    const profNameInput = document.getElementById('edit-prof-name');
    const courseTitleInput = document.getElementById('edit-course-title');

    // --- Function to open the modal ---
    // This one is more complex, as it has to fetch data
    const openEditModal = (bookId) => {
        editModal.style.display = 'flex';
        editMessage.textContent = 'Loading item data...';
        editMessage.className = 'modal-message processing';
        editForm.style.display = 'none'; // Hide form while loading
        editSubmitBtn.disabled = true;

        // Fetch data from the server
        fetch(`get_book.php?id=${bookId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.book) {
                    // Populate the form
                    idInput.value = data.book.id;
                    barcodeInput.value = data.book.barcode || '';
                    bookIdInput.value = data.book.book || '';
                    bookTitleInput.value = data.book['book title'] || '';
                    courseInput.value = data.book.course || '';
                    profNameInput.value = data.book.name || '';
                    courseTitleInput.value = data.book['course title'] || '';

                    // Show the form
                    editForm.style.display = 'flex';
                    editMessage.textContent = '';
                    editMessage.className = 'modal-message';
                    editSubmitBtn.disabled = false;
                    barcodeInput.focus();

                } else {
                    // Show error and close modal
                    editMessage.textContent = data.message;
                    editMessage.className = 'modal-message error';
                    setTimeout(closeEditModal, 2000);
                }
            })
            .catch(error => {
                console.error('Error fetching book data:', error);
                editMessage.textContent = 'A network error occurred.';
                editMessage.className = 'modal-message error';
                setTimeout(closeEditModal, 2000);
            });
    };

    // --- Function to close the modal and reset the form ---
    const closeEditModal = () => {
        editModal.style.display = 'none';
        editForm.reset(); // Clear all form fields
        editMessage.textContent = '';
        editMessage.className = 'modal-message';
        editSubmitBtn.disabled = false;
        editSubmitBtn.textContent = 'Save Changes';
    };

    // --- Event Listener for the main "Edit" button in sidebar ---
    editBtn.addEventListener('click', (e) => {
        e.preventDefault();

        // 1. Find all selected rows
        const selectedRows = document.querySelectorAll('.main-row.row-selected');

        // 2. Check how many are selected
        if (selectedRows.length === 0) {
            alert('Please select an item to edit.');
            return;
        }

        if (selectedRows.length > 1) {
            alert('Please select only ONE item to edit.');
            return;
        }

        // 3. If exactly one is selected, get its ID and open the modal
        const bookId = selectedRows[0].dataset.id;
        openEditModal(bookId);
    });


    // --- Event Listeners to close the modal ---
    editCloseBtn.addEventListener('click', closeEditModal);

    editModal.addEventListener('click', (e) => {
        if (e.target === editModal) {
            closeEditModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && editModal.style.display === 'flex') {
            closeEditModal();
        }
    });

    // --- Event Listener for form submission ---
    editForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Stop the default form submission

        const formData = new FormData(editForm);

        // --- Basic Validation ---
        if (!formData.get('barcode') || !formData.get('book_title')) {
            editMessage.textContent = 'Error: Barcode and Book Title are required.';
            editMessage.className = 'modal-message error';
            return;
        }

        // --- Show processing state ---
        editMessage.textContent = 'Saving changes...';
        editMessage.className = 'modal-message processing';
        editSubmitBtn.disabled = true;
        editSubmitBtn.textContent = 'Saving...';

        // --- Send data to edit.php using fetch ---
        fetch('edit.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                editMessage.textContent = data.message;
                editMessage.className = 'modal-message success';
                
                // Reload the page after a short delay to see the changes
                setTimeout(() => {
                    closeEditModal();
                    location.reload(); 
                }, 1500);

            } else {
                // Show error message from the server
                editMessage.textContent = data.message || 'An unknown error occurred.';
                editMessage.className = 'modal-message error';
                editSubmitBtn.disabled = false;
                editSubmitBtn.textContent = 'Save Changes';
            }
        })
        .catch(error => {
            // Handle network errors
            console.error('Error during edit item:', error);
            editMessage.textContent = 'A network error occurred. Please try again.';
            editMessage.className = 'modal-message error';
            editSubmitBtn.disabled = false;
            editSubmitBtn.textContent = 'Save Changes';
        });
    });

});
