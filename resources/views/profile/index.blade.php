@extends('layouts.app')

@section('title', 'Profile Settings - ChoziPay')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">⚙️ Profile Settings</h2>
                    <p class="text-muted mb-0">Manage your account information and preferences</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <span class="me-1">📊</span>Dashboard
                </a>
            </div>

            <!-- Profile Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">👤 Personal Information</h5>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ auth()->user()->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ auth()->user()->email }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="{{ auth()->user()->phone }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Account Type</label>
                                <input type="text" class="form-control" id="role" 
                                       value="{{ ucfirst(auth()->user()->role) }}" readonly>
                                <div class="form-text">Contact support to change your account type</div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Address (Optional)</label>
                                <textarea class="form-control" id="address" name="address" 
                                          rows="3">{{ auth()->user()->address }}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <span class="me-1">💾</span>Update Profile
                        </button>
                    </form>
                </div>
            </div>

            <!-- Account Security -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">🔐 Account Security</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Password</h6>
                            <p class="text-muted">Last changed: {{ auth()->user()->updated_at->diffForHumans() }}</p>
                            <button class="btn btn-outline-warning" onclick="showChangePasswordModal()">
                                <span class="me-1">🔑</span>Change Password
                            </button>
                        </div>
                        <div class="col-md-6">
                            <h6>Account Status</h6>
                            <p class="mb-2">
                                @if(auth()->user()->is_active)
                                    <span class="badge bg-success">✅ Active</span>
                                @else
                                    <span class="badge bg-danger">❌ Inactive</span>
                                @endif
                            </p>
                            <small class="text-muted">
                                Member since {{ auth()->user()->created_at->format('F Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wallet Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">💰 Wallet Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Current Balance</h6>
                            <h3 class="text-primary">${{ number_format(auth()->user()->wallet_balance, 2) }}</h3>
                        </div>
                        <div class="col-md-6">
                            <h6>Account Activity</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Sent:</span>
                                <strong id="totalSent">Loading...</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Received:</span>
                                <strong id="totalReceived">Loading...</strong>
                            </div>
                            @if(auth()->user()->isBroker())
                            <div class="d-flex justify-content-between">
                                <span>Total Commissions:</span>
                                <strong id="totalCommissions">Loading...</strong>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isBroker())
            <!-- Broker Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">🏷️ Broker Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Active ChoziCodes</h6>
                            <h4 class="text-info" id="activeCodes">Loading...</h4>
                        </div>
                        <div class="col-md-6">
                            <h6>Total Code Uses</h6>
                            <h4 class="text-success" id="totalUses">Loading...</h4>
                        </div>
                    </div>
                    <hr>
                    <a href="{{ route('chozi-codes.index') }}" class="btn btn-outline-primary">
                        <span class="me-1">🏷️</span>Manage ChoziCodes
                    </a>
                    <a href="{{ route('chozi-codes.analytics') }}" class="btn btn-outline-info">
                        <span class="me-1">📊</span>View Analytics
                    </a>
                </div>
            </div>
            @endif

            <!-- Account Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">⚠️ Account Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Download Data</h6>
                            <p class="text-muted small">Download a copy of your account data and transaction history.</p>
                            <button class="btn btn-outline-info btn-sm" onclick="downloadData()">
                                <span class="me-1">📥</span>Download Data
                            </button>
                        </div>
                        <div class="col-md-6">
                            <h6>Deactivate Account</h6>
                            <p class="text-muted small">Temporarily deactivate your account. You can reactivate it later.</p>
                            <button class="btn btn-outline-warning btn-sm" onclick="deactivateAccount()">
                                <span class="me-1">⏸️</span>Deactivate
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAccountStats();
    
    // Form submission
    document.getElementById('profileForm').addEventListener('submit', handleProfileUpdate);
});

async function loadAccountStats() {
    try {
        const response = await fetch('/api/payments/stats', {
            headers: {
                'Authorization': 'Bearer ' + getAuthToken(),
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                document.getElementById('totalSent').textContent = '$' + (data.data.total_sent || 0).toFixed(2);
                document.getElementById('totalReceived').textContent = '$' + (data.data.total_received || 0).toFixed(2);
                
                @if(auth()->user()->isBroker())
                document.getElementById('totalCommissions').textContent = '$' + (data.data.total_commissions || 0).toFixed(2);
                document.getElementById('activeCodes').textContent = data.data.active_chozi_codes || 0;
                
                // Load broker-specific stats
                loadBrokerStats();
                @endif
            }
        }
    } catch (error) {
        console.error('Error loading account stats:', error);
    }
}

@if(auth()->user()->isBroker())
async function loadBrokerStats() {
    try {
        const response = await fetch('/api/chozi-codes/analytics', {
            headers: {
                'Authorization': 'Bearer ' + getAuthToken(),
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                document.getElementById('totalUses').textContent = data.data.total_uses || 0;
            }
        }
    } catch (error) {
        console.error('Error loading broker stats:', error);
    }
}
@endif

async function handleProfileUpdate(e) {
    e.preventDefault();
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Updating...';
    
    try {
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        
        // Simulate API call - in real implementation, this would call an API endpoint
        setTimeout(() => {
            showAlert('Profile updated successfully!', 'success');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 1000);
        
    } catch (error) {
        console.error('Profile update error:', error);
        showAlert('Failed to update profile', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

function downloadData() {
    showAlert('Data download feature coming soon', 'info');
}

function deactivateAccount() {
    if (confirm('Are you sure you want to deactivate your account? You can reactivate it later by contacting support.')) {
        showAlert('Account deactivation feature coming soon', 'info');
    }
}

function getAuthToken() {
    return 'session-token';
}

function showAlert(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : type === 'warning' ? 'alert-warning' : 'alert-info';
    const alertIcon = type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : 'ℹ️';
    
    const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <span class="me-2">${alertIcon}</span>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('show')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    }, 5000);
}
</script>
@endpush 