<!-- Blur backdrop -->
<div id="modalBackdrop"></div>

<!-- Confirm modal -->
<div id="confirmModal">
    <div class="modal-card">
        <div class="modal-icon" id="modalIcon">!</div>
        <div class="modal-title" id="modalTitle">Confirm</div>
        <div class="modal-message" id="modalMessage">Are you sure?</div>
        <!-- Optional cancel note textarea (shown only when needed) -->
        <div id="modalNoteWrap" style="display:none; width:100%; margin-top:10px;">
            <textarea id="modalNoteInput" class="form-control"
                      placeholder="Cancellation reason goes here..."
                      rows="3" style="width:100%; resize:vertical;"></textarea>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeModal()">No</button>
            <button class="btn btn-danger" id="modalConfirmBtn" onclick="confirmAction()">Yes</button>
        </div>
    </div>
</div>
