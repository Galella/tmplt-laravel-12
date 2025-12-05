@extends('layouts.adminator')

@section('title', 'Office Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Office Management</h1>
        <a href="{{ route('offices.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Office
        </a>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Offices List</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Parent Office</th>
                            <th>Users Count</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offices as $office)
                        <tr>
                            <td>{{ $office->code }}</td>
                            <td>{{ $office->name }}</td>
                            <td>
                                <span class="badge 
                                    @if($office->type == 'headquarters') bg-primary 
                                    @elseif($office->type == 'regional') bg-success 
                                    @else bg-info @endif">
                                    {{ $officeTypes[$office->type] ?? ucfirst($office->type) }}
                                </span>
                            </td>
                            <td>
                                @if($office->parent)
                                    {{ $office->parent->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $office->users->count() }}</td>
                            <td>
                                @if($office->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $office->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('offices.show', $office->id) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('offices.edit', $office->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('offices.destroy', $office->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Are you sure you want to delete this office? This action cannot be undone if it has child offices.')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $offices->links() }}
            </div>
        </div>
    </div>
</div>
@endsection