document.addEventListener('DOMContentLoaded', function() {
    
    // --- Get all necessary elements for the 'Add' modal ---
    const addBtn = document.getElementById('add-btn');
    const addModal = document.getElementById('add-modal');
    const addCloseBtn = addModal.querySelector('.modal-close');
    const addForm = document.getElementById('add-form');
    const addMessage = document.getElementById('add-message');
    const addSubmitBtn = document.getElementById('add-submit-btn');

    // --- Function to open the modal ---
    const openAddModal = () => {
        addModal.style.display = 'flex';
        // Focus the first input field
        addForm.querySelector('input[name="barcode"]').focus();
    };

    // --- Function to close the modal and reset the form ---
    const closeAddModal = () => {
        addModal.style.display = 'none';
        addForm.reset(); // Clear all form fields
        addMessage.textContent = '';
        addMessage.className = 'modal-message';
        addSubmitBtn.disabled = false;
        addSubmitBtn.textContent = 'Add Item';
    };

    // --- Event Listeners to open/close the modal ---
    addBtn.addEventListener('click', (e) => {
        e.preventDefault();
        openAddModal();
    });

    addCloseBtn.addEventListener('click', closeAddModal);

    addModal.addEventListener('click', (e) => {
        if (e.target === addModal) {
            closeAddModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && addModal.style.display === 'flex') {
            closeAddModal();
        }
    });

    // --- Event Listener for form submission ---
    addForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Stop the default form submission

        // Get all form data at once
        const formData = new FormData(addForm);

        // --- Basic Validation (Example) ---
        // You can make this more complex if needed
        const barcode = formData.get('barcode');
        const bookTitle = formData.get('book_title');

        if (!barcode || !bookTitle) {
            addMessage.textContent = 'Error: Barcode and Book Title are required.';
            addMessage.className = 'modal-message error';
            return;
        }

        // --- Show processing state ---
        addMessage.textContent = 'Adding item...';
        addMessage.className = 'modal-message processing';
        addSubmitBtn.disabled = true;
        addSubmitBtn.textContent = 'Adding...';

        // --- Send data to add.php using fetch ---
        fetch('add.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addMessage.textContent = data.message;
                addMessage.className = 'modal-message success';
                
                // Reload the page after a short delay to see the new item
                setTimeout(() => {
                    closeAddModal();
                    location.reload(); 
                }, 1500);

            } else {
                // Show error message from the server
                addMessage.textContent = data.message || 'An unknown error occurred.';
                addMessage.className = 'modal-message error';
                addSubmitBtn.disabled = false;
                addSubmitBtn.textContent = 'Add Item';
            }
        })
        .catch(error => {
            // Handle network errors
            console.error('Error during add item:', error);
            addMessage.textContent = 'A network error occurred. Please try again.';
            addMessage.className = 'modal-message error';
            addSubmitBtn.disabled = false;
            addSubmitBtn.textContent = 'Add Item';
        });
    });

});
