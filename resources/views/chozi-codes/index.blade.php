@extends('layouts.app')

@section('title', 'ChoziCodes - ChoziPay')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">🏷️ My ChoziCodes</h2>
                    <p class="text-muted mb-0">Manage your broker referral codes and track commissions</p>
                </div>
                <div>
                    <a href="{{ route('chozi-codes.analytics') }}" class="btn btn-outline-info me-2">
                        <span class="me-1">📊</span>Analytics
                    </a>
                    <button class="btn btn-primary" onclick="showGenerateModal()">
                        <span class="me-1">➕</span>Generate New Code
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Active Codes</h6>
                                    <h4 class="mb-0" id="activeCodes">0</h4>
                                </div>
                                <span class="fs-2">🏷️</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Commissions</h6>
                                    <h4 class="mb-0" id="totalCommissions">$0.00</h4>
                                </div>
                                <span class="fs-2">💰</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Uses</h6>
                                    <h4 class="mb-0" id="totalUses">0</h4>
                                </div>
                                <span class="fs-2">📊</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">This Month</h6>
                                    <h4 class="mb-0" id="monthlyCommissions">$0.00</h4>
                                </div>
                                <span class="fs-2">📅</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ChoziCodes List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🏷️ Your ChoziCodes</h5>
                    <button class="btn btn-outline-primary btn-sm" onclick="loadChoziCodes()">
                        <span class="me-1">🔄</span>Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading your ChoziCodes...</p>
                    </div>
                    
                    <div id="choziCodesContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Commission Rate</th>
                                        <th>Uses</th>
                                        <th>Max Uses</th>
                                        <th>Status</th>
                                        <th>Expires</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="choziCodesTableBody">
                                    <!-- ChoziCode rows will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div id="noChoziCodes" class="text-center py-4" style="display: none;">
                        <span class="fs-1">🏷️</span>
                        <h5 class="mt-3">No ChoziCodes Yet</h5>
                        <p class="text-muted">Generate your first ChoziCode to start earning commissions.</p>
                        <button class="btn btn-primary" onclick="showGenerateModal()">
                            <span class="me-1">➕</span>Generate Your First Code
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate ChoziCode Modal -->
<div class="modal fade" id="generateModal" tabindex="-1" aria-labelledby="generateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="generateForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="generateModalLabel">🏷️ Generate New ChoziCode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="commission_rate" class="form-label">Commission Rate (%)</label>
                        <input type="number" class="form-control" id="commission_rate" name="commission_rate" 
                               value="5.00" min="1" max="20" step="0.01" required>
                        <div class="form-text">Default is 5%. Maximum allowed is 20%.</div>
                    </div>
                    <div class="mb-3">
                        <label for="max_uses" class="form-label">Maximum Uses (Optional)</label>
                        <input type="number" class="form-control" id="max_uses" name="max_uses" 
                               min="1" max="10000" placeholder="Unlimited">
                        <div class="form-text">Leave empty for unlimited uses.</div>
                    </div>
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Expiration Date (Optional)</label>
                        <input type="date" class="form-control" id="expires_at" name="expires_at" 
                               min="{{ date('Y-m-d') }}">
                        <div class="form-text">Leave empty for no expiration.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" maxlength="500" placeholder="Purpose or campaign description..."></textarea>
                        <div class="form-text">Max 500 characters.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="me-1">🚀</span>Generate Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Code Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">🏷️ ChoziCode Details</h5>
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
document.addEventListener('DOMContentLoaded', function() {
    loadChoziCodes();
    loadStats();
    
    // Form submission
    document.getElementById('generateForm').addEventListener('submit', handleGenerateSubmit);
});

async function loadStats() {
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
                document.getElementById('activeCodes').textContent = data.data.active_codes || 0;
                document.getElementById('totalCommissions').textContent = '$' + (data.data.total_commissions || 0).toFixed(2);
                document.getElementById('totalUses').textContent = data.data.total_uses || 0;
                document.getElementById('monthlyCommissions').textContent = '$' + (data.data.monthly_commissions || 0).toFixed(2);
            }
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadChoziCodes() {
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('choziCodesContainer').style.display = 'none';
    document.getElementById('noChoziCodes').style.display = 'none';
    
    try {
        const response = await fetch('/api/chozi-codes', {
            headers: {
                'Authorization': 'Bearer ' + getAuthToken(),
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        document.getElementById('loadingSpinner').style.display = 'none';
        
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.data.length > 0) {
                displayChoziCodes(data.data);
                document.getElementById('choziCodesContainer').style.display = 'block';
            } else {
                document.getElementById('noChoziCodes').style.display = 'block';
            }
        } else {
            throw new Error('Failed to load ChoziCodes');
        }
    } catch (error) {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('noChoziCodes').style.display = 'block';
        console.error('Error loading ChoziCodes:', error);
        showAlert('Error loading ChoziCodes', 'error');
    }
}

function displayChoziCodes(codes) {
    const tbody = document.getElementById('choziCodesTableBody');
    tbody.innerHTML = '';
    
    codes.forEach(code => {
        const row = document.createElement('tr');
        
        const statusBadge = getStatusBadge(code);
        const usageText = code.max_uses ? `${code.usage_count}/${code.max_uses}` : code.usage_count;
        const expiresText = code.expires_at ? formatDate(code.expires_at) : 'Never';
        
        row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <code class="text-primary fw-bold">${code.code}</code>
                    <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('${code.code}')">
                        <span class="small">📋</span>
                    </button>
                </div>
            </td>
            <td><span class="fw-bold">${code.commission_rate}%</span></td>
            <td>${usageText}</td>
            <td>${code.max_uses || '∞'}</td>
            <td>${statusBadge}</td>
            <td><small>${expiresText}</small></td>
            <td><small>${formatDate(code.created_at)}</small></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-info" onclick="showDetails('${code.id}')">
                        <span class="me-1">👁️</span>View
                    </button>
                    ${code.is_active ? `
                        <button class="btn btn-outline-danger" onclick="deactivateCode('${code.id}')">
                            <span class="me-1">🚫</span>Deactivate
                        </button>
                    ` : ''}
                </div>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function getStatusBadge(code) {
    if (!code.is_active) {
        return '<span class="badge bg-secondary">🚫 Inactive</span>';
    }
    
    if (code.expires_at && new Date(code.expires_at) < new Date()) {
        return '<span class="badge bg-warning">⏰ Expired</span>';
    }
    
    if (code.max_uses && code.usage_count >= code.max_uses) {
        return '<span class="badge bg-danger">🚫 Limit Reached</span>';
    }
    
    return '<span class="badge bg-success">✅ Active</span>';
}

function showGenerateModal() {
    const modal = new bootstrap.Modal(document.getElementById('generateModal'));
    modal.show();
}

async function handleGenerateSubmit(e) {
    e.preventDefault();
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Generating...';
    
    try {
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        
        // Remove empty fields
        Object.keys(data).forEach(key => {
            if (data[key] === '') {
                delete data[key];
            }
        });
        
        const response = await fetch('/api/chozi-codes', {
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
            showAlert('ChoziCode generated successfully!', 'success');
            
            // Close modal and refresh data
            const modal = bootstrap.Modal.getInstance(document.getElementById('generateModal'));
            modal.hide();
            
            loadChoziCodes();
            loadStats();
            
            // Reset form
            e.target.reset();
        } else {
            throw new Error(result.message || 'Generation failed');
        }
        
    } catch (error) {
        console.error('Generation error:', error);
        showAlert(error.message || 'ChoziCode generation failed', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

async function deactivateCode(codeId) {
    if (!confirm('Are you sure you want to deactivate this ChoziCode? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/chozi-codes/${codeId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + getAuthToken(),
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('ChoziCode deactivated successfully', 'success');
            loadChoziCodes();
            loadStats();
        } else {
            throw new Error(result.message || 'Deactivation failed');
        }
        
    } catch (error) {
        console.error('Deactivation error:', error);
        showAlert(error.message || 'Failed to deactivate ChoziCode', 'error');
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('ChoziCode copied to clipboard!', 'success');
    }).catch(() => {
        showAlert('Failed to copy to clipboard', 'error');
    });
}

function showDetails(codeId) {
    // Implementation for showing code details
    showAlert('Code details feature coming soon', 'info');
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
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