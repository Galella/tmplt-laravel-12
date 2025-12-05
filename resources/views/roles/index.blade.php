@extends('layouts.adminator')

@section('title', 'Role Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Role Management</h1>
        <button type="button" class="btn btn-sm btn-primary shadow-sm" id="create-role-btn" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Role
        </button>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Roles List</h6>
        </div>
        <div class="card-body">
            <div id="alert-container"></div>

            <div class="table-responsive">
                <table class="table table-bordered" id="roles-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr id="role-{{ $role->id }}">
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->display_name }}</td>
                            <td>{{ $role->description ?? 'N/A' }}</td>
                            <td>{{ $role->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('roles.show', $role->id) }}" class="btn btn-sm btn-info">View</a>
                                <button type="button" class="btn btn-sm btn-warning edit-role-btn" data-id="{{ $role->id }}">Edit</button>
                                <button type="button" class="btn btn-sm btn-danger delete-role-btn" data-id="{{ $role->id }}"
                                    @if(in_array($role->name, ['admin', 'user'])) disabled @endif>
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $roles->links() }}
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modals-container">
        <!-- Include the modals -->
        @include('roles.create-modal')
        @include('roles.edit-modal')
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // CSRF Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Create Role Button Click
        $('#create-role-btn').click(function() {
            $.ajax({
                url: '{{ route('roles.create') }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('.modals-container').html(response.html);
                        $('#createRoleModal').modal('show');
                    }
                },
                error: function(xhr) {
                    showNotification('Error loading form.', 'danger');
                }
            });
        });

        // Store New Role
        $(document).on('submit', '#createRoleForm', function(e) {
            e.preventDefault();

            // Clear previous errors
            clearErrors();

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ route('roles.store') }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if(response.success) {
                        $('#createRoleModal').modal('hide');
                        showNotification(response.message, 'success');
                        location.reload(); // Refresh the page to show updated list
                    }
                },
                error: function(xhr) {
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            $('#' + field + '_error').text(messages[0]);
                        });
                    } else {
                        showNotification('An error occurred while creating the role.', 'danger');
                    }
                }
            });
        });

        // Edit Role Button Click
        $(document).on('click', '.edit-role-btn', function() {
            let roleId = $(this).data('id');

            $.ajax({
                url: '/roles/' + roleId + '/edit',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('.modals-container').html(response.html);
                        loadEditRoleData(roleId);
                    }
                },
                error: function(xhr) {
                    showNotification('Error loading form.', 'danger');
                }
            });
        });

        // Load edit form data
        function loadEditRoleData(roleId) {
            $.ajax({
                url: '/roles/' + roleId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        let role = response.data;
                        $('#edit_role_id').val(role.id);
                        $('#edit_name').val(role.name);
                        $('#edit_display_name').val(role.display_name);
                        $('#edit_description').val(role.description);
                        $('#editRoleModal').modal('show');
                    }
                },
                error: function(xhr) {
                    showNotification('Error loading role data.', 'danger');
                }
            });
        }

        // Update Role
        $(document).on('submit', '#editRoleForm', function(e) {
            e.preventDefault();

            // Clear previous errors
            clearErrors();

            let roleId = $('#edit_role_id').val();
            let formData = $(this).serialize();

            $.ajax({
                url: '/roles/' + roleId,
                type: 'PUT',
                data: formData,
                success: function(response) {
                    if(response.success) {
                        $('#editRoleModal').modal('hide');
                        showNotification(response.message, 'success');
                        location.reload(); // Refresh the page to show updated list
                    }
                },
                error: function(xhr) {
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            $('#' + field + '_error').text(messages[0]);
                        });
                    } else {
                        showNotification('An error occurred while updating the role.', 'danger');
                    }
                }
            });
        });

        // Delete Role Button Click
        $(document).on('click', '.delete-role-btn', function() {
            let roleId = $(this).data('id');

            if(confirm('Are you sure you want to delete this role? This action cannot be undone if the role is assigned to any users.')) {
                $.ajax({
                    url: '/roles/' + roleId,
                    type: 'DELETE',
                    success: function(response) {
                        if(response.success) {
                            $('#role-' + roleId).remove();
                            showNotification(response.message, 'success');
                        } else {
                            showNotification(response.message, 'danger');
                        }
                    },
                    error: function(xhr) {
                        showNotification('An error occurred while deleting the role.', 'danger');
                    }
                });
            }
        });

        // Show notification
        function showNotification(message, type) {
            let alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            $('#alert-container').html(alertHtml);
        }

        // Clear errors
        function clearErrors() {
            $('[id$="_error"]').text('');
        }
    });
</script>
@endsection