@extends('layouts.app')

@section('title', 'Withdraw from Deriv Account')

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
        <h2 class="page-title">Withdraw from Deriv</h2>
        <div class="page-actions">
            <a href="{{ route('wallet.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i> Back to Wallet
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Wallet Balance Card -->
        <div class="col-md-6 mb-4">
            <div class="card balance-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Wallet Balance</h5>
                        <a href="#" id="toggleWalletBalance" class="text-muted">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <h2 class="mb-0" id="walletBalance">{{ number_format($wallet->balance, 2) }}</h2>
                        <span class="text-muted ml-2">KES</span>
                    </div>
                    <div class="mt-2 text-muted small">Available balance</div>
                </div>
            </div>
        </div>

        <!-- Deriv Balance Card -->
        <div class="col-md-6 mb-4">
            <div class="card balance-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Deriv Balance</h5>
                        <a href="#" id="refreshDerivBalance" class="text-muted refresh-balance">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <h2 class="mb-0" id="derivBalance">{{ number_format($derivBalance ?? 0, 2) }}</h2>
                        <span class="text-muted ml-2">USD</span>
                    </div>
                    <div class="mt-2 text-muted small">
                        Last updated: <span id="derivLastUpdated">{{ now()->format('H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Withdraw from Deriv</h4>
                    
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

                    <form id="withdrawForm">
                        <div id="withdrawStep1">
                            <div class="form-group">
                                <label for="amount">Amount (USD)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" 
                                           class="form-control" 
                                           id="amount" 
                                           name="amount" 
                                           required
                                           min="1"
                                           max="10000"
                                           step="0.01"
                                           placeholder="Enter amount in USD">
                                </div>
                                <small class="form-text text-muted">Minimum: $1.00 | Maximum: $10,000.00</small>
                            </div>

                            <div class="form-group">
                                <label>Exchange Rate</label>
                                <div class="alert alert-info">
                                    1 USD = {{ number_format($exchangeRate, 2) }} KES
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Estimated Credit</label>
                                <div class="alert alert-success">
                                    <div class="d-flex justify-content-between">
                                        <span>Amount (KES):</span>
                                        <strong id="kesAmount">0.00 KES</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Fee (0.5%):</span>
                                        <strong id="feeAmount">0.00 KES</strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between font-weight-bold">
                                        <span>Total Credit:</span>
                                        <strong id="totalCredit">0.00 KES</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="button" class="btn btn-primary btn-lg" id="initiateWithdrawalBtn">
                                    <i class="fas fa-exchange-alt mr-2"></i> Request Withdrawal
                                </button>
                            </div>
                        </div>

                        <!-- Verification Step (initially hidden) -->
                        <div id="verificationStep" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-envelope-open-text mr-2"></i>
                                We've sent a verification code to <strong>{{ Auth::user()->email }}</strong>.
                                Please check your email and enter the 8-digit code below.
                            </div>

                            <div class="form-group">
                                <label for="verificationCode">Verification Code</label>
                                <input type="text" 
                                       class="form-control form-control-lg text-center" 
                                       id="verificationCode" 
                                       name="verification_code" 
                                       maxlength="8"
                                       placeholder="Enter 8-digit code"
                                       style="font-size: 1.5rem; letter-spacing: 0.5em;">
                                <small class="form-text text-muted">
                                    Didn't receive a code? 
                                    <a href="#" id="resendCodeLink">Resend code</a>
                                </small>
                            </div>

                            <div class="form-group mt-4">
                                <button type="button" class="btn btn-primary btn-lg btn-block" id="verifyWithdrawalBtn">
                                    <i class="fas fa-check-circle mr-2"></i> Verify & Complete Withdrawal
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-block mt-2" id="backToAmountBtn">
                                    <i class="fas fa-arrow-left mr-2"></i> Back to Amount
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Withdrawal Information</h5>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Important:</strong> Please ensure your Deriv account is verified before proceeding with withdrawals.
                    </div>
                    
                    <div class="withdrawal-info">
                        <h6>Processing Time</h6>
                        <p class="small text-muted">
                            Withdrawals are typically processed within 1-2 business days. You will receive a confirmation email once processed.
                        </p>

                        <h6 class="mt-4">Fees</h6>
                        <ul class="small text-muted pl-3">
                            <li>0.5% withdrawal fee (min 5 KES, max 250 KES)</li>
                            <li>No hidden charges</li>
                        </ul>

                        <h6 class="mt-4">Need Help?</h6>
                        <p class="small text-muted">
                            Contact our support team at <a href="mailto:support@stepakash.com">support@stepakash.com</a> for any assistance.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form elements
        const withdrawForm = document.getElementById('withdrawForm');
        const withdrawStep1 = document.getElementById('withdrawStep1');
        const verificationStep = document.getElementById('verificationStep');
        const initiateWithdrawalBtn = document.getElementById('initiateWithdrawalBtn');
        const verifyWithdrawalBtn = document.getElementById('verifyWithdrawalBtn');
        const backToAmountBtn = document.getElementById('backToAmountBtn');
        const resendCodeLink = document.getElementById('resendCodeLink');
        
        // Input fields
        const amountInput = document.getElementById('amount');
        const verificationCodeInput = document.getElementById('verificationCode');
        
        // Display elements
        const kesAmount = document.getElementById('kesAmount');
        const feeAmount = document.getElementById('feeAmount');
        const totalCredit = document.getElementById('totalCredit');
        
        // Constants
        const exchangeRate = {{ $exchangeRate }};
        let verificationId = null;
        
        // Format number with commas
        const formatNumber = (num) => {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        };
        
        // Calculate amounts
        const calculateAmounts = () => {
            const usdAmount = parseFloat(amountInput.value) || 0;
            const kesTotal = usdAmount * exchangeRate;
            const fee = Math.min(Math.max(kesTotal * 0.005, 5), 250); // 0.5% fee, min 5, max 250 KES
            const total = kesTotal - fee;
            
            kesAmount.textContent = formatNumber(kesTotal.toFixed(2)) + ' KES';
            feeAmount.textContent = formatNumber(fee.toFixed(2)) + ' KES';
            totalCredit.textContent = formatNumber(total.toFixed(2)) + ' KES';
        };
        
        // Event listeners
        amountInput.addEventListener('input', calculateAmounts);
        
        // Initial calculation
        calculateAmounts();
        
        // Handle withdrawal initiation
        if (initiateWithdrawalBtn) {
            initiateWithdrawalBtn.addEventListener('click', function() {
                const amount = parseFloat(amountInput.value);
                
                if (!amount || amount <= 0) {
                    alert('Please enter a valid amount');
                    return;
                }
                
                // Disable button and show loading
                const originalText = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending verification code...';
                
                // Call API to initiate withdrawal and send verification code
                fetch('{{ route("wallet.deriv.withdraw.initiate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        amount: amount
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Store verification ID for the next step
                        verificationId = data.verification_id;
                        
                        // Show verification step
                        withdrawStep1.style.display = 'none';
                        verificationStep.style.display = 'block';
                        
                        // Focus on verification code input
                        verificationCodeInput.focus();
                    } else {
                        alert(data.message || 'Failed to initiate withdrawal. Please try again.');
                        this.disabled = false;
                        this.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    this.disabled = false;
                    this.innerHTML = originalText;
                });
            });
        }
        
        // Handle verification code submission
        if (verifyWithdrawalBtn) {
            verifyWithdrawalBtn.addEventListener('click', function() {
                const code = verificationCodeInput.value.trim();
                
                if (!code || code.length !== 8) {
                    alert('Please enter a valid 8-digit verification code');
                    return;
                }
                
                // Disable button and show loading
                const originalText = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...';
                
                // Call API to verify code and complete withdrawal
                fetch('{{ route("wallet.deriv.withdraw.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        verification_code: code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to dashboard with success message
                        window.location.href = '{{ route("wallet.dashboard") }}?withdrawal=success';
                    } else {
                        alert(data.message || 'Verification failed. Please try again.');
                        this.disabled = false;
                        this.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    this.disabled = false;
                    this.innerHTML = originalText;
                });
            });
        }
        
        // Handle back to amount button
        if (backToAmountBtn) {
            backToAmountBtn.addEventListener('click', function() {
                withdrawStep1.style.display = 'block';
                verificationStep.style.display = 'none';
                initiateWithdrawalBtn.disabled = false;
                initiateWithdrawalBtn.innerHTML = '<i class="fas fa-exchange-alt mr-2"></i> Request Withdrawal';
            });
        }
        
        // Handle resend code link
        if (resendCodeLink) {
            resendCodeLink.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Show loading on resend link
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
                
                // Call API to resend code
                fetch('{{ route("wallet.deriv.withdraw.initiate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        amount: amountInput.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        verificationId = data.verification_id;
                        alert('A new verification code has been sent to your email.');
                    } else {
                        alert(data.message || 'Failed to resend verification code. Please try again.');
                    }
                    this.innerHTML = originalText;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    this.innerHTML = originalText;
                });
            });
        }
        
        // Toggle wallet balance visibility
        const toggleBalance = document.getElementById('toggleWalletBalance');
        const walletBalance = document.getElementById('walletBalance');
        
        if (toggleBalance && walletBalance) {
            toggleBalance.addEventListener('click', function(e) {
                e.preventDefault();
                const icon = this.querySelector('i');
                
                if (walletBalance.classList.contains('blurred')) {
                    walletBalance.classList.remove('blurred');
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    walletBalance.classList.add('blurred');
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        }
        
        // Refresh Deriv balance
        const refreshDerivBalance = document.getElementById('refreshDerivBalance');
        const derivBalance = document.getElementById('derivBalance');
        const derivLastUpdated = document.getElementById('derivLastUpdated');
        
        if (refreshDerivBalance) {
            refreshDerivBalance.addEventListener('click', function() {
                this.classList.add('fa-spin');
                
                fetch('{{ route("wallet.deriv.balance") }}', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        derivBalance.textContent = parseFloat(data.balance).toFixed(2);
                        const now = new Date();
                        derivLastUpdated.textContent = now.getHours().toString().padStart(2, '0') + ':' + 
                                                     now.getMinutes().toString().padStart(2, '0');
                    }
                })
                .catch(error => {
                    console.error('Error fetching balance:', error);
                })
                .finally(() => {
                    this.classList.remove('fa-spin');
                });
            });
        }
    });
</script>
@endpush
@endsection