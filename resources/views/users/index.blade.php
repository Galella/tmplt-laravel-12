@extends('layouts.adminator')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Management</h1>
        <button type="button" class="btn btn-sm btn-primary shadow-sm" id="create-user-btn" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New User
        </button>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
        </div>
        <div class="card-body">
            <div id="alert-container"></div>

            <div class="table-responsive">
                <table class="table table-bordered" id="users-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Assigned Office</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr id="user-{{ $user->id }}">
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary">{{ $role->display_name }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">No Role</span>
                                @endif
                            </td>
                            <td>
                                @if($user->office)
                                    <span class="badge bg-info">{{ $user->office->name }}</span>
                                @else
                                    <span class="badge bg-secondary">No Office</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info">View</a>
                                <button type="button" class="btn btn-sm btn-warning edit-user-btn" data-id="{{ $user->id }}">Edit</button>
                                <button type="button" class="btn btn-sm btn-danger delete-user-btn" data-id="{{ $user->id }}">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modals-container">
        <!-- Include the modals -->
        @include('users.create-modal', ['roles' => $roles])
        @include('users.edit-modal', ['roles' => $roles])
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Pass offices data to JavaScript
    @if(isset($offices))
    var offices = [
        @foreach($offices as $office)
        {
            id: {{ $office->id }},
            name: '{{ $office->name }}'
        }@if(!$loop->last),@endif
        @endforeach
    ];
    @else
    var offices = [];
    @endif

    $(document).ready(function() {
        // CSRF Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Create User Button Click
        $('#create-user-btn').click(function() {
            $.ajax({
                url: '{{ route('users.create') }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        // Clear any existing modals to prevent duplicates
                        $('.modals-container').empty();
                        $('.modals-container').html(response.html);

                        // Initialize office selection in create modal if available
                        if(typeof(offices) !== 'undefined'){
                            let officeSelect = $('#create_office_id');
                            officeSelect.empty();
                            officeSelect.append('<option value="">No Office</option>');
                            offices.forEach(function(office) {
                                officeSelect.append('<option value="'+office.id+'">'+office.name+'</option>');
                            });
                        }

                        $('#createUserModal').modal('show');
                    }
                },
                error: function(xhr) {
                    showNotification('Error loading form.', 'danger');
                }
            });
        });

        // Store New User
        $(document).on('submit', '#createUserForm', function(e) {
            e.preventDefault();

            // Clear previous errors
            clearErrors();

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ route('users.store') }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if(response.success) {
                        $('#createUserModal').modal('hide');
                        showNotification(response.message, 'success');
                        location.reload(); // Refresh the page to show updated list
                    }
                },
                error: function(xhr) {
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            $('#' + field.replace('.', '_') + '_error').text(messages[0]);
                        });
                        console.log('Validation errors:', errors);
                    } else {
                        console.log('AJAX error details:', xhr);
                        showNotification('An error occurred while creating the user: ' + (xhr.responseJSON?.message || xhr.statusText), 'danger');
                    }
                }
            });
        });

        // Edit User Button Click
        $(document).on('click', '.edit-user-btn', function() {
            let userId = $(this).data('id');

            $.ajax({
                url: '/users/' + userId + '/edit',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('.modals-container').html(response.html);

                        // Use requestAnimationFrame or setTimeout to ensure DOM is updated before loading data
                        // This ensures the modal HTML is fully rendered before we try to populate it
                        requestAnimationFrame(function() {
                            loadEditUserData(userId);
                        });
                    }
                },
                error: function(xhr) {
                    showNotification('Error loading form.', 'danger');
                }
            });
        });

        // Load edit form data
        function loadEditUserData(userId) {
            $.ajax({
                url: '/users/' + userId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success && response.data) {
                        let user = response.data;
                        $('#edit_user_id').val(user.id);
                        $('#edit_name').val(user.name);
                        $('#edit_email').val(user.email);

                        // Handle roles checkboxes
                        $('#edit_roles_container input[type="checkbox"]').prop('checked', false);
                        if(user.roles && Array.isArray(user.roles)) {
                            user.roles.forEach(function(role) {
                                $('#edit_role_' + role.id).prop('checked', true);
                            });
                        }

                        // Set selected office
                        if(user.office) {
                            $('#edit_office_id').val(user.office.id);
                        } else {
                            $('#edit_office_id').val('');
                        }

                        $('#editUserModal').modal('show');
                    } else {
                        showNotification('Error loading user data: Invalid response format.', 'danger');
                    }
                },
                error: function(xhr) {
                    showNotification('Error loading user data.', 'danger');
                }
            });
        }

        // Update User
        $(document).on('submit', '#editUserForm', function(e) {
            e.preventDefault();

            // Clear previous errors
            clearErrors();

            let userId = $('#edit_user_id').val();
            let formData = $(this).serialize();

            $.ajax({
                url: '/users/' + userId,
                type: 'PUT',
                data: formData,
                success: function(response) {
                    if(response.success) {
                        $('#editUserModal').modal('hide');
                        showNotification(response.message, 'success');
                        location.reload(); // Refresh the page to show updated list
                    }
                },
                error: function(xhr) {
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            $('#' + field.replace('.', '_') + '_error').text(messages[0]);
                        });
                        console.log('Validation errors:', errors);
                    } else {
                        console.log('AJAX error details:', xhr);
                        showNotification('An error occurred while updating the user: ' + (xhr.responseJSON?.message || xhr.statusText), 'danger');
                    }
                }
            });
        });

        // Delete User Button Click
        $(document).on('click', '.delete-user-btn', function() {
            let userId = $(this).data('id');

            if(confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: '/users/' + userId,
                    type: 'DELETE',
                    success: function(response) {
                        if(response.success) {
                            $('#user-' + userId).remove();
                            showNotification(response.message, 'success');
                        } else {
                            showNotification(response.message, 'danger');
                        }
                    },
                    error: function(xhr) {
                        showNotification('An error occurred while deleting the user.', 'danger');
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

        // Handle modal closing for dynamically added modals
        $(document).on('click', '[data-bs-dismiss="modal"]', function() {
            const modal = $(this).closest('.modal');
            if (modal.length) {
                const modalId = modal.attr('id');
                $(`#${modalId}`).modal('hide');
            }
        });

        // Clean up modal backdrop when modals are hidden
        $(document).on('hidden.bs.modal', '.modal', function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        });
    });
</script>
@endsection