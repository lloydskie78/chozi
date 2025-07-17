@extends('layouts.app')

@section('title', 'ChoziCode Analytics - ChoziPay')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">📊 ChoziCode Analytics</h2>
                    <p class="text-muted mb-0">Track your commission performance and code usage</p>
                </div>
                <a href="{{ route('chozi-codes.index') }}" class="btn btn-outline-secondary">
                    <span class="me-1">🏷️</span>Manage Codes
                </a>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Earnings</h6>
                                    <h3 class="mb-0" id="totalEarnings">$0.00</h3>
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
                                    <h6 class="card-title mb-1">This Month</h6>
                                    <h3 class="mb-0" id="thisMonth">$0.00</h3>
                                </div>
                                <span class="fs-2">📅</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Uses</h6>
                                    <h3 class="mb-0" id="totalUses">0</h3>
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
                                    <h6 class="card-title mb-1">Avg Commission</h6>
                                    <h3 class="mb-0" id="avgCommission">$0.00</h3>
                                </div>
                                <span class="fs-2">📈</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Analytics -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">📈 Commission Trends</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-5">
                                <span class="fs-1">📊</span>
                                <h5 class="mt-3">Charts Coming Soon</h5>
                                <p class="text-muted">Advanced analytics and commission trends will be available in the next update.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">🏆 Top Performing Codes</h5>
                        </div>
                        <div class="card-body" id="topCodes">
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">⏱️ Recent Commission Activity</h5>
                </div>
                <div class="card-body">
                    <div id="recentActivity">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading recent activity...</p>
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
    loadAnalytics();
});

async function loadAnalytics() {
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
                displayAnalytics(data.data);
            }
        }
    } catch (error) {
        console.error('Error loading analytics:', error);
    }
}

function displayAnalytics(data) {
    // Update summary cards
    document.getElementById('totalEarnings').textContent = '$' + (data.total_commissions || 0).toFixed(2);
    document.getElementById('thisMonth').textContent = '$' + (data.monthly_commissions || 0).toFixed(2);
    document.getElementById('totalUses').textContent = data.total_uses || 0;
    document.getElementById('avgCommission').textContent = '$' + (data.avg_commission || 0).toFixed(2);
    
    // Display top codes
    displayTopCodes(data.top_codes || []);
    
    // Display recent activity  
    displayRecentActivity(data.recent_activity || []);
}

function displayTopCodes(codes) {
    const container = document.getElementById('topCodes');
    
    if (codes.length === 0) {
        container.innerHTML = `
            <div class="text-center py-3">
                <span class="fs-4">🏷️</span>
                <p class="text-muted mb-0">No codes used yet</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    codes.forEach((code, index) => {
        const medal = index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : '🏷️';
        html += `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="me-2">${medal}</span>
                    <code class="text-primary">${code.code}</code>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">${code.usage_count} uses</small>
                    <strong>$${code.total_commission.toFixed(2)}</strong>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function displayRecentActivity(activities) {
    const container = document.getElementById('recentActivity');
    
    if (activities.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <span class="fs-1">💸</span>
                <h5 class="mt-3">No Recent Activity</h5>
                <p class="text-muted">Commission activity will appear here once your codes are used.</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    activities.forEach(activity => {
        html += `
            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                <div class="me-3">
                    <span class="bg-success text-white rounded-circle p-2">💰</span>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1">Commission Earned</h6>
                    <p class="mb-1">Code <code>${activity.code}</code> used by ${activity.payer_name}</p>
                    <small class="text-muted">${formatDateTime(activity.created_at)}</small>
                </div>
                <div class="text-end">
                    <strong class="text-success">+$${activity.commission.toFixed(2)}</strong>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getAuthToken() {
    return 'session-token';
}
</script>
@endpush 