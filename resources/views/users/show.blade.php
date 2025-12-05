@extends('layouts.adminator')

@section('title', 'View User')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Details</h1>
        <div>
            <a href="{{ route('users.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Users
            </a>
            <a href="{{ route('users.edit', $user->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit
            </a>
        </div>
    </div>

    <!-- User Detail Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Full Name</label>
                        <p class="form-control-static">{{ $user->name }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Email</label>
                        <p class="form-control-static">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Assigned Office</label>
                        <p class="form-control-static">
                            @if($user->office)
                                <span class="badge bg-info">{{ $user->office->name }}</span>
                            @else
                                <span class="badge bg-secondary">No Office Assigned</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Created At</label>
                        <p class="form-control-static">{{ $user->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Roles</label>
                        <div>
                            @if($user->roles->count() > 0)
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary mr-1">{{ $role->display_name }}</span>
                                @endforeach
                            @else
                                <span class="badge bg-secondary">No Role Assigned</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Last Updated</label>
                        <p class="form-control-static">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">User ID</label>
                        <p class="form-control-static">{{ $user->id }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">&nbsp;</label> <!-- Spacer for alignment -->
                        <p class="form-control-static">&nbsp;</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection