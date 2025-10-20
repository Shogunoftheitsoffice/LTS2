document.addEventListener('DOMContentLoaded', function() {
    
    // --- Existing script for details row ---
    document.querySelectorAll('.main-row').forEach(row => {
        row.addEventListener('click', () => {
            const detailsRow = row.nextElementSibling;
            if (detailsRow && detailsRow.classList.contains('details-row')) {
                detailsRow.style.display = (detailsRow.style.display === 'table-row') ? 'none' : 'table-row';
            }
        });
    });

    // --- Search Modal Logic ---
    const searchBtn = document.getElementById('search-btn');
    const searchModal = document.getElementById('search-modal');
    const closeModalBtn = document.querySelector('.modal-close');
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const searchResultsContainer = document.getElementById('search-results');

    // Function to open the modal
    const openModal = () => {
        searchModal.style.display = 'flex';
        searchInput.focus();
    }

    // Function to close the modal
    const closeModal = () => {
        searchModal.style.display = 'none';
        searchResultsContainer.innerHTML = '';
        searchInput.value = '';
    };
    
    // --- Event Listeners ---
    searchBtn.addEventListener('click', (e) => {
        e.preventDefault();
        openModal();
    });
    
    closeModalBtn.addEventListener('click', closeModal);
    
    searchModal.addEventListener('click', (e) => {
        if (e.target === searchModal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && searchModal.style.display === 'flex') {
            closeModal();
        }
    });

    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const searchTerm = searchInput.value.trim();

        if (searchTerm.length < 2) {
            searchResultsContainer.innerHTML = '<p class="search-message">Please enter at least 2 characters.</p>';
            return;
        }

        searchResultsContainer.innerHTML = '<p class="search-message">Searching...</p>';

        fetch(`search.php?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                displaySearchResults(data);
            })
            .catch(error => {
                console.error('Error fetching search results:', error);
                searchResultsContainer.innerHTML = '<p class="search-message">An error occurred. Please try again.</p>';
            });
    });

    // Function to display search results
    function displaySearchResults(results) {
        if (results.length === 0) {
            searchResultsContainer.innerHTML = '<p class="search-message">No results found.</p>';
            return;
        }

        let html = '<ul>';
        results.forEach(item => {
            const status = (item.checkedout && item.checkedout.toLowerCase() === 'yes') 
                ? `<span class="status-out">Checked Out</span> to ${item.name || 'N/A'}` 
                : '<span class="status-available">Available</span>';

            html += `
                <li>
                    <div class="result-main-content">
                        <div class="result-title">${item['book title'] || 'N/A'}</div>
                        <div class="result-details">
                            <span><strong>TUID:</strong> ${item.tuid || 'N/A'}</span>
                            <span><strong>Course:</strong> ${item.course || 'N/A'}</span>
                            <span><strong>Barcode:</strong> ${item.barcode || 'N/A'}</span>
                        </div>
                        <div class="result-status">${status}</div>
                    </div>
                    <div class="result-action">
                        <button class="go-to-item-btn" data-id="${item.id}">Go to Item</button>
                    </div>
                </li>
            `;
        });
        html += '</ul>';
        searchResultsContainer.innerHTML = html;
    }


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
    // --- END: Checkout Modal Logic ---


    // --- Existing: Event listener for "Go to Item" buttons ---
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
