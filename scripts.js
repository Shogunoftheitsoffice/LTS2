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

    // --- NEW: Search Modal Logic ---
    const searchBtn = document.getElementById('search-btn');
    const searchModal = document.getElementById('search-modal');
    const closeModalBtn = document.querySelector('.modal-close');
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const searchResultsContainer = document.getElementById('search-results');

    // Function to open the modal
    const openModal = () => {
        searchModal.style.display = 'flex';
        searchInput.focus(); // Automatically focus the input field
    }

    // Function to close the modal
    const closeModal = () => {
        searchModal.style.display = 'none';
        searchResultsContainer.innerHTML = ''; // Clear results when closing
        searchInput.value = ''; // Clear input when closing
    };
    
    // --- Event Listeners ---
    searchBtn.addEventListener('click', (e) => {
        e.preventDefault(); // Prevent the link from navigating
        openModal();
    });
    
    closeModalBtn.addEventListener('click', closeModal);
    
    // Close modal if user clicks on the gray overlay area
    searchModal.addEventListener('click', (e) => {
        if (e.target === searchModal) {
            closeModal();
        }
    });

    // Close modal with the Escape key for better accessibility
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && searchModal.style.display === 'flex') {
            closeModal();
        }
    });

    // Handle the search form submission
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const searchTerm = searchInput.value.trim();

        // Provide feedback if the search term is too short
        if (searchTerm.length < 2) {
            searchResultsContainer.innerHTML = '<p class="search-message">Please enter at least 2 characters.</p>';
            return;
        }

        // Show a "Searching..." message while waiting for results
        searchResultsContainer.innerHTML = '<p class="search-message">Searching...</p>';

        // Use the Fetch API to get results from our search.php script
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

    // Function to take search data and render it as HTML
    function displaySearchResults(results) {
        if (results.length === 0) {
            searchResultsContainer.innerHTML = '<p class="search-message">No results found.</p>';
            return;
        }

        let html = '<ul>';
        results.forEach(item => {
            // Determine the status text based on the 'checkedout' field
            const status = (item.checkedout && item.checkedout.toLowerCase() === 'yes') 
                ? `<span class="status-out">Checked Out</span> to ${item.name || 'N/A'}` 
                : '<span class="status-available">Available</span>';

            html += `
                <li>
                    <div class="result-title">${item['book title'] || 'N/A'}</div>
                    <div class="result-details">
                        <span><strong>TUID:</strong> ${item.tuid || 'N/A'}</span>
                        <span><strong>Course:</strong> ${item.course || 'N/A'}</span>
                        <span><strong>Barcode:</strong> ${item.barcode || 'N/A'}</span>
                    </div>
                    <div class="result-status">${status}</div>
                </li>
            `;
        });
        html += '</ul>';
        searchResultsContainer.innerHTML = html;
    }
});
