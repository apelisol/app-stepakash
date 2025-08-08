@extends('layouts.wallet')

@section('title', 'Buy Airtime')

@section('content')
<div class="wallet-container">
    <div class="page-header">
        <h2 class="page-title">Buy Airtime</h2>
        <div class="page-actions">
            <a href="{{ route('wallet.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    
    <div class="airtime-container">
        <div class="airtime-form">
            <form action="{{ route('wallet.airtime.process') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <div class="input-with-prefix">
                        <span class="prefix">+254</span>
                        <input type="tel" id="phone" name="phone" placeholder="712345678" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="network">Network</label>
                    <select id="network" name="network" required>
                        <option value="">-- Select Network --</option>
                        <option value="safaricom">Safaricom</option>
                        <option value="airtel">Airtel</option>
                        <option value="telkom">Telkom</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="amount">Amount (KES)</label>
                    <input type="number" id="amount" name="amount" min="10" step="1" placeholder="Minimum KES 10" required>
                </div>
                
                <div class="form-group">
                    <label for="recipient">Recipient Name (Optional)</label>
                    <input type="text" id="recipient" name="recipient" placeholder="Who is this airtime for?">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-shopping-cart"></i> Buy Airtime
                    </button>
                </div>
            </form>
            
            <div class="quick-amounts">
                <h3>Quick Purchase</h3>
                <div class="amount-buttons">
                    <button type="button" data-amount="50">KES 50</button>
                    <button type="button" data-amount="100">KES 100</button>
                    <button type="button" data-amount="200">KES 200</button>
                    <button type="button" data-amount="500">KES 500</button>
                    <button type="button" data-amount="1000">KES 1,000</button>
                </div>
            </div>
        </div>
        
        <div class="airtime-info">
            <div class="info-card">
                <h3>Airtime Purchase</h3>
                
                <div class="benefits">
                    <div class="benefit-item">
                        <i class="fas fa-bolt"></i>
                        <span>Instant delivery</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-percentage"></i>
                        <span>No extra charges</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-mobile-alt"></i>
                        <span>All networks supported</span>
                    </div>
                </div>
                
                <div class="recent-purchases">
                    <h4>Recent Purchases</h4>
                    @if($recentAirtime->isEmpty())
                    <p class="no-purchases">No recent airtime purchases</p>
                    @else
                    <ul class="purchase-list">
                        @foreach($recentAirtime as $purchase)
                        <li>
                            <span class="purchase-amount">KES {{ number_format($purchase->amount, 0) }}</span>
                            <span class="purchase-phone">{{ $purchase->phone }}</span>
                            <span class="purchase-date">{{ $purchase->created_at->format('M d, H:i') }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set quick amount buttons
        document.querySelectorAll('.amount-buttons button').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('amount').value = this.dataset.amount;
            });
        });
        
        // Auto-detect network based on phone number
        document.getElementById('phone').addEventListener('input', function() {
            const phone = this.value;
            if (phone.length >= 2) {
                const prefix = phone.substring(0, 2);
                let network = '';
                
                // Safaricom prefixes
                if (['70', '71', '72', '74', '79', '10', '11'].includes(prefix)) {
                    network = 'safaricom';
                } 
                // Airtel prefixes
                else if (['73', '75', '78', '77'].includes(prefix)) {
                    network = 'airtel';
                }
                // Telkom prefixes
                else if (['76'].includes(prefix)) {
                    network = 'telkom';
                }
                
                if (network) {
                    document.getElementById('network').value = network;
                }
            }
        });
    });
</script>
@endpush
@endsection