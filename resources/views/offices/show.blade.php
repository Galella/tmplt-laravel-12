@extends('layouts.adminator')

@section('title', 'View Office')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Office Details</h1>
        <div>
            <a href="{{ route('offices.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Offices
            </a>
            <a href="{{ route('offices.edit', $office->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Office
            </a>
        </div>
    </div>

    <!-- Office Detail Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Office Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Office Name</label>
                        <p class="form-control-static">{{ $office->name }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Office Code</label>
                        <p class="form-control-static">{{ $office->code }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Office Type</label>
                        <p class="form-control-static">
                            <span class="badge 
                                @if($office->type == 'headquarters') bg-primary 
                                @elseif($office->type == 'regional') bg-success 
                                @else bg-info @endif">
                                {{ $officeTypes[$office->type] ?? ucfirst($office->type) }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Parent Office</label>
                        <p class="form-control-static">
                            @if($office->parent)
                                {{ $office->parent->name }}
                            @else
                                <span class="text-muted">None (Top Level)</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Address</label>
                        <p class="form-control-static">{{ $office->address ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Phone</label>
                        <p class="form-control-static">{{ $office->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Email</label>
                        <p class="form-control-static">{{ $office->email ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Status</label>
                        <p class="form-control-static">
                            @if($office->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Created At</label>
                        <p class="form-control-static">{{ $office->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Last Updated</label>
                        <p class="form-control-static">{{ $office->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Child Offices -->
    @if($office->children->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Child Offices</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Users</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($office->children as $child)
                        <tr>
                            <td>{{ $child->code }}</td>
                            <td>{{ $child->name }}</td>
                            <td>
                                <span class="badge 
                                    @if($child->type == 'headquarters') bg-primary 
                                    @elseif($child->type == 'regional') bg-success 
                                    @else bg-info @endif">
                                    {{ $officeTypes[$child->type] ?? ucfirst($child->type) }}
                                </span>
                            </td>
                            <td>
                                @if($child->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $child->users->count() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Office Users -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Assigned Users</h6>
        </div>
        <div class="card-body">
            @if($office->users->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Position</th>
                            <th>Primary Office</th>
                            <th>Assigned Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($office->users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->pivot->position ?? 'N/A' }}</td>
                            <td>
                                @if($user->pivot->is_primary)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>{{ $user->pivot->assigned_date ? $user->pivot->assigned_date->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">No users assigned to this office.</p>
            @endif
        </div>
    </div>
</div>
@endsection