@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Create Your Account</h1>
        
        <!-- Registration Form -->
        <form id="registerForm" class="space-y-4">
            @csrf
            
            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="John Doe">
                <div id="nameError" class="text-red-600 text-sm mt-1 hidden"></div>
            </div>
            
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
                       minlength="8"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter your password">
                <div id="passwordError" class="text-red-600 text-sm mt-1 hidden"></div>
            </div>
            
            <!-- Password Confirmation Field -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required 
                       minlength="8"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Confirm your password">
                <div id="passwordConfirmationError" class="text-red-600 text-sm mt-1 hidden"></div>
            </div>
            
            <!-- Submit Button -->
            <div>
                <button type="submit" 
                        id="registerBtn"
                        class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    Register
                </button>
            </div>
            
            <!-- Success/Error Messages -->
            <div id="message" class="hidden p-3 rounded-md"></div>
        </form>
        
        <!-- Links -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Login here
                </a>
            </p>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const data = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value
    };
    
    const registerBtn = document.getElementById('registerBtn');
    const messageDiv = document.getElementById('message');
    const nameError = document.getElementById('nameError');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const passwordConfirmationError = document.getElementById('passwordConfirmationError');
    
    // Reset previous errors
    nameError.classList.add('hidden');
    emailError.classList.add('hidden');
    passwordError.classList.add('hidden');
    passwordConfirmationError.classList.add('hidden');
    messageDiv.classList.add('hidden');
    messageDiv.className = 'hidden p-3 rounded-md';
    
    // Validate passwords match
    if (data.password !== data.password_confirmation) {
        passwordConfirmationError.textContent = 'Passwords do not match';
        passwordConfirmationError.classList.remove('hidden');
        return;
    }
    
    // Disable button and show loading
    registerBtn.disabled = true;
    registerBtn.textContent = 'Registering...';
    
    try {
        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            // Registration successful
            messageDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded hidden';
            messageDiv.textContent = 'Registration successful! Redirecting to login...';
            messageDiv.classList.remove('hidden');
            
            // Clear form
            document.getElementById('registerForm').reset();
            
            // Redirect to login page after 2 seconds
            setTimeout(() => {
                window.location.href = '{{ route('login') }}';
            }, 2000);
            
        } else {
            // Registration failed - show errors
            if (result.errors) {
                if (result.errors.name) {
                    nameError.textContent = result.errors.name[0];
                    nameError.classList.remove('hidden');
                }
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
        console.error('Registration error:', error);
        messageDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden';
        messageDiv.textContent = 'An error occurred. Please try again.';
        messageDiv.classList.remove('hidden');
    } finally {
        // Re-enable button
        registerBtn.disabled = false;
        registerBtn.textContent = 'Register';
    }
});
</script>
@endsection
