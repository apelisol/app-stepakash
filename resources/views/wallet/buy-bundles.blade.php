@extends('layouts.wallet')

@section('title', 'Buy Data Bundles')

@section('content')
<div class="wallet-container">
    <div class="page-header">
        <h2 class="page-title">Buy Data Bundles</h2>
        <div class="page-actions">
            <a href="{{ route('wallet.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    
    <div class="bundles-container">
        <div class="bundles-form">
            <form action="{{ route('wallet.bundles.process') }}" method="POST">
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
                    <label for="bundle">Select Bundle</label>
                    <select id="bundle" name="bundle" required>
                        <option value="">-- Select Bundle --</option>
                        <!-- Bundles will be loaded via JavaScript based on network -->
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="recipient">Recipient Name (Optional)</label>
                    <input type="text" id="recipient" name="recipient" placeholder="Who is this bundle for?">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-shopping-cart"></i> Buy Bundle
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bundles-offers">
            <div class="network-tabs">
                <button class="network-tab active" data-network="safaricom">Safaricom</button>
                <button class="network-tab" data-network="airtel">Airtel</button>
                <button class="network-tab" data-network="telkom">Telkom</button>
            </div>
            
            <div class="bundles-grid">
                <!-- Bundles will be loaded here via JavaScript -->
                <div class="loading-bundles">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading bundles...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Define bundles for each network
        const bundles = {
            safaricom: [
                { name: 'Daily 50MB', validity: '1 Day', price: 20, value: '50MB' },
                { name: 'Daily 100MB', validity: '1 Day', price: 50, value: '100MB' },
                { name: 'Weekly 300MB', validity: '7 Days', price: 100, value: '300MB' },
                { name: 'Monthly 1GB', validity: '30 Days', price: 300, value: '1GB' },
                { name: 'Monthly 3GB', validity: '30 Days', price: 700, value: '3GB' },
                { name: 'Monthly 5GB', validity: '30 Days', price: 1000, value: '5GB' },
                { name: 'Monthly 10GB', validity: '30 Days', price: 2000, value: '10GB' }
            ],
            airtel: [
                { name: 'Daily 100MB', validity: '1 Day', price: 20, value: '100MB' },
                { name: 'Weekly 350MB', validity: '7 Days', price: 100, value: '350MB' },
                { name: 'Monthly 1.5GB', validity: '30 Days', price: 500, value: '1.5GB' },
                { name: 'Monthly 3GB', validity: '30 Days', price: 800, value: '3GB' },
                { name: 'Monthly 7GB', validity: '30 Days', price: 1500, value: '7GB' },
                { name: 'Monthly 12GB', validity: '30 Days', price: 2500, value: '12GB' }
            ],
            telkom: [
                { name: 'Daily 150MB', validity: '1 Day', price: 20, value: '150MB' },
                { name: 'Weekly 500MB', validity: '7 Days', price: 100, value: '500MB' },
                { name: 'Monthly 2GB', validity: '30 Days', price: 500, value: '2GB' },
                { name: 'Monthly 5GB', validity: '30 Days', price: 1000, value: '5GB' },
                { name: 'Monthly 10GB', validity: '30 Days', price: 2000, value: '10GB' }
            ]
        };
        
        // Load bundles when network is selected
        document.getElementById('network').addEventListener('change', function() {
            const network = this.value;
            const bundleSelect = document.getElementById('bundle');
            
            // Clear existing options
            bundleSelect.innerHTML = '<option value="">-- Select Bundle --</option>';
            
            if (network && bundles[network]) {
                bundles[network].forEach(bundle => {
                    const option = document.createElement('option');
                    option.value = bundle.value;
                    option.textContent = `${bundle.name} - ${bundle.validity} - KES ${bundle.price}`;
                    option.dataset.price = bundle.price;
                    bundleSelect.appendChild(option);
                });
            }
            
            // Also update the bundles grid
            updateBundlesGrid(network);
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
                    // Trigger change event to load bundles
                    document.getElementById('network').dispatchEvent(new Event('change'));
                }
            }
        });
        
        // Handle network tabs
        document.querySelectorAll('.network-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Update active tab
                document.querySelectorAll('.network-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Update bundles grid
                updateBundlesGrid(this.dataset.network);
                
                // Update form network selection
                document.getElementById('network').value = this.dataset.network;
                document.getElementById('network').dispatchEvent(new Event('change'));
            });
        });
        
        // Function to update bundles grid
        function updateBundlesGrid(network) {
            const bundlesGrid = document.querySelector('.bundles-grid');
            
            if (!network || !bundles[network]) {
                bundlesGrid.innerHTML = '<div class="no-bundles">No bundles available for this network</div>';
                return;
            }
            
            let html = '';
            bundles[network].forEach(bundle => {
                html += `
                <div class="bundle-card" data-value="${bundle.value}" data-price="${bundle.price}">
                    <h4 class="bundle-name">${bundle.name}</h4>
                    <div class="bundle-details">
                        <span class="bundle-data">${bundle.value}</span>
                        <span class="bundle-validity">${bundle.validity}</span>
                    </div>
                    <div class="bundle-price">KES ${bundle.price}</div>
                    <button class="select-bundle">Select</button>
                </div>
                `;
            });
            
            bundlesGrid.innerHTML = html;
            
            // Add click handlers to bundle cards
            document.querySelectorAll('.bundle-card').forEach(card => {
                card.addEventListener('click', function() {
                    const bundleSelect = document.getElementById('bundle');
                    bundleSelect.value = this.dataset.value;
                    
                    // Highlight selected bundle
                    document.querySelectorAll('.bundle-card').forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });
        }
        
        // Initialize with Safaricom bundles
        updateBundlesGrid('safaricom');
    });
</script>
@endpush
@endsection