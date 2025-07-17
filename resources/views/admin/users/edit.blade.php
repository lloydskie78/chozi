@extends('layouts.app')

@section('title', 'Edit User - ChoziPay Admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">✏️ Edit User</h2>
                    <p class="text-muted mb-0">Modify user information and settings</p>
                </div>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                    <span class="me-1">👥</span>Back to Users
                </a>
            </div>

            <!-- User Info Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="bg-primary text-white rounded-circle p-3 fs-4">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-1">{{ $user->email }}</p>
                            <div class="d-flex gap-2">
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
                                @if($user->is_active)
                                    <span class="badge bg-success">✅ Active</span>
                                @else
                                    <span class="badge bg-danger">❌ Inactive</span>
                                @endif
                                <small class="text-muted">Member since {{ $user->created_at->format('M Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit User Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">👤 User Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <span class="me-1">👤</span>Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <span class="me-1">📧</span>Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
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
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
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

                        <!-- Password (Optional) -->
                        <div class="alert alert-info">
                            <strong>ℹ️ Password Update:</strong> Leave password fields empty to keep the current password unchanged.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <span class="me-1">🔑</span>New Password
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" minlength="8">
                                <div class="form-text">Leave empty to keep current password</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <span class="me-1">🔑</span>Confirm New Password
                                </label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" minlength="8">
                                <div class="form-text">Re-enter the new password</div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="wallet_balance" class="form-label">
                                    <span class="me-1">💰</span>Wallet Balance
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('wallet_balance') is-invalid @enderror" 
                                           id="wallet_balance" name="wallet_balance" 
                                           value="{{ old('wallet_balance', $user->wallet_balance) }}" 
                                           min="0" step="0.01">
                                </div>
                                <div class="form-text">Current balance: <strong>${{ number_format($user->wallet_balance, 2) }}</strong></div>
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
                                          id="address" name="address" rows="3" maxlength="500">{{ old('address', $user->address) }}</textarea>
                                <div class="form-text">User's physical address</div>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
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
                                    <span class="me-1">💾</span>Update User
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- User Statistics (if applicable) -->
            @if($user->role !== 'admin')
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">📊 User Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        @if($user->role === 'renter')
                            <div class="col-md-4">
                                <h6 class="text-muted">Payments Sent</h6>
                                <h4 class="text-primary">{{ $user->sentPayments()->count() }}</h4>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Total Spent</h6>
                                <h4 class="text-success">${{ number_format($user->sentPayments()->sum('amount'), 2) }}</h4>
                            </div>
                        @elseif($user->role === 'owner')
                            <div class="col-md-4">
                                <h6 class="text-muted">Payments Received</h6>
                                <h4 class="text-primary">{{ $user->receivedPayments()->count() }}</h4>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Total Received</h6>
                                <h4 class="text-success">${{ number_format($user->receivedPayments()->sum('net_amount'), 2) }}</h4>
                            </div>
                        @elseif($user->role === 'broker')
                            <div class="col-md-4">
                                <h6 class="text-muted">ChoziCodes</h6>
                                <h4 class="text-primary">{{ $user->choziCodes()->count() }}</h4>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Total Commissions</h6>
                                <h4 class="text-success">${{ number_format($user->brokerCommissions()->sum('broker_commission'), 2) }}</h4>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <h6 class="text-muted">Last Login</h6>
                            <h6 class="text-info">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Security Notice -->
            <div class="alert alert-warning mt-4">
                <h6 class="alert-heading">🔐 Security Notice</h6>
                <ul class="mb-0 small">
                    <li>All user modifications are logged for security purposes</li>
                    <li>Changing the user's role will affect their system permissions immediately</li>
                    <li>Password changes will require the user to log in again</li>
                    <li>Wallet balance changes should be made carefully and documented</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection 