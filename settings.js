document.addEventListener('DOMContentLoaded', function() {
    
    // --- Get all necessary elements for the 'Settings' modal ---
    const settingsBtn = document.getElementById('settings-btn');
    const settingsModal = document.getElementById('settings-modal');
    
    if (settingsModal) {
        const settingsCloseBtn = settingsModal.querySelector('.modal-close');
        const deleteAllBtn = document.getElementById('delete-all-btn');
        const deleteAllMessage = document.getElementById('delete-all-message');

        // --- Function to open the modal ---
        const openSettingsModal = () => {
            settingsModal.style.display = 'flex';
            // Reset message
            deleteAllMessage.textContent = '';
            deleteAllMessage.className = 'modal-message';
            deleteAllBtn.disabled = false;
        };

        // --- Function to close the modal ---
        const closeSettingsModal = () => {
            settingsModal.style.display = 'none';
        };

        // --- Event Listener for sidebar "Settings" button ---
        settingsBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openSettingsModal();
        });

        // --- Event Listeners to close the modal ---
        settingsCloseBtn.addEventListener('click', closeSettingsModal);

        settingsModal.addEventListener('click', (e) => {
            if (e.target === settingsModal) {
                closeSettingsModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && settingsModal.style.display === 'flex') {
                closeSettingsModal();
            }
        });

        // --- Event Listener for "Delete All" button ---
        deleteAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // First confirmation (simple click)
            if (!confirm('Are you ABSOLUTELY sure you want to delete ALL items?\nThis action cannot be undone.')) {
                return;
            }

            // Second, typed confirmation for safety
            const confirmationText = prompt('This is your final warning.\nTo confirm, please type "DELETE ALL" in the box below:');

            if (confirmationText !== 'DELETE ALL') {
                alert('Confirmation text did not match. Deletion cancelled.');
                return;
            }

            // --- Processing ---
            deleteAllMessage.textContent = 'Processing...';
            deleteAllMessage.className = 'modal-message processing';
            deleteAllBtn.disabled = true;

            const formData = new FormData();
            formData.append('confirmation', confirmationText);

            fetch('delete_all.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    deleteAllMessage.textContent = data.message;
                    deleteAllMessage.className = 'modal-message success';
                    // Reload the page to show empty tables
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    deleteAllMessage.textContent = data.message || 'An unknown error occurred.';
                    deleteAllMessage.className = 'modal-message error';
                    deleteAllBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error during delete all:', error);
                deleteAllMessage.textContent = 'A network error occurred. Please try again.';
                deleteAllMessage.className = 'modal-message error';
                deleteAllBtn.disabled = false;
            });
        });
    }
});
