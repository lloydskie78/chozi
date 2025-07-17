@extends('layouts.app')

@section('title', 'Manage Users - ChoziPay Admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">👥 User Management</h2>
                    <p class="text-muted mb-0">Manage all system users and their permissions</p>
                </div>
                <div>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-2">
                        <span class="me-1">📊</span>Dashboard
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <span class="me-1">➕</span>Add New User
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-2 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1">Total Users</h6>
                            <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                            <small>👥</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1">Active</h6>
                            <h3 class="mb-0">{{ $stats['active_users'] }}</h3>
                            <small>✅</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1">Inactive</h6>
                            <h3 class="mb-0">{{ $stats['inactive_users'] }}</h3>
                            <small>⚠️</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1">Renters</h6>
                            <h3 class="mb-0">{{ $stats['renters'] }}</h3>
                            <small>🏠</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1">Owners</h6>
                            <h3 class="mb-0">{{ $stats['owners'] }}</h3>
                            <small>🏢</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card bg-dark text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1">Brokers</h6>
                            <h3 class="mb-0">{{ $stats['brokers'] }}</h3>
                            <small>🏷️</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">🔍 Search & Filter Users</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users') }}">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Name, email, or phone...">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="">All Roles</option>
                                    <option value="renter" {{ request('role') === 'renter' ? 'selected' : '' }}>Renter</option>
                                    <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                                    <option value="broker" {{ request('role') === 'broker' ? 'selected' : '' }}>Broker</option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="me-1">🔍</span>Filter
                                    </button>
                                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                                        <span class="me-1">🗑️</span>Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">👥 Users List</h5>
                    <small class="text-muted">{{ $users->total() }} total users</small>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Contact</th>
                                        <th>Wallet Balance</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr class="{{ !$user->is_active ? 'table-warning' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="bg-primary text-white rounded-circle p-2">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $user->name }}</h6>
                                                        <small class="text-muted">{{ $user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $roleColors = [
                                                        'admin' => 'bg-danger',
                                                        'broker' => 'bg-warning text-dark',
                                                        'owner' => 'bg-info',
                                                        'renter' => 'bg-success'
                                                    ];
                                                    $roleIcons = [
                                                        'admin' => '🛡️',
                                                        'broker' => '🏷️',
                                                        'owner' => '🏢',
                                                        'renter' => '🏠'
                                                    ];
                                                @endphp
                                                <span class="badge {{ $roleColors[$user->role] ?? 'bg-secondary' }}">
                                                    {{ $roleIcons[$user->role] ?? '👤' }} {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    @if($user->phone)
                                                        <small class="text-muted">📞 {{ $user->phone }}</small><br>
                                                    @endif
                                                    @if($user->address)
                                                        <small class="text-muted">📍 {{ Str::limit($user->address, 30) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-success">${{ number_format($user->wallet_balance, 2) }}</strong>
                                            </td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge bg-success">✅ Active</span>
                                                @else
                                                    <span class="badge bg-danger">❌ Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $user->created_at->format('M j, Y') }}</small><br>
                                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                                       class="btn btn-outline-primary" title="Edit User">
                                                        <span class="me-1">✏️</span>Edit
                                                    </a>
                                                    
                                                    @if($user->id !== auth()->id())
                                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                                                    title="{{ $user->is_active ? 'Deactivate' : 'Activate' }} User"
                                                                    onclick="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?')">
                                                                <span class="me-1">{{ $user->is_active ? '⏸️' : '▶️' }}</span>
                                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                            </button>
                                                        </form>
                                                        
                                                        <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-danger"
                                                                    title="Delete User"
                                                                    onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                                <span class="me-1">🗑️</span>Delete
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-info small">Current Admin</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $users->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <span class="fs-1">👥</span>
                            <h5 class="mt-3">No Users Found</h5>
                            <p class="text-muted">No users match your current filter criteria.</p>
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-primary">
                                <span class="me-1">🗑️</span>Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 