@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                    
                    @if(Auth::check())
                        <div class="mt-4">
                            <h4>Account Information</h4>
                            <p><strong>Name:</strong> {{ Auth::user()->fullname ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ Auth::user()->email ?? 'N/A' }}</p>
                            <p><strong>Wallet ID:</strong> {{ Auth::user()->wallet_id ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
