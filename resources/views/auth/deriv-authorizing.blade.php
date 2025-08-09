@extends('layouts.app')

@push('styles')
<style>
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Authorizing with Deriv
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Please wait while we connect to your Deriv account...
            </p>
        </div>

        <div class="mt-8 bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
                </div>
                
                <h3 class="text-lg font-medium text-gray-900">Connecting to Deriv</h3>
                <p class="mt-2 text-sm text-gray-600">
                    You'll be redirected to the registration page once authorization is complete.
                </p>
                
                <!-- Hidden form to submit the authorization request -->
                <form id="authorizeForm" class="hidden">
                    @csrf
                    <input type="hidden" name="account_number" value="{{ $primary_account['account_number'] }}">
                </form>
                
                <div id="errorMessage" class="mt-4 text-red-600 text-sm hidden">
                    There was an error authorizing your account. Please try again.
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const errorMessage = document.getElementById('errorMessage');
    const loadingSpinner = document.querySelector('.animate-spin');
    const loadingText = document.querySelector('h3');
    const loadingSubtext = document.querySelector('p');
    
    // Function to update the UI state
    function setLoadingState(isLoading, message = '') {
        if (isLoading) {
            loadingSpinner.classList.remove('hidden');
            loadingText.textContent = 'Connecting to Deriv...';
            loadingSubtext.textContent = 'Please wait while we connect to your account...';
            errorMessage.classList.add('hidden');
        } else {
            loadingSpinner.classList.add('hidden');
            loadingText.textContent = message || 'Authorization Complete';
        }
    }
    
    // Function to show error
    function showError(message) {
        setLoadingState(false);
        errorMessage.textContent = message;
        errorMessage.classList.remove('hidden');
        
        // Show retry button
        const existingButton = document.querySelector('.retry-button');
        if (!existingButton) {
            const retryButton = document.createElement('button');
            retryButton.type = 'button';
            retryButton.className = 'mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary retry-button';
            retryButton.textContent = 'Try Again';
            retryButton.onclick = authorizeAccount;
            
            const buttonContainer = document.createElement('div');
            buttonContainer.className = 'mt-4';
            buttonContainer.appendChild(retryButton);
            
            const container = document.querySelector('.text-center');
            container.appendChild(buttonContainer);
        }
    }
    
    // Function to handle the authorization
    async function authorizeAccount() {
        setLoadingState(true);
        
        try {
            const response = await fetch('{{ route("auth.deriv.authorize") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    account_number: '{{ $primary_account["account_number"] }}',
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                })
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Server returned an error');
            }
            
            if (data.success && data.redirect) {
                // Update UI before redirect
                setLoadingState(false, 'Success! Redirecting...');
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to authorize with Deriv');
            }
        } catch (error) {
            console.error('Authorization error:', error);
            showError(error.message || 'An unexpected error occurred. Please try again.');
        }
    }
    
    // Start the authorization process with a small delay to show the loading state
    setTimeout(authorizeAccount, 500);
});
</script>
@endpush

@endsection
