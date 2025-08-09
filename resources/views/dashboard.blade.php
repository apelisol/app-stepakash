@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Success Message -->
        @if (session('status'))
            <div class="col-12">
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        <!-- Wallet Balance Card -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Wallet Balance</h5>
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="display-4">{{ number_format($wallet['balance'], 2) }} <small class="text-muted">{{ $wallet['currency'] }}</small></h2>
                        <p class="text-muted mb-0">Available: {{ number_format($wallet['available'], 2) }} {{ $wallet['currency'] }}</p>
                        <p class="text-muted">Wallet ID: {{ $wallet['id'] }}</p>
                    </div>
                    
                    <div class="mt-4">
                        <h6>Recent Transactions</h6>
                        @if(count($wallet['recent_transactions']) > 0)
                            <div class="list-group">
                                @foreach($wallet['recent_transactions'] as $transaction)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $transaction->description }}</h6>
                                            <span class="{{ $transaction->cr_dr === 'cr' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->cr_dr === 'cr' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                            </span>
                                        </div>
                                        <small class="text-muted">{{ $transaction->trans_date->format('M d, Y H:i') }}</small>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No recent transactions</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Deriv Balance Card -->
        <div class="col-md-6 mb-4">
            <div id="deriv-balance-card" class="card h-100">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Deriv Account</h5>
                        <div>
                            <i class="fas fa-sync-alt fa-spin me-2 d-none" id="deriv-refresh-spinner"></i>
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($derivBalance && isset($derivBalance['balance']))
                        <div class="text-center mb-3">
                            <h2 class="display-4">
                                <span id="deriv-balance">{{ number_format($derivBalance['balance'], 2) }}</span>
                                <small class="text-muted">{{ $derivBalance['currency'] ?? 'USD' }}</small>
                            </h2>
                            <p class="text-muted">Account: <span id="deriv-account">{{ $derivBalance['loginid'] ?? 'N/A' }}</span></p>
                        </div>
                        
                        @if(isset($derivBalance['accounts']) && count($derivBalance['accounts']) > 0)
                            <div class="mt-4">
                                <h6>All Accounts</h6>
                                <div class="table-responsive">
                                    <table id="deriv-accounts-table" class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Account</th>
                                                <th class="text-end">Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($derivBalance['accounts'] as $accountId => $account)
                                                <tr>
                                                    <td>{{ $accountId }}</td>
                                                    <td class="text-end">{{ number_format($account['balance'] ?? 0, 2) }} {{ $account['currency'] ?? 'USD' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <p class="mb-0">Unable to fetch Deriv balance at this time.</p>
                            <small class="text-muted">Please try again later or contact support.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('wallet.deposit') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-plus-circle me-2"></i> Deposit
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('wallet.withdraw') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-minus-circle me-2"></i> Withdraw
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('transactions') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-history me-2"></i> Transactions
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('transfer') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-exchange-alt me-2"></i> Transfer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1.25rem 1.5rem;
    }
    .display-4 {
        font-size: 2.5rem;
        font-weight: 300;
        line-height: 1.2;
    }
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    .list-group-item:first-child {
        border-top: none;
    }
    .balance-loading {
        opacity: 0.7;
        position: relative;
    }
    .balance-loading::after {
        content: 'Updating...';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.9);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        color: #495057;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to update Deriv balance
        function updateDerivBalance() {
            const balanceCard = document.getElementById('deriv-balance-card');
            const balanceElement = document.getElementById('deriv-balance');
            const accountElement = document.getElementById('deriv-account');
            const accountsTable = document.getElementById('deriv-accounts-table');
            
            // Show loading state
            balanceCard.classList.add('balance-loading');
            
            // Make AJAX request to get fresh balance
            fetch('{{ route("wallet.deriv-balance") }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update balance display
                    if (balanceElement && data.balance !== undefined) {
                        balanceElement.textContent = parseFloat(data.balance).toFixed(2);
                    }
                    
                    // Update account info
                    if (accountElement && data.loginid) {
                        accountElement.textContent = data.loginid;
                    }
                    
                    // Update accounts table if exists
                    if (accountsTable && data.accounts) {
                        const tbody = accountsTable.querySelector('tbody');
                        if (tbody) {
                            tbody.innerHTML = '';
                            Object.entries(data.accounts).forEach(([accountId, account]) => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${accountId}</td>
                                    <td class="text-end">${parseFloat(account.balance || 0).toFixed(2)} ${account.currency || 'USD'}</td>
                                `;
                                tbody.appendChild(row);
                            });
                        }
                    }
                } else {
                    console.error('Failed to update balance:', data.message);
                }
            })
            .catch(error => {
                console.error('Error updating balance:', error);
            })
            .finally(() => {
                // Remove loading state
                balanceCard.classList.remove('balance-loading');
            });
        }
        
        // Initial update
        updateDerivBalance();
        
        // Set up auto-refresh every 30 seconds
        const refreshInterval = 30000; // 30 seconds
        setInterval(updateDerivBalance, refreshInterval);
    });
</script>
@endpush
@endsection
