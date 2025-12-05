<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="create_name" name="name" required>
                                <div class="text-danger" id="create_name_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="create_email" name="email" required>
                                <div class="text-danger" id="create_email_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="create_password" name="password" required>
                                <div class="text-danger" id="create_password_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="create_password_confirmation" name="password_confirmation" required>
                                <div class="text-danger" id="create_password_confirmation_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_office_id" class="form-label">Assigned Office</label>
                                <select class="form-select" id="create_office_id" name="office_id">
                                    <option value="">No Office</option>
                                    @if(isset($offices) && $offices->count() > 0)
                                        @foreach($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="">No offices available</option>
                                    @endif
                                </select>
                                <div class="form-text">Primary office for this user</div>
                                <div class="text-danger" id="create_office_id_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Roles</label>
                        <div id="create_roles_container">
                            @if($roles->count() > 0)
                                @foreach($roles as $role)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="create_role_{{ $role->id }}">
                                    <label class="form-check-label" for="create_role_{{ $role->id }}">
                                        {{ $role->display_name }}
                                    </label>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted">No roles available. Please create roles first.</p>
                            @endif
                        </div>
                        <div class="text-danger" id="create_roles_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="createUserBtn">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>