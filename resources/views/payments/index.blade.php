@extends('layouts.app')

@section('title', 'Payments - ChoziPay')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">💳 Payments</h2>
                    <p class="text-muted mb-0">Manage your payment transactions</p>
                </div>
                <a href="{{ route('payments.send') }}" class="btn btn-primary">
                    <span class="me-1">➕</span>Send Payment
                </a>
            </div>

            <!-- Payment Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Sent</h6>
                                    <h4 class="mb-0" id="totalSent">$0.00</h4>
                                </div>
                                <span class="fs-2">📤</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Received</h6>
                                    <h4 class="mb-0" id="totalReceived">$0.00</h4>
                                </div>
                                <span class="fs-2">📥</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Pending</h6>
                                    <h4 class="mb-0" id="pendingCount">0</h4>
                                </div>
                                <span class="fs-2">⏳</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Completed</h6>
                                    <h4 class="mb-0" id="completedCount">0</h4>
                                </div>
                                <span class="fs-2">✅</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">🔍 Filter Payments</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="statusFilter" class="form-label">Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="typeFilter" class="form-label">Type</label>
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="rent">Rent</option>
                                <option value="deposit">Deposit</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="fromDate" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="fromDate">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="toDate" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="toDate">
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm" onclick="loadPayments()">
                        <span class="me-1">🔍</span>Apply Filters
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                        <span class="me-1">🗑️</span>Clear
                    </button>
                </div>
            </div>

            <!-- Payment History -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">📋 Payment History</h6>
                    <button class="btn btn-outline-primary btn-sm" onclick="loadPayments()">
                        <span class="me-1">🔄</span>Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading payments...</p>
                    </div>
                    <div id="paymentsContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reference</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>From/To</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsTableBody">
                                    <!-- Payment rows will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                        <nav aria-label="Payments pagination">
                            <ul class="pagination justify-content-center" id="paginationContainer">
                                <!-- Pagination will be inserted here -->
                            </ul>
                        </nav>
                    </div>
                    <div id="noPayments" class="text-center py-4" style="display: none;">
                        <span class="fs-1">💸</span>
                        <h5 class="mt-3">No Payments Found</h5>
                        <p class="text-muted">You haven't made or received any payments yet.</p>
                        <a href="{{ route('payments.send') }}" class="btn btn-primary">
                            <span class="me-1">➕</span>Send Your First Payment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadPaymentStats();
    loadPayments();
});

async function loadPaymentStats() {
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
                document.getElementById('pendingCount').textContent = data.data.pending_payments || 0;
                document.getElementById('completedCount').textContent = data.data.completed_payments || 0;
            }
        }
    } catch (error) {
        console.error('Error loading payment stats:', error);
    }
}

async function loadPayments(page = 1) {
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('paymentsContainer').style.display = 'none';
    document.getElementById('noPayments').style.display = 'none';
    
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: 15
        });
        
        // Add filters
        const status = document.getElementById('statusFilter').value;
        const type = document.getElementById('typeFilter').value;
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        
        if (status) params.append('status', status);
        if (type) params.append('type', type);
        if (fromDate) params.append('from_date', fromDate);
        if (toDate) params.append('to_date', toDate);
        
        const response = await fetch('/api/payments?' + params.toString(), {
            headers: {
                'Authorization': 'Bearer ' + getAuthToken(),
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        document.getElementById('loadingSpinner').style.display = 'none';
        
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.data.data.length > 0) {
                displayPayments(data.data.data);
                displayPagination(data.data);
                document.getElementById('paymentsContainer').style.display = 'block';
            } else {
                document.getElementById('noPayments').style.display = 'block';
            }
        } else {
            throw new Error('Failed to load payments');
        }
    } catch (error) {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('noPayments').style.display = 'block';
        console.error('Error loading payments:', error);
        showAlert('Error loading payments', 'error');
    }
}

