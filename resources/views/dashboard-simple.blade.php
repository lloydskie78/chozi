@extends('layouts.app')

@section('title', 'Dashboard - ChoziPay')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <!-- Welcome Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Welcome back, {{ $user->name }}!</h1>
                    <p class="text-muted">{{ ucfirst($user->role) }} Dashboard</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">Last login: {{ $user->last_login_at ? $user->last_login_at->format('M j, Y g:i A') : 'Never' }}</small>
                </div>
            </div>

            <!-- Wallet Balance Card -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0">
                                        💰 Wallet Balance
                                    </h5>
                                    <h2 class="mb-0">KES {{ number_format($user->wallet_balance, 2) }}</h2>
                                </div>
                                <div>
                                    <span style="font-size: 3rem; opacity: 0.3;">💵</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role-Specific Stats -->
            <div class="row mb-4">
                @if($user->role === 'broker')
                    <!-- Broker Stats -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    🏷️ Active ChoziCode
                                </h5>
                                @if(isset($choziCode) && $choziCode)
                                    <h4 class="text-success">{{ $choziCode->code }}</h4>
                                    <p class="text-muted mb-0">
                                        Commission Rate: {{ $choziCode->commission_rate }}%<br>
                                        Usage Count: {{ $choziCode->usage_count }}
                                    </p>
                                @else
                                    <p class="text-muted">No active ChoziCode found</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    📈 Total Commissions
                                </h5>
                                <h4 class="text-info">KES {{ number_format($totalCommissions ?? 0, 2) }}</h4>
                                <p class="text-muted mb-0">Lifetime earnings</p>
                            </div>
                        </div>
                    </div>
                @elseif($user->role === 'owner')
                    <!-- Owner Stats -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    ⬇️ Received Payments
                                </h5>
                                <h4 class="text-success">KES {{ number_format($receivedPayments ?? 0, 2) }}</h4>
                                <p class="text-muted mb-0">Total completed payments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    ⏰ Pending Payments
                                </h5>
                                <h4 class="text-warning">{{ $pendingPayments ?? 0 }}</h4>
                                <p class="text-muted mb-0">Awaiting processing</p>
                            </div>
                        </div>
                    </div>
                @elseif($user->role === 'renter')
                    <!-- Renter Stats -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    ⬆️ Sent Payments
                                </h5>
                                <h4 class="text-primary">KES {{ number_format($sentPayments ?? 0, 2) }}</h4>
                                <p class="text-muted mb-0">Total payments made</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    ⏰ Pending Payments
                                </h5>
                                <h4 class="text-warning">{{ $pendingPayments ?? 0 }}</h4>
                                <p class="text-muted mb-0">Awaiting processing</p>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Admin Stats -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    👥 Total Users
                                </h5>
                                <h4 class="text-primary">{{ \App\Models\User::count() }}</h4>
                                <p class="text-muted mb-0">Registered users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    💳 Total Payments
                                </h5>
                                <h4 class="text-success">{{ \App\Models\Payment::count() }}</h4>
                                <p class="text-muted mb-0">All payments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    🏷️ ChoziCodes
                                </h5>
                                <h4 class="text-info">{{ \App\Models\ChoziCode::count() }}</h4>
                                <p class="text-muted mb-0">Active codes</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Recent Transactions -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                🕒 Recent Transactions
                            </h5>
                            @if($user->role !== 'admin')
                                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($recentTransactions && $recentTransactions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentTransactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->created_at->format('M j, Y') }}</td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ ucfirst($transaction->type) }}</span>
                                                    </td>
                                                    <td>{{ $transaction->description }}</td>
                                                    <td>
                                                        <span class="fw-bold {{ $transaction->amount > 0 ? 'text-success' : 'text-danger' }}">
                                                            {{ $transaction->amount > 0 ? '+' : '' }}KES {{ number_format($transaction->amount, 2) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($transaction->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <span style="font-size: 3rem; color: #6c757d;">📥</span>
                                    <p class="text-muted mt-2">No recent transactions found</p>
                                    @if($user->role === 'renter')
                                        <a href="#" class="btn btn-primary">Make a Payment</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                ⚡ Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($user->role === 'renter')
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-primary w-100">
                                            💳 Make Payment
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-info w-100">
                                            🕒 Payment History
                                        </a>
                                    </div>
                                @elseif($user->role === 'owner')
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-success w-100">
                                            ⬇️ View Payments
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-info w-100">
                                            🏢 Manage Properties
                                        </a>
                                    </div>
                                @elseif($user->role === 'broker')
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-warning w-100">
                                            🏷️ Manage Codes
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-success w-100">
                                            📈 Commission Report
                                        </a>
                                    </div>
                                @else
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-primary w-100">
                                            👥 Manage Users
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-success w-100">
                                            💳 View Payments
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-info w-100">
                                            ⚙️ System Settings
                                        </a>
                                    </div>
                                @endif
                                <div class="col-md-3 mb-3">
                                    <a href="#" class="btn btn-outline-secondary w-100">
                                        ⚙️ Profile Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh dashboard data every 30 seconds
    setInterval(function() {
        // In a real implementation, you would refresh specific data via AJAX
        console.log('Dashboard data refresh would happen here');
    }, 30000);
});
</script>
@endsection 