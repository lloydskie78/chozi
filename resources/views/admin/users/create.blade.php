@extends('layouts.app')

@section('title', 'Create User - ChoziPay Admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 fw-bold text-gray-900 mb-2">Create New User</h1>
                    <p class="text-muted mb-0">Add a new user to the ChoziPay system</p>
                </div>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Users
                </a>
            </div>

            <!-- Create User Form -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-user-plus me-2"></i>
                        User Information
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        
                        <!-- Basic Information -->
                        <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Basic Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2 text-muted"></i>
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
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
                                       id="email" name="email" value="{{ old('email') }}" required>
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
                                       id="phone" name="phone" value="{{ old('phone') }}">
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
                                    <option value="renter" {{ old('role') === 'renter' ? 'selected' : '' }}>
                                        🏠 Renter
                                    </option>
                                    <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>
                                        🏢 Property Owner
                                    </option>
                                    <option value="broker" {{ old('role') === 'broker' ? 'selected' : '' }}>
                                        🏷️ Broker
                                    </option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
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
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2 text-muted"></i>
                                    Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required minlength="8">
                                <div class="form-text">Minimum 8 characters with mixed case, numbers, and symbols</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-2 text-muted"></i>
                                    Confirm Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required minlength="8">
                                <div class="form-text">Re-enter the password to confirm</div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Additional Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="wallet_balance" class="form-label">
                                    <i class="fas fa-wallet me-2 text-muted"></i>
                                    Initial Wallet Balance
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('wallet_balance') is-invalid @enderror" 
                                           id="wallet_balance" name="wallet_balance" value="{{ old('wallet_balance', '0.00') }}" 
                                           min="0" step="0.01">
                                </div>
                                <div class="form-text">Optional: Set an initial wallet balance for the user</div>
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
                                          id="address" name="address" rows="3" maxlength="500">{{ old('address') }}</textarea>
                                <div class="form-text">Optional: User's physical address</div>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Role Information -->
                        <div class="alert alert-info border-0" style="background-color: var(--primary-50);">
                            <h6 class="alert-heading fw-semibold">
                                <i class="fas fa-info-circle me-2"></i>
                                Role Permissions
                            </h6>
                            <div class="row g-3 small">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-home text-success me-2 mt-1"></i>
                                        <div>
                                            <strong>Renter:</strong> Send payments, view history, use ChoziCodes
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-building text-info me-2 mt-1"></i>
                                        <div>
                                            <strong>Owner:</strong> Receive payments, manage properties, view tenant payments
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-tags text-warning me-2 mt-1"></i>
                                        <div>
                                            <strong>Broker:</strong> Generate ChoziCodes, earn commissions, manage referrals
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-shield-alt text-danger me-2 mt-1"></i>
                                        <div>
                                            <strong>Admin:</strong> Full system access, user management, system settings
                                        </div>
                                    </div>
                                </div>
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
                                Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="alert alert-warning border-0 mt-4" style="background-color: #fffbeb;">
                <h6 class="alert-heading fw-semibold">
                    <i class="fas fa-shield-alt me-2"></i>
                    Security Notice
                </h6>
                <div class="row g-3 small">
                    <div class="col-md-6">
                        <ul class="mb-0">
                            <li>All user creation actions are logged for security purposes</li>
                            <li>Users can change their password after first login</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="mb-0">
                            <li>Email addresses must be unique across the system</li>
                            <li>New users are activated by default</li>
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
</style>
@endpush
@endsection 