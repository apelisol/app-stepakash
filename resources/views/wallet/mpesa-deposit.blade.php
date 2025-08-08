@extends('layouts.wallet')

@section('title', 'Deposit via M-Pesa')

@section('content')
<div class="wallet-container">
    <div class="page-header">
        <h2 class="page-title">Deposit via M-Pesa</h2>
        <div class="page-actions">
            <a href="{{ route('wallet.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    
    <div class="deposit-container">
        <div class="deposit-form">
            <form action="{{ route('wallet.mpesa.deposit.process') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="phone">M-Pesa Number</label>
                    <div class="input-with-prefix">
                        <span class="prefix">+254</span>
                        <input type="tel" id="phone" name="phone" value="{{ substr(auth()->user()->phone, 4) }}" placeholder="712345678" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="amount">Amount (KES)</label>
                    <input type="number" id="amount" name="amount" min="10" step="1" placeholder="Minimum KES 10" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-money-bill-wave"></i> Initiate Deposit
                    </button>
                </div>
            </form>
            
            <div class="deposit-instructions">
                <h3>How to Deposit:</h3>
                <ol class="steps">
                    <li>Enter your M-Pesa number and amount</li>
                    <li>Click "Initiate Deposit"</li>
                    <li>Enter your M-Pesa PIN when prompted</li>
                    <li>Wait for confirmation SMS</li>
                </ol>
                
                <div class="charges-info">
                    <h4>Charges:</h4>
                    <div class="charge-item">
                        <span>KES 0 - 49</span>
                        <span>Free</span>
                    </div>
                    <div class="charge-item">
                        <span>KES 50 - 1,000</span>
                        <span>KES 10</span>
                    </div>
                    <div class="charge-item">
                        <span>KES 1,001 - 50,000</span>
                        <span>KES 30</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="deposit-info">
            <div class="info-card">
                <h3>M-Pesa Deposit Limits</h3>
                
                <div class="limit-card">
                    <div class="limit-icon">
                        <i class="fas fa-money-bill-alt"></i>
                    </div>
                    <div class="limit-details">
                        <span class="limit-label">Per Transaction</span>
                        <span class="limit-value">KES 70,000</span>
                    </div>
                </div>
                
                <div class="limit-card">
                    <div class="limit-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="limit-details">
                        <span class="limit-label">Daily Limit</span>
                        <span class="limit-value">KES 150,000</span>
                    </div>
                </div>
                
                <div class="limit-card">
                    <div class="limit-icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="limit-details">
                        <span class="limit-label">Weekly Limit</span>
                        <span class="limit-value">KES 500,000</span>
                    </div>
                </div>
                
                <div class="support-info">
                    <h4>Need Help?</h4>
                    <p>Contact our 24/7 support for any deposit issues</p>
                    <a href="https://wa.me/254741554994" class="support-link">
                        <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection