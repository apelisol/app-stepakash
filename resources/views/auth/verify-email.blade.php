@extends('layouts.app')

@section('title', 'Verify Email | StepaKash')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-background via-primary-50 to-secondary-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <img src="{{ asset('assets/img/stepakash-money-on-the-go.png') }}" 
                 alt="STEPAKASH Logo" 
                 class="mx-auto h-16 w-auto">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Verify Your Email
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                We've sent a verification code to <span class="font-medium">{{ auth()->user()->email }}</span>
            </p>
        </div>

        @if (session('status'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            {{ session('status') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            There were {{ count($errors) }} errors with your submission
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form class="space-y-6" action="{{ route('verify.email.submit') }}" method="POST">
                @csrf
                
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700">
                        Verification Code
                    </label>
                    <div class="mt-1">
                        <input id="otp" name="otp" type="text" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                               placeholder="Enter 6-digit code">
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Please check your email for the verification code.
                    </p>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Verify Email
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            Didn't receive the code?
                        </span>
                    </div>
                </div>

                <div class="mt-6">
                    <form action="{{ route('verification.resend') }}" method="POST" class="text-center">
                        @csrf
                        <button type="submit" class="font-medium text-primary-600 hover:text-primary-500">
                            Resend Verification Code
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
