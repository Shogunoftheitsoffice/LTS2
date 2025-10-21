<div id="add-modal" class="modal-overlay">
    <div class="modal-content">
        <button class="modal-close">&times;</button>
        <h2>Add New Textbook</h2>
        <form id="add-form" onsubmit="return false;">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="add-barcode">Item Barcode: <span class="required">*</span></label>
                    <input type="text" id="add-barcode" name="barcode" placeholder="e.g., 3403..." autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="add-book-id">Book ID:</label>
                    <input type="text" id="add-book-id" name="book" placeholder="e.g., 1001" autocomplete="off">
                </div>
                <div class="form-group-full">
                    <label for="add-book-title">Book Title: <span class="required">*</span></label>
                    <input type="text" id="add-book-title" name="book_title" placeholder="e.g., Introduction to Programming" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="add-course">Course:</label>
                    <input type="text" id="add-course" name="course" placeholder="e.g., CIS 1051" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="add-prof-name">Professor Name:</label>
                    <input type="text" id="add-prof-name" name="name" placeholder="e.g., Dr. Smith" autocomplete="off">
                </div>
                <div class="form-group-full">
                    <label for="add-course-title">Course Title:</label>
                    <input type="text" id="add-course-title" name="course_title" placeholder="e.g., Intro to Problem Solving" autocomplete="off">
                </div>
            </div>
            
            <div id="add-message" class="modal-message"></div>
            <button type="submit" id="add-submit-btn" class="modal-submit-btn">Add Item</button>
        </form>
    </div>
</div>
