@extends('layouts.app')

@section('title', 'Dashboard - ChoziPay')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Welcome Section -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 fw-bold text-gray-900 mb-2">Welcome back, {{ $user->name }}!</h1>
                    <p class="text-muted mb-0">{{ ucfirst($user->role) }} Dashboard</p>
                </div>
                <div class="text-end">
                    <div class="d-flex align-items-center gap-2 text-muted">
                        <i class="fas fa-clock"></i>
                        <small>Last login: {{ $user->last_login_at ? $user->last_login_at->format('M j, Y g:i A') : 'Never' }}</small>
                    </div>
                </div>
            </div>

            <!-- Wallet Balance Card -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card border-0" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                        <div class="card-body text-white p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-semibold mb-2 opacity-90">
                                        <i class="fas fa-wallet me-2"></i>
                                        Wallet Balance
                                    </h6>
                                    <h2 class="fw-bold mb-0">${{ number_format($user->wallet_balance, 2) }}</h2>
                                </div>
                                <div class="text-end opacity-75">
                                    <i class="fas fa-dollar-sign" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role-Specific Stats -->
            <div class="row g-4 mb-5">
                @if($user->role === 'broker')
                    <!-- Broker Stats -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" 
                                             style="width: 48px; height: 48px;">
                                            <i class="fas fa-tag fa-lg"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Active ChoziCode</h6>
                                        <p class="text-muted small mb-0">Your referral code</p>
                                    </div>
                                </div>
                                @if($activeCode)
                                    <div class="bg-light rounded p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="fw-bold mb-1">{{ $activeCode->code }}</h5>
                                                <small class="text-success">{{ $activeCode->commission_rate }}% commission</small>
                                            </div>
                                            <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard('{{ $activeCode->code }}')">
                                                <i class="fas fa-copy me-1"></i>
                                                Copy
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h6 class="fw-bold text-primary">{{ $activeCode->usage_count }}</h6>
                                                <small class="text-muted">Times Used</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h6 class="fw-bold text-success">${{ number_format($totalCommissions, 2) }}</h6>
                                                <small class="text-muted">Total Earned</small>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                                        <p class="text-muted">No active ChoziCode found</p>
                                        <a href="{{ route('chozi-codes.index') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>
                                            Create Code
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" 
                                             style="width: 48px; height: 48px;">
                                            <i class="fas fa-chart-line fa-lg"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Commission Analytics</h6>
                                        <p class="text-muted small mb-0">Your performance metrics</p>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h6 class="fw-bold text-info">{{ $totalCodes }}</h6>
                                            <small class="text-muted">Total Codes</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h6 class="fw-bold text-warning">{{ $totalUsage }}</h6>
                                            <small class="text-muted">Total Usage</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('chozi-codes.analytics') }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        View Analytics
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($user->role === 'renter')
                    <!-- Renter Stats -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" 
                                             style="width: 48px; height: 48px;">
                                            <i class="fas fa-paper-plane fa-lg"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Payment Summary</h6>
                                        <p class="text-muted small mb-0">Your payment activity</p>
                                    </div>
                                </div>
                                <div class="row g-3 text-center">
                                    <div class="col-6">
                                        <h6 class="fw-bold text-primary">{{ $totalPayments }}</h6>
                                        <small class="text-muted">Payments Sent</small>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="fw-bold text-success">${{ number_format($totalAmountSent, 2) }}</h6>
                                        <small class="text-muted">Total Amount</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" 
                                             style="width: 48px; height: 48px;">
                                            <i class="fas fa-clock fa-lg"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Recent Activity</h6>
                                        <p class="text-muted small mb-0">Latest payment status</p>
                                    </div>
                                </div>
                                @if($lastPayment)
                                    <div class="bg-light rounded p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted">Last Payment</small>
                                                <h6 class="fw-semibold mb-0">${{ number_format($lastPayment->amount, 2) }}</h6>
                                            </div>
                                            <span class="badge bg-{{ $lastPayment->status === 'completed' ? 'success' : ($lastPayment->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($lastPayment->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-info-circle text-info fa-2x mb-2"></i>
                                        <p class="text-muted">No payments yet</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @elseif($user->role === 'owner')
                    <!-- Owner Stats -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" 
                                             style="width: 48px; height: 48px;">
                                            <i class="fas fa-inbox fa-lg"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Received Payments</h6>
                                        <p class="text-muted small mb-0">Incoming transactions</p>
                                    </div>
                                </div>
                                <div class="row g-3 text-center">
                                    <div class="col-6">
                                        <h6 class="fw-bold text-primary">{{ $totalReceived }}</h6>
                                        <small class="text-muted">Total Received</small>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="fw-bold text-success">${{ number_format($totalAmountReceived, 2) }}</h6>
                                        <small class="text-muted">Total Amount</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" 
                                             style="width: 48px; height: 48px;">
                                            <i class="fas fa-hourglass-half fa-lg"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">Pending Payments</h6>
                                        <p class="text-muted small mb-0">Awaiting processing</p>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h6 class="fw-bold text-warning">${{ number_format($pendingAmount, 2) }}</h6>
                                    <small class="text-muted">{{ $pendingCount }} pending payments</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($user->role === 'renter')
                            <div class="col-md-3">
                                <a href="{{ route('payments.send') }}" class="btn btn-primary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-credit-card fa-lg mb-2"></i>
                                    <span>Make Payment</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('payments.index') }}" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-history fa-lg mb-2"></i>
                                    <span>Payment History</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-list fa-lg mb-2"></i>
                                    <span>Transaction Log</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-user fa-lg mb-2"></i>
                                    <span>Profile Settings</span>
                                </a>
                            </div>
                        @elseif($user->role === 'owner')
                            <div class="col-md-3">
                                <a href="{{ route('payments.index') }}" class="btn btn-primary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-inbox fa-lg mb-2"></i>
                                    <span>View Payments</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-history fa-lg mb-2"></i>
                                    <span>Transaction History</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-3" onclick="showAlert('Property management coming soon!', 'info')">
                                    <i class="fas fa-building fa-lg mb-2"></i>
                                    <span>Manage Properties</span>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-cog fa-lg mb-2"></i>
                                    <span>Settings</span>
                                </a>
                            </div>
                        @elseif($user->role === 'broker')
                            <div class="col-md-3">
                                <a href="{{ route('chozi-codes.index') }}" class="btn btn-primary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-tags fa-lg mb-2"></i>
                                    <span>Manage Codes</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('chozi-codes.analytics') }}" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-chart-line fa-lg mb-2"></i>
                                    <span>Commission Report</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-eye fa-lg mb-2"></i>
                                    <span>View Payments</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-cog fa-lg mb-2"></i>
                                    <span>Settings</span>
                                </a>
                            </div>
                        @else
                            <!-- Admin Quick Actions -->
                            <div class="col-md-3">
                                <a href="{{ route('admin.users') }}" class="btn btn-primary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-users fa-lg mb-2"></i>
                                    <span>Manage Users</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('payments.index') }}" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-eye fa-lg mb-2"></i>
                                    <span>View Payments</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-3" onclick="showAlert('System settings coming soon!', 'info')">
                                    <i class="fas fa-cogs fa-lg mb-2"></i>
                                    <span>System Settings</span>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="fas fa-shield-alt fa-lg mb-2"></i>
                                    <span>Security Logs</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-history me-2"></i>
                        Recent Transactions
                    </h6>
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-external-link-alt me-1"></i>
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($recentTransactions && $recentTransactions->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentTransactions as $transaction)
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @php
                                                    $iconMap = [
                                                        'payment' => 'credit-card',
                                                        'login' => 'sign-in-alt',
                                                        'chozi_code_usage' => 'tag',
                                                        'chozi_code_generation' => 'plus-circle',
                                                        'profile_update' => 'user-edit'
                                                    ];
                                                    $colorMap = [
                                                        'payment' => 'success',
                                                        'login' => 'info',
                                                        'chozi_code_usage' => 'warning',
                                                        'chozi_code_generation' => 'primary',
                                                        'profile_update' => 'secondary'
                                                    ];
                                                @endphp
                                                <div class="rounded-circle bg-{{ $colorMap[$transaction->transaction_type] ?? 'secondary' }} bg-opacity-10 text-{{ $colorMap[$transaction->transaction_type] ?? 'secondary' }} d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-{{ $iconMap[$transaction->transaction_type] ?? 'circle' }}"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $transaction->action }}</h6>
                                                <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        @if($transaction->amount)
                                            <div class="text-end">
                                                <span class="fw-semibold text-success">${{ number_format($transaction->amount, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-muted opacity-50 mb-3"></i>
                            <h6 class="text-muted">No Recent Transactions</h6>
                            <p class="text-muted small">Your recent activity will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.2s ease;
    }
    
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    
    .list-group-item {
        transition: background-color 0.2s ease;
    }
    
    .list-group-item:hover {
        background-color: var(--gray-50);
    }
</style>
@endpush

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            showAlert('ChoziCode copied to clipboard!', 'success');
        });
    }
    
    function showAlert(message, type = 'info') {
        const alertTypes = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        };
        
        const icons = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        };
        
        const alertHtml = `
            <div class="alert ${alertTypes[type]} alert-dismissible fade show" role="alert">
                <i class="fas ${icons[type]} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        const container = document.querySelector('.container-fluid');
        const alertDiv = document.createElement('div');
        alertDiv.innerHTML = alertHtml;
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = alertDiv.querySelector('.alert');
            if (alert && alert.classList.contains('show')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }
</script>
@endpush
@endsection 