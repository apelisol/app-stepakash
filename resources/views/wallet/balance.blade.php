@extends('layouts.wallet')

@section('title', 'Wallet Balance')

@section('content')
<div class="wallet-container">
    <!-- Balance Overview -->
    <div class="balance-overview detailed">
        <!-- Wallet Balance Card -->
        <div class="balance-card detailed">
            <div class="balance-header">
                <h3>Wallet Balance Details</h3>
                <button class="eye-toggle">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            
            <div class="balance-amount">
                <span class="currency">KES</span>
                <span class="amount">12,345.00</span>
            </div>
            
            <div class="balance-details">
                <div class="detail-item">
                    <span class="detail-label">Available Balance</span>
                    <span class="detail-value">KES 12,345.00</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Pending Transactions</span>
                    <span class="detail-value">KES 1,200.00</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Reserved Funds</span>
                    <span class="detail-value">KES 500.00</span>
                </div>
            </div>
            
            <div class="balance-actions">
                <a href="{{ route('wallet.mpesa.deposit') }}" class="action-btn deposit">
                    <i class="fas fa-plus"></i> Deposit
                </a>
                <a href="{{ route('wallet.mpesa.withdraw') }}" class="action-btn withdraw">
                    <i class="fas fa-minus"></i> Withdraw
                </a>
            </div>
        </div>
        
        <!-- Deriv Balance Card -->
        <div class="balance-card deriv-balance detailed">
            <div class="balance-header">
                <h3>Deriv Balance Details</h3>
                <img src="{{ asset('images/deriv-logo.png') }}" alt="Deriv" class="deriv-logo">
            </div>
            
            <div class="balance-amount">
                <span class="currency">USD</span>
                <span class="amount">1,234.50</span>
            </div>
            
            <div class="balance-details">
                <div class="detail-item">
                    <span class="detail-label">Trading Balance</span>
                    <span class="detail-value">USD 1,000.00</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Withdrawable</span>
                    <span class="detail-value">USD 1,234.50</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Open Positions</span>
                    <span class="detail-value">USD 234.50</span>
                </div>
            </div>
            
            <div class="balance-actions">
                <a href="{{ route('wallet.deriv.deposit') }}" class="action-btn deposit">
                    <i class="fas fa-plus"></i> Fund Deriv
                </a>
                <a href="{{ route('wallet.deriv.withdraw') }}" class="action-btn withdraw">
                    <i class="fas fa-minus"></i> Withdraw
                </a>
            </div>
        </div>
    </div>
    
    <!-- Transaction History -->
    <div class="transactions-section">
        <div class="section-header">
            <h3 class="section-title">Transaction History</h3>
            <div class="time-filter">
                <select>
                    <option>Last 7 days</option>
                    <option>This month</option>
                    <option>Last month</option>
                    <option>Custom range</option>
                </select>
            </div>
        </div>
        
        <div class="transactions-list detailed">
            @foreach($transactions as $transaction)
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
                        <span class="transaction-type">{{ ucfirst($transaction->type) }} - {{ $transaction->description }}</span>
                        <span class="transaction-date">{{ $transaction->created_at->format('M d, H:i') }}</span>
                        <span class="transaction-reference">Ref: {{ $transaction->reference }}</span>
                    </div>
                    <div class="transaction-amount {{ $transaction->amount > 0 ? 'credit' : 'debit' }}">
                        {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount, 2) }}
                        <span>{{ strtoupper($transaction->currency) }}</span>
                    </div>
                </div>
                <div class="transaction-status {{ $transaction->status }}">
                    {{ ucfirst($transaction->status) }}
                </div>
            </a>
            @endforeach
            
            @if($transactions->isEmpty())
            <div class="empty-transactions">
                <i class="fas fa-exchange-alt"></i>
                <p>No transactions found</p>
            </div>
            @endif
        </div>
        
        <div class="pagination">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection