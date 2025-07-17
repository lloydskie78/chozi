@extends('layouts.app')

@section('title', 'User Management - ChoziPay Admin')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 fw-bold text-gray-900 mb-2">User Management</h1>
                    <p class="text-muted mb-0">Manage users, roles, and permissions across the system</p>
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Add User
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-2">
                    <div class="card h-100 border-0" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                        <div class="card-body text-white text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $stats['total_users'] }}</h3>
                            <p class="small mb-0 opacity-90">Total Users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card h-100 border-0" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <div class="card-body text-white text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $stats['active_users'] }}</h3>
                            <p class="small mb-0 opacity-90">Active</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card h-100 border-0" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <div class="card-body text-white text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-pause-circle fa-lg"></i>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $stats['inactive_users'] }}</h3>
                            <p class="small mb-0 opacity-90">Inactive</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2 text-primary">
                                <i class="fas fa-home fa-lg"></i>
                            </div>
                            <h3 class="fw-bold mb-1 text-primary">{{ $stats['renters'] }}</h3>
                            <p class="small mb-0 text-muted">Renters</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2 text-info">
                                <i class="fas fa-building fa-lg"></i>
                            </div>
                            <h3 class="fw-bold mb-1 text-info">{{ $stats['owners'] }}</h3>
                            <p class="small mb-0 text-muted">Owners</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2 text-warning">
                                <i class="fas fa-tags fa-lg"></i>
                            </div>
                            <h3 class="fw-bold mb-1 text-warning">{{ $stats['brokers'] }}</h3>
                            <p class="small mb-0 text-muted">Brokers</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search Users</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Name, email, or phone...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="">All Roles</option>
                                    <option value="renter" {{ request('role') === 'renter' ? 'selected' : '' }}>Renter</option>
                                    <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                                    <option value="broker" {{ request('role') === 'broker' ? 'selected' : '' }}>Broker</option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-2"></i>
                                        Apply Filters
                                    </button>
                                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>
                                        Clear
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
                    <h6 class="mb-0 fw-semibold">Users ({{ $users->total() }})</h6>
                    <small class="text-muted">{{ $users->count() }} of {{ $users->total() }} users</small>
                </div>
                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Contact</th>
                                        <th>Wallet</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th width="200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr class="{{ !$user->is_active ? 'opacity-75' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 40px; font-weight: 600;">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1 fw-semibold">{{ $user->name }}</h6>
                                                        <small class="text-muted">{{ $user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $roleConfig = [
                                                        'admin' => ['color' => 'danger', 'icon' => 'shield-alt'],
                                                        'broker' => ['color' => 'warning', 'icon' => 'tags'],
                                                        'owner' => ['color' => 'info', 'icon' => 'building'],
                                                        'renter' => ['color' => 'success', 'icon' => 'home']
                                                    ];
                                                    $config = $roleConfig[$user->role] ?? ['color' => 'secondary', 'icon' => 'user'];
                                                @endphp
                                                <span class="badge bg-{{ $config['color'] }} bg-opacity-10 text-{{ $config['color'] }} border border-{{ $config['color'] }} border-opacity-25">
                                                    <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($user->phone)
                                                    <div class="small text-muted">
                                                        <i class="fas fa-phone me-1"></i>
                                                        {{ $user->phone }}
                                                    </div>
                                                @endif
                                                @if($user->address)
                                                    <div class="small text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        {{ Str::limit($user->address, 25) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-success">
                                                    ${{ number_format($user->wallet_balance, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                                        <i class="fas fa-times-circle me-1"></i>
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="small">{{ $user->created_at->format('M j, Y') }}</div>
                                                <div class="small text-muted">{{ $user->created_at->diffForHumans() }}</div>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="{{ route('admin.users.edit', $user) }}" class="dropdown-item">
                                                                <i class="fas fa-edit me-2"></i>
                                                                Edit User
                                                            </a>
                                                        </li>
                                                        @if($user->id !== auth()->id())
                                                            <li>
                                                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="dropdown-item"
                                                                            onclick="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?')">
                                                                        <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }} me-2"></i>
                                                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger"
                                                                            onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                                        <i class="fas fa-trash me-2"></i>
                                                                        Delete User
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <span class="dropdown-item text-muted">
                                                                    <i class="fas fa-lock me-2"></i>
                                                                    Current Admin
                                                                </span>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($users->hasPages())
                            <div class="card-footer bg-light">
                                <div class="d-flex justify-content-center">
                                    {{ $users->withQueryString()->links() }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-users fa-3x text-muted opacity-50"></i>
                            </div>
                            <h5 class="text-muted mb-2">No Users Found</h5>
                            <p class="text-muted mb-3">No users match your current filter criteria.</p>
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-primary">
                                <i class="fas fa-refresh me-2"></i>
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: all 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .input-group-text {
        background-color: var(--gray-50);
        border-color: var(--gray-300);
    }
    
    .table > tbody > tr:hover {
        background-color: var(--gray-50);
    }
    
    .opacity-75 {
        opacity: 0.75;
    }
    
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    
    .border-opacity-25 {
        --bs-border-opacity: 0.25;
    }
</style>
@endpush
@endsection 