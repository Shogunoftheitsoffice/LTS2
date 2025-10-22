document.addEventListener('DOMContentLoaded', function() {
    
    // --- Manual Checkout Form Logic ---
    const manualForm = document.getElementById('manual-checkout-form');
    const tuidInput = document.getElementById('manual-tuid-input');
    const barcodeInput = document.getElementById('manual-barcode-input');
    const checkoutMessage = document.getElementById('manual-checkout-message');
    const checkoutBtn = document.getElementById('manual-checkout-btn');

    if (manualForm) {
        tuidInput.focus(); // Auto-focus on page load

        manualForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const tuid = tuidInput.value.trim();
            const barcode = barcodeInput.value.trim();

            if (!/^\d{9}$/.test(tuid)) {
                checkoutMessage.textContent = 'Error: TUID must be exactly 9 digits.';
                checkoutMessage.className = 'modal-message error';
                return;
            }
            if (barcode.length === 0) {
                checkoutMessage.textContent = 'Error: Barcode cannot be empty.';
                checkoutMessage.className = 'modal-message error';
                return;
            }

            checkoutMessage.textContent = 'Processing...';
            checkoutMessage.className = 'modal-message processing';
            checkoutBtn.disabled = true;

            const formData = new FormData();
            formData.append('tuid', tuid);
            formData.append('barcode', barcode);

            fetch('checkout.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    checkoutMessage.textContent = data.message;
                    checkoutMessage.className = 'modal-message success';
                    setTimeout(() => { location.reload(); }, 1500);
                } else {
                    checkoutMessage.textContent = data.message || 'An unknown error occurred.';
                    checkoutMessage.className = 'modal-message error';
                    checkoutBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error during checkout:', error);
                checkoutMessage.textContent = 'A network error occurred. Please try again.';
                checkoutMessage.className = 'modal-message error';
                checkoutBtn.disabled = false;
            });
        });

        // Auto-focus barcode after TUID is entered
        tuidInput.addEventListener('input', function() {
            if (tuidInput.value.length === 9) {
                barcodeInput.focus();
            }
        });
    }

    // --- Row Click (Expand / Select) Logic ---
    document.querySelectorAll('.main-row').forEach(row => {
        row.addEventListener('click', (e) => {
            if (e.target.closest('button, a')) {
                return; 
            }
            if (document.body.classList.contains('select-mode-active')) {
                row.classList.toggle('row-selected');
                return; 
            }
            const detailsRow = row.nextElementSibling;
            if (detailsRow && detailsRow.classList.contains('details-row')) {
                detailsRow.style.display = (detailsRow.style.display === 'table-row') ? 'none' : 'table-row';
            }
        });
    });

    // --- Select / Deselect All Logic ---
    const selectBtn = document.getElementById('select-btn');
    const deselectBtn = document.getElementById('deselect-btn');

    selectBtn.addEventListener('click', (e) => {
        e.preventDefault();
        document.body.classList.toggle('select-mode-active');
        if (!document.body.classList.contains('select-mode-active')) {
            document.querySelectorAll('.main-row.row-selected').forEach(row => {
                row.classList.remove('row-selected');
            });
        }
    });

    deselectBtn.addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelectorAll('.main-row.row-selected').forEach(row => {
            row.classList.remove('row-selected');
        });
    });

    // --- Close All Details Logic ---
    const closeAllBtn = document.getElementById('close-all-btn');
    if (closeAllBtn) {
        closeAllBtn.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('.details-row').forEach(row => {
                row.style.display = 'none';
            });
        });
    }
    
    // --- Table Sorting Logic ---
    function getSortValue(row, colIndex, sortKey) {
        if (sortKey === 'time-remaining') {
            const cell = row.querySelector('.countdown-cell');
            const time = new Date(cell.dataset.returnTime.replace(' ', 'T') || 0).getTime();
            return isNaN(time) ? 0 : time;
        }
        
        if (sortKey === 'last-checkout' || sortKey === 'expected-return') {
            const val = row.children[colIndex].textContent;
            const time = new Date(val.replace(' ', 'T') || 0).getTime();
            return isNaN(time) ? 0 : time;
        }

        const val = row.children[colIndex].textContent.trim();

        if (sortKey === 'book-id' || sortKey === 'barcode' || sortKey === 'tuid') {
            const num = parseFloat(val);
            if (isNaN(num)) {
                return Infinity; 
            }
            return num;
        }
        return val.toLowerCase();
    }

    function sortTable(table, th, colIndex) {
        const tbody = table.querySelector('tbody');
        const sortKey = th.dataset.sort;
        const isAscending = th.dataset.order === 'desc';
        th.dataset.order = isAscending ? 'asc' : 'desc';

        table.querySelectorAll('.sortable').forEach(header => {
            if (header !== th) {
                delete header.dataset.order;
            }
        });

        const rows = Array.from(tbody.querySelectorAll('tr.main-row'));
        const rowGroups = rows.map(row => [row, row.nextElementSibling]);

        rowGroups.sort((groupA, groupB) => {
            const valA = getSortValue(groupA[0], colIndex, sortKey);
            const valB = getSortValue(groupB[0], colIndex, sortKey);
            if (valA < valB) return isAscending ? -1 : 1;
            if (valA > valB) return isAscending ? 1 : -1;
            return 0;
        });

        rowGroups.forEach(group => {
            tbody.appendChild(group[0]);
            tbody.appendChild(group[1]);
        });
    }

    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', () => {
            const table = header.closest('table');
            const colIndex = Array.from(header.parentNode.children).indexOf(header);
            sortTable(table, header, colIndex);
        });
    });

    // --- Search Modal Logic ---
    const searchBtn = document.getElementById('search-btn');
    const searchModal = document.getElementById('search-modal');
    const closeModalBtn = searchModal.querySelector('.modal-close');
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const searchResultsContainer = document.getElementById('search-results');

    const openSearchModal = () => {
        searchModal.style.display = 'flex';
        searchInput.focus();
    }
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

    searchResultsContainer.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('go-to-item-btn')) {
            const bookId = e.target.dataset.id;
            const targetRow = document.querySelector(`.main-row[data-id='${bookId}']`);
            if (targetRow) {
                closeSearchModal();
                targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                targetRow.classList.add('highlight');
                setTimeout(() => {
                    targetRow.classList.remove('highlight');
                }, 2500);
            } else {
                alert('Could not find the item in the list.');
            }
        }
    });
    
    // --- Clipboard Fallback Function ---
    function copyEmailFallback(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.top = 0;
        textArea.style.left = 0;
        textArea.style.opacity = 0;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        let success = false;
        try {
            success = document.execCommand('copy');
        } catch (err) {
            console.error('Fallback copy failed', err);
        }
        document.body.removeChild(textArea);
        return success;
    }

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

    // This is the close button click
    adInfoCloseBtn.addEventListener('click', closeAdModal);

    // This is for clicking the modal background
    adInfoModal.addEventListener('click', (e) => {
        if (e.target === adInfoModal) {
            closeAdModal();
        }
        // Copy Email Button Logic (with fallback)
        if (e.target.classList.contains('copy-email-btn')) {
            const email = e.target.dataset.email;
            const button = e.target;

            const updateButton = (success) => {
                if (success) {
                    button.textContent = 'Copied!';
                    button.disabled = true;
                    setTimeout(() => {
                        button.textContent = 'Copy';
                        button.disabled = false;
                    }, 2000);
                } else {
                    alert('Failed to copy email.');
                }
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(email).then(() => {
                    updateButton(true);
                }).catch(err => {
                    console.error('Failed to copy email: ', err);
                    updateButton(false);
                });
            } else {
                console.warn('Using fallback copy method. Consider using HTTPS.');
                const success = copyEmailFallback(email);
                updateButton(success);
            }
        }
    });
     
    // --- Main Content Click Handler (Return Button + TUID Link) ---
    const mainContent = document.querySelector('.main-content'); 
    mainContent.addEventListener('click', function(e) {
        
        // Return Button Logic
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

        // TUID Link Logic
        if (e.target.classList.contains('tuid-link')) {
            e.preventDefault();
            e.stopPropagation();
            const tuid = e.target.dataset.tuid;
            
            if (tuid) {
                openAdModal();
                fetch(`activearchive.php?tuid=${encodeURIComponent(tuid)}`)
                    .then(response => response.json())
                    .then(res => {
                        if (res.success && res.data) {
                            const email = res.data.email || 'N/A';
                            adInfoContent.innerHTML = `
                                <div class="ad-info-box">
                                    <div class="ad-info-item"><strong>TUID:</strong> ${res.data.employeeID}</div>
                                    <div class="ad-info-item"><strong>Name:</strong> ${res.data.name}</div>
                                    <div class="ad-info-item ad-info-with-button">
                                        <span><strong>Email:</strong> ${email}</span>
                                        ${email !== 'N/A' ? `<button class="copy-email-btn" data-email="${email}">Copy</button>` : ''}
                                    </div>
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

    // --- Global Keydown Listener (for all modals) ---
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (searchModal.style.display === 'flex') {
                closeSearchModal();
            }
            if (adInfoModal.style.display === 'flex') {
                closeAdModal();
            }
            // Add other modals here if they exist
            const addModal = document.getElementById('add-modal');
            if (addModal && addModal.style.display === 'flex') {
                addModal.querySelector('.modal-close').click(); // Triggers its own close logic
            }
            const editModal = document.getElementById('edit-modal');
            if (editModal && editModal.style.display === 'flex') {
                editModal.querySelector('.modal-close').click();
            }
            const importModal = document.getElementById('import-modal');
            if (importModal && importModal.style.display === 'flex') {
                importModal.querySelector('.modal-close').click();
            }
            const statsModal = document.getElementById('stats-modal');
            if (statsModal && statsModal.style.display === 'flex') {
                statsModal.querySelector('.modal-close').click();
            }
            const settingsModal = document.getElementById('settings-modal');
            if (settingsModal && settingsModal.style.display === 'flex') {
                settingsModal.querySelector('.modal-close').click();
            }
        }
    });
    
    // --- Countdown Timer Logic ---
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

    // --- Delete Logic ---
    const deleteBtn = document.getElementById('delete-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const selectedRows = document.querySelectorAll('.main-row.row-selected');
            if (selectedRows.length === 0) {
                alert('Please select one or more items to delete.');
                return;
            }
            const idsToDelete = [];
            selectedRows.forEach(row => {
                idsToDelete.push(row.dataset.id);
            });

            const itemText = selectedRows.length === 1 ? 'item' : 'items';
            if (confirm(`Are you sure you want to permanently delete these ${selectedRows.length} ${itemText}?`)) {
                const formData = new FormData();
                idsToDelete.forEach(id => {
                    formData.append('ids[]', id);
                });
                fetch('delete.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload(); 
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error during deletion:', error);
                    alert('A network error occurred. Please try again.');
                });
            }
        });
    }

    // --- Admin/User Mode Toggle ---
    const exitBtn = document.getElementById('exit-btn');
    const adminLoginBtn = document.getElementById('admin-login-btn');

    if (exitBtn) {
        exitBtn.addEventListener('click', (e) => {
            e.preventDefault();
            document.body.classList.remove('admin-mode-active');
        });
    }

    if (adminLoginBtn) {
        adminLoginBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const password = prompt('Please enter the admin password:');
            if (password === 'admin123') { 
                document.body.classList.add('admin-mode-active');
            } else if (password !== null) {
                alert('Incorrect password.');
            }
        });
    }

}); // This is the VERY LAST line of your file
