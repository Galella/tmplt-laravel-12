<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_user_id" name="user_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                                <div class="text-danger" id="edit_name_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                                <div class="text-danger" id="edit_email_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_password" class="form-label">New Password (Leave blank to keep current)</label>
                                <input type="password" class="form-control" id="edit_password" name="password">
                                <div class="text-danger" id="edit_password_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation">
                                <div class="text-danger" id="edit_password_confirmation_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_office_id" class="form-label">Assigned Office</label>
                                <select class="form-select" id="edit_office_id" name="office_id">
                                    <option value="">No Office</option>
                                    @if(isset($offices) && $offices->count() > 0)
                                        @foreach($offices as $office)
                                        <option value="{{ $office->id }}" {{ old('office_id', $user->office_id) == $office->id ? 'selected' : '' }}>
                                            {{ $office->name }}
                                        </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-text">Primary office for this user</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Roles</label>
                        <div id="edit_roles_container">
                            @if($roles->count() > 0)
                                @foreach($roles as $role)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="edit_role_{{ $role->id }}"
                                           {{ in_array($role->id, $userRoleIds ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="edit_role_{{ $role->id }}">
                                        {{ $role->display_name }}
                                    </label>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted">No roles available. Please create roles first.</p>
                            @endif
                        </div>
                        <div class="text-danger" id="edit_roles_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateUserBtn">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>