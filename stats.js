document.addEventListener('DOMContentLoaded', function() {
    
    // --- Get all necessary elements for the 'Stats' modal ---
    const statsBtn = document.getElementById('stats-btn');
    const statsModal = document.getElementById('stats-modal');
    
    // Check if modal exists before adding listeners
    if (statsModal) {
        const statsCloseBtn = statsModal.querySelector('.modal-close');
        const statsContent = document.getElementById('stats-content');

        // --- Function to open the modal and fetch stats ---
        const openStatsModal = () => {
            statsModal.style.display = 'flex';
            // Show loading message while fetching
            statsContent.innerHTML = '<p class="modal-message processing">Loading stats...</p>';
            
            fetch('stats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.stats) {
                        displayStats(data.stats);
                    } else {
                        statsContent.innerHTML = `<p class="modal-message error">${data.message || 'Could not load stats.'}</p>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching stats:', error);
                    statsContent.innerHTML = '<p class="modal-message error">A network error occurred.</p>';
                });
        };

        // --- Function to build and display the stats HTML ---
        const displayStats = (stats) => {
            statsContent.innerHTML = `
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-value">${stats.total_books}</span>
                        <span class="stat-label">Total Books</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">${stats.checked_out}</span>
                        <span class="stat-label">Checked Out</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">${stats.available}</span>
                        <span class="stat-label">Available</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">${stats.overdue}</span>
                        <span class="stat-label">Currently Overdue</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">${stats.total_checkouts}</span>
                        <span class="stat-label">Total All-Time C/Os</span>
                    </div>
                    <div class="stat-item wide">
                        <span class="stat-label">Most Popular Book</span>
                        <span class="stat-value-small">${stats.most_popular.title}</span>
                        <span class="stat-label-small">(${stats.most_popular.count} checkouts)</span>
                    </div>
                </div>
            `;
        };

        // --- Function to close the modal ---
        const closeStatsModal = () => {
            statsModal.style.display = 'none';
        };

        // --- Event Listener for sidebar "Stats" button ---
        statsBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openStatsModal();
        });

        // --- Event Listeners to close the modal ---
        statsCloseBtn.addEventListener('click', closeStatsModal);

        statsModal.addEventListener('click', (e) => {
            if (e.target === statsModal) {
                closeStatsModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && statsModal.style.display === 'flex') {
                closeStatsModal();
            }
        });
    }
});
