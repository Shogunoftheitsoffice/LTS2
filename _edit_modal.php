<div id="edit-modal" class="modal-overlay">
    <div class="modal-content">
        <button class="modal-close">&times;</button>
        <h2>Edit Textbook</h2>
        <form id="edit-form" onsubmit="return false;">
            
            <input type="hidden" id="edit-id" name="id">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="edit-barcode">Item Barcode: <span class="required">*</span></label>
                    <input type="text" id="edit-barcode" name="barcode" placeholder="e.g., 3403..." autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="edit-book-id">Book ID:</label>
                    <input type="text" id="edit-book-id" name="book" placeholder="e.g., 1001" autocomplete="off">
                </div>
                <div class="form-group-full">
                    <label for="edit-book-title">Book Title: <span class="required">*</span></label>
                    <input type="text" id="edit-book-title" name="book_title" placeholder="e.g., Introduction to Programming" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="edit-course">Course:</label>
                    <input type="text" id="edit-course" name="course" placeholder="e.g., CIS 1051" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="edit-prof-name">Professor Name:</label>
                    <input type="text" id="edit-prof-name" name="name" placeholder="e.g., Dr. Smith" autocomplete="off">
                </div>
                <div class="form-group-full">
                    <label for="edit-course-title">Course Title:</label>
                    <input type="text" id="edit-course-title" name="course_title" placeholder="e.g., Intro to Problem Solving" autocomplete="off">
                </div>
            </div>
            
            <div id="edit-message" class="modal-message"></div>
            <button type="submit" id="edit-submit-btn" class="modal-submit-btn">Save Changes</button>
        </form>
    </div>
</div>