function displayPayments(payments) {
    const tbody = document.getElementById('paymentsTableBody');
    tbody.innerHTML = '';
    
    payments.forEach(payment => {
        const row = document.createElement('tr');
        
        const statusBadge = getStatusBadge(payment.status);
        const typeIcon = getTypeIcon(payment.payment_type);
        const isReceived = payment.recipient_id === getCurrentUserId();
        const otherParty = isReceived ? payment.payer : payment.recipient;
        const direction = isReceived ? '📥 From' : '📤 To';
        
        row.innerHTML = `
            <td>
                <code class="text-primary">${payment.payment_reference}</code>
            </td>
            <td>
                <span class="me-1">${typeIcon}</span>${capitalizeFirst(payment.payment_type)}
            </td>
            <td>
                <strong>$${parseFloat(payment.amount).toFixed(2)}</strong>
                ${payment.broker_commission > 0 ? `<br><small class="text-muted">Commission: $${parseFloat(payment.broker_commission).toFixed(2)}</small>` : ''}
            </td>
            <td>${statusBadge}</td>
            <td>
                <div>
                    <small class="text-muted">${direction}</small><br>
                    <strong>${otherParty ? otherParty.name : 'Unknown'}</strong><br>
                    <small class="text-muted">${otherParty ? otherParty.email : ''}</small>
                </div>
            </td>
            <td>
                <small>${formatDate(payment.created_at)}</small>
            </td>
            <td>
                <a href="{{ route('payments.details', '') }}/${payment.payment_reference}" class="btn btn-outline-primary btn-sm">
                    <span class="me-1">👁️</span>View
                </a>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function displayPagination(paginationData) {
    const container = document.getElementById('paginationContainer');
    container.innerHTML = '';
    
    if (paginationData.last_page <= 1) return;
    
    // Previous button
    if (paginationData.current_page > 1) {
        const prevItem = document.createElement('li');
        prevItem.className = 'page-item';
        prevItem.innerHTML = `<a class="page-link" href="#" onclick="loadPayments(${paginationData.current_page - 1})">Previous</a>`;
        container.appendChild(prevItem);
    }
    
    // Page numbers
    for (let i = Math.max(1, paginationData.current_page - 2); i <= Math.min(paginationData.last_page, paginationData.current_page + 2); i++) {
        const pageItem = document.createElement('li');
        pageItem.className = `page-item ${i === paginationData.current_page ? 'active' : ''}`;
        pageItem.innerHTML = `<a class="page-link" href="#" onclick="loadPayments(${i})">${i}</a>`;
        container.appendChild(pageItem);
    }
    
    // Next button
    if (paginationData.current_page < paginationData.last_page) {
        const nextItem = document.createElement('li');
        nextItem.className = 'page-item';
        nextItem.innerHTML = `<a class="page-link" href="#" onclick="loadPayments(${paginationData.current_page + 1})">Next</a>`;
        container.appendChild(nextItem);
    }
}

function clearFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('fromDate').value = '';
    document.getElementById('toDate').value = '';
    loadPayments();
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">⏳ Pending</span>',
        'processing': '<span class="badge bg-info">⚙️ Processing</span>',
        'completed': '<span class="badge bg-success">✅ Completed</span>',
        'failed': '<span class="badge bg-danger">❌ Failed</span>',
        'cancelled': '<span class="badge bg-secondary">🚫 Cancelled</span>'
    };
    return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
}

function getTypeIcon(type) {
    const icons = {
        'rent': '🏠',
        'deposit': '💰',
        'maintenance': '🔧',
        'other': '📄'
    };
    return icons[type] || '💸';
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getCurrentUserId() {
    // This should be set from the backend or user session
    return {{ auth()->user()->id }};
}

function getAuthToken() {
    // For now, we'll use session-based auth
    // In a production app, this would be a proper API token
    return 'session-token';
}

function showAlert(message, type = 'info') {
    // Simple alert for now - can be replaced with a proper notification system
    alert(message);
}
</script>
@endpush 