@extends('layouts.app')

@section('title', 'Send Payment - ChoziPay')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">💳 Send Payment</h2>
                    <p class="text-muted mb-0">Send secure rental payments</p>
                </div>
                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                    <span class="me-1">📋</span>View History
                </a>
            </div>

            <!-- Wallet Balance Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">💰 Your Wallet Balance</h6>
                            <h3 class="text-primary fw-bold mb-0">${{ number_format(auth()->user()->wallet_balance, 2) }}</h3>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-outline-primary btn-sm" onclick="refreshBalance()">
                                <span class="me-1">🔄</span>Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Send Payment Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📤 Payment Details</h5>
                </div>
                <div class="card-body">
                    <form id="sendPaymentForm">
                        @csrf
                        
                        <!-- Recipient Information -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="recipient_email" class="form-label">
                                    <span class="me-1">👤</span>Recipient Email Address
                                </label>
                                <input type="email" class="form-control" id="recipient_email" name="recipient_email" required>
                                <div class="form-text">Enter the email address of the property owner</div>
                                <div id="recipientInfo" class="mt-2" style="display: none;">
                                    <div class="alert alert-info">
                                        <span class="me-1">✅</span>
                                        <strong id="recipientName"></strong> - <span id="recipientRole"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Amount -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">
                                    <span class="me-1">💵</span>Payment Amount (USD)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                           min="1" max="999999.99" step="0.01" required>
                                </div>
                                <div class="form-text">Maximum amount: $999,999.99</div>
                            </div>
                            <div class="col-md-6">
                                <label for="payment_type" class="form-label">
                                    <span class="me-1">🏷️</span>Payment Type
                                </label>
                                <select class="form-select" id="payment_type" name="payment_type" required>
                                    <option value="rent">🏠 Rent</option>
                                    <option value="deposit">💰 Deposit</option>
                                    <option value="maintenance">🔧 Maintenance</option>
                                    <option value="other">📄 Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- ChoziCode Section -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <span class="me-1">🏷️</span>ChoziCode (Optional)
                                </h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="chozi_code" class="form-label">Broker Referral Code</label>
                                        <input type="text" class="form-control" id="chozi_code" name="chozi_code" 
                                               placeholder="Enter ChoziCode if referred by a broker">
                                        <div class="form-text">Add a ChoziCode to give your broker 5% commission</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-outline-primary w-100" onclick="validateChoziCode()">
                                            <span class="me-1">✅</span>Validate Code
                                        </button>
                                    </div>
                                </div>
                                <div id="choziCodeInfo" class="mt-3" style="display: none;">
                                    <div class="alert alert-success">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong>Valid ChoziCode!</strong><br>
                                                <small>Broker: <span id="brokerName"></span></small><br>
                                                <small>Commission: <span id="commissionRate">5%</span></small>
                                            </div>
                                            <span class="fs-3">✅</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="choziCodeError" class="mt-3" style="display: none;">
                                    <div class="alert alert-warning">
                                        <span class="me-1">⚠️</span>Invalid or expired ChoziCode
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description and Property Details -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="description" class="form-label">
                                    <span class="me-1">📝</span>Description (Optional)
                                </label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="3" maxlength="1000" placeholder="Payment description..."></textarea>
                                <div class="form-text">Max 1000 characters</div>
                            </div>
                            <div class="col-md-6">
                                <label for="property_details" class="form-label">
                                    <span class="me-1">🏠</span>Property Details (Optional)
                                </label>
                                <textarea class="form-control" id="property_details" name="property_details" 
                                          rows="3" maxlength="2000" placeholder="Property address, unit number, etc..."></textarea>
                                <div class="form-text">Max 2000 characters</div>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="card bg-primary text-white mb-4" id="paymentSummary" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title mb-3">💰 Payment Summary</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small>Payment Amount:</small><br>
                                        <strong id="summaryAmount">$0.00</strong>
                                    </div>
                                    <div class="col-6" id="commissionSection" style="display: none;">
                                        <small>Broker Commission:</small><br>
                                        <strong id="summaryCommission">$0.00</strong>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-6">
                                        <small>Recipient Receives:</small><br>
                                        <strong id="summaryNetAmount">$0.00</strong>
                                    </div>
                                    <div class="col-6">
                                        <small>Your Balance After:</small><br>
                                        <strong id="summaryNewBalance">$0.00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="calculateSummary()">
                                    <span class="me-1">🧮</span>Calculate Summary
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
                                    <span class="me-1">🚀</span>Send Payment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="alert alert-info mt-4">
                <h6 class="alert-heading">🔐 Security Notice</h6>
                <ul class="mb-0">
                    <li>All payments are processed securely with end-to-end encryption</li>
                    <li>Verify recipient email address before sending payment</li>
                    <li>ChoziCode commissions are automatically calculated and distributed</li>
                    <li>You will receive email confirmation once payment is processed</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentBalance = {{ auth()->user()->wallet_balance }};
let validatedChoziCode = null;

