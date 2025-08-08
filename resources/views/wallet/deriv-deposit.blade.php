@extends('layouts.wallet')

@section('title', 'Deposit to Deriv Account')

@section('content')
@push('styles')
<style>
    .balance-card {
        transition: all 0.3s ease;
    }
    .balance-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .refresh-balance {
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    .refresh-balance:hover {
        transform: rotate(180deg);
    }
    .balance-actions {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    #submitBtn {
        min-width: 180px;
    }
</style>
@endpush
<div class="wallet-container">
    <div class="page-header">
        <h2 class="page-title">Deposit to Deriv</h2>
        <div class="page-actions">
            <a href="{{ route('wallet.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form id="depositForm" action="{{ route('wallet.deriv.deposit') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="cr_number">CR Number</label>
                            <input type="text" class="form-control" id="cr_number" name="cr_number" 
                                   value="{{ old('cr_number', auth()->user()->deriv_account_number) }}" 
                                   {{ auth()->user()->deriv_account_number ? 'readonly' : '' }} required>
                            @if(!auth()->user()->deriv_account_number)
                                <small class="form-text text-muted">Enter your Deriv CR Number</small>
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="amount">Amount (KES)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">KES</span>
                                </div>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       min="100" step="1" value="{{ old('amount') }}" required>
                            </div>
                            <small class="form-text text-muted">Minimum: KES 100</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="deriv_account">Deriv Account Type</label>
                            <select class="form-control" id="deriv_account" name="deriv_account" required>
                                <option value="">-- Select Account --</option>
                                <option value="main" {{ old('deriv_account') == 'main' ? 'selected' : '' }}>Main Account (USD)</option>
                                <option value="mt5" {{ old('deriv_account') == 'mt5' ? 'selected' : '' }}>MT5 Account (USD)</option>
                                <option value="dxtrade" {{ old('deriv_account') == 'dxtrade' ? 'selected' : '' }}>Deriv X Account (USD)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="font-weight-bold">Exchange Rate:</span>
                                    <span id="exchangeRate">1 USD = KES {{ number_format($exchangeRate, 2) }}</span>
                                </div>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-exchange-alt mr-2"></i> Transfer to Deriv
                                </button>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between">
                                <span>You'll receive:</span>
                                <span><strong id="usdAmount">0.00</strong> USD</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Fee (1%):</span>
                                <span><strong id="feeAmount">0.00</strong> KES</span>
                            </div>
                            <div class="d-flex justify-content-between font-weight-bold">
                                <span>Total to pay:</span>
                                <span id="totalAmount">0.00 KES</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/deriv-logo.png') }}" alt="Deriv" class="img-fluid mr-2" style="max-height: 30px;">
                        <h5 class="mb-0">Deriv Deposit Info</h5>
                    </div>
                    
                    <div class="mb-3">
                        <h6><i class="fas fa-clock text-primary mr-2"></i>Processing Time</h6>
                        <p class="mb-0">Instant - 5 minutes</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6><i class="fas fa-exchange-alt text-primary mr-2"></i>Exchange Rate</h6>
                        <p class="mb-0">1 USD = KES {{ number_format($exchangeRate, 2) }}</p>
                        <small class="text-muted">Rates are updated every 5 minutes</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6><i class="fas fa-info-circle text-primary mr-2"></i>Fees</h6>
                        <p class="mb-1">1% fee (min KES 10, max KES 500)</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6><i class="fas fa-tachometer-alt text-primary mr-2"></i>Limits</h6>
                        <p class="mb-1">Minimum: KES 100</p>
                        <p class="mb-0">Maximum: KES 100,000 per transaction</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i> 
                            Please ensure your CR number is correct. Transactions cannot be reversed.
                        </small>
                    </div>
                </div>
            </div>
        </div>
                
                <div class="support-info">
                    <h4>Need Help?</h4>
                    <a href="https://wa.me/254741554994" class="support-link">
                        <i class="fab fa-whatsapp"></i> Chat with Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');
        const estimatedUsd = document.getElementById('estimated-usd');
        const exchangeRate = 150.25; // This should come from your backend
        
        amountInput.addEventListener('input', function() {
            const kesAmount = parseFloat(this.value) || 0;
            const fee = Math.min(Math.max(kesAmount * 0.01, 10), 500);
            const usdAmount = (kesAmount - fee) / exchangeRate;
            
            estimatedUsd.textContent = usdAmount.toFixed(2);
        });
    });
</script>
@endpush
@endsection