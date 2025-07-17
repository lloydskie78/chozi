@extends('layouts.app')

@section('title', 'Create User - ChoziPay Admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">➕ Create New User</h2>
                    <p class="text-muted mb-0">Add a new user to the ChoziPay system</p>
                </div>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                    <span class="me-1">👥</span>Back to Users
                </a>
            </div>

            <!-- Create User Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">👤 User Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <span class="me-1">👤</span>Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <span class="me-1">📧</span>Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <span class="me-1">📞</span>Phone Number
                                </label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">
                                    <span class="me-1">🎭</span>User Role <span class="text-danger">*</span>
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

                        <!-- Password -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <span class="me-1">🔑</span>Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required minlength="8">
                                <div class="form-text">Minimum 8 characters</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <span class="me-1">🔑</span>Confirm Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required minlength="8">
                                <div class="form-text">Re-enter the password</div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="wallet_balance" class="form-label">
                                    <span class="me-1">💰</span>Initial Wallet Balance
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('wallet_balance') is-invalid @enderror" 
                                           id="wallet_balance" name="wallet_balance" value="{{ old('wallet_balance', '0.00') }}" 
                                           min="0" step="0.01">
                                </div>
                                <div class="form-text">Optional: Set an initial wallet balance</div>
                                @error('wallet_balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">
                                    <span class="me-1">📍</span>Address
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
                        <div class="alert alert-info">
                            <h6 class="alert-heading">ℹ️ Role Permissions</h6>
                            <ul class="mb-0 small">
                                <li><strong>🏠 Renter:</strong> Can send payments, view payment history, use ChoziCodes</li>
                                <li><strong>🏢 Property Owner:</strong> Can receive payments, manage properties, view tenant payments</li>
                                <li><strong>🏷️ Broker:</strong> Can generate ChoziCodes, earn commissions, manage referral codes</li>
                                <li><strong>🛡️ Administrator:</strong> Full system access, user management, system settings</li>
                            </ul>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary w-100">
                                    <span class="me-1">❌</span>Cancel
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    <span class="me-1">💾</span>Create User
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="alert alert-warning mt-4">
                <h6 class="alert-heading">🔐 Security Notice</h6>
                <ul class="mb-0 small">
                    <li>All user creation actions are logged for security purposes</li>
                    <li>Users will be able to change their password after first login</li>
                    <li>Email addresses must be unique across the system</li>
                    <li>New users are activated by default - you can deactivate them from the user list</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection 