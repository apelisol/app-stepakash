@extends('layouts.wallet')

@section('title', 'Wallet Dashboard')

@section('content')
<div class="wallet-container">
    <!-- Balance Overview -->
    <div class="balance-overview">
        <div class="balance-card">
            <div class="balance-header">
                <h3>Wallet Balance</h3>
                <button class="eye-toggle" id="toggleWalletBalance">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="balance-amount">
                <span class="currency">KES</span>
                <span class="amount" id="walletBalance">{{ number_format(auth()->user()->wallet->balance, 2) }}</span>
            </div>
            <div class="balance-footer">
                <span>Last updated: <span id="walletLastUpdated">{{ now()->format('H:i') }}</span></span>
                <span>{{ auth()->user()->phone }}</span>
            </div>
        </div>

        <div class="balance-card deriv-balance">
            <div class="balance-header">
                <h3>Deriv Balance</h3>
                <div class="refresh-balance" id="refreshDerivBalance" title="Refresh Balance">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <img src="{{ asset('images/deriv-logo.png') }}" alt="Deriv" class="deriv-logo">
            </div>
            <div class="balance-amount">
                <span class="currency">USD</span>
                <span class="amount" id="derivBalance">0.00</span>
            </div>
            <div class="balance-footer">
                <span>Last updated: <span id="derivLastUpdated">--:--</span></span>
                <span>CR: {{ auth()->user()->deriv_account_number ?? 'Not set' }}</span>
            </div>
            <div class="balance-actions">
                <a href="{{ route('wallet.deriv.deposit') }}" class="btn btn-sm btn-outline-light mr-2">Deposit</a>
                <a href="{{ route('wallet.deriv.withdraw') }}" class="btn btn-sm btn-outline-light">Withdraw</a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3 class="section-title">Quick Actions</h3>
        <div class="action-grid">
            <!-- Send Money -->
            <a href="{{ route('wallet.send') }}" class="action-card">
                <div class="action-icon send-money">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <span>Send Money</span>
            </a>

            <!-- M-Pesa Deposit -->
            <a href="{{ route('wallet.mpesa.deposit') }}" class="action-card">
                <div class="action-icon mpesa-deposit">
                    <img src="{{ asset('images/mpesa.png') }}" alt="M-Pesa">
                </div>
                <span>Deposit via M-Pesa</span>
            </a>

            <!-- Buy Airtime -->
            <a href="{{ route('wallet.airtime') }}" class="action-card">
                <div class="action-icon airtime">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <span>Buy Airtime</span>
            </a>

            <!-- Bank Transfer -->
            <a href="{{ route('wallet.bank') }}" class="action-card">
                <div class="action-icon bank-transfer">
                    <i class="fas fa-university"></i>
                </div>
                <span>Bank Transfer</span>
            </a>

            <!-- Deriv Deposit -->
            <a href="{{ route('wallet.deriv.deposit') }}" class="action-card">
                <div class="action-icon deriv-deposit">
                    <img src="{{ asset('images/deriv-logo.png') }}" alt="Deriv">
                </div>
                <span>Fund Deriv</span>
            </a>

            <!-- Buy Bundles -->
            <a href="{{ route('wallet.bundles') }}" class="action-card">
                <div class="action-icon bundles">
                    <i class="fas fa-wifi"></i>
                </div>
                <span>Buy Bundles</span>
            </a>

            <!-- M-Pesa Withdraw -->
            <a href="{{ route('wallet.mpesa.withdraw') }}" class="action-card">
                <div class="action-icon mpesa-withdraw">
                    <img src="{{ asset('images/mpesa.png') }}" alt="M-Pesa">
                </div>
                <span>Withdraw to M-Pesa</span>
            </a>

            <!-- Deriv Withdraw -->
            <a href="{{ route('wallet.deriv.withdraw') }}" class="action-card">
                <div class="action-icon deriv-withdraw">
                    <img src="{{ asset('images/deriv-logo.png') }}" alt="Deriv">
                </div>
                <span>Withdraw from Deriv</span>
            </a>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="transactions-section">
        <div class="section-header">
            <h3 class="section-title">Recent Transactions</h3>
            <a href="{{ route('wallet.transactions') }}" class="view-all">View All</a>
        </div>
        
        <div class="transactions-list">
            @foreach($recentTransactions as $transaction)
            <a href="{{ route('wallet.transactions.show', $transaction->id) }}" class="transaction-item">
                <div class="transaction-icon">
                    @if($transaction->type === 'mpesa')
                    <img src="{{ asset('images/mpesa.png') }}" alt="M-Pesa">
                    @elseif($transaction->type === 'deriv')
                    <img src="{{ asset('images/deriv-logo.png') }}" alt="Deriv">
                    @else
                    <i class="fas fa-exchange-alt"></i>
                    @endif
                </div>
                <div class="transaction-details">
                    <div class="transaction-info">
                        <span class="transaction-type">{{ ucfirst($transaction->type) }}</span>
                        <span class="transaction-date">{{ $transaction->created_at->format('M d, H:i') }}</span>
                    </div>
                    <div class="transaction-amount {{ $transaction->amount > 0 ? 'credit' : 'debit' }}">
                        {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount, 2) }}
                        <span>{{ strtoupper($transaction->currency) }}</span>
                    </div>
                </div>
            </a>
            @endforeach
            
            @if($recentTransactions->isEmpty())
            <div class="empty-transactions">
                <i class="fas fa-exchange-alt"></i>
                <p>No transactions yet</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection