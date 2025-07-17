@extends('layouts.app')

@section('title', 'Edit User - ChoziPay Admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 fw-bold text-gray-900 mb-2">Edit User</h1>
                    <p class="text-muted mb-0">Modify user information and settings</p>
                </div>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Users
                </a>
            </div>

            <!-- User Info Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-4">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                 style="width: 64px; height: 64px; font-size: 24px; font-weight: 600;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-2">{{ $user->name }}</h5>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            <div class="d-flex gap-3 align-items-center">
                                @php
                                    $roleConfig = [
                                        'admin' => ['color' => 'danger', 'icon' => 'shield-alt'],
                                        'broker' => ['color' => 'warning', 'icon' => 'tags'],
                                        'owner' => ['color' => 'info', 'icon' => 'building'],
                                        'renter' => ['color' => 'success', 'icon' => 'home']
                                    ];
                                    $config = $roleConfig[$user->role] ?? ['color' => 'secondary', 'icon' => 'user'];
                                @endphp
                                <span class="badge bg-{{ $config['color'] }} bg-opacity-10 text-{{ $config['color'] }} border border-{{ $config['color'] }} border-opacity-25 px-3 py-2">
                                    <i class="fas fa-{{ $config['icon'] }} me-2"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                                @if($user->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2">
                                        <i class="fas fa-times-circle me-2"></i>
                                        Inactive
                                    </span>
                                @endif
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Member since {{ $user->created_at->format('M Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit User Form -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-user-edit me-2"></i>
                        User Information
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Basic Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2 text-muted"></i>
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2 text-muted"></i>
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-2 text-muted"></i>
                                    Phone Number
                                </label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="role" class="form-label">
                                    <i class="fas fa-user-tag me-2 text-muted"></i>
                                    User Role <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">Select a role...</option>
                                    <option value="renter" {{ old('role', $user->role) === 'renter' ? 'selected' : '' }}>
                                        🏠 Renter
                                    </option>
                                    <option value="owner" {{ old('role', $user->role) === 'owner' ? 'selected' : '' }}>
                                        🏢 Property Owner
                                    </option>
                                    <option value="broker" {{ old('role', $user->role) === 'broker' ? 'selected' : '' }}>
                                        🏷️ Broker
                                    </option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                        🛡️ Administrator
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Section -->
                        <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Security</h6>
                        <div class="alert alert-info border-0 mb-3" style="background-color: var(--primary-50);">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle text-primary me-2 mt-1"></i>
                                <div class="small">
                                    <strong>Password Update:</strong> Leave password fields empty to keep the current password unchanged.
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2 text-muted"></i>
                                    New Password
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" minlength="8">
                                <div class="form-text">Leave empty to keep current password</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-2 text-muted"></i>
                                    Confirm New Password
                                </label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" minlength="8">
                                <div class="form-text">Re-enter the new password</div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Financial Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="wallet_balance" class="form-label">
                                    <i class="fas fa-wallet me-2 text-muted"></i>
                                    Wallet Balance
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('wallet_balance') is-invalid @enderror" 
                                           id="wallet_balance" name="wallet_balance" 
                                           value="{{ old('wallet_balance', $user->wallet_balance) }}" 
                                           min="0" step="0.01">
                                </div>
                                <div class="form-text">
                                    Current balance: <strong class="text-success">${{ number_format($user->wallet_balance, 2) }}</strong>
                                </div>
                                @error('wallet_balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                    Address
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3" maxlength="500">{{ old('address', $user->address) }}</textarea>
                                <div class="form-text">User's physical address</div>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3 justify-content-end">
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-times me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- User Statistics -->
            @if($user->role !== 'admin')
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-bar me-2"></i>
                        User Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @if($user->role === 'renter')
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-paper-plane fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase">Payments Sent</h6>
                                    <h4 class="fw-bold text-primary">{{ $user->sentPayments()->count() }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-dollar-sign fa-2x text-success"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase">Total Spent</h6>
                                    <h4 class="fw-bold text-success">${{ number_format($user->sentPayments()->sum('amount'), 2) }}</h4>
                                </div>
                            </div>
                        @elseif($user->role === 'owner')
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-inbox fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase">Payments Received</h6>
                                    <h4 class="fw-bold text-primary">{{ $user->receivedPayments()->count() }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-dollar-sign fa-2x text-success"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase">Total Received</h6>
                                    <h4 class="fw-bold text-success">${{ number_format($user->receivedPayments()->sum('net_amount'), 2) }}</h4>
                                </div>
                            </div>
                        @elseif($user->role === 'broker')
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-tags fa-2x text-warning"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase">ChoziCodes</h6>
                                    <h4 class="fw-bold text-warning">{{ $user->choziCodes()->count() }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-percentage fa-2x text-success"></i>
                                    </div>
                                    <h6 class="text-muted small text-uppercase">Total Commissions</h6>
                                    <h4 class="fw-bold text-success">${{ number_format($user->brokerCommissions()->sum('broker_commission'), 2) }}</h4>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="mb-2">
                                    <i class="fas fa-clock fa-2x text-info"></i>
                                </div>
                                <h6 class="text-muted small text-uppercase">Last Login</h6>
                                <h6 class="fw-semibold text-info">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Security Notice -->
            <div class="alert alert-warning border-0 mt-4" style="background-color: #fffbeb;">
                <h6 class="alert-heading fw-semibold">
                    <i class="fas fa-shield-alt me-2"></i>
                    Security Notice
                </h6>
                <div class="row g-3 small">
                    <div class="col-md-6">
                        <ul class="mb-0">
                            <li>All user modifications are logged for security purposes</li>
                            <li>Changing user roles affects permissions immediately</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="mb-0">
                            <li>Password changes require users to log in again</li>
                            <li>Wallet balance changes should be documented</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-500);
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
    }
    
    .input-group-text {
        background-color: var(--gray-50);
        border-color: var(--gray-300);
        color: var(--gray-600);
    }
    
    .border-bottom {
        border-color: var(--gray-200) !important;
    }
    
    .alert-info {
        border-left: 4px solid var(--primary-500);
    }
    
    .alert-warning {
        border-left: 4px solid #f59e0b;
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