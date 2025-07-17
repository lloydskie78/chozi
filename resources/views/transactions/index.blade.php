@extends('layouts.app')

@section('title', 'Transaction History - ChoziPay')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">📋 Transaction History</h2>
                    <p class="text-muted mb-0">Complete audit trail of your account activity</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="exportTransactions()">
                        <span class="me-1">📥</span>Export
                    </button>
                    <button class="btn btn-primary" onclick="loadTransactions()">
                        <span class="me-1">🔄</span>Refresh
                    </button>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">🔍 Filter Transactions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="typeFilter" class="form-label">Transaction Type</label>
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="login">Login</option>
                                <option value="logout">Logout</option>
                                <option value="payment">Payment</option>
                                <option value="chozi_code_usage">ChoziCode Usage</option>
                                <option value="chozi_code_generation">ChoziCode Generation</option>
                                <option value="failed_login">Failed Login</option>
                                <option value="password_change">Password Change</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="severityFilter" class="form-label">Severity</label>
                            <select class="form-select" id="severityFilter">
                                <option value="">All Severities</option>
                                <option value="info">Info</option>
                                <option value="warning">Warning</option>
                                <option value="error">Error</option>
                                <option value="critical">Critical</option>
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
                    <button class="btn btn-primary btn-sm" onclick="loadTransactions()">
                        <span class="me-1">🔍</span>Apply Filters
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                        <span class="me-1">🗑️</span>Clear
                    </button>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📋 Transaction Log</h5>
                    <div>
                        <small class="text-muted me-3" id="transactionCount">Loading...</small>
                        <button class="btn btn-outline-secondary btn-sm" onclick="toggleSuspiciousOnly()">
                            <span class="me-1" id="suspiciousIcon">🚨</span>
                            <span id="suspiciousText">Suspicious Only</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading transactions...</p>
                    </div>
                    
                    <div id="transactionsContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Severity</th>
                                        <th>IP Address</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionsTableBody">
                                    <!-- Transaction rows will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <nav aria-label="Transaction pagination">
                            <ul class="pagination justify-content-center" id="paginationContainer">
                                <!-- Pagination will be inserted here -->
                            </ul>
                        </nav>
                    </div>
                    
                    <div id="noTransactions" class="text-center py-4" style="display: none;">
                        <span class="fs-1">📋</span>
                        <h5 class="mt-3">No Transactions Found</h5>
                        <p class="text-muted">No transactions match your current filter criteria.</p>
                        <button class="btn btn-outline-primary" onclick="clearFilters()">
                            <span class="me-1">🗑️</span>Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">📋 Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailsModalBody">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPage = 1;
let showSuspiciousOnly = false;

document.addEventListener('DOMContentLoaded', function() {
    loadTransactions();
});

async function loadTransactions(page = 1) {
    currentPage = page;
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('transactionsContainer').style.display = 'none';
    document.getElementById('noTransactions').style.display = 'none';
    
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: 20
        });
        
        // Add filters
        const type = document.getElementById('typeFilter').value;
        const severity = document.getElementById('severityFilter').value;
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        
        if (type) params.append('type', type);
        if (severity) params.append('severity', severity);
        if (fromDate) params.append('from_date', fromDate);
        if (toDate) params.append('to_date', toDate);
        if (showSuspiciousOnly) params.append('suspicious_only', '1');
        
        // Simulate API call - in real app, this would be an actual API endpoint
        setTimeout(() => {
            const mockData = generateMockTransactions();
            displayTransactions(mockData.data);
            displayPagination(mockData.pagination);
            document.getElementById('transactionCount').textContent = `${mockData.pagination.total} transactions`;
            
            document.getElementById('loadingSpinner').style.display = 'none';
            if (mockData.data.length > 0) {
                document.getElementById('transactionsContainer').style.display = 'block';
            } else {
                document.getElementById('noTransactions').style.display = 'block';
            }
        }, 1000);
        
    } catch (error) {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('noTransactions').style.display = 'block';
        console.error('Error loading transactions:', error);
        showAlert('Error loading transactions', 'error');
    }
}

function generateMockTransactions() {
    // Mock transaction data for demonstration
    const types = ['login', 'payment', 'chozi_code_usage', 'logout'];
    const severities = ['info', 'warning', 'error'];
    const actions = ['User login', 'Payment processed', 'ChoziCode used', 'User logout', 'Failed login attempt'];
    
    const data = [];
    for (let i = 0; i < 15; i++) {
        const date = new Date();
        date.setHours(date.getHours() - Math.floor(Math.random() * 24 * 7)); // Random time in last week
        
        data.push({
            id: i + 1,
            transaction_type: types[Math.floor(Math.random() * types.length)],
            action: actions[Math.floor(Math.random() * actions.length)],
            description: `Sample transaction ${i + 1} - automated activity`,
            amount: Math.random() > 0.7 ? (Math.random() * 1000).toFixed(2) : null,
            severity: severities[Math.floor(Math.random() * severities.length)],
            ip_address: `192.168.1.${Math.floor(Math.random() * 255)}`,
            is_suspicious: Math.random() > 0.9,
            created_at: date.toISOString(),
            data: {
                user_agent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            }
        });
    }
    
    return {
        data: data,
        pagination: {
            current_page: currentPage,
            last_page: 3,
            total: 45,
            per_page: 20
        }
    };
}

function displayTransactions(transactions) {
    const tbody = document.getElementById('transactionsTableBody');
    tbody.innerHTML = '';
    
    transactions.forEach(transaction => {
        const row = document.createElement('tr');
        
        if (transaction.is_suspicious) {
            row.classList.add('table-warning');
        }
        
        const severityBadge = getSeverityBadge(transaction.severity);
        const typeBadge = getTypeBadge(transaction.transaction_type);
        const statusIcon = transaction.is_suspicious ? '🚨' : '✅';
        
        row.innerHTML = `
            <td>
                <small>${formatDateTime(transaction.created_at)}</small>
            </td>
            <td>${typeBadge}</td>
            <td>
                <strong>${transaction.action}</strong>
            </td>
            <td>
                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="${transaction.description}">
                    ${transaction.description}
                </span>
            </td>
            <td>
                ${transaction.amount ? `<strong>$${transaction.amount}</strong>` : '-'}
            </td>
            <td>${severityBadge}</td>
            <td>
                <code class="small">${transaction.ip_address}</code>
            </td>
            <td>
                <span title="${transaction.is_suspicious ? 'Suspicious Activity' : 'Normal Activity'}">
                    ${statusIcon}
                </span>
                <button class="btn btn-sm btn-outline-info ms-1" onclick="showTransactionDetails(${transaction.id})">
                    <span class="small">👁️</span>
                </button>
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
        prevItem.innerHTML = `<a class="page-link" href="#" onclick="loadTransactions(${paginationData.current_page - 1})">Previous</a>`;
        container.appendChild(prevItem);
    }
    
    // Page numbers
    for (let i = Math.max(1, paginationData.current_page - 2); i <= Math.min(paginationData.last_page, paginationData.current_page + 2); i++) {
        const pageItem = document.createElement('li');
        pageItem.className = `page-item ${i === paginationData.current_page ? 'active' : ''}`;
        pageItem.innerHTML = `<a class="page-link" href="#" onclick="loadTransactions(${i})">${i}</a>`;
        container.appendChild(pageItem);
    }
    
    // Next button
    if (paginationData.current_page < paginationData.last_page) {
        const nextItem = document.createElement('li');
        nextItem.className = 'page-item';
        nextItem.innerHTML = `<a class="page-link" href="#" onclick="loadTransactions(${paginationData.current_page + 1})">Next</a>`;
        container.appendChild(nextItem);
    }
}

function getSeverityBadge(severity) {
    const badges = {
        'info': '<span class="badge bg-info">ℹ️ Info</span>',
        'warning': '<span class="badge bg-warning text-dark">⚠️ Warning</span>',
        'error': '<span class="badge bg-danger">❌ Error</span>',
        'critical': '<span class="badge bg-dark">🚨 Critical</span>'
    };
    return badges[severity] || `<span class="badge bg-secondary">${severity}</span>`;
}

function getTypeBadge(type) {
    const badges = {
        'login': '<span class="badge bg-success">🔐 Login</span>',
        'logout': '<span class="badge bg-secondary">🚪 Logout</span>',
        'payment': '<span class="badge bg-primary">💳 Payment</span>',
        'chozi_code_usage': '<span class="badge bg-warning text-dark">🏷️ Code Used</span>',
        'chozi_code_generation': '<span class="badge bg-info">➕ Code Generated</span>',
        'failed_login': '<span class="badge bg-danger">❌ Failed Login</span>',
        'password_change': '<span class="badge bg-warning text-dark">🔑 Password</span>'
    };
    return badges[type] || `<span class="badge bg-light text-dark">${type}</span>`;
}

function toggleSuspiciousOnly() {
    showSuspiciousOnly = !showSuspiciousOnly;
    const icon = document.getElementById('suspiciousIcon');
    const text = document.getElementById('suspiciousText');
    
    if (showSuspiciousOnly) {
        icon.textContent = '✅';
        text.textContent = 'Show All';
    } else {
        icon.textContent = '🚨';
        text.textContent = 'Suspicious Only';
    }
    
    loadTransactions();
}

function clearFilters() {
    document.getElementById('typeFilter').value = '';
    document.getElementById('severityFilter').value = '';
    document.getElementById('fromDate').value = '';
    document.getElementById('toDate').value = '';
    showSuspiciousOnly = false;
    
    document.getElementById('suspiciousIcon').textContent = '🚨';
    document.getElementById('suspiciousText').textContent = 'Suspicious Only';
    
    loadTransactions();
}

function showTransactionDetails(transactionId) {
    // Mock transaction details
    const detailsHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Transaction Information</h6>
                <p><strong>ID:</strong> ${transactionId}</p>
                <p><strong>Type:</strong> Payment Processing</p>
                <p><strong>Action:</strong> Payment sent successfully</p>
                <p><strong>Severity:</strong> Info</p>
            </div>
            <div class="col-md-6">
                <h6>Security Details</h6>
                <p><strong>IP Address:</strong> 192.168.1.100</p>
                <p><strong>User Agent:</strong> Mozilla/5.0...</p>
                <p><strong>Session ID:</strong> sess_abc123...</p>
                <p><strong>Suspicious:</strong> No</p>
            </div>
        </div>
        <hr>
        <h6>Additional Data</h6>
        <pre class="bg-light p-3 rounded"><code>{
  "amount": "150.00",
  "recipient": "owner@example.com",
  "payment_reference": "PAY123456789"
}</code></pre>
    `;
    
    document.getElementById('detailsModalBody').innerHTML = detailsHTML;
    const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
    modal.show();
}

function exportTransactions() {
    showAlert('Export functionality coming soon', 'info');
}

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
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