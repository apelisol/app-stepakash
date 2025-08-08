@extends('layouts.app')

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
                
                <div class="mt-6">
                    <a href="{{ route('auth.deriv') }}" class="font-medium text-primary hover:text-primary-600">
                        &larr; Back to login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Submit the authorization form
    const form = document.getElementById('authorizeForm');
    const errorMessage = document.getElementById('errorMessage');
    
    // Function to handle the authorization
    async function authorizeAccount() {
        try {
            const response = await fetch('{{ route("auth.deriv.authorize") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    account_number: '{{ $primary_account["account_number"] }}'
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Redirect to the registration page on success
                window.location.href = data.redirect;
            } else {
                // Show error message
                errorMessage.textContent = data.message || 'Failed to authorize with Deriv';
                errorMessage.classList.remove('hidden');
                
                // Show retry button
                const retryButton = document.createElement('button');
                retryButton.type = 'button';
                retryButton.className = 'mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary';
                retryButton.textContent = 'Try Again';
                retryButton.onclick = authorizeAccount;
                
                const buttonContainer = document.createElement('div');
                buttonContainer.className = 'mt-4';
                buttonContainer.appendChild(retryButton);
                
                const container = document.querySelector('.text-center');
                container.appendChild(buttonContainer);
            }
        } catch (error) {
            console.error('Authorization error:', error);
            errorMessage.textContent = 'An unexpected error occurred. Please try again.';
            errorMessage.classList.remove('hidden');
        }
    }
    
    // Start the authorization process
    authorizeAccount();
});
</script>
@endpush

@endsection
