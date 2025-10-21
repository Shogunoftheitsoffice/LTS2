document.addEventListener('DOMContentLoaded', function() {
    
    // --- UPDATED: script for details row ---
// --- UPDATED: script for details row (now with selection) ---
    document.querySelectorAll('.main-row').forEach(row => {
        row.addEventListener('click', (e) => { // Get the event object 'e'
            
            // --- First, always ignore clicks on buttons or links ---
            if (e.target.closest('button, a')) {
                return; // Do nothing
            }

            // --- NEW: Handle selection if in select mode ---
            if (document.body.classList.contains('select-mode-active')) {
                row.classList.toggle('row-selected');
                return; // Stop here, don't expand the row
            }
            // --- END NEW ---

            // If not in select mode, and not a button, expand the row
            const detailsRow = row.nextElementSibling;
            if (detailsRow && detailsRow.classList.contains('details-row')) {
                detailsRow.style.display = (detailsRow.style.display === 'table-row') ? 'none' : 'table-row';
            }
        });
    });

    // --- NEW: Select / Deselect All Logic ---
    const selectBtn = document.getElementById('select-btn');
    const deselectBtn = document.getElementById('deselect-btn');

    // 1. Logic for the 'Select' button (toggles select mode)
    selectBtn.addEventListener('click', (e) => {
        e.preventDefault();
        // Toggle the 'select-mode-active' class on the whole page
        document.body.classList.toggle('select-mode-active');

        // If we just *turned off* select mode, also clear all selections
        if (!document.body.classList.contains('select-mode-active')) {
            document.querySelectorAll('.main-row.row-selected').forEach(row => {
                row.classList.remove('row-selected');
            });
        }
    });

    // 2. Logic for the 'Deselect' button (clears all selections)
    deselectBtn.addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelectorAll('.main-row.row-selected').forEach(row => {
            row.classList.remove('row-selected');
        });
    });
    // --- END NEW: Select / Deselect ---

    // --- Search Modal Logic ---
    const searchBtn = document.getElementById('search-btn');
    const searchModal = document.getElementById('search-modal');
    const closeModalBtn = searchModal.querySelector('.modal-close');
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const searchResultsContainer = document.getElementById('search-results');

    // Function to open the modal
    const openSearchModal = () => {
        searchModal.style.display = 'flex';
        searchInput.focus();
    }

    // Function to close the modal
    const closeSearchModal = () => {
        searchModal.style.display = 'none';
        searchResultsContainer.innerHTML = '';
        searchInput.value = '';
    };
    
    searchBtn.addEventListener('click', (e) => {
        e.preventDefault();
        openSearchModal();
    });
    
    closeModalBtn.addEventListener('click', closeSearchModal);
    
    searchModal.addEventListener('click', (e) => {
        if (e.target === searchModal) {
            closeSearchModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && searchModal.style.display === 'flex') {
            closeSearchModal();
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

    function displaySearchResults(results) {
        if (results.length === 0) {
            searchResultsContainer.innerHTML = '<p class="search-message">No results found.</p>';
            return;
        }

        let html = '<ul>';
        results.forEach(item => {
            const status = (item.checkedout && item.checkedout.toLowerCase() === 'yes') 
                ? `<span class="status-out">Checked Out</span> to Professor ${item.name || 'N/A'}` 
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


    // --- Checkout Modal Logic ---
    const checkoutModal = document.getElementById('checkout-modal');
    const checkoutForm = document.getElementById('checkout-form');
    const checkoutTuidInput = document.getElementById('checkout-tuid-input');
    const checkoutMessage = document.getElementById('checkout-message');
    const checkoutCloseBtn = checkoutModal.querySelector('.modal-close');
    const mainContent = document.querySelector('.main-content'); 

    const openCheckoutModal = (bookId) => {
        checkoutModal.dataset.bookId = bookId; 
        checkoutModal.style.display = 'flex';
        checkoutTuidInput.focus();
    };

    const closeCheckoutModal = () => {
        checkoutModal.style.display = 'none';
        checkoutTuidInput.value = '';
        checkoutMessage.textContent = '';
        checkoutMessage.className = 'checkout-message';
    };

    mainContent.addEventListener('click', function(e) {
        if (e.target.classList.contains('checkout-btn')) {
            e.stopPropagation(); 
            e.preventDefault();
            const mainRow = e.target.closest('.main-row');
            const bookId = mainRow.dataset.id;
            if (bookId) {
                openCheckoutModal(bookId);
            }
        }

        if (e.target.classList.contains('return-btn')) {
            e.stopPropagation();
            e.preventDefault();

            if (confirm('Are you sure you want to return this book?')) {
                const mainRow = e.target.closest('.main-row');
                const bookId = mainRow.dataset.id;
                
                const formData = new FormData();
                formData.append('bookId', bookId);

                fetch('return.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); 
                    } else {
                        alert('Error returning book: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error during return:', error);
                    alert('A network error occurred. Please try again.');
                });
            }
        }
    });

    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const tuid = checkoutTuidInput.value.trim();
        const bookId = checkoutModal.dataset.bookId;

        if (!/^\d{9}$/.test(tuid)) {
            checkoutMessage.textContent = 'Error: TUID must be exactly 9 digits.';
            checkoutMessage.className = 'checkout-message error';
            return;
        }

        checkoutMessage.textContent = 'Processing...';
        checkoutMessage.className = 'checkout-message processing';

        const formData = new FormData();
        formData.append('bookId', bookId);
        formData.append('tuid', tuid);

        fetch('checkout.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                checkoutMessage.textContent = data.message;
                checkoutMessage.className = 'checkout-message success';
                setTimeout(() => {
                    closeCheckoutModal();
                    location.reload(); 
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

    // --- AD Info Modal Logic ---
    const adInfoModal = document.getElementById('ad-info-modal');
    const adInfoContent = document.getElementById('ad-info-content');
    const adInfoCloseBtn = adInfoModal.querySelector('.modal-close');

    const openAdModal = () => {
        adInfoModal.style.display = 'flex';
        adInfoContent.innerHTML = '<p class="ad-message">Fetching information...</p>';
    };

    const closeAdModal = () => {
        adInfoModal.style.display = 'none';
        adInfoContent.innerHTML = '';
    };

    mainContent.addEventListener('click', function(e) {
        if (e.target.classList.contains('tuid-link')) {
            e.preventDefault();
            e.stopPropagation(); // Stop event from bubbling
            const tuid = e.target.dataset.tuid;
            
            if (tuid) {
                openAdModal();

                fetch(`activearchive.php?tuid=${encodeURIComponent(tuid)}`)
                    .then(response => response.json())
                    .then(res => {
                        if (res.success && res.data) {
                            adInfoContent.innerHTML = `
                                <div class="ad-info-box">
                                    <div class="ad-info-item"><strong>TUID:</strong> ${res.data.employeeID}</div>
                                    <div class="ad-info-item"><strong>Name:</strong> ${res.data.name}</div>
                                    <div class="ad-info-item"><strong>Email:</strong> ${res.data.email}</div>
                                </div>
                            `;
                        } else {
                             adInfoContent.innerHTML = `<p class="ad-message error">${res.message}</p>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching AD info:', error);
                        adInfoContent.innerHTML = `<p class="ad-message error">A network error occurred.</p>`;
                    });
            }
        }
    });

    adInfoCloseBtn.addEventListener('click', closeAdModal);
    adInfoModal.addEventListener('click', (e) => {
        if (e.target === adInfoModal) {
            closeAdModal();
        }
    });
     document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && adInfoModal.style.display === 'flex') {
            closeAdModal();
        }
    });
    // --- END: AD Info Modal Logic ---


    searchResultsContainer.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('go-to-item-btn')) {
            const bookId = e.target.dataset.id;
            const targetRow = document.querySelector(`.main-row[data-id='${bookId}']`);

            if (targetRow) {
                closeSearchModal();
                
                targetRow.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                targetRow.classList.add('highlight');
                setTimeout(() => {
                    targetRow.classList.remove('highlight');
                }, 2500);
            } else {
                alert('Could not find the item in the list.');
            }
        }
    });
    
    function formatTime(ms) {
        let totalSeconds = Math.floor(ms / 1000);
        let hours = Math.floor(totalSeconds / 3600);
        totalSeconds %= 3600;
        let minutes = Math.floor(totalSeconds / 60);
        let seconds = totalSeconds % 60;
        const pad = (num) => String(num).padStart(2, '0');
        return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
    }

    function startCountdown(cell) {
        const returnTimeStr = cell.dataset.returnTime;
        if (!returnTimeStr) {
            cell.innerHTML = 'N/A';
            return;
        }
        const returnTime = new Date(returnTimeStr.replace(' ', 'T'));
        const timer = setInterval(() => {
            const now = new Date();
            const timeRemaining = returnTime - now;

            if (timeRemaining <= 0) {
                clearInterval(timer);
                cell.innerHTML = '<span class="status-out">OVERDUE</span>';
            } else {
                cell.innerHTML = formatTime(timeRemaining);
            }
        }, 1000);
    }

    document.querySelectorAll('.countdown-cell').forEach(startCountdown);

});
