@extends('layouts.app')

@section('title', 'Dashboard - ChoziPay')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Welcome Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Welcome back, {{ auth()->user()->name }}!</h1>
                    <p class="text-muted">{{ ucfirst(auth()->user()->role) }} Dashboard</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 p-2">
                        <i class="bi bi-wallet2 me-1"></i>
                        Balance: ${{ number_format(auth()->user()->wallet_balance, 2) }}
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row mb-4">
                @if(isset($totalCommissions))
                    <!-- Broker Stats -->
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Total Commissions</h6>
                                        <h4>${{ number_format($totalCommissions, 2) }}</h4>
                                    </div>
                                    <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Active ChoziCode</h6>
                                        <h4>{{ $choziCode ? $choziCode->code : 'None' }}</h4>
                                    </div>
                                    <i class="bi bi-qr-code fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($receivedPayments))
                    <!-- Owner Stats -->
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Received</h6>
                                        <h4>${{ number_format($receivedPayments, 2) }}</h4>
                                    </div>
                                    <i class="bi bi-arrow-down-circle fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Pending</h6>
                                        <h4>{{ $pendingPayments }}</h4>
                                    </div>
                                    <i class="bi bi-clock fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($sentPayments))
                    <!-- Renter Stats -->
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Total Sent</h6>
                                        <h4>${{ number_format($sentPayments, 2) }}</h4>
                                    </div>
                                    <i class="bi bi-arrow-up-circle fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Pending</h6>
                                        <h4>{{ $pendingPayments }}</h4>
                                    </div>
                                    <i class="bi bi-clock fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-send fs-1 text-primary mb-3"></i>
                            <h5 class="card-title">Send Payment</h5>
                            <p class="card-text">Send rental payments securely with optional ChoziCode for broker commission.</p>
                            <a href="{{ route('payments.send') }}" class="btn btn-primary">
                                <i class="bi bi-arrow-right me-1"></i>Send Payment
                            </a>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->isBroker())
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-qr-code fs-1 text-info mb-3"></i>
                            <h5 class="card-title">Manage ChoziCode</h5>
                            <p class="card-text">Generate and manage your ChoziCodes for earning commissions on referrals.</p>
                            <a href="{{ route('chozi-codes.index') }}" class="btn btn-info">
                                <i class="bi bi-gear me-1"></i>Manage Codes
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-list-ul fs-1 text-success mb-3"></i>
                            <h5 class="card-title">Transaction History</h5>
                            <p class="card-text">View detailed history of all your payments and transactions.</p>
                            <a href="{{ route('transactions.index') }}" class="btn btn-success">
                                <i class="bi bi-eye me-1"></i>View History
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            @if(isset($recentTransactions) && $recentTransactions->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Recent Activity
                    </h5>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
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
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}</span>
                                    </td>
                                    <td>{{ Str::limit($transaction->description, 50) }}</td>
                                    <td>
                                        @if($transaction->amount)
                                            ${{ number_format($transaction->amount, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->is_suspicious)
                                            <span class="badge bg-danger">Flagged</span>
                                        @else
                                            <span class="badge bg-success">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Security Notice -->
            <div class="alert alert-info mt-4" role="alert">
                <i class="bi bi-shield-check me-2"></i>
                <strong>Security Notice:</strong> All transactions are encrypted and logged for your security. 
                If you notice any suspicious activity, please contact support immediately.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-refresh dashboard data every 30 seconds
    setInterval(() => {
        // In a real implementation, this would use AJAX to refresh data
        console.log('Dashboard data refresh would happen here');
    }, 30000);
</script>
@endpush
@endsection
