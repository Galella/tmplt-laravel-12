<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" role="dialog" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRoleModalLabel">Create New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createRoleForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_name" class="form-label">Role Name (system)</label>
                        <input type="text" class="form-control" id="create_name" name="name" required>
                        <div class="form-text">Use lowercase letters, numbers, and underscores only (e.g., super_admin)</div>
                        <div class="text-danger" id="create_name_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="create_display_name" class="form-label">Display Name</label>
                        <input type="text" class="form-control" id="create_display_name" name="display_name" required>
                        <div class="form-text">The name that will be displayed to users (e.g., Super Administrator)</div>
                        <div class="text-danger" id="create_display_name_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="create_description" class="form-label">Description</label>
                        <textarea class="form-control" id="create_description" name="description" rows="3"></textarea>
                        <div class="text-danger" id="create_description_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="createRoleBtn">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>