document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('sendPaymentForm');
    const inputs = form.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('input', validateForm);
        input.addEventListener('change', validateForm);
    });
    
    // Auto-calculate summary when amount changes
    document.getElementById('amount').addEventListener('input', function() {
        const amount = parseFloat(this.value);
        if (amount > 0) {
            calculateSummary();
        }
    });
    
    // Validate recipient email
    document.getElementById('recipient_email').addEventListener('blur', validateRecipient);
    
    // Form submission
    form.addEventListener('submit', handleFormSubmit);
});

async function validateRecipient() {
    const email = document.getElementById('recipient_email').value;
    if (!email) return;
    
    try {
        const response = await fetch(`/api/users/search?email=${encodeURIComponent(email)}`, {
            headers: {
                'Authorization': 'Bearer ' + getAuthToken(),
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.data.length > 0) {
                const user = data.data[0];
                document.getElementById('recipientName').textContent = user.name;
                document.getElementById('recipientRole').textContent = capitalizeFirst(user.role);
                document.getElementById('recipientInfo').style.display = 'block';
            } else {
                document.getElementById('recipientInfo').style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error validating recipient:', error);
    }
}

async function validateChoziCode() {
    const code = document.getElementById('chozi_code').value;
    if (!code) {
        document.getElementById('choziCodeInfo').style.display = 'none';
        document.getElementById('choziCodeError').style.display = 'none';
        validatedChoziCode = null;
        return;
    }
    
    try {
        const response = await fetch('/api/chozi-codes/validate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ code: code })
        });
        
        const data = await response.json();
        
        if (data.success && data.data.is_valid) {
            validatedChoziCode = data.data;
            document.getElementById('brokerName').textContent = data.data.broker_name;
            document.getElementById('commissionRate').textContent = data.data.commission_rate + '%';
            document.getElementById('choziCodeInfo').style.display = 'block';
            document.getElementById('choziCodeError').style.display = 'none';
            calculateSummary(); // Recalculate with commission
        } else {
            document.getElementById('choziCodeInfo').style.display = 'none';
            document.getElementById('choziCodeError').style.display = 'block';
            validatedChoziCode = null;
            calculateSummary(); // Recalculate without commission
        }
    } catch (error) {
        console.error('Error validating ChoziCode:', error);
        document.getElementById('choziCodeError').style.display = 'block';
        validatedChoziCode = null;
    }
}

function calculateSummary() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    if (amount <= 0) {
        document.getElementById('paymentSummary').style.display = 'none';
        return;
    }
    
    let commission = 0;
    let commissionRate = 0;
    
    if (validatedChoziCode) {
        commissionRate = validatedChoziCode.commission_rate || 5;
        commission = (amount * commissionRate) / 100;
    }
    
    const netAmount = amount - commission;
    const newBalance = currentBalance - amount;
    
    // Update summary display
    document.getElementById('summaryAmount').textContent = '$' + amount.toFixed(2);
    document.getElementById('summaryCommission').textContent = '$' + commission.toFixed(2);
    document.getElementById('summaryNetAmount').textContent = '$' + netAmount.toFixed(2);
    document.getElementById('summaryNewBalance').textContent = '$' + newBalance.toFixed(2);
    
    // Show/hide commission section
    const commissionSection = document.getElementById('commissionSection');
    if (commission > 0) {
        commissionSection.style.display = 'block';
    } else {
        commissionSection.style.display = 'none';
    }
    
    // Show summary
    document.getElementById('paymentSummary').style.display = 'block';
    
    // Check if sufficient balance
    if (newBalance < 0) {
        document.getElementById('summaryNewBalance').style.color = '#dc3545';
        document.getElementById('submitBtn').disabled = true;
        showAlert('Insufficient wallet balance for this payment', 'warning');
    } else {
        document.getElementById('summaryNewBalance').style.color = 'white';
        validateForm();
    }
}

function validateForm() {
    const form = document.getElementById('sendPaymentForm');
    const isValid = form.checkValidity();
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const sufficientBalance = (currentBalance - amount) >= 0;
    
    document.getElementById('submitBtn').disabled = !(isValid && amount > 0 && sufficientBalance);
}

async function handleFormSubmit(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';
    
    try {
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        
        const response = await fetch('/api/payments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + getAuthToken(),
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Payment sent successfully!', 'success');
            
            // Show success details
            setTimeout(() => {
                window.location.href = '{{ route("payments.details", "") }}/' + result.data.payment_reference;
            }, 2000);
        } else {
            throw new Error(result.message || 'Payment failed');
        }
        
    } catch (error) {
        console.error('Payment error:', error);
        showAlert(error.message || 'Payment processing failed', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

function refreshBalance() {
    // In a real app, this would fetch the current balance from the API
    location.reload();
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function getAuthToken() {
    return 'session-token';
}

function showAlert(message, type = 'info') {
    // Create a more user-friendly alert
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : type === 'warning' ? 'alert-warning' : 'alert-info';
    const alertIcon = type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : 'ℹ️';
    
    const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <span class="me-2">${alertIcon}</span>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto-remove after 5 seconds
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