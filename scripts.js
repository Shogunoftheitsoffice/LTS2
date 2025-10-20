document.addEventListener('DOMContentLoaded', function() {
    
    // --- Existing script for details row ---
    document.querySelectorAll('.main-row').forEach(row => {
        // ... (your existing code for this) ...
    });

    // --- Search Modal Logic ---
    const searchBtn = document.getElementById('search-btn');
    const searchModal = document.getElementById('search-modal');
    // ... (all your existing search modal JS code) ...
    // ...
    // ... (end of existing search modal JS code) ...


    // --- NEW: Checkout Modal Logic ---
    const checkoutModal = document.getElementById('checkout-modal');
    const checkoutForm = document.getElementById('checkout-form');
    const checkoutTuidInput = document.getElementById('checkout-tuid-input');
    const checkoutMessage = document.getElementById('checkout-message');
    const checkoutCloseBtn = checkoutModal.querySelector('.modal-close');
    const mainContent = document.querySelector('.main-content'); // Get the table container

    // Function to open the checkout modal
    const openCheckoutModal = (bookId) => {
        checkoutModal.dataset.bookId = bookId; // Store the book ID on the modal
        checkoutModal.style.display = 'flex';
        checkoutTuidInput.focus();
    };

    // Function to close the checkout modal
    const closeCheckoutModal = () => {
        checkoutModal.style.display = 'none';
        checkoutTuidInput.value = '';
        checkoutMessage.textContent = '';
        checkoutMessage.className = 'checkout-message';
    };

    // Event Delegation for all "Checkout" buttons
    mainContent.addEventListener('click', function(e) {
        if (e.target.classList.contains('checkout-btn')) {
            e.stopPropagation(); // Stop the row-click event
            e.preventDefault();
            
            const mainRow = e.target.closest('.main-row');
            const bookId = mainRow.dataset.id;
            
            if (bookId) {
                openCheckoutModal(bookId);
            }
        }
    });

    // Handle checkout form submission
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const tuid = checkoutTuidInput.value.trim();
        const bookId = checkoutModal.dataset.bookId;

        // Validation
        if (!/^\d{9}$/.test(tuid)) {
            checkoutMessage.textContent = 'Error: TUID must be exactly 9 digits.';
            checkoutMessage.className = 'checkout-message error';
            return;
        }

        checkoutMessage.textContent = 'Processing...';
        checkoutMessage.className = 'checkout-message processing';

        // Prepare data to send
        const formData = new FormData();
        formData.append('bookId', bookId);
        formData.append('tuid', tuid);

        // Send data to checkout.php
        fetch('checkout.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                checkoutMessage.textContent = data.message;
                checkoutMessage.className = 'checkout-message success';
                
                // On success, close modal and reload the page to see the update
                setTimeout(() => {
                    closeCheckoutModal();
                    location.reload(); // Easiest way to move the book to the "Checked Out" list
                }, 1500);

            } else {
                checkoutMessage.textContent = data.message || 'An unknown error occurred.';
                checkoutMessage.className = 'checkout-message error';
            }
        })
        .catch(error => {
            console.error('Error during checkout:', error);
            checkoutMessage.textContent = 'A network error occurred. Please try again.';
            checkoutMessage.className = 'checkout-message error';
        });
    });

    // Listeners to close the checkout modal
    checkoutCloseBtn.addEventListener('click', closeCheckoutModal);

    checkoutModal.addEventListener('click', (e) => {
        if (e.target === checkoutModal) {
            closeCheckoutModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && checkoutModal.style.display === 'flex') {
            closeCheckoutModal();
        }
    });
    
    // --- NEW: Event listener for "Go to Item" buttons ---
    searchResultsContainer.addEventListener('click', function(e) {
        // Check if a 'go-to-item-btn' was clicked
        if (e.target && e.target.classList.contains('go-to-item-btn')) {
            const bookId = e.target.dataset.id;
            const targetRow = document.querySelector(`.main-row[data-id='${bookId}']`);

            if (targetRow) {
                closeModal();
                
                // Scroll the found row into the middle of the screen
                targetRow.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Add a temporary highlight effect
                targetRow.classList.add('highlight');
                // Remove the highlight after 2.5 seconds
                setTimeout(() => {
                    targetRow.classList.remove('highlight');
                }, 2500);
            } else {
                alert('Could not find the item in the list.');
            }
        }
    });
});

