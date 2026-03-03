<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RateThatDriver')</title>
    
    <!-- Import Tailwind CSS -->
    @vite('resources/css/app.css')
    
    <style>
        /* Custom styles can go here */
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo/Brand -->
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-gray-800">
                        RateThatDriver
                    </a>
                </div>
                
                <!-- Navigation Links -->
                <div id="navLinks" class="flex items-center space-x-4">
                    <a href="/" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Home
                    </a>
                    
                    <!-- Conditional links based on authentication -->
                    @if(auth()->check())
                        <span class="text-sm text-gray-600">Welcome, {{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                            Register
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600 text-sm">
                &copy; {{ date('Y') }} RateThatDriver. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- JavaScript for API calls -->
    <script>
        // Global JavaScript functions for API calls
        async function apiRequest(url, method = 'GET', data = null) {
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            };
            
            if (data) {
                options.body = JSON.stringify(data);
            }
            
            const response = await fetch(url, options);
            return await response.json();
        }
        // Check authentication state and update navigation
                function updateNavigation() {
                    const user = localStorage.getItem('user');
                    const navLinks = document.getElementById('navLinks');
                    
                    if (user) {
                        const userData = JSON.parse(user);
                        navLinks.innerHTML = `
                            <div class="relative">
                                <button onclick="toggleDropdown()" id="profileBtn" class="flex items-center text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                    <span class="mr-1">👤</span>
                                    <span>Welcome, ${userData.name}</span>
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div id="dropdownMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border hidden z-50">
                                    <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 border-b border-gray-100">
                                        📱 My Profile
                                    </a>
                                    <a href="/my-ratings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 border-b border-gray-100">
                                        ⭐ My Ratings
                                    </a>
                                    <form action="/logout" method="POST" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 hover:text-red-700 font-medium">
                                            🔐 Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        `;
                    } else {
                        navLinks.innerHTML = `
                            <a href="/login" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                Login
                            </a>
                            <a href="/register" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                                Register
                            </a>
                        `;
                    }
                }

                function toggleDropdown() {
                    const dropdown = document.getElementById('dropdownMenu');
                    dropdown.classList.toggle('hidden');
                }

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    const dropdown = document.getElementById('dropdownMenu');
                    const profileBtn = document.getElementById('profileBtn');
                    
                    if (dropdown && profileBtn && !dropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });


        // Run on page load
        document.addEventListener('DOMContentLoaded', updateNavigation);

        // Also run when page loads (for SPA-like behavior)
        updateNavigation();

    </script>
</body>
</html>

