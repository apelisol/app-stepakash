@extends('layouts.wallet')

@section('title', 'Send Money')

@section('content')
<div class="wallet-container">
    <div class="page-header">
        <h2 class="page-title">Send Money</h2>
        <div class="page-actions">
            <a href="{{ route('wallet.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    
    <div class="send-money-container">
        <div class="send-options">
            <div class="option-tabs">
                <button class="option-tab active" data-target="to-phone">
                    <i class="fas fa-mobile-alt"></i> To Phone
                </button>
                <button class="option-tab" data-target="to-wallet">
                    <i class="fas fa-wallet"></i> To Wallet
                </button>
                <button class="option-tab" data-target="to-bank">
                    <i class="fas fa-university"></i> To Bank
                </button>
            </div>
            
            <!-- To Phone Form -->
            <div class="option-content active" id="to-phone">
                <form action="{{ route('wallet.send.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="phone">
                    
                    <div class="form-group">
                        <label for="phone">Recipient Phone Number</label>
                        <div class="input-with-prefix">
                            <span class="prefix">+254</span>
                            <input type="tel" id="phone" name="phone" placeholder="712345678" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Amount (KES)</label>
                        <input type="number" id="amount" name="amount" min="10" step="1" placeholder="Enter amount" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description (Optional)</label>
                        <input type="text" id="description" name="description" placeholder="e.g. Lunch money">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Send Money
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- To Wallet Form -->
            <div class="option-content" id="to-wallet">
                <form action="{{ route('wallet.send.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="wallet">
                    
                    <div class="form-group">
                        <label for="wallet_id">Recipient Wallet ID</label>
                        <input type="text" id="wallet_id" name="wallet_id" placeholder="e.g. AB1234" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Amount (KES)</label>
                        <input type="number" id="amount" name="amount" min="10" step="1" placeholder="Enter amount" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description (Optional)</label>
                        <input type="text" id="description" name="description" placeholder="e.g. Payment for services">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Send to Wallet
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- To Bank Form -->
            <div class="option-content" id="to-bank">
                <form action="{{ route('wallet.send.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="bank">
                    
                    <div class="form-group">
                        <label for="bank">Select Bank</label>
                        <select id="bank" name="bank" required>
                            <option value="">-- Select Bank --</option>
                            <option value="equity">Equity Bank</option>
                            <option value="kcb">KCB Bank</option>
                            <option value="coop">Co-operative Bank</option>
                            <option value="standard">Standard Chartered</option>
                            <option value="absa">Absa Bank</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="account_number">Account Number</label>
                        <input type="text" id="account_number" name="account_number" placeholder="Enter account number" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="account_name">Account Name</label>
                        <input type="text" id="account_name" name="account_name" placeholder="Enter account name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Amount (KES)</label>
                        <input type="number" id="amount" name="amount" min="100" step="1" placeholder="Minimum KES 100" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Send to Bank
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="send-info">
            <div class="info-card">
                <h3>Send Money Safely</h3>
                <ul class="info-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Double-check recipient details before sending</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Transactions are instant and cannot be reversed</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Standard charges apply for bank transfers</span>
                    </li>
                </ul>
                
                <div class="limits-info">
                    <h4>Transaction Limits</h4>
                    <div class="limit-item">
                        <span>Daily Limit</span>
                        <span>KES 150,000</span>
                    </div>
                    <div class="limit-item">
                        <span>Per Transaction</span>
                        <span>KES 70,000</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection