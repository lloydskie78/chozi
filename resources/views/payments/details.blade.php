@extends('layouts.app')

@section('title', 'Payment Details - ChoziPay')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">💳 Payment Details</h2>
                    <p class="text-muted mb-0">Transaction Reference: <code>{{ $reference }}</code></p>
                </div>
                <div>
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary me-2">
                        <span class="me-1">📋</span>Back to History
                    </a>
                    <button class="btn btn-primary" onclick="window.print()">
                        <span class="me-1">🖨️</span>Print Receipt
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading payment details...</p>
            </div>

            <!-- Payment Not Found -->
            <div id="notFoundState" class="text-center py-5" style="display: none;">
                <span class="fs-1">❌</span>
                <h3 class="mt-3">Payment Not Found</h3>
                <p class="text-muted">The payment reference you're looking for doesn't exist or you don't have access to it.</p>
                <a href="{{ route('payments.index') }}" class="btn btn-primary">
                    <span class="me-1">📋</span>View All Payments
                </a>
            </div>

            <!-- Payment Details Content -->
            <div id="paymentContent" style="display: none;">
                <!-- Status Banner -->
                <div class="alert" id="statusBanner">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="alert-heading mb-1" id="statusTitle">Payment Status</h5>
                            <small id="statusMessage">Status information</small>
                        </div>
                        <span class="fs-1" id="statusIcon">💳</span>
                    </div>
                </div>

                <!-- Main Payment Information -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">📄 Payment Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Reference Number</label>
                                        <p class="fw-bold mb-0">
                                            <code class="text-primary fs-6" id="paymentReference">{{ $reference }}</code>
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Payment Type</label>
                                        <p class="fw-bold mb-0" id="paymentType">
                                            <span id="typeIcon">💰</span> <span id="typeText">Loading...</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Amount</label>
                                        <p class="fw-bold mb-0 fs-4 text-primary" id="paymentAmount">$0.00</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Date & Time</label>
                                        <p class="fw-bold mb-0" id="paymentDate">Loading...</p>
                                    </div>
                                    <div class="col-md-12 mb-3" id="descriptionSection" style="display: none;">
                                        <label class="form-label text-muted">Description</label>
                                        <p class="mb-0" id="paymentDescription"></p>
                                    </div>
                                    <div class="col-md-12" id="propertySection" style="display: none;">
                                        <label class="form-label text-muted">Property Details</label>
                                        <p class="mb-0" id="propertyDetails"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Amount Breakdown -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">💰 Amount Breakdown</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Payment Amount:</span>
                                    <strong id="breakdownAmount">$0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2" id="commissionRow" style="display: none;">
                                    <span>Broker Commission:</span>
                                    <strong class="text-warning" id="breakdownCommission">$0.00</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span><strong>Recipient Receives:</strong></span>
                                    <strong class="text-success" id="breakdownNet">$0.00</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Security Info -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">🔐 Security</h6>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">
                                    <div class="mb-1">
                                        <strong>Transaction Hash:</strong><br>
                                        <code class="small" id="transactionHash">Loading...</code>
                                    </div>
                                    <div>
                                        <strong>Processed:</strong><br>
                                        <span id="processedTime">Loading...</span>
                                    </div>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parties Involved -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">📤 From (Payer)</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="bg-primary text-white rounded-circle p-2 fs-4">👤</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1" id="payerName">Loading...</h6>
                                        <p class="mb-0 text-muted" id="payerEmail">Loading...</p>
                                        <small class="badge bg-primary" id="payerRole">Loading...</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">📥 To (Recipient)</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="bg-success text-white rounded-circle p-2 fs-4">👤</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1" id="recipientName">Loading...</h6>
                                        <p class="mb-0 text-muted" id="recipientEmail">Loading...</p>
                                        <small class="badge bg-success" id="recipientRole">Loading...</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ChoziCode Information -->
                <div id="choziCodeSection" class="row mb-4" style="display: none;">
                    <div class="col-md-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">🏷️ ChoziCode Commission</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <span class="bg-warning text-dark rounded-circle p-2 fs-4">🏷️</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">ChoziCode: <code id="choziCodeValue">Loading...</code></h6>
                                                <p class="mb-0 text-muted">Broker: <span id="brokerName">Loading...</span></p>
                                                <small class="badge bg-warning text-dark" id="brokerEmail">Loading...</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6 class="text-muted mb-1">Commission Earned</h6>
                                        <h4 class="text-warning mb-0" id="commissionAmount">$0.00</h4>
                                        <small class="text-muted">Rate: <span id="commissionRate">5%</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Timeline (if available) -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">⏱️ Transaction Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div id="timelineContent">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="bg-info text-white rounded-circle p-2">1</span>
                                </div>
                                <div>
                                    <h6 class="mb-1">Payment Initiated</h6>
                                    <small class="text-muted" id="initiatedTime">Loading...</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3" id="processingStep" style="display: none;">
                                <div class="me-3">
                                    <span class="bg-warning text-white rounded-circle p-2">2</span>
                                </div>
                                <div>
                                    <h6 class="mb-1">Processing Payment</h6>
                                    <small class="text-muted" id="processingTime">Loading...</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center" id="completedStep" style="display: none;">
                                <div class="me-3">
                                    <span class="bg-success text-white rounded-circle p-2">3</span>
                                </div>
                                <div>
                                    <h6 class="mb-1">Payment Completed</h6>
                                    <small class="text-muted" id="completedTime">Loading...</small>
                                </div>
                            </div>
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
    loadPaymentDetails();
});

async function loadPaymentDetails() {
    const reference = '{{ $reference }}';
    
    try {
        const response = await fetch(`/api/payments/${reference}`, {
            headers: {
                'Authorization': 'Bearer ' + getAuthToken(),
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const result = await response.json();
            if (result.success) {
                displayPaymentDetails(result.data);
                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('paymentContent').style.display = 'block';
            } else {
                showNotFound();
            }
        } else {
            showNotFound();
        }
        
    } catch (error) {
        console.error('Error loading payment details:', error);
        showNotFound();
    }
}

function displayPaymentDetails(payment) {
    // Status Banner
    updateStatusBanner(payment.status);
    
    // Basic Information
    document.getElementById('paymentReference').textContent = payment.payment_reference;
    document.getElementById('paymentAmount').textContent = '$' + parseFloat(payment.amount).toFixed(2);
    document.getElementById('paymentDate').textContent = formatDateTime(payment.created_at);
    
    // Payment Type
    const typeIcon = getTypeIcon(payment.payment_type);
    document.getElementById('typeIcon').textContent = typeIcon;
    document.getElementById('typeText').textContent = capitalizeFirst(payment.payment_type);
    
    // Description and Property Details
    if (payment.description) {
        document.getElementById('paymentDescription').textContent = payment.description;
        document.getElementById('descriptionSection').style.display = 'block';
    }
    if (payment.property_details) {
        document.getElementById('propertyDetails').textContent = payment.property_details;
        document.getElementById('propertySection').style.display = 'block';
    }
    
    // Amount Breakdown
    document.getElementById('breakdownAmount').textContent = '$' + parseFloat(payment.amount).toFixed(2);
    document.getElementById('breakdownNet').textContent = '$' + parseFloat(payment.net_amount).toFixed(2);
    
    if (payment.broker_commission > 0) {
        document.getElementById('breakdownCommission').textContent = '$' + parseFloat(payment.broker_commission).toFixed(2);
        document.getElementById('commissionRow').style.display = 'flex';
    }
    
    // Security Information
    document.getElementById('transactionHash').textContent = payment.transaction_hash || 'N/A';
    document.getElementById('processedTime').textContent = payment.processed_at ? formatDateTime(payment.processed_at) : 'Not yet processed';
    
    // Parties
    if (payment.payer) {
        document.getElementById('payerName').textContent = payment.payer.name;
        document.getElementById('payerEmail').textContent = payment.payer.email;
        document.getElementById('payerRole').textContent = capitalizeFirst(payment.payer.role || 'User');
    }
    
    if (payment.recipient) {
        document.getElementById('recipientName').textContent = payment.recipient.name;
        document.getElementById('recipientEmail').textContent = payment.recipient.email;
        document.getElementById('recipientRole').textContent = capitalizeFirst(payment.recipient.role || 'User');
    }
    
    // ChoziCode Information
    if (payment.chozi_code && payment.broker) {
        document.getElementById('choziCodeValue').textContent = payment.chozi_code.code;
        document.getElementById('brokerName').textContent = payment.broker.name;
        document.getElementById('brokerEmail').textContent = payment.broker.email;
        document.getElementById('commissionAmount').textContent = '$' + parseFloat(payment.broker_commission).toFixed(2);
        document.getElementById('choziCodeSection').style.display = 'block';
    }
    
    // Timeline
    updateTimeline(payment);
}

function updateStatusBanner(status) {
    const banner = document.getElementById('statusBanner');
    const title = document.getElementById('statusTitle');
    const message = document.getElementById('statusMessage');
    const icon = document.getElementById('statusIcon');
    
    banner.className = 'alert';
    
    switch (status) {
        case 'pending':
            banner.classList.add('alert-warning');
            title.textContent = '⏳ Payment Pending';
            message.textContent = 'Payment is waiting to be processed';
            icon.textContent = '⏳';
            break;
        case 'processing':
            banner.classList.add('alert-info');
            title.textContent = '⚙️ Payment Processing';
            message.textContent = 'Payment is currently being processed';
            icon.textContent = '⚙️';
            break;
        case 'completed':
            banner.classList.add('alert-success');
            title.textContent = '✅ Payment Completed';
            message.textContent = 'Payment has been successfully processed';
            icon.textContent = '✅';
            break;
        case 'failed':
            banner.classList.add('alert-danger');
            title.textContent = '❌ Payment Failed';
            message.textContent = 'Payment processing failed';
            icon.textContent = '❌';
            break;
        case 'cancelled':
            banner.classList.add('alert-secondary');
            title.textContent = '🚫 Payment Cancelled';
            message.textContent = 'Payment was cancelled';
            icon.textContent = '🚫';
            break;
        default:
            banner.classList.add('alert-secondary');
            title.textContent = '💳 Payment Status';
            message.textContent = 'Status: ' + status;
            icon.textContent = '💳';
    }
}

function updateTimeline(payment) {
    document.getElementById('initiatedTime').textContent = formatDateTime(payment.created_at);
    
    if (payment.status === 'processing' || payment.status === 'completed') {
        document.getElementById('processingStep').style.display = 'flex';
        document.getElementById('processingTime').textContent = 'Processing...';
    }
    
    if (payment.status === 'completed' && payment.processed_at) {
        document.getElementById('completedStep').style.display = 'flex';
        document.getElementById('completedTime').textContent = formatDateTime(payment.processed_at);
    }
}

function showNotFound() {
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('notFoundState').style.display = 'block';
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

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

function getAuthToken() {
    return 'session-token';
}
</script>

<style>
@media print {
    .btn, .navbar, .alert .btn-close {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
    
    body {
        background: white !important;
    }
}
</style>
@endpush 