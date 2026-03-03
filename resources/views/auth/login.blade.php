@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Login to RateThatDriver</h1>
        
        <!-- Login Form -->
        <form id="loginForm" class="space-y-4">
            @csrf
            
            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="your@email.com">
                <div id="emailError" class="text-red-600 text-sm mt-1 hidden"></div>
            </div>
            
            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter your password">
                <div id="passwordError" class="text-red-600 text-sm mt-1 hidden"></div>
            </div>
            
            <!-- Submit Button -->
            <div>
                <button type="submit" 
                        id="loginBtn"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Login
                </button>
            </div>
            
            <!-- Success/Error Messages -->
            <div id="message" class="hidden p-3 rounded-md"></div>
        </form>
        
        <!-- Links -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Register here
                </a>
            </p>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        email: document.getElementById('email').value,
        password: document.getElementById('password').value
    };
    
    const loginBtn = document.getElementById('loginBtn');
    const messageDiv = document.getElementById('message');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    
    // Reset previous errors
    emailError.classList.add('hidden');
    passwordError.classList.add('hidden');
    messageDiv.classList.add('hidden');
    messageDiv.className = 'hidden p-3 rounded-md';
    
    // Disable button and show loading
    loginBtn.disabled = true;
    loginBtn.textContent = 'Logging in...';
    
    try {
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            // Login successful
            messageDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded hidden';
            messageDiv.textContent = 'Login successful! Redirecting...';
            messageDiv.classList.remove('hidden');
            
            // Store user data in localStorage (for now)
            localStorage.setItem('user', JSON.stringify(result.user));
            
            // Redirect to home page after 1 second
            setTimeout(() => {
                window.location.href = '/';
            }, 1000);
            
        } else {
            // Login failed - show errors
            if (result.errors) {
                if (result.errors.email) {
                    emailError.textContent = result.errors.email[0];
                    emailError.classList.remove('hidden');
                }
                if (result.errors.password) {
                    passwordError.textContent = result.errors.password[0];
                    passwordError.classList.remove('hidden');
                }
            } else if (result.message) {
                messageDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden';
                messageDiv.textContent = result.message;
                messageDiv.classList.remove('hidden');
            }
        }
    } catch (error) {
        console.error('Login error:', error);
        messageDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden';
        messageDiv.textContent = 'An error occurred. Please try again.';
        messageDiv.classList.remove('hidden');
    } finally {
        // Re-enable button
        loginBtn.disabled = false;
        loginBtn.textContent = 'Login';
    }
});
</script>
@endsection
